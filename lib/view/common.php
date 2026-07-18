<?php
// Include the Main class (adjust path if your folder structure differs)
include_once(__DIR__ . '/../function/main.php');

$currentpage = basename($_SERVER['PHP_SELF']);

// Ensure session is started (safe check in case this file is included
// before session_start() runs in the parent page)
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Fetch the logged-in user's details for the navbar
$logged_user = null;
if (isset($_SESSION['user'])) {
  $header_obj  = new Main();
  $header_conn = $header_obj->dbResult;
  $header_uid  = $header_conn->real_escape_string($_SESSION['user']);

  $header_sql = "SELECT loginid, loginEmail, loginRole FROM login_tbl WHERE loginid = '$header_uid'";
  $header_res = $header_conn->query($header_sql);

  if ($header_res && $header_res->num_rows > 0) {
    $logged_user = $header_res->fetch_assoc();
  }
}

// Fallback-safe values for navbar display
$nav_username = htmlspecialchars($logged_user['loginEmail'] ?? 'Guest User');
$nav_email    = htmlspecialchars($logged_user['loginEmail'] ?? '');
$nav_role     = htmlspecialchars($logged_user['loginRole'] ?? 'Admin');

// Using default avatar image for all users
$nav_image = '../../assets/woman.png';
?>

<!--begin::Fonts-->
<link
  rel="stylesheet"
  href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
  integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q="
  crossorigin="anonymous"
  media="print"
  onload="this.media = 'all'" />
<!--end::Fonts-->

<!--begin::Third Party Plugin(Bootstrap Icons)-->
<link
  rel="stylesheet"
  href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
  crossorigin="anonymous" />
<!--end::Third Party Plugin(Bootstrap Icons)-->

<!--begin::Required Plugin(AdminLTE)-->
<link rel="stylesheet" href="../../css/adminlte.css" />

<script src="../../js/jquery.js"></script>


