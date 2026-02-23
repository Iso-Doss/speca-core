<?php

namespace Speca\SpecaCore\Database\Factories;

use Speca\SpecaCore\Models\PasswordResetToken;

class PasswordResetTokenFactory
{
    /**
     * The model.
     *
     * @var string $model The model.
     */
    protected string $model = PasswordResetToken::class;

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
