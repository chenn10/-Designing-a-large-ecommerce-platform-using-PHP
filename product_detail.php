<!-- product_detail.php -->
<?php
session_start();
require_once("config/database_sqlite.php");

$name = $_GET['name'] ?? '';
$product = getSingleResult("SELECT * FROM products WHERE name = ?", [$name]);

if (!$product) {
  echo "找不到商品";
  exit;
}

// 購物車商品數量 (如果用戶已登入)
$cartCount = 0;
if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
    $user_id = $_SESSION['user_id'];
    $cartResult = getSingleResult("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?", [$user_id]);
    $cartCount = $cartResult ? $cartResult['total'] : 0;
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title><?php echo htmlspecialchars($product['name']); ?> - 商品資訊</title>
  <link rel="stylesheet" href="home_style.css">
  <style>
    body {
      background-color: #f0f4f8;
      margin: 0;
      font-family: 'Arial', sans-serif;
    }
    .product-main {
      max-width: 1250px;
      margin: 40px auto;
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 0 18px #bbb;
      display: flex;
      gap: 40px;
      padding: 40px 30px 30px 30px;
      align-items: flex-start;
      transition: background 0.3s, color 0.3s;
    }
    .product-main .left {
      flex: 1.2;
      min-width: 520px;
      display: flex;
      flex-direction: column;
      align-items: center;
    }
    .product-main .left img {
      width: 100%;
      max-width: 520px;
      border-radius: 14px;
      box-shadow: 0 2px 10px #aaa;
      background: #f5f5f5;
    }
    .product-main .gallery {
      margin-top: 14px;
      display: flex;
      gap: 8px;
    }
    .product-main .gallery img {
      width: 54px;
      height: 54px;
      object-fit: cover;
      border-radius: 7px;
      border: 2px solid #eee;
      cursor: pointer;
      transition: border 0.2s;
    }
    .product-main .gallery img:hover {
      border: 2px solid #007acc;
    }
    .product-main .right {
      flex: 2;
      display: flex;
      flex-direction: column;
      justify-content: flex-start;
    }
    .product-title {
      font-size: 24px;
      font-weight: bold;
      margin-bottom: 10px;
      color: #003366;
    }
    .product-price {
      color: #e74c3c;
      font-size: 22px;
      font-weight: bold;
      margin-bottom: 10px;
    }
    .product-tags {
      margin-bottom: 16px;
      font-size: 14px;
      color: #555;
      display: flex;
      gap: 10px;
    }
    .product-tags span {
      background: #f0f4f8;
      color: #007acc;
      border-radius: 7px;
      padding: 2px 12px;
      margin-right: 0;
      font-weight: bold;
      font-size: 13px;
      transition: background 0.3s, color 0.3s;
    }
    /* 深色主題下標籤顏色 */
    body.dark-theme .product-tags span {
      background: #003366;
      color: #ffdd66;
    }
    .product-summary {
      font-size: 15px;
      color: #222;
      margin-bottom: 16px;
      line-height: 1.7;
      background: #f8fafd;
      border-radius: 7px;
      padding: 12px 14px 8px 14px;
    }
    .product-info-table {
      width: 100%;
      margin-bottom: 12px;
      border-collapse: collapse;
      font-size: 13px;
    }
    .product-info-table th, .product-info-table td {
      padding: 6px 10px;
      text-align: left;
    }
    .product-info-table th {
      color: #888;
      font-weight: normal;
      width: 70px;
      background: none;
    }
    .product-info-table td {
      color: #222;
      background: none;
    }
    .buy-buttons {
      margin-top: 12px;
    }
    .buy-buttons button {
      padding: 10px 26px;
      font-size: 15px;
      border: none;
      margin-right: 14px;
      cursor: pointer;
      border-radius: 6px;
      font-weight: bold;
      transition: background 0.2s, color 0.2s;
    }
    .buy-buttons .add-cart {
      background-color: #007acc;
      color: white;
    }
    .buy-buttons .add-cart:hover {
      background-color: #005999;
    }
    .buy-buttons .buy-now {
      background-color: #e74c3c;
      color: white;
    }
    .buy-buttons .buy-now:hover {
      background-color: #c0392b;
    }
    @media (max-width: 900px) {
      .product-main { flex-direction: column; gap: 20px; padding: 20px 5vw; }
      .product-main .left, .product-main .right { width: 100%; min-width: 0; }
      .product-main .left img { max-width: 100%; }
    }
    body.dark-theme .product-main {
      background: #232323;
      color: #f0f0f0;
      box-shadow: 0 0 12px #222;
    }
    body.dark-theme .product-title { color: #ffdd66; }
    body.dark-theme .product-summary { background: #222; color: #f0f0f0; }
    body.dark-theme .product-info-table th { color: #aaa; }
    body.dark-theme .product-info-table td { color: #f0f0f0; }
    /* 賣家資訊樣式 */
    .seller-info-box {
      max-width: 1250px;
      margin: 28px auto 0 auto;
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 0 18px #bbb;
      padding: 28px 40px 18px 40px;
      font-size: 20px;
      font-weight: bold;
      color: #222;
      border: none;
      transition: background 0.3s, color 0.3s;
    }
    .seller-info-box span {
      color: #111;
    }
    body.dark-theme .seller-info-box {
      background: #232323;
      color: #fff;
      box-shadow: 0 0 18px #222;
    }
    body.dark-theme .seller-info-box span {
      color: #fff;
    }
    /* 商品詳情區塊樣式 */
    .product-detail-section {
      max-width: 1250px;
      margin: 40px auto 0 auto;
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 0 18px #bbb;
      padding: 32px 40px 32px 40px;
      font-size: 18px;
      color: #222;
      transition: background 0.3s, color 0.3s;
    }
    .detail-title {
      font-size: 22px;
      font-weight: bold;
      color: #003366;
      margin-bottom: 18px;
    }
    .detail-content {
      font-size: 17px;
      color: #222;
      line-height: 1.8;
      white-space: pre-line;
    }
    body.dark-theme .product-detail-section {
      background: #232323;
      color: #f0f0f0;
      box-shadow: 0 0 18px #222;
    }
    body.dark-theme .detail-title {
      color: #ffdd66;
    }
    body.dark-theme .detail-content {
      color: #f0f0f0;
    }
    .cart-link {
      color: #fff;
      font-weight: bold;
      position: relative;
      margin-left: 8px;
      text-decoration: none;
    }
    .cart-badge {
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
          echo "<span><a href='user_products.php?user=all' class='account-link'>👤 $realname</a></span> &nbsp;&nbsp;";
          // 購物車顯示
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
  <nav class="category-bar">
    <a href="home.php">首頁</a>
    <a href="home.php?category=3C">3C</a>
    <a href="home.php?category=通訊">通訊</a>
    <a href="home.php?category=家電">家電</a>
    <a href="home.php?category=日用">日用</a>
    <a href="home.php?category=母嬰">母嬰</a>
    <a href="home.php?category=食品">食品</a>
    <a href="home.php?category=生活">生活</a>
    <a href="home.php?category=居家">居家</a>
    <a href="home.php?category=保健">保健</a>
    <a href="home.php?category=美妝">美妝</a>
    <a href="home.php?category=時尚">時尚</a>
    <a href="home.php?category=書店">書店</a>
  </nav>

  <!-- 商品主內容區 -->
  <div class="product-main">
    <div class="left">
      <img src="uploads/<?php echo htmlspecialchars($product['image']); ?>" alt="商品圖片" id="main-img">
      <!-- 若有多圖可加縮圖預覽，這裡僅示範一張 -->
      <!--
      <div class="gallery">
        <img src="uploads/xxx.jpg" onclick="document.getElementById('main-img').src=this.src;">
      </div>
      -->
    </div>
    <div class="right">
      <div class="product-title"><?php echo htmlspecialchars($product['name']); ?></div>
      <div class="product-price">NT$ <?php echo intval($product['price']); ?></div>
      
      <!-- 商品評分顯示 -->
      <?php if (isset($product['rating']) && $product['rating'] > 0): ?>
        <div class="product-rating" style="margin: 10px 0; font-size: 16px;">
          <?php
          $rating = floatval($product['rating']);
          $ratingCount = isset($product['rating_count']) ? intval($product['rating_count']) : 0;
          $fullStars = floor($rating);
          $halfStar = ($rating - $fullStars) >= 0.5 ? 1 : 0;
          $emptyStars = 5 - $fullStars - $halfStar;
          
          // 顯示星星
          echo '<span style="color: #ffa500; font-size: 18px;">';
          for ($i = 0; $i < $fullStars; $i++) echo '★';
          if ($halfStar) echo '☆';
          for ($i = 0; $i < $emptyStars; $i++) echo '☆';
          echo '</span>';
          echo ' <span style="color: #666; margin-left: 8px;">' . $rating . '/5.0 (' . $ratingCount . ' 評分)</span>';
          ?>
        </div>
      <?php endif; ?>
      
      <div class="product-tags">
        <span><?php echo htmlspecialchars($product['category']); ?></span>
        <span>庫存 <?php echo intval($product['stock']); ?> 件</span>
      </div>
      <!-- 商品描述（簡短） -->
      <div class="product-summary">
        <?php echo nl2br(htmlspecialchars($product['description'])); ?>
      </div>
      <table class="product-info-table">
        <tr>
          <th>上架時間</th>
          <td><?php echo isset($product['upload_time']) ? htmlspecialchars($product['upload_time']) : '—'; ?></td>
        </tr>
      </table>
      <div class="buy-buttons">
        <form method="post" action="add_to_cart.php" style="display:inline;">
          <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($product['name']); ?>">
          <button type="submit" class="add-cart">加入購物車</button>
        </form>
      </div>
      
      <!-- 評分功能 -->
      <?php if (isset($_SESSION["login"]) && $_SESSION["login"] === true): ?>
        <div class="rating-section" style="margin-top: 25px; padding: 20px; background: #f8fafd; border-radius: 10px; border: 1px solid #e1e8ed;">
          <h4 style="margin: 0 0 15px 0; color: #333; font-size: 16px;">為這個商品評分</h4>
          <form method="post" action="rate_product.php" style="display: flex; align-items: center; gap: 15px;">
            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
            <div class="star-rating" style="display: flex; gap: 5px;">
              <?php for ($i = 1; $i <= 5; $i++): ?>
                <label style="cursor: pointer; font-size: 24px; color: #ddd; transition: color 0.2s;">
                  <input type="radio" name="rating" value="<?php echo $i; ?>" style="display: none;" onchange="updateStars(this)">
                  <span class="star" data-value="<?php echo $i; ?>">☆</span>
                </label>
              <?php endfor; ?>
            </div>
            <button type="submit" style="padding: 8px 16px; background: #007acc; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 14px;">送出評分</button>
          </form>
        </div>
      <?php else: ?>
        <div style="margin-top: 25px; padding: 15px; background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 8px; color: #856520; text-align: center;">
          <a href="home1-7.php" style="color: #007acc; text-decoration: none; font-weight: bold;">登入</a> 後可以為商品評分
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- 賣家資訊區塊 -->
  <div class="seller-info-box">
    賣家：<span><?php echo isset($product['username']) ? htmlspecialchars($product['username']) : '—'; ?></span>
  </div>

  <!-- 商品詳情區塊 -->
  <div class="product-detail-section">
    <div class="detail-title">商品詳情</div>
    <div class="detail-content">
      <?php echo nl2br(htmlspecialchars($product['detail'] ?? '')); ?>
    </div>
  </div>

  <script>
    // 深色主題切換
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
    
    // 星星評分交互效果
    function updateStars(selectedInput) {
      const value = parseInt(selectedInput.value);
      const stars = document.querySelectorAll('.star');
      
      stars.forEach((star, index) => {
        const starValue = parseInt(star.getAttribute('data-value'));
        if (starValue <= value) {
          star.textContent = '★';
          star.style.color = '#ffa500';
        } else {
          star.textContent = '☆';
          star.style.color = '#ddd';
        }
      });
    }
    
    // 星星懸停效果
    document.addEventListener('DOMContentLoaded', function() {
      const stars = document.querySelectorAll('.star');
      const labels = document.querySelectorAll('.star-rating label');
      
      labels.forEach((label, index) => {
        label.addEventListener('mouseenter', function() {
          for (let i = 0; i <= index; i++) {
            stars[i].textContent = '★';
            stars[i].style.color = '#ffa500';
          }
          for (let i = index + 1; i < stars.length; i++) {
            stars[i].textContent = '☆';
            stars[i].style.color = '#ddd';
          }
        });
        
        label.addEventListener('mouseleave', function() {
          const checkedInput = document.querySelector('input[name="rating"]:checked');
          if (checkedInput) {
            updateStars(checkedInput);
          } else {
            stars.forEach(star => {
              star.textContent = '☆';
              star.style.color = '#ddd';
            });
          }
        });
      });
    });
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