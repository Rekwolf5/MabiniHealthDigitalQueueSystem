<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PatientAccount;
use App\Models\Queue;
use App\Models\Medicine;
use App\Models\ActivityLog;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class AdminController extends Controller
{
    public function __construct()
    {
        // Remove the middleware calls from constructor
        // These will be handled in routes instead
    }

    public function dashboard()
    {
        $totalUsers = User::count();
        $staffCount = User::where('role', 'staff')->count();
        $adminCount = User::where('role', 'admin')->count();
        $patientCount = PatientAccount::count();
        
        $todayQueue = Queue::whereDate('created_at', today())->count();
        $completedToday = Queue::whereDate('created_at', today())
            ->where('status', 'Completed')->count();
        $waitingQueue = Queue::whereIn('status', ['Waiting', 'Consulting'])->count();
        $medicineCount = Medicine::count();

        // Get recent activity with user relationship
        $recentActivity = ActivityLog::with(['systemUser', 'patientUser'])
            ->whereNotNull('created_at')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers', 'staffCount', 'adminCount', 'patientCount',
            'todayQueue', 'completedToday', 'waitingQueue', 'medicineCount',
            'recentActivity'
        ));
    }

    public function userManagement()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(15);
        return view('admin.user-management', compact('users'));
    }

    public function createUser()
    {
        $services = \App\Models\Service::where('is_active', true)
            ->orderBy('name')
            ->get();
        return view('admin.create-user', compact('services'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->letters()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
            ],
            'role' => 'required|in:admin,staff,manager,front_desk',
            'service_id' => 'required_if:role,staff|nullable|exists:services,id',
            'phone' => 'nullable|string|max:255',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'service_id' => $request->role === 'staff' ? $request->service_id : null,
            'phone' => $request->phone,
        ]);

        if ($request->role === 'staff' && $request->service_id) {
            $serviceName = \App\Models\Service::find($request->service_id)->name;
            
            ActivityLog::log('user_create', "Created new staff: {$user->name} assigned to service: {$serviceName}", [
                'user_id' => $user->id,
                'role' => $user->role,
                'service_id' => $request->service_id,
            ]);
        } else {
            ActivityLog::log('user_create', "Created new user: {$user->name} ({$user->role})", [
                'user_id' => $user->id,
                'role' => $user->role,
            ]);
        }

        return redirect()->route('admin.users')->with('success', 'User created successfully!');
    }

    /**
     * Show staff creation form (Admin-only)
     */
    public function createStaff()
    {
        $services = \App\Models\Service::where('is_active', true)
            ->orderBy('name')
            ->get();
        return view('admin.create-staff', compact('services'));
    }

    /**
     * Store new staff account (Admin-only with strong password)
     */
    public function storeStaff(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()      // Requires uppercase AND lowercase
                    ->letters()        // Requires at least one letter
                    ->numbers()        // Requires at least one number
                    ->symbols()        // Requires special character
                    ->uncompromised(), // Checks against breached passwords
            ],
            'role' => 'required|in:staff,admin,manager,front_desk',
            'service_id' => 'required_if:role,staff|nullable|exists:services,id',
            'phone' => 'nullable|string|max:255',
        ]);

        // Store plain password before hashing for the email
        $plainPassword = $request->password;

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'service_id' => $request->role === 'staff' ? $request->service_id : null,
            'phone' => $request->phone,
        ]);

        // Send welcome email with credentials
        try {
            $serviceName = null;
            if ($user->role === 'staff' && $user->service_id) {
                $serviceName = \App\Models\Service::find($user->service_id)->name;
            }
            $user->notify(new \App\Notifications\WelcomeStaffNotification($plainPassword, $user->role, $serviceName));
        } catch (\Exception $e) {
            \Log::error('Failed to send welcome email: ' . $e->getMessage());
        }

        if ($request->role === 'staff' && $request->service_id) {
            $serviceName = \App\Models\Service::find($request->service_id)->name;
            
            ActivityLog::log('staff_create', "Admin created new staff: {$user->name} assigned to service: {$serviceName}", [
                'staff_id' => $user->id,
                'role' => $user->role,
                'service_id' => $request->service_id,
                'created_by' => Auth::id(),
            ], auth()->user()->role);
        } else {
            ActivityLog::log('staff_create', "Admin created new user: {$user->name} ({$user->role})", [
                'staff_id' => $user->id,
                'role' => $user->role,
                'created_by' => Auth::id(),
            ], auth()->user()->role);
        }

        return redirect()->route('admin.users')->with('success', ucfirst($request->role) . ' account created successfully! A welcome email has been sent to ' . $user->email);
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return view('admin.edit-user', compact('user'));
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'role' => 'required|in:admin,staff,manager,front_desk',
            'phone' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Update basic user info
        $user->update($request->only(['name', 'email', 'role', 'phone']));

        // Update password if provided
        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($request->password)
            ]);
        }

        ActivityLog::log('user_update', "Updated user: {$user->name}", [
            'user_id' => $user->id,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('admin.users')->with('success', 'User updated successfully!');
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $userName = $user->name;
        $user->delete();

        ActivityLog::log('user_delete', "Deleted user: {$userName}", [
            'deleted_user_id' => $id,
        ]);

        return back()->with('success', 'User deleted successfully!');
    }

    public function queueMonitor()
    {
        $queue = Queue::with('patient')
            ->whereDate('created_at', today())
            ->orderBy('arrived_at', 'asc')
            ->get()
            ->sortBy(function ($item) {
                $priorities = ['Emergency' => 1, 'Urgent' => 2, 'Normal' => 3];
                return $priorities[$item->priority] ?? 4;
            })
            ->values();

        return view('admin.queue-monitor', compact('queue'));
    }

    public function medicineManagement()
    {
        $medicines = Medicine::orderBy('name')->paginate(15);
        return view('admin.medicine-management', compact('medicines'));
    }

    public function approveMedicineChange(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'stock_change' => 'required|integer',
            'reason' => 'required|string|max:255',
        ]);

        $medicine = Medicine::findOrFail($id);
        
        if ($request->action === 'approve') {
            $oldStock = $medicine->stock;
            $newStock = $oldStock + $request->stock_change;
            
            if ($newStock < 0) {
                return back()->with('error', 'Stock cannot be negative.');
            }
            
            $medicine->update(['stock' => $newStock]);
            
            ActivityLog::log('medicine_admin_approve', "Admin approved stock change for {$medicine->name}", [
                'medicine_id' => $medicine->id,
                'old_stock' => $oldStock,
                'new_stock' => $newStock,
                'change' => $request->stock_change,
                'reason' => $request->reason,
            ]);
            
            return back()->with('success', 'Medicine stock change approved.');
        }

        ActivityLog::log('medicine_admin_reject', "Admin rejected stock change for {$medicine->name}", [
            'medicine_id' => $medicine->id,
            'reason' => $request->reason,
        ]);

        return back()->with('success', 'Medicine stock change rejected.');
    }

    public function activityLogs()
    {
        $logs = ActivityLog::orderBy('created_at', 'desc')->paginate(50);
        return view('admin.activity-logs', compact('logs'));
    }

    public function systemSettings()
    {
        $settings = SystemSetting::orderBy('key')->get();
        return view('admin.system-settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        foreach ($request->settings as $key => $value) {
            // Determine the type based on the value
            if (is_array($value)) {
                SystemSetting::set($key, $value, 'json');
            } elseif (in_array($value, ['0', '1'])) {
                SystemSetting::set($key, $value, 'boolean');
            } elseif (is_numeric($value)) {
                SystemSetting::set($key, $value, 'number');
            } else {
                SystemSetting::set($key, $value, 'string');
            }
        }

        ActivityLog::log('settings_update', 'Updated system settings', $request->settings, auth()->user()->role);

        return back()->with('success', 'Settings updated successfully!');
    }

    public function backupData()
    {
        try {
            $timestamp = now()->format('Y-m-d_H-i-s');
            $filename = "backup_{$timestamp}.sql";
            
            // For SQLite, copy the database file
            if (config('database.default') === 'sqlite') {
                $dbPath = database_path('database.sqlite');
                $backupPath = storage_path("app/backups/{$filename}");
                
                if (!Storage::disk('local')->exists('backups')) {
                    Storage::disk('local')->makeDirectory('backups');
                }
                
                copy($dbPath, $backupPath);
            }

            ActivityLog::log('backup_create', "Created database backup: {$filename}");

            return response()->download($backupPath)->deleteFileAfterSend();
        } catch (\Exception $e) {
            return back()->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    public function restoreData(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|mimes:sql,sqlite',
        ]);

        try {
            $file = $request->file('backup_file');
            $timestamp = now()->format('Y-m-d_H-i-s');
            
            // Create backup of current database first
            $this->backupData();
            
            // For SQLite, replace the database file
            if (config('database.default') === 'sqlite') {
                $dbPath = database_path('database.sqlite');
                $file->move(dirname($dbPath), 'database.sqlite');
            }

            ActivityLog::log('backup_restore', "Restored database from backup file");

            return back()->with('success', 'Database restored successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Restore failed: ' . $e->getMessage());
        }
    }

    /**
     * Update system settings
     */
    public function updateSystemSettings(Request $request)
    {
        $validated = $request->validate([
            'queue_cutoff_time' => 'required|date_format:H:i',
            'opening_time' => 'required|date_format:H:i',
            'closing_time' => 'required|date_format:H:i',
            'max_queue_per_day' => 'required|integer|min:1|max:500',
            'enable_cutoff_notifications' => 'required|boolean',
            'cutoff_warning_time' => 'required|date_format:H:i',
        ]);

        foreach ($validated as $key => $value) {
            SystemSetting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => is_bool($value) ? ($value ? 'true' : 'false') : $value,
                    'type' => is_bool($value) ? 'boolean' : (is_numeric($value) ? 'number' : 'time'),
                ]
            );
        }

        ActivityLog::log('settings_update', 'Updated system settings including queue cutoff time', $validated);

        return back()->with('success', 'System settings updated successfully!');
    }

    /**
     * Manually trigger cutoff processing
     */
    public function processCutoff()
    {
        try {
            \Artisan::call('queue:process-cutoff', ['--force' => true]);
            $output = \Artisan::output();
            
            ActivityLog::log('manual_cutoff', 'Manually triggered queue cutoff processing');

            return back()->with('success', 'Cutoff processing completed. ' . $output);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to process cutoff: ' . $e->getMessage());
        }
    }
}
