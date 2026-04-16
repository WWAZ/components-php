<?php

namespace wwaz\RespectValidationExtension\Validation\Exceptions;

use Respect\Validation\Exceptions\ValidationException;

final class isComponentException extends ValidationException
{
    protected $defaultTemplates = [
      self::MODE_DEFAULT => [
        self::STANDARD => '{{name}} must be component object',
      ],
      self::MODE_NEGATIVE => [
        self::STANDARD => '{{name}} is no component object',
      ],
    ];
}
