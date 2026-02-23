<?php

namespace Speca\SpecaCore\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends SpecaCoreBaseModel
{
    /**
     * The attributes that are mass-assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'code',
        'iso_code',
        'phone_code',
        'flag',
        'default_currency',
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
    protected $appends = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var string[]
     */
    protected $hidden = [];

    /**
     * Get the user's country.
     *
     * @return HasMany That belongs to.
     */
    public function usersCountry(): HasMany
    {
        return $this->hasMany(User::class, 'country_id');
    }

    /**
     * Get the user's residence country.
     *
     * @return HasMany That belongs to.
     */
    public function usersResidenceCountry(): HasMany
    {
        return $this->hasMany(User::class, 'residence_country_id');
    }

    /**
     * Get the user's nationality country.
     *
     * @return HasMany That belongs to.
     */
    public function usersNationalityCountry(): HasMany
    {
        return $this->hasMany(User::class, 'nationality_country_id');
    }
}
