<?php
// Initialize variables
$product_message = '';
$category_message = '';
$errors = []; // Array to store validation errors

// Include necessary files
include_once(__DIR__ . '/../../models/product.php');
include_once(__DIR__ . '/../../models/category.php');
include_once(__DIR__ . '/../../controllers/imagesUpload.php');

// Get product ID from URL
$productId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch product data
$product = getProductById($productId);
if (!$product) {
    header("Location: listProducts.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
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
    $image = $product['image']; // Default to current image
    
    // Check if remove image is checked
    $removeImage = isset($_POST['remove_image']) && $_POST['remove_image'] == 'on';
    
    // If new image is uploaded
    if (!empty($_FILES['image']['name'])) {
        $uploadResult = imageUpload();
        if ($uploadResult !== false) {
            $image = $uploadResult;
        } else {
            $errors['image'] = 'Failed to upload image. Please try again.';
        }
    } elseif ($removeImage) {
        $image = ''; // Remove the image
    }
    
    // If no errors, proceed with update
    if (empty($errors)) {
        if (editProduct($productId, $productName, $image, $price, $categoryId)) {
            $product_message = 'Product updated successfully!';
            // Refresh product data
            $product = getProductById($productId);
            // Redirect to product list
            header("Location: listProducts.php");
            exit();
        } else {
            $product_message = 'Failed to update product. Please try again.';
        }
    } else {
        $product_message = 'Please correct the errors below.';
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
    <title>Edit Product</title>
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
        
        .table-responsive {
            background-color: white;
            border-radius: 0.35rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            padding: 1.5rem;
        }
        
        .table th {
            background-color: var(--secondary-color);
            color: #5a5c69;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
        }
        
        .product-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 0.35rem;
            border: 1px solid #e3e6f0;
        }
        
        .badge-available {
            background-color: var(--accent-color);
        }
        
        .badge-unavailable {
            background-color: var(--danger-color);
        }
        
        .action-btn {
            padding: 0.35rem 0.75rem;
            margin: 0 0.25rem;
        }
        
        .add-product-btn {
            margin-bottom: 1.5rem;
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
            
            .table-responsive {
                padding: 0.5rem;
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
            
            <div class="sidebar-divider"></div>
            
            <div class="nav flex-column">
                <a href="home.php" class="sidebar-item">
                    <i class="fas fa-home"></i>
                    <span>Home</span>
                </a>
                
                <a href="listProducts.php" class="sidebar-item">
                    <i class="fas fa-box-open"></i>
                    <span>Products</span>
                </a>
                
                <a href="users.php" class="sidebar-item">
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
            </div>
    </div>
        
        <div class="main-content">
            <div class="form-container">
                <h2 class="page-title"><i class="fas fa-edit"></i> Edit Product</h2>
                
                <?php 
                // Enhanced Alert Message Handling
                if (!empty($product_message)): 
                    // Determine alert type and icon based on message content
                    $isSuccess = strpos(strtolower($product_message), 'success') !== false || 
                                strpos(strtolower($product_message), 'updated') !== false;
                    
                    $alertClass = $isSuccess ? 'success' : 'danger';
                    $iconClass = $isSuccess ? 'fa-check-circle' : 'fa-exclamation-triangle';
                    
                    // Additional visual enhancements
                    $borderClass = $isSuccess ? 'border-success' : 'border-danger';
                    ?>
                    <div class="alert alert-<?= $alertClass ?> d-flex align-items-center border-2 <?= $borderClass ?> alert-dismissible fade show">
                        <i class="fas <?= $iconClass ?> me-3 fs-4"></i>
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
                                   value="<?= htmlspecialchars($_POST['product_name'] ?? $product['product_name']) ?>">
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
                                   value="<?= htmlspecialchars($_POST['price'] ?? $product['price']) ?>">
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
                                        id="category" name="category">
                                    <option value="">Select a category</option>
                                    <?php
                                    $categories = getCategories();
                                    $selectedCategory = $_POST['category'] ?? $product['category_id'];
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
                        
                        <?php if (!empty($product['image'])): ?>
                            <div class="mb-3">
                                <img src="<?= $product['image'] ?>" alt="Current product image" 
                                     class="img-thumbnail mb-2" style="max-height: 150px;">
                                
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
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="listProducts.php" class="btn btn-secondary me-md-2">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                        <button type="submit" name="update_product" class="btn btn-success">
                            <i class="fas fa-save me-2"></i>Update Product
                        </button>
                    </div>
                </form>
            </div>
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
        
        // Toggle image removal warning
        const removeImageCheckbox = document.getElementById('remove_image');
        if (removeImageCheckbox) {
            removeImageCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    if (true) {
                        this.checked = false;
                    }
                }
            });
        }
        
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