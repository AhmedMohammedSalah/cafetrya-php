<?php
// Initialize variables
$user_message = '';
$errors = [];
$success = false;

// Include necessary files
include_once(__DIR__ . '/../../models/user.php');
include_once(__DIR__ . '/../../models/room.php');
include_once(__DIR__ . '/../admin/imagesUpload.php');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    // Validate name
    $name = trim($_POST['name']);
    if (empty($name)) {
        $errors['name'] = 'Name is required';
    } elseif (strlen($name) > 100) {
        $errors['name'] = 'Name must be less than 100 characters';
    }

    // Validate email
    $email = trim($_POST['email']);
    if (empty($email)) {
        $errors['email'] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format';
    } elseif (strlen($email) > 100) {
        $errors['email'] = 'Email must be less than 100 characters';
    }
    // Check if email already exists
    if (checkMail($email)) {
        $errors['email'] = 'Email already exists';
    }
    // Validate password
    $password = $_POST['password'];
    if (empty($password)) {
        $errors['password'] = 'Password is required';
    } elseif (strlen($password) < 8) {
        $errors['password'] = 'Password must be at least 8 characters';
    }

    // Validate age
    $age = $_POST['age'];
    if (empty($age)) {
        $errors['age'] = 'Age is required';
    } elseif (!is_numeric($age)) {
        $errors['age'] = 'Age must be a number';
    } elseif ($age < 18 || $age > 120) {
        $errors['age'] = 'Age must be between 18 and 120';
    }

    // Validate room
    $room_id = $_POST['room_id'];
    if (empty($room_id)) {
        $errors['room_id'] = 'Room selection is required';
    }

    // Handle image upload
    $image = '';
    if (empty($_FILES['image']['name'])) {
        $errors['image'] = 'Profile image is required';
    } else {
        $uploadResult = userImageUpload();
        if ($uploadResult !== false) {
            $image = $uploadResult;
        } else {
            $errors['image'] = 'Failed to upload image. Please try again.';
        }
    }

    // If no errors, proceed with user creation
    if (empty($errors)) {
        // Hash the password before storing
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        if (addUser($name, $hashedPassword, $email, $image, $age, $room_id)) {
            $success = true;
            $user_message = 'User added successfully!';
            // Clear form on success
            $_POST = array();
        } else {
            $user_message = 'Failed to add user. Please try again.';
        }
    } else {
        $user_message = 'Please correct the errors below.';
    }
}

