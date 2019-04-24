<?php

use ShyimSass\Compiler;

require dirname(__DIR__) . '/vendor/autoload.php';

$compiler = new Compiler();
$compiler->setOptions([
    'output_style' => Compiler::STYLE_EXPANDED
]);

echo $compiler->compile(__DIR__ . '/test.scss');