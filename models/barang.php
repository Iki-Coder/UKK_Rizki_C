<?php
class Barang {
    private $db;

    public function __construct($koneksi) {
        $this->db = $koneksi;
    }

    public function getAll() {
        return $this->db->query("SELECT * FROM barang");
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM barang WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getStok($id) {
        $stmt = $this->db->prepare("SELECT stok FROM barang WHERE id=?");
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