<?php

namespace wwaz\Components\Exceptions;

class ComponentValidationException
{
    protected $msg;

    public function __construct($msg)
    {
        $this->msg = $msg;
    }
}
