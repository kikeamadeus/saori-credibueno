/**
 * SAORI Global Script
 * -------------------
 * Inicializa los controladores globales comunes:
 * - Validación de formularios
 * - Menú de navegación (solo en /main/)
 * - Diálogos modales
 */

import { FormValidation } from "./form.validation.js";
import { MenuBarHandler } from "./navmenu.js";
import { DialogHandler } from "./dialog.handler.js";

document.addEventListener("DOMContentLoaded", () => {
    const path = window.location.pathname;
    const isMain = path.includes("/main/");

    // ==========================================================
    // 1) Inicializar validación de formularios
    // ==========================================================
    const formValidation = new FormValidation();
    formValidation.init();

    // ==========================================================
    // 2) Inicializar menú de navegación (solo vistas main)
    // ==========================================================
    if (isMain) {
        const navMenu = new MenuBarHandler();
        navMenu.init();
    }

    // ==========================================================
    // 3) Inicializar diálogos modales
    // ==========================================================
    const dialogHandler = new DialogHandler();
    dialogHandler.init();

    // ==========================================================
    // 4) Notificación de carga (para debugging o monitoreo)
    // ==========================================================
    //console.info(`✅ SAORI global script cargado correctamente [${isMain ? 'main' : 'public'}]`);
});
