<?php

namespace core\Requests;

class SecondStepRequest extends Request
{

    protected function fields(): array
    {
        return ['company', 'position', 'about_me', 'photo'];
    }

    public function __construct()
    {
        foreach ($this->fields() as $field) {
            if ($field === 'photo') {
                $this->data[$field] = $_FILES[$field] ?? null;
            } else {
                $this->data[$field] = $_POST[$field] ?? '';
            }
        }
    }
}