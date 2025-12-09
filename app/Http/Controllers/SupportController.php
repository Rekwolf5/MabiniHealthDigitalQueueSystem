<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\SupportMessage;

class SupportController extends Controller
{
    /**
     * Show the support form
     */
    public function show()
    {
        $guard = $this->getAuthGuard();
        $user = Auth::guard($guard)->user();
        
        // Get user's previous messages
        $messages = [];
        if ($user) {
            $messages = SupportMessage::where('user_type', $guard)
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();
            
            // Mark support messages as read for patient users
            if ($guard === 'patient' && method_exists($user, 'update')) {
                $user->update(['last_support_check' => now()]);
            }
        }
        
        return view('support.index', compact('messages'));
    }

    /**
     * Submit a support message
     */
    public function submit(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        $guard = $this->getAuthGuard();
        $user = Auth::guard($guard)->user();

        // Determine name and email
        if ($guard === 'patient' && $user) {
            $name = $user->patient ? $user->patient->full_name : $user->email;
            $email = $user->email;
        } elseif ($user) {
            $name = $user->name;
            $email = $user->email;
        } else {
            $name = 'Guest';
            $email = 'unknown@guest.com';
        }

        $support = SupportMessage::create([
            'user_type' => $guard,
            'user_id' => $user ? $user->id : null,
            'name' => $name,
            'email' => $email,
            'subject' => $request->subject,
            'message' => $request->message,
            'status' => 'pending',
        ]);

        // Send notification email to admin (optional)
        try {
            Mail::send('emails.support-notification', [
                'name' => $name,
                'email' => $email,
                'subject' => $request->subject,
                'messageContent' => $request->message,
            ], function ($message) {
                $message->to(config('mail.from.address'))
                        ->subject('New Support Request - Mabini Health Center');
            });
        } catch (\Exception $e) {
            \Log::error('Failed to send support notification: ' . $e->getMessage());
        }

        return back()->with('success', 'Your message has been sent! We will respond as soon as possible.');
    }

    /**
     * Admin view all support messages
     */
    public function adminIndex()
    {
        $messages = SupportMessage::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.support.index', compact('messages'));
    }

    /**
     * Admin reply to message
     */
    public function adminReply(Request $request, $id)
    {
        $request->validate([
            'reply' => 'required|string|max:5000',
        ]);

        $message = SupportMessage::findOrFail($id);
        
        $message->update([
            'admin_reply' => $request->reply,
            'status' => 'replied',
            'replied_at' => now(),
        ]);

        // Send reply email to user
        try {
            Mail::send('emails.support-reply', [
                'name' => $message->name,
                'subject' => $message->subject,
                'originalMessage' => $message->message,
                'reply' => $request->reply,
            ], function ($mail) use ($message) {
                $mail->to($message->email)
                     ->subject('Re: ' . $message->subject . ' - Mabini Health Center');
            });
            
            \Log::info('Support reply email sent successfully to: ' . $message->email);
        } catch (\Exception $e) {
            \Log::error('Failed to send support reply email: ' . $e->getMessage());
            return back()->with('success', 'Reply saved, but email notification failed to send. Error: ' . $e->getMessage());
        }

        return back()->with('success', 'Reply sent successfully! Email notification has been sent to ' . $message->email);
    }

    /**
     * Get current auth guard
     */
    private function getAuthGuard()
    {
        if (Auth::guard('patient')->check()) {
            return 'patient';
        } elseif (Auth::guard('web')->check()) {
            return 'web';
        }
        return 'patient'; // Default to patient instead of guest
    }
}
