<?php

namespace dbapi\Utilities;

class Hash
{
    const PASSWORD_TIME_COST = 11;  // # of iterations

    /**
     * Hash password string using default - currently bcrypt
     *
     * @param $string
     * @return bool|string
     */
    public static function makePass($string){
        return password_hash($string, PASSWORD_DEFAULT, ['cost' => self::PASSWORD_TIME_COST] );
    }

    /**
     * Verify string against the hash.
     *
     * @param string $string
     * @param string $hash
     * @return bool
     */
    public static function verify($string, $hash)
    {
        return password_verify($string, $hash);
    }
}

?>