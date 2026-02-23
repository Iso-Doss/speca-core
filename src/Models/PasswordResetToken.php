<?php

namespace Speca\SpecaCore\Models;

class PasswordResetToken extends SpecaCoreBaseModel
{
    /**
     * The attributes that are mass-assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'email',
        'new_email',
        'type',
        'token',
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
}
