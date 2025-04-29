<?php
$user_message = '';
$errors = []; 
include_once(__DIR__ . '/../../models/user.php');
include_once(__DIR__ . '/../../models/room.php');
include_once(__DIR__ . '/../../controllers/imagesUpload.php');


$userId = isset($_GET['id']) ? intval($_GET['id']) : 0;

$user = getUserById($userId);
if (!$user) {
    header("Location: usersList.php");
    exit();
}


$rooms = getAllRooms();


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {

    $name = trim($_POST['name']);
    if (empty($name)) {
        $errors['name'] = 'Name is required';
    } elseif (strlen($name) > 100) {
        $errors['name'] = 'Name must be less than 100 characters';
    }
    

    $email = trim($_POST['email']);
    if (empty($email)) {
        $errors['email'] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format';
    } elseif (strlen($email) > 100) {
        $errors['email'] = 'Email must be less than 100 characters';
    }
    

    $age = isset($_POST['age']) ? intval($_POST['age']) : null;
    if ($age !== null && ($age < 0 || $age > 120)) {
        $errors['age'] = 'Age must be between 0 and 120';
    }
    

    $roomId = isset($_POST['room_id']) ? intval($_POST['room_id']) : null;
    if ($roomId !== null && $roomId <= 0) {
        $errors['room_id'] = 'Please select a valid room';
    }
    
    $image = $user['image'];
    

    $removeImage = isset($_POST['remove_image']) && $_POST['remove_image'] == 'on';
    

    if (!empty($_FILES['image']['name'])) {

        $uploadResult = imageUpload();
        if ($uploadResult !== false) {
            $image = $uploadResult;
        } else {
            $errors['image'] = 'Failed to upload image. Please try again.';
        }
    } elseif ($removeImage) {
        $image = ''; 
    }
    
    if (empty($errors)) {
        $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;
        
        if (updateUser($userId, $name, $email, $password, $image, $age, $roomId)) {
            $_SESSION['message'] = 'User updated successfully!';
            $_SESSION['message_type'] = 'success';
            header("Location: userList.php");
            exit();
        } else {
            $user_message = 'Failed to update user. Please try again.';
        }
    } else {
        $user_message = 'Please correct the errors below.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #f8f9fc;
            --accent-color: #1cc88a;
            --danger-color: #e74a3b;
            --warning-color: #f6c23e;
            --sidebar-width: 250px;
        }
        
        body {
            background-color: #f8f9fc;
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, sans-serif;
            overflow-x: hidden;
        }
        
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: linear-gradient(180deg, var(--primary-color) 0%, #224abe 100%);
            color: white;
            padding: 20px 0;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            z-index: 1000;
            transition: all 0.3s;
        }
        
        .sidebar-brand {
            height: 4.375rem;
            text-decoration: none;
            font-size: 1.2rem;
            font-weight: 800;
            padding: 1.5rem 1rem;
            text-align: center;
            letter-spacing: 0.05rem;
            color: white;
            display: block;
            margin-bottom: 1rem;
        }
        
        .sidebar-divider {
            border-top: 1px solid rgba(255, 255, 255, 0.15);
            margin: 1rem 0;
        }
        
        .sidebar-item {
            padding: 0.75rem 1rem;
            margin: 0 0.5rem;
            border-radius: 0.35rem;
            color: rgba(255, 255, 255, 0.8);
            transition: all 0.3s;
            display: block;
            text-decoration: none;
        }
        
        .sidebar-item:hover, .sidebar-item.active {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            text-decoration: none;
        }
        
        .sidebar-item i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px;
            transition: all 0.3s;
        }
        
        .page-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, #224abe 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 0.35rem;
            margin-bottom: 2rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        
        .page-header h1 {
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
        }
        
        .page-header h1 i {
            margin-right: 15px;
            font-size: 1.5rem;
        }

        
        .is-invalid {
            border-color: var(--danger-color) !important;
            background-image: none !important;
        }
        
        .invalid-feedback {
            color: var(--danger-color);
            font-size: 0.875em;
            margin-top: 0.25rem;
            display: block;
        }
        
        .file-upload-container {
            border: 2px dashed #d1d3e2;
            border-radius: 0.35rem;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s;
            background-color: #f8f9fc;
        }
        
        .file-upload-container:hover {
            border-color: var(--primary-color);
            background-color: rgba(78, 115, 223, 0.05);
        }
        
        .file-upload-label {
            cursor: pointer;
            display: block;
        }
        
        .file-upload-input {
            display: none;
        }
        
        .user-img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #e3e6f0;
            margin: 0 auto;
            display: block;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 0;
                overflow: hidden;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .sidebar.active {
                width: var(--sidebar-width);
            }
            
            .main-content.active {
                margin-left: var(--sidebar-width);
            }
        }
    </style>
