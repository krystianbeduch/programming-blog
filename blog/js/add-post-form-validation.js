// import {validateForm} from "./modules/form-validation";

// import {validateForm} from "./modules/form-validation";

document.addEventListener("DOMContentLoaded", () => {
    const form = document.forms.add_post_form;

    // Pobieranie wszystkich pol formularza
    const formFields = form.querySelectorAll("input, textarea");
    const captchaButtons = Array.from(document.getElementsByClassName("captcha-button"));
    let captchaValid = false;
    const captchaError = document.getElementById("captcha-error");

    window.addEventListener("load", () => {
        // console.log("simea");
        formFields.forEach(field => {
            // if (field.classList.contains("invalid-input")) {
            //     console.log(`check: ${field}`);
            //     validateField(field);
            // }
            // validateField(field)
            // console.log("simea");
            field.addEventListener("invalid", (e) => {
                // console.log("siea");
                e.preventDefault();
                // e.stopPropagation();
                validateField(field);
            });
            // Sluchacze na zdarzenia 'input' i 'blur' dla dynamicznego sprawdzania poprawnosci
            field.addEventListener("input", () => validateField(field));
            field.addEventListener("blur", () => validateField(field));
            // form.addEventListener("submit", () => validateForm(field));
        });

        // Sprawdzenie calosci formularza przed wysylka wraz z captcha
        form.addEventListener("submit", (e) => {
            // Flaga poprawnosci formularza
            let isFormValid = true;

            // Sprawdzenie poprawnosci kazdego pola w formularzu
            formFields.forEach(field => {
                if (!field.checkValidity()) {
                    // Jesli pole jest niepoprawne ustaw flage
                    isFormValid = false;
                }
            });

            // Sprawdzenie, czy CAPTCHA zostala zaznaczona poprawnie
            if (!captchaValid) {
                // Jesli CAPTCHA jest niepoprawna, ustaw flage na false
                isFormValid = false;
            }

            // Jesli formularz nie jest poprawny, anuluj jego wyslanie
            if (!isFormValid) {
                e.preventDefault();
            }
        });

    });

    const validateField = (field) => {
        // Znajdz label powiązany z polem za pomocą atrybutu "for" i "id"
        const label = document.querySelector(`label[for="${field.id}"]`);

        // Znajdz pole bledu powiazane z polem
        const spanError = document.querySelector(`#${field.id}\-error`);

        if (label && spanError) {
            if (!field.validity.valid) {
                label.style.color = "#e3192c";
                field.style.borderColor = "#e3192c";
                field.classList.remove("valid-input");
                field.classList.add("invalid-input");
                // field.classList.add("input-error");
                spanError.textContent = `Pole ${field.id} niepoprawne`;
            }
            else {
                label.style.color = "#218838";
                field.style.borderColor = "#218838";
                field.classList.remove("invalid-input");
                field.classList.add("valid-input");
                spanError.textContent = "";
            }
        }
    };

    // Dla kazdego przycisku obsluz zdarzenie klikniecia
    captchaButtons.forEach((button) => {
        button.addEventListener("click", () => {
            captchaButtons.forEach(btn => btn.classList.remove("checked-captcha-button"));
            button.classList.add("checked-captcha-button");
            captchaValid = button.classList.contains("correct-captcha-button");
            if (!captchaValid) {
                captchaError.textContent = "Błędna captcha";
            }
            else {
                captchaError.textContent = "";
            }
        });
    });

    // const execeuteReptcha = (form) => {
    //     console.log("ca");
    //     grecaptcha.ready(function() {
    //         grecaptcha.execute('6Lee1W8qAAAAAMwE7w2suh1TsFE9Gku9HZFfP0vB', { action: 'submit' }).then(function(token) {
    //             document.getElementById('recaptcha_response').value = token;
    //             form.submit();
    //         });
    //     });
    // };
});