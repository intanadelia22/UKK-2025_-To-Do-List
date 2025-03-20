<?php 
// Koneksi ke database
$koneksi = mysqli_connect("localhost", "root", "", "ukk2025_todolist");

// Periksa koneksi
if (!$koneksi) {
    die("<script>alert('Koneksi database gagal: " . mysqli_connect_error() . "'); window.location='index.php';</script>");
}

// Simpan data yang akan diedit
$editData = null;

// Cek apakah ada parameter 'id' di URL dan ambil data
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['id']);
    $query = mysqli_query($koneksi, "SELECT * FROM task WHERE id = '$id'");

    if (mysqli_num_rows($query) > 0) {
        $editData = mysqli_fetch_assoc($query);
    } else {
        echo "<script>alert('Task tidak ditemukan!'); window.location='index.php';</script>";
        exit;
    }
} else {
    echo "<script>alert('ID tidak valid!'); window.location='index.php';</script>";
    exit;
}

// PROSES UPDATE (EDIT)
if (isset($_POST['update'])) {
    if (!isset($_POST['id']) || empty($_POST['id'])) {
        echo "<script>alert('ID tidak valid!');</script>";
    } else {
        // Mengamankan inputan dari pengguna
        $id = mysqli_real_escape_string($koneksi, $_POST['id']);
        $task = mysqli_real_escape_string($koneksi, $_POST['task']);
        $description = mysqli_real_escape_string($koneksi, $_POST['description']);
        $priority = mysqli_real_escape_string($koneksi, $_POST['priority']);
        $due_date = mysqli_real_escape_string($koneksi, $_POST['due_date']);

        // Memastikan seluruh field terisi
        if (!empty($task) && !empty($description) && !empty($priority) && !empty($due_date)) {
            $sql = "UPDATE task SET task='$task', description='$description', priority='$priority', due_date='$due_date' WHERE id='$id'";
            $update = mysqli_query($koneksi, $sql);

            if ($update) {
                echo "<script>alert('Task berhasil diperbarui!'); window.location='index.php';</script>";
                exit;
            } else {
                echo "<script>alert('Gagal memperbarui task! Error: " . mysqli_error($koneksi) . "');</script>";
            }
        } else {
            echo "<script>alert('Harap isi semua data!');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #B0D0E6;
        }
        .container {
            width: 40%;
            margin: 50px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            font-weight: bold;
        }
        .form-control {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .btn-container {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        .btn {
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .btn-primary {
            background-color: #007bff;
            color: white;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        .btn-secondary:hover {
            background-color: #565e64;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Task</h2>
        <?php if ($editData): ?> <!-- Cek apakah data edit ada -->
        <form method="POST">
            <input type="hidden" name="id" value="<?php echo $editData['id']; ?>">

            <div class="form-group">
                <label>Task</label>
                <input type="text" class="form-control" name="task" value="<?php echo htmlspecialchars($editData['task']); ?>" required>
            </div>

            <div class="form-group">
                <label>Deskripsi</label>
                <textarea class="form-control" name="description" rows="3" required><?php echo htmlspecialchars($editData['description']); ?></textarea>
            </div>

            <div class="form-group">
                <label>Priority</label>
                <select class="form-control" name="priority">
                    <option value="1" <?php if ($editData['priority'] == 1) echo 'selected'; ?>>Low</option>
                    <option value="2" <?php if ($editData['priority'] == 2) echo 'selected'; ?>>Medium</option>
                    <option value="3" <?php if ($editData['priority'] == 3) echo 'selected'; ?>>High</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Tanggal</label>
                <input type="date" name="due_date" class="form-control" value="<?php echo $editData['due_date']; ?>" min="<?php echo date('Y-m-d'); ?>" required>
            </div>

            <div class="d-flex justify-content-center gap-3 mt-3">
                <button type="submit" class="btn btn-primary px-3" name="update">Update Task</button>
                <a href="index.php" class="btn btn-secondary px-4">Batal</a>
            </div>
        </form>
        <?php else: ?>
            <p class="text-danger text-center">Data tidak ditemukan.</p>
        <?php endif; ?>
    </div>
</body>
</html>
