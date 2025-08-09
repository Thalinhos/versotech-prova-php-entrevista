<?php
require_once "connection.php";

function getAllUsers(): array {
    $pdo = Database::getConnection();
    $stmt = $pdo->query("SELECT * FROM users ORDER BY id DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getUserById(int $id): ?array {
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    return $user ?: null;
}

function createUser(string $name, string $email, array $color_ids): int {
    $pdo = Database::getConnection();
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
    $stmt->execute([$name, $email]);
    $user_id = (int)$pdo->lastInsertId();

    updateUserColors($user_id, $color_ids);

    $pdo->commit();
    return $user_id;
}

function updateUser(int $id, string $name, string $email, array $color_ids): bool {
    $pdo = Database::getConnection();
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
    $stmt->execute([$name, $email, $id]);

    updateUserColors($id, $color_ids);

    $pdo->commit();
    return true;
}

function deleteUser(int $id): bool {
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    return $stmt->execute([$id]);
}

function updateUserColors(int $user_id, array $color_ids): void {
    $pdo = Database::getConnection();

    $stmt = $pdo->prepare("DELETE FROM user_colors WHERE user_id = ?");
    $stmt->execute([$user_id]);

    if (count($color_ids) > 0) {
        $stmtInsert = $pdo->prepare("INSERT INTO user_colors (user_id, color_id) VALUES (?, ?)");
        foreach ($color_ids as $color_id) {
            $stmtInsert->execute([$user_id, $color_id]);
        }
    }
}

function getUserColors(int $user_id): array {
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare("
        SELECT c.id, c.name FROM colors c
        INNER JOIN user_colors uc ON c.id = uc.color_id
        WHERE uc.user_id = ?
    ");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAllColors(): array {
    $pdo = Database::getConnection();
    $stmt = $pdo->query("SELECT * FROM colors ORDER BY name");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
