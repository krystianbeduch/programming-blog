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
    };

    const showRegisterForm = () => {
        registerContainer.style.display = "block";
        loginContainer.style.display = "none";
    };

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
            console.log(`Akcja: ${isRegister}`);
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
                            if ( !(isUsernameAvailable && isUsernameAvailable.success)) {
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
                            if ( !(isEmailAvailable && isEmailAvailable.success)) {
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
                        if (isUsernameAvailable && !isUsernameAvailable.success) {
                            // Username nie istnieje
                            isFormValid = false;
                            fieldIsInvalid(
                                usernameField,
                                document.querySelector(`label[for="${usernameField.id}"]`)
                            );
                            usernameField.nextElementSibling.textContent = "Nazwa użytkownika nie istnieje!";
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
          console.error("Blad podczas sprawdzania czy uzytkownik o podanej nazwie istnieje: ", error);
          return null
      }
    }; // checkUserExisting()
}); // DOMContentLoaded