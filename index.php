<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Leads</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>

<?php
require('connection.php');

// Proses data saat formulir disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $tanggal = htmlspecialchars($_POST['tanggal']);
    $sales = htmlspecialchars($_POST['sales']);
    $leadname = htmlspecialchars($_POST['leadname']);
    $produk = htmlspecialchars($_POST['produk']);
    $whatsapp = htmlspecialchars($_POST['whatsapp']);
    $kota = htmlspecialchars($_POST['kota']);

    // Simpan data ke database
    $sql = "INSERT INTO leads (tanggal, id_sales, nama_lead, id_produk, no_wa, kota) VALUES ('$tanggal', '$sales', '$leadname', '$produk', '$whatsapp', '$kota')";
    if ($conn->query($sql) === TRUE) {
        $message = "Data berhasil disimpan.";
    } else {
        $message = "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Ambil semua data dari tabel leads
$sql_select = "SELECT * FROM leads";
$result = $conn->query($sql_select);
?>

<div class="p-5" id="root">
    <h2>Selamat Datang di Tambah Leads</h2>
    <main>
        <div class="card">
            <div class="card-header">
                <button type="button" class="btn btn-success">Kembali</button>
            </div>
            <div class="card-body">
                <form action="" method="post">
                    <!-- Input Tanggal -->
                    <div class="row">
                        <div class="col">
                            <label class="form-label" for="tanggal">Tanggal</label>
                            <input class="form-control" type="date" id="tanggal" name="tanggal" required>
                        </div>
                        <!-- Input Sales -->
                        <div class="col">
                            <label class="form-label" for="sales">Sales</label>
                            <select class="form-select" name="sales" id="sales" required>
                                <option value="">-- Pilih Sales --</option>
                                <option value="1">Sales 1</option>
                                <option value="2">Sales 2</option>
                                <option value="3">Sales 3</option>
                            </select>
                        </div>
                        <!-- Input Lead Name -->
                        <div class="col">
                            <label class="form-label" for="leadname">Nama Lead</label>
                            <input class="form-control" type="text" id="leadname" name="leadname" placeholder="Nama Lead" required>
                        </div>
                    </div>
                    <!-- Input Produk -->
                    <div class="row">
                        <div class="col">
                            <label class="form-label" for="produk">Produk</label>
                            <select class="form-select" name="produk" id="produk" required>
                                <option value="">-- Pilih Produk --</option>
                                <option value="1">Cipta Residence 2</option>
                                <option value="2">The Rich</option>
                                <option value="3">Namorambe City</option>
                                <option value="4">Grand Banten</option>
                                <option value="5">Turi Mansion</option>
                                <option value="6">Cipta Residence 1</option>
                            </select>
                        </div>
                        <!-- Input No. Whatsapp -->
                        <div class="col">
                            <label class="form-label" for="whatsapp">No. Whatsapp</label>
                            <input class="form-control" type="text" id="whatsapp" name="whatsapp" placeholder="No. Whatsapp" required>
                        </div>
                        <!-- Input Kota -->
                        <div class="col">
                            <label class="form-label" for="kota">Kota</label>
                            <input class="form-control" type="text" id="kota" name="kota" placeholder="Kota" required>
                        </div>
                    </div>
                    <div class="row justify-content-center mt-3">
                        <div class="col-2 d-flex m-0">
                            <input class="btn btn-primary btn-lg ms-auto" type="submit" value="Simpan">
                        </div>
                        <div class="col-2 d-flex m-0">
                            <input class="btn btn-secondary btn-lg me-auto" type="reset" value="Cancel">
                        </div> 
                    </div>
                </form>
                <?php if (isset($message)) echo "<div class='alert alert-info mt-3'>$message</div>"; ?>
            </div>
        </div>
        
        <!-- Tampilkan Data Leads -->
        <div class="mt-4">
            <h2>Data Leads</h2>
            <?php if ($result->num_rows > 0) { ?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Sales</th>
                            <th>Nama Lead</th>
                            <th>Produk</th>
                            <th>No. Whatsapp</th>
                            <th>Kota</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    // Fetch data per baris
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['tanggal']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['id_sales']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['nama_lead']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['id_produk']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['no_wa']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['kota']) . "</td>";
                        echo "</tr>";
                    }
                    ?>
                    </tbody>
                </table>
            <?php } else { ?>
                <div class="alert alert-warning">Tidak ada data.</div>
            <?php } ?>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>
</html>
