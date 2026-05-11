<?php
$st = App\Models\TipeKamar::where('nama_tipe_kamar', 'like', '%Standard%')->first(); if($st){ $st->foto = 'tipe_kamar/standard.png'; $st->save(); }
$dl = App\Models\TipeKamar::where('nama_tipe_kamar', 'like', '%Deluxe%')->first(); if($dl){ $dl->foto = 'tipe_kamar/deluxe.png'; $dl->save(); }
$su = App\Models\TipeKamar::where('nama_tipe_kamar', 'like', '%Suite%')->first(); if($su){ $su->foto = 'tipe_kamar/suite.png'; $su->save(); }
$fa = App\Models\TipeKamar::where('nama_tipe_kamar', 'like', '%Family%')->first(); if($fa){ $fa->foto = 'tipe_kamar/family.png'; $fa->save(); }
echo "Done\n";
