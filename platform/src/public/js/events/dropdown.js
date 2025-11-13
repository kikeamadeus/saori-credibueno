export class DropdownHandler {
    constructor() {
        this.menuToggle = document.getElementById("menuToggle");
        this.mobileToggle = document.getElementById("mobileToggle");
        this.navMenu = document.getElementById("navMenu");
    }

    toggleDropdown() {
        if (!this.navMenu.classList.contains("show")) {
            this.navMenu.classList.add("show");
        } else {
            this.closeDropdown();
        }
    }

    closeDropdown() {
        this.navMenu.classList.add("closing");
        setTimeout(() => {
            this.navMenu.classList.remove("show", "closing");
        }, 300); // duración de la animación
    }

    bindToggle(button) {
        button.addEventListener("click", (event) => {
            event.preventDefault();
            this.toggleDropdown();
        });
    }

    handleOutsideClick = (event) => {
        if ((!this.mobileToggle || !this.mobileToggle.contains(event.target)) && (!this.menuToggle || !this.menuToggle.contains(event.target)) && !this.navMenu.contains(event.target) && this.navMenu.classList.contains("show")) {
            this.closeDropdown();
        }
    }

    init() {
        if (this.mobileToggle) this.bindToggle(this.mobileToggle);
        if (this.menuToggle) this.bindToggle(this.menuToggle);
        document.addEventListener("click", this.handleOutsideClick);
    }
}