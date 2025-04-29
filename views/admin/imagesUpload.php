<?php
function imageUpload() {
    if (!isset($_FILES['image'])) {
        return false;
    }
    $uploadDir = './uploads/';
    // $uploadDir = './uploads/';
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $maxSize = 2 * 1024 * 1024;

    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $file = $_FILES['image'];
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    if ($file['size'] > $maxSize) {
        return false;
    }
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    if (!in_array($mime, $allowedTypes)) {
        return false;
    }
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $safeName = bin2hex(random_bytes(16)) . '.' . $extension;
    $targetPath = $uploadDir . $safeName;
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return $targetPath;
    }

    return false;
}
function userImageUpload() {
    if (!isset($_FILES['image'])) {
        return false;
    }
    $uploadDir = './uploads/user/';
    // $uploadDir = './uploads/';
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $maxSize = 2 * 1024 * 1024;

    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $file = $_FILES['image'];
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    if ($file['size'] > $maxSize) {
        return false;
    }
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    if (!in_array($mime, $allowedTypes)) {
        return false;
    }
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $safeName = bin2hex(random_bytes(16)) . '.' . $extension;
    $targetPath = $uploadDir . $safeName;
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return $targetPath;
    }

    return false;
}
?>