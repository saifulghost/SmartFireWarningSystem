<?php
// Sambung ke database
include 'db_connect.php';

// Function untuk dapatkan senarai jabatan dari database
function getJabatanList($conn) {
    $jabatan_list = array();
    $query = "SELECT id, nama_jabatan FROM departments ORDER BY nama_jabatan";
    $result = mysqli_query($conn, $query);
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $jabatan_list[] = $row;
        }
    }
    
    return $jabatan_list;
}

// Function untuk tambah jabatan baru ke database
function addNewJabatan($conn, $nama_jabatan) {
    $nama_jabatan = mysqli_real_escape_string($conn, $nama_jabatan);
    $query = "INSERT INTO departments (nama_jabatan) VALUES ('$nama_jabatan')";
    return mysqli_query($conn, $query);
}

// Function untuk update jabatan
function updateJabatan($conn, $id, $nama_jabatan) {
    $nama_jabatan = mysqli_real_escape_string($conn, $nama_jabatan);
    $query = "UPDATE departments SET nama_jabatan = '$nama_jabatan' WHERE id = $id";
    return mysqli_query($conn, $query);
}

// Function untuk delete jabatan
function deleteJabatan($conn, $id) {
    // Check jika ada makmal yang menggunakan jabatan ini
    $check_query = "SELECT COUNT(*) as total FROM labs WHERE jabatan = (SELECT nama_jabatan FROM departments WHERE id = $id)";
    $result = mysqli_query($conn, $check_query);
    $row = mysqli_fetch_assoc($result);
    
    if ($row['total'] > 0) {
        return false; // Tidak boleh delete jika ada makmal yang menggunakan jabatan ini
    }
    
    $query = "DELETE FROM departments WHERE id = $id";
    return mysqli_query($conn, $query);
}

// Function untuk check jika jabatan sudah wujud
function jabatanExists($conn, $nama_jabatan, $exclude_id = null) {
    $nama_jabatan = mysqli_real_escape_string($conn, $nama_jabatan);
    $query = "SELECT id FROM departments WHERE nama_jabatan = '$nama_jabatan'";
    if ($exclude_id) {
        $query .= " AND id != $exclude_id";
    }
    $result = mysqli_query($conn, $query);
    return mysqli_num_rows($result) > 0;
}

// Function untuk update makmal
function updateLab($conn, $id, $jabatan, $nama_makmal, $nama_pic, $telefon_pic) {
    $jabatan = mysqli_real_escape_string($conn, $jabatan);
    $nama_makmal = mysqli_real_escape_string($conn, $nama_makmal);
    $nama_pic = mysqli_real_escape_string($conn, $nama_pic);
    $telefon_pic = mysqli_real_escape_string($conn, $telefon_pic);
    
    $query = "UPDATE labs SET jabatan = '$jabatan', nama_makmal = '$nama_makmal', 
              nama_pic = '$nama_pic', telefon_pic = '$telefon_pic' WHERE id = $id";
    return mysqli_query($conn, $query);
}

// Function untuk delete makmal
function deleteLab($conn, $id) {
    $query = "DELETE FROM labs WHERE id = $id";
    return mysqli_query($conn, $query);
}

// Process form submissions
if (isset($_POST['add_jabatan'])) {
    $new_jabatan = trim($_POST['new_jabatan']);
    
    if (!empty($new_jabatan)) {
        if (!jabatanExists($conn, $new_jabatan)) {
            if (addNewJabatan($conn, $new_jabatan)) {
                $success_jabatan = "Jabatan '$new_jabatan' berjaya ditambah!";
            } else {
                $error_jabatan = "Error: Gagal menambah jabatan.";
            }
        } else {
            $error_jabatan = "Jabatan '$new_jabatan' sudah wujud!";
        }
    } else {
        $error_jabatan = "Sila masukkan nama jabatan.";
    }
}

