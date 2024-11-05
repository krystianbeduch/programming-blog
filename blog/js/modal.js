document.addEventListener("DOMContentLoaded", () => {
    const loginLink = document.getElementById("login-link");
    const authModal = document.getElementById("auth-modal");
    const loginContainer = document.getElementById("login-container");
    const registerContainer = document.getElementById("register-container");
    const closeModalSpan = document.getElementById("close-modal");
    const toggleAuthInLogin = document.querySelector("#login-container .toggle-auth")
    const toggleAuthInRegister = document.querySelector("#register-container .toggle-auth")
    const registerFormFields = document.querySelectorAll("#register-form input");
    const loginFormFields = document.querySelectorAll("#login-form input");
    const modalContent = document.getElementsByClassName("modal-content")[0];

    // Laczenie obu zestawow pol w jedna kolekcje
    // ... pozwala na rozpakowanie wszystkich elementow bezposrednio do nowej kolekcji
    const allFormFields = [...registerFormFields, ...loginFormFields];

    // Otworz okno modalne
    loginLink.addEventListener("click", () => {
        authModal.style.display = "flex";
        showLoginForm();
    });

    // Zamknij okno modalne
    const closeModal = () => {
        authModal.style.display = "none";
    };

    closeModalSpan.addEventListener("click", closeModal);

    // Zamkniecie okna przez przycisk Esc
    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape") {
            closeModal();
        }
    });

    // Przelacz modala na rejestracje
    toggleAuthInLogin.addEventListener("click", () => {
        showRegisterForm();
        clearFieldsAndLabelsStyles()
    });

    // Przelacz modala na login
    toggleAuthInRegister.addEventListener("click", () => {
        showLoginForm();
        clearFieldsAndLabelsStyles();
    });

    // Reset styli etykiet i pol
    const clearFieldsAndLabelsStyles = () => {
        allFormFields.forEach(field => {
            const label = field.previousElementSibling;
            label.style.color = "";
            field.style.borderColor = "";
            field.style.backgroundColor = "";
        });
    };

    const showLoginForm = () => {
        loginContainer.style.display = "block";
        registerContainer.style.display = "none";
    };

    const showRegisterForm = () => {
        registerContainer.style.display = "block";
        loginContainer.style.display = "none";
    };

    // Zamkniecie modala po kliknieciu poza jego zawartoscia
    window.addEventListener("click", (event) => {
       if (event.target === authModal) {
           authModal.style.display = "none";
       }
    });

    window.addEventListener("load", () => {
        allFormFields.forEach(field => {
            // Zdarzenie 'invalid', ktore jest wywolywane, gdy pole formularza jest nieprawidlowe
            field.addEventListener("invalid", (e) => {
                // Zapobiegamy domyslnemu zachowaniu przegladarki m.in. wyswietlenie komunikatu o blędzie
                e.preventDefault()
                validateField(field);
            });
            // Sluchacze na zdarzenia 'input' i 'blur' dla dynamicznego sprawdzania poprawnosci
            field.addEventListener("input", () => validateField(field));
            field.addEventListener("blur", () => validateField(field));
        });
    });


    const validateField = (field) => {
        const label = field.previousElementSibling;

        // Sprawdzamy, czy pole jest niepoprawne i ustawiamy odpowiednie kolory
        if (!field.validity.valid) {
            label.style.color = "red";
            field.style.borderColor = "red";
            field.classList.remove("valid-input");
            field.classList.add("invalid-input");
            modalContent.classList.add("modal-content-invalid");

        }
        else {
            label.style.color = "green";
            field.style.borderColor = "green";
            field.classList.remove("invalid-input");
            field.classList.add("valid-input");
        }
    };

});



// function invalidHandler(e) {
//     let label = e.srcElement.parentElement.getElementsByTagName("label")[0];
//     label.style.color = "red";
//     e.stopPropagation();
//     e.preventDefault();
// }
//
// function zarejestuj() {
//     document.register.addEventListener("invalid", invalidHandler, true);
// }
//
// window.addEventListener("load", zarejestuj, false);

// function addValidationEventListeners() {
//     // Pobieramy wszystkie pola formularza
//     const formFields = document.querySelectorAll("#register-form input");
//
//     formFields.forEach(field => {
//         // Dodajemy słuchacze na zdarzenia 'input' i 'blur' dla dynamicznego sprawdzania poprawności
//         // field.addEventListener("input", () => validateField(field));
//         // field.addEventListener("blur", () => validateField(field));
//         field.addEventListener("invalid", (e) => {
//             e.preventDefault()
//             validateField(field);
//         });
//         field.addEventListener("input", () => validateField(field));
//         field.addEventListener("blur", () => validateField(field));
//     });
// }


// Dodajemy zdarzenie do sprawdzania walidacji po załadowaniu strony
// window.addEventListener("load", addValidationEventListeners, false);
