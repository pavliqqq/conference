<?php

use app\Controllers\RegistrationController;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/app/Controllers/RegistrationController.php';

$controller = new RegistrationController();

$requestUri = ltrim($_SERVER['REQUEST_URI'], '/');

switch ($requestUri) {
    case 'register/first':
        $controller->first_step();
        break;

    case 'register/second':
        $controller->second_step();
        break;

    default:
        http_response_code(404);
        echo json_encode(['error' => 'Route not found']);
        break;
}