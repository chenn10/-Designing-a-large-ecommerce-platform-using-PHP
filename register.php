<?php
session_start();
require_once("config/database_sqlite.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    $realname = trim($_POST["realname"]);

    // 基本檢查
    if ($username === "" || $password === "" || $realname === "") {
        $error = "所有欄位皆必填！";
    } else {
        // 檢查帳號是否已存在
        $existingUser = getSingleResult("SELECT id FROM users WHERE username = ? OR email = ?", [$username, $username . '@example.com']);
        
        if ($existingUser) {
            $error = "帳號已存在，請換一個帳號。";
        } else {
            // 新增用戶到資料庫
            $result = executeQuery("INSERT INTO users (username, email, password) VALUES (?, ?, ?)", [
                $username, 
                $username . '@example.com', // 暫時使用 username@example.com 作為email
                $password
            ]);

            if ($result) {
                // 註冊成功，導向登入頁
                header("Location: home1-7.php?register=success");
                exit();
            } else {
                $error = "註冊失敗，請稍後再試。";
            }
        }
    }
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>註冊新帳號 - MYGO Home</title>
    <link rel="stylesheet" href="home_style.css">
    <style>
        .register-container {
            min-height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
            margin: 0;
            padding: 20px;
        }
        .register-panel {
            width: 450px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1), 0 5px 15px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .register-panel:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15), 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        .register-panel h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
            font-size: 28px;
            font-weight: 600;
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .register-form {
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
        .input-group input[type="text"],
        .input-group input[type="password"] {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e1e8ed;
            border-radius: 15px;
            font-size: 16px;
            background: rgba(255, 255, 255, 0.8);
            transition: all 0.3s ease;
            box-sizing: border-box;
        }
        .input-group input[type="text"]:focus,
        .input-group input[type="password"]:focus {
            outline: none;
            border-color: #ff6b6b;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 0 0 3px rgba(255, 107, 107, 0.1);
        }
        .register-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
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
        .register-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(255, 107, 107, 0.3);
        }
        .register-btn:active {
            transform: translateY(0);
        }
        .login-link {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }
        .login-link a {
            color: #ff6b6b;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }
        .login-link a:hover {
            color: #ee5a24;
        }
        .message {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 500;
        }
        .error-message {
            background: rgba(231, 76, 60, 0.1);
            color: #e74c3c;
            border: 1px solid rgba(231, 76, 60, 0.2);
        }
        .success-message {
            background: rgba(39, 174, 96, 0.1);
            color: #27ae60;
            border: 1px solid rgba(39, 174, 96, 0.2);
        }
        /* 深色主題優化 */
        body.dark-theme .register-container {
            background: linear-gradient(135deg, #2d3436 0%, #636e72 100%);
        }
        body.dark-theme .register-panel {
            background: rgba(45, 44, 44, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        body.dark-theme .register-panel h1 {
            color: #f0f0f0;
            background: linear-gradient(135deg, #fd79a8, #fdcb6e);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        body.dark-theme .input-group label {
            background: rgba(45, 44, 44, 0.9);
            color: #ccc;
        }
        body.dark-theme .input-group input[type="text"],
        body.dark-theme .input-group input[type="password"] {
            background: rgba(60, 60, 60, 0.8);
            border-color: #555;
            color: #f0f0f0;
        }
        body.dark-theme .input-group input[type="text"]:focus,
        body.dark-theme .input-group input[type="password"]:focus {
            border-color: #fd79a8;
            background: rgba(60, 60, 60, 0.95);
            box-shadow: 0 0 0 3px rgba(253, 121, 168, 0.1);
        }
        body.dark-theme .register-btn {
            background: linear-gradient(135deg, #fd79a8 0%, #fdcb6e 100%);
        }
        body.dark-theme .login-link {
            color: #ccc;
        }
        body.dark-theme .login-link a {
            color: #fd79a8;
        }
        body.dark-theme .login-link a:hover {
            color: #fdcb6e;
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
    <div class="register-container">
        <div class="register-panel">
            <h1>📝 註冊新帳號</h1>
            <?php if (!empty($error)): ?>
                <div class="message error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="post" action="register.php" class="register-form">
                <div class="input-group">
                    <label for="realname">真實姓名</label>
                    <input type="text" id="realname" name="realname" required placeholder="請輸入您的真實姓名">
                </div>
                <div class="input-group">
                    <label for="username">帳號</label>
                    <input type="text" id="username" name="username" required placeholder="請輸入帳號">
                </div>
                <div class="input-group">
                    <label for="password">密碼</label>
                    <input type="password" id="password" name="password" required placeholder="請輸入密碼">
                </div>
                <button type="submit" class="register-btn">註冊</button>
            </form>
            <div class="login-link">
                已經有帳號了嗎？<a href="home1-7.php">立即登入</a>
            </div>
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