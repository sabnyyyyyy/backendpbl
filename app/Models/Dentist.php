<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dentist extends Model
{
    protected $fillable = [
        'name',
        'specialization',
        'photo'
    ];

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function schedules()
    {
        return $this->hasMany(DentistSchedule::class);
    }
}