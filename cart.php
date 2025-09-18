<?php
include "./config/connection.php";
include './process/produk.php';
include "./process/cart.php";
session_start();

$cartItems = getCartItems($conn);
$cartItemsQty = getCartItemsQty($conn);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500&family=Poppins:wght@200;600;700&display=swap"
        rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/97e48f7299.js" crossorigin="anonymous"></script>

    <!-- Libraries Stylesheet -->
    <link href="assets/lib/animate/animate.min.css" rel="stylesheet">
    <link href="assets/lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="assets/css/style.css" rel="stylesheet">

    <style>
        .cart-wrapper {
            background-color: #f8f9fa;
            min-height: 100vh;
            padding: 40px 0;
        }

        .product-card {
            background: white;
            border-radius: 12px;
            transition: transform 0.2s;
        }

        .product-card:hover {
            transform: translateY(-2px);
        }

        .quantity-input {
            width: 60px;
            text-align: center;
            border: 1px solid #dee2e6;
            border-radius: 6px;
        }

        .product-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
        }

        .summary-card {
            background: white;
            border-radius: 12px;
            position: sticky;
            top: 20px;
        }

        .checkout-btn {
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            border: none;
            transition: transform 0.2s;
        }

        .checkout-btn:hover {
            transform: translateY(-2px);
            background: linear-gradient(135deg, #4f46e5, #4338ca);
        }

        .remove-btn {
            color: #dc2626;
            cursor: pointer;
            transition: all 0.2s;
        }

        .remove-btn:hover {
            color: #991b1b;
        }

        .quantity-btn {
            width: 28px;
            height: 28px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            background: #f3f4f6;
            border: none;
            transition: all 0.2s;
        }

        .quantity-btn:hover {
            background: #e5e7eb;
        }

        .discount-badge {
            background: #dcfce7;
            color: #166534;
            font-size: 0.875rem;
            padding: 4px 8px;
            border-radius: 6px;
        }
    </style>
</head>
<body>
    <!-- Spinner Start -->
    <div id="spinner"
        class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-grow text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    <!-- Spinner End -->

 

    <!-- Navbar Start -->
    <div class="container-fluid sticky-top bg-primary">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-light p-0">
                <a href="index.php" class="navbar-brand">
                    <h2 class="text-white">Hairnic</h2>
                </a>
                <button type="button" class="navbar-toggler ms-auto me-0" data-bs-toggle="collapse"
                    data-bs-target="#navbarCollapse">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <div class="navbar-nav ms-auto">
                        <a href="index.php" class="nav-item nav-link">Home</a>
                        <a href="about.php" class="nav-item nav-link">About</a>
                        <a href="products.php" class="nav-item nav-link">Products</a>
                        <a href="contact.php" class="nav-item nav-link">Contact</a>
                        <a href="cart.php" class="nav-item nav-link active"><i class="fa-solid fa-cart-shopping"></i><span><?= $cartItemsQty ?></span></a>
                    </div>
                    <a href="" class="btn btn-dark py-2 px-4 d-none d-lg-inline-block">Shop Now</a>
                </div>
            </nav>
        </div>
    </div>
    <!-- Navbar End -->

    <div class="cart-wrapper">
        <div class="container">
            <div class="row g-4">
                <!-- Cart Items Section -->
                <div class="col-lg-8">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="mb-0">Shopping Cart</h4>
                        <!-- BUG: The item count is hardcoded as "3 items" instead of reflecting the actual cart contents -->
                        <span class="text-muted"><?php echo count($cartItems); ?> items</span>
                    </div>

                    <!-- Product Cards -->
                    <div class="d-flex flex-column gap-3">
                        <?php if (empty($cartItems)): ?>
                            <div class="alert alert-info">Your cart is empty.</div>
                        <?php else: ?>
                            <?php foreach($cartItems as $item): ?>
                            <div class="product-card p-3 shadow-sm">
                                <div class="row align-items-center">
                                    <div class="col-md-2">
                                        <!-- BUG: Product image is hardcoded, should use $item['image'] if available -->
                                        <img src="<?= htmlspecialchars('uploads/' . $item['image_url'] ?? 'https://via.placeholder.com/100') ?>" alt="Product" class="product-image">
                                    </div>
                                    <div class="col-md-4">
                                        <!-- BUG: Product name and details are hardcoded -->
                                        <h6 class="mb-1"><?= htmlspecialchars($item['name'] ?? 'Product') ?></h6>
                                        <!-- Optionally, show more product details if available -->
                                        <!-- <p class="text-muted mb-0"><?= htmlspecialchars($item['description'] ?? '') ?></p> -->
                                        <!-- Discount badge is hardcoded, should be dynamic if discounts exist -->
                                        <!-- <span class="discount-badge mt-2">20% OFF</span> -->
                                    </div>
                                    <div class="col-md-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <!-- BUG: The quantity update buttons do not actually update the cart on the server, only the input value in the UI -->
                                            <form method="post" action="cart.php" class="d-inline"">
                                                <input type="hidden" name="update_quantity" value="1">
                                                <input type="hidden" name="product_id" value="<?= (int)$item['product_id'] ?>">
                                                <input type="hidden" name="quantity" value="<?= max(1, (int)$item['quantity'] - 1) ?>">
                                                <button type="submit" class="quantity-btn" <?= $item['quantity'] <= 1 ? 'disabled' : '' ?>>-</button>
                                            </form>
                                            <input type="number" class="quantity-input" value="<?= (int)$item['quantity'] ?>" min="1" readonly>
                                            <form method="post" action="cart.php" class="d-inline"">
                                                <input type="hidden" name="update_quantity" value="1">
                                                <input type="hidden" name="product_id" value="<?= (int)$item['product_id'] ?>">
                                                <input type="hidden" name="quantity" value="<?= (int)$item['quantity'] + 1 ?>">
                                                <button type="submit" class="quantity-btn">+</button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <!-- BUG: Price is hardcoded, should use $item['price'] -->
                                        <span class="fw-bold"><?= isset($item['price']) ? 'Rp ' . number_format($item['price'], 0, ',', '.') : '' ?></span>
                                    </div>
                                    <div class="col-md-1">
                                        <!-- BUG: Remove button does not actually remove the item from the cart -->
                                        <form method="post" action="cart.php" class="d-inline">
                                            <input type="hidden" name="remove_item" value="1">
                                            <input type="hidden" name="product_id" value="<?= (int)$item['product_id'] ?>">
                                            <button type="submit" class="btn p-0 border-0 bg-transparent">
                                                <i class="bi bi-trash remove-btn"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Summary Section -->
                <div class="col-lg-4">
                    <div class="summary-card p-4 shadow-sm">
                        <h5 class="mb-4">Order Summary</h5>
                        <?php
                            // Calculate subtotal
                            $subtotal = 0;
                            foreach ($cartItems as $item) {
                                $subtotal += ((float)$item['price'] ?? 0) * ((int)$item['quantity'] ?? 1);
                            }
                            // For demo, discount and shipping are hardcoded
                            $discount = 0;
                            $shipping = $subtotal > 0 ? 5000 : 0;
                            $total = $subtotal - $discount + $shipping;
                        ?>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Subtotal</span>
                            <span><?= 'Rp ' . number_format($subtotal, 0, ',', '.') ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Discount</span>
                            <span class="text-success"><?= $discount > 0 ? '-Rp ' . number_format($discount, 0, ',', '.') : 'Rp 0' ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Shipping</span>
                            <span><?= $shipping > 0 ? 'Rp ' . number_format($shipping, 0, ',', '.') : 'Free' ?></span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-4">
                            <span class="fw-bold">Total</span>
                            <span class="fw-bold"><?= 'Rp ' . number_format($total, 0, ',', '.') ?></span>
                        </div>

                        <!-- Promo Code -->
                        <div class="mb-4">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Promo code" name="promo_code" disabled>
                                <button class="btn btn-outline-secondary" type="button" disabled>Apply</button>
                            </div>
                        </div>

                        <button class="btn btn-primary checkout-btn w-100 mb-3" <?= empty($cartItems) ? 'disabled' : '' ?>>
                            Proceed to Checkout
                        </button>
                        
                        <div class="d-flex justify-content-center gap-2">
                            <i class="bi bi-shield-check text-success"></i>
                            <small class="text-muted">Secure checkout</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>


    <!-- JavaScript Libraries -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/lib/wow/wow.min.js"></script>
    <script src="assets/lib/easing/easing.min.js"></script>
    <script src="assets/lib/waypoints/waypoints.min.js"></script>
    <script src="assets/lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Template Javascript -->
    <script src="assets/js/main.js"></script>
</body>
</html>
<?php
// Handle cart actions (should be at the top, but for demonstration, placed here)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_quantity'], $_POST['product_id'], $_POST['quantity'])) {
        // Update quantity in the database
        $productId = (int)$_POST['product_id'];
        $quantity = max(1, (int)$_POST['quantity']);
        // You need to implement updateCartItemQuantity($conn, $productId, $quantity)
        // Example:
        updateCartItemQuantity($conn, $productId, $quantity);
        // Instead of header redirect (headers already sent), use JavaScript to redirect
        echo "<script>window.location.href='cart.php';</script>";
        exit;
    }
    if (isset($_POST['remove_item'], $_POST['product_id'])) {
        $productId = (int)$_POST['product_id'];
        // You need to implement removeCartItem($conn, $productId)
        // Example:
        removeCartItem($conn, $productId);
        echo "<script>window.location.href='cart.php';</script>";
        exit;
    }
}
?>