<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h2>Add New Product</h2>
        <?php if (isset($product_message)): ?>
            <div class="alert alert-info"><?= $product_message ?></div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="product_name" class="form-label">Product Name</label>
                <input type="text" class="form-control" id="product_name" name="product_name" required>
            </div>
            
            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="number" step="0.01" class="form-control" id="price" name="price" required>
            </div>
            
            <div class="mb-3 row">
                <div class="col-md-10">
                    <label for="category" class="form-label">Category</label>
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
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                        Add Category
                    </button>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="image" class="form-label">Product Image</label>
                <input type="file" class="form-control" id="image" name="image">
            </div>
            
            <button type="submit" name="add_product" class="btn btn-success">Add Product</button>
        </form>
    </div>

    <!-- Add Category Modal -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCategoryModalLabel">Add New Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" id="categoryForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="category_name" class="form-label">Category Name</label>
                            <input type="text" class="form-control" id="category_name" name="category_name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="save_category">Save</button>
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
        
        // If we have a category message, show the modal
        <?php if (isset($category_message) && isset($_POST['save_category'])): ?>
            $('#addCategoryModal').modal('hide');
            alert('<?= addslashes($category_message) ?>');
        <?php endif; ?>
    });
    </script>
</body>
</html>

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
}
?>