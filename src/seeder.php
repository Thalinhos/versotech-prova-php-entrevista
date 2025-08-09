<?php

require_once "connection.php";

try {
    $pdo = Database::getConnection();

    $pdo->beginTransaction();

    $pdo->exec("DROP TABLE IF EXISTS users CASCADE");
    $pdo->exec("DROP TABLE IF EXISTS colors CASCADE");
    $pdo->exec("DROP TABLE IF EXISTS user_colors CASCADE");

    $sqlCreateColors = "
        CREATE TABLE IF NOT EXISTS colors (
            id SERIAL PRIMARY KEY,
            name VARCHAR(50) NOT NULL
        )
    ";
    $pdo->exec($sqlCreateColors);

    $sqlCreateUsers = "
        CREATE TABLE IF NOT EXISTS users (
            id SERIAL PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE
        )
    ";
    $pdo->exec($sqlCreateUsers);

    $sqlCreateUsersColor = "
        CREATE TABLE IF NOT EXISTS user_colors (
            user_id INTEGER NOT NULL,
            color_id INTEGER NOT NULL,
            PRIMARY KEY(user_id, color_id),
            FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY(color_id) REFERENCES colors(id) ON DELETE CASCADE
        )
    ";
    $pdo->exec($sqlCreateUsersColor);

    $sqlInsertColors = "
        INSERT INTO colors (name) VALUES
        ('Blue'), ('Red'), ('Yellow'), ('Green')
    ";
    $pdo->exec($sqlInsertColors);

    $sqlInsertUsers = "
        INSERT INTO users (name, email) VALUES
        ('Foo Bar', 'foo@bar.com'),
        ('Bar Baz', 'bar@baz.com'),
        ('Baz Foo', 'baz@foo.com')
    ";
    $pdo->exec($sqlInsertUsers);

    $sqlInsertUserColor = "
        INSERT INTO user_colors (user_id, color_id) VALUES 
        (1, 1),
        (1, 2),
        (2, 2),
        (3, 3)
    ";
    $pdo->exec($sqlInsertUserColor);

    $pdo->commit();

    header('Location: index.php');
    exit;
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "Erro: " . $e->getMessage();
}
