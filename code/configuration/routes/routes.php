<?php

//Get Request
$app->get('/orders[/{page}[/{limit}]]', 'ApiController:orders');

//Post Request
$app->post('/order', 'ApiController:saveOrder');

//Patch Request
$app->patch('/order/{id}', 'ApiController:updateOrder');
