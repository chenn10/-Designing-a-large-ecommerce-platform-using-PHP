<?php
session_start();
require_once("config/database_sqlite.php");

// 檢查用戶是否登入
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header('Location: home1-7.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];
    $user_id = $_SESSION['user_id'];
    
    // 從資料庫中刪除購物車項目
    executeQuery("DELETE FROM cart WHERE user_id = ? AND product_id = ?", [$user_id, $product_id]);
}

// 移除後導回購物車頁
header("Location: cart.php");
exit;