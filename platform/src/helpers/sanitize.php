<?php
// src/helpers/sanitize.php

/**
 * Limpia un string eliminando etiquetas HTML peligrosas
 * y espacios extra.
 */
function sanitizeString($string) {
    return trim(htmlspecialchars($string, ENT_QUOTES, 'UTF-8'));
}