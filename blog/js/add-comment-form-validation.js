$(document).ready(() => {
    const form = $("form[name='add-comment-form']");
    const captchaButtons = $(".captcha-button");
    let captchaValid = false;

    const fieldNamesMap = {
        username: "Nazwa użytkownika",
        email: "Email",
        content: "Treść komentarza"
    };

    const clearInputErrors = () => {
        form.find("input, textarea").removeClass("input-error");
    };

    const clearErrorMessages = () => {
        form.find(".error").empty();
        $("#form-errors, #captcha-error").empty();
    };

    const showFieldError = (field, message) => {
        const input = form.find(`[name='${field}']`)
        input.next(".error").text(message);
        input.addClass("input-error");
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

    form.on("submit", (e) => {
        e.preventDefault();
        let isValid = true;

        // Pobieranie wartosci pol formularza
        const username = form.find("[name='username']").val().trim();
        const email = form.find("[name='email']").val().trim();
        const content = form.find("[name='content']").val().trim();

        // Czyszczenie blednych inputow
        clearInputErrors();
        clearErrorMessages();

        // Tablica bledow
        const missingFields = [];

        const usernameValid = validateField("username", username);
        const contentValid = validateField("content", content);
        const emailPattern = /^[a-zA-Z0-9._]+@[a-zA-Z0-9]{2,10}\.[a-zA-Z]{2,}$/;
        const emailValid = validateField("email", email, emailPattern,"Email niepoprawny");

        if (!usernameValid) missingFields.push("username");
        if (!contentValid) missingFields.push("content");
        if (!emailValid) missingFields.push("email");

        // Jesli wiecej niz jedno pole jest bledne, wyswietl zbiorczy komunikat
        if (missingFields.length > 1) {
            $("#form-errors").text(
                `Nie wypełniono pól: 
                ${missingFields.map(field => fieldNamesMap[field]).join(", ")}`
            );
            isValid = false;
        }

        // Jesli tylko jedno pole jest bledne, wyswietl blad pod tym polem
        if (missingFields.length === 1) {
            const field = missingFields[0];
            if (field === "email") {
                if (!emailValid && email) {
                    showFieldError(field, `${fieldNamesMap[field]} niepoprawny`);
                }
                else {
                    showFieldError(field, `${fieldNamesMap[field]} jest wymagany`);
                }
            } // if field email
            else {
                showFieldError(field, `${fieldNamesMap[field]} jest wymagana`);
            }
            isValid = false;
        }

        // Walidacja CAPTCHA
        if (!captchaValid) {
            $("#captcha-error").text("Błąd CAPTCHA");
            isValid = false;
        }
        if (isValid) {
            form.off("submit").submit();
        }
    });

    // Dla kazdego przycisku obsluz zdarzenie klikniecia
    captchaButtons.on("click", function() {
        captchaButtons.removeClass("checked-captcha-button");
        $(this).addClass("checked-captcha-button");
        captchaValid = $(this).hasClass("correct-captcha-button");
    });
});