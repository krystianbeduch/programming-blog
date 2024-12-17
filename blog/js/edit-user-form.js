import {
    validateField, fieldStyleReset, fieldIsInvalid,
    fieldIsValid, toggleButtons, enableField, disableField
} from "./modules/validate-field-edit-form.js";

$(document).ready(() => {
    const form = $("#edit-user-form");

    // Referencje do pol formularza
    const inputs = {
        username: form.find("input[name='username']"),
        email: form.find("input[name='email']"),
        currentPassword: form.find("input[name='current-password']"),
        newPassword: form.find("input[name='new-password']"),
        newPasswordConfirm: form.find("input[name='new-password-confirm']"),
        about_me: form.find("textarea")
    };

    // Referencje do etykiet
    const labels = {
        username: form.find(`label[for="${inputs.username.attr('id')}"`),
        email: form.find(`label[for="${inputs.email.attr('id')}"`),
        currentPassword: form.find(`label[for="${inputs.currentPassword.attr('id')}"`),
        newPassword: form.find("label[for*='new-password']"),
        passwordLegend: form.find("fieldset[id*='password']").find("legend"),
        about_me: form.find(`label[for="${inputs.about_me.attr('id')}"`)
    };

    // Oryginalne tresci etykiet
    const labelsOriginal = {
        username: labels.username.text(),
        email: labels.email.text(),
    }

    let editCorrect = true; // Flaga poprawnej edycji pola

    const closeButtons = $("button[name*='close']");


    const editButtons = $(".edit-field-form-button");
    const SERVER_URI = "/US/blog/db/api";

    const validateAndSave = async (input, label, labelTextOriginal, type) => {
        const newValue = input.val();
        // console.log(input.data("originalValue"));
        // Sprawdzenie czy wartosc sie zmienila
        if (newValue !== input.data("originalValue")) {
            const fieldName = input.attr("name");
            const labelTextOriginal = labelsOriginal[fieldName];
            editCorrect = validateField(input, label, labelTextOriginal);
            if (editCorrect) {
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
                    editCorrect = false;
                }
            } // if validateField
        } // if newValue
        else {
            label.text(labelTextOriginal);
            fieldStyleReset(input, label);
            editCorrect = true;
        }
    }; // validateAndSave()

    const handlePasswordFields = (button, closeButton) => {
        if (button.text() === "Zmień") {
            toggleButtons(editButtons, button, closeButton, false);
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
                    toggleButtons(editButtons, button, closeButton);
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

    const handleAboutMeField = (button, closeButton) => {
        if (button.text() === "Zmień") {
            toggleButtons(editButtons, button, closeButton, false);
            enableField(inputs.about_me);
            labels.about_me.removeClass("edited-label");

            // Zapisz oryginalna wartosc
            inputs.about_me.data("originalValue", inputs.about_me.val());
        }
        else if (button.text() === "Zapisz") {
            disableField(inputs.about_me);
            toggleButtons(editButtons, button, closeButton);
            labels.about_me.addClass("edited-label");
        }
    }; // handleAboutMeField()

    const handleEditableField = (fieldInput, label, labelTextOriginal, button, type, closeButton) => {
        if (button.text() === "Zmień") {
            toggleButtons(editButtons, button, closeButton,false);
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
            toggleButtons(editButtons, button, closeButton);
            disableField(fieldInput);
            label.addClass("edited-label").removeClass("invalid-label").removeClass("valid-label");
            $("button[type='submit']").show();
        } // else if button text Zapisz
    }; // handleEditableField()

    editButtons.on("click", function(e) {
        e.preventDefault();
        const fieldType = $(this).attr("name");
        const closeButton = closeButtons.filter(`[name="close-${fieldType}"]`);
        if (fieldType === "username") {
            handleEditableField(
                inputs.username,
                labels.username,
                labels.username.text(),
                $(this),
                "username",
                closeButton
            );
        }
        else if (fieldType === "email") {
            handleEditableField(
                inputs.email,
                labels.email,
                labels.email.text(),
                $(this),
                "email",
                closeButton
            );
        }
        else if (fieldType === "password") {
            handlePasswordFields($(this), closeButton);
        }
        else if (fieldType === "about_me") {
            handleAboutMeField($(this), closeButton);
        }
    }); // editButtons click

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

    closeButtons.on("click", function() {
        closeButtons.hide();
        editButtons.show();
        editButtons.text("Zmień");

        const buttonName = $(this).attr("name");

        // Usuwamy 'close-' z nazwy przycisku
        const fieldName = buttonName.replace("close-", "");
        let input, label;
        if (fieldName === "password") {
            input = $("input[name*='password']");
            label = $("label[for*='password']");
        }
        else {
            input = inputs[fieldName];
            label = labels[fieldName];
        }

        disableField(input, false);
        fieldStyleReset(input, label);
        input.val(input.data("originalValue"));
    }); // closeButtons click
});