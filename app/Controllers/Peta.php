<?php

namespace App\Controllers;

use App\Models\OutletModel;
use App\Models\ProdukModel;
use App\Models\OperasionalModel;

class Peta extends BaseController
{
    protected $outletModel;
    protected $produkModel;
    protected $opsModel;

    public function __construct()
    {
        $this->outletModel = new OutletModel();
        $this->produkModel = new ProdukModel();
        $this->opsModel = new OperasionalModel();
    }

    public function index()
    {
        return view('peta/index');
    }

    public function getOutlets()
    {
        $data = $this->outletModel->where('status', 'Approved')->findAll();
        return $this->response->setJSON($data);
    }

    public function getDetail($id)
    {
        $outlet = $this->outletModel->find($id);
        
        $produk = $this->produkModel->where('id_outlet', $id)->findAll();
        
        $ops = $this->opsModel->where('id_outlet', $id)->findAll();

        return $this->response->setJSON([
            'outlet' => $outlet,
            'produk' => $produk,
            'operasional' => $ops
        ]);
    }
}