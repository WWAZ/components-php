<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use wwaz\Components\BaseComponent;
use wwaz\Components\Component;

class InspectableBaseComponent extends BaseComponent
{
    protected $properties = [
        'attributes' => [
            'id' => 'isString',
            'class' => 'isString',
            'href' => 'isString',
        ],
        'content' => [
            'title' => 'isString',
            'body' => 'isString',
        ],
    ];

    protected function markup()
    {
        return '<article' . ($this->htmlAttributes() ?: '') . '>' . $this->getContentMarkup($this->getContent()) . '</article>';
    }

    public function htmlAttributesPublic(): string|false
    {
        return $this->htmlAttributes();
    }

    public function isComponentObjectPublic(mixed $value): bool
    {
        return $this->isComponentObject($value);
    }

    public function getTypePublic(): string
    {
        return $this->getType();
    }
}

class InspectableChildBaseComponent extends InspectableBaseComponent
{
    protected $properties = [
        'attributes' => [
            'data-role' => 'isString',
        ],
        'data' => [
            'size' => 'isString',
        ],
    ];
}

class InspectableComponent extends Component
{
    protected $properties = [
        'attributes' => [
            'id' => 'isString',
        ],
        'data' => [
            'variant' => 'isString',
        ],
        'content' => [
            'title' => 'isString',
        ],
    ];

    protected function markup()
    {
        return '<section data-title="' . $this->cr('title') . '">Body</section>';
    }

    public function getComponentClassPublic(): string
    {
        return $this->getComponentClass();
    }
}

class SimpleNestedComponent extends BaseComponent
{
    protected $properties = [
        'content' => [
            'text' => 'isString',
        ],
    ];

    protected function markup()
    {
        return '<span>' . $this->getContentMarkup($this->getContent()) . '</span>';
    }
}
