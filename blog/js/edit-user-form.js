$(document).ready(() => {
    const form = $("#edit-user-form");

    // Referencje do pol formularza
    const inputs = {
        username: form.find("input[name='username']"),
        email: form.find("input[name='email']"),
        currentPassword: form.find("input[name='current-password']"),
        newPassword: form.find("input[name='new-password']"),
        newPasswordConfirm: form.find("input[name='new-password-confirm']"),
        aboutMe: form.find("textarea")
    };

    // Referencje do etykiet
    const labels = {
        username: form.find(`label[for="${inputs.username.attr('id')}"`),
        email: form.find(`label[for="${inputs.email.attr('id')}"`),
        currentPassword: form.find(`label[for="${inputs.currentPassword.attr('id')}"`),
        newPassword: form.find("label[for*='new-password']"),
        passwordLegend: form.find("fieldset[id*='password']").find("legend"),
        aboutMe: form.find(`label[for="${inputs.aboutMe.attr('id')}"`)
    };

    // Oryginalne tresci etykiet
    const labelsOriginal = {
        username: labels.username.text(),
        email: labels.email.text(),
    }

    let editCorrect = true; // Flaga poprawnej edycji pola

    const allFormFields = $("input, textarea"); //////// ??

    const editButtons = $(".edit-profile-button");
    const SERVER_URI = "/US/blog/db/api";

    // Pokaz/ukryj przyciski edycji
    const toggleButtons = (button, show = true) => {
        $("form").find("button").each(function() {
            $(this).toggle(show);
        });
        button.show();
        if (button.text() === "Zmień") {
            button.text("Zapisz");
        }
        else {
            button.text("Zmień");
        }
    };

    const enableField = (input) => {
        input.removeAttr("disabled readonly").attr("required", true).focus();
    };

    const disableField = (input) => {
        input.attr("readonly", true).removeAttr("required");
    };

    const validateAndSave = async (input, label, labelTextOriginal, type) => {
        const newValue = input.val();
        // Sprawdzenie czy wartosc sie zmienila
        if (newValue !== input.data("originalValue")) {
            if (validateField(input, label)) {
                const result = await checkAvailability(type, newValue);
                /* ECMAScript 2020 wprowadzil opcjonalny lancuch dostepu
                (Optional Chaining Operator). Jesli result bedzie null lub undefined operator zwroci undefined
                */
                if (result?.success) {
                    label.text(labelTextOriginal);
                    fieldIsValid(input, label);
                }
                else {
                    label.text(`${labelTextOriginal} (nazwa zajęta)`);
                    fieldIsInvalid(input, label);
                }
            } // if validateField
        } // if newValue
        else {
            label.text(labelTextOriginal);
            fieldStyleReset(input, label);
        }
    }; // validateAndSave()

    const handlePasswordFields = (button) => {
        if (button.text() === "Zmień") {
            toggleButtons(button, false);
            // Wlaczenie wszystkich pol hasla
            [inputs.currentPassword, inputs.newPassword, inputs.newPasswordConfirm]
                .forEach(enableField);
            inputs.currentPassword.focus();
            labels.passwordLegend.removeClass("edited-label");
            labels.newPassword.removeClass("invalid-label");
        }
        else if (button.text() === "Zapisz") {
            // Sprawdz czy wprowadzono aktualne haslo
            if (inputs.currentPassword.val().trim() === "") {
                inputs.currentPassword.focus();
                labels.currentPassword.addClass("invalid-label");
            }
            else {
                labels.currentPassword.removeClass("invalid-label");
                const passwordsMatch = inputs.newPassword.val() === inputs.newPasswordConfirm.val();
                if (passwordsMatch && inputs.newPassword[0].validity.valid) {
                    toggleButtons(button);
                    // Wylaczenie wszystkich pol hasla
                    [inputs.currentPassword, inputs.newPassword, inputs.newPasswordConfirm]
                        .forEach(disableField);
                    labels.passwordLegend.addClass("edited-label");
                    labels.newPassword.removeClass("invalid-label");
                }
                else {
                    labels.newPassword.addClass("invalid-label");
                }
            } // else
        } // else if Zapisz
    } // handlePasswordFields()

    const handleAboutMeField = (button) => {
        if (button.text() === "Zmień") {
            toggleButtons(button, false);
            enableField(inputs.aboutMe);
            labels.aboutMe.removeClass("edited-label");
        }
        else if (button.text() === "Zapisz") {
            disableField(inputs.aboutMe);
            toggleButtons(button);
            labels.aboutMe.addClass("edited-label");
        }
    }; // handleAboutMeField()

    const handleEditableField = (fieldInput, label, labelTextOriginal, button, type) =>{
        if (button.text() === "Zmień") {
            toggleButtons(button, false);
            enableField(fieldInput);
            label.removeClass("edited-label");

            // Zapisz oryginalna wartosc
            fieldInput.data("originalValue", fieldInput.val());

            if (type === "username" || type === "email") {
                fieldInput.on("blur", async function() {
                    await validateAndSave(fieldInput, label, labelTextOriginal, type)
                });
            } // if type username || email

        } // if button text Zmien
        else if (button.text() === "Zapisz" && editCorrect) {
            toggleButtons(button);
            disableField(fieldInput);
            label.addClass("edited-label").removeClass("invalid-label").removeClass("valid-label");
        } // else if button text Zapisz
    }; // handleEditableField()

    editButtons.on("click", function(e) {
        e.preventDefault();
        const fieldType = $(this).attr("name")
        if (fieldType === "username") {
            handleEditableField(
                inputs.username,
                labels.username,
                labels.username.text(),
                $(this),
                "username"
            );
        }
        else if (fieldType === "email") {
            handleEditableField(
                inputs.email,
                labels.email,
                labels.email.text(),
                $(this),
                "email"
            );
        }
        else if (fieldType === "password") {
            handlePasswordFields($(this));
        }
        else if (fieldType === "about_me") {
            handleAboutMeField($(this));
        }
    }); // editButtons click


    $(window).on("load", () => {
        allFormFields.each(function() {
           const field = $(this);
           field.on("invalid", (e) => {
               e.preventDefault();
               alert("blad");
           });
            // Zdarzenia 'input' i 'blur' - dynamiczne sprawdzanie poprawności
            // field.on("input blur", () => validateField(field));
        });
    }); // window load

    const checkAvailability = async (type, value) => {
        try {
            // Wyslanie zapytania GET do API za pomoca jQuery.ajax()
            // Zwracamy odpowiedz z serwera w formacie JSON
            return await $.ajax({
                url: `${SERVER_URI}/check-availability.php`,
                method: "GET",
                data: {
                    type: type, // typ zapytania
                    value: value // wartość do sprawdzenia
                },
                dataType: "json", // Oczekiwany format odpowiedzi
                headers: {
                    "Content-Type": "application/json"
                }
            });
        } catch (error) {
            console.error("Błąd podczas sprawdzania dostępności nazwy użytkownika:", error);
            return {
                success: false,
                message: "Wystąpił błąd podczas komunikacji z serwerem."
            };
        }
    }; // checkAvailability()

    const validateField = (field, label) => {
        const fieldName = field.attr("name");
        const labelTextOriginal = labelsOriginal[fieldName];
        if (!field[0].validity.valid) {
            fieldIsInvalid(field, label);
            if (field[0].validity.tooShort) {
                label.text(`${labelTextOriginal} (wartość za krótka)`);
            }
            else if (field[0].validity.valueMissing) {
                label.text(`${labelTextOriginal} (brak wartości)`);
            }
            else if (field[0].validity.typeMismatch) {
                label.text(`${labelTextOriginal} (niepoprawny format)`);
            }
            return false;
        }
        else {
            fieldIsValid(field, label)
            label.text(labelTextOriginal);
            return true;
        }
    }; // validateField()

    const fieldIsValid = (field, label) => {
        label.addClass("valid-label").removeClass("invalid-label");
        field.addClass("invalid-field").removeClass("valid-field");
        field.addClass("valid-input").removeClass("invalid-input")
        editCorrect = true;
    };

    const fieldIsInvalid = (field, label) => {
        label.removeClass("valid-label").addClass("invalid-label");
        field.removeClass("invalid-field").addClass("valid-field");
        field.removeClass("valid-input").addClass("invalid-input");
        editCorrect = false;
    };

    const fieldStyleReset = (field, label) => {
        field
            .removeClass("invalid-field")
            .removeClass("valid-input")
            .removeClass("invalid-input")
            .removeClass("valid-input");
        label
            .removeClass("invalid-label")
            .removeClass("valid-label");
        editCorrect = true;
    }
});