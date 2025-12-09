<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_type',
        'user_id',
        'action',
        'description',
        'data',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Define separate relationships for each user type
    public function systemUser()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function patientUser()
    {
        return $this->belongsTo(PatientAccount::class, 'user_id');
    }

    // Get the actual user based on user_type
    public function getActualUserAttribute()
    {
        if ($this->user_type === 'user') {
            return $this->systemUser;
        } elseif ($this->user_type === 'patient') {
            return $this->patientUser;
        }
        return null;
    }

    // Get user name accessor
    public function getUserNameAttribute()
    {
        if ($this->user_type === 'user' && $this->systemUser) {
            return $this->systemUser->name;
        } elseif ($this->user_type === 'patient' && $this->patientUser) {
            return $this->patientUser->first_name . ' ' . $this->patientUser->last_name;
        }
        return 'Unknown User';
    }

    public static function log($action, $description, $data = null, $userType = 'user', $userId = null)
    {
        $userId = $userId ?? auth()->id();
        
        if (!$userId) return;

        self::create([
            'user_type' => $userType,
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
            'data' => $data,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
