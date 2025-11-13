<?php
require_once __DIR__ . '/../../../middleware/checkAuth.php';
require_once APP_PATH . '/services/branches/branchesServices.php';
$pageTitle = ": Sucursales";
$branches = getAllBranches();
?>
<!DOCTYPE html>
<html lang="en">
<?php include APP_PATH . '/layouts/head.php' ?>

<body>
    <?php include APP_PATH . '/layouts/navbar.php' ?>
    <main>
        <div class="container">
            <h1 class="center-align">Sucursales</h1>
            <?php if (empty($branches)): ?>
                <h3>Aún no cuentas con sucursales registradas.</h3>
            <?php else: ?>
                <div class="col-3">
                    <?php foreach ($branches as $branch): ?>
                        <div class="card">
                            <div class="card-content">
                                <div class="card-action">
                                    <a data-dialog-open="delete_<?= $branch['id'] ?>" href="#!">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" color="currentColor" fill="none">
                                            <path d="M18 6L6.00081 17.9992M17.9992 18L6 6.00085" stroke="#196273" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </a>
                                    <dialog id="delete_<?= $branch['id'] ?>" class="modal center-align">
                                        <h3>¿Estás seguro de eliminar la sucursal de <?= htmlspecialchars($branch['name']) ?>?</h3>
                                        <a class="btn" href="actions/delete.php?id=<?= $branch['id'] ?>">Aceptar</a>
                                    </dialog>
                                </div>
                                <h2 class="card-title">
                                    <?= htmlspecialchars($branch['name']) ?>
                                    <a data-dialog-open="edit_<?= $branch['id'] ?>" href="#!">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="svg-button" color="currentColor" fill="none">
                                            <path d="M16.4249 4.60509L17.4149 3.6151C18.2351 2.79497 19.5648 2.79497 20.3849 3.6151C21.205 4.43524 21.205 5.76493 20.3849 6.58507L19.3949 7.57506M16.4249 4.60509L9.76558 11.2644C9.25807 11.772 8.89804 12.4078 8.72397 13.1041L8 16L10.8959 15.276C11.5922 15.102 12.228 14.7419 12.7356 14.2344L19.3949 7.57506M16.4249 4.60509L19.3949 7.57506" stroke="#141B34" stroke-width="2" stroke-linejoin="round" />
                                            <path d="M18.9999 13.5C18.9999 16.7875 18.9999 18.4312 18.092 19.5376C17.9258 19.7401 17.7401 19.9258 17.5375 20.092C16.4312 21 14.7874 21 11.4999 21H11C7.22876 21 5.34316 21 4.17159 19.8284C3.00003 18.6569 3 16.7712 3 13V12.5C3 9.21252 3 7.56879 3.90794 6.46244C4.07417 6.2599 4.2599 6.07417 4.46244 5.90794C5.56879 5 7.21252 5 10.5 5" stroke="#141B34" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </a>
                                </h2>
                                <div class="divider"></div>
                                <p class="card-texts"><?= htmlspecialchars($branch['address']) ?></p>
                                <a class="btn" href="https://www.google.com/maps/search/?api=1&query=<?= $branch['latitude'] ?>,<?= $branch['longitude'] ?>" target="_blank" rel="noopener noreferrer">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="svg-button" color="currentColor" fill="none">
                                        <path d="M7 18C5.17107 18.4117 4 19.0443 4 19.7537C4 20.9943 7.58172 22 12 22C16.4183 22 20 20.9943 20 19.7537C20 19.0443 18.8289 18.4117 17 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                                        <path d="M14.5 9C14.5 10.3807 13.3807 11.5 12 11.5C10.6193 11.5 9.5 10.3807 9.5 9C9.5 7.61929 10.6193 6.5 12 6.5C13.3807 6.5 14.5 7.61929 14.5 9Z" stroke="currentColor" stroke-width="2"></path>
                                        <path d="M13.2574 17.4936C12.9201 17.8184 12.4693 18 12.0002 18C11.531 18 11.0802 17.8184 10.7429 17.4936C7.6543 14.5008 3.51519 11.1575 5.53371 6.30373C6.6251 3.67932 9.24494 2 12.0002 2C14.7554 2 17.3752 3.67933 18.4666 6.30373C20.4826 11.1514 16.3536 14.5111 13.2574 17.4936Z" stroke="currentColor" stroke-width="2"></path>
                                    </svg>
                                    Ver mapa
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>
    <?php include APP_PATH . '/layouts/scripts.php' ?>
</body>

</html>