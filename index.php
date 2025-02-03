<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نظام إدارة المستشفى</title>
    <link rel="stylesheet" href="styles_home.css">
</head>
<body>
    <header>
        <div class="logo">
            <h1>مستشفى الشفاء</h1>
            <p>نحن هنا من أجل رعايتك</p>
        </div>
        <nav>
            <ul>
                <li><a href="#about">عن المستشفى</a></li>
                <li><a href="#services">خدماتنا</a></li>
                <li><a href="register.php">انشاء حساب </a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="hero">
            <div class="hero-content">
                <h2>مرحبًا بكم في نظام إدارة مستشفى الشفاء</h2>
                <p>الرجاء تسجيل الدخول للوصول إلى النظام</p>
                <div class="login-buttons">
                    <button > <a href="login.php"> تسجيل دخول طبيب </a></button>
                    <button > <a href="login.php"> تسجيل دخول الاستقبال</a></button>
                    <button > <a href="login.php">تسجيل دخول المعمل</a></button>
                   
                </div>
            </div>
        </section>

        <!-- نموذج تسجيل الدخول -->
        <div id="loginModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeLogin()">&times;</span>
                <h2 id="modalTitle">تسجيل الدخول</h2>
                <form id="loginForm">
                    <div class="input-group">
                        <label for="email">البريد الإلكتروني:</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="input-group">
                        <label for="password">كلمة المرور:</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <button type="submit">تسجيل الدخول</button>
                </form>
            </div>
        </div>
    </main>

    <footer>
        <p>© 2023 مستشفى الشفاء. جميع الحقوق محفوظة.</p>
    </footer>

    <script src="script.js"></script>
</body>
</html>