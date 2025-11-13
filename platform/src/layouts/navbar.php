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
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" color="currentColor" fill="none">
              <path d="M2 12C2 8.22876 2 6.34315 3.17157 5.17157C4.34315 4 6.22876 4 10 4H14C17.7712 4 19.6569 4 20.8284 5.17157C22 6.34315 22 8.22876 22 12C22 15.7712 22 17.6569 20.8284 18.8284C19.6569 20 17.7712 20 14 20H10C6.22876 20 4.34315 20 3.17157 18.8284C2 17.6569 2 15.7712 2 12Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
              <path d="M11 10C11 8.89543 10.1046 8 9 8C7.89543 8 7 8.89543 7 10C7 11.1046 7.89543 12 9 12C10.1046 12 11 11.1046 11 10Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
              <path d="M13 16C13 13.7909 11.2091 12 9 12C6.79086 12 5 13.7909 5 16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
              <path d="M15 9H19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
              <path d="M15 12H19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>'
    ],
    'historial' => [
        'label' => 'Historial',
        'href'  => BASE_URL . '/main/historial/',
        'icon'  => '
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" color="currentColor" fill="none">
              <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C7.52232 2 3.77426 4.94289 2.5 9H5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
              <path d="M12 8V12L14 14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
              <path d="M2 12C2 12.3373 2.0152 12.6709 2.04494 13M9 22C8.6584 21.8876 8.32471 21.7564 8 21.6078M3.20939 17C3.01655 16.6284 2.84453 16.2433 2.69497 15.8462M4.83122 19.3065C5.1369 19.6358 5.46306 19.9441 5.80755 20.2292" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
            </svg>'
    ],
    'branches' => [
        'label' => 'Sucursales',
        'href'  => BASE_URL . '/main/branches/',
        'icon'  => '
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" color="currentColor" fill="none">
              <path d="M2.97659 10.5146V15.009C2.97659 17.8339 2.97659 19.2463 3.85624 20.1239C4.73588 21.0015 6.15165 21.0015 8.98318 21.0015H12.9876" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
              <path d="M6.98148 17.0066H10.9859" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
              <path d="M18.4941 13.5107C20.4292 13.5107 21.998 15.0464 21.998 16.9408C21.998 19.0836 19.8799 20.1371 18.8695 21.7433C18.6542 22.0857 18.3496 22.0857 18.1187 21.7433C17.0768 20.1981 14.9903 19.0389 14.9903 16.9408C14.9903 15.0464 16.559 13.5107 18.4941 13.5107Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" />
              <path d="M18.4942 17.0066H18.5032" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
              <path d="M17.7957 2.00254L6.14986 2.03002C4.41169 1.94542 3.96603 3.2116 3.96603 3.83056C3.96603 4.38414 3.89058 5.19117 2.82527 6.70798C1.75996 8.22478 1.84001 8.67537 2.44074 9.72544C2.93931 10.5969 4.20744 10.9374 4.86865 10.9946C6.96886 11.0398 7.99068 9.32381 7.99068 8.1178C9.03254 11.1481 11.9956 11.1481 13.3158 10.8016C14.6386 10.4545 15.7717 9.2118 16.0391 8.1178C16.195 9.47735 16.6682 10.2707 18.0663 10.8158C19.5145 11.3805 20.7599 10.5174 21.3848 9.9642C22.0097 9.41096 22.4107 8.18278 21.2968 6.83288C20.5286 5.90195 20.2084 5.02494 20.1033 4.11599C20.0423 3.58931 19.9888 3.02336 19.5972 2.66323C19.0248 2.13691 18.2036 1.97722 17.7957 2.00254Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
            </svg>'
    ],
    'employees' => [
        'label' => 'Empleados',
        'href'  => BASE_URL . '/main/employees/',
        'icon'  => '
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" color="currentColor" fill="none">
              <path d="M11.9458 6L9.58384 17.0855C9.39588 17.9677 9.49933 18.298 10.1472 18.9315L12.7673 21.4934C13.1127 21.8311 13.2854 22 13.5 22C13.7146 22 13.8873 21.8311 14.2327 21.4934L16.8528 18.9315C17.5007 18.298 17.6041 17.9677 17.4162 17.0855L15.0542 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
              <path d="M10.5568 3.12403C10.4894 2.60014 10.4557 2.3382 10.6093 2.1691C10.763 2 11.0347 2 11.5781 2H15.4219C15.9653 2 16.237 2 16.3907 2.1691C16.5443 2.3382 16.5106 2.60014 16.4432 3.12403L16.3924 3.51931C16.2498 4.62718 16.1786 5.18111 15.8224 5.54049C15.7645 5.59888 15.7018 5.65262 15.635 5.70117C15.2238 6 14.6492 6 13.5 6C12.3508 6 11.7762 6 11.365 5.70117C11.2982 5.65262 11.2355 5.59888 11.1776 5.54049C10.8214 5.18111 10.7502 4.62718 10.6076 3.5193L10.5568 3.12403Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
              <path d="M9.99988 15C9.39212 15.6925 8.87749 16 8.49988 16C8.0155 16 7.14348 14.7794 6.75647 13.8954C6.57487 13.4806 6.48408 13.2732 6.50214 13.0108C6.52019 12.7484 6.63912 12.5565 6.87698 12.1727C8.22201 10.0024 10.5144 8.02113 11.9999 6" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" />
            </svg>'
    ],
    'logout' => [
        'label' => 'Cerrar Sesión',
        'href'  => BASE_URL . '/auth/logout.php',
        'icon'  => '
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" color="currentColor" fill="none">
              <path d="M19.9999 6.99974C19.923 5.58247 19.7124 4.66414 19.1363 3.96217C18.9701 3.75963 18.7844 3.57392 18.5819 3.4077C17.4755 2.49974 15.8318 2.49974 12.5443 2.49974L11.9999 2.4999C8.22871 2.4999 6.34309 2.4999 5.17152 3.67147C3.99994 4.84305 3.99994 6.72866 3.99994 10.4999V13.4999C3.99994 17.2711 3.99994 19.1568 5.17152 20.3283C6.34309 21.4999 8.22871 21.4999 11.9999 21.4999L12.5443 21.4997C15.8318 21.4997 17.4755 21.4997 18.5819 20.5918C18.7844 20.4256 18.9701 20.2399 19.1363 20.0373C19.7124 19.3353 19.923 18.417 19.9999 16.9997" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
              <path d="M16 7.99991C16 7.99991 20 10.9459 20 11.9999C20 13.054 16 15.9999 16 15.9999M19.5 11.9999H9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
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
      <div id="navMenu" class="dropdown-menu">
        <?php foreach ($menuItems as $item): ?>
          <a class="dropdown-link" href="<?= $item['href'] ?>">
            <?= $item['icon'] ?>
            <?= htmlspecialchars($item['label']) ?>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</header>