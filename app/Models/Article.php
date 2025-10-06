<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;
    // public function interests()
    // {
    //     return $this->belongsToMany(Interests::class, 'article_interests');
    // }
    public function interests()
    {
        return $this->belongsToMany(Interests::class, 'article_interests', 'article_id', 'interest_id');
    }
}
