<?php
$filename = '../data/universities.json'; // or connect to DB if preferred

// Load data
$data = file_exists($filename) ? json_decode(file_get_contents($filename), true) : [];

$action = $_POST['action'] ?? '';

if ($action === 'add') {
    $new = [
        "id" => uniqid(),
        "name" => $_POST['name'],
        "location" => $_POST['location'],
        "type" => $_POST['type'],
        "website" => $_POST['website'] ?? '',
        "description" => $_POST['description'] ?? ''
    ];
    $data[] = $new;
    file_put_contents($filename, json_encode($data));
    echo "added";
} elseif ($action === 'delete') {
    $id = $_POST['id'];
    $data = array_filter($data, fn($u) => $u['id'] !== $id);
    file_put_contents($filename, json_encode(array_values($data)));
    echo "deleted";
} elseif ($action === 'edit') {
    foreach ($data as &$u) {
        if ($u['id'] === $_POST['id']) {
            $u['name'] = $_POST['name'];
            $u['location'] = $_POST['location'];
            $u['type'] = $_POST['type'];
            $u['website'] = $_POST['website'];
            $u['description'] = $_POST['description'];
            break;
        }
    }
    file_put_contents($filename, json_encode($data));
    echo "edited";
} else {
    echo "invalid";
}