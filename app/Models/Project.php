<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'created_by',
        'name',
        'description',
        'status',
    ];

    /**
     * Relasi: project dimiliki oleh satu user (creator)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi: satu project memiliki banyak task
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
