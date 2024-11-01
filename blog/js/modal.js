// Funkcja otwierająca modal logowania
function openLoginModal() {
    document.getElementById("auth-modal").style.display = "flex";
    showLoginForm();
}

// Funkcja zamykająca modal
function closeAuthModal() {
    document.getElementById("auth-modal").style.display = "none";
}

// Funkcja pokazująca formularz logowania i ukrywająca formularz rejestracji
function showLoginForm() {
    document.getElementById("login-container").style.display = "block";
    document.getElementById("register-container").style.display = "none";
}

// Funkcja pokazująca formularz rejestracji i ukrywająca formularz logowania
function showRegisterForm() {
    document.getElementById("register-container").style.display = "block";
    document.getElementById("login-container").style.display = "none";
}

// Zamknięcie modala po kliknięciu poza jego zawartością
window.onclick = function(event) {
    const modal = document.getElementById("auth-modal");
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

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

function addValidationEventListeners() {
    // Pobieramy wszystkie pola formularza
    const formFields = document.querySelectorAll("#register-form input");

    formFields.forEach(field => {
        // Dodajemy słuchacze na zdarzenia 'input' i 'blur' dla dynamicznego sprawdzania poprawności
        // field.addEventListener("input", () => validateField(field));
        // field.addEventListener("blur", () => validateField(field));
        field.addEventListener("invalid", (e) => {
            e.preventDefault()
            validateField(field);
        });
        field.addEventListener("input", () => validateField(field));
        field.addEventListener("blur", () => validateField(field));
    });
}
function validateField(field) {
    // e.stopPropagation();
    // e.preventDefault();

    const label = field.previousElementSibling;

    // Resetujemy style etykiety
    label.style.color = "";
    field.style.borderColor = "";
    field.style.backgroundColor = "";

    // Sprawdzamy, czy pole jest niepoprawne, i ustawiamy kolor etykiety na czerwono, jeśli jest
    if (!field.validity.valid) {
        label.style.color = "red";
        field.style.borderColor = "red";
        field.style.backgroundColor = "#ffe6e6";
    } else {
        label.style.color = "green";  // Zielony kolor, jeśli pole jest poprawne
        field.style.borderColor = "green";
        field.style.backgroundColor = "#e6ffe6";
    }
}

// Dodajemy zdarzenie do sprawdzania walidacji po załadowaniu strony
window.addEventListener("load", addValidationEventListeners, false);
