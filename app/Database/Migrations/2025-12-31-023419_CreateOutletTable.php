<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOutletTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_outlet' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_umkm' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'id_admin' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true, // Bisa null jika belum divalidasi admin
            ],
            'nama' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'jenis' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'alamat' => [
                'type' => 'TEXT',
            ],
            // operasional dihapus, diganti relasi tabel baru di bawah
            'area' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'kategori' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'deskripsi' => [
                'type' => 'TEXT',
            ],
            'kontak' => [
                'type' => 'TEXT',
            ],
            'longitude' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'latitude' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'foto' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'validasi' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
            ],
            // REQUEST BARU: STATUS
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'default'    => 'Aktif',
            ],
            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',
            'updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
        ]);

        $this->forge->addKey('id_outlet', true);
        
        // Foreign Keys
        $this->forge->addForeignKey('id_umkm', 'tb_umkm', 'id_umkm', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_admin', 'tb_admin', 'id_admin', 'CASCADE', 'SET NULL');

        $this->forge->createTable('tb_outlet');
    }

    public function down()
    {
        $this->forge->dropTable('tb_outlet');
    }
}