const SERVER_URI = "/US/blog/db/api";

export const selectors = {
    buttonDelete: $("button.delete-button"),
    buttonDeleteCancel: $("button#cancel-button"),
    buttonDeleteConfirm: $("button#confirm-button"),
    deleteModal: $("div.delete-modal")
};

export const showErrorAlert = (message) => {
    const alertDiv = $("<div>", {
        class: "alert alert-danger",
        html: `<strong>Błąd!</strong> ${message}`
    });
    $("header").prepend(alertDiv);
    window.scrollTo(0, 0);
};

export const showSuccessAlert = (message) => {
    // Przewijanie strony do gory
    window.scrollTo(0, 0);
    const alertDiv = $("<div>", {
        class: "alert alert-success",
        html: `<strong>Sukces!</strong> ${message}`
    });
    $("header").prepend(alertDiv);
};

export const checkAndShowSuccessAlert = () => {
    const successMessage = sessionStorage.getItem("successMessage");
    if (successMessage) {
        showSuccessAlert(successMessage);
        // Po wyswietleniu, usuwamy komunikat z sessionStorage
        sessionStorage.removeItem("successMessage");
    }
};

export const handleDelete  = (type, postCategory, id, modal, successCallback) => {
    $.ajax({
        url: `${SERVER_URI}/admin-management.php`,
        method: "DELETE",
        contentType: "application/json",
        data: JSON.stringify({type, id }),
        dataType: "json",
        success: (response) => {
            console.log(response);
            if (response.success) {
                let displayType = null;
                switch (type) {
                    case "user":
                        displayType = "Użytkownik";
                        break;
                    case "post":
                        displayType = "Post";
                        break;
                    case "comment":
                        displayType = "Komentarz";
                        break;
                }
                // Zapisujemy informacje o sukcesie w sessionStorage
                sessionStorage.setItem("successMessage", `${displayType} o numerze ${id} usunięty`);

                // Sprawdzamy z jakiej strony pochodzi zapytanie
                const currentPage = window.location.pathname;
                if (currentPage.includes("post.php")) {
                    // Jesli zapytanie pochodzi z post.php, to przekierowujemy do kategorii
                    if (postCategory) {
                        window.location.href = `../pages/${postCategory}.php`;
                    }
                }
                else {
                    successCallback();
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
        modal.fadeOut();
    }); // ajax
};