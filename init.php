<?php
// Inicializace databáze SQLite
$db = new PDO("sqlite:profile.db");

// Vytvoření tabulky profile, pokud neexistuje
$db->exec("CREATE TABLE IF NOT EXISTS profile (
    id INTEGER PRIMARY KEY CHECK (id = 1),
    name TEXT NOT NULL,
    skills_json TEXT
)");

// Vytvoření tabulky interests, pokud neexistuje
$db->exec("CREATE TABLE IF NOT EXISTS interests (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL UNIQUE
)");

// Vytvoření tabulky projects, pokud neexistuje
$db->exec("CREATE TABLE IF NOT EXISTS projects (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL UNIQUE
)");

// Vložení výchozích dat do profile
$stmt = $db->prepare("INSERT OR IGNORE INTO profile (id, name, skills_json) VALUES (1, ?, ?)");
$stmt->execute(['Marek Novák', json_encode(['PHP', 'HTML', 'CSS', 'Git', 'SQL'])]);

// Vložení výchozích projektů
$default_projects = ["Webová prezentace", "API pro správu úloh", "Interní nástroj pro import dat"];
$stmt = $db->prepare("INSERT OR IGNORE INTO projects (name) VALUES (?)");
foreach ($default_projects as $project) {
    $stmt->execute([$project]);
}
?>