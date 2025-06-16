<?php

namespace app\Controllers;

use app\Models\Member;
use app\Services\FileUploader;
use core\View;
use core\Validator;

class FormController
{
    private Member $member;
    private Validator $validator;

    public function __construct()
    {
        $this->member = new Member();
        $this->validator = new Validator();
    }


    public function wizardForm()
    {
        $config = require __DIR__ . '/../../config/config.php';

        $shareUrl = $config['app_url'] . $config['share']['path'];
        $tweetText = $config['share']['tweetText'];
        $facebookUrl = 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($shareUrl);
        $twitterUrl = 'https://twitter.com/intent/tweet?text=' . urlencode($tweetText) . '&url=' . urlencode($shareUrl);

        View::render('wizard_form', ['facebookUrl' => $facebookUrl, 'twitterUrl' => $twitterUrl]);
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

        $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;

        $errors = $this->validator->validateFirstStep($data, $id);

        if (!empty($errors)) {
            echo json_encode([
                'success' => false,
                'errors' => $errors
            ]);
            return;
        }

        if (!empty($_POST['id'])) {
            $id = (int)$_POST['id'];
            $this->member->updateFirstStep($data, $id);
        } else {
            $id = $this->member->create($data);
        }

        echo json_encode(['id' => $id]);
    }

    public function second_step()
    {
        $this->check_request_method('POST');

        $defaultPhotoPath = '/uploads/default_photo.png';

        $id = (int)$_POST['id'] ?? 0;

        if (!$this->member->exists($id)) {
            echo json_encode(['success' => false, 'error' => 'Invalid member ID']);
            return;
        }

        $data = [
            'company' => $_POST['company'] ?? '',
            'position' => $_POST['position'] ?? '',
            'about_me' => $_POST['about_me'] ?? '',
            'photo' => $_FILES['photo'] ?? null
        ];

        $errors = $this->validator->validateSecondStep($data);

        if (!empty($errors)) {
            echo json_encode([
                'success' => false,
                'errors' => $errors
            ]);
            return;
        }

        $fileUploader = new FileUploader();
        $data['photo'] = $fileUploader->upload($data['photo']) ?? $defaultPhotoPath;


        $this->member->updateSecondStep($data, $id);
        $count = $this->member->count();

        echo json_encode(['success' => true, 'count' => $count]);
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