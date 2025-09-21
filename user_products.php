<?php
session_start();
$user = isset($_GET['user']) ? $_GET['user'] : '';
$products = json_decode(@file_get_contents("products.json"), true) ?? [];

// 判斷目前登入者
$isAdmin = (isset($_SESSION["username"]) && $_SESSION["username"] === "admin");
$loginUser = isset($_SESSION["realname"]) ? $_SESSION["realname"] : null;

// 僅管理員可看所有人商品，一般使用者只能看自己的
if ($user === 'all') {
    if ($isAdmin) {
        $userProducts = $products;
        $showUser = "所有使用者";
    } else {
        // 非管理員強制只看自己
        $userProducts = array_filter($products, function($p) use ($loginUser) {
            return isset($p['username']) && $p['username'] === $loginUser;
        });
        $showUser = $loginUser;
        $user = $loginUser;
    }
} else {
    // 若未指定user或user不是自己且不是管理員，強制只看自己
    if (!$isAdmin && $user !== $loginUser) {
        $user = $loginUser;
    }
    $userProducts = array_filter($products, function($p) use ($user) {
        return isset($p['username']) && $p['username'] === $user;
    });
    $showUser = $user;
}

// 取得所有使用者
$allUsers = array_unique(array_column($products, 'username'));
sort($allUsers);
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo htmlspecialchars($showUser); ?> 的上傳商品</title>
    <link rel="stylesheet" href="home_style.css">
    <style>
    /* 表格樣式（延用首頁色系） */
    .user-products-table {
        width: 95%;
        margin: 30px auto 0 auto;
        border-collapse: collapse;
        background: rgba(255,255,255,0.97);
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 8px #bbb;
        color: #222;
        transition: background 0.3s, color 0.3s;
    }
    .user-products-table th, .user-products-table td {
        border: 1px solid #ddd;
        padding: 10px 8px;
        text-align: center;
        font-size: 16px;
        transition: background 0.3s, color 0.3s;
    }
    .user-products-table th {
        background: #f5f5f5;
        font-weight: bold;
    }
    .user-products-table img {
        width: 90px;    /* 原本 60px，放大為 90px */
        height: 90px;   /* 原本 60px，放大為 90px */
        object-fit: cover;
        border-radius: 8px; /* 稍微加大圓角 */
        box-shadow: 0 1.5px 4px #bbb;
    }
    .manage-link {
        color: #2980b9;
        text-decoration: underline;
        cursor: pointer;
        font-weight: bold;
    }
    .manage-link:hover {
        color: #e74c3c;
    }
    .back-link {
        display: inline-block;
        margin: 20px 0 0 30px;
        color: #555;
        text-decoration: none;
        font-size: 16px;
    }
    .back-link:hover {
        color: #e74c3c;
    }
    .main-content {
        margin-top: 30px;
    }
    /* 深色主題下表格樣式 */
    body.dark-theme .user-products-table {
        background: rgba(45, 44, 44, 0.97);
        color: #f0f0f0;
        box-shadow: 0 2px 8px #222;
    }
    body.dark-theme .user-products-table th {
        background: #222;
        color: #ffdd66;
    }
    body.dark-theme .user-products-table td {
        border-color: #444;
        color: #f0f0f0;
    }
    body.dark-theme .manage-link {
        color: #80cfff;
    }
    body.dark-theme .manage-link:hover {
        color: #ffdd66;
    }
    body.dark-theme .back-link {
        color: #f0f0f0;
    }
    body.dark-theme .back-link:hover {
        color: #ffdd66;
    }
    .user-switch-list {
        margin: 18px 0 18px 30px;
    }
    .user-switch-list a {
        margin-right: 12px;
        color: #007acc;
        text-decoration: underline;
        font-weight: bold;
    }
    .user-switch-list a.selected {
        color: #e74c3c;
        text-decoration: none;
    }
    </style>
