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
            --border-radius: 0.35rem;
        }
        
        body {
            background-color: #f8f9fc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
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
        
        <div class="sidebar-divider"></div>
        
        <div class="nav flex-column">
            <a href="index.php" class="sidebar-item">
                <i class="fas fa-home"></i>
                <span>Home</span>
            </a>
            
            <a href="products.php" class="sidebar-item">
                <i class="fas fa-box-open"></i>
                <span>Products</span>
            </a>
            
            <a href="users.php" class="sidebar-item">
                <i class="fas fa-users"></i>
                <span>Users</span>
            </a>
            
            <a href="manual_order.php" class="sidebar-item active">
                <i class="fas fa-cart-plus"></i>
                <span>Manual Order</span>
            </a>
            
            <a href="checks.php" class="sidebar-item">
                <i class="fas fa-file-invoice-dollar"></i>
                <span>Checks</span>
            </a>
            
            <a href="unfinshedOrders.php" class="sidebar-item ">
                <i class="fas fa-clipboard-list"></i>
                <span>Pending Orders</span>
            </a>
        </div>
    </div>
        <div class="form-container">
            <h2 class="page-title"><i class="fas fa-plus-circle"></i> Add New Product</h2>
            
            <?php if (isset($product_message)): ?>
                <div class="alert alert-info d-flex align-items-center">
                    <i class="fas fa-info-circle me-2"></i>
                    <?= $product_message ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-4">
                    <label for="product_name" class="form-label"><i class="fas fa-tag me-2"></i>Product Name</label>
                    <div class="input-icon">
                        <input type="text" class="form-control" id="product_name" name="product_name" required>
                        <i class="fas fa-edit"></i>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="price" class="form-label"><i class="fas fa-dollar-sign me-2"></i>Price</label>
                    <div class="input-icon">
                        <input type="number" step="0.01" class="form-control" id="price" name="price" required>
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                </div>
                
                <div class="mb-4 row">
                    <div class="col-md-9">
                        <label for="category" class="form-label"><i class="fas fa-list-alt me-2"></i>Category</label>
                        <div class="input-icon">
                            <select class="form-select" id="category" name="category" required>
                                <option value="">Select a category</option>
                                <?php
                                include(__DIR__ .'/../../models/category.php');
                                $categories = getCategories();
                                foreach ($categories as $category) {
                                    $selected = ($category['id'] == $selected_category) ? 'selected' : '';
                                    echo "<option value='{$category['id']}' $selected>{$category['category_name']}</option>";
                                }
                                ?>
                            </select>
                            <i class="fas fa-chevron-down"></i>
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
                    <div class="file-upload-container">
                        <label class="file-upload-label">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span>Click to upload or drag and drop</span>
                            <input type="file" class="file-upload-input" id="image" name="image">
                        </label>
                    </div>
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
                        <div class="mb-3">
                            <label for="category_name" class="form-label"><i class="fas fa-tag me-2"></i>Category Name</label>
                            <input type="text" class="form-control" id="category_name" name="category_name" required>
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
    $(document).ready(function() {
        // Clear category form when modal closes
        $('#addCategoryModal').on('hidden.bs.modal', function () {
            $('#category_name').val('');
        });
        
        // Show file name when image is selected
        $('#image').change(function() {
            var fileName = $(this).val().split('\\').pop();
            if (fileName) {
                $('.file-upload-container span').html(fileName);
                $('.file-upload-container i').removeClass('fa-cloud-upload-alt').addClass('fa-check-circle');
                $('.file-upload-container').css({
                    'border-color': '#1cc88a',
                    'background-color': 'rgba(28, 200, 138, 0.05)'
                });
            }
        });
        
        // If we have a category message, show the modal
        <?php if (isset($category_message) && isset($_POST['save_category'])): ?>
            $('#addCategoryModal').modal('hide');
            alert('<?= addslashes($category_message) ?>');
        <?php endif; ?>
    });

    </script>

<?php
// Initialize variables
$product_message = '';
$category_message = '';
$selected_category = '';

// Include necessary files
include_once(__DIR__ .'/../../models/product.php');
include_once(__DIR__ .'/../../models/category.php');
include_once(__DIR__ .'/../../controllers/imagesUpload.php');

// Handle product submission
if (isset($_POST['add_product'])) {
    $productName = $_POST['product_name'];
    $price = $_POST['price'];
    $categoryId = $_POST['category'];
    
    // Handle image upload
    $image = imageUpload();
    
    if ($image) {
        if (addProduct($productName, $image, $price, $categoryId)) {
            $product_message = 'Product added successfully!';
            // Clear form fields
            $_POST = array();
        } else {
            $product_message = 'Failed to add product.';
            $selected_category = $categoryId; // Keep the selected category
        }
    } else {
        $product_message = 'Failed to upload image.';
        $selected_category = $categoryId; // Keep the selected category
    }
}

// Handle category submission
if (isset($_POST['save_category'])) {
    $categoryName = $_POST['category_name'];
    
    if (addCategory($categoryName)) {
        $category_message = 'Category added successfully!';
        $categories = getCategories();

    } else {
        $category_message = 'Failed to add category.';
    }

    // Prevent form resubmission
    echo "<script>window.history.replaceState(null, null, window.location.href);</script>";
    exit();
}?>
</body>
</html>