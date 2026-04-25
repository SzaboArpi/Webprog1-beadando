<?php
header('Content-Type: application/json');

// --- Adatbázis kapcsolat ---
$host = 'mysql.nethely.hu';
$db   = 'utazas12';
$user = 'utazas12';
$pass = 'Webprog1-beadando';
$charset = 'utf8';

$dsn = "mysql:host=$host;dbname=$db;";

try {
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (PDOException $e) {
    echo json_encode(['error' => 'DB kapcsolat hiba']);
    exit;
}

// --- Helper: bemenet JSON-ból ---
function getJsonInput() {
    return json_decode(file_get_contents("php://input"), true);
}

// --- GET: táblák listája ---
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['tables'])) {

    try {
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

        echo json_encode($tables);

    } catch (PDOException $e) {
        echo json_encode(['error' => 'Nem sikerült lekérni a táblákat']);
    }

    exit;
}

// --- GET (READ) ---
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    if (!isset($_GET['table'])) {
        echo json_encode(['error' => 'Nincs tábla megadva']);
        exit;
    }

    $table = $_GET['table'];

    // egyszerű védelem (csak betűk, számok, _)
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $table)) {
        echo json_encode(['error' => 'Érvénytelen tábla']);
        exit;
    }

    try {
        // oszlopok lekérdezése
        $stmt = $pdo->query("DESCRIBE `$table`");
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // adatok lekérdezése
        $stmt = $pdo->query("SELECT * FROM `$table`");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'columns' => $columns,
            'data' => $data
        ]);

    } catch (PDOException $e) {
        echo json_encode(['error' => 'Lekérdezési hiba']);
    }

    exit;
}

// --- POST (CREATE / UPDATE) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $input = getJsonInput();

    if (!isset($input['table'], $input['action'])) {
        echo json_encode(['error' => 'Hiányzó adatok']);
        exit;
    }

    $table = $input['table'];
    $action = $input['action'];

    // védelem
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $table)) {
        echo json_encode(['error' => 'Érvénytelen tábla']);
        exit;
    }

    // mezők kigyűjtése (id, table, action kivéve)
    $fields = [];
    foreach ($input as $key => $value) {
        if (!in_array($key, ['table', 'action', 'id'])) {
            $fields[$key] = $value;
        }
    }

    try {

        // --- CREATE ---
        if ($action === 'create') {

            $cols = implode(',', array_keys($fields));
            $placeholders = ':' . implode(', :', array_keys($fields));

            $sql = "INSERT INTO `$table` ($cols) VALUES ($placeholders)";
            $stmt = $pdo->prepare($sql);

            $stmt->execute($fields);

            echo json_encode(['success' => true]);
        }

        // --- UPDATE ---
        if ($action === 'update') {

            if (!isset($input['id'])) {
                echo json_encode(['error' => 'Hiányzó ID']);
                exit;
            }

            $id = $input['id'];

            $set = '';
            foreach ($fields as $key => $value) {
                $set .= "$key = :$key, ";
            }
            $set = rtrim($set, ', ');

            $sql = "UPDATE `$table` SET $set WHERE id = :id";
            $stmt = $pdo->prepare($sql);

            $fields['id'] = $id;
            $stmt->execute($fields);

            echo json_encode(['success' => true]);
        }

    } catch (PDOException $e) {
        echo json_encode(['error' => 'Mentési hiba']);
    }

    exit;
}

// --- DELETE ---
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {

    $input = getJsonInput();

    if (!isset($input['id'], $input['table'])) {
        echo json_encode(['error' => 'Hiányzó adatok']);
        exit;
    }

    $id = $input['id'];
    $table = $input['table'];

    // védelem
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $table)) {
        echo json_encode(['error' => 'Érvénytelen tábla']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM `$table` WHERE id = :id");
        $stmt->execute(['id' => $id]);

        echo json_encode(['success' => true]);

    } catch (PDOException $e) {
        echo json_encode(['error' => 'Törlési hiba']);
    }

    exit;
}

// --- fallback ---
echo json_encode(['error' => 'Nem támogatott metódus']);