<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location:'/../../user/login.php");
    exit; 
  }
  
  if (isset($_POST['logout'])) {    
    session_destroy();
    header("Location:'/../../user/login.php");
  }
  
// Initialize variables
$product_message = '';
$category_message = '';
$selected_category = '';
$errors = []; // Array to store validation errors

// Include necessary files
include_once(__DIR__ .'/../../models/product.php');
include_once(__DIR__ .'/../../models/category.php');
include_once('imagesUpload.php');

// Handle product submission
if (isset($_POST['add_product'])) {
    // Validate product name
    $productName = trim($_POST['product_name']);
    if (empty($productName)) {
        $errors['product_name'] = 'Product name is required';
    } elseif (strlen($productName) > 255) {
        $errors['product_name'] = 'Product name must be less than 255 characters';
    }
    
    // Validate price
    $price = trim($_POST['price']);
    if (empty($price)) {
        $errors['price'] = 'Price is required';
    } elseif (!is_numeric($price)) {
        $errors['price'] = 'Price must be a valid number';
    } elseif ($price <= 0) {
        $errors['price'] = 'Price must be greater than 0';
    }
    
    // Validate category
    $categoryId = intval($_POST['category']);
    if ($categoryId <= 0) {
        $errors['category'] = 'Please select a valid category';
    }
    
    // Handle image upload
    $image = '';
    if (empty($_FILES['image']['name'])) {
        $errors['image'] = 'Product image is required';
    } else {
        $uploadResult = imageUpload();
        if ($uploadResult !== false) {
            $image = $uploadResult;
        } else {
            $errors['image'] = 'Failed to upload image. Please try again.';
        }
    }
    
    // If no errors, proceed with product creation
    if (empty($errors)) {
        if (addProduct($productName, $image, $price, $categoryId)) {
            $product_message = 'Product added successfully!';
            // Clear form fields
            $_POST = array();
            // wait for 2 seconds before redirecting
            echo "<script>
                setTimeout(function() {
                    window.location.href = 'listProducts.php';
                }, 2000);
            </script>";
        } else {
            $product_message = 'Failed to add product. Please try again.';
            $selected_category = $categoryId; // Keep the selected category
        }
    } else {
        $product_message = 'Please correct the errors below.';
        $selected_category = $categoryId; // Keep the selected category
    }
}

