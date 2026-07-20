<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once('lib/function/customerfunction.php');

$isLoggedIn   = isset($_SESSION['user']) && isset($_SESSION['usertype']) && $_SESSION['usertype'] == 'Customer';
$customerName = '';

if ($isLoggedIn) {
    $custObj = new Customer();
    $customerJson = $custObj->loaddatabyid($_SESSION['user']);

    if ($customerJson) {
        $customerData = json_decode($customerJson, true);
        $customerName = $customerData['customerName'] ?? 'Customer';
    }
}
?>
<style>
    .nav-icon {
        color: #BF9264;
        font-size: 20px;
        text-decoration: none;
    }

    .search-btn {
        background: transparent;
        border: none;
        color: #BF9264;
    }

    .search-btn:hover {
        color: dark;
    }

    .user-menu-toggle {
        display: flex;
        align-items: center;
        color: #fff;
        text-decoration: none;
    }

    .user-menu-toggle img {
        width: 32px;
        height: 32px;
        object-fit: cover;
    }

    .user-menu-toggle:hover {
        color: #BF9264;
    }
</style>

<nav class="navbar navbar-expand-lg bg-dark navbar-dark" data-bs-theme="dark" py-0>
    <?php $currentpage = basename($_SERVER['PHP_SELF']); ?>

    <div class="container-fluid">

        <!-- Logo -->
        <a class="navbar-brand py-0" href="login.php">
            <img src="assets/logo2.png" alt="Logo" height="60" style="margin-top:-10px">
        </a>

        <!-- Toggle button (for mobile) -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarColor02"
            aria-controls="navbarColor02" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Collapsible content -->
        <div class="collapse navbar-collapse" id="navbarColor02">

            <!-- Left: Navigation links -->
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?php echo $currentpage == 'shop.php' ? 'active' : '' ?>" href="shop.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $currentpage == 'products.php' ? 'active' : '' ?>" href="#">Products</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $currentpage == 'table.php' ? 'active' : '' ?>" href="table.php">Design Studio</a>
                </li>
            </ul>

            <!-- Center: Search Bar -->
            <form class="d-flex mx-auto my-2 my-lg-0 w-50" role="search">
                <input class="form-control" type="search" placeholder="Search" aria-label="Search">
                <button class="search-btn ms-2" type="submit">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </form>

            <!-- Right: Login/User dropdown -->
            <div class="d-flex align-items-center ms-lg-3 mt-2 mt-lg-0">

                <?php if ($isLoggedIn) : ?>
                    <!-- Logged-in customer: avatar + dropdown -->
                    <div class="dropdown">
                        <a href="#" class="user-menu-toggle dropdown-toggle" id="userMenuToggle" onclick="document.getElementById('userMenuList').classList.toggle('show'); return false;">
                            <img src="assets/user.png" class="rounded-circle me-2" alt="User Avatar">
                            <span class="d-none d-md-inline"><?php echo htmlspecialchars($customerName); ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" id="userMenuList">
                            <li><a class="dropdown-item" href="profile.php"><i class="fa-solid fa-user me-2"></i>Profile</a></li>
                            <li><a class="dropdown-item" href="my_orders.php"><i class="fa-solid fa-box me-2"></i>My Orders</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item text-danger" href="lib/view/logout.php"><i class="fa-solid fa-right-from-bracket me-2"></i>Sign out</a></li>
                        </ul>
                    </div>
                <?php else : ?>
                    <!-- Not logged in -->
                    <a href="login.php" class="nav-icon" title="Login">
                        <i class="fa-solid fa-user me-1"></i>
                        <span class="d-none d-md-inline">Login</span>
                    </a>
                <?php endif; ?>

            </div>
        </div>
    </div>
</nav>