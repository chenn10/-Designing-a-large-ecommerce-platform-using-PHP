<?php
session_start();
require_once("config/database_sqlite.php");

// 檢查用戶是否登入
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header('Location: home1-7.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['product_id']) && isset($_POST['quantity'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    $user_id = $_SESSION['user_id'];
    
    // 獲取商品資料
    $product = getSingleResult("SELECT * FROM products WHERE id = ?", [$product_id]);
    
    if (!$product) {
        $_SESSION['cart_message'] = "商品不存在！";
        header("Location: cart.php");
        exit();
    }
    
    // 檢查數量是否有效
    if ($quantity <= 0) {
        // 如果數量為0或負數，從購物車中移除該商品
        executeQuery("DELETE FROM cart WHERE user_id = ? AND product_id = ?", [$user_id, $product_id]);
        $_SESSION['cart_message'] = "已從購物車移除商品";
    } else if ($quantity > $product['stock']) {
        $_SESSION['cart_message'] = "庫存不足！目前庫存：{$product['stock']} 件";
    } else {
        // 更新數量
        executeQuery("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?", [$quantity, $user_id, $product_id]);
        $_SESSION['cart_message'] = "已更新商品數量";
    }
    
    // 重新導向回購物車頁面
    header("Location: cart.php");
    exit();
} else {
    // 如果沒有正確的POST資料，重新導向回購物車
    header("Location: cart.php");
    exit();
}
?>