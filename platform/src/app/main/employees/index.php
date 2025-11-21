<?php
require_once __DIR__ . '/../../../middleware/checkAuth.php';
require_once APP_PATH . '/services/areas/areaServices.php';
require_once APP_PATH . '/services/branches/branchesServices.php';
require_once APP_PATH . '/services/roles/roleServices.php';
require_once APP_PATH . '/services/employees/credentialsServices.php';
require_once APP_PATH . '/services/employees/employeesServices.php';
require_once APP_PATH . '/services/statuses/statusesServices.php';

$areas = getAllAreas();
$branches = getAllBranches();
$roles = getAllRoles();
$creds = generateEmployeeCredentials();

$employees = getAllEmployees();
$statusList = getAllStatuses();

// Agrupar empleados por estatus
$grouped = [];
foreach ($employees as $emp) {
    $status = $emp['status_name'];
    if (!isset($grouped[$status])) {
        $grouped[$status] = [];
    }
    $grouped[$status][] = $emp;
}

$pageTitle = ": Empleados";
?>
<!DOCTYPE html>
<html lang="en">
<?php include APP_PATH . '/layouts/head.php' ?>

<body>
    <?php include APP_PATH . '/layouts/navbar.php' ?>
    <main>
        <div class="container">
            <h1 class="center-align">Empleados</h1>

            <div data-tabs>
                <div class="tabs">
                    <?php foreach ($statusList as $st): ?>
                        <a data-tab="status_<?= $st['id'] ?>">
                            <?= htmlspecialchars($st['name']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>

                <?php foreach ($statusList as $st): ?>
                    <div data-tab-section="status_<?= $st['id'] ?>" class="hidden">

                        <?php
                        $filtered = array_filter($employees, fn($e) => $e['status_id'] == $st['id']);
                        ?>

                        <?php if (empty($filtered)): ?>
                            <p>Aún no tienes empleados asignados a este estatus.</p>

                        <?php else: ?>
                            <table>
                                <thead>
                                    <tr>
                                        <th class="th radius-top-left">ID</th>
                                        <th class="th">Empleado</th>
                                        <th class="th">Teléfono</th>
                                        <th class="th">Sucursal</th>
                                        <th class="th">Rol</th>
                                        <th class="th">Fecha de Ingreso</th>
                                        <th class="th radius-top-right">Acciones</th>
                                    </tr>
                                </thead>
                                <?php foreach ($filtered as $emp): ?>
                                    <tr>
                                        <td class="td center-align"><?= $emp['id'] ?></td>
                                        <td class="td"><?= $emp['names'] . ' ' . $emp['surname1'] . ' ' . $emp['surname2'] ?></td>
                                        <td class="td center-align"><?= $emp['phone'] ?></td>
                                        <td class="td center-align"><?= $emp['branch_name'] ?></td>
                                        <td class="td"><?= $emp['role_name'] ?></td>
                                        <td class="td center-align"><?= $emp['hire_date'] ?></td>
                                        <td class="td center-align">
                                            <a data-dialog-open="settings-dialog_<?= $emp['id'] ?>" class="action-table" href="#!">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" color="#087895" fill="none">
                                                    <path d="M15.5 12C15.5 13.933 13.933 15.5 12 15.5C10.067 15.5 8.5 13.933 8.5 12C8.5 10.067 10.067 8.5 12 8.5C13.933 8.5 15.5 10.067 15.5 12Z" stroke="#087895" stroke-width="2"></path>
                                                    <path d="M21.011 14.0965C21.5329 13.9558 21.7939 13.8854 21.8969 13.7508C22 13.6163 22 13.3998 22 12.9669V11.0332C22 10.6003 22 10.3838 21.8969 10.2493C21.7938 10.1147 21.5329 10.0443 21.011 9.90358C19.0606 9.37759 17.8399 7.33851 18.3433 5.40087C18.4817 4.86799 18.5509 4.60156 18.4848 4.44529C18.4187 4.28902 18.2291 4.18134 17.8497 3.96596L16.125 2.98673C15.7528 2.77539 15.5667 2.66972 15.3997 2.69222C15.2326 2.71472 15.0442 2.90273 14.6672 3.27873C13.208 4.73448 10.7936 4.73442 9.33434 3.27864C8.95743 2.90263 8.76898 2.71463 8.60193 2.69212C8.43489 2.66962 8.24877 2.77529 7.87653 2.98663L6.15184 3.96587C5.77253 4.18123 5.58287 4.28891 5.51678 4.44515C5.45068 4.6014 5.51987 4.86787 5.65825 5.4008C6.16137 7.3385 4.93972 9.37763 2.98902 9.9036C2.46712 10.0443 2.20617 10.1147 2.10308 10.2492C2 10.3838 2 10.6003 2 11.0332V12.9669C2 13.3998 2 13.6163 2.10308 13.7508C2.20615 13.8854 2.46711 13.9558 2.98902 14.0965C4.9394 14.6225 6.16008 16.6616 5.65672 18.5992C5.51829 19.1321 5.44907 19.3985 5.51516 19.5548C5.58126 19.7111 5.77092 19.8188 6.15025 20.0341L7.87495 21.0134C8.24721 21.2247 8.43334 21.3304 8.6004 21.3079C8.76746 21.2854 8.95588 21.0973 9.33271 20.7213C10.7927 19.2644 13.2088 19.2643 14.6689 20.7212C15.0457 21.0973 15.2341 21.2853 15.4012 21.3078C15.5682 21.3303 15.7544 21.2246 16.1266 21.0133L17.8513 20.034C18.2307 19.8187 18.4204 19.711 18.4864 19.5547C18.5525 19.3984 18.4833 19.132 18.3448 18.5991C17.8412 16.6616 19.0609 14.6226 21.011 14.0965Z" stroke="#087895" stroke-width="2" stroke-linecap="round"></path>
                                                </svg>
                                            </a>
                                            <a class="action-table" href="#!">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="23" height="23" color="#087895" fill="none">
                                                    <path d="M22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 16.4292 4.87962 20.1859 8.86884 21.5M9 14L12 12V6.66702" stroke="#087895" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                                    <path d="M13 16H15C15.5523 16 16 16.4477 16 17V18C16 18.5523 15.5523 19 15 19H14C13.4477 19 13 19.4477 13 20V21C13 21.5523 13.4477 22 14 22H16" stroke="#087895" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                                    <path d="M22 16V19M22 19H20C19.4477 19 19 18.5523 19 18V16M22 19V22" stroke="#087895" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                                </svg>
                                            </a>
                                            <a class="action-table" href="#!">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="23" height="23" color="#b50800" fill="none">
                                                    <path d="M19.5 5.5L18.8803 15.5251C18.7219 18.0864 18.6428 19.3671 18.0008 20.2879C17.6833 20.7431 17.2747 21.1273 16.8007 21.416C15.8421 22 14.559 22 11.9927 22C9.42312 22 8.1383 22 7.17905 21.4149C6.7048 21.1257 6.296 20.7408 5.97868 20.2848C5.33688 19.3626 5.25945 18.0801 5.10461 15.5152L4.5 5.5" stroke="#b50800" stroke-width="2" stroke-linecap="round"></path>
                                                    <path d="M3 5.5H21M16.0557 5.5L15.3731 4.09173C14.9196 3.15626 14.6928 2.68852 14.3017 2.39681C14.215 2.3321 14.1231 2.27454 14.027 2.2247C13.5939 2 13.0741 2 12.0345 2C10.9688 2 10.436 2 9.99568 2.23412C9.8981 2.28601 9.80498 2.3459 9.71729 2.41317C9.32164 2.7167 9.10063 3.20155 8.65861 4.17126L8.05292 5.5" stroke="#b50800" stroke-width="2" stroke-linecap="round"></path>
                                                    <path d="M9.5 16.5L9.5 10.5" stroke="#b50800" stroke-width="2" stroke-linecap="round"></path>
                                                    <path d="M14.5 16.5L14.5 10.5" stroke="#b50800" stroke-width="2" stroke-linecap="round"></path>
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>

                                    <!-- Diálogo de configuración -->
                                    <dialog id="settings-dialog_<?= $emp['id'] ?>">
                                        <div class="dialog-action">
                                            <a data-dialog-close href="#!">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" color="currentColor" fill="none">
                                                    <path d="M18 6L6.00081 17.9992M17.9992 18L6 6.00085" stroke="#196273" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                            </a>
                                        </div>
                                        <div class="dialog-content center-align">
                                            <h2 class="title-dialogs">Modificar Empleado</h2>
                                            <form action="actions/update.php" method="POST">
                                                <input type="hidden" name="id" value="<?= $emp['id'] ?>">
                                                <div class="col-3">
                                                    <div class="input-field">
                                                        <label for="names">Nombre(s)</label>
                                                        <input id="names" type="text" name="names" value="<?= htmlspecialchars($emp['names']) ?>">
                                                    </div>
                                                    <div class="input-field">
                                                        <label for="surname1">Apellido Paterno</label>
                                                        <input id="surname1" type="text" name="surname1" value="<?=  htmlspecialchars($emp['surname1']) ?>">
                                                    </div>
                                                    <div class="input-field">
                                                        <label for="surname2">Apellido Materno</label>
                                                        <input id="surname2" type="text" name="surname2" value="<?= htmlspecialchars($emp['surname2']) ?>">
                                                    </div>
                                                </div>
                                                <div class="col-2">
                                                    <div class="input-field">
                                                        <label for="email">Correo Electrónico</label>
                                                        <input id="email" type="email" name="email" value="<?= htmlspecialchars($emp['email']) ?>">
                                                    </div>
                                                    <div class="input-field">
                                                        <label for="phone">Teléfono</label>
                                                        <input id="phone" type="text" name="phone" value="<?= htmlspecialchars($emp['phone']) ?>">
                                                    </div>
                                                </div>
                                                <div class="col-3">
                                                    <div class="input-field">
                                                        <label for="id_area">Área</label>
                                                        <select name="id_area" required>
                                                            <option value="<?= $emp['id_area'] ?>" selected>
                                                                <?= htmlspecialchars($emp['area_name']) ?>
                                                            </option>
                                                            <?php foreach ($areas as $area): ?>
                                                                <?php if ($area['id'] != $emp['id_area']): ?>
                                                                    <option value="<?= $area['id'] ?>">
                                                                        <?= htmlspecialchars($area['name']) ?>
                                                                    </option>
                                                                <?php endif; ?>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    <div class="input-field">
                                                        <label for="id_branch">Sucursal</label>
                                                        <select name="id_branch" required>
                                                            <option value="<?= $emp['id_branch'] ?>" selected>
                                                                <?= htmlspecialchars($emp['branch_name']) ?>
                                                            </option>
                                                            <?php foreach ($branches as $b): ?>
                                                                <?php if ($b['id'] != $emp['id_branch']): ?>
                                                                    <option value="<?= $b['id'] ?>">
                                                                        <?= htmlspecialchars($b['name']) ?>
                                                                    </option>
                                                                <?php endif; ?>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    <div class="input-field">
                                                        <label for="id_role">Rol</label>
                                                        <select name="id_role" required>
                                                            <option value="<?= $emp['id_role'] ?>" selected>
                                                                <?= htmlspecialchars($emp['role_name']) ?>
                                                            </option>
                                                            <?php foreach ($roles as $r): ?>
                                                                <?php if ($r['id'] != $emp['id_role']): ?>
                                                                    <option value="<?= $r['id'] ?>">
                                                                        <?= htmlspecialchars($r['name']) ?>
                                                                    </option>
                                                                <?php endif; ?>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-4 aux_row">
                                                    <div class="input-field">
                                                        <label for="hire_date">Fecha de Ingreso</label>
                                                        <input id="hire_date" type="date" name="hire_date" value="<?= $emp['hire_date'] ?>">
                                                    </div>
                                                    <div class="input-field">
                                                        <label for="status">Estatus</label>
                                                        <select name="status" required>
                                                            <option value="<?= $emp['status_id'] ?>" selected>
                                                                <?= htmlspecialchars($emp['status_name']) ?>
                                                            </option>
                                                            <?php foreach ($statusList as $s): ?>
                                                                <?php if ($s['id'] != $emp['status_id']): ?>
                                                                    <option value="<?= $s['id'] ?>">
                                                                        <?= htmlspecialchars($s['name']) ?>
                                                                    </option>
                                                                <?php endif; ?>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    <div class="input-field">
                                                        <label for="username">Usuario</label>
                                                        <input id="username" placeholder="Usuario" readonly type="text" value="<?= htmlspecialchars($emp['username']) ?>">
                                                    </div>
                                                    <div class="input-field">
                                                        <label for="reset">Contraseña</label>
                                                        <a class="btn" href="actions/reset_password.php?id=<?= $emp['id'] ?>">Regenerar</a>
                                                    </div>
                                                </div>
                                                <button type="submit">Guardar cambios</button>
                                            </form>
                                        </div>
                                    </dialog>
                                <?php endforeach; ?>
                            </table>
                        <?php endif; ?>

                    </div>
                <?php endforeach; ?>
            </div>


            <a data-dialog-open="categoryDialog" class="btn-floating">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="22" height="22" color="#ffffff" fill="none" style="margin-bottom: 4px;">
                    <path d="M14 8.5C14 5.73858 11.7614 3.5 9 3.5C6.23858 3.5 4 5.73858 4 8.5C4 11.2614 6.23858 13.5 9 13.5C11.7614 13.5 14 11.2614 14 8.5Z" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M16 20.5C16 16.634 12.866 13.5 9 13.5C5.13401 13.5 2 16.634 2 20.5" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M19 9V15M22 12L16 12" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
            </a>

            <!-- Diálogo -->
            <dialog id="categoryDialog" class="modal">
                <div class="dialog-action">
                    <a data-dialog-close href="#!">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" color="currentColor" fill="none">
                            <path d="M18 6L6.00081 17.9992M17.9992 18L6 6.00085" stroke="#196273" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </a>
                </div>
                <div class="dialog-content center-align">
                    <h2 class="title-dialogs">Nuevo Empleado</h2>
                    <form action="actions/create.php" method="POST">
                        <div class="col-3">
                            <div class="input-field">
                                <input placeholder="Nombre(s)" name="names" type="text">
                            </div>
                            <div class="input-field">
                                <input placeholder="Apellido Paterno" name="surname1" type="text">
                            </div>
                            <div class="input-field">
                                <input placeholder="Apellido Materno" name="surname2" type="text">
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="input-field">
                                <input placeholder="Correo Electrónico" name="email" type="email">
                            </div>
                            <div class="input-field">
                                <input placeholder="Teléfono" name="phone" type="text">
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="input-field">
                                <select name="id_area" required>
                                    <option value="">Seleccione una área</option>
                                    <?php foreach ($areas as $area): ?>
                                        <option value="<?= $area['id'] ?>">
                                            <?= htmlspecialchars($area['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="input-field">
                                <select name="id_branch" required>
                                    <option value="">Asignar sucursal</option>
                                    <?php foreach ($branches as $b): ?>
                                        <option value="<?= $b['id'] ?>">
                                            <?= htmlspecialchars($b['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="input-field">
                                <select name="id_role" required>
                                    <option value="">Seleccione rol</option>
                                    <?php foreach ($roles as $r): ?>
                                        <option value="<?= $r['id'] ?>">
                                            <?= htmlspecialchars($r['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-3 aux_row">
                            <div class="input-field">
                                <label for="hire_date">Fecha de Ingreso</label>
                                <input placeholder="Fecha de Ingreso" name="hire_date" type="date" id="hire_date">
                            </div>
                            <div class="input-field">
                                <label for="user">Usuario</label>
                                <input name="username" type="text" id="user" readonly value="<?= $creds['username'] ?>">
                            </div>
                            <div class="input-field">
                                <label for="password">Contraseña</label>
                                <input name="password" type="text" id="password" readonly value="<?= $creds['password'] ?>">
                            </div>
                        </div>
                        <button type="submit">Crear empleado</button>
                    </form>
                </div>
            </dialog>
        </div>
    </main>
    <?php include APP_PATH . '/layouts/scripts.php' ?>
</body>

</html>