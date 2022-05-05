<?php
function validateDateTime($date) {
    return (DateTime::createFromFormat('Y-m-d H:i:s', $date) !== false);
}
function validateDate($date) {
    return (DateTime::createFromFormat('Y-m-d', $date) !== false);
}

function date_invalid() {
    http_response_code(400);
    echo json_encode(array("error" => "invalid date format"));
    exit();
}