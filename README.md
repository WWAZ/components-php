# Components

A PHP library for building data-driven UI components.

Define composable, nestable components that render plain data into clean HTML, XML, or SVG — declaratively, with built-in validation and no template engine needed.

Works great for rendering structured content from headless CMSs or APIs, generating server-side UI fragments, producing email markup, or assembling document-like output.

After installation:

```php
$link = Factory::make('fragment.html.a', [
    'href'    => 'https://example.com',
    'class'   => 'btn btn-primary',
    'content' => 'Get started',
]);

echo $link->render();
// <a href="https://example.com" class="btn btn-primary">Get started</a>
```

## Installation

```bash
composer require wwaz/components-php
```

## Usage

### Register components

Register the namespace for your components once, then use it anywhere:

```php
use wwaz\Components\Factory;

Factory::addNamespace('MyApp\\Components');
```

### Creating components

A component is created by extending Component class. Each component defines its own properties, which can be freely configured and are passed during construction. Validation rules control the expected type and whether a value is required. Every component also implements a markup() method that returns the rendered HTML.

```php
namespace MyApp\Components;

use wwaz\Components\Component;

// Define the component class
class Card extends Component
{
    protected $properties = [
        'content' => [
            'title' => 'required|isString',
            'text'  => 'required|isString',
        ],
    ];
    protected function markup(): string
    {
        return '
            <h2 class="card__title">' . $this->content('title') . '</h2>
            <p class="card__text">' . $this->content('text') . '</p>
        ';
    }
}

// Create the component instance with its content
$component = new Card([
    'title' => 'My Card',
    'text' => 'My text'    
]);

// Render and output the HTML markup
echo $component->render();
```


### Validating Component properties

Properties are validated automatically before rendering. Declare rules in your component:

```php
protected $properties = [
    'content' => [
        'title'  => 'required|isString|length:1,100',
        'status' => 'select:draft,published|default:draft',
        'teaser' => 'isString',
        'link' => 'isComponent|required'
    ],
];
```

Available validators and rule helpers in this package:

| Rule | Description |
| --- | --- |
| `required` | The value must be present. |
| `isString` | The value must be a PHP string. |
| `isInt` | The value must be a PHP integer. |
| `isNumber` | The value must be numeric. |
| `isComponent` | The value must be an instance of a class extending `wwaz\Components\Component`.|
| `select:foo,bar,baz` | The value must match one of the listed options. |
| `default:value` | Sets a fallback value when the property is missing. This is a rule helper, not a validator. |

In addition to these package-specific rules, you can also use native `Respect\Validation` rules such as `length`, `boolVal`, and many others.

Invalid data stops execution with a clear HTML error message, which is helpful during development and easy to catch in production.


### Manipulating instances at runtime

Every fragment and component shares the same fluent API for attributes and classes:

```php
$el = Factory::make('fragment.html.a', ['href' => '#']);

$el->setAttribute('aria-label', 'Home');
$el->addClass('is-active');
$el->addContent(' (current)');

echo $el->render();
// <a href="#" aria-label="Home" class="is-active"> (current)</a>
```

On components you can also control the wrapper element:

```php
$card->setWrapTag('article');
$card->addComponentClass('dark-mode');

echo $card->render();
// <article class="myapp components card dark-mode">...</article>

// Or drop the wrapper entirely:
echo $card->render(false);
```

### Configuration

Global settings are inherited by all namespaces. Override per namespace as needed:

```php
use wwaz\Components\Config;

Config::set('global', [
    'wrapComponent' => ['wrap' => true, 'tag' => 'div'],
]);

Config::set('MyApp\\Components', [
    'wrapComponent' => ['tag' => 'section', 'class' => 'my-app'],
    'url'           => ['images' => 'public/assets/images'],
]);

Config::get('MyApp\\Components', 'url.images');
// → 'public/assets/images'
```

## Development

For contributor onboarding, local quality checks, and PR expectations, see [CONTRIBUTING.md](CONTRIBUTING.md).

Common local commands:

```bash
composer lint
composer stan
composer test
composer qa
```

## Testing

```bash
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
