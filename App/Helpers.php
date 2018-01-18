<?php

namespace App;

class Helpers
{
    /**
     * @param int $size The size of the returned string. Sometimes the returned string is 1 character shorter.
     */
    public function getUniqueToken(int $size = 40): string
    {
        $bytes = random_bytes($size / 2);
        return bin2hex($bytes);
    }
}