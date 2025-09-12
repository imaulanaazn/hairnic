<?php

function postMessage($conn, $data){
    if(empty($_POST['name']) || empty($_POST['email']) || empty($_POST['subject']) || empty($_POST['message'])){
        $_SESSION['error'] = "Please fill all the inputs";
        header("Location: contact.php#form-message");
        exit();
    }

    $stmt = mysqli_prepare($conn, "INSERT INTO messages (name, email, subject, message) VALUES (?,?,?,?)");
    if (!$stmt) {
        $_SESSION['error'] = "Database error: " . mysqli_error($conn);
        header("Location: contact.php#form-message");
        exit();
    }

    mysqli_stmt_bind_param($stmt, 'ssss', $data['name'], $data['email'], $data['subject'], $data['message']);
    
    if(mysqli_stmt_execute($stmt)){
        $_SESSION['success'] = "Berhasil mengirimkan pesan";
    }else{
        $_SESSION['error'] = "Gagal mengirimkan pesan";
    }
    mysqli_stmt_close($stmt);
    header("Location: contact.php#form-message");
    exit();
}