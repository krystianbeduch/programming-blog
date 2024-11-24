// export const validateForm = (form, config) => {
//     // Flaga poprawności formularza
//     let formValid = true;
//
//     // Czyszczenie stylów błędnych inputów i komunikatów błędów
//     config.fields.forEach(field => {
//         const input = form[field.name];
//         const errorField = form.querySelector(`#${field.name} + .error`);
//         input.classList.remove("input-error");
//         if (errorField) {
//             errorField.textContent = "";
//         }
//     });
//     const formErrors = document.getElementById("form-errors");
//     if (formErrors) formErrors.textContent = "";
//
//     // Tablica błędów
//     const missingFields = [];
//
//     // Walidacja pól
//     config.fields.forEach(field => {
//         const input = form[field.name];
//         const value = input.value.trim();
//
//         if (field.required && value === "") {
//             missingFields.push({ field: field.name, message: field.errorMessage || `${field.label} jest wymagany` });
//             input.classList.add("input-error");
//             formValid = false;
//         }
//
//         if (field.pattern && value !== "" && !field.pattern.test(value)) {
//             const errorField = form.querySelector(`#${field.name} + .error`);
//             if (errorField) errorField.textContent = field.invalidMessage || `Niepoprawny ${field.label}`;
//             input.classList.add("input-error");
//             formValid = false;
//         }
//     });
//
//     // Zbiorczy komunikat błędów
//     if (missingFields.length > 1 && formErrors) {
//         formErrors.textContent = "Nie wypełniono pól: " +
//             missingFields.map(error => error.message).join(", ");
//         formValid = false;
//     }
//     else if (missingFields.length === 1) {
//         const error = missingFields[0];
//         const errorField = form.querySelector(`#${error.field} + .error`);
//         if (errorField) errorField.textContent = error.message;
//         formValid = false;
//     }
//
//     // CAPTCHA walidacja
//     const captchaValid = config.validateCaptcha ? config.validateCaptcha() : true;
//     const captchaError = document.getElementById("captcha-error");
//     if (!captchaValid && captchaError) {
//         captchaError.textContent = "Błąd CAPTCHA";
//         formValid = false;
//     }
//
//     return formValid;
// };
