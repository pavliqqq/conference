<?php

namespace core;

use app\Models\Member;
use DateTime;

class Validator
{
    private Member $member;

    private const COUNTRY_CODES_PATH = __DIR__ . '/../resources/data/iso2_codes.json';

    private const NAME_MIN = 2;
    private const NAME_MAX = 50;

    private const REPORT_SUBJECT_MIN = 2;
    private const REPORT_SUBJECT_MAX = 500;

    private const PHONE_MIN_LENGTH = 10;
    private const PHONE_MAX_LENGTH = 15;

    private const PHOTO_MAX_SIZE = 500 * 1024;
    private const PHOTO_ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif'];
    public function __construct()
    {
        $this->member = new Member();
    }

    public function validateFirstStep(array $data): array
    {
        return array_filter([
            'first_name' => $this->validateName($data['first_name'] ?? ''),
            'last_name' => $this->validateName($data['last_name'] ?? ''),
            'birthdate' => $this->validateDate($data['birthdate'] ?? ''),
            'report_subject' => $this->validateReportSubject($data['report_subject'] ?? ''),
            'country' => $this->validateCountry($data['country'] ?? ''),
            'phone' => $this->validatePhoneNumber($data['phone'] ?? ''),
            'email' => $this->validateEmail($data['email'] ?? ''),
        ]);
    }

    public function validateSecondStep(array $data): array
    {
        return array_filter([
            'company' => $this->validateCompany($data['company'] ?? ''),
            'position' => $this->validatePosition($data['position'] ?? ''),
            'about_me' => $this->validateAboutMe($data['about_me'] ?? ''),
            'photo' => $this->validatePhoto($data['photo'] ?? null),
        ]);
    }



    private function validateName(string $value): ?string
    {
        $value = trim($value);

        if ($value === '') {
            return 'Require field';
        }

        $length = mb_strlen($value);

        if ($length < self::NAME_MIN || $length > self::NAME_MAX) {
            return "The field must be between " . self::NAME_MIN . " and " . self::NAME_MAX . " characters";
        }

        if (!preg_match('/^[\p{L}\s\-]+$/u', $value)) {
            return 'Only letters, spaces, and hyphens are allowed';
        }

        return null;
    }

    private function validateDate(string $value): ?string
    {
        $value = trim($value);

        if ($value === '') {
            return 'Invalid date';
        }

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return 'Invalid date format';
        }

        $timestamp = strtotime($value);
        if (!$timestamp) {
            return 'Invalid date';
        }

        $birthdate = new DateTime($value);
        $today = new DateTime();

        if ($birthdate > $today) {
            return 'Birthdate cannot be in the future';
        }

        $age = $today->diff($birthdate)->y;

        if ($age < 10) {
            return 'Participant must be at least 10 years old';
        }

        if ($age > 120) {
            return 'Invalid age';
        }

        return null;
    }

    private function validateReportSubject(string $value): ?string
    {
        $value = trim($value);
        $length = mb_strlen($value);

        if ($value === '') {
            return 'Require field';
        }

        if ($length < self::REPORT_SUBJECT_MIN || $length > self::REPORT_SUBJECT_MAX) {
            return "The field must be between " . self::REPORT_SUBJECT_MIN . " and " . self::REPORT_SUBJECT_MAX . " characters";
        }

        return null;
    }

    private function validateCountry(string $value): ?string
    {
        $iso2CodesJson = file_get_contents(self::COUNTRY_CODES_PATH);
        $validCountryCodes = json_decode($iso2CodesJson, true);

        $value = trim($value);

        if ($value === '') {
            return 'Invalid country';
        }

        if (!in_array($value, $validCountryCodes)) {
            return 'Invalid country name';
        }

        return null;
    }

    private function validatePhoneNumber(string $value): ?string
    {
        $value = trim($value);

        if ($value === '') {
            return 'Require field';
        }

        if (!preg_match('/^\+[\d\s\-\(\)]+$/', $value)) {
            return 'Phone must be in international format and start with +';
        }

        $digitsOnly = preg_replace('/\D+/', '', $value);
        $length = strlen($digitsOnly);

        if ($length < self::PHONE_MIN_LENGTH || $length > self::PHONE_MAX_LENGTH) {
            return 'Phone number must be between 10 and 15 digits';
        }

        return null;
    }

    private function validateEmail(string $value): ?string
    {
        $value = trim($value);

        if ($value === '') {
            return 'Require field';
        }

        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return 'Invalid email format';
        }

        if (!$this->isEmailUnique($value)) {
            return 'This email is already registered';
        }
        return null;
    }

    private function isEmailUnique(string $email): bool
    {
        return $this->member->emailCheck($email) === 0;
    }


    private function validateCompany(string $value): ?string
    {
        $value = trim($value);

        if ($value === '') {
            return null;
        }

        if (mb_strlen($value) > 100) {
            return 'The field must be under 100 characters';
        }

        return null;
    }

    private function validatePosition(string $value): ?string
    {
        $value = trim($value);

        if ($value === '') {
            return null;
        }

        if (mb_strlen($value) > 100) {
            return 'The field must be under 100 characters';
        }

        return null;
    }

    private function validateAboutMe(string $value): ?string
    {
        $value = trim($value);

        if ($value === '') {
            return null;
        }

        if (mb_strlen($value) > 1000) {
            return 'The field must be under 1000 characters';
        }

        return null;
    }

    public function validatePhoto(?array $file): ?string
    {
        if (!$file || $file['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return 'Error uploading file';
        }

        if ($file['size'] > self::PHOTO_MAX_SIZE) {
            return 'File size must be less than 500 KB';
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, self::PHOTO_ALLOWED_EXTENSIONS)) {
            return 'Allowed file types: jpg, jpeg, png, gif';
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        $allowedMimeTypes = [
            'image/jpeg',
            'image/png',
            'image/gif',
        ];

        if (!in_array($mime, $allowedMimeTypes)) {
            return 'Invalid file type';
        }

        return null; // Ошибок нет
    }
}