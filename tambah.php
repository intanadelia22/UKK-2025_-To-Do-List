<?php 
// Koneksi ke database MySQL 
// server lokal, root dan password default, db kita.
$koneksi = mysqli_connect("localhost", "root", "", "ukk2025_todolist");

// di db ada 1 table "task" 6 kolom

// TAMBAH TASK
// memastikan add_task berfungsi
if (isset($_POST['add_task'])) {
// mengambil data dengan post(kirim)
    $task = mysqli_real_escape_string($koneksi, $_POST['task']);
    $description = mysqli_real_escape_string($koneksi, $_POST['description']);
    $priority = mysqli_real_escape_string($koneksi, $_POST['priority']);
    $due_date = mysqli_real_escape_string($koneksi, $_POST['due_date']);

    if (!empty($task) && !empty($description) && !empty($priority) && !empty($due_date)) {
    // memastikan bahwa field di atas tidak kosong
        mysqli_query($koneksi, "INSERT INTO task VALUES ('', '$task', '$description', '$priority', '$due_date', '0')");
    // bagian '' kosong krn auto_increment 
        echo "<script>alert('Task Berhasil ditambahkan'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Task Gagal ditambahkan');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id"> 
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Task</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<styl>
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #B0D0E6; 
    }
    .container {
        background: white;
        max-width: 500px;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        margin: auto;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);

    }
    .form-label {
        font-weight: bold;
    }
    .form-control {
        margin-bottom: 10px;
        border-radius: 8px;
    }
</style>
<body>
    <div class="container mt-4">
        <h2 class="text-center">Tambah Task</h2>
        <form action="" method="post" class="">
            <!-- input nama -->
            <label class="form-label">Nama Task</label>
            <input type="text" name="task" class="form-control" placeholder="Masukan Task Baru" autocomplete="off" autofocus required>
            <!-- input deskripsi -->
            <label class="form-label">Deskripsi</label>
            <input type="text" name="description" class="form-control" placeholder="Masukan Deskripsi" autocomplete="off" autofocus required>
            <!-- pilih prioritas -->
            <label class="form-label">Prioritas</label>
            <select name="priority" class="form-control" required>
                <option value="">-- Pilih Prioritas --</option>
                <option value="1">Low</option>
                <option value="2">Medium</option>
                <option value="3">High</option>
            </select>
            <!-- input tanggal -->
            <label class="form-label">Tanggal</label>
            <input type="date" name="due_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" min="<?php echo date('Y-m-d'); ?>" required>
            <div class="d-flex justify-content-center gap-3 mt-3">
                <button type="submit" class="btn btn-primary px-3" name="add_task">Tambah</button>
                <a href="index.php" class="btn btn-secondary px-4">Batal</a>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
