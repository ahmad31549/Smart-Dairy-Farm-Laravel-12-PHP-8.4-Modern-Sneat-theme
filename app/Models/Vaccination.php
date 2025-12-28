<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vaccination extends Model
{
    use HasFactory;

    protected $fillable = [
        'animal_id',
        'vaccine_name',
        'date_administered',
        'next_due_date',
        'batch_number',
        'veterinarian',
        'notes',
        'status'
    ];

    protected $casts = [
        'date_administered' => 'date',
        'next_due_date' => 'date',
    ];

    public function animal()
    {
        return $this->belongsTo(Animal::class);
    }

    public function getStatusAttribute($value)
    {
        if ($value) {
            return $value;
        }

        if (!$this->next_due_date) {
            return 'completed';
        }

        $today = now()->startOfDay();
        /** @var \Carbon\Carbon $dueDate */
        $dueDate = $this->next_due_date;

        if ($dueDate < $today) {
            return 'overdue';
        } elseif ($dueDate->diffInDays($today) <= 7) {
            return 'due';
        } else {
            return 'completed';
        }
    }
}

