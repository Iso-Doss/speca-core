<?php

namespace Speca\SpecaCore\Models;


use Illuminate\Database\Eloquent\Relations\HasMany;

class UserProfile extends SpecaCoreBaseModel
{
    /**
     * The attributes that are mass-assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
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
    protected $appends = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var string[]
     */
    protected $hidden = [];

    /**
     * Get the user profile.
     *
     * @return HasMany That belongs to.
     */
    public function user(): HasMany
    {
        return $this->hasMany(User::class, 'user_profile_id');
    }

}
