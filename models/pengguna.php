<?php
class Pengguna {
    private $db;

    public function __construct($koneksi) {
        $this->db = $koneksi;
    }

    public function login($username) {  
        $stmt = $this->db->prepare("SELECT * FROM pengguna WHERE username=?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM pengguna WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getSiswa() {
        return $this->db->query("SELECT * FROM pengguna WHERE role='siswa'");
    }

    public function getByBarcode($barcode) {
        $stmt = $this->db->prepare("
            SELECT * FROM pengguna 
            WHERE barcode=? AND role='siswa'
        ");
        $stmt->bind_param("s", $barcode);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}