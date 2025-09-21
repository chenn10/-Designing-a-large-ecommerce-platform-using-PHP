<?php
echo "=== MySQL 資料庫配置助手 ===\n\n";

echo "看起來您的 MySQL 需要密碼。請按照以下步驟配置：\n\n";

echo "方法 1: 使用 MySQL 命令行設定密碼\n";
echo "----------------------------------------\n";
echo "1. 開啟命令提示字元 (以管理員身分執行)\n";
echo "2. 執行: net stop mysql8\n";
echo "3. 執行: mysqld --skip-grant-tables --skip-networking\n";
echo "4. 開啟新的命令提示字元視窗\n";
echo "5. 執行: mysql -u root\n";
echo "6. 在 MySQL 中執行:\n";
echo "   USE mysql;\n";
echo "   UPDATE user SET authentication_string=PASSWORD('') WHERE User='root';\n";
echo "   FLUSH PRIVILEGES;\n";
echo "   EXIT;\n";
echo "7. 關閉 mysqld 程序，重新啟動 MySQL 服務\n\n";

echo "方法 2: 使用空密碼配置 (測試環境)\n";
echo "----------------------------------------\n";
echo "如果您想要設定 root 用戶為空密碼，請執行以下步驟：\n";
echo "1. 停止 MySQL 服務: net stop mysql8\n";
echo "2. 以安全模式啟動: mysqld --skip-grant-tables\n";
echo "3. 連接並重設密碼\n\n";

echo "方法 3: 使用現有密碼\n";
echo "----------------------------------------\n";
echo "如果您知道 root 密碼，請修改 config/database.php 檔案中的密碼設定\n\n";

// 嘗試常見的密碼
$common_passwords = ['', 'root', 'password', '123456', 'admin'];

echo "正在測試常見密碼...\n";
foreach ($common_passwords as $pwd) {
    try {
        $conn = new PDO("mysql:host=localhost", "root", $pwd);
        echo "✓ 成功！密碼是: " . ($pwd === '' ? '(空密碼)' : $pwd) . "\n";
        
        // 更新配置檔案
        $config_file = 'config/database.php';
        $config_content = file_get_contents($config_file);
        $config_content = preg_replace(
            "/private \$password = '.*?';/",
            "private \$password = '$pwd';",
            $config_content
        );
        file_put_contents($config_file, $config_content);
        
        echo "✓ 已自動更新配置檔案\n";
        echo "\n現在可以執行: php init_database.php\n";
        exit(0);
        
    } catch (PDOException $e) {
        // 繼續嘗試下一個密碼
    }
}

echo "❌ 未找到正確的密碼\n";
echo "\n手動配置步驟：\n";
echo "1. 確定您的 MySQL root 密碼\n";
echo "2. 編輯 config/database.php 檔案\n";
echo "3. 修改 \$password 變數為正確的密碼\n";
echo "4. 執行 php init_database.php\n";
?>