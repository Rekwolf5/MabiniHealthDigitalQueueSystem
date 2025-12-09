<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportMessage extends Model
{
    protected $fillable = [
        'user_type',
        'user_id',
        'name',
        'email',
        'subject',
        'message',
        'status',
        'admin_reply',
        'replied_at',
    ];

    protected $casts = [
        'replied_at' => 'datetime',
    ];

    /**
     * Get the user who sent the message (polymorphic)
     */
    public function user()
    {
        if ($this->user_type === 'patient') {
            return $this->belongsTo(\App\Models\PatientAccount::class, 'user_id');
        }
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}
