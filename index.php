<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>

  <?php
  require('connection.php');

  // Tentukan jumlah data per halaman
  $limit = 10; // 10 data per halaman

  // Ambil nomor halaman dari URL, jika tidak ada default ke halaman 1
  $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
  $offset = ($page - 1) * $limit;

  // Ambil data produk untuk dropdown
  $sql_produk = "SELECT * FROM produk";
  $result_produk = $conn->query($sql_produk);

  // Ambil data sales untuk dropdown
  $sql_sales = "SELECT * FROM sales";
  $result_sales = $conn->query($sql_sales);

  // Penanganan pencarian berdasarkan produk, sales, dan bulan
  $search_produk = "";
  $search_sales = "";
  $search_bulan = "";

  $sql = "SELECT leads.*, sales.nama_sales, produk.nama_produk 
          FROM leads 
          JOIN sales ON leads.id_sales = sales.id_sales 
          JOIN produk ON leads.id_produk = produk.id_produk";

  // Check if searching
  if (isset($_POST['search'])) {
      $search_produk = htmlspecialchars($_POST['search_produk']);
      $search_sales = htmlspecialchars($_POST['search_sales']);
      $search_bulan = htmlspecialchars($_POST['search_bulan']);

      $conditions = [];
      if (!empty($search_produk)) {
          $conditions[] = "leads.id_produk = '$search_produk'";
      }
      if (!empty($search_sales)) {
          $conditions[] = "sales.nama_sales LIKE '%$search_sales%'";
      }
      if (!empty($search_bulan)) {
          $conditions[] = "MONTH(leads.tanggal) = '$search_bulan'";
      }

      if (count($conditions) > 0) {
          $sql .= " WHERE " . implode(' AND ', $conditions);
      }
  }

  // Query untuk menghitung total data (dengan filter pencarian jika ada)
  $sql_count = str_replace("SELECT leads.*, sales.nama_sales, produk.nama_produk", "SELECT COUNT(*) as total", $sql);
  $result_count = $conn->query($sql_count);
  $total_data = $result_count->fetch_assoc()['total'];
  $total_pages = ceil($total_data / $limit); // Hitung total halaman

  // Tambahkan ORDER BY, LIMIT dan OFFSET
  $sql .= " ORDER BY leads.id_leads ASC LIMIT $limit OFFSET $offset";

  $result = $conn->query($sql);
  ?>

  <div class="p-5" id="root">
    <h2>Selamat Datang di Tambah Leads</h2>
    <main>
      <div class="card">
        <div class="card-header">
          <button type="button" class="btn btn-success">Kembali</button>
        </div>
        <div class="card-body">
          <form action="simpan.php" method="post">
            <!-- Input Tanggal -->
            <div class="row">
              <div class="col">
                <label class="form-label" for="tanggal">Tanggal</label><br>
                <input class="form-control" type="date" id="tanggal" name="tanggal" required><br><br>
              </div>
              <!-- Input Sales -->
              <div class="col">
                <label class="form-label" for="sales">Sales</label><br>
                <select class="form-select" name="sales" id="sales" required>
                  <option value="">-- Pilih Sales --</option>
                  <?php while ($sales = $result_sales->fetch_assoc()) { ?>
                    <option value="<?= $sales['id_sales'] ?>"><?= $sales['nama_sales'] ?></option>
                  <?php } ?>
                </select><br><br>
              </div>
              <!-- Input Lead Name -->
              <div class="col">
                <label class="form-label" for="leadname">Nama Lead</label><br>
                <input class="form-control" type="text" id="leadname" name="leadname" placeholder="Nama Lead" required></input><br><br>
              </div>
            </div>
            <!-- Input Produk -->
            <div class="row">
              <div class="col">
                <label class="form-label" for="produk">Produk</label><br>
                <select class="form-select" name="produk" id="produk" required>
                  <option value="">-- Pilih Produk --</option>
                  <?php while ($produk = $result_produk->fetch_assoc()) { ?>
                    <option value="<?= $produk['id_produk'] ?>"><?= $produk['nama_produk'] ?></option>
                  <?php } ?>
                </select><br><br>
              </div>
              <!-- Input No. Whatsapp -->
              <div class="col">
                <label class="form-label" for="whatsapp">No. Whatsapp</label><br>
                <input class="form-control" type="text" id="whatsapp" name="whatsapp" placeholder="No. Whatsapp" required></input><br><br>
              </div>
              <!-- Input Kota -->
              <div class="col">
                <label class="form-label" for="kota">Kota</label><br>
                <input class="form-control" type="text" id="kota" name="kota" placeholder="Kota" required></input><br><br>
              </div>
            </div>
            <div class="row justify-content-center">
              <div class="col-2 d-flex m-0">
                <input class="btn btn-primary btn-lg ms-auto" type="submit" value="Simpan">
              </div>
              <div class="col-2 d-flex m-0">
                <input class="btn btn-secondary btn-lg me-auto" type="reset" value="Cancel">
              </div>
            </div>
          </form>
        </div>
      </div>

      <h2>Daftar Leads</h2>

      <!-- Form Pencarian -->
      <form method="post" class="mb-3">
        <div class="row mb-3">
          <div class="col">
            <select class="form-select" name="search_produk">
              <option value="">-- Pilih Produk --</option>
              <?php
              $result_produk->data_seek(0); // Reset pointer ke awal
              while ($produk = $result_produk->fetch_assoc()) { ?>
                  <option value="<?= $produk['id_produk'] ?>" <?= $search_produk == $produk['id_produk'] ? 'selected' : '' ?>>
                      <?= $produk['nama_produk'] ?>
                  </option>
              <?php } ?>
            </select>
          </div>
          <div class="col">
            <select class="form-select" name="search_sales">
              <option value="">-- Pilih Sales --</option>
              <?php
              $result_sales->data_seek(0); // Reset pointer ke awal
              while ($sales = $result_sales->fetch_assoc()) { ?>
                  <option value="<?= $sales['id_sales'] ?>" <?= $search_sales == $sales['id_sales'] ? 'selected' : '' ?>>
                      <?= $sales['nama_sales'] ?>
                  </option>
              <?php } ?>
            </select>
          </div>
          <div class="col">
            <select class="form-select" name="search_bulan">
              <option value="">-- Pilih Bulan --</option>
              <?php for ($i = 1; $i <= 12; $i++) { ?>
                <option value="<?= $i ?>" <?= $search_bulan == $i ? 'selected' : '' ?>><?= date('F', mktime(0, 0, 0, $i, 1)) ?></option>
              <?php } ?>
            </select>
          </div>
          <div class="col-auto">
            <button class="btn btn-outline-secondary" type="submit" name="search">Cari</button>
          </div>
        </div>
      </form>

      <!-- Tabel Data Leads -->
      <table class="table">
        <tr>
          <th>No</th>
          <th>ID Input</th>
          <th>Tanggal</th>
          <th>Sales</th>
          <th>Nama Lead</th>
          <th>Produk</th>
          <th>No. WhatsApp</th>
          <th>Kota</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
          $no = $offset;
          while ($row = $result->fetch_assoc()) {
              $id_leads = str_pad(htmlspecialchars($row['id_leads']), 3, '0', STR_PAD_LEFT);
              echo "<tr>";
              echo "<td>" .  ++$no . "</td>";
              echo "<td>$id_leads</td>";
              echo "<td>" . htmlspecialchars($row['tanggal']) . "</td>";
              echo "<td>" . htmlspecialchars($row['nama_sales']) . "</td>";
              echo "<td>" . htmlspecialchars($row['nama_lead']) . "</td>";
              echo "<td>" . htmlspecialchars($row['nama_produk']) . "</td>";
              echo "<td>" . htmlspecialchars($row['no_wa']) . "</td>";
              echo "<td>" . htmlspecialchars($row['kota']) . "</td>";
              echo "</tr>";
          }
        } else {
          echo "<tr><td colspan='8' class='text-center'>Tidak ada data.</td></tr>";
        }
        ?>
      </table>

      <!-- Navigasi Pagination -->
      <nav aria-label="Page navigation">
        <ul class="pagination">
          <!-- Tombol Previous -->
          <li class="page-item <?= ($page == 1) ? 'disabled' : '' ?>">
            <a class="page-link" href="?page=<?= $page - 1 ?>" aria-label="Previous">
              <span aria-hidden="true">&laquo;</span>
            </a>
          </li>

          <!-- Nomor Halaman -->
          <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
              <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
            </li>
          <?php } ?>

          <!-- Tombol Next -->
          <li class="page-item <?= ($page == $total_pages) ? 'disabled' : '' ?>">
            <a class="page-link" href="?page=<?= $page + 1 ?>" aria-label="Next">
              <span aria-hidden="true">&raquo;</span>
            </a>
          </li>
        </ul>
      </nav>

    </main>
  </div>
</body>

</html>
