<?php

namespace Speca\SpecaCore\Enums;

use Speca\SpecaCore\Traits\EnumTrait;

enum Gender: string
{
    use EnumTrait;

    case FEMALE = 'FEMALE';
    case MALE = 'MALE';
    case OTHER = 'OTHER';

    /**
     * Returns the label corresponding to the current enum value.
     *
     * @return string The label of the current enum value.
     */
    public function label(): string
    {
        return match ($this) {
            self::FEMALE => __('Féminin'),
            self::MALE => __('Masculin'),
            self::OTHER => __('Autre'),
        };
    }

    /**
     * Courtesy.
     *
     * @return string The courtesy.
     */
    public function courtesy(): string
    {
        return match ($this) {
            self::OTHER => '',
            self::MALE => __('Monsieur'),
            self::FEMALE => __('Madame'),
        };
    }

    /**
     * Pronouns.
     *
     * @return string The courtesy.
     */
    public function pronouns(): string
    {
        return match ($this) {
            self::OTHER => '',
            self::MALE => __('Le'),
            self::FEMALE => __('La'),
            default => __('Le/La'),
        };
    }

    /**
     * Pronouns name.
     *
     * @return string The courtesy.
     */
    public function pronounsLabel(): string
    {
        return match ($this) {
            self::OTHER => '',
            self::MALE => __('Le nommé'),
            self::FEMALE => __('La nommée'),
            default => __('Le/La nommé(e)'),
        };
    }

    /**
     * Pronouns name.
     *
     * @return string The courtesy.
     */
    public function birthdayLabel(): string
    {
        return match ($this) {
            self::OTHER => '',
            self::MALE => __('Né le'),
            self::FEMALE => __('Née le'),
            default => __('Né(e) le'),
        };
    }
}
