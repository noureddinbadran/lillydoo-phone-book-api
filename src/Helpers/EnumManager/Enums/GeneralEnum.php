<?php

namespace App\Helpers\EnumManager\Enums;

use App\Helpers\EnumManager\EnumTrait;

final class GeneralEnum
{
    use EnumTrait;

    const SUCCESS = 'success';
    const INTERNAL_ERROR = 'internal_error';
    const VALIDATION = 'validation';
    const INVALID= 'invalid';
    const INVALID_CREDENTIALS = 'invalid_credentials';
    const NOT_FOUND = 'not_found';
    const ALREADY_RATED = 'already_rated';
}