export class FormValidation {
    constructor() {
        this.validationRules = {
            username: {
                regex: /^[a-zA-Z0-9_]{3,20}$/,
                message: "El nombre de usuario solo puede contener guión bajo, números y letras",
            },

            password: {
                regex: /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d$%#_-]{8,12}$/,
                message: "La contraseña debe tener entre 8 y 12 caracteres, incluir al menos una letra, un número y solo los símbolos permitidos: $ % # _ -",
            },

            category: {
                regex: /^[a-zA-ZÀ-ÿ0-9\s]{3,50}$/,
                message: "El nombre de la categoría debe tener entre 3 y 50 caracteres y solo puede contener letras, números y espacios.",
            }
        }
    }

    init() {
        document.querySelectorAll('.formData').forEach(form => {
            this.beginValidation(form);
        });
    }

    beginValidation(form) {
        //Previene que el formulario reciba múltiples listeners
        if (form.dataset.listenerAttached === "true") return;

        form.querySelectorAll('input, textarea').forEach(input => {
            input.addEventListener('blur', () => this.validateField(input));
        });

        form.addEventListener('submit', (event) => {
            event.preventDefault();
            this.handleSubmit(form);
        });

        //Marca que este formulario ya fue inicializado
        form.dataset.listenerAttached = "true";
    }

    validateField(input) {
        const rules = this.validationRules[input.name];
        if (!rules) return true;

        if (!rules.regex.test(input.value.trim())) {
            this.showErrorMessage(input, rules.message);
            return false;
        }
        else {
            this.hideErrorMessage(input);
            return true;
        }
    }

    handleSubmit(form) {
        let isValid = true;
        let formData = new FormData(form);
        let actionUrl = form.getAttribute("action");

        form.querySelectorAll("input, textarea").forEach(input => {
            if (!this.validateField(input)) {
                isValid = false;
            }
        });

        if (!isValid) return;

        fetch(actionUrl, {
            method: "POST",
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        alert(data.message || "Operación exitosa.");
                        window.location.reload(); // Forzar recarga de la página actual
                        //this.showSuccessMessage(form, data.message || "Operación exitosa.");
                    }
                } else {
                    this.showErrorMessage(form, data.message || "Ocurrió un error al intentar iniciar sesión.");
                }
            })
            .catch(error => {
                console.error("Error en la solicitud:", error);
                this.showErrorMessage(form, "Ocurrió un error en la conexión con el servidor.");
            });
    }

    showErrorMessage(input, message) {
        let errorSpan = input.nextElementSibling;
        if (!errorSpan || !errorSpan.classList.contains("error-message")) {
            errorSpan = document.createElement("span");
            errorSpan.classList.add("error-message");
            input.parentNode.appendChild(errorSpan);
        }
        errorSpan.textContent = message;
        input.classList.add("invalid");
    }

    hideErrorMessage(input) {
        let errorSpan = input.nextElementSibling;
        if (errorSpan && errorSpan.classList.contains("error-message")) {
            errorSpan.remove();
        }
        input.classList.remove("invalid");
    }

    showSuccessMessage(form, message) {
        let successDiv = form.querySelector(".success-message");
        if (!successDiv) {
            successDiv = document.createElement("div");
            successDiv.classList.add("success-message");
            successDiv.style.color = "green";
            form.prepend(successDiv);
        }
        successDiv.textContent = message;
    }
}