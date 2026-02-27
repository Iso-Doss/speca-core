<?php

namespace Speca\SpecaCore\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;
use Speca\SpecaCore\Database\Factories\UserFactory;
use Speca\SpecaCore\Traits\ModelTrait;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasPermissions, HasRoles, HasUuids, LogsActivity, ModelTrait, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass-assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'phone_with_indicative',
        'type',
        'gender',
        'password',
        'address',
        'birthday',
        'activation_code',
        'reset_password_code',
        'full_name',
        'avatar',
        'deleted_reason',
        'remember_token',
        'country_id',
        'user_profile_id',
        'activated_at',
        'notification_enable_at',
        'activation_code_expire_at',
        'reset_password_code_expire_at',
        'cgu_validated_at',
        'cgu_rested_at',
        'two_factor_enabled_at',
        'two_factor_generated_at',
        'two_factor_emergency_keys_copied_at',
        'two_factor_number_failed_login_attempts',
        'two_factor_last_failed_login_attempted_at',
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
        'password' => 'hashed',
        'notification_enable_at' => 'datetime',
        'activation_code_expire_at' => 'datetime',
        'reset_password_code_expire_at' => 'datetime',
        'cgu_validated_at' => 'datetime',
        'cgu_rested_at' => 'datetime',
        'two_factor_enabled_at' => 'datetime',
        'two_factor_generated_at' => 'datetime',
        'two_factor_emergency_keys_copied_at' => 'datetime',
        'two_factor_last_failed_login_attempted_at' => 'datetime',
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
    protected $hidden = [
        'password',
        'remember_token',
        'master_key',
        'activation_code',
        'reset_password_code',
    ];

    /**
     * Get the user country.
     *
     * @return BelongsTo That belongs to.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the user residence country.
     *
     * @return BelongsTo That belongs to.
     */
    public function residenceCountry(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'residence_country_id');
    }

    /**
     * Get the user nationality country.
     *
     * @return BelongsTo That belongs to.
     */
    public function nationalityCountry(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'nationality_country_id');
    }

    /**
     * Get the user profile.
     *
     * @return BelongsTo That belongs to.
     */
    public function userProfiles(): BelongsTo
    {
        return $this->belongsTo(UserProfile::class, 'user_profile_id');
    }
}
