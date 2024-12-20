import {
    showErrorAlert, showSuccessAlert,
    checkAndShowSuccessAlert, handleDelete
} from "./modules/delete-utils.js";

$(document).ready(() => {
    const table = $("#user-posts-stats");
    const buttonToggle = $("button.toggle-stats-table");
    const buttonDeletePost = $("button.delete-button");
    const buttonDeletePostCancel = $("button#cancel-button");
    const buttonDeletePostConfirm = $("button#confirm-button")
    const deleteModal = $("div#delete-post-modal");
    const SERVER_URI = "/US/blog/db/api";

    let currentPostId;

    buttonToggle.on("click", function() {
        // Sprawdz czy przycisk zawiera slowa 'Pokaz'
        if ($(this).text().includes("Pokaż")) {
            table.show();
            $(this).text("Ukryj tabele ze statystykami");
        }
        else if ($(this).text().includes("Ukryj")) {
            table.hide();
            $(this).text("Pokaż tabele ze statystykami");
        }
    });

    buttonDeletePost.on("click", function() {
        currentPostId = $(this).data('post-id'); // Pobierz ID posta
        deleteModal.css('display', 'flex').hide().fadeIn();
    });

    buttonDeletePostCancel.on("click", () =>{
        currentPostId = null;
        deleteModal.show().fadeOut();
    });

    buttonDeletePostConfirm.on("click", function() {
        if (currentPostId) {
            handleDelete("post", currentPostId, deleteModal, () => location.reload());
        }
    }); // buttonDeletePostConfirm click

    // const showErrorAlert = (message) => {
    //     const alertDiv = $("<div>", {
    //         class: "alert alert-danger",
    //         html: `<strong>Błąd!</strong> ${message}`
    //     });
    //     $("header").prepend(alertDiv);
    // }


    // const showSuccessAlert = (message) => {
    //     const alertDiv = $("<div>", {
    //         class: "alert alert-success",
    //         html: `<strong>Sukces!</strong> ${message}`
    //     });
    //     $("header").prepend(alertDiv);
    // };

    // // Sprawdzamy, czy jest zapisany komunikat o sukcesie
    // const successMessage = sessionStorage.getItem("successMessage");
    // if (successMessage) {
    //     showSuccessAlert(successMessage);
    //     // Po wyswietleniu, usuwamy komunikat z sessionStorage
    //     sessionStorage.removeItem("successMessage");
    // }
    checkAndShowSuccessAlert();
});