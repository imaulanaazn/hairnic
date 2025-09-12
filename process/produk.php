<?php

function getTodayDeals($conn) {
    $sql = "SELECT * FROM deals LEFT JOIN products ON deals.product_id = products.id WHERE cd = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        error_log("Prepare failed: " . mysqli_error($conn));
        return [];
    }

    $dealType = "today_deal";
    mysqli_stmt_bind_param($stmt, "s", $dealType);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    $deals = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $deals[] = $row;
        }
    }

    mysqli_stmt_close($stmt);

    return $deals;
}

function getAllProducts($conn, $limit = 10){
    $sql = "SELECT 
    p.id as product_id, 
    p.name, 
    p.price, 
    p.description, 
    p.image_url, 
    IFNULL(AVG(r.rating), 0) as avg_rating,
    COUNT(r.id) as total_reviews
    FROM products p LEFT JOIN reviews r ON p.id = r.product_id GROUP BY p.id LIMIT ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $limit);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $products = [];
    if($result){
        while($row = mysqli_fetch_assoc($result)){
            $products[] = $row;
        }
    }

    $totalResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM products");
    $totalRow = mysqli_fetch_assoc($totalResult);
    $totalPages = ceil($totalRow['total'] / $limit);

    mysqli_stmt_close($stmt);
    return ["limit" => $limit, "data" => $products, "totalPages" => $totalPages];
}

function getAllFeedback($conn){
    $sql = "SELECT * FROM feedback";
    $result = mysqli_query($conn, $sql);

    $feedback = [];
    while($row = mysqli_fetch_assoc($result)){
        $feedback[] = $row;
    }

    return $feedback;
}

function subscribeNewsletter($conn, $email, $redirect){

    $checkSql = "SELECT * FROM subscribers WHERE email = ?";
    $checkStmt = mysqli_prepare($conn, $checkSql);
    mysqli_stmt_bind_param($checkStmt, 's', $email);
    mysqli_stmt_execute($checkStmt);
    mysqli_stmt_store_result($checkStmt);

    if(mysqli_stmt_num_rows($checkStmt) > 0){
        $_SESSION['error'] = "This email has ben subscribed";
        mysqli_stmt_close($checkStmt);
        header($redirect);
        exit();
    }
    
    mysqli_stmt_close($checkStmt);
    
    $sql = "INSERT INTO subscribers (email) VALUES (?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 's', $email);
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success'] = "Thanks for subscribing to our newsletter";
    } else {
        $_SESSION['error'] = "Failed to subscribe to newsletter: " . mysqli_error($conn);
    }
    
    mysqli_stmt_close($stmt);
    header($redirect);
    exit();
}

function getProductDetail($conn, $productId){
    $sql = " SELECT
        p.id as product_id, 
        p.name, 
        p.price, 
        p.description, 
        p.image_url, 
        IFNULL(AVG(r.rating), 0) as avg_rating,
        COUNT(r.id) as total_reviews
        FROM products p LEFT JOIN reviews r ON p.id = r.product_id WHERE p.id = ?
        ";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $productId);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    $product = [];
    if($result){
        while($row = mysqli_fetch_assoc($result)){
            $product[] = $row;
        }
    }

    mysqli_stmt_close($stmt);
    return $product[0];    
}

// if ($_SERVER['REQUEST_METHOD'] == "POST") {
//     $name = $_POST['name'];
//     $price = $_POST['price'];
//     $description = $_POST['description'];

//     $image_url = "";
//     if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
//         $targetDir = "../uploads/";
//         $imageExtension = pathinfo(basename($_FILES['image']['name']), PATHINFO_EXTENSION);

//         $allowedType = array('jpg', 'jpeg', 'png', 'webp');
//         if (in_array(strtolower($imageExtension), $allowedType)) {
//             $fileName = uniqid('img_', true) . '.' . $imageExtension;
//             $targetFile = $targetDir . $fileName;

//             // Create uploads directory if not exists
//             if (!is_dir($targetDir)) {
//                 mkdir($targetDir, 0777, true);
//             }

//             if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
//                 $image_url = "/uploads/" . $fileName;
//             } else {
//                 $image_url = "";
//             }
//         } else {
//             $image_url = "";
//         }
//     } else {
//         $image_url = "";
//     }

//     $query = "INSERT INTO products (name, price, description, image_url) VALUES (?, ?, ?, ?)";
//     $stmt = mysqli_prepare($conn, $query);

//     if ($stmt) {
//         mysqli_stmt_bind_param($stmt, 'sdss', $name, $price, $description, $image_url);
//         try {
//             $result = mysqli_stmt_execute($stmt);
//             if ($result) {
//                 echo "Produk ditambahkan";
//             } else {
//                 echo "Gagal menambahkan produk";
//             }
//         } catch (\Throwable $th) {
//             echo "Gagal menambahkan produk";
//         }
//     } else {
//         echo "Gagal menyiapkan statement";
//     }
// }

// if($_SERVER['REQUEST_METHOD'] == "GET"){
//     $productId = $_GET['productId'];

//     //GET PRODUCT BY ID
//     if($productId){
//         $query = "SELECT * FROM products WHERE id = ?";
//         $stmt = mysqli_prepare($conn, $query);
//         if($stmt){
//             mysqli_stmt_bind_param($stmt, 's', $productId);
//             try {
//                 if (mysqli_stmt_execute($stmt)) {
//                     $result = mysqli_stmt_get_result($stmt);
//                     if ($result && mysqli_num_rows($result) > 0) {
//                         $product = mysqli_fetch_assoc($result);
//                         // Output as JSON for retrieval on the page
//                         header('Content-Type: application/json');
//                         echo json_encode($product);
//                     } else {
//                         http_response_code(404);
//                         echo json_encode(['error' => 'Product not found']);
//                     }
//                 } else {  
//                     http_response_code(500);
//                     echo json_encode(['error' => 'Failed to execute statement']);
//                 }
//             } catch (\Throwable $th) {
//                 http_response_code(500);
//                 echo json_encode(['error' => 'An error occurred']);
//             }
//         }
//     }else{
//     //GET ALL PRODUCTS

//     }
// }