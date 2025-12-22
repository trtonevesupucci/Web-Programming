<?php
echo "Step 1: Started<br>";

echo "Step 2: Loading config...<br>";
require_once 'rest/config.php';

echo "Step 3: Config loaded!<br>";
echo "DB Name: " . Config::DB_NAME() . "<br>";
echo "Done!";
?>