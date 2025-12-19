<!-- NAVBAR -->
<nav class="w-full fixed top-0 left-0 bg-white/40 shadow-sm py-4 z-50">
  <div class="flex items-center px-4 sm:px-6 justify-between">

    <!-- เฉพาะหน้า home_ped -->
    <?php if (basename($_SERVER['PHP_SELF']) == 'homeped.php'): ?>
      <button id="openFilter" class="text-2xl mr-4 hover:text-green-700">☰</button>
    <?php endif; ?>

    <!-- LOGO -->
    <h1>
      <a href="index.php" class="flex items-center gap-3 text-xl sm:text-2xl font-extrabold text-[#2f5d31]">
        <div class="bg-white rounded-full shadow-md p-1 px-2">🐾</div>
        PawHome
      </a>
    </h1>


    <!-- ☰ MOBILE MENU BUTTON (แสดงเฉพาะจอเล็ก) -->
    <button id="mobileMenuBtn"
      class="text-2xl mr-3 hover:text-green-700 md:hidden"
      aria-label="Open menu">
      ☰
    </button>

    <!-- DESKTOP MENU (แสดง md ขึ้นไป) -->
    <ul class="hidden md:flex items-center space-x-6 font-semibold text-gray-900 ml-auto">
      <li><a href="homeped.php" class="hover:text-green-700">HOME</a></li>
      <li><a href="form.php" class="hover:text-green-700">FORM</a></li>
      <li><a href="donate.php" class="hover:text-green-700">DONATE</a></li>
      <li><a href="request_status.php" class="hover:text-green-700">REQUEST STATUS</a></li>
      <?php if (($_SESSION['role'] ?? 'user') === 'admin'): ?>
        <li><a href="admin/adminindex.php" class="hover:text-green-700">ADMIN</a></li>
      <?php endif; ?>


      <?php if (isset($_SESSION['user_id'])): ?>
        <!-- DESKTOP PROFILE -->
        <li class="relative" id="profileMenu">
          <button id="profileBtn" type="button"
            class="relative z-50 w-10 h-10 rounded-full bg-green-300 shadow-md overflow-hidden flex items-center justify-center cursor-pointer">
            <?php if (!empty($avatar_src)): ?>
              <img src="<?= $avatar_src ?>" class="w-full h-full object-cover" alt="avatar">
            <?php else: ?>
              <span class="text-white font-bold"><?= strtoupper($_SESSION['email'][0] ?? 'U'); ?></span>
            <?php endif; ?>
          </button>

          <div id="profileDropdown"
            class="hidden absolute right-0 mt-3 w-60 bg-white shadow-2xl rounded-xl p-4 text-gray-700 z-60 border border-gray-200">

            <p class="text-sm text-gray-500">เข้าสู่ระบบเป็น</p>
            <p class="font-bold"><?= explode("@", $_SESSION['email'])[0]; ?></p>
            <p class="text-sm"><?= $_SESSION['email']; ?></p>

            <hr class="my-3">

            <a href="setting.php" class="block py-1 hover:text-red-600">⚙️ ตั้งค่า</a>
            <a href="profile.php" class="block py-1 hover:text-red-600">👤 โปรไฟล์</a>
            <a href="about_us.php" class="block py-1 hover:text-red-600">ℹ️ About Us</a>

            <hr class="my-3">
            <a href="logout.php" class="text-red-600 font-bold">ออกจากระบบ</a>
          </div>
        </li>
      <?php else: ?>
        <li>
          <a href="login.php"
            class="px-5 py-2 rounded-full bg-green-600 text-white font-semibold shadow-md hover:bg-green-700 transition">
            Login
          </a>
        </li>
      <?php endif; ?>
    </ul>
  </div>

  <!-- MOBILE MENU PANEL -->
  <div id="mobileMenuOverlay" class="hidden fixed inset-0 z-40">
    <!-- backdrop -->
    <div class="absolute inset-0 bg-black/30"></div>

    <!-- panel -->
    <div id="mobileMenuPanel"
      class="absolute right-0 top-0 h-full w-72 max-w-[85%] bg-white shadow-2xl p-4">
      <div class="flex items-center justify-between">
        <span class="font-extrabold text-[#2f5d31] text-lg">Menu</span>
        <button id="mobileMenuClose" class="text-2xl hover:text-red-600" aria-label="Close menu">✕</button>
      </div>

      <!-- ✅ PROFILE TOP -->
      <div class="mt-4">
        <?php if (isset($_SESSION['user_id'])): ?>
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-full bg-green-300 overflow-hidden flex items-center justify-center">
              <?php if (!empty($avatar_src)): ?>
                <img src="<?= $avatar_src ?>" class="w-full h-full object-cover" alt="avatar">
              <?php else: ?>
                <span class="text-white font-bold text-lg"><?= strtoupper($_SESSION['email'][0] ?? 'U'); ?></span>
              <?php endif; ?>
            </div>
            <div class="min-w-0">
              <div class="font-bold truncate"><?= explode("@", $_SESSION['email'])[0]; ?></div>
              <div class="text-sm text-gray-600 truncate"><?= $_SESSION['email']; ?></div>
            </div>
          </div>
        <?php else: ?>
          <a href="login.php"
            class="inline-flex w-full items-center justify-center px-5 py-2 rounded-full bg-green-600 text-white font-semibold shadow-md hover:bg-green-700 transition">
            Login
          </a>
        <?php endif; ?>
      </div>

      <hr class="my-4">

      <!-- ✅ NAV LINKS -->
      <div class="flex flex-col gap-2 font-semibold text-gray-900">
        <a href="homeped.php" class="px-3 py-2 rounded-lg hover:bg-green-50 hover:text-green-700">HOME</a>
        <a href="form.php" class="px-3 py-2 rounded-lg hover:bg-green-50 hover:text-green-700">FORM</a>
        <a href="donate.php" class="px-3 py-2 rounded-lg hover:bg-green-50 hover:text-green-700">DONATE</a>
        <a href="request_status.php" class="px-3 py-2 rounded-lg hover:bg-green-50 hover:text-green-700">REQUEST STATUS</a>
        <?php if (($_SESSION['role'] ?? 'user') === 'admin'): ?>
          <a href="admin/adminindex.php" class="px-3 py-2 rounded-lg hover:bg-green-50 hover:text-green-700">
            ADMIN
          </a>
        <?php endif; ?>

      </div>

      <?php if (isset($_SESSION['user_id'])): ?>
        <hr class="my-4">

        <!-- ✅ SETTINGS LINKS -->
        <div class="flex flex-col gap-2 text-gray-800">
          <a href="setting.php" class="px-3 py-2 rounded-lg hover:bg-red-50 hover:text-red-600">⚙️ ตั้งค่า</a>
          <a href="profile.php" class="px-3 py-2 rounded-lg hover:bg-red-50 hover:text-red-600">👤 โปรไฟล์</a>
          <a href="about_us.php" class="px-3 py-2 rounded-lg hover:bg-red-50 hover:text-red-600">ℹ️ About Us</a>
          <a href="logout.php" class="px-3 py-2 rounded-lg text-red-600 font-bold hover:bg-red-50">ออกจากระบบ</a>
        </div>
      <?php endif; ?>
    </div>
  </div>
