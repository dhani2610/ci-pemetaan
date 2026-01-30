<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDetailsTables extends Migration
{
    public function up()
    {
        // 1. Tabel tb_operasional (Relasi Baru)
        $this->forge->addField([
            'id_operasional' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_outlet' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'nama_hari' => [
                'type'       => 'VARCHAR', // Senin, Selasa, dst
                'constraint' => '20',
            ],
            'jam_buka' => [
                'type' => 'TIME',
            ],
            'jam_tutup' => [
                'type' => 'TIME',
            ],
        ]);
        $this->forge->addKey('id_operasional', true);
        $this->forge->addForeignKey('id_outlet', 'tb_outlet', 'id_outlet', 'CASCADE', 'CASCADE');
        $this->forge->createTable('tb_operasional');

        // 2. Tabel tb_produk
        $this->forge->addField([
            'id_produk' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_outlet' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'nama' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'jenis' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'harga' => [
                'type'       => 'BIGINT', // Menggunakan BIGINT untuk harga
                'constraint' => 20,
            ],
            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',
            'updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
        ]);
        $this->forge->addKey('id_produk', true);
        $this->forge->addForeignKey('id_outlet', 'tb_outlet', 'id_outlet', 'CASCADE', 'CASCADE');
        $this->forge->createTable('tb_produk');
    }

    public function down()
    {
        $this->forge->dropTable('tb_produk');
        $this->forge->dropTable('tb_operasional');
    }
}