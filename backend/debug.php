<?php
require 'vendor/autoload.php';
require 'bootstrap/app.php';

use App\Models\TipeKamar;

$tipeKamars = TipeKamar::all();
foreach ($tipeKamars as $tk) {
  echo "ID: {$tk->id} | Nama: {$tk->nama_tipe_kamar} | Foto: {$tk->foto}\n";
  echo "Fasilitas (type): " . gettype($tk->fasilitas_tipe) . "\n";
  if (is_array($tk->fasilitas_tipe)) {
    echo "Fasilitas (array): " . json_encode($tk->fasilitas_tipe) . "\n";
  } else {
    echo "Fasilitas (raw): " . substr((string)$tk->fasilitas_tipe, 0, 200) . "\n";
  }
  echo "---\n";
}
