<?php

namespace Atomic\Contracts\Support;

interface Jsonable
{
    /**
     * Get the instance as an array.
     *
     * @param int $options
     * @return array
     */
    public function toJson(int $options = 0): string;
}
