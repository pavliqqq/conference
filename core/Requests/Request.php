<?php

namespace core\Requests;

abstract class Request
{
    protected array $data;

    abstract protected function fields(): array;

    public function __construct()
    {
        foreach ($this->fields() as $field) {
            $this->data[$field] = $_POST[$field] ?? '';
        }
    }

    public function all(): array
    {
        return $this->data;
    }
}