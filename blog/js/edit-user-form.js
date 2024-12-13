$(document).ready(() => {
    const form = $("#edit-user-form");
    // const form1 = document.forms.edit_user_form;
    // console.log(form1.username);
    const usernameInput = form.find("input[name='username']");
    const labelUsername = form.find(`label[for="${usernameInput.attr('id')}"`);
    const labelUsernameTextOriginal = labelUsername.text();

    const emailInput = form.find("input[name='email']");
    const labelEmail = form.find(`label[for="${emailInput.attr('id')}"`);
    const labelEmailTextOriginal = labelEmail.text();

    const currentPasswordInput = form.find("input[name='current-password']");
    const newPasswordInput = form.find("input[name='new-password']");
    const newPasswordConfirmInput = form.find("input[name='new-password-confirm']");
    const labelPassword = form.find(`label[for="${newPasswordConfirmInput.attr('id')}`);
    const labelPasswordOriginal = labelPassword.text();

    let editCorrect = true;



    const allFormFields = $("input, textarea");

    // const email
    const editButtons = $(".edit-profile-button");
    const SERVER_URI = "/US/blog/db/api";

    const handleEditableField = (fieldInput, label, labelTextOriginal, button, type) =>{
        // Jesli pole jest zablokowane i przycisk to 'Zmien'
        if (button.text() === "Zmień") {
            // Schowaj pozostale przyciski
            editButtons.each(function() {
                $(this).hide();
            });
            // Pokaz obecny przycisk i zmien jego nazwe
            button.show();
            button.text("Zapisz");

            // Odblkuj je, ustaw wlasciwosc required i ustaw focus
            fieldInput.removeAttr("disabled");
            fieldInput.removeAttr("readonly");
            fieldInput.attr("required", "true");
            fieldInput.focus();

            label.removeClass("edited-label");
            

            // Odczytaj poprzednia wartosc, wartosc domyslna usera
            const previousValue = fieldInput.val();
            if (type === "username" || type === "email") {
                fieldInput.on("blur", async function() {
                    const newValue = fieldInput.val();
                    if (newValue !== previousValue) {
                        if (validateField(fieldInput)) {
                            const result = await checkAvailability(type, newValue);
                            /* ECMAScript 2020 wprowadzil opcjonalny lancuch dostepu
                            (Optional Chaining Operator). Jesli result bedzie null lub undefined operator zwroci undefined
                            */
                            if (result?.success) {
                                label.text(labelTextOriginal);
                                fieldIsValid(fieldInput);
                            }
                            else {
                                label.text(`${labelTextOriginal} (nazwa zajęta)`);
                                fieldIsInvalid(fieldInput);
                            }
                        }
                    }
                    else {
                        label.text(labelTextOriginal);
                        fieldStyleRest($(this));
                    }
                }); // fieldInput blut
            } // if type username || email
            else if (type === "password") {
                handlePasswordFields();
            }


        } // if button text Zmien
        else if (button.text() === "Zapisz" && editCorrect) {
        // else if ($(this).text() === "Zapisz") {
                // Pokaz pozostale przyciski
            editButtons.each(function() {
                $(this).show();
            });
            // Zmien typ inputa na readonly
            fieldInput.attr("readonly", "true");
            // Pokaz obecny przycisk i zmien jego nazwe
            // $(this).show();
            button.text("Zmień");
            label.addClass("edited-label");
        } // else if button text Zapisz

            // usernameButton.attr("disabled");
            // usernameInput.removeAttr("required");

    }; // handleEditableField()

    const handlePasswordFields = () => {
        const newPassword = newPasswordInput.text();
        const newPasswordConfirm = newPasswordConfirmInput.text();
        if (newPassword === newPasswordConfirm) {
            alert("git");
        }
        else {
            alert("nie git");
        }
    };

    editButtons.on("click", function(e) {
        e.preventDefault();
        if ($(this).attr("name") === "username") {
            handleEditableField(
                usernameInput,
                labelUsername,
                labelUsernameTextOriginal,
                $(this),
                "username"
            );
        }
        else if ($(this).attr("name") === "email") {
            handleEditableField(
                emailInput,
                labelEmail,
                labelEmailTextOriginal,
                $(this),
                "email"
            );
        }
        else if ($(this).attr("name") === "password") {
            handleEditableField(
                emailInput,
                labelEmail,
                labelEmailTextOriginal,
                $(this),
                "password"
            );
        }
    });



    // usernameButton.on("click", function(e) {
    //     e.preventDefault();
    //     if (usernameInput.attr("disabled")) {
    //         usernameInput.removeAttr("disabled");
    //         usernameInput.attr("required", "true");
    //         usernameInput.focus();
    //         const previousValue = usernameInput.val();
    //         $(this).hide();
    //
    //         usernameInput.on("blur", async function() {
    //             const username = $(this).val();
    //             if (username !== previousValue) {
    //                 if (validateField($(this))) {
    //                     const result = await checkAvailability("username", username);
    //                     /* ECMAScript 2020 wprowadzil opcjonalny lancuch dostepu
    //                     (Optional Chaining Operator). Jesli result bedzie null lub undefined operator zwroci undefined
    //                     */
    //                     if (result?.success) {
    //                         labelUsername.text(labelUsernameTextOriginal);
    //                         fieldIsValid($(this));
    //                     }
    //                     else {
    //                         labelUsername.text(`${labelUsernameTextOriginal} (nazwa zajęta)`);
    //                         fieldIsInvalid($(this));
    //                     }
    //                 }
    //             }
    //             else {
    //                 labelUsername.text(labelUsernameTextOriginal);
    //                 fieldStyleRest($(this));
    //             }
    //
    //         });
    //     }
    //     else {
    //         usernameButton.attr("disabled");
    //         usernameInput.removeAttr("required");
    //     }
    // });

    const unlockUsernameInput = () => {

    };


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
    });

    // allFormFields.on("input", async function(event) {
    //     console.log("Input fired on: ", event.target);
    //     // console.log($(this));
    //     const previousValue = $(this).data("previousValue");
    //     const value = $(this).val();
    //     if (previousValue !== value && validateField($(this))) {
    //         let type;
    //         if ($(this).attr("name") === "username") {
    //             type = "username";
    //             // console.log("username");
    //         }
    //         else if ($(this).attr("name") === "email") {
    //             type = "email";
    //             // console.log("email");
    //         }
    //         const result = await checkAvailability(type, value);
    //
    //         /* ECMAScript 2020 wprowadzil opcjonalny lancuch dostepu
    //         (Optional Chaining Operator). Jesli result bedzie null lub undefined operator zwroci undefined
    //         */
    //         if (result?.success) {
    //             if (type === "username") {
    //                 labelUsername.text(labelUsernameText);
    //             }
    //             else if (type === "email") {
    //                 labelEmail.text(labelEmailText);
    //             }
    //             fieldIsValid($(this));
    //         }
    //         else {
    //             if (type === "username") {
    //                 labelUsername.text(`${labelUsernameText} (nazwa zajęta)`);
    //             }
    //             else if (type === "email") {
    //                 labelEmail.text(`${labelEmailText} (email zajęty)`);
    //             }
    //             fieldIsInvalid($(this));
    //         }
    //     }
    // });

        // usernameInput.on("input blur", async function() {
        //     const username = $(this).val();
        //     const result = await checkAvailability("username", username);
        //
        //     /* ECMAScript 2020 wprowadzil opcjonalny lancuch dostepu
        //     (Optional Chaining Operator). Jesli result bedzie null lub undefined operator zwroci undefined
        //     */
        //     if (result?.success) {
        //         labelUsername.text(labelUsernameText);
        //         fieldIsValid($(this));
        //     }
        //     else {
        //         labelUsername.text(`${labelUsernameText} (nazwa zajęta)`);
        //         fieldIsInvalid($(this));
        //     }
        // });



        // button.on("click", function(e) {
        //     e.preventDefault();
        // });




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
    };

    const validateField = (field) => {
        if (!field[0].validity.valid) {
            // console.log("inv");
            fieldIsInvalid(field);
            console.log(field[0].validity.tooShort);
            if (field[0].validity.tooShort) {
                labelUsername.text(`${labelUsernameTextOriginal} (wartość za krótka)`);
            }
            else if (field[0].validity.valueMissing) {
                labelUsername.text(`${labelUsernameTextOriginal} (brak wartości)`);
            }
            return false;
        }
        else {
            // console.log("va");
            fieldIsValid(field)
            return true;
        }
    };

    const fieldIsValid = (field) => {
        const label = form.find(`label[for="${usernameInput.attr('id')}"`);
        // label.css("color", "var(--primary-color)");
        label.addClass("valid-label");
        label.removeClass("invalid-label");

        field.addClass("invalid-field");
        field.removeClass("valid-field");
        // console.log($(this));
        // field.css("border-color", "var(--primary-color)");
        field.removeClass("invalid-input");
        field.addClass("valid-input");
        editCorrect = true;
    };

    const fieldIsInvalid = (field) => {
        const label = form.find(`label[for="${usernameInput.attr('id')}"`);
        // label.css("color", "var(--error-text)");
        label.removeClass("valid-label");
        label.addClass("invalid-label");
        // console.log($(this));
        // field.css("border-color", "var(--error-text)");
        field.removeClass("invalid-field");
        field.addClass("valid-field");

        field.addClass("invalid-input");
        field.removeClass("valid-input");
        editCorrect = false;
    };

    const fieldStyleRest = (field) => {
        const label = form.find(`label[for="${usernameInput.attr('id')}"`);
        // label.css("color", "#000");
        // console.log($(this));
        // field.css("border-color", "#000");
        field.removeClass("invalid-field");
        field.removeClass("valid-input");
        field.removeClass("invalid-label");
        field.removeClass("valid-label");
        field.removeClass("invalid-input");
        field.removeClass("valid-input");
        editCorrect = true;
    }
});