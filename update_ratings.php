<?php
// 讀取現有商品資料
$products = json_decode(@file_get_contents("products.json"), true) ?? [];

// 為每個商品添加評分資訊（如果還沒有的話）
foreach ($products as &$product) {
    if (!isset($product['rating'])) {
        // 生成隨機評分（3.5-5.0之間）
        $product['rating'] = round(3.5 + (rand(0, 150) / 100), 1);
    }
    if (!isset($product['rating_count'])) {
        // 生成隨機評分人數（10-200之間）
        $product['rating_count'] = rand(10, 200);
    }
    if (!isset($product['ratings'])) {
        // 初始化評分明細（用於儲存個別評分）
        $product['ratings'] = [];
    }
}

// 儲存更新後的資料
if (file_put_contents("products.json", json_encode($products, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
    echo "商品評分資料已成功更新！";
} else {
    echo "更新失敗！";
}
?>