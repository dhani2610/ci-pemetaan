<?php

namespace App\Controllers;

class Admin extends BaseController
{
    public function index()
    {
        $builder = $this->db->table('tb_admin');
        $query   = $builder->get()->getResult();
        $data['admin'] = $query;
        
        return view('admin/index', $data);
    }

    public function store()
    {
        // Ambil data dari form
        $data = [
            'nama'     => $this->request->getPost('nama'),
            'username' => $this->request->getPost('username'),
            // Password wajib di-hash
            'password' => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
        ];

        $this->db->table('tb_admin')->insert($data);

        return redirect()->to(site_url('admin'))->with('success', 'Data Admin Berhasil Disimpan');
    }

    public function update()
    {
        $id = $this->request->getPost('id_admin');
        
        $data = [
            'nama'     => $this->request->getPost('nama'),
            'username' => $this->request->getPost('username'),
        ];

        // Cek apakah password diisi? Jika ya, update password. Jika kosong, biarkan password lama.
        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_BCRYPT);
        }

        $this->db->table('tb_admin')->where(['id_admin' => $id])->update($data);

        return redirect()->to(site_url('admin'))->with('success', 'Data Admin Berhasil Diupdate');
    }

    public function destroy($id)
    {
        $this->db->table('tb_admin')->where(['id_admin' => $id])->delete();
        return redirect()->to(site_url('admin'))->with('success', 'Data Admin Berhasil Dihapus');
    }
}