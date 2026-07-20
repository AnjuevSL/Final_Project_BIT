<?php
session_start();
if (isset($_SESSION['user']) && isset($_SESSION['usertype']) && $_SESSION['usertype'] == "Admin") {
  $currentpage = 'supplier_report.php';
} else {
  header('Location:../../login.php');
  exit();
}

include_once('../function/reportfunction.php');

$repObj = new Report();

$status = $_GET['status'] ?? null;

$suppliers = $repObj->getSupplierReport($status);
$summary   = $repObj->getSupplierReportSummary($status);
?>
<!doctype html>
<html lang="en">

<head>
  <title>Suppliers Report</title>
  <?php include_once('common.php'); ?>
</head>

<body>
  <main class="app-main">
    <div class="app-content-header mt-3">
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-6">
            <h3 class="mb-0">Suppliers Report</h3>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-end">
              <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
              <li class="breadcrumb-item active">Suppliers Report</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <div class="app-content">
      <div class="container-fluid">

        <!-- Summary cards -->
        <!-- <div class="row mb-3">
          <div class="col-md-4 col-sm-6">
            <div class="card text-bg-primary">
              <div class="card-body">
                <h6 class="mb-1">Total Suppliers</h6>
                <h3 class="mb-0"><?php echo (int) $summary['total_suppliers']; ?></h3>
              </div>
            </div>
          </div>
          <div class="col-md-4 col-sm-6">
            <div class="card text-bg-success">
              <div class="card-body">
                <h6 class="mb-1">Active</h6>
                <h3 class="mb-0"><?php echo (int) $summary['active_count']; ?></h3>
              </div>
            </div>
          </div>
          <div class="col-md-4 col-sm-6">
            <div class="card text-bg-secondary">
              <div class="card-body">
                <h6 class="mb-1">Inactive</h6>
                <h3 class="mb-0"><?php echo (int) $summary['inactive_count']; ?></h3>
              </div>
            </div>
          </div>
        </div> -->

        <!-- Filters -->
        <div class="card mb-3">
          <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
              <div class="col-md-3">
                <label class="form-label mb-1">Status</label>
                <select name="status" class="form-select form-select-sm">
                  <option value="">All</option>
                  <option value="1" <?php echo ($status === '1') ? 'selected' : ''; ?>>Active</option>
                  <option value="0" <?php echo ($status === '0') ? 'selected' : ''; ?>>Inactive</option>
                </select>
              </div>
              <div class="col-md-9 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-funnel"></i> Filter</button>
                <a href="supplier_report.php" class="btn btn-secondary btn-sm">Reset</a>
                <a href="export_supplier_report.php?status=<?php echo urlencode($status ?? ''); ?>"
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
              <h4 class="mb-0">All Suppliers</h4>
              <input type="text" id="searchSupplier" class="form-control form-control-sm" style="max-width:250px" placeholder="Search on this page...">
            </div>
            <div class="table-responsive">
              <table class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>Supplier ID</th>
                    <th>Supplier Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Products Supplied</th>
                    <th>Status</th>
                    <th>Created At</th>
                  </tr>
                </thead>
                <tbody id="supplierlist">
                  <?php if (!empty($suppliers)) : ?>
                    <?php foreach ($suppliers as $sup) : ?>
                      <tr>
                        <td><?php echo htmlspecialchars($sup['supplierid']); ?></td>
                        <td><?php echo htmlspecialchars($sup['supplierName']); ?></td>
                        <td><?php echo htmlspecialchars($sup['email'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($sup['phone'] ?? '-'); ?></td>
                        <td><span class="badge bg-info text-dark"><?php echo (int) $sup['product_count']; ?></span></td>
                        <td>
                          <?php if ($sup['d_status'] == 1) : ?>
                            <span class="badge bg-success">Active</span>
                          <?php else : ?>
                            <span class="badge bg-secondary">Inactive</span>
                          <?php endif; ?>
                        </td>
                        <td><?php echo date('Y-m-d', strtotime($sup['created_at'])); ?></td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else : ?>
                    <tr>
                      <td colspan="7" class="text-center text-muted">No suppliers found for the selected filters.</td>
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
    document.getElementById('searchSupplier').addEventListener('keyup', function() {
      const filter = this.value.toLowerCase();
      document.querySelectorAll('#supplierlist tr').forEach(function(row) {
        row.style.display = row.textContent.toLowerCase().includes(filter) ? '' : 'none';
      });
    });
  </script>
  <?php include_once('footer.php'); ?>
</body>

</html>