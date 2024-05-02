<?php
class ReferenceBook extends Buku
{
    private $isbn;
    private $penerbit;

    public function __construct($title, $author, $year, $isbn, $penerbit)
    {
        parent::__construct($title, $author, $year);
        $this->isbn = $isbn;
        $this->penerbit = $penerbit;
    }

    public function getISBN()
    {
        return $this->isbn;
    }

    public function getPenerbit()
    {
        return $this->penerbit;
    }
}

class Buku
{
    private $title;
    private $author;
    private $year;
    private $peminjam;
    private $dipinjam;
    private $tanggal_kembali;
    private $denda;

    public function __construct($title, $author, $year)
    {
        $this->title = $title;
        $this->author = $author;
        $this->year = $year;
        $this->dipinjam = false;
    }

    public function gettitle()
    {
        return $this->title;
    }

    public function getauthor()
    {
        return $this->author;
    }

    public function getyear()
    {
        return $this->year;
    }

    public function getPeminjam()
    {
        return $this->peminjam;
    }
    public function dipinjam()
    {
        return $this->dipinjam;
    }
    public function getTanggalKembali()
    {
        return $this->tanggal_kembali;
    }
   
    public function pinjamBuku($borrower, $returnDate)
    {
        if (!$this->dipinjam) {
            $this->dipinjam = true;
            $this->peminjam = $borrower;
            $this->tanggal_kembali = $returnDate;
        }
    }
    public function kembalikanBuku()
{
    if ($this->dipinjam) {
        $hari_ini = new DateTime();
        $tanggal_kembali = new DateTime($this->tanggal_kembali);
        if ($tanggal_kembali < $hari_ini) {
            $denda = 5000;
            $this->denda = $denda;
            echo "<script>alert('Buku berhasil dikembalikan. Anda terkena denda sebesar $denda');</script>";
        } else {
            echo "<script>alert('Buku berhasil dikembalikan.');</script>";
        }

        $this->dipinjam = false;
        $this->peminjam = "";
        $this->tanggal_kembali = "";
    }
}

}   


