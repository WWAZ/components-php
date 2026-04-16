<?php

$reflection = new \ReflectionClass(\Composer\Autoload\ClassLoader::class);
$rootDir = dirname($reflection->getFileName(), 3);

require_once $rootDir . '/vendor/autoload.php';

$config = require dirname(__FILE__, 2) . '/config/config.php';

\wwaz\Components\Config::set('global', $config);

$overrideConfigPath = null;
$configCandidates = array_filter([
    defined('WWAZ_COMPONENTS_CONFIG') ? WWAZ_COMPONENTS_CONFIG : null,
    getenv('WWAZ_COMPONENTS_CONFIG') ?: null,
    $rootDir . '/config/components.php',
    $rootDir . '/config/wwaz-components.php',
]);

foreach ($configCandidates as $configCandidate) {
    if (is_file($configCandidate)) {
        $overrideConfigPath = $configCandidate;
        break;
    }
}

if ($overrideConfigPath !== null) {
    $overrideConfig = require $overrideConfigPath;

    if (is_array($overrideConfig)) {
        \wwaz\Components\Config::set('global', $overrideConfig);
    }
}
