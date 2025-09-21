<?php session_start(); ?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>買 GO 網!!!!!</title>
  <link rel="stylesheet" href="home_style.css">
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
          // 從資料庫獲取購物車數量
          require_once("config/database_sqlite.php");
          $user_id = $_SESSION['user_id'];
          $cartResult = getSingleResult("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?", [$user_id]);
          $cartCount = $cartResult ? $cartResult['total'] : 0;
          echo "<span><a href='user_products.php?user=all' class='account-link'>👤 $realname</a></span> &nbsp;&nbsp;";
          echo "<a href='cart.php' class='cart-link'>購物車";
          if ($cartCount > 0) {
              echo " <span class='cart-badge'>{$cartCount}</span>";
          }
          echo "</a> &nbsp;&nbsp| ";
          if ($username === "admin") {
              echo "<a href='admin_database.php' style='color: #ff6b6b; font-weight: bold;'>🔧 資料庫管理</a> &nbsp;&nbsp| ";
              echo "<a href='upitem.php'>上傳商品</a> &nbsp;&nbsp| ";
          } else {
              echo "<a href='upitem.php'>上傳商品</a> &nbsp;&nbsp| ";
          }
          echo "<a href='home1-8.php'>登出</a>";
      } else {
          echo "<a href='register.php'>註冊</a> &nbsp;&nbsp| 
                <a href='home1-7.php'>登入</a>";
      }
      ?>
      <!-- 主題切換按鈕 -->
      <button id="theme-toggle" class="theme-toggle">深色</button>
    </div>
  </div>

  <!-- 第二層：商品分類導覽 -->
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

  <!-- 主內容區塊 -->
  <div class="main-content">
    <!-- 商品展示區上方無縫跑馬燈 -->
    <div class="marquee-wrap">
      <div class="marquee-content">
        <marquee class="marquee" behavior="scroll" direction="left" scrollamount="8">
          不定期限時發送限量免運或折價券
        </marquee>
      </div>
    </div>
    
    <!-- 商品搜尋區 -->
    <div style="text-align: center; margin: 30px 0;">
      <form method="get" action="home.php" style="display: inline-block;">
        <?php if (isset($_GET['category'])): ?>
          <input type="hidden" name="category" value="<?php echo htmlspecialchars($_GET['category']); ?>">
        <?php endif; ?>
        <input type="text" name="search" placeholder="搜尋商品名稱..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" style="padding: 10px; border: 2px solid #007acc; border-radius: 25px 0 0 25px; width: 300px; font-size: 16px; outline: none;">
        <button type="submit" style="padding: 10px 20px; background: #007acc; color: white; border: 2px solid #007acc; border-radius: 0 25px 25px 0; font-size: 16px; cursor: pointer; margin-left: -2px;">🔍 搜尋</button>
        <?php if (isset($_GET['search']) && $_GET['search'] !== ''): ?>
          <a href="home.php<?php echo isset($_GET['category']) ? '?category=' . urlencode($_GET['category']) : ''; ?>" style="margin-left: 10px; color: #666; text-decoration: none;">✖ 清除搜尋</a>
        <?php endif; ?>
      </form>
      
      <!-- 顯示購物車操作訊息 -->
      <?php if (isset($_SESSION['cart_message'])): ?>
        <div style="margin-top: 15px; padding: 10px; background: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 5px; display: inline-block;">
          <?php echo htmlspecialchars($_SESSION['cart_message']); ?>
        </div>
        <?php unset($_SESSION['cart_message']); ?>
      <?php endif; ?>
      
      <!-- 顯示評分操作訊息 -->
      <?php if (isset($_SESSION['rating_message'])): ?>
        <div style="margin-top: 15px; padding: 10px; background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; border-radius: 5px; display: inline-block;">
          <?php echo htmlspecialchars($_SESSION['rating_message']); ?>
        </div>
        <?php unset($_SESSION['rating_message']); ?>
      <?php endif; ?>
    </div>
    <h2 style="text-align: center">
      商品展示區
      <?php if (isset($_GET['search']) && $_GET['search'] !== ''): ?>
        <small style="color: #666; font-size: 16px;">
          - 搜尋「<?php echo htmlspecialchars($_GET['search']); ?>」找到 <?php echo $total; ?> 件商品
        </small>
      <?php elseif (isset($_GET['category']) && $_GET['category'] !== ''): ?>
        <small style="color: #666; font-size: 16px;">
          - <?php echo htmlspecialchars($_GET['category']); ?> 分類 (<?php echo $total; ?> 件商品)
        </small>
      <?php endif; ?>
    </h2>
    <?php
      require_once("config/database_sqlite.php");
      
      // 建立基本查詢
      $sql = "SELECT * FROM products WHERE 1=1";
      $params = [];
      
      // 分類篩選
      if (isset($_GET['category']) && $_GET['category'] !== '') {
        $sql .= " AND category = ?";
        $params[] = $_GET['category'];
      }
      
      // 搜尋篩選
      if (isset($_GET['search']) && $_GET['search'] !== '') {
        $sql .= " AND (name LIKE ? OR description LIKE ?)";
        $searchTerm = '%' . $_GET['search'] . '%';
        $params[] = $searchTerm;
        $params[] = $searchTerm;
      }
      
      $sql .= " ORDER BY created_at DESC";
      $products = getMultipleResults($sql, $params);

      $perPage = 36;
      $total = count($products);
      $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
      $start = ($page - 1) * $perPage;
      $currentProducts = array_slice($products, $start, $perPage);
      $totalPages = ceil($total / $perPage);
    ?>

    <style>
    .grid-container {
      display: grid;
      grid-template-columns: repeat(6, 1fr);
      gap: 20px;
      margin: 30px 50px;  /* 增加左右邊距，讓商品區域有更多空間 */
    }
    .product-card {
      background-color: white;
      border-radius: 10px;
      box-shadow: 0 0 5px #aaa;
      padding: 10px;
      text-align: center;
      transition: background-color 0.3s, color 0.3s;
    }
    body.dark-theme .product-card {
      background-color: rgba(45, 44, 44, 0.95);
      color: #f0f0f0;
      box-shadow: 0 0 5px #555;
    }
    .product-card img {
      width: 100%;
      height: 150px;
      object-fit: cover;
      border-radius: 8px;
    }
    .product-name {
      margin: 10px 0 5px;
      font-weight: bold;
      font-size: 16px;
    }
    .product-price {
      color: red;
      font-size: 18px;
    }
    .pagination {
      display: flex;
      justify-content: center;
      align-items: center;
      margin-top: 20px;
    }
    .pagination a, .pagination button {
      padding: 10px 20px;
      margin: 0 5px;
      background-color: #007acc;
      color: white;
      border-radius: 5px;
      text-decoration: none;
      cursor: pointer;
    }
    .pagination button {
      background-color: #f0f0f0;
      color: #007acc;
      border: 1px solid #007acc;
    }
    .pagination .active {
      background-color: #005c8d;
      color: white;
    }
    </style>

  <div class="grid-container">
    <?php foreach ($currentProducts as $product): ?>
  <a href="product_detail.php?name=<?php echo urlencode($product['name']); ?>" target="" style="text-decoration:none; color:inherit;">
    <div class="product-card">
      <img src="uploads/<?php echo htmlspecialchars($product['image']); ?>" alt="商品圖">
      <div class="product-name"><?php echo htmlspecialchars($product['name']); ?></div>
      <div class="product-price">NT$ <?php echo intval($product['price']); ?></div>
      <?php if (isset($product['rating'])): ?>
        <div class="product-rating" style="margin: 5px 0; font-size: 14px;">
          <?php
          $rating = floatval($product['rating']);
          $fullStars = floor($rating);
          $halfStar = ($rating - $fullStars) >= 0.5 ? 1 : 0;
          $emptyStars = 5 - $fullStars - $halfStar;
          
          // 顯示星星
          echo '<span style="color: #ffa500;">';
          for ($i = 0; $i < $fullStars; $i++) echo '★';
          if ($halfStar) echo '☆';
          for ($i = 0; $i < $emptyStars; $i++) echo '☆';
          echo '</span>';
          echo ' <span style="color: #666; font-size: 12px;">(' . $rating . '/5.0)</span>';
          ?>
        </div>
      <?php endif; ?>
      <div style="font-size: 12px; color: <?php echo $product['stock'] <= 5 ? '#e60012' : '#666'; ?>; margin-top: 5px;">
        庫存：<?php echo $product['stock']; ?> 件
        <?php if ($product['stock'] <= 5): ?>
          <span style="color: #e60012;">⚠ 庫存不足</span>
        <?php endif; ?>
      </div>
    </div>
  </a>
