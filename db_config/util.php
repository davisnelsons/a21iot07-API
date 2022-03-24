<?php
function validate_date($date) {
    return (DateTime::createFromFormat('Y-m-d H:i:s', $date) !== false);
}