<?php

namespace app\Models;

use database\Database;
use PDO;

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

    public function updateFirstStep(array $member, int $id): bool
    {
        $sql = "UPDATE members SET first_name = ?, last_name = ?, birthdate = ?, report_subject = ?, country = ?, phone = ?, email = ? WHERE id = ?";
        $this->db->query($sql, [$member['first_name'], $member['last_name'], $member['birthdate'], $member['report_subject'],
            $member['country'], $member['phone'], $member['email'], $id]);

        return true;
    }

    public function updateSecondStep(array $member, int $id): bool
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

    public function count(): int
    {
        $result = $this->db->query("SELECT COUNT(*) as count FROM members");
        $row = $result->fetch(PDO::FETCH_ASSOC);
        return (int)$row['count'];
    }

    public function emailCheck(string $email, ?int $excludeId = null): int
    {
        if ($excludeId !== null) {
            $sql = "SELECT COUNT(*) FROM members WHERE email = ? AND id != ?";
            $result = $this->db->query($sql, [$email, $excludeId]);
        } else {
            $sql = "SELECT COUNT(*) FROM members WHERE email = ?";
            $result = $this->db->query($sql, [$email]);
        }
        $count = $result->fetchColumn();

        return $count;
    }

    public function exists(int $id): bool
    {
        $sql = ("SELECT 1 FROM members WHERE id = ?");
        $result = $this->db->query($sql, [$id]);


        return (bool) $result->fetchColumn();
    }
}