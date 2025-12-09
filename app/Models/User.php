<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'service_id',
        'user_type',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function hasVerifiedEmail()
    {
        return ! is_null($this->email_verified_at);
    }

    public function markEmailAsVerified()
    {
        return $this->forceFill([
            'email_verified_at' => $this->freshTimestamp(),
        ])->save();
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new \App\Notifications\VerifyEmail);
    }

    public function getEmailForVerification()
    {
        return $this->email;
    }

    /**
     * Get the service that this user is assigned to (single - legacy)
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get all services this user has access to (multiple services)
     */
    public function services()
    {
        return $this->belongsToMany(Service::class, 'service_user')
                    ->withTimestamps();
    }

    /**
     * Check if user has access to a specific service
     */
    public function hasAccessToService($serviceId)
    {
        return $this->services()->where('service_id', $serviceId)->exists();
    }

    /**
     * Get queues assigned to this staff member
     */
    public function assignedQueues()
    {
        return $this->hasMany(FrontDeskQueue::class, 'assigned_staff_id');
    }

    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return $this->role === 'admin' || $this->user_type === 'admin';
    }

    /**
     * Check if user is manager
     */
    public function isManager()
    {
        return $this->role === 'manager' || $this->user_type === 'manager';
    }

    /**
     * Check if user is admin or manager (has elevated permissions)
     */
    public function isAdminOrManager()
    {
        return $this->isAdmin() || $this->isManager();
    }

    /**
     * Check if user is service staff
     */
    public function isServiceStaff()
    {
        return $this->role === 'staff' || $this->user_type === 'service_staff';
    }

    /**
     * Check if user can access a specific service
     */
    public function canAccessService($serviceId)
    {
        if ($this->isAdminOrManager()) {
            return true; // Admin and Manager can access all services
        }
        
        return $this->service_id == $serviceId;
    }

    /**
     * Check if user is front desk staff
     */
    public function isFrontDesk()
    {
        return $this->role === 'front_desk' || $this->user_type === 'front_desk';
    }

    /**
     * Check if user is medical staff (doctor, nurse, etc.)
     */
    public function isStaff()
    {
        return in_array($this->role, ['staff', 'doctor', 'nurse', 'pharmacist']) || 
               in_array($this->user_type, ['staff', 'doctor', 'nurse', 'pharmacist']);
    }

    /**
     * Check if user can access reports
     */
    public function canAccessReports()
    {
        return $this->isAdmin() || $this->isFrontDesk();
    }

    /**
     * Check if user can access medicine inventory
     */
    public function canAccessMedicines()
    {
        return $this->isAdmin() || $this->role === 'pharmacist' || $this->user_type === 'pharmacist';
    }

    /**
     * Check if user can access patient medical history
     */
    public function canAccessPatientHistory()
    {
        return $this->isAdmin() || 
               in_array($this->role, ['doctor', 'nurse']) ||
               in_array($this->user_type, ['doctor', 'nurse']);
    }
}
