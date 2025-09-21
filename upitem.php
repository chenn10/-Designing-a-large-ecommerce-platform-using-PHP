<?php session_start(); ?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>上傳商品 - MYGO Home</title>
  <link rel="stylesheet" href="home_style.css">
  <style>
    label {
      font-weight: bold;
      display: block;
      margin-top: 15px;
      text-align: left;
    }
    input, textarea, select {
      width: 100%;
      padding: 10px;
      border-radius: 5px;
      border: 1px solid #ccc;
      box-sizing: border-box;
      margin-top: 5px;
    }
    input[type="submit"] {
      width: 140px;
      height: 48px;
      font-size: 20px;
      padding: 10px 0;
      font-weight: bold;
      border: none;
      border-radius: 8px;
      margin-top: 28px;
      margin-right: 12px;
      background-color: #007acc;
      color: white;
      box-shadow: 0 2px 8px #007acc44;
      cursor: pointer;
      transition: background 0.2s, color 0.2s;
    }
    input[type="submit"]:hover {
      background-color: #005fa3;
    }
    input[type="reset"] {
      width: 100px;
      height: 38px;
      font-size: 16px;
      padding: 6px 0;
      font-weight: bold;
      border: none;
      border-radius: 8px;
      margin-top: 28px;
      margin-right: 12px;
      background-color: #999;
      color: white;
      cursor: pointer;
      transition: background 0.2s, color 0.2s;
    }
    input[type="reset"]:hover {
      background-color: #666;
    }
    .product-detail-box {
      background: #f8fafd;
      border-radius: 10px;
      padding: 18px 18px 14px 18px;
      margin-bottom: 18px;
      box-shadow: 0 1px 6px #eee;
      transition: background 0.3s, color 0.3s;
    }
    .detail-title {
      color: #003366;
      margin-bottom: 10px;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    body.dark-theme .product-detail-box {
      background: #232323;
      box-shadow: 0 1px 6px #222;
    }
    body.dark-theme .detail-title {
      color: #ffdd66;
    }
  </style>
</head>
<body>

<!-- 導覽列 -->
<div class="top-bar">
  <div class="top-bar-left">
    <span>歡迎來到 買GO網!!!!!</span>
  </div>
  <div class="top-bar-right">
    <?php
    if (isset($_SESSION["login"]) && $_SESSION["login"] === true) {
        $realname = htmlspecialchars($_SESSION["realname"]);
        echo "<span><a href='user_products.php?user={$realname}' class='account-link'>👤 $realname</a></span> &nbsp;&nbsp| 
            <a href='upitem.php'>上傳商品</a> &nbsp;&nbsp| 
            <a href='home1-8.php'>登出</a>";
    } else {
        echo "<a href='register.php' class='account-link'>註冊</a> &nbsp;&nbsp| 
              <a href='home1-7.php' class='account-link'>登入</a>";
    }
    ?>
    <button id="theme-toggle" class="theme-toggle">深色</button>
  </div>
</div>
<!-- 首頁分類列 -->
<nav class="category-bar">
  <a href="home.php">首頁</a>
</nav>

<!-- 表單主體 -->
<div class="main-content">
  <div class="box-panel">
    <h1>📦 上傳商品資訊</h1>

    <form action="upload_product_receive.php" method="post" enctype="multipart/form-data">
      <label for="product_name">📌 商品名稱：</label>
      <input type="text" id="product_name" name="product_name" required>

      <label for="category">📂 分類：</label>
      <select id="category" name="category" required>
        <option value="">請選擇分類</option>
        <option value="3C">3C</option>
        <option value="通訊">通訊</option>
        <option value="家電">家電</option>
        <option value="日用">日用</option>
        <option value="母嬰">母嬰</option>
        <option value="食品">食品</option>
        <option value="生活">生活</option>
        <option value="居家">居家</option>
        <option value="保健">保健</option>
        <option value="美妝">美妝</option>
        <option value="時尚">時尚</option>
        <option value="書店">書店</option>
      </select>

      <label for="price">💰 價格（NT$）：</label>
      <input type="number" id="price" name="price" min="0" step="1" required>

      <label for="stock">📦 庫存數量：</label>
      <input type="number" id="stock" name="stock" min="1" required>

      <label for="product_image">🖼️ 商品圖片（JPG/JPEG/PNG）：</label>
      <input type="file" id="product_image" name="product_image" accept=".jpg,.jpeg,.png" required>

      <!-- 商品描述 -->
      <label for="description">📝 商品描述（簡短）：</label>
      <textarea id="description" name="description" rows="2" maxlength="100" required placeholder="簡短描述，會顯示在商品主頁右側"></textarea>

      <!-- 商品詳情 -->
      <label for="detail">📄 商品詳情（完整說明）：</label>
      <textarea id="detail" name="detail" rows="6" required placeholder="請輸入完整商品資訊，會顯示在商品頁下方"></textarea>

      <input type="submit" value="上傳商品">
      <input type="reset" value="清除重填">
    </form>
  </div>
</div>

<!-- 主題切換 JS -->
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
  };
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