<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
     <link rel="stylesheet" href="css/index.css">
</head>

<body>
     <?php
     include_once('navbar.php')
     ?>
 
 <div id="carouselIndicators" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000">
  <div class="carousel-indicators">
    <button type="button" data-bs-target="#carouselIndicators" data-bs-slide-to="0" class="active"></button>
    <button type="button" data-bs-target="#carouselIndicators" data-bs-slide-to="1"></button>
    <button type="button" data-bs-target="#carouselIndicators" data-bs-slide-to="2"></button>
  </div>
  
  <div class="carousel-inner">
    <div class="carousel-item active">
      <img src="assets/Banner Image 1.webp" class="d-block w-100 banner-img" alt="Banner 1">
      <div class="carousel-caption">
        <h1>Explore Our Fashion Collection</h1>
      </div>
    </div>
    <div class="carousel-item">
      <img src="assets/Banner Image 2.webp" class="d-block w-100 banner-img" alt="Banner 2">
      <div class="carousel-caption">
        <h1>Discover New Trends</h1>
      </div>
    </div>
    <div class="carousel-item">
      <img src="assets/Banner Image 3.webp" class="d-block w-100 banner-img" alt="Banner 3">
      <div class="carousel-caption">
        <h1>Shop Your Style</h1>
      </div>
    </div>
  </div>

  <button class="carousel-control-prev" type="button" data-bs-target="#carouselIndicators" data-bs-slide="prev">
    <span class="carousel-control-prev-icon"></span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#carouselIndicators" data-bs-slide="next">
    <span class="carousel-control-next-icon"></span>
  </button>
</div>

<div class="container mt-5">

    <!-- Section Title -->
    <h2 class="text-center mb-4">Featured Products</h2>

    <div class="row">

        <!-- Product 1 -->
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card h-100">
                <img src="assets/productImages/pattern frock green.webp" class="card-img-top" alt="Watch">
                <div class="card-body text-center">
                    <h5 class="card-title">Green Pattern Summer Frock</h5>
                    <p class="card-text text-success fw-bold">Rs.4200.00</p>
                    <a href="#" class="btn btn-dark w-100">Add to Cart</a>
                </div>
            </div>
        </div>

        <!-- Product 2 -->
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card h-100">
                <img src="assets/productImages/denim shirt.webp" class="card-img-top" alt="Watch">
                <div class="card-body text-center">
                    <h5 class="card-title">Classic Denim Shirt</h5>
                    <p class="card-text text-success fw-bold">Rs.4000.00</p>
                    <a href="#" class="btn btn-dark w-100">Add to Cart</a>
                </div>
            </div>
        </div>

        <!-- Product 3 -->
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card h-100">
                <img src="assets/productImages/frock green.webp" class="card-img-top" alt="Watch">
                <div class="card-body text-center">
                    <h5 class="card-title">Elegant Green Casual Frock</h5>
                    <p class="card-text text-success fw-bold">Rs.3500.00</p>
                    <a href="#" class="btn btn-dark w-100">Add to Cart</a>
                </div>
            </div>
        </div>

        <!-- Product 4 -->
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card h-100">
                <img src="assets/productImages/check shirt.webp" class="card-img-top" alt="Watch">
                <div class="card-body text-center">
                    <h5 class="card-title">Trendy Checkered Shirt</h5>
                    <p class="card-text text-success fw-bold">Rs.2500.00</p>
                    <a href="#" class="btn btn-dark w-100">Add to Cart</a>
                </div>
            </div>
        </div>

    </div>

</div>

<?php include_once('footer.php'); ?>


<script src="js/bootstrap.bundle.min.js"></script>

</body>
</html>