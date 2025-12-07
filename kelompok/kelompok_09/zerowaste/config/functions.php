<?php
function generateTicketCode() {
    $prefix = "FR-";
    $code = strtoupper(substr(md5(uniqid(rand(), true)), 0, 3));
    return $prefix . $code; 
}

function formatTanggal($time) {
    return date("d M Y H:i", strtotime($time));
}

function getUserIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

function logActivity($conn, $user_id, $action, $description) {
    $ip = getUserIP();
    $stmt = mysqli_prepare($conn, "INSERT INTO activity_logs (user_id, action, description, ip_address) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "isss", $user_id, $action, $description, $ip);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}
?>