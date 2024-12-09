$(document).ready(() => {
    const form = $("#edit-user-form");
    // const form1 = document.forms.edit_user_form;
    // console.log(form1.username);
    const usernameInput = form.find("input[name='username']");
    const currentUsername = usernameInput.val();
    const labelUsername = usernameInput.prev();
    const labelUsernameText = labelUsername.text();
    const button = $("button");
    const SERVER_URI = "/US/blog/db/api";


    $(window).on("load", () => {
        const allFormFields = $("input, textarea");
        allFormFields.each(function() {
           const field = $(this);
           field.on("invalid", (e) => {
               e.preventDefault();
               alert("blad");
           });
            // Zdarzenia 'input' i 'blur' - dynamiczne sprawdzanie poprawności
            field.on("input blur", () => validateField(field));
        });
        usernameInput.on("input blur", async function() {
            const username = $(this).val();
            // console.log(username);

            if (username.length > 2) { // Sprawdzamy tylko dla nazw dłuższych niż 2 znaki
                const result = await checkAvailability("username", username);

                /* ECMAScript 2020 wprowadzil opcjonalny lancuch dostepu
                (Optional Chaining Operator). Jesli result bedzie null lub undefined operator zwroci undefined
                */
                if (result?.success) {
                    labelUsername.text(labelUsernameText);
                    fieldIsValid($(this));
                }
                else {
                    labelUsername.text(`${labelUsernameText} (nazwa zajęta)`);
                    fieldIsInvalid($(this));
                }
            }
        });

        // button.on("click", function(e) {
        //     e.preventDefault();
        // });
    });





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
            console.log(field)
            fieldIsInvalid(field);
        }
        else {
            fieldIsValid(field)
        }
    };

    const fieldIsValid = (field) => {
        const label = field.prev();
        label.css("color", "var(--primary-color)");
        // console.log($(this));
        field.css("border-color", "var(--primary-color)");
        field.removeClass("invalid-input");
        field.addClass("valid-input");
    };

    const fieldIsInvalid = (field) => {
        const label = field.prev();
        label.css("color", "var(--error-text)");
        // console.log($(this));
        field.css("border-color", "var(--error-text)");
        field.addClass("invalid-input");
        field.removeClass("valid-input");
    };


    // console.log(usernameInput);



});