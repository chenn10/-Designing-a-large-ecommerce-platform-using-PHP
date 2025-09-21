<?php
require_once 'config/database.php';

echo "開始初始化資料庫...\n\n";

// 創建資料庫實例
$database = new Database();

// 1. 先嘗試連接 MySQL 服務器（不指定資料庫）
try {
    $conn = new PDO("mysql:host=localhost;charset=utf8mb4", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 創建資料庫
    $conn->exec("CREATE DATABASE IF NOT EXISTS shopping_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✓ 資料庫 'shopping_db' 創建成功\n";
    
} catch(PDOException $e) {
    echo "❌ 創建資料庫失敗: " . $e->getMessage() . "\n";
    exit(1);
}

// 2. 執行資料庫結構 SQL
echo "\n正在創建資料表...\n";
if ($database->executeSQLFile('database_structure.sql')) {
    echo "✓ 資料表創建成功\n";
} else {
    echo "❌ 資料表創建失敗\n";
    exit(1);
}

// 3. 測試連線
echo "\n測試資料庫連線...\n";
if ($database->testConnection()) {
    echo "✓ 資料庫連線測試成功\n";
} else {
    echo "❌ 資料庫連線測試失敗\n";
    exit(1);
}

// 4. 遷移現有 JSON 資料
echo "\n開始遷移現有資料...\n";

try {
    $conn = $database->getConnection();
    
    // 遷移產品資料
    if (file_exists('products.json')) {
        $products = json_decode(file_get_contents('products.json'), true);
        
        $stmt = $conn->prepare("
            INSERT INTO products (name, description, price, stock, category, image, rating, rating_count, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
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
            $accounts = eval("return $accounts_str;");
            
            $stmt = $conn->prepare("
                INSERT INTO users (username, email, password, created_at) 
                VALUES (?, ?, ?, NOW())
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
        }
    }
    
    echo "\n🎉 資料庫初始化完成！\n";
    echo "您現在可以使用 MySQL 資料庫了。\n";
    
} catch (Exception $e) {
    echo "❌ 資料遷移失敗: " . $e->getMessage() . "\n";
    exit(1);
}
?>