// Get all rooms for dropdown
include_once(__DIR__ . '/../../models/room.php');
$rooms = getAllRooms();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #f8f9fc;
            --success-color: #1cc88a;
            --danger-color: #e74a3b;
            --warning-color: #f6c23e;
            --border-radius: 0.35rem;
        }
        
        body {
      background: linear-gradient(to right, #74ebd5, #acb6e5);
      height: 100vh;
      margin: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
        
        .container {
            max-width: 800px;
            margin-top: 2rem;
        }
        
        .form-container {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
        }
         .form-container:hover {
      transform: scale(1.01);
    }
        .page-title {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--primary-color);
            display: flex;
            align-items: center;
        }
        
        .page-title i {
            margin-right: 15px;
            font-size: 1.8rem;
        }
        
        .form-label {
            font-weight: 600;
            color: #5a5c69;
            margin-bottom: 0.5rem;
        }
        
        .form-control, .form-select {
            border-radius: var(--border-radius);
            padding: 0.75rem 1rem;
            border: 1px solid #d1d3e2;
            transition: all 0.3s;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
        }
        
        .btn {
            border-radius: var(--border-radius);
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-success {
            background-color: var(--success-color);
            border-color: var(--success-color);
        }
        
        .btn-primary:hover, .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        
        .alert {
            border-radius: var(--border-radius);
        }
        
        .input-icon {
            position: relative;
        }
        
        .input-icon i {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #d1d3e2;
        }
        
        .file-upload-container {
            border: 2px dashed #d1d3e2;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s;
            cursor: pointer;
        }
        
        .file-upload-container:hover {
            border-color: var(--primary-color);
            background-color: rgba(78, 115, 223, 0.05);
        }
        
        .file-upload-label {
            display: flex;
            flex-direction: column;
            align-items: center;
            cursor: pointer;
        }
        
        .file-upload-label i {
            font-size: 2rem;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }
        
        .file-upload-input {
            display: none;
        }
        
        /* Form error styles */
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
        
        .password-toggle {
            position: absolute;
            right: 35px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 0 15px;
            }
            
            .form-container {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2 class="page-title"><i class="fas fa-user-plus"></i> Register</h2>
            
            <?php if (!empty($user_message)): ?>
                <div class="alert alert-<?= $success ? 'success' : 'danger' ?> d-flex align-items-center border-2 <?= $success ? 'border-success' : 'border-danger' ?> alert-dismissible fade show">
                    <i class="fas <?= $success ? 'fa-check-circle' : 'fa-exclamation-triangle' ?> me-3 fs-4"></i>
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
                
                <script>
                // Auto-dismiss alert after 5 seconds
                document.addEventListener('DOMContentLoaded', function() {
                    var alert = document.querySelector('.alert');
                    if (alert) {
                        setTimeout(function() {
                            var bsAlert = new bootstrap.Alert(alert);
                            bsAlert.close();
                        }, 5000);
                    }
                });
                </script>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label for="name" class="form-label"><i class="fas fa-user me-2"></i>Full Name</label>
                        <div class="input-icon">
                            <input type="text" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                                   id="name" name="name" 
                                   value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                            <i class="fas fa-user"></i>
                            <?php if (isset($errors['name'])): ?>
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($errors['name']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-4">
                        <label for="email" class="form-label"><i class="fas fa-envelope me-2"></i>Email Address</label>
                        <div class="input-icon">
                            <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                                   id="email" name="email" 
                                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                            <i class="fas fa-envelope"></i>
                            <?php if (isset($errors['email'])): ?>
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($errors['email']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label for="password" class="form-label"><i class="fas fa-lock me-2"></i>Password</label>
                        <div class="input-icon">
                            <input type="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" 
                                   id="password" name="password" required>
                            <i class="fas fa-lock"></i>
                            <span class="password-toggle" onclick="togglePassword()">
                                <i class="fas fa-eye"></i>
                            </span>
                            <?php if (isset($errors['password'])): ?>
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($errors['password']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <small class="text-muted">Minimum 8 characters</small>
                    </div>
                    
                    <div class="col-md-3 mb-4">
                        <label for="age" class="form-label"><i class="fas fa-birthday-cake me-2"></i>Age</label>
                        <input type="number" class="form-control <?= isset($errors['age']) ? 'is-invalid' : '' ?>" 
                               id="age" name="age" min="18" max="120" 
                               value="<?= htmlspecialchars($_POST['age'] ?? '') ?>" required>
                        <?php if (isset($errors['age'])): ?>
                            <div class="invalid-feedback">
                                <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($errors['age']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="col-md-3 mb-4">
                        <label for="room_id" class="form-label"><i class="fas fa-door-open me-2"></i>Room</label>
                        <select class="form-select <?= isset($errors['room_id']) ? 'is-invalid' : '' ?>" 
                                id="room_id" name="room_id" required>
                            <option value="">Select Room</option>
                            <?php foreach ($rooms as $room): ?>
                                <option value="<?= $room['id'] ?>" 
                                    <?= (isset($_POST['room_id']) && $_POST['room_id'] == $room['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($room['room_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['room_id'])): ?>
                            <div class="invalid-feedback">
                                <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($errors['room_id']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label"><i class="fas fa-image me-2"></i>Profile Image</label>
                    <div class="file-upload-container <?= isset($errors['image']) ? 'border-danger' : '' ?>">
                        <label class="file-upload-label">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span>Click to upload or drag and drop</span>
                            <input type="file" class="file-upload-input" id="image" name="image" accept="image/*" required>
                        </label>
                    </div>
                    <?php if (isset($errors['image'])): ?>
                        <div class="invalid-feedback d-block">
                            <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($errors['image']) ?>
                        </div>
                    <?php endif; ?>
                    <small class="text-muted">Max file size: 2MB. Allowed formats: JPG, PNG, GIF.</small>
                </div>
                
                <div class="d-grid">
                    <button type="submit" name="add_user" class="btn btn-success btn-lg">
                        <i class="fa-solid fa-address-card"></i>  Register
                        
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.querySelector('.password-toggle i');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        // Show file name when image is selected
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
        
        // Client-side validation for file upload
        const imageInput = document.getElementById('image');
        if (imageInput) {
            imageInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    // Check file size (2MB max)
                    if (file.size > 2 * 1024 * 1024) {
                        alert('File size exceeds 2MB limit. Please choose a smaller file.');
                        this.value = '';
                    }
                    
                    // Check file type
                    const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
                    if (!validTypes.includes(file.type)) {
                        alert('Only JPG, PNG, and GIF files are allowed.');
                        this.value = '';
                    }
                }
            });
        }
    });
    </script>
</body>
</html>