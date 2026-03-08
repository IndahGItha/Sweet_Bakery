<?php

namespace SweetBakery\Models;

use SweetBakery\Utils\Database;

class Kategori
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function readAll()
    {
        $sql = "SELECT * FROM kategori ORDER BY nama_kategori ASC";
        $result = $this->db->query($sql);

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        return $data;
    }
}