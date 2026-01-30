<?php
  // Ambil data session
  $isLoggedIn = session()->get('isLoggedIn');
  $role = session()->get('role'); // 'admin' atau 'umkm'
?>

<li class="menu-header">Menu Utama</li>

<li class="<?= uri_string() == 'peta' ? 'active' : '' ?>">
  <a class="nav-link" href="<?= site_url('peta') ?>">
    <i class="fas fa-map-marked-alt"></i> <span>Peta Sebaran</span>
  </a>
</li>

<?php if($isLoggedIn): ?>
  
  <li class="menu-header">Manajemen</li>

  <li class="<?= uri_string() == '/' || uri_string() == '' ? 'active' : '' ?>">
    <a class="nav-link" href="<?= site_url() ?>">
      <i class="fas fa-fire"></i> <span>Dashboard</span>
    </a>
  </li>

  <li class="<?= uri_string() == 'outlet' ? 'active' : '' ?>">
    <a class="nav-link" href="<?= site_url('outlet') ?>">
      <i class="fas fa-store"></i> <span>Data Outlet</span>
    </a>
  </li>

  <li class="<?= uri_string() == 'produk' ? 'active' : '' ?>">
    <a class="nav-link" href="<?= site_url('produk') ?>">
      <i class="fas fa-box"></i> <span>Data Produk</span>
    </a>
  </li>

  <?php if($role == 'admin'): ?>
    
    <li class="menu-header">Administrator</li>

    <li class="<?= uri_string() == 'admin' ? 'active' : '' ?>">
      <a class="nav-link" href="<?= site_url('admin') ?>">
        <i class="fas fa-user-shield"></i> <span>Data Admin</span>
      </a>
    </li>

    <li class="<?= uri_string() == 'umkm' ? 'active' : '' ?>">
      <a class="nav-link" href="<?= site_url('umkm') ?>">
        <i class="fas fa-users"></i> <span>Data UMKM</span>
      </a>
    </li>

  <?php endif; ?>

<?php endif; ?>