<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class article extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'text',
        'approval',
        'user_id'
    ];

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    static function searchArticles($query)
    {
        return Article::where('title', 'LIKE', '%' . $query . '%')
            ->orWhere('text', 'LIKE', '%' . $query . '%')
            ->get();
    }
}
