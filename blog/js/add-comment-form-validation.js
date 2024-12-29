document.addEventListener("DOMContentLoaded", () => {
    const form = document.forms.add_comment_form;
    // Captcha
    const captchaButtons = Array.from(document.getElementsByClassName("captcha-button"));
    let captchaValid = false;

    const clearInputErrors = () => {
        form.username.classList.remove("input-error");
        form.email.classList.remove("input-error");
        form.content.classList.remove("input-error");
    };

    const clearErrorMessages = () => {
        form.querySelector("#username + .error").textContent = "";
        form.querySelector("#email + .error").textContent = "";
        form.querySelector("#content + .error").textContent = "";
        document.getElementById("form-errors").textContent = "";
        document.getElementById("captcha-error").textContent = "";
    };

    const showFieldError = (field, message) => {
        const errorElement = form.querySelector(`#${field} + .error`);
        errorElement.textContent = message;
        form[field].classList.add("input-error");
    };

    const validateField = (field, value, pattern, errorMessage) => {
        if (!value) {
            return false;
        }
        if (pattern && !pattern.test(value)) {
            showFieldError(field, errorMessage);
            return false;
        }
        return true;
    };

    form.onsubmit = (e) => {
        let isValid = true;
        e.preventDefault();
        // form.onsubmit = (event) => {

        // Pobieranie wartosci pol formularza
        const username = form.username.value.trim();
        const email = form.email.value.trim();
        const content = form.content.value.trim();

        // Czyszczenie blednych inputow
        clearInputErrors();
        clearErrorMessages();

        // Tablica bledow
        let missingFields = [];

        const usernameValid = validateField("username", username);
        const contentValid = validateField("content", content);
        const emailValid = validateField("email", email, /^[a-zA-Z0-9._]+@[a-zA-Z0-9]{2,10}\.[a-zA-Z]{2,}$/, "Email niepoprawny");

        if (!usernameValid) missingFields.push("username");
        if (!contentValid) missingFields.push("content");
        if (!emailValid) missingFields.push("email");

        // Jesli wiecej niz jedno pole jest bledne, wyswietl zbiorczy komunikat
        if (missingFields.length > 1) {
            document.getElementById("form-errors").textContent = `Nie wypełniono pól: ${missingFields.join(", ")}`;
            isValid = false;
        }

        // Jesli tylko jedno pole jest bledne, wyswietl blad pod tym polem
        if (missingFields.length === 1) {
            const field = missingFields[0];
            showFieldError(field, `${field.charAt(0).toUpperCase() + field.slice(1)} jest wymagany`);
            isValid = false;
        }

        // Walidacja CAPTCHA
        if (!captchaValid) {
            document.getElementById("captcha-error").textContent = "Błąd CAPTCHA";
            isValid = false;
        }
        if (isValid) {
            form.submit();
        }
    };

    // Dla kazdego przycisku obsluz zdarzenie klikniecia
    captchaButtons.forEach((button) => {
       button.addEventListener("click", () => {
           captchaButtons.forEach(btn => btn.classList.remove("checked-captcha-button"));
           button.classList.add("checked-captcha-button");
           captchaValid = button.classList.contains("correct-captcha-button");
       });
    });
});