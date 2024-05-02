<?php
class Perpustakaan
{
    private $books = [];

    public function getAllBook()
    {
        return $this->books;
    }

    public function addBook(ReferenceBook $book)
    {
        $this->books[] = $book;
    }

    public function removeBook($isbn)
    {
        foreach ($this->books as $key => $book) {
            if ($book instanceof ReferenceBook && $book->getISBN() === $isbn) {
                unset($this->books[$key]);
                return true;
            }
        }
        return false;
    }
    
    public function sortBook($kriteria)
    {
        $bukuTerurut = $this->books;

        usort($bukuTerurut, function ($a, $b) use ($kriteria) {
            if ($kriteria === 'author') {
                return strcmp($a->getauthor(), $b->getauthor());
            } elseif ($kriteria === 'tahun') {
                return $a->getyear() - $b->getyear();
            }
            return 0;
        });

        return $bukuTerurut;
    }

    public function searchBook($keyword)
    {
        $hasil = [];

        foreach ($this->books as $book) {
            if (stripos($book->gettitle(), $keyword) !== false || stripos($book->getauthor(), $keyword) !== false) {
                $hasil[] = $book;
            }
        }

        return $hasil;
    }

    public function searchBookByISBN($isbn)
    {
        foreach ($this->books as $key => $book) {
            if ($book instanceof ReferenceBook && $book->getISBN() === $isbn) {
                return $this->books[$key];
            }
        }
        return false;
    }

    public function limitPinjam($peminjam)
    {
        $jumlahPinjaman = 0;

        foreach ($this->books as $book) {
            if ($book->dipinjam() && $book->getPeminjam() === $peminjam) {
                $jumlahPinjaman++;
            }
        }

        if ($jumlahPinjaman >= 5) {
            echo "<script>alert('Tidak dapat meminjam buku karena anda sudah melebihi batas peminjaman');</script>";
            return false;
        }

        return true;
    }

    public function saveSession()
    {
        $_SESSION['perpustakaan'] = $this;
    }
}
