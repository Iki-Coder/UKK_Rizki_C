<?php
class Transaksi {
    private $db;

    public function __construct($koneksi) {
        $this->db = $koneksi;
    }

    public function buatTransaksi($id_pengguna, $tgl_kembali) {
        $tgl_pinjam = date('Y-m-d');

        $this->db->query("
            INSERT INTO transaksi 
            (id_pengguna, tanggal_pinjam, tanggal_kembali, status, kondisi)
            VALUES 
            ('$id_pengguna', '$tgl_pinjam', '$tgl_kembali', 'dipinjam', 'normal')
        ");

        return $this->db->insert_id;
    }

    // tambah detail barang
    public function tambahBarang($id_transaksi, $id_barang, $jumlah) {
        $this->db->query("
            INSERT INTO detail_transaksi 
            VALUES('', '$id_transaksi', '$id_barang', '$jumlah')
        ");

        // update stok
        $this->db->query("
            UPDATE barang 
            SET stok = stok - $jumlah 
            WHERE id='$id_barang'
        ");
    }
}