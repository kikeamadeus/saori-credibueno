<?php
/**
 * Navbar principal – SAORI Credibueno
 * -----------------------------------
 * Muestra el nombre del empleado autenticado y un menú dinámico
 * basado en las carpetas del módulo /main.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/config.php';

// ======================================================
// 1) Obtener nombre del usuario desde sesión
// ======================================================
$employeeName = $_SESSION['employee_name'] ?? 'Usuario';

// ======================================================
// 2) Detectar carpetas dentro de /main (para futuro uso dinámico)
// ======================================================
$mainPath = __DIR__ . '/../main/';
$menuFolders = [];

if (is_dir($mainPath)) {
    $dirs = scandir($mainPath);
    foreach ($dirs as $dir) {
        if ($dir === '.' || $dir === '..') continue;
        if (is_dir($mainPath . $dir)) {
            $menuFolders[] = $dir;
        }
    }
}

// ======================================================
// 3) Definir los ítems de menú (etapa visual)
// ======================================================
$menuItems = [
    'perfil' => [
        'label' => 'Perfil',
        'href'  => BASE_URL . '/main/perfil/',
        'icon'  => '
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="7" r="4"></circle>
                <path d="M5.5 21a6.5 6.5 0 0 1 13 0"></path>
            </svg>'
    ],
    'empleados' => [
        'label' => 'Empleados',
        'href'  => BASE_URL . '/main/empleados/',
        'icon'  => '
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M16 21v-2a4 4 0 0 0-8 0v2Z"></path>
                <circle cx="12" cy="7" r="4"></circle>
            </svg>'
    ],
    'sucursales' => [
        'label' => 'Sucursales',
        'href'  => BASE_URL . '/main/sucursales/',
        'icon'  => '
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 9L12 2l9 7v11H3z"></path>
                <path d="M9 22V12h6v10"></path>
            </svg>'
    ],
    'historial' => [
        'label' => 'Historial de Movimientos',
        'href'  => BASE_URL . '/main/historial/',
        'icon'  => '
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
            </svg>'
    ],
    'logout' => [
        'label' => 'Cerrar Sesión',
        'href'  => BASE_URL . '/auth/logout.php',
        'icon'  => '
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M16 17l5-5-5-5"></path>
                <path d="M21 12H9"></path>
                <path d="M13 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h8"></path>
            </svg>'
    ]
];
?>

<header>
  <div class="container">
    <div class="nav-wrapper">

      <!-- Logo principal -->
      <a href=".">
        <img src="<?= BASE_URL ?>/public/image/logo.png" alt="Credibueno: Nómina & Asistencia">
      </a>

      <!-- Usuario + menú desplegable -->
      <nav>
        <a href="#" id="menuToggle">
          <!-- Icono usuario -->
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" color="#000000" fill="none">
            <path d="M15 10C15 8.34315 13.6569 7 12 7C10.3431 7 9 8.34315 9 10C9 11.6569 10.3431 13 12 13C13.6569 13 15 11.6569 15 10Z" stroke="#141B34" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M17 18C17 15.2386 14.7614 13 12 13C9.23858 13 7 15.2386 7 18" stroke="#141B34" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M21 13V11C21 7.22876 21 5.34315 19.8284 4.17157C18.6569 3 16.7712 3 13 3H11C7.22876 3 5.34315 3 4.17157 4.17157C3 5.34315 3 7.22876 3 11V13C3 16.7712 3 18.6569 4.17157 19.8284C5.34315 21 7.22876 21 11 21H13C16.7712 21 18.6569 21 19.8284 19.8284C21 18.6569 21 16.7712 21 13Z" stroke="#141B34" stroke-width="1.5" stroke-linecap="square" stroke-linejoin="round" />
          </svg>

          <?= htmlspecialchars($employeeName) ?>

          <!-- Flecha dropdown -->
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" color="#000000" fill="none">
            <path d="M18 9.00005C18 9.00005 13.5811 15 12 15C10.4188 15 6 9 6 9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
          </svg>
        </a>
      </nav>

      <!-- Menú desplegable -->
      <!--div id="navMenu" class="dropdown-menu">
        <?php foreach ($menuItems as $item): ?>
          <a class="dropdown-link" href="<?= $item['href'] ?>">
            <?= $item['icon'] ?>
            <?= htmlspecialchars($item['label']) ?>
          </a>
        <?php endforeach; ?>
      </div-->
    </div>
  </div>
</header>