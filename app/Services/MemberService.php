<?php

namespace app\Services;

use app\Models\Member;

class MemberService
{
    private Member $member;

    public function __construct()
    {
        $this->member = new Member();
    }
    public function isEmailUnique(string $email, ?int $excludeId = null): bool
    {
        return $this->member->emailCheck($email, $excludeId) === 0;
    }
}