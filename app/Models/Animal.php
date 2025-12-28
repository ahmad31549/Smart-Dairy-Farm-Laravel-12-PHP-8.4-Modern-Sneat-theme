<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Animal extends Model
{
    use HasFactory;

    protected $fillable = [
        'animal_id',
        'name',
        'tag_number',
        'breed',
        'gender',
        'birth_date',
        'weight',
        'status'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'weight' => 'decimal:2'
    ];

    public function healthRecords()
    {
        return $this->hasMany(AnimalHealthRecord::class);
    }

    public function milkProduction()
    {
        return $this->hasMany(MilkProduction::class);
    }

    public function vaccinations()
    {
        return $this->hasMany(Vaccination::class);
    }

    public function getAgeAttribute()
    {
        return $this->birth_date->diffInYears(now());
    }

    public function getLatestHealthRecordAttribute()
    {
        return $this->healthRecords()->latest('check_date')->first();
    }
}
