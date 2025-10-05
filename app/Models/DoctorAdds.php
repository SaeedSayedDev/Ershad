<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorAdds extends Model
{
    use HasFactory;
    public $table = "doctor_adds";
    public function doctor()
    {
        return $this->hasOne(Doctors::class, 'id', 'doctor_id');
    }

    protected $fillable = [
        'doctor_id',
        'start_date',
        'number_days',
        'payment_method',
        'total_amount',
        'taxs',
        'payment_staus',
        'rejection_reason',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'total_amount' => 'decimal:2',
        'taxs' => 'decimal:2',
        'payment_staus' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the doctor associated with the promotion
     */
   
    /**
     * Scope a query to only include pending promotions
     */
    public function scopePending($query)
    {
        return $query->where('payment_staus', 0);
    }

    /**
     * Scope a query to only include approved promotions
     */
    public function scopeApproved($query)
    {
        return $query->where('payment_staus', 1);
    }

    /**
     * Scope a query to only include rejected promotions
     */
    public function scopeRejected($query)
    {
        return $query->where('payment_staus', 2);
    }

    /**
     * Get status name
     */
    public function getStatusNameAttribute()
    {
        return match ($this->payment_staus) {
            0 => 'Pending',
            1 => 'Approved',
            2 => 'Rejected',
            default => 'Unknown'
        };
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeAttribute()
    {
        return match ($this->payment_staus) {
            0 => 'badge-warning',
            1 => 'badge-success',
            2 => 'badge-danger',
            default => 'badge-secondary'
        };
    }

    /**
     * Calculate end date based on start date and number of days
     */
    public function getEndDateAttribute()
    {
        if ($this->start_date && $this->number_days) {
            return date('Y-m-d', strtotime($this->start_date . ' +' . $this->number_days . ' days'));
        }
        return null;
    }

    /**
     * Check if promotion is active
     */
    public function isActive()
    {
        if ($this->payment_staus !== 1) {
            return false;
        }

        $today = date('Y-m-d');
        $endDate = $this->end_date;

        return $today >= $this->start_date && $today <= $endDate;
    }
}
