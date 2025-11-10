<?php
// Ruta: src/app/middleware/guest.php

session_start();

// Si el usuario tiene tokens activos, ya está logueado
if (isset($_COOKIE['access_token']) || isset($_COOKIE['refresh_token'])) {
    header("Location: /main/");
    exit;
}