// Update jabatan
if (isset($_POST['update_jabatan'])) {
    $jabatan_id = $_POST['jabatan_id'];
    $nama_jabatan = trim($_POST['nama_jabatan']);
    
    if (!empty($nama_jabatan)) {
        if (!jabatanExists($conn, $nama_jabatan, $jabatan_id)) {
            if (updateJabatan($conn, $jabatan_id, $nama_jabatan)) {
                $success_jabatan = "Jabatan berjaya dikemaskini!";
            } else {
                $error_jabatan = "Error: Gagal mengemaskini jabatan.";
            }
        } else {
            $error_jabatan = "Jabatan '$nama_jabatan' sudah wujud!";
        }
    } else {
        $error_jabatan = "Sila masukkan nama jabatan.";
    }
}

// Delete jabatan
if (isset($_POST['delete_jabatan'])) {
    $jabatan_id = $_POST['jabatan_id'];
    
    if (deleteJabatan($conn, $jabatan_id)) {
        $success_jabatan = "Jabatan berjaya dipadam!";
    } else {
        $error_jabatan = "Error: Tidak boleh memadam jabatan yang mempunyai makmal.";
    }
}

// Tambah makmal baru
if (isset($_POST['add_lab'])) {
    $jabatan = mysqli_real_escape_string($conn, $_POST['jabatan']);
    $nama_makmal = mysqli_real_escape_string($conn, $_POST['nama_makmal']);
    $nama_pic = mysqli_real_escape_string($conn, $_POST['nama_pic']);
    $telefon_pic = mysqli_real_escape_string($conn, $_POST['telefon_pic']);

    // Check jika jabatan wujud dalam database departments
    if (!jabatanExists($conn, $jabatan)) {
        // Jika tak wujud, tambah ke departments
        addNewJabatan($conn, $jabatan);
    }

    $sql = "INSERT INTO labs (jabatan, nama_makmal, nama_pic, telefon_pic) 
            VALUES ('$jabatan', '$nama_makmal', '$nama_pic', '$telefon_pic')";
    
    if (mysqli_query($conn, $sql)) {
        $success_lab = "Makmal berjaya ditambah!";
    } else {
        $error_lab = "Error: " . mysqli_error($conn);
    }
}

// Update makmal
if (isset($_POST['update_lab'])) {
    $lab_id = $_POST['lab_id'];
    $jabatan = mysqli_real_escape_string($conn, $_POST['jabatan']);
    $nama_makmal = mysqli_real_escape_string($conn, $_POST['nama_makmal']);
    $nama_pic = mysqli_real_escape_string($conn, $_POST['nama_pic']);
    $telefon_pic = mysqli_real_escape_string($conn, $_POST['telefon_pic']);

    if (updateLab($conn, $lab_id, $jabatan, $nama_makmal, $nama_pic, $telefon_pic)) {
        $success_lab = "Makmal berjaya dikemaskini!";
    } else {
        $error_lab = "Error: Gagal mengemaskini makmal.";
    }
}

// Delete makmal
if (isset($_POST['delete_lab'])) {
    $lab_id = $_POST['lab_id'];
    
    if (deleteLab($conn, $lab_id)) {
        $success_lab = "Makmal berjaya dipadam!";
    } else {
        $error_lab = "Error: Gagal memadam makmal.";
    }
}

// Dapatkan senarai jabatan dari database
$jabatan_list = getJabatanList($conn);

