<?php
require_once("config/database_sqlite.php");

echo "=== 買GO網 資料庫功能介紹 ===\n\n";

// 檢查所有資料表
$conn = getDB();
$tables = $conn->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll();

echo "📊 資料表結構:\n";
echo "==============\n";
foreach ($tables as $table) {
    $tableName = $table['name'];
    echo "• {$tableName}\n";
}

echo "\n📈 資料統計:\n";
echo "============\n";

// 統計各資料表數量
$stats = [
    'users' => '用戶數量',
    'products' => '商品數量', 
    'cart' => '購物車項目',
    'ratings' => '評分數量',
    'orders' => '訂單數量',
    'order_items' => '訂單項目',
    'favorites' => '收藏數量'
];

foreach ($stats as $table => $description) {
    try {
        $count = $conn->query("SELECT COUNT(*) as count FROM {$table}")->fetch()['count'];
        echo "• {$description}: {$count}\n";
    } catch (Exception $e) {
        echo "• {$description}: 0\n";
    }
}

echo "\n🎯 核心功能:\n";
echo "============\n";

echo "1. 用戶管理系統\n";
echo "   - 用戶註冊與登入\n";
echo "   - 密碼安全存儲\n";
echo "   - 會話管理\n\n";

echo "2. 商品管理系統\n";
echo "   - 商品信息存儲\n";
echo "   - 庫存管理\n";
echo "   - 分類篩選\n";
echo "   - 搜尋功能\n\n";

echo "3. 購物車系統\n";
echo "   - 個人化購物車\n";
echo "   - 數量修改\n";
echo "   - 庫存檢查\n";
echo "   - 持久化存儲\n\n";

echo "4. 評分系統\n";
echo "   - 用戶評分記錄\n";
echo "   - 平均評分計算\n";
echo "   - 評分統計\n\n";

echo "5. 訂單系統 (預留)\n";
echo "   - 訂單記錄\n";
echo "   - 訂單項目\n";
echo "   - 訂單狀態管理\n\n";

// 示範一些查詢
echo "🔍 範例查詢:\n";
echo "============\n";

// 最受歡迎的商品
$topProducts = getMultipleResults("
    SELECT name, rating, rating_count 
    FROM products 
    WHERE rating_count > 0 
    ORDER BY rating DESC, rating_count DESC 
    LIMIT 5
");

if (!empty($topProducts)) {
    echo "最高評分商品:\n";
    foreach ($topProducts as $product) {
        echo "  • {$product['name']} - {$product['rating']}星 ({$product['rating_count']}人評分)\n";
    }
} else {
    echo "暫無評分商品\n";
}

echo "\n各分類商品數量:\n";
$categories = getMultipleResults("
    SELECT category, COUNT(*) as count 
    FROM products 
    GROUP BY category 
    ORDER BY count DESC
");

foreach ($categories as $cat) {
    echo "  • {$cat['category']}: {$cat['count']} 件商品\n";
}

echo "\n💡 技術優勢:\n";
echo "============\n";
echo "• SQLite 輕量級資料庫，無需額外服務\n";
echo "• PDO 預處理語句，防止 SQL 注入\n";
echo "• 外鍵約束確保資料完整性\n";
echo "• 事務支援確保資料一致性\n";
echo "• 支援複雜查詢和資料分析\n";
echo "• 易於備份和遷移\n";

echo "\n🚀 擴展功能 (可未來開發):\n";
echo "========================\n";
echo "• 訂單管理系統\n";
echo "• 收藏夾功能\n";
echo "• 瀏覽記錄\n";
echo "• 商品推薦\n";
echo "• 用戶設定\n";
echo "• 數據分析報表\n";

echo "\n✅ 資料庫整合完成！\n";
?>