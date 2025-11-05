<?php

require_once __DIR__ . '/../../database/Database.php';

class Anime {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function all() {
        $stmt = $this->db->query("SELECT * FROM anime ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM anime WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $stmt = $this->db->prepare(
            "INSERT INTO anime (title, genre, episodes, status, rating) VALUES (?, ?, ?, ?, ?)"
        );
        return $stmt->execute([
            $data['title'],
            $data['genre'],
            $data['episodes'],
            $data['status'],
            $data['rating']
        ]);
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare(
            "UPDATE anime SET title = ?, genre = ?, episodes = ?, status = ?, rating = ? WHERE id = ?"
        );
        return $stmt->execute([
            $data['title'],
            $data['genre'],
            $data['episodes'],
            $data['status'],
            $data['rating'],
            $id
        ]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM anime WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
