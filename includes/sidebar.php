<!-- ===== Sidebar ===== -->
<aside id="sidebar" class="sidebar" aria-label="Sidebar navigation">
  <div class="sidebar-top">
    <div class="sidebar-logo">
      <img src="../assets/img/logo/logo1.png" alt="Logo BPS" class="logo">
      <h4 class="brand">BPS Dashboard</h4>
    </div>

    <!-- Collapse (desktop) -->
    <button id="collapse-toggle" class="collapse-toggle" aria-label="Collapse sidebar" title="Collapse">
      <i class="fas fa-angle-double-left"></i>
    </button>
  </div>

  <nav class="sidebar-nav" role="navigation">
    <ul>
      <li>
        <a href="dashboard.php" class="nav-item">
          <i class="fas fa-tachometer-alt"></i>
          <span class="nav-text">Dashboard</span>
        </a>
      </li>

      <li>
        <a href="pegawai.php" class="nav-item">
          <i class="fas fa-users"></i>
          <span class="nav-text">Data Pegawai</span>
        </a>
      </li>

      <li>
        <a href="apel.php" class="nav-item">
          <i class="fas fa-calendar-check"></i>
          <span class="nav-text">Data Apel</span>
        </a>
      </li>

      <!-- KB-S Mart (submenu) -->
      <li class="has-sub">
        <details class="kbs-mart-menu">
          <summary class="nav-item">
            <i class="fas fa-store"></i>
            <span class="nav-text">KB-S Mart</span>
            <i class="fas fa-chevron-right caret" aria-hidden="true"></i>
          </summary>
          <ul class="sub-menu">
            <li><a href="tambah_penjualan.php">Tambah Penjualan</a></li>
            <li><a href="barang_tersedia.php">Stok Barang</a></li>

            <li><a href="history_penjualan.php">History Penjualan</a></li>

          </ul>
        </details>
      </li>
    </ul>
  </nav>
</aside>

<!-- Mobile hamburger + backdrop -->
<button id="menu-toggle" class="menu-toggle" aria-controls="sidebar" aria-expanded="false" aria-label="Toggle sidebar">
  <i class="fas fa-bars"></i>
</button>
<div id="sidebar-backdrop" class="sidebar-backdrop" hidden></div>

<style>
:root{
  --sbw:250px; --sbw-collapsed:72px;
  --sb-bg:#2c3e50; --sb-hover:#34495e; --brand:#ecf0f1;
}
.sidebar {
  position: fixed; inset: 0 auto 0 0;
  width: var(--sbw);
  background: var(--sb-bg);
  color: var(--brand);
  z-index: 1000;
  box-shadow: 2px 0 5px rgba(0,0,0,.08);
  overflow-y: auto;
  transition: left .3s ease,width .25s ease;
}
.sidebar-top {display:flex; align-items:center; justify-content:space-between; gap:8px; padding:16px 14px 8px;}
.sidebar-logo {display:flex; align-items:center; gap:12px;}
.sidebar .logo {width:44px; height:44px; border-radius:50%; object-fit:cover}
.brand {color:var(--brand); margin:0; font-size:1.05rem; letter-spacing:.2px}

