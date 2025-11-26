<?php
require 'db_connect.php';

$conn = db_connect();

if ($conn) {
    echo "OK: Connected!";
} else {
    echo "Error.";
}