</head>
<body>
    <!-- 導覽列（與首頁一致） -->
    <div class="top-bar">
        <div class="top-bar-left">
        <span>歡迎來到 買GO網!!!!!</span>
        </div>
        <div class="top-bar-right">
        <?php
        if (isset($_SESSION["login"]) && $_SESSION["login"] === true) {
            $realname = htmlspecialchars($_SESSION["realname"]);
            $username = $_SESSION["username"];
            echo "<span><a href='user_products.php?user=" . ($isAdmin ? "all" : urlencode($realname)) . "' class='account-link'>👤 $realname</a></span> &nbsp;&nbsp| ";
            if ($username !== "admin") {
                echo "<a href='upitem.php'>上傳商品</a> &nbsp;&nbsp| ";
            }
            echo "<a href='home1-8.php'>登出</a>";
        } else {
            echo "<a href='register.php'>註冊</a> &nbsp;&nbsp| 
                  <a href='home1-7.php'>登入</a>";
        }
        ?>
        <button id="theme-toggle" class="theme-toggle">深色</button>
        </div>
    </div>
    <!-- 商品分類列（與首頁一致） -->
    <nav class="category-bar">
        <a href="home.php">首頁</a>
    </nav>

    <div class="main-content">
    <h2 style="text-align:center;"><?php echo htmlspecialchars($showUser); ?> 上傳的商品</h2>
    <!-- 使用者切換列（僅管理員可見） -->
    <?php if ($isAdmin): ?>
    <div class="user-switch-list">
        <a href="user_products.php?user=all" <?php if($user==='all')echo'class="selected"';?>>所有使用者</a>
        <?php foreach ($allUsers as $u): ?>
            <a href="user_products.php?user=<?php echo urlencode($u); ?>" <?php if($user===$u)echo'class="selected"';?>><?php echo htmlspecialchars($u); ?></a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
    <table class="user-products-table">
        <tr>
        <th>商品圖</th>
        <th>商品名稱</th>
        <th>售價</th>
        <th>目前庫存</th>
        <?php if ($isAdmin): ?>
            <th>賣家</th>
        <?php else: ?>
            <th>管理</th>
        <?php endif; ?>
        <th>上架時間</th>
        </tr>
        <?php if (!empty($userProducts)): ?>
        <?php foreach ($userProducts as $product): ?>
            <tr>
            <td>
                <img src="uploads/<?php echo htmlspecialchars($product['image']); ?>" alt="商品圖">
            </td>
            <td>
                <?php echo htmlspecialchars($product['name']); ?>
            </td>
            <td>NT$ <?php echo intval($product['price']); ?></td>
            <td>
                <?php echo isset($product['stock']) ? intval($product['stock']) : '—'; ?>
            </td>
            <?php if ($isAdmin): ?>
                <td>
                    <?php echo htmlspecialchars($product['username']); ?>
                </td>
            <?php else: ?>
                <td>
                    <a class="manage-link" href="edit_product.php?name=<?php echo urlencode($product['name']); ?>">編輯</a>
                </td>
            <?php endif; ?>
            <td>
                <?php echo isset($product['upload_time']) ? htmlspecialchars($product['upload_time']) : '—'; ?>
            </td>
            </tr>
        <?php endforeach; ?>
        <?php else: ?>
        <tr>
            <td colspan="<?php echo $isAdmin ? 6 : 6; ?>" style="color:#e74c3c;">這個帳號尚未上傳任何商品。</td>
        </tr>
        <?php endif; ?>
    </table>
    <a class="back-link" href="home.php">← 回首頁</a>
    </div>
    <script>
    // 深色主題切換（與首頁一致）
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
</body>
<!-- 網站頁腳 -->
<footer style="background:#04344a; color:#fff; padding:40px 0 0 0; margin-top:60px;">
  <div style="max-width:1200px; margin:auto; display:flex; flex-wrap:wrap; justify-content:space-between; align-items:flex-start;">
    <div style="flex:2; min-width:300px;">
      <img src="https://shopping.pchome.com.tw/img/logo24h.png" alt="" style="height:36px; margin-bottom:18px;">
      <div style="margin-bottom:18px;">
        <a href="#" style="color:#fff; margin-right:12px;"><img src="https://cdn-icons-png.flaticon.com/512/733/733547.png" alt="facebook" style="width:24px;vertical-align:middle;"></a>
        <a href="#" style="color:#fff; margin-right:12px;"><img src="https://cdn-icons-png.flaticon.com/512/2111/2111463.png" alt="instagram" style="width:24px;vertical-align:middle;"></a>
        <a href="#" style="color:#fff; margin-right:12px;"><img src="https://cdn-icons-png.flaticon.com/512/2111/2111728.png" alt="line" style="width:24px;vertical-align:middle;"></a>
        <a href="#" style="color:#fff;"><img src="https://cdn-icons-png.flaticon.com/512/1384/1384060.png" alt="youtube" style="width:24px;vertical-align:middle;"></a>
      </div>
      <div style="font-size:15px;line-height:1.8;">
        地址<br>
        統一編號：<br>
        <br>
        保險證號：
      </div>
    </div>
    <div style="flex:3; min-width:200px; display:flex; flex-wrap:wrap;">
      <div style="margin-right:40px;">
        <div style="font-weight:bold; margin-bottom:10px;">關於買GO網</div>
        <div><a href="#" style="color:#fff;">榮耀時刻</a></div>
        <div><a href="#" style="color:#fff;">大事記</a></div>
        <div><a href="#" style="color:#fff;">人才招募</a></div>
        <div><a href="#" style="color:#fff;">隱私權聲明</a></div>
        <div><a href="#" style="color:#fff;">服務條款</a></div>
        <div><a href="#" style="color:#fff;">商品專櫃總覽</a></div>
      </div>
      <div style="margin-right:40px;">
        <div style="font-weight:bold; margin-bottom:10px;">顧客權益</div>
        <div><a href="#" style="color:#fff;">聯絡我們</a></div>
        <div><a href="#" style="color:#fff;">常見Q&A</a></div>
        <div><a href="#" style="color:#fff;">防詐騙宣導</a></div>
        <div><a href="#" style="color:#fff;">退換貨說明</a></div>
        <div><a href="#" style="color:#fff;">24h到貨說明</a></div>
        <div><a href="#" style="color:#fff;">政令宣導</a></div>
      </div>
      <div style="margin-right:40px;">
        <div style="font-weight:bold; margin-bottom:10px;">其他服務</div>
        <div><a href="#" style="color:#fff;">旅遊</a></div>
      </div>
      <div>
        <div style="font-weight:bold; margin-bottom:10px;">企業合作</div>
        <div><a href="#" style="color:#fff;">招商專區</a></div>
        <div><a href="#" style="color:#fff;">媒體聯繫</a></div>
        <div><a href="#" style="color:#fff;">企業／大型採購</a></div>
      </div>
    </div>
    <div style="min-width:180px; text-align:center;">
      <div>
        
      </div>
    </div>
  </div>
  <div style="text-align:center; color:#b5c9d6; font-size:13px; margin-top:30px; padding-bottom:18px; border-top:1px solid #b5c9d6;">
    
  </div>
</footer>
</html>