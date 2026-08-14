<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_id',
        'caption',
        'image_url',
    ];

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }
}
