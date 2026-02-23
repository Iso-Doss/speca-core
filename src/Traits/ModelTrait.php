<?php

namespace Speca\SpecaCore\Traits;

use Illuminate\Support\Facades\Schema;
use Speca\SpecaCore\Enums\GroupActionType;
use Spatie\Activitylog\LogOptions;

trait ModelTrait
{
    /**
     * Get the status of the model.
     *
     * @return string The status.
     */
    public function getStatusAttribute(): string
    {
        return match (true) {
            !is_null($this->deleted_at) => GroupActionType::ARCHIVED->label(),
            is_null($this->activated_at) => GroupActionType::DEACTIVATED->label(),
            default => GroupActionType::ACTIVATED->label()
        };
    }

    /**
     * Get activity log options.
     *
     * @return LogOptions The log options
     */
    public function getActivityLogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(Schema::getColumnListing($this->getTable()));
    }
}
