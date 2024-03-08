<?php

declare(strict_types=1);

namespace App\Services;

class ErrorWrapperService
{
    public $errors;
    public function __construct(array $errors, bool $segreateByInput = false)
    {
        $this->errors = $errors;
        return $this->init();
    }

    public function init()
    {
        $errorsMerged = $this->errors[0];
        $resultAray = [];
        unset($this->errors[0]);
        foreach ($this->errors as $key => $value) {
            $errorsMerged = array_merge($this->errors[$key], $errorsMerged);
        }
        usort($errorsMerged, [$this::class, "comparator"]);
        foreach ($errorsMerged as $key => $errorsArray) {
            $number = $errorsArray['requestFieldNumber'];
            $errors = $errorsArray['errors'];
            unset($errorsArray['requestFieldNumber']);
            unset($errorsArray['errors']);
            $resultAray["input_$number"] = $errors;
        }
        foreach ($resultAray as $key => $value) {
            getRidOfNestsed($resultAray[$key]);
        }
        $this->errors = $resultAray;
    }

    private function comparator($a, $b)
    {
        return $a['requestFieldNumber'] > $b['requestFieldNumber'];
    }
}
