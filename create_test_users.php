<?php
require_once("config/database_sqlite.php");

echo "創建測試用戶...\n";

// 創建測試用戶
$testUsers = [
    ['username' => 'test', 'email' => 'test@example.com', 'password' => '123456'],
    ['username' => 'admin', 'email' => 'admin@example.com', 'password' => 'admin123'],
    ['username' => 'user1', 'email' => 'user1@example.com', 'password' => 'password']
];

foreach ($testUsers as $user) {
    // 檢查用戶是否已存在
    $existing = getSingleResult("SELECT id FROM users WHERE username = ?", [$user['username']]);
    
    if (!$existing) {
        executeQuery("INSERT INTO users (username, email, password) VALUES (?, ?, ?)", [
            $user['username'], 
            $user['email'], 
            $user['password']
        ]);
        echo "✓ 已創建用戶: {$user['username']}\n";
    } else {
        echo "- 用戶 {$user['username']} 已存在\n";
    }
}

// 檢查資料庫狀態
$userCount = getSingleResult("SELECT COUNT(*) as count FROM users")['count'];
$productCount = getSingleResult("SELECT COUNT(*) as count FROM products")['count'];
$cartCount = getSingleResult("SELECT COUNT(*) as count FROM cart")['count'];
$ratingCount = getSingleResult("SELECT COUNT(*) as count FROM ratings")['count'];

echo "\n📊 資料庫狀態:\n";
echo "   用戶數量: {$userCount}\n";
echo "   商品數量: {$productCount}\n";
echo "   購物車項目: {$cartCount}\n";
echo "   評分數量: {$ratingCount}\n";

echo "\n🎉 資料庫整合完成！\n";
echo "您可以使用以下測試帳號登入:\n";
echo "- 帳號: test, 密碼: 123456\n";
echo "- 帳號: admin, 密碼: admin123\n";
echo "- 帳號: user1, 密碼: password\n";
?>