<?php
// 資料庫配置檔案
class Database {
    private $host = 'localhost';
    private $db_name = 'shopping_db';
    private $username = 'root';  // 您的 MySQL 用戶名
    private $password = '';      // 您的 MySQL 密碼
    private $charset = 'utf8mb4';
    private $conn;

    // 取得資料庫連線
    public function getConnection() {
        $this->conn = null;
        
        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=" . $this->charset;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
        } catch(PDOException $e) {
            echo "資料庫連線錯誤: " . $e->getMessage();
            die();
        }

        return $this->conn;
    }

    // 檢查資料庫連線
    public function testConnection() {
        try {
            $conn = $this->getConnection();
            if ($conn) {
                return true;
            }
        } catch (Exception $e) {
            return false;
        }
        return false;
    }

    // 執行 SQL 檔案
    public function executeSQLFile($filename) {
        try {
            $sql = file_get_contents($filename);
            $conn = $this->getConnection();
            
            // 分割 SQL 語句
            $statements = array_filter(array_map('trim', explode(';', $sql)));
            
            foreach ($statements as $statement) {
                if (!empty($statement)) {
                    $conn->exec($statement);
                }
            }
            
            return true;
        } catch (PDOException $e) {
            echo "執行 SQL 檔案錯誤: " . $e->getMessage();
            return false;
        }
    }
}

// 工具函數：取得資料庫連線
function getDB() {
    static $database = null;
    if ($database === null) {
        $database = new Database();
    }
    return $database->getConnection();
}

// 工具函數：安全地執行查詢
function executeQuery($sql, $params = []) {
    try {
        $conn = getDB();
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch (PDOException $e) {
        error_log("資料庫查詢錯誤: " . $e->getMessage());
        return false;
    }
}

// 工具函數：取得單一結果
function getSingleResult($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    return $stmt ? $stmt->fetch() : false;
}

// 工具函數：取得多個結果
function getMultipleResults($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    return $stmt ? $stmt->fetchAll() : [];
}
?>