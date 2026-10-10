<?php
// config/upload_helper.php - Robust Image & Document File Upload Helper

function processImageUpload($fileField, $inputField, $existingValue = null, $prefix = 'img') {
    $uploadDir = __DIR__ . '/../uploads/';
    if (!is_dir($uploadDir)) {
        @mkdir($uploadDir, 0777, true);
    }

    // 1. Check Multipart $_FILES Upload
    if (isset($_FILES[$fileField]) && is_array($_FILES[$fileField]) && $_FILES[$fileField]['error'] === UPLOAD_ERR_OK && !empty($_FILES[$fileField]['tmp_name'])) {
        $tmpName = $_FILES[$fileField]['tmp_name'];
        $origName = $_FILES[$fileField]['name'] ?? '';
        $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
        
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'])) {
            $ext = 'jpg';
        }

        $filename = $prefix . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $targetPath = $uploadDir . $filename;

        if (move_uploaded_file($tmpName, $targetPath)) {
            return 'uploads/' . $filename;
        } elseif (@file_put_contents($targetPath, file_get_contents($tmpName))) {
            return 'uploads/' . $filename;
        }
    }

    // 2. Check JSON / POST Input data
    $inputValue = null;
    if (is_array($inputField)) {
        $inputValue = $inputField[$fileField] ?? null;
    } elseif (is_string($inputField)) {
        $inputValue = $inputField;
    }

    if (!empty($inputValue) && is_string($inputValue)) {
        $trimmed = trim($inputValue);
        
        // Base64 Data URI handling -> save to file
        if (preg_match('/^data:image\/([a-zA-Z0-9\+\-\.]+);base64,(.+)$/', $trimmed, $matches)) {
            $rawExt = strtolower($matches[1]);
            $ext = ($rawExt === 'jpeg' || $rawExt === 'jpg') ? 'jpg' : (($rawExt === 'png') ? 'png' : (($rawExt === 'webp') ? 'webp' : 'png'));
            $data = base64_decode($matches[2]);
            if ($data !== false) {
                $filename = $prefix . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                $targetPath = $uploadDir . $filename;
                if (@file_put_contents($targetPath, $data) !== false) {
                    return 'uploads/' . $filename;
                }
            }
        }

        // Direct URL or local file path
        if (strpos($trimmed, 'http://') === 0 || strpos($trimmed, 'https://') === 0 || strpos($trimmed, 'uploads/') === 0 || strpos($trimmed, 'assets/') === 0) {
            return $trimmed;
        }
    }

    // 3. Keep Existing Database Value (Do NOT overwrite with empty string)
    return $existingValue ?? '';
}
