<?php

namespace Atomic\Validation;

use Atomic\Http\Request;
use Atomic\Contracts\Validation\Rule;
use Atomic\Validation\Rules\Required;
use Atomic\Validation\Traits\ValidatesAttributes;
use Atomic\Validation\ValidationException;

class Validator
{
    use ValidatesAttributes;

    /**
     * The validation errors
     *
     * @var array
     */
    protected $errors = [];

    /**
     * Validate the request inputs
     *
     * @param \Atomic\Http\Request $request
     * @param array $rules
     * @param array $messages
     * @return array
     *
     * @throws \Atomic\Validation\ValidationException
     */
    public function validate(Request $request, array $rules, array $messages = [])
    {
        foreach ($this->parseRules($rules) as $attribute => $rules) {
            foreach ($rules as $rule) {
                $this->resolveRule($rule, $attribute, $request[$attribute]);
            }
        }
        if (!empty($this->errors)) {
            throw (new ValidationException())->setErrorBag($this->errors);
        }

        return $request;
    }

    /**
     * Asserrts a rule passes or fails
     *
     * @param Rule $rule
     * @param string $attribute
     * @param mixed $value
     * @return void
     */
    public function resolveRule(Rule $rule, string $attribute, $value)
    {
        if ($rule instanceof Required) {
            if (!$rule->passes($attribute, $value)) {
                $this->errors[$attribute][] = $this->parseMessage($attribute, $rule->message());
            }
        } else {
            if ($value && !$rule->passes($attribute, $value)) {
                $this->errors[$attribute][] = $this->parseMessage($attribute, $rule->message());
            }
        }
    }

    /**
     * Parse the rules into instances of the Rule interface
     *
     * @param array $rules
     * @return array
     */
    public function parseRules(array $rules): array
    {
        $parsedRules = [];

        foreach ($rules as $attribute => $singleRules) {
            $validation = [];

            $singleRules = is_string($singleRules) ? explode('|', $singleRules) : $singleRules;

            $singleRulesArray = is_array($singleRules) ? $singleRules : [$singleRules];

            foreach ($singleRulesArray  as $rule) {

                if ($rule instanceof Rule) {
                    $validation[] = $rule;
                } else {
                    $parts = explode(':', $rule);

                    $method = 'validate' . ucwords($parts[0]);

                    if (method_exists($this, $method)) {
                        $validation[] = call_user_func_array([$this, $method], explode(',', $parts[1] ?? ''));
                    }
                }
            }
            $parsedRules[$attribute] = $validation;
        }
        return $parsedRules;
    }

    /**
     * Replace placeholders in a message
     *
     * @param string $attribute
     * @param string $message
     * @return string
     */
    public function parseMessage(string $attribute, string $message): string
    {
        return str_replace(':attribute', $attribute, $message);
    }
}
