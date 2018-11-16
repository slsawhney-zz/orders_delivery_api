<?php

/** @var \Container\ContainerInterface $container */
$container = $app->getContainer();

$container['ApiHelper'] = function ($container) {
    return new \App\Helper\ApiHelper(
        $container->get('logger')
    );
};
