<?php

namespace app\Controllers;

use app\Models\Member;
use core\View;

class FormController
{
    private Member $member;

    public function __construct()
    {
        $this->member = new Member();
    }


    public function wizardForm()
    {
        View::render('wizard_form');
    }

    public function getAllMembers()
    {
        $this->check_request_method('GET');

        $members = $this->member->all();

        $result = [];

        foreach ($members as $member) {
            $result[] = [
                'photo' => $member['photo'],
                'full_name' => $member['first_name'] . ' ' . $member['last_name'],
                'report_subject' => $member['report_subject'],
                'email' => $member['email']
            ];
        }

        View::render('all_members', ['members' => $result]);
    }

    public function first_step()
    {
        $this->check_request_method('POST');

        $data = [
            'first_name' => $_POST['first_name'] ?? '',
            'last_name' => $_POST['last_name'] ?? '',
            'birthdate' => $_POST['birthdate'] ?? '',
            'report_subject' => $_POST['report_subject'] ?? '',
            'country' => $_POST['country'] ?? '',
            'phone' => $_POST['phone'] ?? '',
            'email' => $_POST['email'] ?? '',
        ];

        //Добавить валидацию

        $id = $this->member->create($data);

        echo json_encode(['id' => $id]);
    }

    public function second_step()
    {
        $this->check_request_method('POST');

        $id = (int)$_POST['id'] ?? '';

        $data = [
            'company' => $_POST['company'] ?? '',
            'position' => $_POST['position'] ?? '',
            'about_me' => $_POST['about_me'] ?? '',
            'photo' => ''
        ];

        //отрефакторить код файла: добавить проверку на расширение, на размер
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {

            $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
            $fileName = uniqid('photo_', true) . '.' . $ext;

            $targetPath = __DIR__ . '/../../public/uploads/' . $fileName;

            if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetPath)) {
                $data['photo'] = '/uploads/' . $fileName;
            }
        }
        //Добавить валидацию

        $this->member->update($data, $id);
        $count = $this->member->count();

        echo json_encode(['count' => $count]);
    }

    public function check_request_method(string $method)
    {
        if ($_SERVER['REQUEST_METHOD'] !== $method) {
            http_response_code(405);
            echo json_encode(['error' => 'Method is not allowed']);
            exit;
        }
    }
}