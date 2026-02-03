<?php

namespace App\Controllers;

class Auth extends BaseController
{
    public function index()
    {
       return redirect()->to(site_url('login'));
    }

    // 1. Login untuk UMKM
    public function login()
    {
       if(session('id_user')){
         return redirect()->to(site_url('home'));
       }
       
       $data = [
           'title' => 'Login UMKM',
           'role'  => 'umkm' // Set role otomatis
       ];

       return view('auth/login', $data);
    }

    // 2. Login untuk ADMIN (Method Baru)
    public function adminLogin()
    {
       if(session('id_user')){
         return redirect()->to(site_url('home'));
       }

       $data = [
           'title' => 'Login Administrator',
           'role'  => 'admin' // Set role otomatis
       ];

       return view('auth/login', $data);
    }

    public function loginProcess()
    {
       $post = $this->request->getPost();
       
       $role = $post['role']; // 'admin' atau 'umkm'
       $username = $post['username'];
       $password = $post['password'];

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
               
               if($role == 'umkm' && $user->status != 'Aktif'){
                   return redirect()->back()->with('error', 'Akun Anda Non-Aktif. Hubungi Admin.');
               }

               $param = [
                   'id_user'   => ($role == 'admin') ? $user->id_admin : $user->id_umkm,
                   'username'  => $user->username,
                   'nama'      => $user->nama, 
                   'role'      => $role, 
                   'isLoggedIn'=> true
               ];
               
               session()->set($param);
               
               if($role == 'admin') {
                    return redirect()->to(site_url('admin')); // Contoh dashboard admin
               } else {
                    return redirect()->to(site_url('peta'));
               }

           } else {
               return redirect()->back()->with('error', 'Password tidak sesuai');
           }
       } else {
           return redirect()->back()->with('error', 'Username tidak ditemukan di data ' . strtoupper($role));
       }
    }

    public function logout()
    {
       session()->destroy(); 
       return redirect()->to(site_url('login'));
    }
    
    public function register()
    {
        if (session('id_user')) {
            return redirect()->to(site_url('dashboard'));
        }
        return view('auth/register');
    }

    public function registerProcess()
    {
        if (!$this->validate([
            'nama' => [
                'rules' => 'required',
                'errors' => ['required' => 'Nama UMKM harus diisi']
            ],
            'username' => [
                'rules' => 'required|is_unique[tb_umkm.username]', 
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
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'nama'     => $this->request->getPost('nama'),
            'username' => $this->request->getPost('username'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
            'status'   => 'Aktif', 
        ];

        $this->db->table('tb_umkm')->insert($data);

        return redirect()->to(site_url('login'))->with('success', 'Registrasi Berhasil! Silakan Login.');
    }
}