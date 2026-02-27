<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'title',
        'description',
        'event_date',
        'max_participants',
        'price',
        'is_active'
    ];

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }
}
