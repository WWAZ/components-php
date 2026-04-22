<?php

namespace wwaz\RespectValidationExtension\Validation\Exceptions;

use Respect\Validation\Exceptions\ValidationException;

final class selectException extends ValidationException
{
    protected $defaultTemplates = [
        self::MODE_DEFAULT  => [
            self::STANDARD => '{{name}} is not a valid option. Please choose one of {{options}}',
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => '{{name}} must not be select',
        ],
    ];
}
