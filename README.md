# wwaz/components

Ein PHP-Bibliothek für das kompositionelle Erstellen und Rendern von HTML/SVG-Fragmenten und wiederverwendbaren UI-Komponenten. Die Library folgt einem deklarativen Ansatz: Komponenten werden per Daten-Array beschrieben, validiert und in HTML-Markup überführt.

---

## Inhaltsverzeichnis

1. [Installation](#installation)
2. [Grundkonzepte](#grundkonzepte)
3. [Schnellstart](#schnellstart)
4. [API-Referenz](#api-referenz)
   - [Factory](#factory)
   - [FragmentFactory](#fragmentfactory)
   - [Config](#config)
   - [BaseComponent / Component / Fragment](#basecomponent--component--fragment)
   - [Batch](#batch)
5. [Eingebaute Fragmente](#eingebaute-fragmente)
6. [Eingebaute Fragmente – Verwendungsbeispiele](#eingebaute-fragmente--verwendungsbeispiele)
7. [Eigene Komponenten erstellen](#eigene-komponenten-erstellen)
8. [Konfiguration](#konfiguration)
9. [Validierung](#validierung)
10. [Fortgeschrittene Beispiele](#fortgeschrittene-beispiele)
11. [Projektstruktur](#projektstruktur)

---

## Installation

```bash
composer require wwaz/components
```

Die Library registriert sich beim Laden automatisch über `bootstrap/bootstrap.php` (Autoload-files in `composer.json`). Die globale Konfiguration aus `config/config.php` wird dabei ebenfalls eingelesen.

---

## Grundkonzepte

| Begriff | Bedeutung |
|---|---|
| **Fragment** | Atomares HTML/SVG-Element (z. B. `<a>`, `<div>`, `<circle>`). Erbt von `BaseComponent`. |
| **Component** | Zusammengesetzte UI-Komponente (z. B. Card, BannerHero). Erbt von `Component`. Wird automatisch in einen Wrapper-Tag eingebettet. |
| **Factory** | Erstellt Komponenten/Fragmente per Typ-String und Daten-Array. |
| **Config** | Globale und namespace-spezifische Konfiguration (z. B. Wrapper-Tag, CSS-Klassen). |
| **Batch** | Konvertiert verschachtelte Daten-Arrays oder JSON in fertig gerenderte Komponenten. |

---

## Schnellstart

```php
<?php
require 'vendor/autoload.php';

use wwaz\Components\Factory;

// Einfacher Link
$link = Factory::make('fragment.html.a', [
    'href'    => 'https://example.com',
    'target'  => '_blank',
    'content' => 'Besuche Example.com'
]);

echo $link->render();
// <a href="https://example.com" target="_blank">Besuche Example.com</a>
```

---

## API-Referenz

### Factory

`wwaz\Components\Factory`

Die zentrale Einstiegsklasse. Alle Methoden sind statisch.

#### `Factory::make(string $type, array $data): object`

Erstellt eine Komponenten-Instanz anhand eines Typ-Strings und eines Daten-Arrays.

**Typ-String-Auflösung:**  
Der Typ-String wird in einen PHP-Klassennamen umgewandelt. Dabei werden folgende Varianten in den registrierten Namespaces gesucht:

| Typ-String | Gesuchte Klassen (Beispiel) |
|---|---|
| `'fragment.html.a'` | `wwaz\Components\Fragment\Html\A` |
| `'callaction'` | `wwaz\Components\Callaction`, `wwaz\Components\Callaction\Callaction`, … |
| `'my_component'` | wird zu `MyComponent` (camelCase) konvertiert |
| `'wwaz.Components.Foo.Bar'` | direkt: `wwaz\Components\Foo\Bar` |

```php
// Kurzform (Namespace wird automatisch aufgelöst)
$div = Factory::make('fragment.html.div', [
    'id'      => 'main',
    'class'   => 'container',
    'content' => 'Hallo Welt!'
]);

// Vollständiger Klassenname mit Punktnotation
$component = Factory::make('wwaz.Components.Componenttest.Callaction', [
    'text' => 'Jetzt klicken!'
]);
```

#### `Factory::addNamespace(string $namespace): void`

Registriert einen zusätzlichen Namespace, in dem die Factory nach Komponenten sucht. Eigene Namespaces werden **vor** dem Standard-Namespace eingetragen und haben damit Vorrang.

```php
Factory::addNamespace('MyApp\\Components');

// Sucht jetzt zuerst in MyApp\Components\Card, dann in wwaz\Components\Card
$card = Factory::make('card', $data);
```

#### `Factory::exists(string $type): string|false`

Prüft, ob eine Komponenten-Klasse für den gegebenen Typ-String existiert. Gibt den vollständigen Klassennamen zurück oder `false`.

```php
if (Factory::exists('fragment.html.span')) {
    echo 'Span-Fragment ist verfügbar.';
}
```

#### `Factory::setThrowErrors(bool $bool): void`

Legt fest, ob bei einem nicht auffindbaren Typ eine Exception geworfen wird.

```php
Factory::setThrowErrors(true);
// Wirft \Exception, wenn Klasse nicht gefunden wird
```

---

### FragmentFactory

`wwaz\Components\FragmentFactory`

Spezialisierte Factory ausschließlich für Fragmente. Der Typ-String **muss** einen Punkt enthalten (z. B. `html.a`).

```php
use wwaz\Components\FragmentFactory;

$span = FragmentFactory::make('html.span', [
    'content' => 'Hallo!'
]);

echo $span->render();
// <span>Hallo!</span>
```

#### `FragmentFactory::addNamespace(string $namespace): void`

Fügt einen weiteren Namespace für eigene Fragment-Klassen hinzu.

```php
FragmentFactory::addNamespace('MyApp\\Fragment');
```

---

### Config

`wwaz\Components\Config`

Verwaltet globale und namespace-spezifische Konfigurationseinstellungen.

#### `Config::set(string $namespace, array $config): void`

Setzt Konfigurationsdaten für einen Namespace. Der spezielle Namespace `'global'` wird automatisch in alle anderen Namespaces vererbt.

```php
use wwaz\Components\Config;

// Globale Einstellungen (werden in alle anderen Namespaces vererbt)
Config::set('global', [
    'wrapComponent' => [
        'wrap'  => true,
        'tag'   => 'section',
        'class' => null
    ]
]);

// Namespace-spezifische Einstellungen
Config::set('MyApp\\Components', [
    'wrapComponent' => [
        'class' => 'my-app'
    ],
    'url' => [
        'images' => 'public/assets/images'
    ]
]);
```

#### `Config::get(string $namespace, string $key = null): mixed`

Liest Konfigurationswerte aus, optional mit Punkt-separiertem Schlüssel.

```php
$tag  = Config::get('global', 'wrapComponent.tag');  // 'section'
$wrap = Config::get('global', 'wrapComponent');       // ['wrap' => true, 'tag' => 'section', ...]
$all  = Config::get('global');                        // gesamtes Config-Array
```

#### `Config::matchNamespace(string $namespace): string`

Gibt den am besten passenden registrierten Namespace zurück. Nützlich, wenn mit Vererbungshierarchien von Namespaces gearbeitet wird.

```php
// Registriert: 'global', 'MyApp\\Components'
// Abfrage:     'MyApp\\Components\\Card'
// Ergebnis:    'MyApp\\Components'
$ns = Config::matchNamespace('MyApp\\Components\\Card');
```

---

### BaseComponent / Component / Fragment

Alle Komponenten erben letztlich von `BaseComponent`. Diese stellt die gemeinsame API bereit:

#### Instanz-Methoden (verfügbar auf allen Komponenten/Fragmenten)

```php
$el = Factory::make('fragment.html.div', ['content' => 'Inhalt']);

// --- Rendern ---
$el->render();          // Gibt fertiges HTML zurück (bei Fragmenten eingerückt via Dindent)

// --- Daten ---
$el->toData();          // Gibt interne Datenstruktur als Array zurück

// --- Attribute ---
$el->setAttribute('data-foo', 'bar');  // Setzt/überschreibt ein Attribut
$el->getAttribute('data-foo');         // Liest ein Attribut aus → 'bar'
$el->getAttributes();                  // Alle Attribute als Array

// --- Klassen ---
$el->addClass('highlight');            // Fügt CSS-Klasse hinzu (Rückgabe: self)
$el->prependClass('first');            // Fügt Klasse vorne ein (Rückgabe: self)
$el->hasClass('highlight');            // true/false
$el->classList();                      // Klassenliste als Array

// --- Inhalt ---
$el->addContent('Weiterer Text');      // Hängt Inhalt an (Rückgabe: self)
$el->addContent($andereKomponente);    // Verschachtelte Komponente hinzufügen
$el->getContent();                     // Gibt aktuellen Content zurück
```

#### Zusätzlich auf `Component`-Instanzen (zusammengesetzte Komponenten)

```php
// render() – mit optionalem Wrapper
$component->render();        // Mit Wrapper-Div (Standard)
$component->render(false);   // Ohne Wrapper-Div, nur inneres Markup

// Wrapper-Tag überschreiben
$component->setWrapTag('article');

// Wrapper-Klassen
$component->addComponentClass('dark-mode');   // Klasse zum Wrapper hinzufügen
$component->getComponentClasses();            // Liste zusätzlicher Klassen
$component->hasComponentClass('dark-mode');   // true/false

// Kurzformen für Zugriff auf Content-Objekte
$component->cr('title');  // getContentMarkupByKey('title') – gibt gerendertes HTML zurück
$component->co('title');  // getContentByKey('title') – gibt Objekt/Wert zurück

// Datenzugriff
$component->getData();           // Gibt $data['data'] zurück
$component->getData('myKey');    // Gibt $data['data']['myKey'] zurück
```

---

### Batch

`wwaz\Components\Batch`

Nimmt ein verschachteltes Array (oder JSON-String) und erstellt daraus rekursiv einen vollständigen Komponenten-Baum, der dann als HTML ausgegeben werden kann.

```php
use wwaz\Components\Batch;

$batch = new Batch([
    'type'    => 'fragment.html.div',
    'content' => [
        [
            'type'    => 'fragment.html.a',
            'href'    => 'https://example.com',
            'content' => 'Klick mich!'
        ],
        [
            'type'    => 'fragment.html.span',
            'content' => 'Oder mich!'
        ]
    ]
]);

echo $batch->toHtml();

// Alternativ als JSON-String:
$json = json_encode([...]);
$batch = new Batch($json);
echo $batch->toHtml();

// Als Daten-Array zurückgeben:
$data = $batch->toData();
```

---

## Eingebaute Fragmente

### HTML-Fragmente

Alle unter dem Typ-Präfix `fragment.html.*` erreichbar:

| Typ-String | Klasse | Besonderheiten |
|---|---|---|
| `fragment.html.a` | `Fragment\Html\A` | `href` required; `target`: null, `_blank`, `_parent`, `_self`, `_top` |
| `fragment.html.div` | `Fragment\Html\Div` | Container |
| `fragment.html.span` | `Fragment\Html\Span` | Container |
| `fragment.html.p` | `Fragment\Html\P` | Container |
| `fragment.html.button` | `Fragment\Html\Button` | Container |
| `fragment.html.h1` | `Fragment\Html\H1` | Container |
| `fragment.html.header` | `Fragment\Html\Header` | Container |
| `fragment.html.footer` | `Fragment\Html\Footer` | Container |
| `fragment.html.img` | `Fragment\Html\Img` | Selbstschließend |
| `fragment.html.table` | `Fragment\Html\Table` | Container |
| `fragment.html.th` | `Fragment\Html\Th` | Container |
| `fragment.html.tr` | `Fragment\Html\Tr` | Container |
| `fragment.html.td` | `Fragment\Html\Td` | Container |

Alle HTML-Fragmente unterstützen die Attribute `id` und `class` per Default (definiert in `HtmlTag`).

### SVG-Fragmente

Typ-Präfix `fragment.svg.*`:

| Typ-String | Klasse | Attribute |
|---|---|---|
| `fragment.svg.circle` | `Fragment\Svg\Circle` | `cx`, `cy`, `r` |
| `fragment.svg.path` | `Fragment\Svg\Path` | |
| `fragment.svg.a` | `Fragment\Svg\A` | |

---

## Eingebaute Fragmente – Verwendungsbeispiele

### `<a>`-Tag

```php
// Explizite Attribut-Schreibweise
$link = Factory::make('fragment.html.a', [
    'attributes' => [
        'id'     => 'my-link',
        'class'  => 'btn btn-primary',
        'href'   => 'https://example.com',
        'target' => '_blank',
        'title'  => 'Seite besuchen',
    ],
    'content' => 'Jetzt besuchen'
]);

// Kurzschreibweise (Attribute als Root-Keys – funktioniert ebenfalls)
$link = Factory::make('fragment.html.a', [
    'id'      => 'my-link',
    'class'   => 'btn btn-primary',
    'href'    => 'https://example.com',
    'target'  => '_blank',
    'title'   => 'Seite besuchen',
    'content' => 'Jetzt besuchen'
]);

echo $link->render();
// <a id="my-link" class="btn btn-primary" href="https://example.com" target="_blank" title="Seite besuchen">Jetzt besuchen</a>
```

### Verschachtelung von Fragmenten

```php
$link = Factory::make('fragment.html.a', [
    'href'    => 'https://example.com',
    'content' => Factory::make('fragment.html.span', [
        'class'    => 'label',
        'data-say' => 'hello',
        'content'  => 'Klick mich!'
    ])
]);

echo $link->render();
// <a href="https://example.com"><span class="label" data-say="hello">Klick mich!</span></a>
```

### `<div>`

```php
$div = Factory::make('fragment.html.div', [
    'id'      => 'wrapper',
    'class'   => 'container',
    'content' => 'Beliebiger Inhalt'
]);

echo $div->render();
// <div id="wrapper" class="container">Beliebiger Inhalt</div>
```

### SVG-Circle

```php
$circle = Factory::make('fragment.svg.circle', [
    'cx' => 50,
    'cy' => 50,
    'r'  => 40,
]);

echo $circle->render();
// <circle cx="50" cy="50" r="40" />
```

---

## Eigene Komponenten erstellen

Eigene Komponenten erweitern die abstrakte Klasse `Component` und implementieren die `markup()`-Methode.

### 1. Klasse erstellen

```php
<?php
namespace MyApp\Components\Card;

use wwaz\Components\Component;

class Card extends Component
{
    // Erlaubte Eingabe-Properties + Validierungsregeln
    protected $properties = [
        'content' => [
            'title'   => 'required|isString',
            'subline' => 'isString',
            'text'    => 'required|isString',
        ],
    ];

    protected function markup(): string
    {
        return '
            <div class="card__inner">
                <h2 class="card__title">' . $this->cr('title') . '</h2>
                <p class="card__subline">' . $this->cr('subline') . '</p>
                <p class="card__text">' . $this->cr('text') . '</p>
            </div>
        ';
    }
}
```

### 2. Namespace registrieren

```php
use wwaz\Components\Factory;

Factory::addNamespace('MyApp\\Components');
```

### 3. Instanziieren und rendern

```php
$card = Factory::make('card', [
    'content' => [
        'title'   => 'Mein Titel',
        'subline' => 'Ein Untertitel',
        'text'    => 'Der eigentliche Inhalt der Karte.'
    ]
]);

echo $card->render();
// <div class="myapp components card">
//   <div class="card__inner">
//     <h2 class="card__title">Mein Titel</h2>
//     <p class="card__subline">Ein Untertitel</p>
//     <p class="card__text">Der eigentliche Inhalt der Karte.</p>
//   </div>
// </div>
```

### Komponenten mit verschachtelten Komponenten als Properties

```php
$card = Factory::make('myapp.card', [
    'icon' => Factory::make('myapp.icon', [
        'url'  => '/assets/icons/star.svg',
        'size' => 60
    ]),
    'title'   => 'Mein Titel',
    'subline' => 'Ein Untertitel',
    'text'    => 'Der Inhalt der Karte.'
]);

echo $card->render();
```

---

## Konfiguration

### Globale Konfiguration (`config/config.php`)

```php
return [
    'wrapComponent' => [
        'wrap'  => true,   // Komponenten in Wrapper einbetten?
        'tag'   => 'div',  // Wrapper-Tag
        'class' => null    // Wrapper-CSS-Klasse (null = auto aus Namespace)
    ],
    'docking' => [
        'wrap'  => false,  // Docking-Point-Wrapper aktiv?
        'tag'   => 'div',
        'class' => 'cdp'   // data-Attribut-Name des Docking-Points
    ]
];
```

### Namespace-spezifische Konfiguration

```php
use wwaz\Components\Config;

Config::set('MyApp\\Components', [
    'wrapComponent' => [
        'wrap'  => true,
        'tag'   => 'article',
        'class' => 'my-component'
    ],
    'url' => [
        'images' => 'public/assets/images'
    ]
]);

// Wert auslesen
$imageBase = Config::get('MyApp\\Components', 'url.images');
// → 'public/assets/images'
```

Der Namespace `'global'` vererbt seine Einstellungen automatisch in alle anderen Namespaces. Namespace-spezifische Werte überschreiben dabei die globalen.

---

## Validierung

Properties werden per Validierungsstring definiert. Mehrere Regeln werden mit `|` verkettet.

| Regel | Bedeutung |
|---|---|
| `required` | Pflichtfeld |
| `isString` | Muss ein String sein |
| `length:1,255` | Mindest- und Maximallänge |
| `default:value` | Standardwert, wenn kein Wert übergeben |
| `select:null,_blank,_self` | Erlaubte Werte (Enum) |
| `*` | Beliebiger Wert erlaubt |

```php
protected $properties = [
    'content' => [
        'title'  => 'required|isString|length:1,100',
        'teaser' => 'isString|default:Kein Teaser',
        'status' => 'select:draft,published,archived|default:draft',
    ],
    'attributes' => [
        'id'    => 'isString',
        'class' => 'isString',
    ]
];
```

Bei Validierungsfehlern gibt die Library eine Fehlerseite aus (HTML-Fehlermeldung im Browser), bevor die Ausführung mit `die()` gestoppt wird.

---

## Fortgeschrittene Beispiele

### Dynamische CSS-Klassen zur Laufzeit

```php
$component = Factory::make('card', $data);
$component->addComponentClass('dark-mode');
$component->addComponentClass('featured');

echo $component->render();
// <div class="myapp components card dark-mode featured">...</div>
```

### Wrapper-Tag überschreiben

```php
$component = Factory::make('card', $data);
$component->setWrapTag('article');

echo $component->render();
// <article class="myapp components card">...</article>
```

### Ohne Wrapper rendern

```php
$component = Factory::make('card', $data);
echo $component->render(false);
// Gibt nur das innere Markup ohne Wrapper-Tag zurück
```

### Attribute zur Laufzeit setzen

```php
$link = Factory::make('fragment.html.a', ['href' => '#']);
$link->setAttribute('data-tracking', 'header-nav');
$link->setAttribute('aria-label', 'Startseite');
$link->addClass('is-active');

echo $link->render();
// <a href="#" data-tracking="header-nav" aria-label="Startseite" class="is-active"></a>
```

### Komponenten-Baum aus JSON (Batch)

```php
use wwaz\Components\Batch;

$json = '{
    "type": "fragment.html.div",
    "id": "wrapper",
    "content": [
        {
            "type": "fragment.html.h1",
            "content": "Willkommen!"
        },
        {
            "type": "fragment.html.a",
            "href": "https://example.com",
            "content": "Mehr erfahren"
        }
    ]
}';

$batch = new Batch($json);
echo $batch->toHtml();
```

### Komplexe Komponente mit Config und verschachtelten Unter-Komponenten

```php
use wwaz\Components\Factory;
use wwaz\Components\Config;

Config::set('MyApp\\Components', [
    'wrapComponent' => ['class' => 'myapp'],
    'url'           => ['images' => 'public/assets/images']
]);

Factory::addNamespace('MyApp\\Components');

$banner = Factory::make('bannerHero', [
    'headline' => 'Willkommen bei MyApp',
    'subline'  => 'Ihre Plattform für moderne Webanwendungen.',
    'callaction' => Factory::make('callaction', [
        'href' => '#features',
        'text' => 'Funktionen entdecken'
    ]),
    'image' => Factory::make('fragment.html.img', [
        'src' => '/assets/hero.jpg',
        'alt' => 'Hero-Bild',
    ])
]);

echo $banner->render();
```

---

## Projektstruktur

```
src/
├── BaseComponent.php              Abstrakte Basisklasse (Attribute, Content, Render)
├── Component.php                  Abstrakte Klasse für zusammengesetzte Komponenten
├── Fragment.php                   Abstrakte Klasse für atomare Fragmente
├── Factory.php                    Haupt-Factory
├── FragmentFactory.php            Fragment-spezifische Factory
├── Config.php                     Konfigurationsverwaltung
├── Batch.php                      Batch-Rendering aus Arrays/JSON
├── Fragment/
│   ├── Html/                      HTML-Elemente (A, Div, Span, P, Button, …)
│   ├── Svg/                       SVG-Elemente (Circle, Path, A)
│   └── Xml/                       XML-Basis (XmlTag)
├── Validate/
│   └── DataValidator.php          Validierungslogik
├── Helper/
│   ├── Arrays/                    Merge, Flatten
│   └── Strings/                   Json, StringConverter
└── Factory/
    └── RecursiveComponentBuilder.php
```

---

## Abhängigkeiten

| Paket | Version | Zweck |
|---|---|---|
| `gajus/dindent` | `^2.0` | Automatische HTML-Einrückung |
| `respect/validation` | `^2.0.17` | Datenvalidierung der Properties |

---

## Autor

**Alexander Zwierzynski** · [az@studio8o8.com](mailto:az@studio8o8.com)
