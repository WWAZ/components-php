<?php

namespace wwaz\Components;

interface ComponentInterface
{
    /**
     * Returns component's markup
     *
     * @return string
     */
    public function markup(): string;
}