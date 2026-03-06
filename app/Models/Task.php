<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'project_id',
        'category_id',
        'created_by',
        'deleted_by',
        'title',
        'description',
        'due_date',
        'deleted_at',
    ];

    protected $casts = [
        'due_date'   => 'date',
        'deleted_at' => 'datetime',
    ];

    /**
     * Relasi: task dimiliki oleh satu project
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Relasi: task memiliki satu kategori
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relasi: task dibuat oleh satu user
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi: task dihapus oleh satu user (nullable)
     */
    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Scope: hanya task yang belum di-soft delete
     */
    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }

    /**
     * Scope: hanya task yang sudah di-soft delete
     */
    public function scopeTrashed($query)
    {
        return $query->whereNotNull('deleted_at');
    }
}
