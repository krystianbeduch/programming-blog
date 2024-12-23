import {
    selectors, checkAndShowSuccessAlert, handleDelete
} from "./modules/utils.js";

$(document).ready(() => {
    const table = $("#user-posts-stats");
    const buttonToggle = $("button.toggle-stats-table");

    const { buttonDelete, buttonDeleteCancel, buttonDeleteConfirm, deleteModal } = selectors;

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

    buttonDelete.on("click", function() {
        currentPostId = $(this).data('post-id'); // Pobierz ID posta
        deleteModal.css('display', 'flex').hide().fadeIn();
    });

    buttonDeleteCancel.on("click", () =>{
        currentPostId = null;
        deleteModal.show().fadeOut();
    });

    buttonDeleteConfirm.on("click", function() {
        if (currentPostId) {
            handleDelete("post", currentPostId, deleteModal, () => location.reload());
        }
    }); // buttonDeletePostConfirm click

    checkAndShowSuccessAlert();
});