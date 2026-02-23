<?php

namespace Speca\SpecaCore\Traits;

trait EnumTrait
{
    /**
     * Retrieves an array of options where each option contains a label and a value
     * derived from the instances of the current enum cases.
     *
     * @return array The array of options formatted as label-value pairs.
     */
    public static function getOptions(): array
    {
        return array_values(array_map(fn(self $t) => [
            'label' => $t->label(),
            'value' => $t->value,
        ], self::cases()));
    }

    /**
     * Retrieves an array of options where each option contains a label and a value
     * derived from the instances of the current enum cases.
     *
     * @param string|null $entity The entity.
     * @param string|null $gender The gender.
     * @return array The array of options formatted as label-value pairs.
     */
    public static function getFilterOptions(?string $entity = null, ?string $gender = null): array
    {
        return array_merge(FilterStatus::getOptions($entity, $gender), self::getOptions());
    }

    /**
     * Retrieves an array of values from the instances of the current enum cases.
     *
     * @return array The array of values extracted from the enum cases.
     */
    public static function getValues(): array
    {
        return array_column(static::cases(), 'value');
    }

    /**
     * Retrieves an array of values from the instances of the current enum cases.
     *
     * @return array The array of values extracted from the enum cases.
     */
    public static function getFilterValues(): array
    {
        return array_merge(FilterStatus::getValues(), self::getValues());
    }
}
