<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;

class Document extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'checksum', 'store_path', 'is_public', 'created_by'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function tracks(): HasMany
    {
        return $this->hasMany(DocumentTrack::class);
    }

    public function current_user_track(): HasOne
    {
        return $this->hasOne(DocumentTrack::class)
            ->where('user_id', Auth::id());
    }
}
