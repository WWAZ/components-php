<?php
namespace wwaz\RespectValidationExtension\Validation\Exceptions;

use Respect\Validation\Exceptions\ValidationException;

final class isIntException extends ValidationException
{
    protected $defaultTemplates = [
        self::MODE_DEFAULT  => [
            self::STANDARD => '{{name}} must be integer',
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => '{{name}} is no integer',
        ],
    ];
}
