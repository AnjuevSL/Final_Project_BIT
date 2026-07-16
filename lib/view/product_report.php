<?php
session_start();
if (isset($_SESSION['user']) && isset($_SESSION['usertype']) && $_SESSION['usertype'] == "Admin") {
    $currentpage = 'product_report.php';
} else {
    header('Location:../../login.php');
    exit();
}

include_once('../function/reportfunction.php');

$repObj = new Report();

$category = $_GET['category'] ?? null;
$supplier = $_GET['supplier'] ?? null;
$status   = $_GET['status'] ?? null;

$products      = $repObj->getProductReport($category, $supplier, $status);
$summary       = $repObj->getProductReportSummary($category, $supplier, $status);
$categoryList  = $repObj->getAllCategoriesList();
$supplierList  = $repObj->getAllSuppliersList();
?>
<!doctype html>
<html lang="en">
<head>
  <title>Product Report</title>
  <?php include_once('common.php'); ?>
</head>
<body>
  <main class="app-main">
    <div class="app-content-header mt-3">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6"><h3 class="mb-0">Product Report</h3></div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-end">
              <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
              <li class="breadcrumb-item active">Product Report</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <div class="app-content">
      <div class="container-fluid">

        <!-- Summary cards -->
        <!-- <div class="row mb-3">
          <div class="col-md-3 col-sm-6">
            <div class="card text-bg-primary">
              <div class="card-body">
                <h6 class="mb-1">Total Products</h6>
                <h3 class="mb-0"><?php echo (int) $summary['total_products']; ?></h3>
              </div>
            </div>
          </div>
          <div class="col-md-3 col-sm-6">
            <div class="card text-bg-success">
              <div class="card-body">
                <h6 class="mb-1">Active</h6>
                <h3 class="mb-0"><?php echo (int) $summary['active_count']; ?></h3>
              </div>
            </div>
          </div>
          <div class="col-md-3 col-sm-6">
            <div class="card text-bg-secondary">
              <div class="card-body">
                <h6 class="mb-1">Inactive</h6>
                <h3 class="mb-0"><?php echo (int) $summary['inactive_count']; ?></h3>
              </div>
            </div>
          </div>
          <div class="col-md-3 col-sm-6">
            <div class="card text-bg-warning">
              <div class="card-body">
                <h6 class="mb-1">Avg. Price</h6>
                <h3 class="mb-0">Rs. <?php echo number_format($summary['avg_price'], 2); ?></h3>
              </div>
            </div>
          </div>
        </div> -->

        <!-- Filters -->
        <div class="card mb-3">
          <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
              <div class="col-md-3">
                <label class="form-label mb-1">Category</label>
                <select name="category" class="form-select form-select-sm">
                  <option value="">All Categories</option>
                  <?php foreach ($categoryList as $c) : ?>
                    <option value="<?php echo htmlspecialchars($c['categoryid']); ?>" <?php echo ($category === $c['categoryid']) ? 'selected' : ''; ?>>
                      <?php echo htmlspecialchars($c['categoryName']); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-md-3">
                <label class="form-label mb-1">Supplier</label>
                <select name="supplier" class="form-select form-select-sm">
                  <option value="">All Suppliers</option>
                  <?php foreach ($supplierList as $s) : ?>
                    <option value="<?php echo htmlspecialchars($s['supplierid']); ?>" <?php echo ($supplier === $s['supplierid']) ? 'selected' : ''; ?>>
                      <?php echo htmlspecialchars($s['supplierName']); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-md-2">
                <label class="form-label mb-1">Status</label>
                <select name="status" class="form-select form-select-sm">
                  <option value="">All</option>
                  <option value="1" <?php echo ($status === '1') ? 'selected' : ''; ?>>Active</option>
                  <option value="0" <?php echo ($status === '0') ? 'selected' : ''; ?>>Inactive</option>
                </select>
              </div>
              <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-funnel"></i> Filter</button>
                <a href="product_report.php" class="btn btn-secondary btn-sm">Reset</a>
                <a href="export_product_report.php?category=<?php echo urlencode($category ?? ''); ?>&supplier=<?php echo urlencode($supplier ?? ''); ?>&status=<?php echo urlencode($status ?? ''); ?>"
                   class="btn btn-success btn-sm ms-auto">
                  <i class="bi bi-file-earmark-excel"></i> Export to Excel
                </a>
              </div>
            </form>
          </div>
        </div>

        <!-- Table -->
        <div class="row">
          <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h4 class="mb-0">All Products</h4>
              <input type="text" id="searchProduct" class="form-control form-control-sm" style="max-width:250px" placeholder="Search on this page...">
            </div>
            <div class="table-responsive">
              <table class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>Product ID</th>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Supplier</th>
                    <th>Price (Rs.)</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody id="productlist">
                  <?php if (!empty($products)) : ?>
                    <?php foreach ($products as $product) : ?>
                      <tr>
                        <td><?php echo htmlspecialchars($product['productid']); ?></td>
                        <td><?php echo htmlspecialchars($product['productName']); ?></td>
                        <td><?php echo htmlspecialchars($product['categoryName'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($product['supplierName'] ?? 'N/A'); ?></td>
                        <td><?php echo number_format($product['price'], 2); ?></td>
                        <td>
                          <?php if ($product['d_status'] == 1) : ?>
                            <span class="badge bg-success">Active</span>
                          <?php else : ?>
                            <span class="badge bg-secondary">Inactive</span>
                          <?php endif; ?>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else : ?>
                    <tr>
                      <td colspan="6" class="text-center text-muted">No products found for the selected filters.</td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

      </div>
    </div>
  </main>
  <script src="../../js/jquery.js"></script>
  <script>
    document.getElementById('searchProduct').addEventListener('keyup', function () {
      const filter = this.value.toLowerCase();
      document.querySelectorAll('#productlist tr').forEach(function (row) {
        row.style.display = row.textContent.toLowerCase().includes(filter) ? '' : 'none';
      });
    });
  </script>
  <?php include_once('footer.php'); ?>
</body>
</html>