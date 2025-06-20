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
        $member['photo'] = '/uploads/default_photo.png';

        $columns = array_keys($member);

        $sql = sprintf('INSERT INTO members (%s) VALUES (%s)',
            implode(', ', $columns),
            ':' . implode(', :', $columns));

        $params = [];
        foreach ($member as $key => $value) {
            $params[":$key"] = $value;
        }

        $this->db->query($sql, $params);

        return $this->db->query("SELECT LAST_INSERT_ID()")->fetchColumn();
    }

    public function update(array $member, int $id): bool
    {
        $columns = array_keys($member);

        $sql = sprintf('UPDATE members SET %s WHERE id = :id',
            implode(', ', array_map(fn($col) => "$col = :$col", $columns)));

        $params = [];
        foreach($member as $key => $value){
            $params[":$key"] = $value;
        }

        $params[':id'] = $id;

        $this->db->query($sql, $params);

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

    public function getIdByEmail(string $email): ?int
    {
        $sql = "SELECT id FROM members WHERE email = ?";
        $result = $this->db->query($sql, [$email]);

        $id = $result->fetchColumn();

        return $id !== false ? (int)$id : null;
    }

    public function exists(int $id): bool
    {
        $sql = ("SELECT 1 FROM members WHERE id = ?");
        $result = $this->db->query($sql, [$id]);


        return (bool)$result->fetchColumn();
    }
}