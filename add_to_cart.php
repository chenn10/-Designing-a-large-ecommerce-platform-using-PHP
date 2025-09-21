<?php
session_start();
require_once("config/database_sqlite.php");

// 檢查用戶是否登入
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    $_SESSION['cart_message'] = "請先登入！";
    header('Location: home1-7.php');
    exit;
}

if (!isset($_POST['product_name'])) {
    header('Location: home.php');
    exit;
}

$name = $_POST['product_name'];
$user_id = $_SESSION['user_id'];

// 從資料庫讀取商品資料
$product = getSingleResult("SELECT * FROM products WHERE name = ?", [$name]);

// 如果找不到商品
if (!$product) {
    $_SESSION['cart_message'] = "商品不存在！";
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}

// 檢查購物車中是否已有此商品
$cartItem = getSingleResult("SELECT * FROM cart WHERE user_id = ? AND product_id = ?", [$user_id, $product['id']]);

if ($cartItem) {
    // 商品已在購物車中，增加數量
    $newQty = $cartItem['quantity'] + 1;
    
    // 檢查庫存
    if ($newQty > $product['stock']) {
        $_SESSION['cart_message'] = "庫存不足！目前庫存：{$product['stock']} 件，購物車已有：{$cartItem['quantity']} 件";
    } else {
        // 更新數量
        executeQuery("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?", [$newQty, $user_id, $product['id']]);
        $_SESSION['cart_message'] = "已加入購物車！";
    }
} else {
    // 新商品加入購物車
    if ($product['stock'] < 1) {
        $_SESSION['cart_message'] = "商品已售完！";
    } else {
        executeQuery("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)", [$user_id, $product['id'], 1]);
        $_SESSION['cart_message'] = "已加入購物車！";
    }
}

header('Location: ' . $_SERVER['HTTP_REFERER']);
exit;