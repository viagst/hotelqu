<?php
$data = json_decode(file_get_contents('error.json'), true);
echo $data['message'] ?? 'No message';
echo "\n";
echo $data['file'] ?? 'No file';
echo " : ";
echo $data['line'] ?? 'No line';
