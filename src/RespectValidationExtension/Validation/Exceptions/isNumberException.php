<?php

namespace wwaz\RespectValidationExtension\Validation\Exceptions;

use Respect\Validation\Exceptions\ValidationException;

final class isNumberException extends ValidationException
{
    protected $defaultTemplates = [
      self::MODE_DEFAULT => [
        self::STANDARD => '{{name}} must be number',
      ],
      self::MODE_NEGATIVE => [
        self::STANDARD => '{{name}} is no number',
      ],
    ];
}
