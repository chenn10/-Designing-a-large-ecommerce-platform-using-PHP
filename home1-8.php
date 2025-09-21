<?php
session_start();
session_destroy(); // 清除所有 session
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>登出成功 - MYGO Home</title>
  <meta http-equiv="refresh" content="5;url=home.php">
  <link rel="stylesheet" href="home_style.css">
  <style>
    html, body {
      height: 100%;
      margin: 0;
      padding: 0;
    }
    body {
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }
    .main-content { /* 你的主內容區塊 class 名稱 */
      flex: 1;
    }
    .message {
      margin-top: 100px;
      background-color: #f8f9fa;
      color: #155724;
      padding: 30px;
      border-radius: 15px;
      display: inline-block;
      font-size: 20px;
      font-weight: bold;
      box-shadow: 0 0 10px rgba(0,0,0,0.2);
      transition: background-color 0.3s, color 0.3s;
    }
    body.dark-theme .message {
      background-color: #1e1e1e;
      color: #a6e6aa;
      border: 1px solid #444;
    }
    a {
      color: #007acc;
      text-decoration: none;
    }
    body.dark-theme a {
      color: #80cfff;
    }
    .center-container {
      text-align: center;
    }

    /* ✅ 固定右上帳號連結為白色 */
    .account-link {
      color: white !important;
    }
    .account-link:hover {
      color: white !important;
      text-decoration: underline;
    }

    footer {
      position: fixed;
      left: 0;
      bottom: 0;
      width: 100%;
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
    echo "<span><a href='user_products.php?user=all' class='account-link'>👤 $realname</a></span> &nbsp;&nbsp| ";
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

  <!-- 第二層：商品分類導覽 -->
  <nav class="category-bar">
    <a href="home.php">首頁</a>
    <a href="#">3C</a>
    <a href="#">通訊</a>
    <a href="#">家電</a>
    <a href="#">日用</a>
    <a href="#">母嬰</a>
    <a href="#">食品</a>
    <a href="#">生活</a>
    <a href="#">居家</a>
    <a href="#">保健</a>
    <a href="#">美妝</a>
    <a href="#">時尚</a>
    <a href="#">書店</a>
  </nav>

  <!-- 登出提示 -->
  <div class="center-container">
    <div class="message">
      ✅ 您已成功登出！<br><br>
      🔄 5秒後自動回首頁，或手動 <a href="home.php">👉 點此前往首頁</a>。
    </div>
  </div>

  <!-- 主題切換 JS（與首頁同步） -->
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