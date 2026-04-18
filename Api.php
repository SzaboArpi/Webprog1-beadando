<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

// kapcsolat
$conn = new mysqli("localhost", "root", "", "utazas");

$method = $_SERVER['REQUEST_METHOD'];

// READ
if ($method === 'GET') {
    $result = $conn->query("SELECT * FROM szalloda");
    $data = [];

    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    echo json_encode($data);
}

// CREATE
if ($method === 'POST') {
    $input = json_decode(file_get_contents("php://input"), true);

    $nev = $input['nev'];
    $bes = $input['besorolas'];

    $conn->query("INSERT INTO szalloda (nev, besorolas) VALUES ('$nev', $bes)");

    echo json_encode(["message" => "Added"]);
}

// UPDATE
if ($method === 'PUT') {
    parse_str($_SERVER['QUERY_STRING'], $query);
    $id = $query['id'];

    $input = json_decode(file_get_contents("php://input"), true);

    $nev = $input['nev'];
    $bes = $input['besorolas'];

    $conn->query("UPDATE szalloda SET nev='$nev', besorolas=$bes WHERE id=$id");

    echo json_encode(["message" => "Updated"]);
}

// DELETE
if ($method === 'DELETE') {
    parse_str($_SERVER['QUERY_STRING'], $query);
    $id = $query['id'];

    $conn->query("DELETE FROM szalloda WHERE id=$id");

    echo json_encode(["message" => "Deleted"]);
}
?>