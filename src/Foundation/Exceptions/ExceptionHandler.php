<?php

namespace Atomic\Foundation\Exceptions;

use Throwable;
use Atomic\Auth\Exceptions\AuthenticationException;
use Atomic\Auth\Exceptions\TokenMismatchException;
use Atomic\Validation\ValidationException;

class ExceptionHandler
{
    /**
     * The exception to handle
     *
     * @var \Throwable
     */
    protected $exception;

    /**
     * Handle the exception
     *
     * @param \Throwable $exception
     */
    public function __construct(Throwable $exception)
    {
        $this->exception = $exception;
    }

    /**
     * Handle the exception
     *
     * @return void
     */
    public function handle()
    {
        if ($this->exception instanceof AuthenticationException) {
            return status_header(401, $this->exception->getMessage());
        }
        if ($this->exception instanceof ValidationException) {
            status_header(422);
            header('Content-Type: application/json');
            return json_encode(['errors' => $this->exception->getErrorBag()]);
        }
        if ($this->exception instanceof TokenMismatchException) {
            return status_header(401, $this->exception->getMessage());
        }
    }
}
