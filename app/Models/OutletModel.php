<?php namespace App\Models;
use CodeIgniter\Model;

class OutletModel extends Model {
    protected $table = 'tb_outlet';
    protected $primaryKey = 'id_outlet';
    protected $allowedFields = ['id_umkm', 'nama', 'kategori', 'alamat', 'kabupaten', 'deskripsi', 'kontak', 'longitude', 'latitude', 'foto', 'status'];
}