<?php

namespace app\Controllers;

use app\Models\Member;

class RegistrationController
{
    private Member $member;

    public function __construct()
    {
        $this->member = new Member();
    }

    public function first_step()
    {
        $this->check_request_method();

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
        $this->check_request_method();

        $id = (int)$_POST['id'] ?? '';

        $data = [
            'company' => $_POST['company'] ?? '',
            'position' => $_POST['position'] ?? '',
            'about_me' => $_POST['about_me'] ?? '',
            'photo' => $_POST['photo'] ?? ''
        ];

        //Добавить валидацию

        $this->member->update($data, $id);

        echo json_encode(['success' => true]);
    }


    public function check_request_method()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method is not allowed']);
            exit;
        }
    }
}