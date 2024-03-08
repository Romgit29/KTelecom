<?php

use App\Http\Resources\GeneralResponseResource;

function getSuccess($data)
{
    return new GeneralResponseResource([
        'success' => $data,
        'errors' => []
    ]);
}

function getError($data)
{
    return new GeneralResponseResource([
        'success' => [],
        'errors' => $data
    ]);
}

function getRidOfNestsed(array &$input, &$output = []): void
{
    foreach ($input as $value) {
        if (gettype($value) == 'array') {
            getRidOfNestsed($value, $output);
        } else {
            array_push($output, $value);
        }
    }
    $input = $output;
}
