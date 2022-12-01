<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Post extends Model
{
    use HasFactory;

    public function scopeBetweenDate($query, $from, $to)
    {
        return $query->whereBetween('created_at', [Carbon::parse($from), Carbon::parse($to)]);
    }
    
    protected $table = 'posts';

    protected $fillable = [
        'id',
        'name',
        'about',
        'media',
        'user_id',
    ];
}
