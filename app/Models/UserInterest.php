<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserInterest extends Model
{
    use HasFactory;
    public $table = "user_interests";
    public function interest()
    {
        return $this->belongsTo(Interests::class, 'interest_id', 'id');
    }
}
