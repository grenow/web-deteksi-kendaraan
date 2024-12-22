<?php
$directory = 'uploads/';
$files = array_diff(scandir($directory), ['..', '.']);

echo json_encode(['videos' => $files]);
?>