<?php endforeach; ?>
</div>

    <!-- 分頁 -->
    <div class="pagination">
      <?php
        $showRange = 3;
        // 建立URL參數
        $urlParams = [];
        if (isset($_GET['category'])) $urlParams['category'] = $_GET['category'];
        if (isset($_GET['search'])) $urlParams['search'] = $_GET['search'];
        $baseUrl = '?' . http_build_query($urlParams);
        $separator = empty($urlParams) ? '?' : '&';
        
        // 上一頁與第一頁
        if ($page > 1) {
          echo '<a href="'.$baseUrl.$separator.'page=1">&lt;&lt;</a>';
          echo '<a href="'.$baseUrl.$separator.'page='.($page-1).'">上一頁</a>';
        }
        // 頁碼區間
        $start = max(1, $page - $showRange);
        $end = min($totalPages, $page + $showRange);
        for ($i = $start; $i <= $end; $i++) {
          if ($i == $page) {
            echo '<a href="'.$baseUrl.$separator.'page='.$i.'" class="active">'.$i.'</a>';
          } else {
            echo '<a href="'.$baseUrl.$separator.'page='.$i.'">'.$i.'</a>';
          }
        }
        // 下一頁與最後一頁
        if ($page < $totalPages) {
          echo '<a href="'.$baseUrl.$separator.'page='.($page+1).'">下一頁</a>';
          echo '<a href="'.$baseUrl.$separator.'page='.$totalPages.'">&gt;&gt;</a>';
        }
      ?>
    </div>

  </div>

  <!-- 切換主題 JavaScript -->
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

        // 防止重複點擊
        if (isDark && btn.textContent === "深色") return;
        if (!isDark && btn.textContent === "亮色") return;

        // 切換主題
        document.body.classList.toggle("dark-theme");
        const nowDark = document.body.classList.contains("dark-theme");
        localStorage.setItem("theme", nowDark ? "dark" : "light");
        btn.textContent = nowDark ? "亮色" : "深色";
      };
    };
  </script>

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
