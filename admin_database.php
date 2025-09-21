<?php
session_start();

// 檢查是否為管理員登入
if (!isset($_SESSION["login"]) || $_SESSION["login"] !== true || $_SESSION["username"] !== "admin") {
    header("Location: home1-7.php");
    exit;
}

require_once("config/database_sqlite.php");
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>資料庫管理 - 買GO網</title>
    <link rel="stylesheet" href="home_style.css">
    <style>
        .admin-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.1);
        }
        
        .admin-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .stat-number {
            font-size: 36px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .stat-label {
            font-size: 16px;
            opacity: 0.9;
        }
        
        .data-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        
        .section-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #333;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .data-table th {
            background: #667eea;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: bold;
        }
        
        .data-table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }
        
        .data-table tr:hover {
            background: #f8f9fa;
        }
        
        .action-buttons {
            margin: 20px 0;
            text-align: center;
        }
        
        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 0 10px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.3s;
        }
        
        .btn:hover {
            background: #5a67d8;
        }
        
        .btn-danger {
            background: #e53e3e;
        }
        
        .btn-danger:hover {
            background: #c53030;
        }
        
        .btn-success {
            background: #38a169;
        }
        
        .btn-success:hover {
            background: #2f855a;
        }
        
        .rating-stars {
            color: #ffd700;
        }
        
        .alert {
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
            font-weight: bold;
        }
        
        .alert-info {
            background: #bee3f8;
            color: #2b6cb0;
            border: 1px solid #90cdf4;
        }
        
        /* 深色主題支援 */
        body.dark-theme .admin-container {
            background: #2d3748;
            color: #e2e8f0;
        }
        
        body.dark-theme .data-section {
            background: #4a5568;
        }
        
        body.dark-theme .section-title {
            color: #e2e8f0;
        }
        
        body.dark-theme .data-table {
            background: #4a5568;
        }
        
        body.dark-theme .data-table td {
            border-bottom-color: #718096;
            color: #e2e8f0;
        }
        
        body.dark-theme .data-table tr:hover {
            background: #718096;
        }
    </style>