// Handle category submission
if (isset($_POST['save_category'])) {
    $categoryName = trim($_POST['category_name']);
    
    if (empty($categoryName)) {
        $category_message = 'Category name is required';
    } elseif (strlen($categoryName) > 255) {
        $category_message = 'Category name must be less than 255 characters';
    } else {
        if (addCategory($categoryName)) {
            $category_message = 'Category added successfully!';
            $categories = getCategories();
        } else {
            $category_message = 'Failed to add category. It may already exist.';
        }
    }
    
    // Prevent form resubmission
    echo "<script>window.history.replaceState(null, null, window.location.href);</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #f8f9fc;
            --success-color: #1cc88a;
            --info-color: #36b9cc;
            --danger-color: #e74a3b;
            --warning-color: #f6c23e;
            --border-radius: 0.35rem;
            --sidebar-width: 250px;
        }
        
        body {
            background-color: #f8f9fc;
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, sans-serif;
            overflow-x: hidden;
        }
        
        /* Sidebar Styles */
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
        
        .container {
            max-width: 800px;
            margin-top: 2rem;
        }
        
        .form-container {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            padding: 2rem;
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
        
        .modal-content {
            border-radius: var(--border-radius);
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        
        .modal-header {
            background-color: var(--primary-color);
            color: white;
            border-radius: var(--border-radius) var(--border-radius) 0 0;
        }
        
        .btn-close {
            filter: invert(1);
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
        <!-- Sidebar Navigation -->
        <div class="sidebar">
            <a href="#" class="sidebar-brand d-flex align-items-center justify-content-center">
                <i class="fas fa-store me-2"></i>
                <span>Admin Panel</span>
            </a>
            <form method="POST">  
      <button type="submit" class="bg-light" style="border:none;" name="logout">
    <a class=" text-danger sidebar-item">Log Out</a>
                  </button> </form>
            <div class="sidebar-divider"></div>
            
            <div class="nav flex-column">
                <a href="listProducts.php" class="sidebar-item">
                    <i class="fas fa-box-open"></i>
                    <span>Products</span>
                </a>
                
                <a href="usersList.php" class="sidebar-item">
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
        
        <div class="form-container">
            <h2 class="page-title"><i class="fas fa-plus-circle"></i> Add New Product</h2>
            
            <?php if (!empty($product_message)): ?>
                <div class="alert alert-<?= empty($errors) ? 'success' : 'danger' ?> d-flex align-items-center border-2 <?= empty($errors) ? 'border-success' : 'border-danger' ?> alert-dismissible fade show">
                    <i class="fas <?= empty($errors) ? 'fa-check-circle' : 'fa-exclamation-triangle' ?> me-3 fs-4"></i>
                    <div class="flex-grow-1">
                        <?= htmlspecialchars($product_message) ?>
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
                <div class="mb-4">
                    <label for="product_name" class="form-label"><i class="fas fa-tag me-2"></i>Product Name</label>
                    <div class="input-icon">
                        <input type="text" class="form-control <?= isset($errors['product_name']) ? 'is-invalid' : '' ?>" 
                               id="product_name" name="product_name" 
                               value="<?= htmlspecialchars($_POST['product_name'] ?? '') ?>" >
                        <i class="fas fa-edit"></i>
                        <?php if (isset($errors['product_name'])): ?>
                            <div class="invalid-feedback">
                                <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($errors['product_name']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="price" class="form-label"><i class="fas fa-dollar-sign me-2"></i>Price</label>
                    <div class="input-icon">
                        <input type="number" step="0.01" class="form-control <?= isset($errors['price']) ? 'is-invalid' : '' ?>" 
                               id="price" name="price" 
                               value="<?= htmlspecialchars($_POST['price'] ?? '') ?>" >
                        <i class="fas fa-money-bill-wave"></i>
                        <?php if (isset($errors['price'])): ?>
                            <div class="invalid-feedback">
                                <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($errors['price']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="mb-4 row">
                    <div class="col-md-9">
                        <label for="category" class="form-label"><i class="fas fa-list-alt me-2"></i>Category</label>
                        <div class="input-icon">
                            <select class="form-select <?= isset($errors['category']) ? 'is-invalid' : '' ?>" 
                                    id="category" name="category" >
                                <option value="">Select a category</option>
                                <?php
                                $categories = getCategories();
                                $selectedCategory = $_POST['category'] ?? $selected_category;
                                foreach ($categories as $category) {
                                    $selected = ($category['id'] == $selectedCategory) ? 'selected' : '';
                                    echo "<option value='{$category['id']}' $selected>{$category['category_name']}</option>";
                                }
                                ?>
                            </select>
                            <i class="fas fa-chevron-down"></i>
                            <?php if (isset($errors['category'])): ?>
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($errors['category']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                            <i class="fas fa-plus me-2"></i>Add Category
                        </button>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label"><i class="fas fa-image me-2"></i>Product Image</label>
                    <div class="file-upload-container <?= isset($errors['image']) ? 'border-danger' : '' ?>">
                        <label class="file-upload-label">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span>Click to upload or drag and drop</span>
                            <input type="file" class="file-upload-input" id="image" name="image" accept="image/*" >
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
                    <button type="submit" name="add_product" class="btn btn-success btn-lg">
                        <i class="fas fa-save me-2"></i>Add Product
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Category Modal -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCategoryModalLabel"><i class="fas fa-plus-circle me-2"></i>Add New Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" id="categoryForm">
                    <div class="modal-body">
                        <?php if (!empty($category_message)): ?>
                            <div class="alert alert-<?= strpos(strtolower($category_message), 'success') !== false ? 'success' : 'danger' ?>">
                                <?= htmlspecialchars($category_message) ?>
                            </div>
                        <?php endif; ?>
                        <div class="mb-3">
                            <label for="category_name" class="form-label"><i class="fas fa-tag me-2"></i>Category Name</label>
                            <input type="text" class="form-control" id="category_name" name="category_name" required
                                   value="<?= isset($_POST['category_name']) ? htmlspecialchars($_POST['category_name']) : '' ?>">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times me-2"></i>Close</button>
                        <button type="submit" class="btn btn-primary" name="save_category"><i class="fas fa-save me-2"></i>Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Clear category form when modal closes
        document.getElementById('addCategoryModal').addEventListener('hidden.bs.modal', function () {
            document.getElementById('category_name').value = '';
        });
        
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
        
        // If we have a category message, show the modal
        <?php if (!empty($category_message) && isset($_POST['save_category'])): ?>
            var categoryModal = new bootstrap.Modal(document.getElementById('addCategoryModal'));
            categoryModal.show();
        <?php endif; ?>
        
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