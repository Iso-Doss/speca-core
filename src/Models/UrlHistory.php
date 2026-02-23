<?php

namespace Speca\SpecaCore\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;
use Spatie\Activitylog\LogOptions;

class UrlHistory extends SpecaCoreBaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string|null
     */
    protected $table = 'url_shortener_histories';

    /**
     * The attributes that are mass-assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'url_id',
        'data',
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
        'data' => 'array',
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
     * Get the url.
     *
     * @return BelongsTo That belongs to.
     */
    public function url(): BelongsTo
    {
        return $this->belongsTo(Url::class);
    }

    /**
     * Get the activity log options.
     *
     * @return LogOptions The log options.
     */
    public function getActivityLogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(Schema::getColumnListing(new UrlHistory()->getTable()));
    }
}
