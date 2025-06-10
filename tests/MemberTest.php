<?php

use app\Models\Member;

require_once __DIR__ . '/../database/Database.php';
require_once __DIR__ . '/../app/models/Member.php';

echo "Начало теста\n";

$member = new Member();

echo "Добавление";

$newMember = $member->create([
    'first_name' => 'Павел',
    'last_name' => 'Тютюнник',
    'birthdate' => '2002-03-01',
    'report_subject' => 'Тест',
    'country' => 'Украина',
    'phone' => '0636566259',
    'email' => 'paveltutunnik0103@gmail.com'
]);
if ($newMember) {
    echo "Успешно добавлен";
} else {
    echo "Не удалось добавить";
    exit;
}

echo "Обновление";
$editMember = $member->update([
    'company' => 'albedo',
    'position' => 'trainee',
    'about_me' => 'trainee developer',
    'photo' => 'test.jpg',
], $newMember);
if ($editMember) {
    echo "Успешно обновлен";
} else {
    echo "Не удалось обновить";
    exit;
}

echo "Все участники:\n";
$all = $member->all();
print_r($all);

echo "Тест завершен";