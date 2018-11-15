<?php

/** @var \Container\ContainerInterface $container */
$container = $app->getContainer();

$container['ApiController'] = function ($container) {
    return new \App\Controller\ApiController(
        $container->get('logger')
    );
};