</head>
<!--end::Head-->
<!--begin::Body-->

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
  <!--begin::App Wrapper-->
  <div class="app-wrapper">
    <!--begin::Header-->
    <nav class="app-header navbar navbar-expand bg-body">
      <!--begin::Container-->
      <div class="container-fluid">
        <!--begin::Start Navbar Links-->
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
              <i class="bi bi-list"></i>
            </a>
          </li>
          <li class="nav-item d-none d-md-block">
            <a href="dashboard.php" class="nav-link">Home</a>
          </li>
          <li class="nav-item d-none d-md-block">
            <a href="contact.php" class="nav-link">Contact</a>
          </li>
        </ul>
        <!--end::Start Navbar Links-->

        <!--begin::End Navbar Links-->
        <ul class="navbar-nav ms-auto">
          <!--begin::Navbar Search-->
          <li class="nav-item">
            <a class="nav-link" data-widget="navbar-search" href="#" role="button">
              <input type="text" id="searchtext" class="form-control">
              <i class="bi bi-search"></i>
            </a>
          </li>
          <!--end::Navbar Search-->

          <!--begin::Messages Dropdown Menu-->
          <li class="nav-item dropdown">
            <a class="nav-link" data-bs-toggle="dropdown" href="#">
              <i class="bi bi-chat-text"></i>
              <span class="navbar-badge badge text-bg-danger">3</span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
              <a href="#" class="dropdown-item">
                <!--begin::Message-->
                <div class="d-flex">
                  <div class="flex-shrink-0">
                    <img
                      src="../../assets/woman.png"
                      alt="User Avatar"
                      class="img-size-50 rounded-circle me-3" />
                  </div>
                  <div class="flex-grow-1">
                    <h3 class="dropdown-item-title">
                      Brad Diesel
                      <span class="float-end fs-7 text-danger"><i class="bi bi-star-fill"></i></span>
                    </h3>
                    <p class="fs-7">Call me whenever you can...</p>
                    <p class="fs-7 text-secondary">
                      <i class="bi bi-clock-fill me-1"></i> 4 Hours Ago
                    </p>
                  </div>
                </div>
                <!--end::Message-->
              </a>
              <div class="dropdown-divider"></div>
              <a href="#" class="dropdown-item">
                <!--begin::Message-->
                <div class="d-flex">
                  <div class="flex-shrink-0">
                    <img
                      src="../../assets/woman.png"
                      alt="User Avatar"
                      class="img-size-50 rounded-circle me-3" />
                  </div>
                  <div class="flex-grow-1">
                    <h3 class="dropdown-item-title">
                      John Pierce
                      <span class="float-end fs-7 text-secondary">
                        <i class="bi bi-star-fill"></i>
                      </span>
                    </h3>
                    <p class="fs-7">I got your message bro</p>
                    <p class="fs-7 text-secondary">
                      <i class="bi bi-clock-fill me-1"></i> 4 Hours Ago
                    </p>
                  </div>
                </div>
                <!--end::Message-->
              </a>
              <div class="dropdown-divider"></div>
              <a href="#" class="dropdown-item">
                <!--begin::Message-->
                <div class="d-flex">
                  <div class="flex-shrink-0">
                    <img
                      src="./assets/img/user3-128x128.jpg"
                      alt="User Avatar"
                      class="img-size-50 rounded-circle me-3" />
                  </div>
                  <div class="flex-grow-1">
                    <h3 class="dropdown-item-title">
                      Nora Silvester
                      <span class="float-end fs-7 text-warning">
                        <i class="bi bi-star-fill"></i>
                      </span>
                    </h3>
                    <p class="fs-7">The subject goes here</p>
                    <p class="fs-7 text-secondary">
                      <i class="bi bi-clock-fill me-1"></i> 4 Hours Ago
                    </p>
                  </div>
                </div>
                <!--end::Message-->
              </a>
              <div class="dropdown-divider"></div>
              <a href="#" class="dropdown-item dropdown-footer">See All Messages</a>
            </div>
          </li>
          <!--end::Messages Dropdown Menu-->

          <!--begin::Notifications Dropdown Menu-->
          <!-- <li class="nav-item dropdown">
               <a class="nav-link" data-bs-toggle="dropdown" href="#">
                 <i class="bi bi-bell-fill"></i>
                 <span class="navbar-badge badge text-bg-warning">15</span>
               </a>
               <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                 <span class="dropdown-item dropdown-header">15 Notifications</span>
                 <div class="dropdown-divider"></div>
                 <a href="#" class="dropdown-item">
                   <i class="bi bi-envelope me-2"></i> 4 new messages
                   <span class="float-end text-secondary fs-7">3 mins</span>
                 </a>
                 <div class="dropdown-divider"></div>
                 <a href="#" class="dropdown-item">
                   <i class="bi bi-people-fill me-2"></i> 8 friend requests
                   <span class="float-end text-secondary fs-7">12 hours</span>
                 </a>
                 <div class="dropdown-divider"></div>
                 <a href="#" class="dropdown-item">
                   <i class="bi bi-file-earmark-fill me-2"></i> 3 new reports
                   <span class="float-end text-secondary fs-7">2 days</span>
                 </a>
                 <div class="dropdown-divider"></div>
                 <a href="#" class="dropdown-item dropdown-footer"> See All Notifications </a>
               </div>
             </li> -->
          <!--end::Notifications Dropdown Menu-->

          <!--begin::Fullscreen Toggle-->
          <li class="nav-item">
            <a class="nav-link" href="#" data-lte-toggle="fullscreen">
              <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i>
              <i data-lte-icon="minimize" class="bi bi-fullscreen-exit" style="display: none"></i>
            </a>
          </li>
          <!--end::Fullscreen Toggle-->

          <!--begin::User Menu Dropdown-->
          <!--begin::User Menu Dropdown-->
          <li class="nav-item dropdown user-menu">
            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
              <img
                src="<?php echo $nav_image; ?>"
                class="user-image rounded-circle shadow"
                alt="User Image" />
              <span class="d-none d-md-inline"><?php echo $nav_username; ?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
              <!--begin::User Image-->
              <li class="user-header text-bg-primary">
                <img
                  src="<?php echo $nav_image; ?>"
                  class="rounded-circle shadow"
                  alt="User Image" />
                <p>
                  <?php echo $nav_username; ?> - <?php echo $nav_role; ?>
                  <small><?php echo $nav_email; ?></small>
                </p>
              </li>
              <!--end::User Image-->

              <!--begin::Menu Footer-->
              <li class="user-footer">
                <a href="profile.php" class="btn btn-outline-secondary">Profile</a>
                <a href="logout.php" class="btn btn-outline-danger float-end">Sign out</a>
              </li>
              <!--end::Menu Footer-->
            </ul>
          </li>
          <!--end::User Menu Dropdown-->
        </ul>
        <!--end::End Navbar Links-->
      </div>
      <!--end::Container-->
    </nav>
    <!--end::Header-->

    <!--begin::Sidebar-->
    <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
      <!--begin::Sidebar Brand-->
      <div class="sidebar-brand">
        <!--begin::Brand Link-->
        <a href="./index.html" class="brand-link">
          <!--begin::Brand Image-->
          <img
            src="../../assets/logo2.png"
            alt="AdminLTE Logo"
            class="brand-image opacity-75 shadow" />
          <!--end::Brand Image-->
          <!--begin::Brand Text-->
          <span class="brand-text fw-light">Malee Dress Point</span>
          <!--end::Brand Text-->
        </a>
        <!--end::Brand Link-->
      </div>
      <!--end::Sidebar Brand-->
      <!--begin::Sidebar Wrapper-->
      <div class="sidebar-wrapper">
        <nav class="mt-2">
          <!--begin::Sidebar Menu-->
          <ul
            class="nav sidebar-menu flex-column"
            data-lte-toggle="treeview"
            role="navigation"
            aria-label="Main navigation"
            data-accordion="false"
            id="navigation">

            <!-- dashboard -->
            <li class="nav-item menu-open mb-3">
              <a href="dashboard.php" class="nav-link <?php echo $currentpage == 'dashboard.php' ? 'active' : ''; ?>">
                <i class="nav-icon bi bi-speedometer2"></i>
                <p>Dashboard</p>
              </a>
            </li>

            <!-- Management -->
            <li class="nav-item menu-open mb-3">
              <a href="#" class="nav-link active">
                <i class="nav-icon bi bi-gear-fill"></i>
                <p>
                  Management Options
                  <i class="nav-arrow bi bi-chevron-right"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="order.php" class="nav-link" <?php echo $currentpage == 'order.php][' ? 'active' : ''; ?>>
                    <i class="nav-icon bi bi-cart-check-fill"></i>
                    <p>Orders Management</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="product.php" class="nav-link" <?php echo $currentpage == 'product.php][' ? 'active' : ''; ?>>
                    <i class="nav-icon bi bi-box-seam-fill"></i>
                    <p>Product Management</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="category.php" class="nav-link" <?php echo $currentpage == 'category.php][' ? 'active' : ''; ?>>
                    <i class="nav-icon bi bi-tags-fill"></i>
                    <p>Category Management</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="supplier.php" class="nav-link" <?php echo $currentpage == 'supplier.php][' ? 'active' : ''; ?>>
                    <i class="nav-icon bi bi-truck"></i>
                    <p>Suppliers Management</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="customer.php" class="nav-link" <?php echo $currentpage == 'customer.php][' ? 'active' : ''; ?>>
                    <i class="nav-icon bi bi-truck"></i>
                    <p>Customer Management</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="user.php" class="nav-link" <?php echo $currentpage == 'user.php' ? 'active' : ''; ?>>
                    <i class="nav-icon bi bi-people-fill"></i>
                    <p>User Managemnt</p>
                  </a>
                </li>
              </ul>
            </li>

            <!-- Reporting -->
            <li class="nav-item <?php echo (in_array($currentpage, ['order_report.php', 'product_report.php', 'category_report.php', 'supplier_report.php'])) ? 'menu-open' : ''; ?> mb-3">
              <a href="#" class="nav-link <?php echo (in_array($currentpage, ['order_report.php', 'product_report.php', 'category_report.php', 'supplier_report.php'])) ? 'active' : ''; ?>">
                <i class="nav-icon bi bi-gear-fill"></i>
                <p>
                  Report Options
                  <i class="nav-arrow bi bi-chevron-right"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="order_report.php" class="nav-link <?php echo ($currentpage == 'order_report.php') ? 'active' : ''; ?>">
                    <i class="nav-icon bi bi-cart-check-fill"></i>
                    <p>Order Report</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="product_report.php" class="nav-link <?php echo ($currentpage == 'product_report.php') ? 'active' : ''; ?>">
                    <i class="nav-icon bi bi-box-seam-fill"></i>
                    <p>Product Report</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="category_report.php" class="nav-link <?php echo ($currentpage == 'category_report.php') ? 'active' : ''; ?>">
                    <i class="nav-icon bi bi-tags-fill"></i>
                    <p>Category Report</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="supplier_report.php" class="nav-link <?php echo ($currentpage == 'supplier_report.php') ? 'active' : ''; ?>">
                    <i class="nav-icon bi bi-truck"></i>
                    <p>Suppliers Report</p>
                  </a>
                </li>
              </ul>
            </li>

            <!-- Theme -->
            <!-- <li class="nav-item mb-3">
                 <a href="./generate/theme.html" class="nav-link">
                   <i class="nav-icon bi bi-palette"></i>
                   <p>Theme Generate</p>
                 </a>
               </li> -->

            <!-- lables -->
            <li class="nav-item menu-open  mb-3">
              <!-- <li class="nav-header">LABELS</li> -->
              <a href="#" class="nav-link active">
                <i class="nav-icon bi bi-gear-fill"></i>
                <p>
                  Labels
                  <i class="nav-arrow bi bi-chevron-right"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="#" class="nav-link">
                    <i class="nav-icon bi bi-circle text-danger"></i>
                    <p class="text">Important</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="#" class="nav-link">
                    <i class="nav-icon bi bi-circle text-warning"></i>
                    <p>Warning</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="#" class="nav-link">
                    <i class="nav-icon bi bi-circle text-info"></i>
                    <p>Informational</p>
                  </a>
                </li>
              </ul>
            </li>
          </ul>
          <!--end::Sidebar Menu-->
          <hr>
          <a href="logout.php"><button type="button" href="" class="btn btn-secondary">logout</button></a>

        </nav>
      </div>
      <!--end::Sidebar Wrapper-->
    </aside>
    <!--end::Sidebar-->