import {
    selectors, checkAndShowSuccessAlert, handleDelete, showErrorAlert, showSuccessAlert
} from "./modules/utils.js";

import {
    checkAvailability
} from "./modules/user-availability.js";

$(document).ready(() => {
    const SERVER_URI = "/US/blog/db/api";
    const adminId = parseInt($("#admin-id").text());

    const aboutMeTd = $("td.about-me-col");
    const aboutMeModalCloseButton = $("button.close-preview-button");

    const isActiveTd = $("td.is-active-col");
    const changeUserActivityModal = $("div#change-user-activity-modal");
    const buttonChangeActivityCancel = changeUserActivityModal.find("button#cancel-activity-button");
    const buttonChangeActivityConfirm = changeUserActivityModal.find("button#confirm-activity-button");

    const editModal = $("div#edit-user-modal");
    const buttonEdit = $("button.edit-user-button");
    const buttonEditCancel = editModal.find("button#cancel-edit-button");
    const buttonEditConfirm = editModal.find("button#confirm-edit-button");
    const editForm = editModal.find("form");
    const usernameAndEmailField = editForm.find("input[name='username'], input[name='email']");


    const { buttonDelete, buttonDeleteCancel, buttonDeleteConfirm, deleteModal } = selectors;

    let currentUserId;
    let isActiveVal;

    let isFormValid = true;
    let isAvailability = true;
    let isPasswordValid = true;

    // Podglad "O mnie"
    aboutMeTd.on("click", function() {
        const aboutMeFullVal = $(this).data("about-me");
        if (aboutMeFullVal) {
            $("#preview-content").text(aboutMeFullVal);
            $("#preview-container").fadeIn();
        }
    });

    aboutMeModalCloseButton.on("click", () => {
        $("#preview-container").fadeOut();
    });

    isActiveTd.each((index, element) => {
        const cell = $(element);
        const row = cell.closest("tr");
        const value = cell.text().trim().toLowerCase();
        if (value === "nieaktywne") {
            row.css("color", "var(--delete-text)");
        }
    });

    // Zmiana aktywnosci uzytkownika
    isActiveTd.on("click", function() {
        isActiveVal = $(this).data("is-active");
        currentUserId = $(this).closest("tr").data("user-id"); // Pobierz ID uzytkownika
        if (currentUserId === adminId) {
            alert("Nie możesz zmienić aktywności swojego konta.");
        }
        else {
            const isActiveText = isActiveVal === 1 ? "Nieaktywne" : "Aktywne";
            const changeUserActivityTxt = `Czy chcesz zmienić aktywność konta o id ${currentUserId} na \"${isActiveText}\"`;
            changeUserActivityModal.find("p").text(changeUserActivityTxt);
            changeUserActivityModal.css("display", "flex").hide().fadeIn();
        }
    });

    buttonChangeActivityCancel.on("click", () => {
        isActiveVal = null;
        currentUserId = null;
        changeUserActivityModal.show().fadeOut();
    });

    buttonChangeActivityConfirm.on("click", () => {
        if (currentUserId) {
            $.ajax({
                url: `${SERVER_URI}/admin-management.php`,
                method: "PATCH",
                contentType: "application/json",
                data: JSON.stringify({
                    userId: currentUserId,
                    activity: isActiveVal
                }),
                dataType: "json",
                success: (response) => {
                    console.log(response);
                    if (response.success) {
                        sessionStorage.setItem("successMessage", response.message);
                        // Odswiezenie strony
                        location.reload();
                    }
                    else {
                        showErrorAlert(response.message);
                    }
                },
                error: (xhr, status, error) => {
                    console.error("AJAX request failed:", status, error);  // Logujemy status i blad zapytania
                    console.error("Response Text:", xhr.responseText);  // Logujemy tresc odpowiedzi serwera
                    const errorMessage = JSON.parse(xhr.responseText);
                    if (errorMessage.message) {
                        showErrorAlert(errorMessage.message);
                    }
                    else {
                        showErrorAlert(xhr.responseText);
                    }
                }
            }).always(() => {
                changeUserActivityModal.show().fadeOut();
            }); // ajax
        } // if currentUserId
    }); // buttonChangeActivityConfirm click

    const passwordCheckbox = editForm.find("input[type='checkbox']");
    const password = editForm.find("input#e-password");
    const passwordConfirm = editForm.find("input#e-password-confirm");

    // Edycja uzytkownika - pokazanie formularza
    buttonEdit.on("click", function() {
        // Pobierz ID uzytkownika z atrybutu data-user-id w najblizszym wierszu tabeli
        const userId = $(this).closest("tr").data('user-id');

        // Uzupelnienie formularza danymi wybranego uzytkownika
        const username = $(this).closest("tr").find("td.user-stats-username").text();
        const email = $(this).closest("tr").find("td.user-stats-email").text();
        const aboutMe = $(this).closest("tr").find("td.user-stats-about-me").text();
        const role = $(this).closest("tr").find("td.user-stats-role").text();
        editForm.find("input#e-id").val(userId).data("originalValue", userId);
        editForm.find("input#e-username").val(username).data("originalValue", username);
        editForm.find("input#e-email").val(email).data("originalValue", email);
        editForm.find("textarea#e-about-me").val(aboutMe).data("originalValue", aboutMe);

        if (role.toLowerCase() === "admin") {
            // Zaznacz radio dla roli admin
            editForm.find("input#e-role-admin").prop("checked", true);
        }
        else {
            // Zaznacz radio dla roli user
            editForm.find("input#e-role-user").prop("checked", true);
        }

        // Wstepna walidacja wszystkich pol - nadanie kolorow bootstrapa
        editForm.find("input").each(function() {
            fieldValidation($(this));
        });

        // Reset klasy walidacji dla pol hasla
        editModal.find("input[type='password']").removeClass("is-invalid").removeClass("is-valid");


        passwordCheckbox.prop("checked", false); // Reset checkoxa zmiany hasla
        passwordInputsDisable();
        editModal.css("display", "flex").hide().fadeIn();
    });

    const passwordInputsDisable = () => {
        if (passwordCheckbox.is(":checked")) {
            // Jesli checkbox zaznaczony, wlaczamy pola hasla
            editForm
                .find("input[type='password']")
                .removeAttr("disabled")
                .attr("required", true)
                .addClass("is-invalid")
                .siblings(".invalid-tooltip")
                .hide();
            isPasswordValid = false;
            lockOrUnlockConfirmButton();
        }
        else {
            editForm.find("input[type='password']").removeAttr("required").attr("disabled", true).val("").removeClass("is-invalid").removeClass("is-valid");
            isPasswordValid = true;
            lockOrUnlockConfirmButton();
        }
    }

    passwordCheckbox.on("change", () => {
        passwordInputsDisable();
    });

    buttonEditCancel.on("click", () => {
        currentUserId = null;
        editModal.show().fadeOut();
    });

    buttonEditConfirm.on("click", async function () {

        if (isFormValid && isPasswordValid) {

            // Sprawdzamy dostepnosc nazwy uzytkownika i email
            if (await checkFieldAvailability()) {
                let requestData = {
                    id: editForm.find("input#e-id").val(),
                    username: editForm.find("input#e-username").val(),
                    email: editForm.find("input#e-email").val(),
                    role: editForm.find("input[name='role']:checked").val()
                };
                const aboutMe = editForm.find("textarea").val();
                if (aboutMe) {
                    requestData.aboutMe = aboutMe;
                }
                const password = editForm.find("input[name='password']").val();
                const passwordConfirm = editForm.find("input[name='passwordConfirm']").val();
                if (password && password === passwordConfirm) {
                    requestData.password = password;
                }

                $.ajax({
                    // url: `${SERVER_URI}/edit-user-endpoint.php`,
                    url: `${SERVER_URI}/admin-management.php`,
                    method: "PATCH",
                    contentType: "application/json",
                    data: JSON.stringify(requestData),
                    dataType: "json",
                    success: (response) => {
                        console.log(response);
                        if (response.success) {
                            if (parseInt(requestData.id) === adminId) {
                                showSuccessAlert("Zaktualizowałeś swoje konto<br>Za chwile nastąpi wylogowanie");
                                setTimeout(() => {
                                    location.href = "../includes/logout.php";
                                 }, 5000);
                            }
                            else {
                                sessionStorage.setItem("successMessage", response.message);
                                // Odswiezenie strony
                                location.reload();
                            }
                        }
                        else {
                            showErrorAlert(response.message);
                        }
                    },
                    error: (xhr, status, error) => {
                        console.error("AJAX request failed:", status, error);  // Logujemy status i blad zapytania
                        console.error("Response Text:", xhr.responseText);  // Logujemy tresc odpowiedzi serwera
                        const errorMessage = JSON.parse(xhr.responseText);
                        if (errorMessage.message) {
                            showErrorAlert(errorMessage.message);
                        }
                        else {
                            showErrorAlert(xhr.responseText);
                        }
                    }
                    }).always(() => {
                        editModal.show().fadeOut();
                    }); // ajax
            } // if checkAvailability
        }
    });
    const checkFieldAvailability = async () => {
        let isUsernameAvailable = true;
        let isEmailAvailable = true;

        // Mapa obietnic
        const promises = usernameAndEmailField.map(async function() {
            const field = $(this);
            const type = field.attr("name");

            // Jesli wartosc pola sie zmienia, sprawdzamy dostepnosc
            if (field.val() !== field.data("originalValue")) {
                const result = await checkAvailability(type, field.val());
                if (result?.success) {
                    field.addClass("is-valid").removeClass("is-invalid");
                    if (type === "username") {
                        isUsernameAvailable = true;
                    }
                    else if (type === "email") {
                        isEmailAvailable = true;
                    }
                }
                else {
                    field.addClass("is-invalid").removeClass("is-valid");
                    const typeTxt = type === "username" ? "Nazwa użytkownika zajęta" : "Email zajęty";
                    field.siblings(".invalid-tooltip").text(typeTxt);
                    if (type === "username") {
                        isUsernameAvailable = false;
                    }
                    else if (type === "email") {
                        isEmailAvailable = false;
                    }
                }
            }
        });
        // Czekamy na zakonczenie wszystkich operacji asynchronicznych
        await Promise.all(promises);

        return isUsernameAvailable && isEmailAvailable;
    };

    editForm.find("input").on("input", function () {
        fieldValidation($(this));
    });

    editForm.find("input[type='password']").on("input", function() {
        if (password.val() && passwordConfirm.val()) {
            passwordValidate();
        }
    });

    const validateForm = () => {
        // Walidacja formularza pol username i email
        isFormValid = editForm
            .find("input[name='username'], input[name='email']")
            .toArray().every(field => field.checkValidity());
    };

    const fieldValidation = (field) => {
        // Walidacja pojedynczego pola
        if (!field[0].validity.valid) {
            field.addClass("is-invalid").removeClass("is-valid");
            if (field[0].validity.tooShort) {
                // Walidacja username - minlength
                field.siblings(".invalid-tooltip").text("Minimum 6 znaków");
            }
            else if (field[0].validity.valueMissing) {
                // Walidacja obu pol - required
                field.siblings(".invalid-tooltip").text("Brak wartości");
            }
            else if (field[0].validity.typeMismatch) {
                // Walidacja email - type=email
                field.siblings(".invalid-tooltip").text("Nie poprawny format email");
            }
            validateForm();
        }
        else {
            field.removeClass("is-invalid").addClass("is-valid");
            validateForm();
        }
        lockOrUnlockConfirmButton();
    }; // fieldValidation

    const passwordValidate = () => {
        if (passwordCheckbox.is(":checked")) {
            // Jesli zmiana hasla jest aktywna
            if (password.val() !== "" && passwordConfirm.val() !== "") {
                // Jesli oba hasla sa wpisane
                if (password.val() !== passwordConfirm.val()) {
                    // Jesli hasla sie nie zgadzaja
                    $([password, passwordConfirm]).each(function() {
                        $(this).addClass("is-invalid").removeClass("is-valid");
                        $(this).siblings(".invalid-tooltip").text("Hasła muszą się zgadzać")
                    });
                    isPasswordValid = false;
                }
                else {
                    $([password, passwordConfirm]).each(function() {
                        $(this).removeClass("is-invalid").addClass("is-valid");
                    });

                    isPasswordValid = true;
                }
            } // if password.val() !== "" && passwordConfirm.val() !== ""
            else {
                isPasswordValid = false;
            }
        } // if passwordCheckbox.is(":checked")
        else {
            isPasswordValid = true;
        }
        lockOrUnlockConfirmButton();
    }; // passwordValidate()

    const lockOrUnlockConfirmButton = () => {
        if (isFormValid && isPasswordValid && isAvailability) {
            buttonEditConfirm.removeAttr("disabled").css("background-color", "var(--button-background)");
        }
        else {
            buttonEditConfirm.attr("disabled", true).css("background-color", "var(--delete-text)");
        }
    }; // lockOrUnlockConfirmButton()

    // Usuwanie uzytkownika
    buttonDelete.on("click", function() {
        currentUserId = $(this).closest("tr").data('user-id'); // Pobierz ID uzytkownika
        deleteModal.css("display", "flex").hide().fadeIn();
    });

    buttonDeleteCancel.on("click", () => {
        currentUserId = null;
        deleteModal.show().fadeOut();
    });

    buttonDeleteConfirm.on("click", function() {
        if (currentUserId) {
            handleDelete("user", currentUserId, deleteModal, () => location.reload());
        }
    });

    checkAndShowSuccessAlert();
});