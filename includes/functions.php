<?php
session_start();
date_default_timezone_set('Asia/Manila');
require_once __DIR__ . '/../db_connect.php';

function clean($text) {
    return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}

function run($conn, $sql, $types = '', $params = array()) {
    $stmt = $conn->prepare($sql);
    if ($types != '') {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    return $stmt;
}

function setFlash($type, $message) {
    $_SESSION['flash'] = array('type' => $type, 'message' => $message);
}

function isAdmin() {
    return isset($_SESSION['admin_id']);
}

function requireAdmin() {
    if (!isAdmin()) {
        header('Location: login.php');
        exit;
    }
}

function formatDate($date) {
    return date('M j, Y', strtotime($date));
}

function peso($amount) {
    return '&#8369;' . number_format($amount, 2);
}

function statusBadge($status) {
    $colors = array(
        'Pending' => 'secondary',
        'Under Review' => 'info text-dark',
        'Approved' => 'success',
        'Rejected' => 'danger',
        'Open' => 'success',
        'Closed' => 'secondary'
    );
    $color = isset($colors[$status]) ? $colors[$status] : 'secondary';
    return '<span class="badge bg-' . $color . '">' . clean($status) . '</span>';
}

function getYearLevels() {
    return array('1st Year', '2nd Year', '3rd Year', '4th Year', '5th Year');
}

function getStatuses() {
    return array('Pending', 'Under Review', 'Approved', 'Rejected');
}

function approvedCount($conn, $scholarshipId, $ignoreId = 0) {
    $sql = "SELECT COUNT(*) AS total FROM applications
            WHERE scholarship_id = ? AND status = 'Approved' AND application_id <> ?";
    $row = run($conn, $sql, 'ii', array($scholarshipId, $ignoreId))->get_result()->fetch_assoc();
    return (int) $row['total'];
}

function isOpenForApplying($s) {
    return $s['status'] == 'Open' && $s['deadline'] >= date('Y-m-d');
}

function makeReference($conn) {
    do {
        $code = 'SCH' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
        $stmt = run($conn, "SELECT application_id FROM applications WHERE reference_code = ?", 's', array($code));
        $stmt->store_result();
        $exists = $stmt->num_rows > 0;
        $stmt->close();
    } while ($exists);
    return $code;
}

function getApplicationData() {
    $fields = array('student_id_no', 'full_name', 'email', 'phone', 'course', 'year_level', 'gwa', 'reason');
    $data = array();
    foreach ($fields as $f) {
        $data[$f] = isset($_POST[$f]) ? trim($_POST[$f]) : '';
    }
    return $data;
}

function validateApplication($conn, $d, $scholarship, $ignoreId = 0, $byAdmin = false) {
    $errors = array();

    if ($d['student_id_no'] == '') {
        $errors[] = 'Student ID number is required.';
    }
    if (strlen($d['full_name']) < 3) {
        $errors[] = 'Please enter your full name.';
    }
    if (!filter_var($d['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }
    if (!preg_match('/^[0-9+\-\s()]{7,20}$/', $d['phone'])) {
        $errors[] = 'Please enter a valid phone number.';
    }
    if ($d['course'] == '') {
        $errors[] = 'Course or program is required.';
    }
    if (!in_array($d['year_level'], getYearLevels())) {
        $errors[] = 'Please choose your year level.';
    }
    if (!is_numeric($d['gwa']) || $d['gwa'] < 1 || $d['gwa'] > 5) {
        $errors[] = 'GWA must be a number from 1.00 to 5.00.';
    } elseif (!$byAdmin && $d['gwa'] > $scholarship['max_gwa']) {
        $errors[] = 'This scholarship needs a GWA of ' . number_format($scholarship['max_gwa'], 2) . ' or better.';
    }
    if (strlen($d['reason']) < 30) {
        $errors[] = 'Please write at least 30 characters on why you deserve this scholarship.';
    }
    if (strlen($d['reason']) > 1500) {
        $errors[] = 'Your reason is too long. Keep it under 1500 characters.';
    }

    if (count($errors) == 0) {
        $sql = "SELECT application_id FROM applications
                WHERE scholarship_id = ? AND student_id_no = ? AND application_id <> ?";
        $stmt = run($conn, $sql, 'isi', array($scholarship['scholarship_id'], $d['student_id_no'], $ignoreId));
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = 'An application with this student ID already exists for this scholarship.';
        }
        $stmt->close();
    }

    return $errors;
}

function saveDocument(&$errors) {
    if (!isset($_FILES['document']) || $_FILES['document']['error'] == UPLOAD_ERR_NO_FILE) {
        return null;
    }

    $file = $_FILES['document'];
    if ($file['error'] != UPLOAD_ERR_OK) {
        $errors[] = 'The file could not be uploaded. Please try again.';
        return null;
    }
    if ($file['size'] > 2 * 1024 * 1024) {
        $errors[] = 'The file is too big. The maximum is 2 MB.';
        return null;
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, array('pdf', 'jpg', 'jpeg', 'png'))) {
        $errors[] = 'Only PDF, JPG or PNG files are allowed.';
        return null;
    }

    $name = bin2hex(random_bytes(8)) . '.' . $ext;
    if (!move_uploaded_file($file['tmp_name'], __DIR__ . '/../uploads/' . $name)) {
        $errors[] = 'Could not save the file on the server.';
        return null;
    }
    return $name;
}

function deleteDocument($name) {
    if ($name != '' && $name != null) {
        $path = __DIR__ . '/../uploads/' . basename($name);
        if (file_exists($path)) {
            unlink($path);
        }
    }
}
