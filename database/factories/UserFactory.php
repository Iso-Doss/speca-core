<?php

namespace Speca\SpecaCore\Database\Factories;

use Speca\SpecaCore\Models\User;

class UserFactory
{
    /**
     * The model.
     *
     * @var string $model The model.
     */
    protected string $model = User::class;

    /**
     * The definition.
     *
     * @return array The definition.
     */
    public function definition(): array
    {
        return [];
    }
}
