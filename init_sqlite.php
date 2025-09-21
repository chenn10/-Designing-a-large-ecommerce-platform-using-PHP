<?php
require_once 'config/database_sqlite.php';

echo "開始使用 SQLite 初始化資料庫...\n\n";

// 創建資料庫實例
$database = new Database();

// 1. 創建資料表
echo "正在創建資料表...\n";
if ($database->createTables()) {
    echo "✓ 資料表創建成功\n";
} else {
    echo "❌ 資料表創建失敗\n";
    exit(1);
}

// 2. 測試連線
echo "\n測試資料庫連線...\n";
if ($database->testConnection()) {
    echo "✓ 資料庫連線測試成功\n";
} else {
    echo "❌ 資料庫連線測試失敗\n";
    exit(1);
}

// 3. 遷移現有 JSON 資料
echo "\n開始遷移現有資料...\n";

try {
    $conn = $database->getConnection();
    
    // 遷移產品資料
    if (file_exists('products.json')) {
        $products = json_decode(file_get_contents('products.json'), true);
        
        $stmt = $conn->prepare("
            INSERT INTO products (name, description, price, stock, category, image, rating, rating_count) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $migrated_products = 0;
        foreach ($products as $index => $product) {
            $stmt->execute([
                $product['name'],
                $product['description'] ?? '',
                $product['price'],
                $product['stock'] ?? 100,
                $product['category'],
                $product['image'],
                $product['rating'] ?? 0,
                $product['rating_count'] ?? 0
            ]);
            $migrated_products++;
        }
        
        echo "✓ 已遷移 {$migrated_products} 個產品\n";
    }
    
    // 遷移用戶資料 (從 Account.php)
    if (file_exists('Account.php')) {
        $account_content = file_get_contents('Account.php');
        
        // 解析 PHP 陣列格式的帳號資料
        if (preg_match('/\$accounts\s*=\s*(\[.*?\]);/s', $account_content, $matches)) {
            $accounts_str = $matches[1];
            // 這裡需要安全地評估 PHP 陣列
            try {
                $accounts = eval("return $accounts_str;");
                
                $stmt = $conn->prepare("
                    INSERT INTO users (username, email, password) 
                    VALUES (?, ?, ?)
                ");
                
                $migrated_users = 0;
                foreach ($accounts as $username => $account) {
                    $stmt->execute([
                        $username,
                        $account['email'] ?? $username . '@example.com',
                        $account['password']
                    ]);
                    $migrated_users++;
                }
                
                echo "✓ 已遷移 {$migrated_users} 個用戶帳號\n";
            } catch (Exception $e) {
                echo "⚠ 用戶資料遷移跳過 (格式問題): " . $e->getMessage() . "\n";
            }
        }
    }
    
    // 檢查資料
    $product_count = $conn->query("SELECT COUNT(*) FROM products")->fetchColumn();
    $user_count = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
    
    echo "\n📊 資料統計:\n";
    echo "   產品數量: {$product_count}\n";
    echo "   用戶數量: {$user_count}\n";
    
    echo "\n🎉 SQLite 資料庫初始化完成！\n";
    echo "資料庫檔案: shopping.db\n";
    echo "您現在可以使用 SQLite 資料庫了。\n";
    
} catch (Exception $e) {
    echo "❌ 資料遷移失敗: " . $e->getMessage() . "\n";
    exit(1);
}
?>