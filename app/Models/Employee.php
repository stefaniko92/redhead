<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    const TYPE_PROFESSOR = 'professor';
    const TYPE_TRADER = 'trader';

    protected $fillable = [
        'type',
        'available_hours',
        'working_hours',
    ];

    protected $casts = [
        'type' => 'string',
        'available_hours' => 'integer',
        'working_hours' => 'integer',
    ];

    protected $guarded = [];

    public function user()
    {
        return $this->morphOne('App\Models\User', 'profile');
    }
}
