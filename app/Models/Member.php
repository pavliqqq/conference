<?php

namespace app\Models;

use database\Database;

class Member
{
    protected $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function create(array $member): int
    {
        $sql = "INSERT INTO members (first_name, last_name, birthdate, report_subject, country, phone, email) VALUES (?,?,?,?,?,?,?)";
        $this->db->query($sql, [$member['first_name'], $member['last_name'], $member['birthdate'], $member['report_subject'],
            $member['country'], $member['phone'], $member['email']]);

        return $this->db->query("SELECT LAST_INSERT_ID()")->fetchColumn();
    }

    public function update(array $member, int $id): bool
    {
        $sql = "UPDATE members SET company = ?, position = ?, about_me = ?, photo = ? WHERE id = ?";
        $this->db->query($sql, [$member['company'], $member['position'], $member['about_me'], $member['photo'], $id]);

        return true;
    }

    public function all(): array
    {
        $result = $this->db->query("SELECT * FROM members");
        return $result->fetchAll();
    }
}