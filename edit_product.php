<?php
session_start();

// 檢查是否已登入
if (!isset($_SESSION["login"]) || $_SESSION["login"] !== true) {
    header("Location: home1-7.php");
    exit();
}

$username = $_SESSION["username"];
$products = json_decode(@file_get_contents("products.json"), true) ?? [];
$product = null;
$error = '';
$success = '';

// 獲取要編輯的商品
if (isset($_GET['name'])) {
    $productName = $_GET['name'];
    foreach ($products as $key => $p) {
        if ($p['name'] === $productName) {
            // 檢查是否為商品擁有者或管理員
            if ($p['username'] === $username || $username === 'admin') {
                $product = $p;
                $productIndex = $key;
                break;
            } else {
                $error = "您沒有權限編輯此商品";
            }
        }
    }
    if (!$product && empty($error)) {
        $error = "找不到指定的商品";
    }
} else {
    header("Location: user_products.php");
    exit();
}

// 處理表單提交
if ($_SERVER["REQUEST_METHOD"] === "POST" && $product) {
    $name = trim($_POST["name"]);
    $price = trim($_POST["price"]);
    $category = trim($_POST["category"]);
    $stock = intval($_POST["stock"]);
    $description = trim($_POST["description"]);
    $detail = trim($_POST["detail"]);
    
    // 基本驗證
    if (empty($name) || empty($price) || empty($category) || $stock < 0) {
        $error = "請填寫所有必要欄位";
    } elseif (!is_numeric($price) || $price <= 0) {
        $error = "價格必須是正數";
    } else {
        // 檢查是否有其他商品使用相同名稱（除了目前這個商品）
        $nameExists = false;
        foreach ($products as $key => $p) {
            if ($p['name'] === $name && $key !== $productIndex) {
                $nameExists = true;
                break;
            }
        }
        
        if ($nameExists) {
            $error = "商品名稱已存在";
        } else {
            // 更新商品資料
            $products[$productIndex]['name'] = $name;
            $products[$productIndex]['price'] = $price;
            $products[$productIndex]['category'] = $category;
            $products[$productIndex]['stock'] = $stock;
            $products[$productIndex]['description'] = $description;
            $products[$productIndex]['detail'] = $detail;
            
            // 儲存到檔案
            if (file_put_contents("products.json", json_encode($products, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
                $success = "商品資料已更新成功";
                $product = $products[$productIndex]; // 更新顯示的資料
            } else {
                $error = "無法儲存商品資料";
            }
        }
    }
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>編輯商品 - 買GO網</title>
    <link rel="stylesheet" href="home_style.css">
    <style>
        .edit-panel {
            max-width: 600px;
            margin: 40px auto;
            background: rgba(255,255,255,0.97);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 2px 8px #bbb;
        }
        .edit-panel h1 {
            text-align: center;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        .form-group textarea {
            height: 80px;
            resize: vertical;
        }
        .form-actions {
            text-align: center;
            margin-top: 30px;
        }
        .btn {
            padding: 12px 30px;
            margin: 0 10px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .btn-primary {
            background: #007acc;
            color: white;
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .btn:hover {
            opacity: 0.9;
        }
        .message {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        body.dark-theme .edit-panel {
            background: rgba(45, 44, 44, 0.97);
            color: #f0f0f0;
        }
        body.dark-theme .form-group input,
        body.dark-theme .form-group select,
        body.dark-theme .form-group textarea {
            background: #333;
            color: #f0f0f0;
            border-color: #555;
        }
    </style>
</head>
<body>
    <div class="top-bar">
        <div class="top-bar-left">
            <span>歡迎來到 買GO網!!!!!</span>
        </div>
        <div class="top-bar-right">
            <?php
            $realname = htmlspecialchars($_SESSION["realname"]);
            $cartCount = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
            echo "<span><a href='user_products.php?user=all' class='account-link'>👤 $realname</a></span> &nbsp;&nbsp;";
            echo "<a href='cart.php' class='cart-link'>購物車";
            if ($cartCount > 0) {
                echo " <span class='cart-badge'>{$cartCount}</span>";
            }
            echo "</a> &nbsp;&nbsp| ";
            if ($username !== "admin") {
                echo "<a href='upitem.php'>上傳商品</a> &nbsp;&nbsp| ";
            }
            echo "<a href='home1-8.php'>登出</a>";
            ?>
            <button id="theme-toggle" class="theme-toggle">深色</button>
        </div>
    </div>
    
    <nav class="category-bar">
        <a href="home.php">首頁</a>
        <a href="user_products.php">我的商品</a>
    </nav>
    
    <div class="main-content">
        <div class="edit-panel">
            <h1>📝 編輯商品</h1>
            
            <?php if ($error): ?>
                <div class="message error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="message success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <?php if ($product): ?>
                <form method="post">
                    <div class="form-group">
                        <label for="name">商品名稱：</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="price">價格：</label>
                        <input type="number" id="price" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" min="1" step="0.01" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="category">分類：</label>
                        <select id="category" name="category" required>
                            <option value="">請選擇分類</option>
                            <?php
                            $categories = ["3C", "通訊", "家電", "日用", "母嬰", "食品", "生活", "居家", "保健", "美妝", "時尚", "書店"];
                            foreach ($categories as $cat) {
                                $selected = ($product['category'] === $cat) ? 'selected' : '';
                                echo "<option value=\"$cat\" $selected>$cat</option>";
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="stock">庫存數量：</label>
                        <input type="number" id="stock" name="stock" value="<?php echo htmlspecialchars($product['stock']); ?>" min="0" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">商品描述：</label>
                        <textarea id="description" name="description"><?php echo htmlspecialchars($product['description']); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="detail">詳細說明：</label>
                        <textarea id="detail" name="detail"><?php echo htmlspecialchars($product['detail']); ?></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">更新商品</button>
                        <a href="user_products.php" class="btn btn-secondary">取消</a>
                    </div>
                </form>
            <?php else: ?>
                <div class="form-actions">
                    <a href="user_products.php" class="btn btn-secondary">返回我的商品</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
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
        }
    </script>
</body>
</html>