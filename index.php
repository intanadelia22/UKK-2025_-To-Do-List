<?php 
// menampilkan error untuk debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// koneksi ke db
$koneksi = mysqli_connect("localhost", "root", "", "ukk2025_todolist");

// cek apakah berhasil
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// TASK SELESAI
// jika ada parameter 'complete' di URL, update status = '1'
if (isset($_GET['complete'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['complete']);
    mysqli_query($koneksi, "UPDATE task SET status = '1' WHERE id = '$id'"); // update status task
    echo "<script>alert('Task berhasil diselesaikan'); window.location.href='index.php';</script>";
    exit; // stop eksekusi
}

// HAPUS TASK
// jika ada parameter 'delete' di URL, hapus task dari db
if (isset($_GET['delete'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['delete']);
    mysqli_query($koneksi, "DELETE FROM task WHERE id = '$id'"); // hapus berdasarkan id
    echo "<script>alert('Task berhasil dihapus'); window.location.href='index.php';</script>";
    exit;
}

// UNDO TASK SELESAI
// jika ada parameter 'undo' di URL, update status = '0'
if (isset($_GET['undo'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['undo']);
    mysqli_query($koneksi, "UPDATE task SET status = '0' WHERE id = '$id'");
    echo "<script>alert('Task dikembalikan ke status belum selesai'); window.location.href='index.php';</script>";
    exit;
}

// FILTER DAN PENCARIAN
// menyiapkan query awal agar mudah + filter
$where = "WHERE 1=1"; 

// jika ada parameter pencarian, tambahkan filter
if (!empty($_GET['search'])) {
    $search = mysqli_real_escape_string($koneksi, $_GET['search']);
    $where .= " AND task LIKE '%$search%'"; // cari task dari kata
}

// jika ada parameter prioritas, tambahkan filter
if (!empty($_GET['priority'])) {
    $priority = mysqli_real_escape_string($koneksi, $_GET['priority']);
    $where .= " AND priority = '$priority'";
}

// maksimal jumlah task 
$max_tasks = 50;

// hitung jumlah task yang belum selesai
$query_count = "SELECT COUNT(*) AS total FROM task WHERE status = '0'";
$result_count = mysqli_query($koneksi, $query_count);
$row_count = mysqli_fetch_assoc($result_count);
$total_tasks = $row_count['total'];

// cek apakah jumlah task sudah mencapai batas maksimal
$disabled = ($total_tasks >= $max_tasks) ? 'disabled' : '';

// TAMBAH TASK BARU
// jika tombol submit ditekan dan task tidak kosong
if (isset($_POST['submit']) && !empty($_POST['task'])) {
    if ($total_tasks < $max_tasks) { // cek jumlah task belum mencapai batas maksimal
        $task = mysqli_real_escape_string($koneksi, $_POST['task']);
        $query_insert = "INSERT INTO task (task, status) VALUES ('$task', '0')"; // tambahkan task dengan status belum selesai
        mysqli_query($koneksi, $query_insert);
        echo "<script>window.location.href='index.php';</script>"; // refresh halaman setelah tambah
        exit;
    } else {
        echo "<script>alert('Maksimal $max_tasks task yang belum selesai!');</script>"; // alert jika batas tercapai
    }
}

// konfigurasi pagination
$tasks_per_page = 5; // jumlah task per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // ambil halaman dari par URL (default: 1)
$offset = ($page - 1) * $tasks_per_page; // hitung OFFSET untuk SQL

// hitung total task sesuai filter
$query_total = "SELECT COUNT(*) AS total FROM task $where";
$result_total = mysqli_query($koneksi, $query_total);
$row_total = mysqli_fetch_assoc($result_total);
$total_tasks = $row_total['total'];

// hitung total halaman
$total_pages = ceil($total_tasks / $tasks_per_page);

// ambil task dengan pagination dan filter
$query = "SELECT * FROM task $where ORDER BY status ASC, priority DESC, due_date ASC LIMIT $tasks_per_page OFFSET $offset";
$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi To Do List (UKK RPL 2025)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <style>
  body {
    font-family: 'Poppins', sans-serif;
    background-color: #B0D0E6;
    padding: 20px;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    }

    .container {
        max-width: 820px;
        background: #FFFFFF;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        margin-top: 20px;
        transition: all 0.3s ease-in-out;
    }

    h2 {
        font-weight: 600;
        color: #343a40;
        text-align: center;
        margin-bottom: 20px;
        letter-spacing: 0.5px;
    }

    .table {
        margin-top: 20px;
        background: white;
        border-radius: 12px;
        overflow: hidden;
        text-align: center;
        border-collapse: collapse;
        width: 100%;
    }

    th, td {
        vertical-align: middle;
        padding: 8px;
        text-align: center;
        border-bottom: 1px solid #dee2e6;
    }

    td, th {
    line-height: 1.2;
    }

    tr {
    height: 40px;
    }

    th {
        background-color: #f8f9fa;
        font-weight: 600;
    }

    td {
        font-size: 10px;
        color: #495057;
    }

    tr:hover {
        background-color: #f1f1f1;
        transition: background 0.3s ease-in-out;
    }

    span {
        font-size: 14px;
        font-weight: 400;
        color: #6c757d;
    }

    </style>
</head>
<body>
    <div class="container">
        <h2>To Do List</h2>
    <?php
        // menghitung jumlah task yang belum selesai
        $query_count = "SELECT COUNT(*) AS total FROM task WHERE status = '0'";
        $result_count = mysqli_query($koneksi, $query_count);
        $row_count = mysqli_fetch_assoc($result_count);
        $total_tasks = $row_count['total'];

        // maksimal jumlah task (misalnya 10)
        $max_tasks = 50;

        // cek apakah jumlah task sudah mencapai batas maksimal
        $disabled = ($total_tasks >= $max_tasks) ? 'disabled' : '';
        ?>

        <p class="text-center">Jumlah task yang belum selesai: <strong><?php echo $total_tasks; ?></strong> / <?php echo $max_tasks; ?></p>

        <!-- Bagian Search -->
        <div class="d-flex justify-content-center">

        <!-- Tombol Tambah Task -->
        <a href="tambah.php" class="btn btn-primary mb-3 me-2 <?php echo $disabled ? 'disabled' : ''; ?>">Tambah Task</a>
        
            <form method="GET" class="d-flex gap-2 mb-3">
                <!-- Input Search -->
                <input type="text" name="search" class="form-control" style="max-width: 250px;" 
                    placeholder="Cari task..." value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">

                <!-- Dropdown Filter Prioritas -->
                <select name="priority" class="form-select form-select-sm" style="width: 140px;" onchange="this.form.submit()">
                    <option value="">Semua</option>
                    <option value="1" <?php if(isset($_GET['priority']) && $_GET['priority'] == "1") echo "selected"; ?>>Low</option>
                    <option value="2" <?php if(isset($_GET['priority']) && $_GET['priority'] == "2") echo "selected"; ?>>Medium</option>
                    <option value="3" <?php if(isset($_GET['priority']) && $_GET['priority'] == "3") echo "selected"; ?>>High</option>
                </select>

                <!-- Tombol Cari -->
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-search"></i></button>
            </form>
        </div>

        <!-- Tabel Daftar Task -->
        <table class="table table-striped text-center">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Task</th>
                    <th>Description</th>
                    <th>Priority</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                    <!-- cek apakah ada data dalam hasil query -->
                <?php
                    if (mysqli_num_rows($result) > 0) {
                    $no = $offset + 1; // menentukan nomor berdasarkan halaman
                    while ($row = mysqli_fetch_assoc($result)) { // looping setiap hasil query
                ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo $row['task']; ?></td>
                    <td><?php echo $row['description']; ?></td>

                    <td>

                    <!-- menampilkan prioritas berdasarkan nilai yang ada di database -->
                        <?php
                        if ($row['priority'] == 1) {
                            echo "Low";
                        } elseif ($row['priority'] == 2) {
                            echo "Medium";
                        } else {
                            echo "High";
                        }
                        ?>
                    </td>
                    <td><?php echo $row['due_date']; ?></td>
                    <td>
                        <?php 
                        if ($row['status'] == 0) {
                            echo "<span style='color: red;'>Belum Selesai</span>"; // jika status 0 belum selesai
                        } else {
                            echo "<span style='color: green;'>Selesai</span>"; // jika status 1 sudah selesai
                        }
                        ?>
                    </td>
                    <td>
                        <!-- aksi jika tugas belum selesai 0-->
                    <?php if ($row['status'] == 0) { ?>   
                        <a href="?complete=<?php echo $row['id']; ?>" class="btn btn-success btn-sm">
                            <i class="fa-solid fa-check"></i>
                        </a>
                        <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-secondary btn-sm">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                        <!-- jika tugas selesai ada undo-->
                        <?php } else { ?> 
                        <a href="?undo=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">
                            <i class="fa-solid fa-rotate-left"></i> Undo
                        </a>
                    <?php } ?>
                        <!-- aksi yang selalu ada-->
                    <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">
                        <i class="fa-solid fa-trash"></i>
                        </a>
                    </td>
                    </tr>
                <?php }  
                    } else { ?>
                        <tr><td colspan="6">Tidak ada task.</td></tr>
                    <?php } ?>
                </tbody>
         </table>
         
        <!-- Pagination -->
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
        <?php
        $search_param = isset($_GET['search']) ? "&search=" . urlencode($_GET['search']) : "";
        $priority_param = isset($_GET['priority']) ? "&priority=" . urlencode($_GET['priority']) : "";
        ?>

        <?php if ($page > 1): ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?= $page - 1 . $search_param . $priority_param; ?>" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
        <?php endif; ?>

        <!-- menampilkan nomor halaman berdasarkan total halaman -->
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?= ($i == $page) ? 'active' : ''; ?>">
                <a class="page-link" href="?page=<?= $i . $search_param . $priority_param; ?>"><?= $i; ?></a>
            </li>
        <?php endfor; ?>

        <?php if ($page < $total_pages): ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?= $page + 1 . $search_param . $priority_param; ?>" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        <?php endif; ?>
            </ul>
        </nav>

        
     </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
