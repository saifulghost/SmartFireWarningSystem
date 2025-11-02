<?php
include 'db_connect.php';
$jabatan_list = array('JTMK', 'JKE', 'JKA', 'JKM', 'JMSK', 'JABATAN LAIN');

// Tambah user baru
if (isset($_POST['add_user'])) {
  $name = $_POST['name'];
  $email = $_POST['email'];
  $ic = $_POST['ic'];
  $jawatan = $_POST['jawatan'];
  $jabatan = $_POST['jabatan'];
  if ($jabatan == 'JABATAN LAIN' && isset($_POST['jabatan_lain'])) {
    $jabatan = $_POST['jabatan_lain'];
  }

  $sql = "INSERT INTO users (ic, name, email, jawatan, jabatan) 
          VALUES ('$ic', '$name', '$email', '$jawatan', '$jabatan')";
  mysqli_query($conn, $sql);
  header("Location: users.php");
  exit();
}

// Kemaskini user
if (isset($_POST['update_user'])) {
  $ic = $_POST['edit_ic'];
  $name = $_POST['edit_name'];
  $email = $_POST['edit_email'];
  $jawatan = $_POST['edit_jawatan'];
  $jabatan = $_POST['edit_jabatan'];
  mysqli_query($conn, "UPDATE users SET name='$name', email='$email', jawatan='$jawatan', jabatan='$jabatan' WHERE ic='$ic'");
  header("Location: users.php");
  exit();
}

// Padam user
if (isset($_GET['delete'])) {
  $ic = $_GET['delete'];
  mysqli_query($conn, "DELETE FROM users WHERE ic='$ic'");
  header("Location: users.php");
  exit();
}

