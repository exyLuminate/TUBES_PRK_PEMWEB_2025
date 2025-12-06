<?php
function generateTicketCode() {
    $prefix = "FR-";
    $code = strtoupper(substr(md5(uniqid(rand(), true)), 0, 3));
    return $prefix . $code; 
}

function formatTanggal($time) {
    return date("d M Y H:i", strtotime($time));
}
?>
