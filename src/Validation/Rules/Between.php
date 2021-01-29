<?php

namespace Atomic\Validation\Rules;

use Atomic\Contracts\Validation\Rule;

class Between implements Rule
{
    /**
     * The minimum value
     *
     * @var int
     */
    protected $min;

    /**
     * The maximum value
     *
     * @var int
     */
    protected $max;

    /**
     * Create the rule instance
     *
     * @param int $min
     * @param int $max
     */
    public function __construct(int $min, int $max)
    {
        $this->min = $min;
        $this->max = $max;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes(string $attribute, $value): bool
    {
        return $value >= $this->min && $value <= $this->max;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return ":attribute is not between {$this->min} and {$this->max}.";
    }
}
