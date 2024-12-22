<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['video'])) {
    $uploadDir = 'uploads/';
    $uploadFile = $uploadDir . basename($_FILES['video']['name']);

    if (move_uploaded_file($_FILES['video']['tmp_name'], $uploadFile)) {
        echo json_encode(['status' => 'success', 'file' => basename($uploadFile)]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to upload file.']);
    }
}
?>
