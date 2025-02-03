// فتح نموذج تسجيل الدخول
function openLogin(role) {
    const modal = document.getElementById('loginModal');
    const modalTitle = document.getElementById('modalTitle');
    modal.style.display = 'flex';

    switch (role) {
        case 'doctor':
            modalTitle.textContent = 'تسجيل دخول الطبيب';
            break;
        case 'lab':
            modalTitle.textContent = 'تسجيل دخول المعمل';
            break;
        case 'reception':
            modalTitle.textContent = 'تسجيل دخول الاستقبال';
            break;
    }
}

// إغلاق نموذج تسجيل الدخول
function closeLogin() {
    const modal = document.getElementById('loginModal');
    modal.style.display = 'none';
}

// إرسال نموذج تسجيل الدخول
document.getElementById('loginForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    // هنا يمكنك إضافة التحقق من البيانات وإعادة التوجيه
    alert(`تم تسجيل الدخول كـ ${email}`);
    closeLogin();
});