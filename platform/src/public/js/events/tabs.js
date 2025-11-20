export class TabsHandler {

    init() {
        const tabGroups = document.querySelectorAll("[data-tabs]");

        if (!tabGroups.length) return;

        tabGroups.forEach(group => this.setupGroup(group));
    }

    setupGroup(group) {
        const buttons = group.querySelectorAll("[data-tab]");
        const sections = group.querySelectorAll("[data-tab-section]");

        if (!buttons.length || !sections.length) return;

        buttons.forEach(btn => {
            btn.addEventListener("click", () => {
                const target = btn.dataset.tab;

                // remover "active" de todos los botones
                buttons.forEach(b => b.classList.remove("active"));

                // mostrar el activo
                btn.classList.add("active");

                // ocultar todas las secciones
                sections.forEach(sec => {
                    if (sec.dataset.tabSection === target) {
                        sec.classList.remove("hidden");
                    } else {
                        sec.classList.add("hidden");
                    }
                });
            });
        });

        // seleccionar el primer tab automáticamente
        buttons[0].click();
    }
}