</head>
<body>
    <!-- 導覽列 -->
    <div class="top-bar">
        <div class="top-bar-left">
            <span>🔧 管理員專區 - 買GO網資料庫管理</span>
        </div>
        <div class="top-bar-right">
            <span><a href='user_products.php?user=all' class='account-link'>👤 <?php echo htmlspecialchars($_SESSION["realname"]); ?></a></span> &nbsp;&nbsp| 
            <a href='home.php'>回首頁</a> &nbsp;&nbsp| 
            <a href='home1-8.php'>登出</a>
            <button id="theme-toggle" class="theme-toggle">深色</button>
        </div>
    </div>

    <div class="admin-container">
        <div class="admin-header">
            <h1>🗄️ 資料庫管理系統</h1>
            <p>管理員專屬 - 即時資料庫狀態監控</p>
        </div>

        <?php
        // 獲取資料庫統計
        $conn = getDB();
        
        $userCount = $conn->query("SELECT COUNT(*) as count FROM users")->fetch()['count'];
        $productCount = $conn->query("SELECT COUNT(*) as count FROM products")->fetch()['count'];
        $cartCount = $conn->query("SELECT COUNT(*) as count FROM cart")->fetch()['count'];
        $ratingCount = $conn->query("SELECT COUNT(*) as count FROM ratings")->fetch()['count'];
        $orderCount = $conn->query("SELECT COUNT(*) as count FROM orders")->fetch()['count'];
        $favoriteCount = $conn->query("SELECT COUNT(*) as count FROM favorites")->fetch()['count'];
        
        // 計算總購物車商品數量
        $cartItemsResult = $conn->query("SELECT COALESCE(SUM(quantity), 0) as total FROM cart")->fetch();
        $totalCartItems = $cartItemsResult['total'];
        ?>

        <!-- 統計卡片 -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $userCount; ?></div>
                <div class="stat-label">註冊用戶</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $productCount; ?></div>
                <div class="stat-label">商品總數</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $totalCartItems; ?></div>
                <div class="stat-label">購物車商品數</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $ratingCount; ?></div>
                <div class="stat-label">評分總數</div>
            </div>
        </div>

        <!-- 操作按鈕 -->
        <div class="action-buttons">
            <a href="admin_database.php?action=backup" class="btn btn-success">📦 備份資料庫</a>
            <a href="admin_database.php?action=export" class="btn">📊 匯出資料</a>
            <a href="admin_database.php?action=optimize" class="btn">⚡ 優化資料庫</a>
        </div>

        <!-- 用戶管理 -->
        <div class="data-section">
            <h2 class="section-title">👥 用戶管理</h2>
            <?php
            $users = getMultipleResults("SELECT id, username, email, created_at FROM users ORDER BY created_at DESC");
            ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>用戶名</th>
                        <th>Email</th>
                        <th>註冊時間</th>
                        <th>購物車商品</th>
                        <th>評分數</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($users as $user): ?>
                        <?php
                        $userCartCount = getSingleResult("SELECT COALESCE(SUM(quantity), 0) as total FROM cart WHERE user_id = ?", [$user['id']])['total'];
                        $userRatingCount = getSingleResult("SELECT COUNT(*) as count FROM ratings WHERE user_id = ?", [$user['id']])['count'];
                        ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><strong><?php echo htmlspecialchars($user['username']); ?></strong></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo date('Y-m-d H:i', strtotime($user['created_at'])); ?></td>
                            <td><?php echo $userCartCount; ?> 件</td>
                            <td><?php echo $userRatingCount; ?> 個</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- 熱門商品 -->
        <div class="data-section">
            <h2 class="section-title">🔥 熱門商品 TOP 10</h2>
            <?php
            $topProducts = getMultipleResults("
                SELECT id, name, price, stock, rating, rating_count, category
                FROM products 
                ORDER BY rating_count DESC, rating DESC 
                LIMIT 10
            ");
            ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>商品名稱</th>
                        <th>分類</th>
                        <th>價格</th>
                        <th>庫存</th>
                        <th>評分</th>
                        <th>評分人數</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($topProducts as $product): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($product['name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($product['category']); ?></td>
                            <td>NT$ <?php echo number_format($product['price']); ?></td>
                            <td><?php echo $product['stock']; ?> 件</td>
                            <td>
                                <span class="rating-stars">
                                    <?php 
                                    $rating = floatval($product['rating']);
                                    for($i = 1; $i <= 5; $i++) {
                                        echo $i <= $rating ? '★' : '☆';
                                    }
                                    echo ' ' . $rating;
                                    ?>
                                </span>
                            </td>
                            <td><?php echo $product['rating_count']; ?> 人</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- 最新評分 -->
        <div class="data-section">
            <h2 class="section-title">⭐ 最新評分</h2>
            <?php
            $recentRatings = getMultipleResults("
                SELECT r.rating, r.comment, r.created_at, u.username, p.name as product_name
                FROM ratings r
                JOIN users u ON r.user_id = u.id
                JOIN products p ON r.product_id = p.id
                ORDER BY r.created_at DESC
                LIMIT 10
            ");
            ?>
            <?php if(!empty($recentRatings)): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>用戶</th>
                            <th>商品</th>
                            <th>評分</th>
                            <th>評論</th>
                            <th>時間</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($recentRatings as $rating): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($rating['username']); ?></td>
                                <td><?php echo htmlspecialchars($rating['product_name']); ?></td>
                                <td>
                                    <span class="rating-stars">
                                        <?php 
                                        for($i = 1; $i <= 5; $i++) {
                                            echo $i <= $rating['rating'] ? '★' : '☆';
                                        }
                                        ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($rating['comment'] ?: '無評論'); ?></td>
                                <td><?php echo date('m-d H:i', strtotime($rating['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="alert alert-info">目前還沒有評分記錄</div>
            <?php endif; ?>
        </div>

        <!-- 資料庫資訊 -->
        <div class="data-section">
            <h2 class="section-title">🗃️ 資料庫資訊</h2>
            <?php
            $dbSize = filesize('shopping.db');
            $dbSizeFormatted = $dbSize > 1024*1024 ? round($dbSize/(1024*1024), 2) . ' MB' : round($dbSize/1024, 2) . ' KB';
            
            $tables = $conn->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'")->fetchAll();
            ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>項目</th>
                        <th>值</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>資料庫檔案</strong></td>
                        <td>shopping.db</td>
                    </tr>
                    <tr>
                        <td><strong>檔案大小</strong></td>
                        <td><?php echo $dbSizeFormatted; ?></td>
                    </tr>
                    <tr>
                        <td><strong>資料表數量</strong></td>
                        <td><?php echo count($tables); ?> 個</td>
                    </tr>
                    <tr>
                        <td><strong>SQLite 版本</strong></td>
                        <td><?php echo $conn->query("SELECT sqlite_version()")->fetchColumn(); ?></td>
                    </tr>
                    <tr>
                        <td><strong>最後更新</strong></td>
                        <td><?php echo date('Y-m-d H:i:s', filemtime('shopping.db')); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // 主題切換
        window.onload = function () {
            const theme = localStorage.getItem("theme");
            const btn = document.getElementById("theme-toggle");
            
            if (theme === "dark") {
                document.body.classList.add("dark-theme");
                btn.textContent = "亮色";
            } else {
                btn.textContent = "深色";
            }
            
            btn.onclick = function () {
                const isDark = document.body.classList.contains("dark-theme");
                if (isDark && btn.textContent === "深色") return;
                if (!isDark && btn.textContent === "亮色") return;
                
                document.body.classList.toggle("dark-theme");
                const nowDark = document.body.classList.contains("dark-theme");
                localStorage.setItem("theme", nowDark ? "dark" : "light");
                btn.textContent = nowDark ? "亮色" : "深色";
            };
        };
        
        // 自動刷新 (每30秒)
        setTimeout(function() {
            location.reload();
        }, 30000);
    </script>
</body>
</html>