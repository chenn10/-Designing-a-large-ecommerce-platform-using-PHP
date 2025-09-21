<?php
session_start();
require_once("config/database_sqlite.php");

if (!isset($_SESSION["login"]) || $_SESSION["login"] !== true) {
    header("Location: home1-7.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['product_id']) && isset($_POST['rating'])) {
    $product_id = intval($_POST['product_id']);
    $userRating = intval($_POST['rating']);
    $user_id = $_SESSION['user_id'];
    $comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';
    
    // 驗證評分範圍
    if ($userRating < 1 || $userRating > 5) {
        $_SESSION['rating_message'] = "評分必須在1-5星之間";
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
    
    // 檢查商品是否存在
    $product = getSingleResult("SELECT * FROM products WHERE id = ?", [$product_id]);
    if (!$product) {
        $_SESSION['rating_message'] = "找不到該商品";
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
    
    // 檢查用戶是否已經評分過
    $existingRating = getSingleResult("SELECT * FROM ratings WHERE user_id = ? AND product_id = ?", [$user_id, $product_id]);
    
    if ($existingRating) {
        // 更新現有評分
        executeQuery("UPDATE ratings SET rating = ?, comment = ? WHERE user_id = ? AND product_id = ?", 
                    [$userRating, $comment, $user_id, $product_id]);
        $_SESSION['rating_message'] = "您的評分已更新為 {$userRating} 星";
    } else {
        // 新增評分
        executeQuery("INSERT INTO ratings (user_id, product_id, rating, comment) VALUES (?, ?, ?, ?)", 
                    [$user_id, $product_id, $userRating, $comment]);
        $_SESSION['rating_message'] = "感謝您的 {$userRating} 星評分！";
    }
    
    // 重新計算平均評分
    $ratingStats = getSingleResult("SELECT AVG(rating) as avg_rating, COUNT(*) as rating_count FROM ratings WHERE product_id = ?", [$product_id]);
    
    $newRating = $ratingStats ? round($ratingStats['avg_rating'], 1) : 0;
    $newRatingCount = $ratingStats ? $ratingStats['rating_count'] : 0;
    
    // 更新產品的評分統計
    executeQuery("UPDATE products SET rating = ?, rating_count = ? WHERE id = ?", [$newRating, $newRatingCount, $product_id]);
    
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
} else {
    header('Location: home.php');
    exit;
}
?>