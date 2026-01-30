<?php

namespace App\Controllers;

class Umkm extends BaseController
{
    public function index()
    {
        // Ambil data dari tabel tb_umkm
        $builder = $this->db->table('tb_umkm');
        $query   = $builder->get()->getResult();
        $data['umkm'] = $query;
        
        return view('umkm/index', $data);
    }

    public function store()
    {
        // Ambil data dari form
        $data = [
            'nama'     => $this->request->getPost('nama'),
            'username' => $this->request->getPost('username'),
            'status'   => $this->request->getPost('status'), // Tambahan field status
            // Password wajib di-hash
            'password' => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
        ];

        $this->db->table('tb_umkm')->insert($data);

        return redirect()->to(site_url('umkm'))->with('success', 'Data UMKM Berhasil Disimpan');
    }

    public function update()
    {
        $id = $this->request->getPost('id_umkm');
        
        $data = [
            'nama'     => $this->request->getPost('nama'),
            'username' => $this->request->getPost('username'),
            'status'   => $this->request->getPost('status'),
        ];

        // Cek apakah password diisi? Jika ya, update password. Jika kosong, biarkan password lama.
        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_BCRYPT);
        }

        $this->db->table('tb_umkm')->where(['id_umkm' => $id])->update($data);

        return redirect()->to(site_url('umkm'))->with('success', 'Data UMKM Berhasil Diupdate');
    }

    public function destroy($id)
    {
        $this->db->table('tb_umkm')->where(['id_umkm' => $id])->delete();
        return redirect()->to(site_url('umkm'))->with('success', 'Data UMKM Berhasil Dihapus');
    }
}