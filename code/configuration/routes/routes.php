<?php

//Get Request
$app->get('/orders[/{page}[/{limit}]]', 'ApiController:orders');

//Post Request
$app->post('/orders', 'ApiController:saveOrder');

//Patch Request
$app->patch('/orders/{id}', 'ApiController:updateOrder');
