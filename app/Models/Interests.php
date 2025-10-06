<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interests extends Model
{
    use HasFactory;
    public $table = "interests";
    public function articles()
    {
        return $this->belongsToMany(Article::class, 'article_interests', 'interest_id', 'article_id');
    }
}
