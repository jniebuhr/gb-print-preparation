<?php

$tryFiles = array(
    __DIR__.'../vendor/autoload.php',
    __DIR__.'../autoload.php',
    './vendor/autoload.php',
);

foreach ($tryFiles as $file) {
    if (file_exists($file)) {
        require_once($file);
        break;
    }
}

$app = new \Goldbek\Cards\PrintPreparation\Application();
$app->run($argv);