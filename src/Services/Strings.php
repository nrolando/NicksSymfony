<?php

namespace App\Services;

class Strings
{
    public function my_mb_ucwords($p_str) {
        if(empty($p_str)) {
            return $p_str;
        }
        // A little capitalization statement (using mb.. since mb_ucfirst() doesn't exist)
        $words = explode(' ', $p_str);
        for($i = 0; $i < count($words); $i++) {
            $words[$i] = mb_strtoupper(mb_substr($words[$i], 0, 1)) . mb_strtolower(mb_substr($words[$i], 1));
        }
        return implode(' ', $words);
    }
}
