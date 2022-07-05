<?php
/**
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;

return static function (RouteBuilder $routes) {
    $routes->setRouteClass(DashedRoute::class);

    $routes->scope('/', function (RouteBuilder $builder) {
        $builder->connect('/', ['controller' => 'Pages', 'action' => 'display', 'home']);
        $builder->connect('/pages/*', ['controller' => 'Pages', 'action' => 'display']);
    
        // Add this
        // New route we're adding for our tagged action.
        // The trailing `*` tells CakePHP that this action has
        // passed parameters.
        $builder->scope('/articles', function (RouteBuilder $builder) {
            $builder->connect('/tagged/*', ['controller' => 'Articles', 'action' => 'tags']);
        });
    
        $builder->fallbacks();
    });
};
