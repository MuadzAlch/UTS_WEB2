//Muhammad Muadz Alchairi
//21552011370

<?php
require_once "Library.php";
require_once "Book.php";
session_start();

if (!isset($_SESSION['perpustakaan'])) {
    $_SESSION['perpustakaan'] = new Perpustakaan();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['addBook'])) {
        $isbn = $_POST['isbn'];
        $title = $_POST['title'];
        $author = $_POST['author'];
        $penerbit = $_POST['penerbit'];
        $tahun = $_POST['tahun'];

        $newBook = new ReferenceBook($title, $author, $tahun, $isbn, $penerbit);
        $_SESSION['perpustakaan']->addBook($newBook);
    }
    if (isset($_POST['removeBook'])) {

        if (isset($_POST['isbn'])) {

            $isbn = $_POST['isbn'];
            if (isset($_SESSION['perpustakaan'])) {
                $_SESSION['perpustakaan']->removeBook($isbn);
            }
        }
    }

    if (isset($_POST['pinjamBuku'])) {
        $isbn = $_POST['isbn'];
        $peminjam = $_POST['peminjam'];
        $tanggal_kembali = $_POST['tanggal'];

        if ($_SESSION['perpustakaan']->limitPinjam($peminjam)) {
            $book = $_SESSION['perpustakaan']->searchBookByISBN($isbn);

            if ($book) {
                $book->pinjamBuku($peminjam, $tanggal_kembali);
                $_SESSION['perpustakaan']->saveSession();
            }
        }
    }

    if (isset($_POST['kembalikanBuku'])) {
        $isbn = $_POST['isbn'];

        $book = $_SESSION['perpustakaan']->searchBookByISBN($isbn);

        if ($book) {
            $book->kembalikanBuku();
            $_SESSION['perpustakaan']->saveSession();
        } else {
            echo "<script>alert('Silakan pinjam buku terlebih dahulu');</script>";
        }
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Perpustakaan Mymumu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
            color: #212529;
        }

        .navbar {
            background-color: #343a40;
        }

        .card-book {
            width: 200px;
        }

        .modal-content {
            background-color: #f8f9fa;
            color: #212529;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }

        .list-group-item {
            background-color: #fff;
            border-color: #dee2e6;
            margin-bottom: 10px;
            border-radius: 10px;
        }

        .list-group-item:hover {
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
            transition: all 0.3s ease;
        }


        .card-header {
            background-color: #007bff;
            color: #fff;
        }

        .card-body {
            background-color: #fff;
            border: 1px solid rgba(0, 0, 0, 0.125);
            border-radius: 0.25rem;
        }

        .form-control {
            border-radius: 10px;
        }

        .container {
            margin-top: 20px;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .book-container {
            margin-top: 20px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .btn-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand text-black" href="#">
                Perpustakaan Mymumu
            </a>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="ml-auto">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Cari buku" name="keyword"
                        aria-label="Cari buku" aria-describedby="button-addon2">
                    <button class="btn btn-outline-light btn-primary" type="submit" id="button-addon2">Cari</button>
                </div>
            </form>
        </div>
    </nav>
    <div class="modal fade" id="modalPinjam" tabindex="-1" aria-labelledby="modalLabelPinjam" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalLabelPinjam">Isi Form Peminjaman</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                    <div class="modal-body">
                        <input type="hidden" class="form-control" id="pinjamISBN" name="isbn" required>
                        <div class="mb-3">
                            <label for="modalPeminjam" class="form-label">Nama Peminjam</label>
                            <input type="text" class="form-control" name="peminjam" id="modalPeminjam" required>
                        </div>
                        <div class="input-group date mb-3" id="datepicker">
                            <input type="date" class="form-control" name="tanggal" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tidak</button>
                        <button type="submit" name="pinjamBuku" class="btn btn-primary">Ya</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalHapus" tabindex="-1" aria-labelledby="modalLabelHapus" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalLabelHapus">Hapus Buku</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                    <div class="modal-body">
                        <p>Apakah anda yakin ingin menghapus buku ini?</p>
                        <input type="hidden" name="isbn" id="hapusISBN" value="" />
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tidak</button>
                        <button type="submit" name="removeBook" class="btn btn-primary">Ya</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="mx-auto px-5 my-3 d-flex justify-content-start align-items-end">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <div class="mb-3">
                    <label for="sort" class="form-label">Sortir Berdasarkan</label>
                    <div class="d-flex gap-2"><select class="form-select" aria-label="Sortir Buku" id="sort"
                            name="sort">
                            <option selected value="author">Penulis</option>
                            <option value="tahun">Tahun</option>
                        </select>
                        <button type="submit" name="apply_sort" class="btn btn-primary"><i
                                class="bi bi-filter"></i></button>
                    </div>
                </div>
            </form>
        </div>

        <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card my-3">
                    <div class="card-header">
                        Daftar Buku
                    </div>

        <div class="list-group">
            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['apply_sort'])) {
                $sortCriteria = $_POST['sort'];

                $sortedBooks = $_SESSION['perpustakaan']->sortBook($sortCriteria);

                foreach ($sortedBooks as $book) {
                    if (!$book->dipinjam()) {
                        echo "<div class='list-group-item'>";
                        echo "<div class='d-flex'>";
                        echo "<div class='left text-start flex-grow-1'>";
                        echo "<div class='card-body'>";
                        echo "<h5 class='card-title'>" . $book->gettitle() . "</h5>";
                        echo "<h6 class='card-subtitle mb-2 text-body-secondary'>" . $book->getauthor() . " - " . $book->getyear() . "</h6>";
                        echo "<h6 class='card-subtitle mb-2 text-body-secondary'>" . $book->getPenerbit() . "</h6>";
                        echo "</div>";
                        echo "</div>";
                        echo "<div class='right d-flex gap-2 align-items-center'>";
                        echo "<a type='button' class='btn btn-primary btn-pinjam' data-bs-toggle='modal' data-bs-target='#modalPinjam' data-isbn='" . $book->getISBN() . "'><i class='bi bi-bookmark-plus'></i> Pinjam</a>";
                        echo "<a type='button' class='btn btn-danger btn-hapus' data-bs-toggle='modal' data-bs-target='#modalHapus' data-isbn='" . $book->getISBN() . "'><i class='bi bi-file-x'></i>Hapus</a>";
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";
                    }
                }
            } elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['keyword'])) {
                $keyword = $_POST['keyword'];
                $searchResults = $_SESSION['perpustakaan']->searchBook($keyword);
                if (sizeof($searchResults) > 0) {
                    foreach ($searchResults as $book) {
                        if (!$book->dipinjam()) {
                            echo "<div class='list-group-item'>";
                            echo "<div class='d-flex'>";
                            echo "<div class='left text-start flex-grow-1'>";
                            echo "<div class='card-body'>";
                            echo "<h5 class='card-title'>" . $book->gettitle() . "</h5>";
                            echo "<h6 class='card-subtitle mb-2 text-body-secondary'>" . $book->getauthor() . " - " . $book->getyear() . "</h6>";
                            echo "<h6 class='card-subtitle mb-2 text-body-secondary'>" . $book->getPenerbit() . "</h6>";
                            echo "</div>";
                            echo "</div>";
                            echo "<div class='right d-flex gap-2 align-items-center'>";
                            echo "<a type='button' class='btn btn-primary btn-pinjam' data-bs-toggle='modal' data-bs-target='#modalPinjam' data-isbn='" . $book->getISBN() . "'><i class='bi bi-bookmark-plus'></i> Pinjam</a>";
                            echo "<a type='button' class='btn btn-danger btn-hapus' data-bs-toggle='modal' data-bs-target='#modalHapus' data-isbn='" . $book->getISBN() . "'><i class='bi bi-file-x'></i>Hapus</a>";
                            echo "</div>";
                            echo "</div>";
                            echo "</div>";
                        }
                    }
                } else {
                    echo "<p>Tidak ada buku dengan title dan author $keyword</p>";
                }
            } else {
                foreach ($_SESSION['perpustakaan']->getAllBook() as $book) {
                    if (!$book->dipinjam()) {
                        echo "<div class='list-group-item'>";
                        echo "<div class='d-flex'>";
                        echo "<div class='left text-start flex-grow-1'>";
                        echo "<div class='card-body'>";
                        echo "<h5 class='card-title'>" . $book->gettitle() . "</h5>";
                        echo "<h6 class='card-subtitle mb-2 text-body-secondary'>" . $book->getauthor() . " - " . $book->getyear() . "</h6>";
                        echo "<h6 class='card-subtitle mb-2 text-body-secondary'>" . $book->getPenerbit() . "</h6>";
                        echo "</div>";
                        echo "</div>";
                        echo "<div class='right d-flex gap-2 align-items-center'>";
                        echo "<a type='button' class='btn btn-primary btn-pinjam' data-bs-toggle='modal' data-bs-target='#modalPinjam' data-isbn='" . $book->getISBN() . "'><i class='bi bi-bookmark-plus'></i> Pinjam</a>";
                        echo "<a type='button' class='btn btn-danger btn-hapus' data-bs-toggle='modal' data-bs-target='#modalHapus' data-isbn='" . $book->getISBN() . "'><i class='bi bi-file-x'></i> Hapus</a>";
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";
                    }
                }
            }
            ?>

        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card my-3">
                    <div class="card-header">
                        Tambahkan Buku
                    </div>
                    <div class="card-body">
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                            <div class="mb-3">
                                <label for="inISBN" class="form-label">ISBN</label>
                                <input type="text" class="form-control" id="inISBN" name="isbn" required>
                            </div>
                            <div class="mb-3">
                                <label for="intitle" class="form-label">Judul Buku</label>
                                <input type="text" class="form-control" id="intitle" name="title" required>
                            </div>
                            <div class="mb-3">
                                <label for="inauthor" class="form-label">Penulis</label>
                                <input type="text" class="form-control" id="inauthor" name="author" required>
                            </div>
                            <div class="mb-3">
                                <label for="inPenerbit" class="form-label">Penerbit</label>
                                <input type="text" class="form-control" id="inPenerbit" name="penerbit" required>
                            </div>
                            <div class="mb-3">
                                <label for="inTahun" class="form-label">Tahun</label>
                                <input type="number" min="1900" max="2099" step="1" class="form-control" id="inTahun"
                                    name="tahun" required>
                            </div>
                            <button type="submit" name="addBook" class="btn btn-primary">Tambah Buku<i
                                    class="bi bi-plus"></i></button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="col-md-12">
                    <div class="card my-3">
                        <div class="card-header">
                            Kembalikan Buku
                        </div>
                        <div class="card-body">
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                                <label for="kembaliISBN">Buku</label>
                                <select class="form-select mb-3" aria-label="Default select example" name="isbn"
                                    id="kembaliISBN" required>
                                    <?php
                                    $counter = 0;

                                    foreach ($_SESSION['perpustakaan']->getAllBook() as $book) {
                                        if ($book->dipinjam()) {
                                            echo "<option value='" . $book->getISBN() . "'>" . $book->gettitle() . "</option>";
                                        } else {
                                            $counter++;
                                        }
                                    }
                                    if ($counter === sizeof($_SESSION['perpustakaan']->getAllBook())) {
                                        echo "<option value='kosong'>Tidak ada buku yang sedang dipinjam</option>";
                                    } ?>
                                </select>
                                <button type="submit" name="kembalikanBuku" class="btn btn-success"><i
                                        class="bi bi-save"></i> Kembalikan Buku</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
            crossorigin="anonymous"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script>
            $(document).on("click", ".btn-hapus", function () {
                var isbn = $(this).data('isbn');
                $(".modal-body #hapusISBN").val(isbn);
            });
            $(document).on("click", ".btn-pinjam", function () {
                var isbn = $(this).data('isbn');
                $(".modal-body #pinjamISBN").val(isbn);
            });
        </script>
</body>
<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <p>Hak Cipta &copy; 2024 Muadz Alchairi</p>
            </div>
            <div class="col-md-6">
                <p class="text-md-end">Hubungi kami: muadzchairi04@gmail.com</p>
            </div>
        </div>
    </div>
</footer>
</html>