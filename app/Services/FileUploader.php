<?php

namespace app\Services;

class FileUploader
{
    public function upload(?array $file): ?string
    {
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = uniqid('photo_', true) . '.' . $ext;


        $targetPath = __DIR__ . '/../../public/uploads/' . $fileName;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return '/uploads/' . $fileName;
        }

        return null;
    }
}