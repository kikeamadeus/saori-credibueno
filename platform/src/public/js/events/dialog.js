export class DialogHandler {
    constructor() {
        this.dialogs = document.querySelectorAll("dialog");
        this.openButtons = document.querySelectorAll("[data-dialog-open]");
        this.closeButtons = document.querySelectorAll("[data-dialog-close]");
    }

    openDialog(dialogId) {
        const dialog = document.getElementById(dialogId);
        if (dialog && !dialog.open) {
            dialog.showModal();
            dialog.classList.add("open");
        }
    }

    closeDialog(dialog) {
        if (dialog && dialog.open) {
            dialog.classList.remove("open");
            setTimeout(() => dialog.close(), 300);
        }
    }

    handleOpenClick = (event) => {
        event.preventDefault();
        const button = event.currentTarget;
        const dialogId = button.getAttribute("data-dialog-open");
        this.openDialog(dialogId);
    };

    handleCloseClick = (event) => {
        event.preventDefault();
        const dialog = event.currentTarget.closest("dialog");
        this.closeDialog(dialog);
    };

    disableBackdropClose(dialog) {
        dialog.addEventListener("click", (event) => {
            const rect = dialog.getBoundingClientRect();
            const isInDialog =
                rect.top <= event.clientY &&
                event.clientY <= rect.top + rect.height &&
                rect.left <= event.clientX &&
                event.clientX <= rect.left + rect.width;

            if (!isInDialog) {
                this.closeDialog(dialog);
            }
        });

        dialog.addEventListener("cancel", (event) => {
            event.preventDefault(); // Evita que se cierre con la tecla ESC
        });
    }

    init() {
        this.openButtons.forEach((button) => {
            button.addEventListener("click", this.handleOpenClick);
        });

        this.closeButtons.forEach((button) => {
            button.addEventListener("click", this.handleCloseClick);
        });

        this.dialogs.forEach((dialog) => {
            this.disableBackdropClose(dialog);
        });
    }
}