<?php

namespace App\Controllers;

class Auth extends BaseController
{
    public function index()
    {
       return redirect()->to(site_url('login'));
    }

    public function login()
    {
       if(session('id_user')){
         return redirect()->to(site_url('home'));
       }
       return view('auth/login');
    }

    public function loginProcess()
    {
       $post = $this->request->getPost();
       
       // Ambil inputan role dari form
       $role = $post['role']; // 'admin' atau 'umkm'
       $username = $post['username'];
       $password = $post['password'];

       // Tentukan tabel berdasarkan role
       if($role == 'admin') {
           $table = 'tb_admin';
       } else {
           $table = 'tb_umkm';
       }

       // Cari user di database
       $query = $this->db->table($table)->getWhere(['username' => $username]);
       $user = $query->getRow();

       if($user){
           // Cek Password
           if(password_verify($password, $user->password)){
               
               // Khusus UMKM: Cek Status Aktif/Tidak
               if($role == 'umkm' && $user->status != 'Aktif'){
                   return redirect()->back()->with('error', 'Akun Anda Non-Aktif. Hubungi Admin.');
               }

               // Siapkan Session
               $param = [
                   'id_user'   => ($role == 'admin') ? $user->id_admin : $user->id_umkm,
                   'username'  => $user->username,
                   'nama'      => $user->nama, // Biar bisa tampil nama di dashboard
                   'role'      => $role, // 'admin' atau 'umkm'
                   'isLoggedIn'=> true
               ];
               
               session()->set($param);
               return redirect()->to(site_url('peta'));

           } else {
               return redirect()->back()->with('error', 'Password tidak sesuai');
           }
       } else {
           return redirect()->back()->with('error', 'Username tidak ditemukan di data ' . strtoupper($role));
       }
    }

    public function logout()
    {
       session()->destroy(); // Hapus semua session
       return redirect()->to(site_url('login'));
    }

    public function register()
    {
        if (session('id_user')) {
            return redirect()->to(site_url('dashboard'));
        }
        return view('auth/register');
    }

    // 2. PROSES PENYIMPANAN DATA REGISTER
    public function registerProcess()
    {
        // Validasi Input
        if (!$this->validate([
            'nama' => [
                'rules' => 'required',
                'errors' => ['required' => 'Nama UMKM harus diisi']
            ],
            'username' => [
                'rules' => 'required|is_unique[tb_umkm.username]', // Cek agar username tidak kembar
                'errors' => [
                    'required' => 'Username harus diisi',
                    'is_unique' => 'Username sudah terdaftar, gunakan yang lain'
                ]
            ],
            'password' => [
                'rules' => 'required|min_length[4]',
                'errors' => [
                    'required' => 'Password harus diisi',
                    'min_length' => 'Password minimal 4 karakter'
                ]
            ],
            'password_conf' => [
                'rules' => 'matches[password]',
                'errors' => ['matches' => 'Konfirmasi password tidak sesuai']
            ],
        ])) {
            // Jika validasi gagal, kembalikan ke form dengan error
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Siapkan Data
        $data = [
            'nama'     => $this->request->getPost('nama'),
            'username' => $this->request->getPost('username'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
            'status'   => 'Aktif', // Default langsung Aktif saat register sendiri
        ];

        // Simpan ke Tabel tb_umkm
        $this->db->table('tb_umkm')->insert($data);

        // Redirect ke Login dengan Pesan Sukses
        return redirect()->to(site_url('login'))->with('success', 'Registrasi Berhasil! Silakan Login.');
    }
}