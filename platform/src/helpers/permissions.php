<?php
/**
 * Helper para validar permisos desde la sesión
 */

function hasPermission(string $key): bool
{
    return !empty($_SESSION['permissions'][$key]);
}