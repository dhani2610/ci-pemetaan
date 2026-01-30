<?php 

namespace App\Models;

use CodeIgniter\Model;

class UmkmModel extends Model
{
    protected $table = 'tb_umkm';
    protected $primaryKey = 'id_umkm';
    protected $returnType = 'object'; 
    protected $allowedFields = ['nama', 'username', 'password', 'status'];
}