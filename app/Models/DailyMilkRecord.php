<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyMilkRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'total_milk_quantity',
        'total_buffaloes_milked',
        'sick_animals',
        'pregnant_animals',
        'male_animals',
        'total_herd_size',
        'recorded_by',
        'notes'
    ];

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
