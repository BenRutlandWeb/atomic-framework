<?php

namespace Atomic\Support;

class Hash
{
    /**
     * Hash the string
     *
     * @param string $value
     * @return string
     */
    public static function make(string $value): string
    {
        return password_hash(static::hash($value), PASSWORD_BCRYPT);
    }

    /**
     * Verify a value and hash match
     *
     * @param string $value
     * @param string $hash
     * @return bool
     */
    public static function check(string $value, string $hash): bool
    {
        if (strlen($hash) === 0) {
            return false;
        }
        return password_verify(static::hash($value), $hash);
    }

    /**
     * "Pepper" the hash
     *
     * @param string $value
     * @return string
     */
    private static function hash(string $value): string
    {
        return hash_hmac("sha256", $value, app('config')->get('app.key'));
    }
}
