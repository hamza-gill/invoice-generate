<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class UserSubscription extends Model
{
    protected $fillable = [
        'user_id',
        'plan_id',
        'start_date',
        'end_date',
        'status',
        'limit_count',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public static function decreaseLimit()
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        $subscription = self::where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        if (!$subscription) {
            return false;
        }

        // Handle unlimited usage
        if ($subscription->limit_count === -1) {
            return -1; // unlimited always returns -1
        }

        // If no credits left
        if ($subscription->limit_count <= 0) {
            return false;
        }

        // Decrease usage
        $subscription->limit_count -= 1;
        $subscription->save();

        return $subscription->limit_count; // remaining credits
    }

    /**
     * Check if subscription is currently active.
     */
    public function isActive()
    {
        return $this->status === 'active' && $this->end_date->isFuture();
    }

    /**
     * Automatically expire subscription if past end_date.
     */
    protected static function booted()
    {
        static::retrieved(function ($subscription) {
            if ($subscription->end_date && $subscription->end_date->isPast() && $subscription->status !== 'expired') {
                $subscription->update(['status' => 'expired']);
            }
        });
    }
}
