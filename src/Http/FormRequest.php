<?php

namespace Atomic\Http;

class FormRequest extends Request
{
    /**
     * Validate the request before getting to the controller
     *
     * @return static
     */
    public function validateResolved()
    {
        return $this->validate($this->rules(), $this->messages());
    }

    /**
     * The form request rules
     *
     * @return array
     */
    public function rules(): array
    {
        return [];
    }

    /**
     * The form request messages
     *
     * @return array
     */
    public function messages(): array
    {
        return [];
    }
}