// Ambil semua user
$result = mysqli_query($conn, "SELECT * FROM users ORDER BY ic ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Users - Smart Fire Alarm System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
  <style>
    body {
      background: #fafafa;
      font-family: 'Poppins', sans-serif;
    }
    .navbar {
      background: linear-gradient(90deg, #ff9800, #e53935);
    }
    .navbar-brand {
      font-weight: 700;
      color: #fff !important;
    }
    .nav-link {
      color: #fff !important;
    }
    .table-container {
      margin-top: 30px;
      background: #fff;
      padding: 20px;
      border-radius: 15px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.08);
    }
    table thead {
      background: #e53935;
      color: #fff;
    }
    table tbody tr:hover {
      background: rgba(255, 152, 0, 0.1);
    }
    .badge-jabatan {
      background: #6f42c1;
      color: white;
    }
    .btn-action {
      margin-right: 5px;
    }
  </style>
</head>
<body>

  <!-- ✅ Navbar asal dikekalkan -->
  <nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
      <a class="navbar-brand" href="loginpage.php">🔥 Smart Fire Alarm</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
              data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" 
              aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="labs.php">Labs</a></li>
          <li class="nav-item"><a class="nav-link" href="alerts.php">Alerts</a></li>
          <li class="nav-item"><a class="nav-link" href="live.php">Live</a></li>
          <li class="nav-item"><a class="nav-link" href="history.php">History</a></li>
          <li class="nav-item"><a class="nav-link active" href="users.php">Users</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Table Users -->
  <div class="container table-container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4 class="text-dark"><i class="fa-solid fa-users"></i> User Management</h4>
      <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#addUserModal">
        <i class="fa-solid fa-user-plus"></i> Add New User
      </button>
    </div>
    <div class="table-responsive">
      <table class="table table-bordered align-middle">
        <thead>
          <tr>
            <th>No. IC</th>
            <th>Nama</th>
            <th>Email</th>
            <th>Jawatan</th>
            <th>Jabatan</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php while($row = mysqli_fetch_assoc($result)) { ?>
          <tr>
            <td><?= $row['ic']; ?></td>
            <td><?= $row['name']; ?></td>
            <td><?= $row['email']; ?></td>
            <td><?= $row['jawatan']; ?></td>
            <td><span class="badge badge-jabatan"><?= $row['jabatan']; ?></span></td>
            <td>
              <!-- Butang Edit -->
              <button class="btn btn-sm btn-primary btn-action"
                      data-bs-toggle="modal"
                      data-bs-target="#editUserModal"
                      data-ic="<?= $row['ic']; ?>"
                      data-name="<?= $row['name']; ?>"
                      data-email="<?= $row['email']; ?>"
                      data-jawatan="<?= $row['jawatan']; ?>"
                      data-jabatan="<?= $row['jabatan']; ?>">
                <i class="fa-solid fa-pen"></i> Edit
              </button>

              <!-- Butang Remove -->
              <a href="users.php?delete=<?= $row['ic']; ?>"
                 class="btn btn-sm btn-danger btn-action"
                 onclick="return confirm('Padam pengguna ini?');">
                 <i class="fa-solid fa-trash"></i> Remove
              </a>
            </td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Modal Add User -->
  <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <form method="POST" class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addUserModalLabel">Add new user</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">No. Kad Pengenalan (IC)</label>
            <input type="text" class="form-control" name="ic" placeholder="Contoh: 010203045678" required />
          </div>
          <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" class="form-control" name="name" required />
          </div>
          <div class="mb-3">
            <label class="form-label">E-mel</label>
            <input type="email" class="form-control" name="email" required />
          </div>
          <div class="mb-3">
            <label class="form-label">Jawatan</label>
            <select class="form-select" name="jawatan" required>
              <option disabled selected>Pilih Jawatan</option>
              <option>Ketua Jabatan</option>
              <option>P.I.C</option>
              <option>Pensyarah</option>
              <option>Staff</option>
              <option>Admin</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Jabatan</label>
            <select class="form-select" name="jabatan" id="jabatanSelect" required>
              <option disabled selected>Pilih Jabatan</option>
              <?php foreach($jabatan_list as $jabatan): ?>
                <option value="<?= $jabatan ?>"><?= $jabatan ?></option>
              <?php endforeach; ?>
            </select>
            <div class="jabatan-lain-container" id="jabatanLainContainer" style="display:none;">
              <label class="form-label">Sila nyatakan jabatan:</label>
              <input type="text" class="form-control" name="jabatan_lain" id="jabatanLainInput">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" name="add_user" class="btn btn-danger">Simpan</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal Edit User -->
  <div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <form method="POST" class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="edit_ic" id="edit_ic">
          <div class="mb-3">
            <label class="form-label">Nama</label>
            <input type="text" class="form-control" name="edit_name" id="edit_name" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="edit_email" id="edit_email" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Jawatan</label>
            <select class="form-select" name="edit_jawatan" id="edit_jawatan" required>
              <option>Ketua Jabatan</option>
              <option>P.I.C</option>
              <option>Pensyarah</option>
              <option>Staff</option>
              <option>Admin</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Jabatan</label>
            <input type="text" class="form-control" name="edit_jabatan" id="edit_jabatan" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" name="update_user" class="btn btn-danger">Simpan</button>
        </div>
      </form>
    </div>
  </div>

  <footer class="text-center mt-4 mb-3 text-muted">
    © 2025 Smart Fire Alarm System. All rights reserved.
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Papar modal edit dengan data user
    const editModal = document.getElementById('editUserModal');
    editModal.addEventListener('show.bs.modal', function (event) {
      const button = event.relatedTarget;
      document.getElementById('edit_ic').value = button.getAttribute('data-ic');
      document.getElementById('edit_name').value = button.getAttribute('data-name');
      document.getElementById('edit_email').value = button.getAttribute('data-email');
      document.getElementById('edit_jawatan').value = button.getAttribute('data-jawatan');
      document.getElementById('edit_jabatan').value = button.getAttribute('data-jabatan');
    });

    // Tunjuk input jika pilih "Jabatan Lain"
    document.getElementById('jabatanSelect').addEventListener('change', function() {
      const jabatanLainContainer = document.getElementById('jabatanLainContainer');
      const jabatanLainInput = document.getElementById('jabatanLainInput');
      if (this.value === 'JABATAN LAIN') {
        jabatanLainContainer.style.display = 'block';
        jabatanLainInput.required = true;
      } else {
        jabatanLainContainer.style.display = 'none';
        jabatanLainInput.required = false;
        jabatanLainInput.value = '';
      }
    });
  </script>
</body>
</html>
