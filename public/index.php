<?php


use app\Controllers\FormController;
use core\Router;

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

if ($_SERVER['REQUEST_URI'] === '/' || $_SERVER['REQUEST_URI'] === '') {
    header("Location: /wizard_form");
    exit;
}

$router = new Router();

$router->add('GET','/all_members', FormController::class, 'getAllMembers');
$router->add('GET','/wizard_form', FormController::class, 'wizardForm');


$router->add('POST','register/first', FormController::class, 'first_step');
$router->add('POST','register/second', FormController::class, 'second_step');

$router->dispatch($_SERVER['REQUEST_URI']);