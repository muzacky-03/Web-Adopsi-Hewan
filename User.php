<?php
require_once 'config.php';

class User {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function createUser($data) {
        $stmt = $this->pdo->prepare("INSERT INTO users (nama, email, password, no_telepon) VALUES (?, ?, ?, ?)");
        $stmt->execute([$data['nama'], $data['email'], $data['password'], $data['no_telepon']]);
        return $this->pdo->lastInsertId();
    }

    public function getUserByEmail($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getUserById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id_user = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateUser($id, $data) {
        $sql = "UPDATE users SET nama = ?, email = ?, no_telepon = ?, alamat = ?, bio = ?";
        $params = [$data['nama'], $data['email'], $data['no_telepon'], $data['alamat'], $data['bio']];
        
        if (isset($data['password'])) {
            $sql .= ", password = ?";
            $params[] = $data['password'];
        }
        
        $sql .= " WHERE id_user = ?";
        $params[] = $id;
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function updateProfilePicture($id, $filename) {
        $stmt = $this->pdo->prepare("UPDATE users SET foto_profil = ? WHERE id_user = ?");
        return $stmt->execute([$filename, $id]);
    }
}
?>