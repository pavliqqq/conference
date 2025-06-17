<?php

use app\Controllers\FormController;
use core\Router;

$router = new Router();

$router->add('GET','/all_members', FormController::class, 'getAllMembers');
$router->add('GET','/wizard_form', FormController::class, 'wizardForm');


$router->add('POST','register/first', FormController::class, 'first_step');
$router->add('POST','register/second', FormController::class, 'second_step');

return $router;