</head>
<body>
    <div class="container">

    <div class="sidebar">
            <a href="#" class="sidebar-brand d-flex align-items-center justify-content-center">
                <i class="fas fa-store me-2"></i>
                <span>Admin Panel</span>
            </a>
            
            <div class="sidebar-divider"></div>
            
            <div class="nav flex-column">
                <a href="listProducts.php" class="sidebar-item">
                    <i class="fas fa-box-open"></i>
                    <span>Products</span>
                </a>
                
                <a href="usersList.php" class="sidebar-item active">
                    <i class="fas fa-users"></i>
                    <span>Users</span>
                </a>
                
                <a href="checks.php" class="sidebar-item">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <span>Checks</span>
                </a>
                
                <a href="unfinshedOrders.php" class="sidebar-item">
                    <i class="fas fa-clipboard-list"></i>
                    <span>Pending Orders</span>
                </a>
                <a href="addOrder.php" class="sidebar-item mb-2">
        <i class="fa-solid fa-cart-shopping"></i> <span>Manual Orders</span>
      </a>
            </div>
        </div>
         
        <div class="main-content">
            <div class="form-container">
                <h2 class="page-header"><i class="fas fa-user-edit"></i> Edit User</h2>
                
                <?php if (!empty($user_message)): ?>
                    <?php
                    $isSuccess = strpos(strtolower($user_message), 'success') !== false;
                    $alertClass = $isSuccess ? 'success' : 'danger';
                    $iconClass = $isSuccess ? 'fa-check-circle' : 'fa-exclamation-triangle';
                    $borderClass = $isSuccess ? 'border-success' : 'border-danger';
                    ?>
                    <div class="alert alert-<?= $alertClass ?> d-flex align-items-center border-2 <?= $borderClass ?> alert-dismissible fade show">
                        <i class="fas <?= $iconClass ?> me-3 fs-4"></i>
                        <div class="flex-grow-1">
                            <?= htmlspecialchars($user_message) ?>
                            <?php if (!empty($errors)): ?>
                                <ul class="mb-0 mt-2">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?= htmlspecialchars($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <form method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-4 text-center mb-4">
                            <?php if (!empty($user['image'])): ?>
                                <img src="<?= $user['image'] ?>" alt="Current user image" class="user-img mb-3">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="remove_image" name="remove_image">
                                    <label class="form-check-label text-danger" for="remove_image">
                                        Remove current image
                                    </label>
                                </div>
                            <?php else: ?>
                                <div class="user-img bg-light d-flex align-items-center justify-content-center mb-3">
                                    <i class="fas fa-user text-muted fa-4x"></i>
                                </div>
                            <?php endif; ?>
                            
                            <div class="file-upload-container <?= isset($errors['image']) ? 'border-danger' : '' ?>">
                                <label class="file-upload-label">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <span>Click to upload new image or drag and drop</span>
                                    <input type="file" class="file-upload-input" id="image" name="image" accept="image/*">
                                </label>
                                <?php if (isset($errors['image'])): ?>
                                    <div class="invalid-feedback d-block">
                                        <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($errors['image']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <small class="text-muted">Max file size: 2MB. Allowed formats: JPG, PNG, GIF.</small>
                        </div>
                        
                        <div class="col-md-8">
                            <div class="mb-4">
                                <label for="name" class="form-label"><i class="fas fa-user me-2"></i>Full Name</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                                           id="name" name="name" 
                                           value="<?= htmlspecialchars($_POST['name'] ?? $user['name']) ?>">
                                    <?php if (isset($errors['name'])): ?>
                                        <div class="invalid-feedback">
                                            <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($errors['name']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="email" class="form-label"><i class="fas fa-envelope me-2"></i>Email</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input disabled type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                                           id="email" name="email" 
                                           value="<?= htmlspecialchars($_POST['email'] ?? $user['email']) ?>">
                                    <?php if (isset($errors['email'])): ?>
                                        <div class="invalid-feedback">
                                            <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($errors['email']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="password" class="form-label"><i class="fas fa-lock me-2"></i>New Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control" 
                                           id="password" name="password" 
                                           placeholder="Leave blank to keep current password">
                                </div>
                                <small class="text-muted">Leave blank if you don't want to change the password</small>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="age" class="form-label"><i class="fas fa-birthday-cake me-2"></i>Age</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-birthday-cake"></i></span>
                                        <input type="number" class="form-control <?= isset($errors['age']) ? 'is-invalid' : '' ?>" 
                                               id="age" name="age" min="0" max="120"
                                               value="<?= $_POST['age'] ?? $user['age'] ?? '' ?>">
                                        <?php if (isset($errors['age'])): ?>
                                            <div class="invalid-feedback">  
                                                <i class="fas fa-exclamation-circle me-2"></i><?= $errors['age']?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-4">
                                    <label for="room_id" class="form-label"><i class="fas fa-door-open me-2"></i>Room</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-door-open"></i></span>
                                        <select class="form-select <?= isset($errors['room_id']) ? 'is-invalid' : '' ?>" 
                                                id="room_id" name="room_id">
                                            <option value="">Select a room</option>
                                            <?php while ($room = mysqli_fetch_assoc($rooms)): ?>
                                                <option value="<?= $room['id'] ?>" 
                                                    <?= (isset($_POST['room_id']) ? $_POST['room_id'] : $user['room_id']) == $room['id'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($room['room_name']) ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                        <?php if (isset($errors['id'])): ?>
                                            <div class="invalid-feedback">
                                                <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($errors['id']) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                <a href="userList.php" class="btn btn-secondary me-md-2">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                                <button type="submit" name="update_user" class="btn btn-success">
                                    <i class="fas fa-save me-2"></i>Update User
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('image').addEventListener('change', function() {
            var fileName = this.value.split('\\').pop();
            var uploadContainer = this.closest('.file-upload-container');
            if (fileName) {
                uploadContainer.querySelector('span').textContent = fileName;
                uploadContainer.querySelector('i').classList.remove('fa-cloud-upload-alt');
                uploadContainer.querySelector('i').classList.add('fa-check-circle');
                uploadContainer.style.borderColor = '#1cc88a';
                uploadContainer.style.backgroundColor = 'rgba(28, 200, 138, 0.05)';
            }
        });
        
        const imageInput = document.getElementById('image');

        if (imageInput) {
            imageInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    if (file.size > 2 * 1024 * 1024) {

                        showAlert('File size exceeds 2MB limit. Please choose a smaller file.','warning');
                        this.value = '';
                    }
                    
                    const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
                    if (!validTypes.includes(file.type)) {
                        showAlert('Only JPG, PNG, and GIF files are allowed.','warning');
                        this.value = '';
                    }
                }
            });
        }
    })

 
    </script>
</body>
</html>