<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = ['question', 'points', 'category_id'];

    public function category()
    {
        return $this->belongsTo(DoctorCategories::class, 'category_id');
    }

    public function choices()
    {
        return $this->hasMany(Choice::class, 'question_id');
    }
}
