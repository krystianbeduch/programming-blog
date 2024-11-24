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

        form.addEventListener("submit", (e) => {
           let isFormValid = true;

           formFields.forEach(field => {
              if (!field.checkValidity()) {
                  isFormValid = false;
              }
           });
            // Walidacja CAPTCHA
            if (!captchaValid) {
                isFormValid = false;
            }

            if (!isFormValid) {
                e.preventDefault();
            }
        });

        // form.addEventListener("submit", (e) => {
        //     let isFormValid = true;
        //
        //     formFields.forEach(field => {
        //         validateField(field); // Walidacja pola
        //         // Wymuszenie walidacji przeglądarki
        //         if (!field.checkValidity()) {
        //             isFormValid = false;
        //         }
        //     });
        //
        //     if (!isFormValid) {
        //         e.preventDefault(); // Blokuj wysłanie formularza
        //     }
        // });

        if (!captchaValid) {
            // captchaError.textContent = "Błąd CAPTCHA";
            // event.preventDefault();
                // return false;
                // formValid = false;
        }
    });

    const validateField = (field) => {
        // Znajdź label powiązany z polem za pomocą atrybutu "for" i "id"
        const label = document.querySelector(`label[for="${field.id}"]`);
        const spanError = document.querySelector(`#${field.id}\-error`);

        if (label && spanError) {
            if(!field.validity.valid) {
                label.style.color = "red";
                field.style.borderColor = "red";
                field.classList.remove("valid-input");
                field.classList.add("invalid-input");
                spanError.textContent = `Pole ${field.id} niepoprawne`;
            }
            else {
                label.style.color = "green";
                field.style.borderColor = "green";
                field.classList.remove("invalid-input");
                field.classList.add("valid-input");
                spanError.textContent = "";
            }
        }
    };




    // // Pobieranie wartosci pol formularza
    // const nick = form.nick.value.trim();
    // const email = form.email.value.trim();
    // const comment = form['post-content'].value.trim();
    //
    // // Czyszczenie stylow blednych inputow
    // form.nick.classList.remove("input-error");
    // form.email.classList.remove("input-error");
    // form.comment.classList.remove("input-error");
    //
    // // Pola bledow
    // const nickError = form.querySelector("#nick + .error");
    // const emailError = form.querySelector("#email + .error");
    // const commentError = form.querySelector("#comment + .error");
    // const formErrors = document.getElementById("form-errors");
    // const captchaError = document.getElementById("captcha-error");

    // const invalidHanlder = (event) => {
    //     let poprawnosc = event.srcElement.validity;
    //     if(poprawnosc.valueMissing) {
    //         formErrors.textContent = poprawnosc;
    //     }
    //     event.preventDefault();
    // };
    // Captcha
    // const captchaButtons = Array.from(document.getElementsByClassName("captcha-button"));
    // let captchaValid = false;
    //
    // // form.addEventListener("submit", (event) => {
    // form.onsubmit = (event) => {
    //
    //     // Pobieranie wartosci pol formularza
    //     const nick = form.nick.value.trim();
    //     const email = form.email.value.trim();
    //     const comment = form['post-content'].value.trim();
    //
    //     // Czyszczenie stylow blednych inputow
    //     form.nick.classList.remove("input-error");
    //     form.email.classList.remove("input-error");
    //     form.comment.classList.remove("input-error");
    //
    //     // Pola bledow
    //     const nickError = form.querySelector("#nick + .error");
    //     const emailError = form.querySelector("#email + .error");
    //     const commentError = form.querySelector("#comment + .error");
    //     const formErrors = document.getElementById("form-errors");
    //     const captchaError = document.getElementById("captcha-error");
    //
    //     // Flaga poprawnosci formularza
    //     let formValid = true;
    //
    //     // Czyszczenie komunikatow bledow
    //     nickError.textContent = "";
    //     emailError.textContent = "";
    //     commentError.textContent = "";
    //     formErrors.textContent = "";
    //     captchaError.textContent = "";
    //
    //     // Tablica bledow
    //     let missingFields = [];
    //
    //     // Flaga poprawnosci emaila
    //     let emailInvalid = false;
    //
    //     // Walidacja pola Nickname
    //     if (nick === "") {
    //         missingFields.push({field: "nickname", message: "Nick jest wymagany"});
    //         form.nick.classList.add("input-error");
    //     }
    //
    //     // Walidacja pola "Tresc komentarza"
    //     if (comment === "") {
    //         missingFields.push({field: "komentarz", message: "Komentarz jest wymagany"});
    //         form.comment.classList.add("input-error");
    //     }
    //
    //     // Walidacja pola "Email"
    //     const emailPattern = /^[a-zA-Z0-9._]+@[a-zA-Z0-9]{2,10}\.(pl|com)$/;
    //     if (email === "") {
    //         missingFields.push({field: "email", message: "Email jest wymagany"});
    //         form.email.classList.add("input-error");
    //     }
    //     else if (!emailPattern.test(email)) {
    //         emailError.textContent = "Email niepoprawny";
    //         emailInvalid = true;
    //         form.email.classList.add("input-error");
    //         formValid = false;
    //     }
    //
    //     if (missingFields.length === 1) {
    //         formErrors.textContent = "";
    //         const error = missingFields[0];
    //         // Wyswietlenie pojedynczego bledu
    //         switch (error.field) {
    //             case "nickname":
    //                 nickError.textContent = error.message;
    //                 break;
    //             case "email":
    //                 emailError.textContent = error.message;
    //                 break;
    //             case "komentarz":
    //                 commentError.textContent = error.message;
    //                 break;
    //         }
    //         // event.preventDefault(); // Zatrzymanie wysylania formularza
    //         // return false;
    //         formValid = false;
    //     }
    //     else if (missingFields.length > 1) {
    //         nickError.textContent = "";
    //         commentError.textContent = "";
    //         if (!emailInvalid) {
    //             emailError.textContent = "";
    //         }
    //
    //         // Zmiana tresci komunikatow
    //         missingFields.forEach(missingField => {
    //             missingField.message = missingField.field;
    //         });
    //         // Wyswielenie zbiorczego bledu
    //         formErrors.textContent = "Nie wypełniono pól " +
    //             missingFields.map(error => error.message).join(", ");
    //         // event.preventDefault(); // Zatrzymanie wysylania formularza
    //         // return false;
    //         formValid = false;
    //     }
    //
    //     // Walidacja CAPTCHA
    //     console.log(captchaValid);
    //     if (!captchaValid) {
    //         captchaError.textContent = "Błąd CAPTCHA";
    //         // event.preventDefault();
    //         // return false;
    //         formValid = false;
    //     }
    //
    //     console.log(formValid);
    //     if (formValid) {
    //         // execeuteReptcha(form);
    //         return true;
    //     }
    //     else {
    //
    //         return false;
    //     }
    // };
    // });


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

    // captchaValidation = () => {
    //
    // };
});