.sidebar-nav ul {list-style:none; margin:8px 0 18px; padding:0;}
.nav-item {display:flex; align-items:center; gap:14px; padding:12px 16px; color:#fff; text-decoration:none; border-radius:10px; margin:4px 8px; transition:background .2s}
.nav-item:hover, .nav-item.active {background:var(--sb-hover)}
.nav-item i {width:20px; text-align:center}
.nav-text {white-space:nowrap; overflow:hidden; text-overflow:ellipsis}

.kbs-mart-menu {margin:4px 8px}
.kbs-mart-menu summary {list-style:none; cursor:pointer; user-select:none; display:flex; align-items:center; gap:14px; padding:12px 16px; border-radius:10px}
.kbs-mart-menu summary::-webkit-details-marker {display:none}
.kbs-mart-menu[open] summary {background:var(--sb-hover)}
.kbs-mart-menu .caret {margin-left:auto; transition:transform .2s}
.kbs-mart-menu[open] .caret {transform:rotate(90deg)}
.sub-menu {list-style:none; margin:4px 0 10px 0; padding:0 0 6px 44px}
.sub-menu a {display:block; padding:9px 10px; border-radius:8px; color:#eaeaea; text-decoration:none}
.sub-menu a:hover {background:rgba(255,255,255,.08)}

.collapse-toggle {border:0; background:transparent; color:#fff; font-size:1.1rem; padding:6px 8px; border-radius:8px; cursor:pointer}
.collapse-toggle:hover {background:rgba(255,255,255,.08)}
.sidebar.collapsed {width:var(--sbw-collapsed)}
.sidebar.collapsed .brand, .sidebar.collapsed .nav-text {display:none}
.sidebar.collapsed .sub-menu {padding-left:0}
.sidebar.collapsed .kbs-mart-menu .caret {display:none}

.main-content {margin-left:var(--sbw); transition:margin-left .25s ease}
.sidebar.collapsed ~ .main-content {margin-left:var(--sbw-collapsed)}

.menu-toggle {
  position: fixed; top:14px; left:14px; z-index:1101;
  display: none; border:0; border-radius:10px;
  padding:10px 12px; background:#3f51b5; color:#fff; cursor:pointer;
  transition: opacity 0.2s ease;
}
.sidebar-backdrop {
  position: fixed; inset:0;
  background: rgba(0,0,0,.35);
  z-index: 900;
}

@media (max-width: 992px){
  .menu-toggle {display:inline-flex; align-items:center; justify-content:center}
  .sidebar {left:-270px}
  .sidebar.active {left:0}
  .sidebar.active ~ .menu-toggle {opacity:0; pointer-events:none;} /* HIDE BUTTON WHEN SIDEBAR OPEN */
  .main-content {margin-left:0 !important; padding-left:16px; padding-right:16px}
}
body.sidebar-open {overflow:hidden}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const sidebar   = document.getElementById('sidebar');
  const menuBtn   = document.getElementById('menu-toggle');
  const collapse  = document.getElementById('collapse-toggle');
  const backdrop  = document.getElementById('sidebar-backdrop');
  const main      = document.querySelector('.main-content');

  collapse.addEventListener('click', () => {
    sidebar.classList.toggle('collapsed');
    if (main) {
      main.style.marginLeft = sidebar.classList.contains('collapsed') ? '72px' : '250px';
    }
  });

  function openSidebarMobile(open){
    if (open){
      sidebar.classList.add('active');
      document.body.classList.add('sidebar-open');
      backdrop.hidden = false;
      menuBtn.setAttribute('aria-expanded','true');
    } else {
      sidebar.classList.remove('active');
      document.body.classList.remove('sidebar-open');
      backdrop.hidden = true;
      menuBtn.setAttribute('aria-expanded','false');
    }
  }
  menuBtn.addEventListener('click', () => {
    openSidebarMobile(!sidebar.classList.contains('active'));
  });
  backdrop.addEventListener('click', () => openSidebarMobile(false));
  window.addEventListener('keydown', (e) => { if (e.key === 'Escape') openSidebarMobile(false); });

  const MQ = 992;
  window.addEventListener('resize', () => {
    if (window.innerWidth > MQ) openSidebarMobile(false);
  });

  const path = location.pathname.split('/').pop();
  document.querySelectorAll('.sidebar-nav a.nav-item').forEach(a => {
    const href = a.getAttribute('href');
    if (href && href === path) a.classList.add('active');
  });

  const kbPages = ['tambah_penjualan.php','barang_tersedia.php','barang_promo.php','history_penjualan.php','tambah_barang.php'];
  if (kbPages.includes(path)){
    const det = document.querySelector('.kbs-mart-menu');
    if (det) det.setAttribute('open','');
  }
});
</script>
