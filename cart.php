<?php
session_start();
require_once("config/database_sqlite.php");

// 檢查用戶是否登入
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header('Location: home1-7.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// 從資料庫獲取購物車項目
$cartItems = getMultipleResults("
    SELECT c.*, p.name, p.price, p.image, p.stock 
    FROM cart c 
    JOIN products p ON c.product_id = p.id 
    WHERE c.user_id = ?
    ORDER BY c.created_at DESC
", [$user_id]);

// 計算購物車總數量
$cartCount = 0;
foreach ($cartItems as $item) {
    $cartCount += $item['quantity'];
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>購物車</title>
  <link rel="stylesheet" href="home_style.css">
  <style>
    .cart-list {
      max-width: 700px;
      margin: 40px auto;
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 2px 12px #bbb;
      padding: 24px;
      transition: background 0.3s, color 0.3s;
    }
    body.dark-theme .cart-list {
      background: #232323;
      box-shadow: 0 2px 12px #222;
      color: #fff;
    }
    .cart-item {
      display: flex;
      align-items: center;
      border-bottom: 1px solid #eee;
      padding: 18px 0;
    }
    body.dark-theme .cart-item {
      border-bottom: 1px solid #444;
    }
    .cart-item:last-child { border-bottom: none; }
    .cart-img { width: 80px; height: 80px; object-fit: cover; border-radius: 8px; margin-right: 18px; }
    .cart-info { flex: 1; }
    .cart-title { font-size: 18px; font-weight: bold; }
    .cart-price { color: #e60012; font-size: 18px; font-weight: bold; margin-top: 6px; }
    .cart-qty { font-size: 16px; margin-left: 10px; }
    .cart-remove { color: #ffdd66; margin-left: 18px; text-decoration: underline; cursor: pointer; background: none; border: none; font-size: 16px;}
    body:not(.dark-theme) .cart-remove { color: #e60012; }
    .cart-empty { text-align: center; color: #888; font-size: 20px; margin: 40px 0; }
    .cart-header {
      display: flex;
      align-items: center;
      font-size: 26px;
      font-weight: bold;
      margin-bottom: 18px;
      gap: 10px;
    }
    .cart-header .cart-link {
      color: #fff;
      font-size: 18px;
      font-weight: bold;
      margin-left: 10px;
      text-decoration: none;
      position: relative;
    }
    .cart-header .cart-badge {
      display: inline-block;
      background: #fff;
      color: #e60012;
      border-radius: 50%;
      font-size: 16px;
      font-weight: bold;
      min-width: 26px;
      height: 26px;
      line-height: 26px;
      text-align: center;
      margin-left: 4px;
      vertical-align: middle;
    }
    body:not(.dark-theme) .cart-header .cart-link { color: #222; }
    body:not(.dark-theme) .cart-header .cart-badge { background: #e60012; color: #fff; }
    .cart-list a {
      color: #6a2cff;
      text-decoration: none;
    }
    .cart-list a:visited {
      color: #6a2cff;
    }
    .cart-list a:hover {
      text-decoration: underline;
    }
    footer {
      position: fixed;
      left: 0;
      bottom: 0;
      width: 100%;
    }
    /* 深色主題下的數量輸入框樣式 */
    body.dark-theme input[type="number"] {
      background-color: #333;
      color: #fff;
      border: 1px solid #555;
    }
    body.dark-theme .cart-qty button {
      background: #444 !important;
      color: #fff !important;
    }
    body.dark-theme .cart-qty button:hover {
      background: #666 !important;
    }
  </style>
</head>
<body>
  <!-- 第一層：帳號資訊導覽 -->
  <div class="top-bar">
    <div class="top-bar-left">
      <span>歡迎來到 買GO網!!!!!</span>
    </div>
    <div class="top-bar-right">
      <?php
      if (isset($_SESSION["login"]) && $_SESSION["login"] === true) {
          $realname = htmlspecialchars($_SESSION["realname"]);
          $username = $_SESSION["username"];
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
      } else {
          echo "<a href='register.php'>註冊</a> &nbsp;&nbsp| 
                <a href='home1-7.php'>登入</a>";
      }
      ?>
      <button id="theme-toggle" class="theme-toggle">深色</button>
    </div>
  </div>
  <!-- 商品分類導覽列（僅首頁） -->
  <nav class="category-bar">
    <a href="home.php">首頁</a>
  </nav>
  <div class="cart-list">
    <div class="cart-header">
      <span style="font-size:22px;">🛒</span> 購物車
      <a href="cart.php" class="cart-link">
        <?php if ($cartCount > 0): ?>
          <span class="cart-badge"><?php echo $cartCount; ?></span>
        <?php endif; ?>
      </a>
    </div>
    
    <!-- 顯示購物車操作訊息 -->
    <?php if (isset($_SESSION['cart_message'])): ?>
      <div style="margin-bottom: 15px; padding: 10px; background: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 5px;">
        <?php echo htmlspecialchars($_SESSION['cart_message']); ?>
      </div>
      <?php unset($_SESSION['cart_message']); ?>
    <?php endif; ?>
    <?php if (empty($cartItems)): ?>
      <div class="cart-empty">購物車是空的</div>
    <?php else: ?>
      <?php foreach ($cartItems as $item): ?>
        <div class="cart-item">
          <img class="cart-img" src="uploads/<?php echo htmlspecialchars($item['image']); ?>" alt="商品圖">
          <div class="cart-info">
            <div class="cart-title"><?php echo htmlspecialchars($item['name']); ?></div>
            <div class="cart-price">NT$ <?php echo intval($item['price']); ?></div>
            <div class="cart-qty">
              <form method="post" action="update_cart_quantity.php" style="display:inline-block; margin-right: 10px;">
                <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                <label for="qty_<?php echo $item['product_id']; ?>">數量：</label>
                <input type="number" id="qty_<?php echo $item['product_id']; ?>" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $item['stock']; ?>" style="width: 60px; padding: 2px; border: 1px solid #ccc; border-radius: 3px;">
                <button type="submit" style="background: #007acc; color: white; border: none; padding: 4px 8px; border-radius: 3px; cursor: pointer; font-size: 12px;">更新</button>
              </form>
            </div>
          </div>
          <form method="post" action="remove_from_cart.php" style="margin:0;">
            <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
            <button type="submit" class="cart-remove">刪除</button>
          </form>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
    <a href="home.php" style="display:inline-block;margin-top:24px;">← 回首頁</a>
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