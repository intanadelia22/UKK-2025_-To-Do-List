<?php 
$koneksi = mysqli_connect("localhost", "root", "", "ukk2025_todolist");

// TAMBAH TASK
if (isset($_POST['add_task'])) {
    $task = $_POST['task'];
    $priority = $_POST['priority'];
    $due_date = $_POST['due_date'];

    if (!empty($task) && !empty($priority) && !empty($due_date)) {
        mysqli_query($koneksi, "INSERT INTO task VALUES ('', '$task', '$priority', '$due_date', '0')");

        echo "<script>alert('Task Berhasil ditambahkan')</script>";
    } else {
        echo "<script>alert('Task Gagal ditambahkan')</script>";
        header("location : index.php");   
    }
}

// TASK SELESAI
if (isset($_GET['complete'])) {
    $id = $_GET['complete'];
    mysqli_query($koneksi, "UPDATE task SET status = '1' WHERE id = '$id'");
    echo "<script>alert('Task berhasil diselesaikan')</script>";
    header("location: index.php");  //HALAMAN REFRESH
}

// HAPUS TASK
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($koneksi, "DELETE FROM task WHERE id = '$id'");
    echo "<script>alert('Task berhasil dihapus')</script>";
    header("location: index.php");
}

// MENAMPILKAN TASK
$result = mysqli_query($koneksi, "SELECT * FROM task ORDER BY status ASC, priority DESC, due_date ASC");
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi To Do List (UKK RPL 2025)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<style>
    body {
    font-family: 'Poppins', sans-serif;
    background-color: #f8f9fa;
    padding: 20px;
}

.container {
    max-width: 700px;
    background: white;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    margin-top: 40px;
}

h2 {
    font-weight: bold;
    color: #343a40;
    text-align: center;
    margin-bottom: 20px;
}

form {
    margin-bottom: 25px;
}

.form-label {
    font-weight: 500;
    margin-bottom: 5px;
}

input, select {
    margin-bottom: 15px;
}

.table {
    margin-top: 20px;
    background: white;
    border-radius: 10px;
    overflow: hidden;
    text-align: center;
}

th, td {
    vertical-align: middle;
    padding: 12px;
    text-align: center;
}

span {
    font-size: 14px;
    font-weight: normal;
}
</style>

<body>
    <div class="container mt-2">
        <h2 class="text-center">Aplikasi To Do List</h2>
        <form action="" method="post" class="border rounded bg-light p-2">
            <label class="form-label">Nama Task</label>
            <input type="text" name="task" class="form-control" placeholder="Masukan Task Baru" autocomplete="off" autofocus required>
            <label class="form-label">Prioritas</label>
            <select name="priority" class="form-control" required>
                <option value="">--Pilih Prioritas--</option>
                <option value="1">Low</option>
                <option value="2">Medium</option>
                <option value="3">High</option>
            </select>
            <label class="form-label">Tanggal</label>
            <input type="date" name="due_date" class="form-control" value="<?php echo date('Y-m-d') ?>" required>
            <button class="btn btn-primary w-100 mt-2" name="add_task">Tambah</button>
</form>
    <table class="table table-striped text-center">
        <thead>
            <tr>
                <th>No</th>
                <th>Task</th>
                <th>Priority</th>
                <th>Tanggal</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
                <?php
            if (mysqli_num_rows($result) > 0) { 
                $no = 1;
                while ($row = mysqli_fetch_assoc($result)) {
                ?>
                <tr>
                    <td><?php echo $no++ ?></td>
                    <td><?php echo $row['task'] ?></td>
                    <td><?php
                    if ($row['priority'] == 1) {
                        echo "Low";
                    } elseif ($row['priority'] == 2) {
                        echo "Medium";
                    } else {
                        echo "High";
                    }?></td>
                    <td><?php echo $row['due_date'] ?></td>
                    <td><?php 
                    if ($row['status'] == 0) {
                        echo "<span style='color: red;'>Belum Selesai</span>" ;
                    }else {
                        echo "<span style='color: green;'>Selesai</span>" ;
                    }
                    ?></td>
                <td>
                    <?php if ($row['status'] == 0) { ?>
                        <a href="?complete=<?php echo $row['id'] ?>" class="btn btn-success btn-sm" <i class="fas fa-check">Selesai</i></a>
                    <?php } ?>
                        <a href="?delete=<?php echo $row['id'] ?>" class="btn btn-danger btn-sm" <i class="fas fa-trash">Hapus</i></a>
                </td>
                </tr>
            <?php }
            }
            ?>
        </tbody>
    </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" rel="stylesheet"></script>
</body>

</html>

