<?php

namespace core\Requests;

class FirstStepRequest extends Request
{

    protected function fields(): array
    {
        return ['first_name', 'last_name', 'birthdate', 'report_subject', 'country', 'phone', 'email'];
    }
}