<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentTrack extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'document_id', 'metadata'];

    protected $casts = [
        'metadata' => 'json'
    ];

    public function user()
    {

    }

    public function documnet()
    {

    }

    public function share_user()
    {

    }
}
