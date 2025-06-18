<?php

namespace app\Controllers;

use app\Models\Member;
use core\Requests\FirstStepRequest;
use core\Requests\SecondStepRequest;
use core\View;
use services\FileUploader;
use services\Validator;

class FormController
{
    private $member;
    private $validator;

    private function getMember()
    {
        if (!$this->member) {
            $this->member = new Member();
        }
        return $this->member;
    }

    private function getValidator()
    {
        if (!$this->validator) {
            $this->validator = new Validator();
        }
        return $this->validator;
    }

    public function wizardForm()
    {
        $config = require __DIR__ . '/../../config.php';

        $shareUrl = $config['app_url'] . $config['share']['path'];
        $tweetText = $config['share']['tweetText'];
        $facebookUrl = 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($shareUrl);
        $twitterUrl = 'https://twitter.com/intent/tweet?text=' . urlencode($tweetText) . '&url=' . urlencode($shareUrl);

        View::render('wizard_form', ['facebookUrl' => $facebookUrl, 'twitterUrl' => $twitterUrl]);
    }

    public function getAllMembers()
    {
        $this->check_request_method('GET');

        $member = $this->getMember();
        $members = $member->all();

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

        $validator = $this->getValidator();
        $member = $this->getMember();
        $request = new FirstStepRequest();

        $data = $request->all();

        $errors = $validator->validateFirstStep($data);

        if ($this->handleValidationErrors($errors)) return;

        $existingId = $member->getIdByEmail($data['email']);

        if ($existingId) {
            $member->update($data, $existingId);
            $id = $existingId;
        } else {
            $id = $member->create($data);
        }

        echo json_encode(['id' => $id]);
    }

    public function second_step()
    {
        $this->check_request_method('POST');

        $validator = $this->getValidator();
        $member = $this->getMember();
        $request = new SecondStepRequest();

        $data = $request->all();

        $defaultPhotoPath = '/uploads/default_photo.png';

        $id = (int)$_POST['id'] ?? 0;

        if (!$member->exists($id)) {
            echo json_encode(['success' => false, 'error' => 'Invalid member ID']);
            return;
        }

        $errors = $validator->validateSecondStep($data);

        if ($this->handleValidationErrors($errors)) return;

        $fileUploader = new FileUploader();
        $data['photo'] = $fileUploader->upload($data['photo']) ?? $defaultPhotoPath;


        $member->update($data, $id);
        $count = $member->count();

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

    private function handleValidationErrors(array $errors): bool
    {
        if (!empty($errors)) {
            echo json_encode([
                'success' => false,
                'errors' => $errors
            ]);
            return true;
        }
        return false;
    }
}