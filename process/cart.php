<?php

function addToCart($conn, $productId, $quantity, $redirect){

    $cart = isCartExist($conn);

    // Kalau cart belum ada, bikin dulu
    if(!$cart){
        $cartId = createCart($conn);
    } else {
        $cartId = $cart['id'];
    }

    $stmt = mysqli_prepare($conn, "INSERT INTO cart_items (cart_id, product_id, quantity) VALUES (?,?,?)");
    mysqli_stmt_bind_param($stmt, 'iii', $cartId, $productId, $quantity);
    
    if(mysqli_stmt_execute($stmt)){
        header($redirect);
        return "Product added to the cart";
    } else {
        return "Failed to add product to the cart";
    }
}

function isCartExist($conn){
    $sessionId = session_id();

    $stmt = mysqli_prepare($conn, "SELECT * FROM carts WHERE session_id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, 's', $sessionId);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    return mysqli_fetch_assoc($result); // return satu row atau null
}

function createCart($conn){
    $sessionId = session_id();

    $stmt = mysqli_prepare($conn, "INSERT INTO carts (session_id) VALUES (?)");
    mysqli_stmt_bind_param($stmt, 's', $sessionId);
    
    if(mysqli_stmt_execute($stmt)){
        return mysqli_insert_id($conn); // balikin id cart yang baru dibuat;
    }else{
        $_SESSION['error'] = "Failed to create a cart";
        return false;
    }
}

function getCartItemsQty($conn){
    $sessionId = session_id();
    $stmt = mysqli_prepare($conn, "SELECT id FROM carts WHERE session_id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, 's', $sessionId);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $cart = mysqli_fetch_assoc($result);
    
    if($cart){
        $items_stmt = mysqli_prepare($conn, "SELECT COUNT(id) as total_items FROM cart_items WHERE cart_id = ?");
        mysqli_stmt_bind_param($items_stmt, 'i', $cart['id']);
        mysqli_stmt_execute($items_stmt);
        $items_result = mysqli_stmt_get_result($items_stmt);
        $items = mysqli_fetch_assoc($items_result);
        return $items['total_items'] ?? 0;
    }else{
        return 0;
    }
}

function getCartItems($conn){
    $sessionId = session_id();
    // Get the cart for this session
    $stmt = mysqli_prepare($conn, "SELECT id FROM carts WHERE session_id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, 's', $sessionId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $cart = mysqli_fetch_assoc($result);

    if(!$cart) {
        return [];
    }

    // Get cart items with product details, filter out duplicates by grouping by product_id
    $items_stmt = mysqli_prepare(
        $conn, 
        "SELECT ci.*, p.name, p.price, p.image_url 
         FROM cart_items ci 
         JOIN products p ON ci.product_id = p.id 
         WHERE ci.cart_id = ? 
         GROUP BY ci.product_id, ci.cart_id"
    );
    mysqli_stmt_bind_param($items_stmt, 'i', $cart['id']);
    mysqli_stmt_execute($items_stmt);
    $items_result = mysqli_stmt_get_result($items_stmt);

    $uniqueItems = [];
    $seen = [];
    while($row = mysqli_fetch_assoc($items_result)) {
        $key = $row['product_id'] . '-' . $row['cart_id'];
        if (!isset($seen[$key])) {
            $uniqueItems[] = $row;
            $seen[$key] = true;
        }
    }

    return $uniqueItems;
}

function updateCartItemQuantity($conn, $productId, $quantity) {
    $sessionId = session_id();

    // Get the cart for this session
    $stmt = mysqli_prepare($conn, "SELECT id FROM carts WHERE session_id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, 's', $sessionId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $cart = mysqli_fetch_assoc($result);

    if (!$cart) {
        // No cart found for this session
        return false;
    }

    $cartId = $cart['id'];

    // If quantity is 0 or less, remove the item from the cart
    if ($quantity <= 0) {
        $deleteStmt = mysqli_prepare($conn, "DELETE FROM cart_items WHERE cart_id = ? AND product_id = ?");
        mysqli_stmt_bind_param($deleteStmt, 'ii', $cartId, $productId);
        $success = mysqli_stmt_execute($deleteStmt);
        mysqli_stmt_close($deleteStmt);
        return $success;
    } else {
        // Update the quantity
        $updateStmt = mysqli_prepare($conn, "UPDATE cart_items SET quantity = ? WHERE cart_id = ? AND product_id = ?");
        mysqli_stmt_bind_param($updateStmt, 'iii', $quantity, $cartId, $productId);
        $success = mysqli_stmt_execute($updateStmt);
        mysqli_stmt_close($updateStmt);
        return $success;
    }
}

function removeCartItem($conn, $productId){
    $sessionId = session_id();

    // Get the cart for this session
    $stmt = mysqli_prepare($conn, "SELECT id FROM carts WHERE session_id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, 's', $sessionId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $cart = mysqli_fetch_assoc($result);

    if (!$cart) {
        // No cart found for this session
        return false;
    }

    $cartId = $cart['id'];

    // Remove the item from the cart
    $deleteStmt = mysqli_prepare($conn, "DELETE FROM cart_items WHERE cart_id = ? AND product_id = ?");
    mysqli_stmt_bind_param($deleteStmt, 'ii', $cartId, $productId);
    $success = mysqli_stmt_execute($deleteStmt);
    mysqli_stmt_close($deleteStmt);

    return $success;
}