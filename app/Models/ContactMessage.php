<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'preferred_locale',
        'status',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    /**
     * Scope for unread messages.
     */
    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('status', 'new')->whereNull('read_at');
    }

    /**
     * Scope filter by status.
     */
    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Mark message as read.
     */
    public function markAsRead(): bool
    {
        return $this->update([
            'status' => 'read',
            'read_at' => now(),
        ]);
    }

    /**
     * Mark message as replied.
     */
    public function markAsReplied(): bool
    {
        return $this->update([
            'status' => 'replied',
            'read_at' => $this->read_at ?: now(),
        ]);
    }

    /**
     * Mark message as archived.
     */
    public function markAsArchived(): bool
    {
        return $this->update([
            'status' => 'archived',
        ]);
    }
}
