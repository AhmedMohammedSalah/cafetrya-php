<?php
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My orders </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-dark navbar-custom mb-4">
    <div class="container">
      <a class="navbar-brand" href="#"><i class="fas fa-mug-hot"></i> Coffee Shop</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
     
        
      </div>
    </div>
  </nav>

  <div class="container mb-5">
    <h1 class="mb-4"><i class="fas fa-clipboard-list me-2"></i>My Orders</h1>

    <div class="filter-form">
      <form class="row g-3 align-items-end" method="get">
        <div class="col-md-4 col-sm-6">
          <label for="date_from" class="form-label"><i class="far fa-calendar-alt me-2"></i>Date from</label>
          <input type="date" id="date_from" name="date_from" class="form-control" value="<?= htmlspecialchars($from) ?>">
        </div>
        <div class="col-md-4 col-sm-6">
          <label for="date_to" class="form-label"><i class="far fa-calendar-alt me-2"></i>Date to</label>
          <input type="date" id="date_to" name="date_to" class="form-control" value="<?= htmlspecialchars($to) ?>">
        </div>
        <div class="col-md-4 col-sm-12 d-flex gap-2">
          <button type="submit" class="btn btn-coffee flex-grow-1">
            <i class="fas fa-filter me-2"></i>Filter
          </button>
          <a href="orders.php" class="btn btn-outline-secondary flex-grow-1">
            <i class="fas fa-redo me-2"></i>Reset
          </a>
        </div>
      </form>
    </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  
</body>
</html>