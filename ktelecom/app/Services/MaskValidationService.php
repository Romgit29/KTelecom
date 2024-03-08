<?php

namespace App\Services;

class MaskValidationService
{
    public $rulesDict = [
        'N' => "[0-9]",
        'A' => "[A-Z]",
        'a' => "[a-z]",
        'X' => "[A-Z0-9]",
        'Z' => "[-_@]"
    ];

    public function maskMatch($serialNumber, $mask)
    {
        $rules = "^";
        $split = str_split($mask);
        foreach ($split as $key => $value) {
            $subString = substr($mask, $key);
            $subSplit = str_split($subString);
            $rule = $subSplit[0];
            $count = 0;
            if (array_key_exists($key - 1, $split) && $split[$key - 1] === $split[$key]) continue;
            foreach ($subSplit as $subValue) {
                if ($subValue == $rule) $count = $count + 1;
                else break;
            }
            $rules .= $this->rulesDict[$rule] . '{' . $count . '}';
        }
        $rules .= "$";
        if (preg_match("/$rules/", $serialNumber)) {
            return true;
        } else {
            return false;
        }
    }
}
