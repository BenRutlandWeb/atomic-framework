<?php

namespace Atomic\Validation\Traits;

use Atomic\Contracts\Validation\Rule;

trait ValidatesAttributes
{
    /**
     * Validate an email address
     *
     * @return \Atomic\Contracts\Validation\Rule
     */
    public function validateEmail(): Rule
    {
        return new \Atomic\Validation\Rules\Email();
    }

    /**
     * Validate a required attribute
     *
     * @return \Atomic\Contracts\Validation\Rule
     */
    public function validateRequired(): Rule
    {
        return new \Atomic\Validation\Rules\Required();
    }

    /**
     * Validate a value is in a range
     *
     * @return \Atomic\Contracts\Validation\Rule
     */
    public function validateBetween(int $min, int $max): Rule
    {
        return new \Atomic\Validation\Rules\Between($min, $max);
    }
}
