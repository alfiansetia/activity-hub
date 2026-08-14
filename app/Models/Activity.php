<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'title',
        'descriptions',
        'rules',
        'tools',
        'user_id',
        'company_id',
        'status',
        'accept_by',
        'reject_by',
        'reject_reason',
        'reject_at',
        'accept_at',
        're_submit_at',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'datetime',
            'reject_at' => 'datetime',
            'accept_at' => 'datetime',
            're_submit_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function acceptor()
    {
        return $this->belongsTo(User::class, 'accept_by');
    }

    public function rejector()
    {
        return $this->belongsTo(User::class, 'reject_by');
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }
}
