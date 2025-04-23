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
                        <!-- Categories will be loaded dynamically -->
                        <option value="1">Beverages</option>
                        <option value="2">Breakfast</option>
                        <option value="3">Sandwiches</option>
                        <option value="4">Salads</option>
                        <option value="5">Desserts</option>
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
                <div class="modal-body">
                    <form id="categoryForm">
                        <div class="mb-3">
                            <label for="category_name" class="form-label">Category Name</label>
                            <input type="text" class="form-control" id="category_name" name="category_name" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveCategory">Save</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    $(document).ready(function() {
        $('#saveCategory').click(function() {
            const categoryName = $('#category_name').val();
            
            if (categoryName) {
                // In a real implementation, this would make an AJAX call to your PHP backend
                // For demo purposes, we'll just add it to the dropdown
                const newId = Date.now(); // Temporary ID for demo
                $('#category').append($('<option>', {
                    value: newId,
                    text: categoryName,
                    selected: true
                }));
                
                // Reset and close modal
                $('#category_name').val('');
                $('#addCategoryModal').modal('hide');
                
                alert('In a real implementation, this would save to the database');
            }
        });
    });
    </script>
</body>
</html>

<?php
include_once('../../models/product.php');
include_once('../../models/category.php');
include_once('../../controllers/imagesUpload.php');

if (isset($_POST['add_product'])) {
    $productName = $_POST['product_name'];
    $price = $_POST['price'];
    $categoryId = $_POST['category'];
    
    // Handle image upload
    $image = imageUpload();
    
    if ($image) {
        if (addProduct($productName, $image, $price, $categoryId)) {
            echo "<script>alert('Product added successfully!');</script>";
        } else {
            echo "<script>alert('Failed to add product.');</script>";
        }
    } else {
        echo "<script>alert('Failed to upload image.');</script>";
    }
    

}
?>