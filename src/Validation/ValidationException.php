<?php

namespace Atomic\Validation;

use Exception;

class ValidationException extends Exception
{
    /**
     * The validation error bag
     *
     * @var array
     */
    protected $errorBag;

    /**
     * Get the error bag
     *
     * @return array
     */
    public function getErrorBag(): array
    {
        return $this->errorBag;
    }

    /**
     * Set the error bag
     *
     * @param array $errorBag
     * @return self
     */
    public function setErrorBag(array $errorBag): self
    {
        $this->errorBag = $errorBag;

        return $this;
    }
}