</nav>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    // DESKTOP PROFILE DROPDOWN
    const profileBtn = document.getElementById('profileBtn');
    const profileDropdown = document.getElementById('profileDropdown');
    const profileMenu = document.getElementById('profileMenu');

    if (profileBtn && profileDropdown && profileMenu) {
      profileBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        profileDropdown.classList.toggle('hidden');
      });

      document.addEventListener('click', (e) => {
        if (!profileMenu.contains(e.target)) profileDropdown.classList.add('hidden');
      });
    }

    // MOBILE MENU
    const openBtn = document.getElementById('mobileMenuBtn');
    const overlay = document.getElementById('mobileMenuOverlay');
    const closeBtn = document.getElementById('mobileMenuClose');
    const panel = document.getElementById('mobileMenuPanel');

    function openMenu() {
      overlay.classList.remove('hidden');
      document.body.style.overflow = 'hidden';
    }

    function closeMenu() {
      overlay.classList.add('hidden');
      document.body.style.overflow = '';
    }

    if (openBtn && overlay && closeBtn && panel) {
      openBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        openMenu();
      });
      closeBtn.addEventListener('click', closeMenu);

      // click outside panel closes
      overlay.addEventListener('click', (e) => {
        if (!panel.contains(e.target)) closeMenu();
      });

      // ESC closes
      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !overlay.classList.contains('hidden')) closeMenu();
      });
    }
  });
</script>