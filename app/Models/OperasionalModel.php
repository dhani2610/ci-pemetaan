<?php namespace App\Models;
use CodeIgniter\Model;

class OperasionalModel extends Model {
    protected $table = 'tb_operasional';
    protected $primaryKey = 'id_operasional';
    protected $allowedFields = ['id_outlet', 'nama_hari', 'jam_buka', 'jam_tutup'];
}