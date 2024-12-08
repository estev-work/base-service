#!/usr/bin/env php
<?php

declare(strict_types=1);

use Core\Config\Generator\YamlConfigGenerator;

require __DIR__ . '/../vendor/autoload.php';

$generator = new YamlConfigGenerator(
    __DIR__ . '/../config/yaml',
    __DIR__ . '/../config/generated',
    'Config'
);
$generator->generate();

echo "Config classes generated.\n";