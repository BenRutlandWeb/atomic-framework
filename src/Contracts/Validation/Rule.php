<?php

namespace Atomic\Contracts\Validation;

interface Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes(string $attribute, $value): bool;

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string;
}
