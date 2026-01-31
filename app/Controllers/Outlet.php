<?php

namespace App\Controllers;

use App\Models\OutletModel;
use App\Models\OperasionalModel;

class Outlet extends BaseController
{
    protected $outletModel;
    protected $opsModel;

    public function __construct()
    {
        $this->outletModel = new OutletModel();
        $this->opsModel = new OperasionalModel();
    }

    public function index()
    {
        $role = session()->get('role');
        $idUser = session()->get('id_user'); 

        $this->outletModel->select('tb_outlet.*, tb_umkm.nama as nama_umkm');
        $this->outletModel->join('tb_umkm', 'tb_umkm.id_umkm = tb_outlet.id_umkm', 'left');

        if ($role == 'umkm') {
            $this->outletModel->where('tb_outlet.id_umkm', $idUser);
        }

        $data['outlets'] = $this->outletModel->findAll();


        $builder = $this->db->table('tb_umkm');

        if ($role == 'umkm') {
            $builder->where('id_umkm', $idUser);
        } else {
            $builder->where('status', 'Aktif');
        }

        $data['umkm_list'] = $builder->get()->getResult();

        return view('outlet/index', $data);
        }

    public function getData($id)
    {
        $outlet = $this->outletModel->find($id);
        $ops = $this->opsModel->where('id_outlet', $id)->findAll();
        return $this->response->setJSON(['outlet' => $outlet, 'ops' => $ops]);
    }

    public function store()
    {

        
        // 1. Upload Foto
        $fileFoto = $this->request->getFile('foto');
        $namaFoto = null; // Default null jika tidak ada foto
        
        if ($fileFoto && $fileFoto->isValid() && !$fileFoto->hasMoved()) {
            $namaFoto = $fileFoto->getRandomName();
            $fileFoto->move('uploads/outlet', $namaFoto);
        }

        // 2. Simpan Data Outlet
        $dataOutlet = [
            'id_umkm'   => $this->request->getPost('id_umkm'),
            'nama'      => $this->request->getPost('nama'),
            'alamat'    => $this->request->getPost('alamat'),
            'kabupaten' => $this->request->getPost('kabupaten'), 
            'kategori'  => $this->request->getPost('kategori'),
            'kontak'    => $this->request->getPost('kontak'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'latitude'  => $this->request->getPost('latitude'),
            'longitude' => $this->request->getPost('longitude'),
            'foto'      => $namaFoto,
            'status'    => 'Waiting Approval',
        ];


        
        // DEBUGGING: Cek apakah insert berhasil
        if (!$this->outletModel->insert($dataOutlet)) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data. Cek inputan Anda.');
        }

        $id_outlet = $this->outletModel->getInsertID();

        $hari = $this->request->getPost('hari');
        $buka = $this->request->getPost('jam_buka');
        $tutup = $this->request->getPost('jam_tutup');

        if($hari){
            for($i=0; $i < count($hari); $i++){
                if(!empty($hari[$i])){
                    $this->opsModel->insert([
                        'id_outlet' => $id_outlet,
                        'nama_hari' => $hari[$i],
                        'jam_buka' => $buka[$i],
                        'jam_tutup' => $tutup[$i],
                    ]);
                }
            }
        }

        return redirect()->to('outlet')->with('success', 'Data Outlet Berhasil Disimpan');
    }

    public function approve()
    {
        if (session()->get('role') != 'admin') {
            return redirect()->back()->with('error', 'Unauthorized access');
        }

        $id = $this->request->getPost('id_outlet');
        $status = $this->request->getPost('status'); // 'Approved' or 'Rejected'

        if ($this->outletModel->update($id, ['status' => $status])) {
            return redirect()->to('outlet')->with('success', 'Status Outlet Berhasil Diubah menjadi ' . $status);
        } else {
            return redirect()->to('outlet')->with('error', 'Gagal mengubah status');
        }
    }

    public function update()
    {
        $id = $this->request->getPost('id_outlet');
        
        // 1. Cek Foto Baru
        $fileFoto = $this->request->getFile('foto');
        
        $dataOutlet = [
            'id_umkm'   => $this->request->getPost('id_umkm'),
            'nama'      => $this->request->getPost('nama'),
            'alamat'    => $this->request->getPost('alamat'),
            'kabupaten' => $this->request->getPost('kabupaten'), 
            'kategori'  => $this->request->getPost('kategori'),
            'kontak'    => $this->request->getPost('kontak'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'latitude'  => $this->request->getPost('latitude'),
            'longitude' => $this->request->getPost('longitude'),
        ];

        if ($fileFoto && $fileFoto->isValid() && !$fileFoto->hasMoved()) {
            $namaFoto = $fileFoto->getRandomName();
            $fileFoto->move('uploads/outlet', $namaFoto);
            $dataOutlet['foto'] = $namaFoto;
        }

        // Cek update berhasil atau tidak
        if (!$this->outletModel->update($id, $dataOutlet)) {
             return redirect()->back()->withInput()->with('error', 'Gagal update data.');
        }

        // 2. Update Operasional
        $this->opsModel->where('id_outlet', $id)->delete();

        $hari = $this->request->getPost('hari');
        $buka = $this->request->getPost('jam_buka');
        $tutup = $this->request->getPost('jam_tutup');

        if($hari){
            for($i=0; $i < count($hari); $i++){
                if(!empty($hari[$i])){
                    $this->opsModel->insert([
                        'id_outlet' => $id,
                        'nama_hari' => $hari[$i],
                        'jam_buka' => $buka[$i],
                        'jam_tutup' => $tutup[$i],
                    ]);
                }
            }
        }

        return redirect()->to('outlet')->with('success', 'Data Outlet Berhasil Diupdate');
    }

 

    public function destroy($id)
    {
        $this->outletModel->delete($id);
        return redirect()->to('outlet')->with('success', 'Data Outlet Berhasil Dihapus');
    }
}