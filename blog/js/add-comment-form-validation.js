document.addEventListener("DOMContentLoaded", () => {
    const form = document.forms.add_comment_form;
    // Captcha
    const captchaButtons = Array.from(document.getElementsByClassName("captcha-button"));
    let captchaValid = false;

    // form.addEventListener("submit", (event) => {
    form.onsubmit = (event) => {

        // Pobieranie wartosci pol formularza
        const username = form.username.value.trim();
        const email = form.email.value.trim();
        const content = form.content.value.trim();

        // Czyszczenie stylow blednych inputow
        form.username.classList.remove("input-error");
        form.email.classList.remove("input-error");
        form.content.classList.remove("input-error");

        // Pola bledow
        const unsernameError = form.querySelector("#username + .error");
        const emailError = form.querySelector("#email + .error");
        const contentError = form.querySelector("#content + .error");
        const formErrors = document.getElementById("form-errors");
        const captchaError = document.getElementById("captcha-error");

        // Flaga poprawnosci formularza
        let formValid = true;

        // Czyszczenie komunikatow bledow
        unsernameError.textContent = "";
        emailError.textContent = "";
        contentError.textContent = "";
        formErrors.textContent = "";
        captchaError.textContent = "";

        // Tablica bledow
        let missingFields = [];

        // Flaga poprawnosci emaila
        let emailInvalid = false;

        // Walidacja pola Nickname
        if (username === "") {
            missingFields.push({field: "username", message: "Nazwa użytkownika jest wymagana"});
            form.username.classList.add("input-error");
        }

        // Walidacja pola "Tresc komentarza"
        if (content === "") {
            missingFields.push({field: "komentarz", message: "Komentarz jest wymagany"});
            form.content.classList.add("input-error");
        }

        // Walidacja pola "Email"
        const emailPattern = /^[a-zA-Z0-9._]+@[a-zA-Z0-9]{2,10}\.[a-zA-Z]{2,}$/;
        if (email === "") {
            missingFields.push({field: "email", message: "Email jest wymagany"});
            form.email.classList.add("input-error");
        }
        else if (!emailPattern.test(email)) {
            emailError.textContent = "Email niepoprawny";
            emailInvalid = true;
            form.email.classList.add("input-error");
            formValid = false;
        }

        if (missingFields.length === 1) {
            formErrors.textContent = "";
            const error = missingFields[0];
            // Wyswietlenie pojedynczego bledu
            switch (error.field) {
                case "username":
                    unsernameError.textContent = error.message;
                    break;
                case "email":
                    emailError.textContent = error.message;
                    break;
                case "komentarz":
                    contentError.textContent = error.message;
                    break;
            }
            // event.preventDefault(); // Zatrzymanie wysylania formularza
            // return false;
            formValid = false;
        }
        else if (missingFields.length > 1) {
            unsernameError.textContent = "";
            contentError.textContent = "";
            if (!emailInvalid) {
                emailError.textContent = "";
            }

            // Zmiana tresci komunikatow
            missingFields.forEach(missingField => {
                missingField.message = missingField.field;
            });
            // Wyswielenie zbiorczego bledu
            formErrors.textContent = "Nie wypełniono pól " +
                missingFields.map(error => error.message).join(", ");
            // event.preventDefault(); // Zatrzymanie wysylania formularza
            // return false;
            formValid = false;
        }

        // Walidacja CAPTCHA
        // console.log(captchaValid);
        if (!captchaValid) {
            captchaError.textContent = "Błąd CAPTCHA";
            // event.preventDefault();
            // return false;
            formValid = false;
        }

        // console.log(formValid);
        return formValid;
    };
   // });


    // Dla kazdego przycisku obsluz zdarzenie klikniecia
    captchaButtons.forEach((button) => {
       button.addEventListener("click", () => {
           captchaButtons.forEach(btn => btn.classList.remove("checked-captcha-button"));
           button.classList.add("checked-captcha-button");
           captchaValid = button.classList.contains("correct-captcha-button");
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