<?php session_start(); ?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>MYGO Home</title>
  <link rel="stylesheet" href="home_style.css">
  <style>
    .login-container {
      min-height: 80vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      margin: 0;
      padding: 20px;
    }
    .login-panel {
      width: 420px;
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      border-radius: 20px;
      padding: 40px;
      box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1), 0 5px 15px rgba(0, 0, 0, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.2);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .login-panel:hover {
      transform: translateY(-5px);
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15), 0 10px 20px rgba(0, 0, 0, 0.1);
    }
    .login-panel h1 {
      text-align: center;
      margin-bottom: 30px;
      color: #333;
      font-size: 28px;
      font-weight: 600;
      background: linear-gradient(135deg, #667eea, #764ba2);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    .login-form {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }
    .input-group {
      position: relative;
    }
    .input-group label {
      position: absolute;
      top: -10px;
      left: 15px;
      background: rgba(255, 255, 255, 0.9);
      padding: 0 8px;
      color: #666;
      font-size: 14px;
      font-weight: 500;
      border-radius: 10px;
      z-index: 1;
    }
    .input-group input {
      width: 100%;
      padding: 15px 20px;
      border: 2px solid #e1e8ed;
      border-radius: 15px;
      font-size: 16px;
      background: rgba(255, 255, 255, 0.8);
      transition: all 0.3s ease;
      box-sizing: border-box;
    }
    .input-group input:focus {
      outline: none;
      border-color: #667eea;
      background: rgba(255, 255, 255, 0.95);
      box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    .login-btn {
      width: 100%;
      padding: 15px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      border: none;
      border-radius: 15px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      margin-top: 10px;
      text-transform: uppercase;
      letter-spacing: 1px;
    }
    .login-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
    }
    .login-btn:active {
      transform: translateY(0);
    }
    .register-link {
      text-align: center;
      margin-top: 20px;
      color: #666;
    }
    .register-link a {
      color: #667eea;
      text-decoration: none;
      font-weight: 600;
      transition: color 0.3s ease;
    }
    .register-link a:hover {
      color: #764ba2;
    }
    /* 深色主題優化 */
    body.dark-theme .login-container {
      background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
    }
    body.dark-theme .login-panel {
      background: rgba(45, 44, 44, 0.95);
      border: 1px solid rgba(255, 255, 255, 0.1);
    }
    body.dark-theme .login-panel h1 {
      color: #f0f0f0;
      background: linear-gradient(135deg, #74b9ff, #a29bfe);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    body.dark-theme .input-group label {
      background: rgba(45, 44, 44, 0.9);
      color: #ccc;
    }
    body.dark-theme .input-group input {
      background: rgba(60, 60, 60, 0.8);
      border-color: #555;
      color: #f0f0f0;
    }
    body.dark-theme .input-group input:focus {
      border-color: #74b9ff;
      background: rgba(60, 60, 60, 0.95);
      box-shadow: 0 0 0 3px rgba(116, 185, 255, 0.1);
    }
    body.dark-theme .login-btn {
      background: linear-gradient(135deg, #74b9ff 0%, #a29bfe 100%);
    }
    body.dark-theme .register-link {
      color: #ccc;
    }
    body.dark-theme .register-link a {
      color: #74b9ff;
    }
    body.dark-theme .register-link a:hover {
      color: #a29bfe;
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

  <!-- 主區塊 -->
  <div class="login-container">
    <!-- 登入表單 -->
    <div class="login-panel">
      <h1>🔐 登入系統</h1>
      <form action="login_receive.php" method="post" class="login-form">
        <div class="input-group">
          <label for="username">帳號</label>
          <input type="text" name="username" id="username" required placeholder="請輸入您的帳號">
        </div>
        <div class="input-group">
          <label for="password">密碼</label>
          <input type="password" name="password" id="password" required placeholder="請輸入您的密碼">
        </div>
        <button type="submit" class="login-btn">登入</button>
      </form>
      <div class="register-link">
        還沒有帳號嗎？<a href="register.php">立即註冊</a>
      </div>
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