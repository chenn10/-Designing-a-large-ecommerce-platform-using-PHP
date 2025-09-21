<?php
session_start();
require_once("config/database_sqlite.php");

$account = $_POST["username"];
$password = $_POST["password"];
$found = false;
$realname = "";
$user_id = null;

// 使用資料庫驗證登入
$user = getSingleResult("SELECT * FROM users WHERE username = ? AND password = ?", [$account, $password]);

if ($user) {
    $found = true;
    $realname = $user['username']; // 暫時使用 username 作為顯示名稱
    $user_id = $user['id'];
}

if ($found) {
    $_SESSION["login"] = true;
    $_SESSION["username"] = $account;
    $_SESSION["realname"] = $realname;
    $_SESSION["user_id"] = $user_id;

    $redirectPage = "home.php";

    echo "
    <!doctype html>
    <html>
    <head>
        <meta charset='utf-8'>
        <meta http-equiv='refresh' content='5;url=$redirectPage'>
        <title>登入成功</title>
        <link rel='stylesheet' href='home_style.css'>
        <style>
            .message {
                margin-top: 100px;
                background-color: #d4edda;
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
            .account-link {
                color: white !important;
            }
            .account-link:hover {
                color: white !important;
                text-decoration: underline;
            }
        </style>
    </head>
    <body>

        <!-- 帳號導覽列 -->
        <div class='top-bar'>
            <div class='top-bar-left'>
                <span>歡迎來到 MYGO Home！</span>
            </div>
            <div class='top-bar-right'>";
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
    echo "          <button id='theme-toggle' class='theme-toggle'>深色</button>
            </div>
        </div>

        <!-- 商品分類列 -->
        <nav class='category-bar'>
            <a href='home.php'>首頁</a>
            <a href='#'>3C</a>
            <a href='#'>通訊</a>
            <a href='#'>家電</a>
            <a href='#'>日用</a>
            <a href='#'>母嬰</a>
            <a href='#'>食品</a>
            <a href='#'>生活</a>
            <a href='#'>居家</a>
            <a href='#'>保健</a>
            <a href='#'>美妝</a>
            <a href='#'>時尚</a>
            <a href='#'>書店</a>
        </nav>

        <!-- 提示訊息 -->
        <div class='center-container'>
            <div class='message'>
                ✅ 登入成功！歡迎 <strong>$realname</strong>！<br><br>
                5秒後自動跳轉至 <strong>首頁</strong>...<br><br>
                若未自動跳轉，請 <a href='$redirectPage'>點此前往</a>。
            </div>
        </div>

        <!-- 主題切換腳本 -->
        <script>
            window.onload = function () {
                const theme = localStorage.getItem('theme');
                const btn = document.getElementById('theme-toggle');

                if (theme === 'dark') {
                    document.body.classList.add('dark-theme');
                    btn.textContent = '亮色';
                } else {
                    btn.textContent = '深色';
                }

                btn.onclick = function () {
                    const isDark = document.body.classList.contains('dark-theme');
                    if (isDark && btn.textContent === '深色') return;
                    if (!isDark && btn.textContent === '亮色') return;

                    document.body.classList.toggle('dark-theme');
                    const nowDark = document.body.classList.contains('dark-theme');
                    localStorage.setItem('theme', nowDark ? 'dark' : 'light');
                    btn.textContent = nowDark ? '亮色' : '深色';
                };
            };
        </script>

    </body>
    </html>";
    exit();
} else {
    // 登入失敗畫面
    echo "
    <!doctype html>
    <html>
    <head>
        <meta charset='utf-8'>
        <title>登入失敗</title>
        <link rel='stylesheet' href='home_style.css'>
        <style>
            .error-box {
                margin-top: 100px;
                background-color: #f8d7da;
                color: #721c24;
                padding: 30px;
                border-radius: 15px;
                font-size: 20px;
                font-weight: bold;
                display: inline-block;
                box-shadow: 0 0 10px rgba(0,0,0,0.2);
                transition: background-color 0.3s, color 0.3s;
            }
            body.dark-theme .error-box {
                background-color: #2a1e1e;
                color: #ffb3b3;
                border: 1px solid #555;
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
        </style>
    </head>
    <body onload='applyTheme()'>
        <div class='center-container'>
            <div class='error-box'>
                ❌ 帳號或密碼錯誤！<br><br>
                請 <a href='home1-7.php'>點此重新登入</a>
            </div>
        </div>
        <script>
            function applyTheme() {
                const theme = localStorage.getItem('theme');
                if (theme === 'dark') {
                    document.body.classList.add('dark-theme');
                }
            }
        </script>
    </body>
    </html>";
}
?>