// Ambil semua makmal
$labs_result = mysqli_query($conn, "SELECT * FROM labs ORDER BY jabatan, nama_makmal ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Lab Management - Smart Fire Alarm System</title>
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
        .jabatan-section {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border-left: 5px solid #e53935;
        }
        .jabatan-header {
            color: #e53935;
            font-weight: 700;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .lab-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            border-left: 4px solid #28a745;
            transition: transform 0.2s;
            position: relative;
        }
        .lab-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .lab-actions {
            position: absolute;
            top: 10px;
            right: 10px;
            display: flex;
            gap: 5px;
        }
        .lab-action-btn {
            background: transparent;
            border: none;
            border-radius: 4px;
            padding: 4px 8px;
            font-size: 12px;
            transition: all 0.3s;
        }
        .lab-action-btn.edit {
            color: #007bff;
        }
        .lab-action-btn.edit:hover {
            background: #007bff;
            color: white;
        }
        .lab-action-btn.delete {
            color: #dc3545;
        }
        .lab-action-btn.delete:hover {
            background: #dc3545;
            color: white;
        }
        .add-lab-btn {
            background: #28a745;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 8px 15px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .add-lab-btn:hover {
            background: #218838;
            transform: translateY(-2px);
        }
        .jabatan-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .jabatan-action-btn {
            background: transparent;
            border: none;
            border-radius: 4px;
            padding: 4px 8px;
            font-size: 12px;
            transition: all 0.3s;
        }
        .jabatan-action-btn.edit {
            color: #007bff;
        }
        .jabatan-action-btn.edit:hover {
            background: #007bff;
            color: white;
        }
        .jabatan-action-btn.delete {
            color: #dc3545;
        }
        .jabatan-action-btn.delete:hover {
            background: #dc3545;
            color: white;
        }
        footer {
            margin-top: 40px;
            text-align: center;
            padding: 20px;
            background: #fff;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
            border-radius: 20px 20px 0 0;
            color: #777;
        }
        .empty-state {
            text-align: center;
            padding: 30px;
            color: #6c757d;
        }
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 15px;
            color: #dee2e6;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
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
                    <li class="nav-item"><a class="nav-link active" href="labs.php">Labs</a></li>
                    <li class="nav-item"><a class="nav-link" href="alerts.php">Alerts</a></li>
                    <li class="nav-item"><a class="nav-link" href="live.php">Live</a></li>
                    <li class="nav-item"><a class="nav-link" href="history.php">History</a></li>
                    <li class="nav-item"><a class="nav-link" href="users.php">Users</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-danger"><i class="fa-solid fa-flask"></i> Lab Management</h2>
            <div>
                <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#addLabModal">
                    <i class="fa-solid fa-plus"></i> Add New Lab
                </button>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addJabatanModal">
                    <i class="fa-solid fa-building"></i> Add New Department
                </button>
            </div>
        </div>

        <!-- Success/Error Messages -->
        <?php if (isset($success_lab)): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fa-solid fa-check"></i> <?php echo $success_lab; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($error_lab)): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fa-solid fa-exclamation-triangle"></i> <?php echo $error_lab; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($success_jabatan)): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fa-solid fa-check"></i> <?php echo $success_jabatan; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($error_jabatan)): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fa-solid fa-exclamation-triangle"></i> <?php echo $error_jabatan; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Display Labs by Jabatan -->
        <div class="row">
            <?php 
            $labs_by_jabatan = array();
            while ($lab = mysqli_fetch_assoc($labs_result)) {
                $labs_by_jabatan[$lab['jabatan']][] = $lab;
            }
            
            if (empty($jabatan_list)): ?>
                <div class="col-12">
                    <div class="empty-state">
                        <i class="fa-solid fa-building"></i>
                        <h4>No Departments Found</h4>
                        <p>Start by adding your first department.</p>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addJabatanModal">
                            <i class="fa-solid fa-plus"></i> Add First Department
                        </button>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($jabatan_list as $jabatan_item): 
                    $jabatan = $jabatan_item['nama_jabatan'];
                    $jabatan_id = $jabatan_item['id'];
                    $labs = $labs_by_jabatan[$jabatan] ?? array();
                ?>
                <div class="col-md-6 mb-4">
                    <div class="jabatan-section">
                        <div class="jabatan-header">
                            <h4><i class="fa-solid fa-building"></i> <?php echo $jabatan; ?></h4>
                            <div class="jabatan-actions">
                                <span class="badge bg-primary"><?php echo count($labs); ?> Labs</span>
                                <button class="add-lab-btn" onclick="setJabatan('<?php echo $jabatan; ?>')">
                                    <i class="fa-solid fa-plus"></i> Add Lab
                                </button>
                                <button class="jabatan-action-btn edit" data-bs-toggle="modal" data-bs-target="#editJabatanModal" 
                                        onclick="setEditJabatan(<?php echo $jabatan_id; ?>, '<?php echo $jabatan; ?>')">
                                    <i class="fa-solid fa-edit"></i>
                                </button>
                                <button class="jabatan-action-btn delete" data-bs-toggle="modal" data-bs-target="#deleteJabatanModal"
                                        onclick="setDeleteJabatan(<?php echo $jabatan_id; ?>, '<?php echo $jabatan; ?>')">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        
                        <?php if (empty($labs)): ?>
                            <div class="empty-state" style="padding: 20px;">
                                <i class="fa-solid fa-flask"></i>
                                <p class="text-muted mb-3">No labs in this department yet.</p>
                                <button class="btn btn-sm btn-success" onclick="setJabatan('<?php echo $jabatan; ?>')">
                                    <i class="fa-solid fa-plus"></i> Add First Lab
                                </button>
                            </div>
                        <?php else: ?>
                            <?php foreach ($labs as $lab): ?>
                            <div class="lab-card">
                                <div class="lab-actions">
                                    <button class="lab-action-btn edit" data-bs-toggle="modal" data-bs-target="#editLabModal"
                                            onclick="setEditLab(<?php echo $lab['id']; ?>, '<?php echo $lab['jabatan']; ?>', '<?php echo $lab['nama_makmal']; ?>', '<?php echo $lab['nama_pic']; ?>', '<?php echo $lab['telefon_pic']; ?>')">
                                        <i class="fa-solid fa-edit"></i>
                                    </button>
                                    <button class="lab-action-btn delete" data-bs-toggle="modal" data-bs-target="#deleteLabModal"
                                            onclick="setDeleteLab(<?php echo $lab['id']; ?>, '<?php echo $lab['nama_makmal']; ?>')">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                                <h6 class="mb-2"><?php echo $lab['nama_makmal']; ?></h6>
                                <p class="mb-1"><strong>P.I.C:</strong> <?php echo $lab['nama_pic']; ?></p>
                                <p class="mb-0"><strong>Phone:</strong> <?php echo $lab['telefon_pic']; ?></p>
                                <small class="text-muted">Added: <?php echo date('d/m/Y', strtotime($lab['created_at'])); ?></small>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal Add Lab -->
    <div class="modal fade" id="addLabModal" tabindex="-1" aria-labelledby="addLabModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addLabModalLabel">Add New Laboratory</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Department</label>
                        <select class="form-select" name="jabatan" id="jabatanSelect" required>
                            <option value="" disabled selected>Select Department</option>
                            <?php foreach($jabatan_list as $jabatan): ?>
                                <option value="<?= $jabatan['nama_jabatan'] ?>"><?= $jabatan['nama_jabatan'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Laboratory Name</label>
                        <input type="text" class="form-control" name="nama_makmal" required 
                               placeholder="Example: Computer Lab 1">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">P.I.C Name</label>
                        <input type="text" class="form-control" name="nama_pic" required 
                               placeholder="Person In Charge Name">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">P.I.C Phone Number</label>
                        <input type="text" class="form-control" name="telefon_pic" required 
                               placeholder="Example: 012-3456789">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_lab" class="btn btn-danger">Save Laboratory</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Lab -->
    <div class="modal fade" id="editLabModal" tabindex="-1" aria-labelledby="editLabModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editLabModalLabel">Edit Laboratory</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="lab_id" id="editLabId">
                    <div class="mb-3">
                        <label class="form-label">Department</label>
                        <select class="form-select" name="jabatan" id="editLabJabatan" required>
                            <option value="" disabled selected>Select Department</option>
                            <?php foreach($jabatan_list as $jabatan): ?>
                                <option value="<?= $jabatan['nama_jabatan'] ?>"><?= $jabatan['nama_jabatan'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Laboratory Name</label>
                        <input type="text" class="form-control" name="nama_makmal" id="editLabNama" required 
                               placeholder="Example: Computer Lab 1">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">P.I.C Name</label>
                        <input type="text" class="form-control" name="nama_pic" id="editLabPic" required 
                               placeholder="Person In Charge Name">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">P.I.C Phone Number</label>
                        <input type="text" class="form-control" name="telefon_pic" id="editLabTelefon" required 
                               placeholder="Example: 012-3456789">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="update_lab" class="btn btn-primary">Update Laboratory</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Delete Lab -->
    <div class="modal fade" id="deleteLabModal" tabindex="-1" aria-labelledby="deleteLabModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteLabModalLabel">Delete Laboratory</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="lab_id" id="deleteLabId">
                    <p>Are you sure you want to delete the laboratory: <strong id="deleteLabNama"></strong>?</p>
                    <p class="text-danger">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="delete_lab" class="btn btn-danger">Delete Laboratory</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Add Jabatan -->
    <div class="modal fade" id="addJabatanModal" tabindex="-1" aria-labelledby="addJabatanModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addJabatanModalLabel">Add New Department</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Department Name</label>
                        <input type="text" class="form-control" name="new_jabatan" required 
                               placeholder="Example: INFORMATION TECHNOLOGY DEPARTMENT">
                        <div class="form-text">Enter the name of the new department you want to add.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_jabatan" class="btn btn-primary">Add Department</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Jabatan -->
    <div class="modal fade" id="editJabatanModal" tabindex="-1" aria-labelledby="editJabatanModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editJabatanModalLabel">Edit Department</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="jabatan_id" id="editJabatanId">
                    <div class="mb-3">
                        <label class="form-label">Department Name</label>
                        <input type="text" class="form-control" name="nama_jabatan" id="editJabatanNama" required 
                               placeholder="Example: INFORMATION TECHNOLOGY DEPARTMENT">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="update_jabatan" class="btn btn-primary">Update Department</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Delete Jabatan -->
    <div class="modal fade" id="deleteJabatanModal" tabindex="-1" aria-labelledby="deleteJabatanModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteJabatanModalLabel">Delete Department</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="jabatan_id" id="deleteJabatanId">
                    <p>Are you sure you want to delete the department: <strong id="deleteJabatanNama"></strong>?</p>
                    <p class="text-danger">Note: You cannot delete a department that has laboratories assigned to it.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="delete_jabatan" class="btn btn-danger">Delete Department</button>
                </div>
            </form>
        </div>
    </div>

    <footer>
        © 2025 Smart Fire Alarm System. All rights reserved.
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Function untuk set jabatan ketika click button "Add Lab"
        function setJabatan(jabatan) {
            document.getElementById('jabatanSelect').value = jabatan;
            
            // Show modal
            var modal = new bootstrap.Modal(document.getElementById('addLabModal'));
            modal.show();
        }

        // Function untuk set data edit lab
        function setEditLab(id, jabatan, nama, pic, telefon) {
            document.getElementById('editLabId').value = id;
            document.getElementById('editLabJabatan').value = jabatan;
            document.getElementById('editLabNama').value = nama;
            document.getElementById('editLabPic').value = pic;
            document.getElementById('editLabTelefon').value = telefon;
        }

        // Function untuk set data delete lab
        function setDeleteLab(id, nama) {
            document.getElementById('deleteLabId').value = id;
            document.getElementById('deleteLabNama').textContent = nama;
        }

        // Function untuk set data edit jabatan
        function setEditJabatan(id, nama) {
            document.getElementById('editJabatanId').value = id;
            document.getElementById('editJabatanNama').value = nama;
        }

        // Function untuk set data delete jabatan
        function setDeleteJabatan(id, nama) {
            document.getElementById('deleteJabatanId').value = id;
            document.getElementById('deleteJabatanNama').textContent = nama;
        }

        // Auto-refresh page setelah success operation
        <?php if (isset($success_jabatan) || isset($success_lab)): ?>
            setTimeout(function() {
                window.location.href = 'labs.php';
            }, 1000);
        <?php endif; ?>

        // Focus pada input field ketika modal dibuka
        document.getElementById('addJabatanModal').addEventListener('shown.bs.modal', function () {
            document.querySelector('#addJabatanModal input[name="new_jabatan"]').focus();
        });
    </script>
</body>
</html>