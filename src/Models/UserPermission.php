<?php

namespace Speca\SpecaCore\Models;


use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Speca\SpecaCore\Traits\ModelTrait;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Models\Permission as SpatiePermission;
use Spatie\Permission\PermissionRegistrar;

class UserPermission extends SpatiePermission
{
    use HasFactory, SoftDeletes, LogsActivity, HasUuids, ModelTrait;

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
        'deleted_at'
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
    protected $appends = ['status'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var string[]
     */
    protected $hidden = [];

    /**
     * The relation to permission categories.
     *
     * @return BelongsToMany<UserPermissionCategory> The relation with permission categories.
     */
    public function userPermissionCategories(): BelongsToMany
    {
        $registrar = app(PermissionRegistrar::class);

        return $this->belongsToMany(
            config('permission.models.permission'),
            config('permission.table_names.category_has_permissions'),
            $registrar->pivotPermission,
            'permission_category_id',
        );
    }
}
