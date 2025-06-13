<?php
return [
    'db' => [
        'host' => $_ENV['DB_HOST'],
        'dbname' => $_ENV['DB_NAME'],
        'user' => $_ENV['DB_USER'],
        'password' => $_ENV['DB_PASS'],
    ],
    'app_url' => $_ENV['APP_URL'],
    'share' => [
        'path' => '/wizard_form',
        'tweetText' => 'Check out this Meetup with SoCal AngularJS!',
    ],
];