<?php

require __DIR__ . '/../vendor/autoload.php';

$loader = new \Aura\Autoload\Loader();
$loader->addPrefix('fieldwork\tests', __DIR__ . '/fieldwork/tests');
$loader->addPrefix('fieldwork', __DIR__ . '/../src/fieldwork');
$loader->register();