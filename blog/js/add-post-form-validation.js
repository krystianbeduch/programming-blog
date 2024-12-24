$(document).ready(() => {
    const form = $("form[name='add-post-form']");

    // Pobieranie wszystkich pol formularza
    const formFields = form.find("input, textarea");
    const captchaButtons = $(".captcha-button");
    let isCaptchaValid = false;
    const captchaError = $("#captcha-error");

    formFields.each(function() {
        const field = $(this);

        field.on("invalid", (e) => {
            e.preventDefault();
            validateField(field);
        });
        // Sluchacze na zdarzenia 'input' i 'blur' dla dynamicznego sprawdzania poprawnosci
        field.on("input blur", () => validateField(field));
    });

    form.on("submit", (e) => {
        // Sprawdzenie poprawnosci kazdego pola w formularzu oraz captchy
        const isFormValid = [...formFields].every(field => field.checkValidity()) && isCaptchaValid;

        // Jesli formularz nie jest poprawny, anuluj jego wyslanie
        if (!isFormValid) {
            e.preventDefault();
        }
    });

    const validateField = (field) => {
        // Znajdz label powiazany z polem za pomoca atrybutu "for" i "id"
        const label = $(`label[for="${field.attr("id")}"]`);

        // Znajdz pole bledu powiazane z polem
        const spanError = $(`#${field.attr("id")}-error`);

        if (label.length && spanError.length) {
            if (!field[0].validity.valid) {
                label.css({
                    "color" : "var(--error-text)",
                    "border-color" : "var(--error-text)",
                });
                field
                    .css("color", "var(--error-text)")
                    .addClass("invalid-input")
                    .removeClass("valid-input");
                spanError.text(`Pole \"${field.data("polish-name")}\" niepoprawne`);
            }
            else {
                label.css("color", "var(--primary-text)");
                field
                    .css("border-color", "var(--primary-text)")
                    .addClass("valid-input")
                    .removeClass("invalid-input");
                spanError.text("");
            }
        }
    }; // validateField()

    captchaButtons.each(function() {
       const button = $(this);

       button.on("click", () => {
           captchaButtons.removeClass("checked-captcha-button");
           button.addClass("checked-captcha-button");
           isCaptchaValid = button.hasClass("correct-captcha-button");
           captchaError.text(isCaptchaValid ? "" : "Błędna captcha");
       });
    });
});