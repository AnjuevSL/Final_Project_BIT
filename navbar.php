<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/fontawesome-free-7.1.0-web/css/all.min.css">
    <style>
        .nav-icon{
    color: #BF9264;
    font-size: 20px;
    text-decoration: none;
}
.search-btn{
    background: transparent;
    border: none;
    color:#BF9264; /* brown */
    font-size: 20px;
}
.search-btn:hover{
    color: dark;/* lighter brown on hover */
}
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-dark navbar-dark" data-bs-theme="dark" py-0>
        <?php $currentpage = basename($_SERVER['PHP_SELF']); ?>

        <div class="container-fluid">

            <!-- Logo -->
            <a class="navbar-brand py-0" href="index.php">
                <img src="assets/logo2.png" alt="Logo" height="60" style="margin-top:-10px" >
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
                        <a class="nav-link <?php echo $currentpage == 'index.php' ? 'active' : '' ?>" href="index.php">Home</a>
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

                <!-- Right: Login + Cart Icons -->
                <div class="d-flex align-items-center ms-lg-3 mt-2 mt-lg-0">
                    <a href="login.php" class="nav-icon me-3" title="Login">
                        <i class="fa-solid fa-user"></i>
                    </a>
                    <!-- <a href="cart.php" class="nav-icon" title="Cart">
                        <i class="fa-solid fa-cart-shopping"></i>
                    </a> -->
                </div>
            </div>
        </div>
    </nav>
</body>
</html>
