<?php

/** @var \Container\ContainerInterface $container */
$container = $app->getContainer();

// Service factory for the ORM

//boot eloquent connection

$capsule = new Illuminate\Database\Capsule\Manager();

$capsule->addConnection($container['settings']['db']);

$capsule->setAsGlobal();

$capsule->bootEloquent();

$container['db'] = function ($container) use ($capsule) {
    return $capsule;
};

