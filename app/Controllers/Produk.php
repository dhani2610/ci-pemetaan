<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ProdukModel;
use App\Models\OutletModel; 
use App\Models\umkmModel; 

class Produk extends BaseController
{
    protected $produkModel;
    protected $outletModel;
    protected $umkmModel;

    public function __construct()
    {
        $this->produkModel = new ProdukModel();
        $this->outletModel = new OutletModel();
        $this->umkmModel = new UmkmModel();
    }

    public function index()
    {
        $role = session()->get('role');
        $idUser = session()->get('id_user');
        
        // 1. Ambil input filter (Gunakan getGet karena form method="get")
        $filterOutlet = $this->request->getGet('id_outlet');
        $data['selected_outlet'] = $filterOutlet;

        // --- BAGIAN 1: DATA UNTUK DROPDOWN (OUTLET) ---
        $builder = $this->outletModel
            ->select('tb_outlet.*, tb_umkm.nama as nama_umkm')
            ->join('tb_umkm', 'tb_umkm.id_umkm = tb_outlet.id_umkm', 'left');

        if ($role == 'umkm') {
            $builder->where('tb_outlet.id_umkm', $idUser);
        }

        $data['outlets'] = $builder->findAll();


        // --- BAGIAN 2: DATA PRODUK (YANG HILANG SEBELUMNYA) ---
        
        // Jika Role UMKM
        if ($role == 'umkm') {
            // Jika ada filter outlet yang dipilih
            if (!empty($filterOutlet)) {
                // Pastikan outlet itu milik UMKM ini (security check optional)
                $data['produk'] = $this->produkModel->getAllProduk($filterOutlet);
            } else {
                // Jika "Tampilkan Semua", hanya tampilkan produk dari outlet milik UMKM ini
                // Kita buat query manual karena getAllProduk model kamu belum support filter by id_umkm
                $data['produk'] = $this->produkModel
                    ->select('tb_produk.*, tb_outlet.nama as nama_outlet')
                    ->join('tb_outlet', 'tb_outlet.id_outlet = tb_produk.id_outlet')
                    ->where('tb_outlet.id_umkm', $idUser)
                    ->findAll();
            }
        } 
        // Jika Role Admin / Lainnya
        else {
            // Gunakan method model yang sudah kamu buat
            // Jika filterOutlet null, dia otomatis ambil semua (sesuai logika model)
            $data['produk'] = $this->produkModel->getAllProduk($filterOutlet);
        }

        // --- BAGIAN 3: DATA TAMBAHAN ---
        if ($role == 'umkm') {
            $data['umkm_list'] = $this->umkmModel->where('id_umkm', $idUser)->findAll();
        } else {
            $data['umkm_list'] = $this->umkmModel->where('status', 'Aktif')->findAll();
        }
        
        return view('produk/index', $data);
    }

    public function store()
    {
        $data = [
            'id_outlet' => $this->request->getPost('id_outlet'),
            'nama'      => $this->request->getPost('nama'),
            'jenis'     => $this->request->getPost('jenis'),
            'harga'     => $this->request->getPost('harga'),
        ];

        $this->produkModel->insert($data);
        return redirect()->to('produk')->with('success', 'Data Produk Berhasil Disimpan');
    }

    public function update()
    {
        $id = $this->request->getPost('id_produk');
        
        $data = [
            'id_outlet' => $this->request->getPost('id_outlet'),
            'nama'      => $this->request->getPost('nama'),
            'jenis'     => $this->request->getPost('jenis'),
            'harga'     => $this->request->getPost('harga'),
        ];

        $this->produkModel->update($id, $data);
        return redirect()->to('produk')->with('success', 'Data Produk Berhasil Diupdate');
    }

    public function destroy($id)
    {
        $this->produkModel->delete($id);
        return redirect()->to('produk')->with('success', 'Data Produk Berhasil Dihapus');
    }
}