<?php
class Barang {
    private $db;

    public function __construct($koneksi) {
        $this->db = $koneksi;
    }

    public function getAll() {
        return $this->db->query("SELECT * FROM barang");
    }

    public function getTotalStok() {
        $result = $this->db->query("SELECT SUM(stok) as total_buku FROM barang");
        $data = $result->fetch_assoc();
        return $data['total_buku'] ?? 0;
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM barang WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function kurangiStok($id, $jumlah) {
        $stmt = $this->db->prepare("UPDATE barang SET stok = stok - ? WHERE id=?");
        $stmt->bind_param("ii", $jumlah, $id);
        return $stmt->execute();
    }
}