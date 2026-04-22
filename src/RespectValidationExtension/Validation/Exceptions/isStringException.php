<?php

namespace wwaz\RespectValidationExtension\Validation\Exceptions;

use Respect\Validation\Exceptions\ValidationException;

final class isStringException extends ValidationException
{
    protected $defaultTemplates = [
        self::MODE_DEFAULT  => [
            self::STANDARD => '{{name}} must be string',
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => '{{name}} is no string',
        ],
    ];
}
