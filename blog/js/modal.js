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
    const SERVER_URI = "/US/blog/db/api";
    const registerForm = document.getElementById("register-form");
    const loginForm = document.getElementById("login-form");
    let isRegister; // Flaga rejestacja/logowanie

    const captchaButton = document.getElementById("captcha-button");
    const captchaModal = document.getElementById("captcha-modal");
    const closeModalCaptcha = document.getElementById("close-modal-captcha");
    const captchaVerifyButton = document.getElementById("captcha-verify");
    const captchaExerciseP = document.getElementById("captcha-exercise");
    const captchaExerciseAnswerInput = document.getElementById("captcha-exercise-answer");
    const registerSubmit = document.getElementById("register-submit");

    // Laczenie obu zestawow pol w jedna kolekcje
    // ... pozwala na rozpakowanie wszystkich elementow bezposrednio do nowej kolekcji
    const allFormFields = [...registerFormFields, ...loginFormFields];

    // Otworz okno modalne
    if (loginLink) {
        loginLink.addEventListener("click", () => {
            authModal.style.display = "flex";
            showLoginForm();
        });
    }


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
        clearFieldsAndLabelsStyles();
        isRegister = true;
    });

    // Przelacz modala na login
    toggleAuthInRegister.addEventListener("click", () => {
        showLoginForm();
        clearFieldsAndLabelsStyles();
        isRegister = false;
    });

    // Reset styli etykiet i pol
    const clearFieldsAndLabelsStyles = () => {
        allFormFields.forEach(field => {
            // Znajdz label powiązany z polem za pomocą atrybutu "for" i "id"
            const label = document.querySelector(`label[for="${field.id}"]`);
            if (label) {
                label.style.color = "";
                field.style.borderColor = "";
                field.style.backgroundColor = "";
            }
        });
    }; // clearFieldsAndLabelsStyles()

    const showLoginForm = () => {
        loginContainer.style.display = "block";
        registerContainer.style.display = "none";
        setFocusToUsernameField(loginFormFields);
    };

    const showRegisterForm = () => {
        registerContainer.style.display = "block";
        loginContainer.style.display = "none";
        setFocusToUsernameField(registerFormFields);
    };

    const setFocusToUsernameField = (fields) => {
        // Spread operator (...) - zamienia iterowalmny obiekt NodeList na pojedyncze elementy
        // { } destrukturyzujemy name z obiektu field
        const usernameField = [...fields].find(({ name }) =>
            name === "username"
        );
        usernameField?.focus();
    };

    let captchaCorrectAnswer;
    // Otwieranie modalnego okna captchy
    captchaButton.addEventListener("click", () => {
        captchaModal.style.display = "flex";
        captchaExerciseAnswerInput.value = "";
        captchaCorrectAnswer = generateCaptchaExercise();
    });

    // Zamknięcie modalnego okna captchy
    closeModalCaptcha.addEventListener("click", () => {
        captchaModal.style.display = "none";
    });

    // Symulacja weryfikacji captchy
    captchaVerifyButton.addEventListener("click", () => {
        if (checkCaptchaExerciseAnswer()) {
            captchaModal.style.display = "none";
            captchaButton.style.backgroundColor = "var(--readonly-input)";
            captchaButton.disabled = true;
            registerSubmit.style.display = "block";
            registerSubmit.disabled = false;
        }
        else {
            captchaModal.style.display = "none";
        }
    }); // captchaVerifyButton click

    const generateCaptchaExercise = () => {
        const operators = ["+", "-", "*", "/"];
        const numbersToStringMapping = (number) => {
            const strings = [
                "zero", "jeden", "dwa", "trzy", "cztery",
                "piec", "szesc", "siedem", "osiem", "dziewiec", "dziesiec"
            ];
            return strings[number];
        };

        let a, b, operator, result;
        do {
            a = Math.floor(Math.random() * 11) // 0-10
            b = Math.floor(Math.random() * 11) // 0-10
            operator = operators[Math.floor(Math.random() * operators.length)];
            switch (operator) {
                case '+':
                    result = a + b;
                    break;
                case '-':
                    // Zamiana miejscami jesli b > a
                    [a, b] = b > a ? [b, a] : [a, b]
                    result = a - b;
                    break;
                case '*':
                    result = a * b;
                    break;
                case '/':
                    if (b === 0) continue;
                    // Zamiana miejscami jesli b > a
                    [a, b] = b > a ? [b, a] : [a, b]
                    result = Math.floor(a / b);
                    break;
            }
        }
        while (result < 0 || result > 10 || !Number.isInteger(result)); // Utrzymanie zakresu [0, 10]
        let aString = numbersToStringMapping(a);
        let bString = numbersToStringMapping(b)
        if (operator === "/") {
            captchaExerciseAnswerInput.placeholder = "Podaj wynik całkowity";
        }
        captchaExerciseP.textContent = `${aString} ${operator} ${bString}`;
        return result;
    }; // generateCaptchaExercise()


    const stringToNumbersMapping = (stringNumber) => {
        const polishChars = {
            "ą": "a",
            "ć": "c",
            "ę": "e",
            "ł": "l",
            "ń": "n",
            "ó": "o",
            "ś": "s",
            "ź": "z",
            "ż": "z",
        };
        stringNumber = stringNumber
            .toLowerCase()
            .trim()
            .replace(/[ąćęłńóśźż/]/g, char => polishChars[char]);

        const strings = {
            "zero": 0,
            "jeden": 1,
            "dwa": 2,
            "trzy": 3,
            "cztery": 4,
            "piec": 5,
            "szesc": 6,
            "siedem": 7,
            "osiem": 8,
            "dziewiec": 9,
            "dziesiec": 10
        };
        return strings[stringNumber];
    }; // stringToNumbersMapping()

    const checkCaptchaExerciseAnswer = () => {
        const exerciseAnswer = stringToNumbersMapping(captchaExerciseAnswerInput.value);
        return exerciseAnswer === captchaCorrectAnswer;
    }

    // Zamkniecie modala po kliknieciu poza jego zawartoscia
    window.addEventListener("click", (e) => {
       if (e.target === authModal) {
       //  if (!authModal.contains(e.target)) {
           closeModal();
       }
    });

    window.addEventListener("load", () => {
        allFormFields.forEach(field => {
            // Zdarzenie 'invalid', ktore jest wywolywane, gdy pole formularza jest nieprawidlowe
            field.addEventListener("invalid", (e) => {
                // Zapobiegamy domyslnemu zachowaniu przegladarki m.in. wyswietlenie komunikatu o blędzie
                e.preventDefault()
            });
            // Sluchacze na zdarzenia 'input' i 'blur' dla dynamicznego sprawdzania poprawnosci
            field.addEventListener("input", () => validateField(field));
            field.addEventListener("blur", () => validateField(field));
        }); // forEach

        registerForm.addEventListener("submit", async  (e) => {
            // console.log(`Akcja: ${isRegister}`);
            // Rejestracja
            if (isRegister) {
                e.preventDefault();
                // Flaga poprawnosci formularza rejestracji
                let isFormValid = true;

                // Sprawdzenie poprawnosci hasla i powtrzenia hasla
                isFormValid = checkPasswordWithConfirmPassword();

                // Jesli hasla sa takie same sprawdz pozostale pola
                if (isFormValid) {
                    // Sprawdzenie poprawnosci kazdego pola w formularzu
                    registerFormFields.forEach(field => {
                        if (!field.checkValidity()) {
                            // Jesli pole jest niepoprawne ustaw flage
                            isFormValid = false;
                        }
                    });

                    // Sprawdz czy uzytkownik o takiej nazwie juz nie istnieje
                    if (isFormValid) {
                        const usernameField = Array.from(registerFormFields).find(field =>
                            field.id === "reg-username"
                        );
                        const emailField = Array.from(registerFormFields).find(field =>
                            field.id === "reg-email"
                        );

                        if (usernameField) {
                            // Sprawdz dostępnosc username
                            const isUsernameAvailable = await checkAvailability("username", usernameField.value);
                            if (!isUsernameAvailable?.success) {
                                // Username zajety
                                isFormValid = false;
                                fieldIsInvalid(
                                    usernameField,
                                    document.querySelector(`label[for="${usernameField.id}"]`)
                                );
                                usernameField.nextElementSibling.textContent = "Nazwa użytkownika jest zajęta!";
                            }
                        } // if usernameField
                        if (emailField) {
                            // Sprawdz dostepnosc email
                            const isEmailAvailable = await checkAvailability("email", emailField.value);
                            if (!isEmailAvailable?.success) {
                                // Email zajety
                                isFormValid = false;
                                fieldIsInvalid(
                                    emailField,
                                    document.querySelector(`label[for="${emailField.id}"]`)
                                );
                                emailField.nextElementSibling.textContent = "Konto z takim emailem już istnieje!";
                            }
                        } // if emailField
                    } // if isFormValid
                } // if isFormValid

                // Jesli formularz jest poprawny, wyslij go
                if (isFormValid) {
                    registerForm.submit();
                }
            } // if isRegister
        }); // registerForm event submit

        loginForm.addEventListener("submit", async (e) => {
            e.preventDefault();
            // Login
            if (!isRegister) {
                // Flaga poprawnosci formularza logowania
                let isFormValid = true;

                // Sprawdzenie poprawnosci kazdego pola w formularzu
                loginFormFields.forEach(field => {
                    if (!field.checkValidity()) {
                        // Jesli pole jest niepoprawne ustaw flage
                        isFormValid = false;
                    }
                });

                // Sprawdz czy uzytkownik o takiej nazwie juz istnieje
                if (isFormValid) {
                    const usernameField = Array.from(loginFormFields).find(field =>
                        field.id === "login-username"
                    );

                    if (usernameField) {
                        // Sprawdz dostępnosc username
                        const isUsernameAvailable = await checkUserExisting(usernameField.value);
                        if (!isUsernameAvailable?.success) {
                            // Username nie istnieje
                            isFormValid = false;
                            fieldIsInvalid(
                                usernameField,
                                document.querySelector(`label[for="${usernameField.id}"]`)
                            );
                            usernameField.nextElementSibling.textContent = "Użytkownik o takiej nazwie nie istnieje";
                        }
                    } // if usernameField
                } // if isFormValid

                // Jesli formularz jest poprawny, wyslij go
                if (isFormValid) {
                    loginForm.submit();
                }
            } // if !isRegister
        }); // loginForm event submit
    }); // window event load

    const fieldIsInvalid = (field, label) => {
        label.style.color = "var(--error-text)";
        field.style.borderColor = "var(--error-text)";
        field.classList.remove("valid-input");
        field.classList.add("invalid-input");
        modalContent.classList.add("modal-content-invalid");
    };

    const fieldIsValid = (field, label) => {
        label.style.color = "var(--primary-color)";
        field.style.borderColor = "var(--primary-color)";
        field.classList.remove("invalid-input");
        field.classList.add("valid-input");
    };


    const validateField = (field) => {
        // Pobierz label przed polem
        const label = field.previousElementSibling;
        // Sprawdzamy, czy pole jest niepoprawne i ustawiamy odpowiednie kolory
        if (!field.validity.valid) {
            fieldIsInvalid(field, label);
            switch (field.type) {
                case "text":
                    if (field.validity.tooShort) {
                        field.nextElementSibling.textContent = "Nazwa użytkownika za krótka!";
                    }
                    else if (field.validity.valueMissing) {
                        field.nextElementSibling.textContent = "Wprowadź nazwe użytkownika!";
                    }
                    break;
                case "email":
                    if (field.validity.typeMismatch) {
                        field.nextElementSibling.textContent = "Email niepoprawny!";
                    }
                    else if (field.validity.valueMissing) {
                        field.nextElementSibling.textContent = "Wprowadź email!";
                    }
                    break;
                case "password":
                    if (field.validity.tooShort) {
                        field.nextElementSibling.textContent = "Hasło za krótkie!";
                    }
                    else if (field.validity.valueMissing) {
                        field.nextElementSibling.textContent = "Wprowadź hasło!";
                    }
                    break;
            } // switch field.type
        } // if !field.validity.valid
        else {
            fieldIsValid(field, label);
            field.nextElementSibling.textContent = "";
        }
    }; // validateForm()

    const checkPasswordWithConfirmPassword = () => {
        // Sprawdzenie poprawnosci hasla i potwierdzenia hasla
        // Filtrujemy pola w oparciu o identyfikator, ktory zawiera "password"
        const passwords = Array.from(registerFormFields).filter(field =>
            field.id.includes("password")
        );
        if (passwords.length === 2) {
            // Destrukturyzacja: przypisanie pol do zmiennych
            const [passwordField, confirmPasswordField] = passwords;

            // Porownanie wartosci pol hasla i potwierdzenia hasla
            if (passwordField.value !== confirmPasswordField.value) {
                // Jesli hasla nie sa takie same, oznaczamy oba pola jako niepoprawne
                passwords.forEach(passwordField => {
                    // Pobranie etykiety powiąznej z polem hasla
                    const passwordFieldLabel = document.querySelector(`label[for="${passwordField.id}"]`);

                    // Oznaczanie wizualne pola jako niepoprawnego
                    fieldIsInvalid(passwordField, passwordFieldLabel);

                    // Wyswietlenie komunikatu bledu ponizej pola w spanie
                    passwordField.nextElementSibling.textContent = "Hasła muszą być takie same!";
                });
                return false;
            }
            else {
                passwordField.nextElementSibling.textContent = "";
                confirmPasswordField.nextElementSibling.textContent = "";
                return true;
            }
        } // if password.length
    }; // checkPasswordWithConfirmPassword()

    // Funkcja oznaczona jako async bedzie wykonywana asynchronicznie, tzn.
    // pozwala na wykonywanie operacji, ktore moga zajac troche czasu, bez blokowania reszty program
    const checkAvailability = async (type, value) => {
        try {
            // await to operator, ktory moze byc uzywany tylko w funkcji oznaczonej jako async.
            // Powoduje, ze wykonanie kodu zostaje "zawieszone", dopoki obietnica (promise) nie zostanie rozwiązana
            // (czyli dopoki np. serwer nie odpowie).

            // Wyslanie zapytania GET do API
            const response = await fetch(
                `${SERVER_URI}/check-availability.php?type=${encodeURIComponent(type)}&value=${encodeURIComponent(value)}`, {
                method: "GET",
                headers: {
                    "Content-Type": "application/json"
                }
            });

            // Obsluga blednych odpowiedzi HTTP
            if (!response.ok) {
                const errorMessage = `Server responded with status: ${response.status} ${response.statusText}`;
                alert(errorMessage);
                return {
                    success: false,
                    message: errorMessage,
                };
            }

            // Odczytanie odpowiedzi w JSON
            return await response.json();
        } // try
        catch (error) {
            console.error("Blad podczas sprawdzania dostepnosci nazwy uzytkownika oraz email: ", error);
            return null;
        }
    }; // checkAvailability()

    const checkUserExisting = async (username) => {
      try {
          const response = await fetch(
              `${SERVER_URI}/check-user-exists.php?username=${encodeURIComponent(username)}`, {
                  method: "GET",
                  headers: {
                      "Content-Type": "application/json"
                  }
              });

          // Obsluga blednych odpowiedzi HTTP
          if (!response.ok) {
              const errorMessage = `Server responded with status: ${response.status} ${response.statusText}`;
              alert(errorMessage);
              return {
                  success: false,
                  message: errorMessage,
              };
          }

          // Odczytanie odpowiedzi w JSON
          return await response.json();
      } // try
      catch (error) {
          console.error("Błąd podczas sprawdzania czy użytkownik o podanej nazwie istnieje: ", error);
          return null
      }
    }; // checkUserExisting()
}); // DOMContentLoaded