<?php

namespace Speca\SpecaCore\Enums;

use Speca\SpecaCore\Traits\EnumTrait;

enum GroupActionType: string
{
    use EnumTrait;

    case ACTIVATED = 'ACTIVATED';
    case DEACTIVATED = 'DEACTIVATED';
    case ARCHIVED = 'ARCHIVED';
    case DELETED = 'DELETED';
    case RESTORED = 'RESTORED';
    case UPDATED = 'UPDATED';

    /**
     * Returns the label corresponding to the current enum value.
     *
     * @return string The label of the current enum value.
     */
    public function label(): string
    {
        return match ($this) {
            self::ACTIVATED => __('Activer'),
            self::DEACTIVATED => __('Désactiver'),
            self::ARCHIVED => __('Archiver'),
            self::DELETED => __('Supprimer'),
            self::RESTORED => __('Restaurer'),
            self::UPDATED => __('Modifier'),
        };
    }
}
