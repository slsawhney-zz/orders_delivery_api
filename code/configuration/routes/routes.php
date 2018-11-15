<?php

//Get Request
$app->get('/orders[/{page}[/{limit}]]', 'ApiController:orders');

//Post Request
$app->post('/order', 'ApiController:saveOrder');

//Put Request
$app->put('/order/{id}', 'ApiController:updateOrder');

