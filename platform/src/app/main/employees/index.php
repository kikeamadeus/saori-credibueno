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

$employees = getAllEmployees($_SESSION['employee_id']);
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
                                        <th class="th radius-top-right">Ajustes</th>
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
                                            <a class="action-table" href="#!">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" color="#087895" fill="none" stroke="#087895" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M5.04798 8.60657L2.53784 8.45376C4.33712 3.70477 9.503 0.999914 14.5396 2.34474C19.904 3.77711 23.0904 9.26107 21.6565 14.5935C20.2227 19.926 14.7116 23.0876 9.3472 21.6553C5.36419 20.5917 2.58192 17.2946 2 13.4844" />
                                                    <path d="M12 8V12L14 14" />
                                                </svg>
                                            </a>
                                            <a data-dialog-open="settings-dialog_<?= $emp['id'] ?>" class="action-table" href="#!">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" color="#087895" fill="none" stroke="#087895" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M11.9967 11.5C12.549 11.5 12.9967 11.9477 12.9967 12.5C12.9967 13.0523 12.549 13.5 11.9967 13.5C11.4444 13.5 10.9967 13.0523 10.9967 12.5C10.9967 11.9477 11.4444 11.5 11.9967 11.5Z" />
                                                    <path d="M11.9967 5.5C12.549 5.5 12.9967 5.94772 12.9967 6.5C12.9967 7.05228 12.549 7.5 11.9967 7.5C11.4444 7.5 10.9967 7.05228 10.9967 6.5C10.9967 5.94772 11.4444 5.5 11.9967 5.5Z" />
                                                    <path d="M11.9967 17.5C12.549 17.5 12.9967 17.9477 12.9967 18.5C12.9967 19.0523 12.549 19.5 11.9967 19.5C11.4444 19.5 10.9967 19.0523 10.9967 18.5C10.9967 17.9477 11.4444 17.5 11.9967 17.5Z" />
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
                                                        <input id="surname1" type="text" name="surname1" value="<?= htmlspecialchars($emp['surname1']) ?>">
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


            <a data-dialog-open="createEmployeeDialog" class="btn-floating">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="22" height="22" color="#ffffff" fill="none" style="margin-bottom: 4px;">
                    <path d="M14 8.5C14 5.73858 11.7614 3.5 9 3.5C6.23858 3.5 4 5.73858 4 8.5C4 11.2614 6.23858 13.5 9 13.5C11.7614 13.5 14 11.2614 14 8.5Z" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M16 20.5C16 16.634 12.866 13.5 9 13.5C5.13401 13.5 2 16.634 2 20.5" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M19 9V15M22 12L16 12" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
            </a>

            <!-- Diálogo -->
            <dialog id="createEmployeeDialog" class="modal">
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