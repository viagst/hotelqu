<?php
$mysqli = new mysqli('localhost', 'root', '', 'hotel');
if ($mysqli->connect_error) {
  die('Connect Error: ' . $mysqli->connect_error);
}

$result = $mysqli->query('SELECT id, nama_tipe_kamar, foto, deskripsi, fasilitas_tipe FROM tipe_kamars');
while ($row = $result->fetch_assoc()) {
  echo "ID: {$row['id']} | Nama: {$row['nama_tipe_kamar']} | Foto: {$row['foto']}\n";
  echo "Deskripsi: " . substr($row['deskripsi'], 0, 100) . "\n";
  echo "Fasilitas: " . substr($row['fasilitas_tipe'], 0, 100) . "\n";
  echo "---\n";
}
$mysqli->close();
