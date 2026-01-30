<?php

namespace App\Models;

use CodeIgniter\Model;

class ProdukModel extends Model
{
    protected $table            = 'tb_produk';
    protected $primaryKey       = 'id_produk';
    protected $returnType       = 'object';
    protected $allowedFields    = ['id_outlet', 'nama', 'jenis', 'harga'];
    
    public function getAllProduk($id_outlet = null)
    {
        $builder = $this->select('tb_produk.*, tb_outlet.nama as nama_outlet')
                        ->join('tb_outlet', 'tb_outlet.id_outlet = tb_produk.id_outlet');

        // Jika ada filter outlet
        if ($id_outlet) {
            $builder->where('tb_produk.id_outlet', $id_outlet);
        }

        return $builder->findAll();
    }
}