<?php
declare(strict_types = 1);
use Cake\Routing\Router;

Router::plugin('Monitor', function ($routes): void {
    $routes->connect('/', ['plugin' => 'Monitor', 'controller' => 'Check', 'action' => 'check']);
    $routes->fallbacks('DashedRoute');
});
