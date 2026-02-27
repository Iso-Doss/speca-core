<?php

namespace Speca\SpecaCore\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Models\Role as SpatieRole;
use Speca\SpecaCore\Traits\ModelTrait;

class UserRole extends SpatieRole
{
    use HasFactory, HasUuids, LogsActivity, ModelTrait, SoftDeletes;

    /**
     * The attributes that are mass-assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'label',
        'name',
        'description',
        'guard_name',
        'activated_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'activated_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The relations an eager load on every query.
     *
     * @var string[]
     */
    protected $with = [];

    /**
     * The accessors to append to the model's array form.
     *
     * @var string[]
     */
    protected $appends = ['status', 'user_count'];

    /**
     * Get the user's sum according to the role.
     *
     * @return int The sum.
     */
    public function getUserCountAttribute(): int
    {
        return $this->users->count();
    }
}
