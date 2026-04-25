<?php
header("Content-Type: application/json; charset=UTF-8");

$host = "localhost";
$db   = "utazas12";
$user = "utazas12";
$pass = "Webprog1-beadando";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    echo json_encode(["error" => "DB hiba"]);
    exit;
}

$conn->set_charset("utf8");

// =========================
// TÁBLÁK LISTÁZÁSA
// =========================
if (isset($_GET['tables'])) {
    $result = $conn->query("SHOW TABLES");

    $tables = [];
    while ($row = $result->fetch_array()) {
        $tables[] = $row[0];
    }

    echo json_encode($tables);
    exit;
}

// =========================
// ADATOK LEKÉRÉSE
// =========================
if (isset($_GET['table'])) {
    $table = $_GET['table'];

    $colsRes = $conn->query("SHOW COLUMNS FROM `$table`");

    $columns = [];
    while ($col = $colsRes->fetch_assoc()) {
        $columns[] = $col['Field'];
    }

    $dataRes = $conn->query("SELECT * FROM `$table`");

    $data = [];
    while ($row = $dataRes->fetch_assoc()) {
        $data[] = $row;
    }

    echo json_encode([
        "columns" => $columns,
        "data" => $data
    ]);
    exit;
}

// =========================
// CREATE / UPDATE / DELETE
// =========================
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents("php://input"), true);

if (!$input) {
    echo json_encode(["error" => "Nincs input"]);
    exit;
}

// =========================
// CREATE / UPDATE
// =========================
if ($method === 'POST') {

    $table = $input['table'];
    $action = $input['action'];

    unset($input['table'], $input['action']);

    // CREATE
    if ($action === 'create') {

        $cols = [];
        $vals = [];

        foreach ($input as $col => $val) {
            $cols[] = "`$col`";
            $vals[] = "'" . $conn->real_escape_string($val) . "'";
        }

        $colStr = implode(",", $cols);
        $valStr = implode(",", $vals);

        $sql = "INSERT INTO `$table` ($colStr) VALUES ($valStr)";
        $conn->query($sql);

        echo json_encode(["success" => true]);
        exit;
    }

    // UPDATE
    if ($action === 'update') {

        // frontend küldi
        $pk = $input['pk'];
        $id = $input[$pk];

        unset($input[$pk]);
        unset($input['pk']);

        $updates = [];

        foreach ($input as $col => $val) {
            $val = $conn->real_escape_string($val);
            $updates[] = "`$col`='$val'";
        }

        $updateStr = implode(",", $updates);

        $sql = "UPDATE `$table` SET $updateStr WHERE `$pk`='$id'";
        $conn->query($sql);

        echo json_encode(["success" => true]);
        exit;
    }
}

// =========================
// DELETE
// =========================
if ($method === 'DELETE') {

    $table = $input['table'];
    $id = $input['id'];
    $pk = $input['pk'];

    $id = $conn->real_escape_string($id);

    $sql = "DELETE FROM `$table` WHERE `$pk`='$id'";
    $conn->query($sql);

    echo json_encode(["success" => true]);
    exit;
}

echo json_encode(["error" => "Nincs ilyen kérés"]);