<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorPromotion extends Model
{
    use HasFactory;

    public function doctor()
    {
        return $this->belongsTo(Doctors::class);
    }
}
