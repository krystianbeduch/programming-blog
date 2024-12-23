import {
    selectors, checkAndShowSuccessAlert, handleDelete
} from "./modules/utils.js";

$(document).ready(() => {
    const viewContentButton = $("button.view-button");
    const contentModal = $("div#preview-container");
    const contentModalCloseButton = contentModal.find("button.close");


    viewContentButton.on("click", function() {
        const postContent = $(this).closest("tr").data("post-content");
        if (postContent) {
            $("#preview-content").text(postContent);
            $("#preview-container").fadeIn();
        }
    });

    contentModalCloseButton.on("click", () => {
        $("#preview-container").fadeOut();
    });

    const { buttonDelete, buttonDeleteCancel, buttonDeleteConfirm, deleteModal } = selectors;
    const typeSpan = deleteModal.find("span");
    let currentId = null, deleteType = null, postCategory = null;

    // Usuwanie posta
    buttonDelete.on("click", function() {
        currentId = $(this).data("post-id");
        if (currentId) {
            deleteType = "post";
            postCategory = $(this).data("category-name").toLowerCase();

        }
        else {
            currentId = $(this).data("comment-id");
            if (currentId) {
                deleteType = "comment";
            }
        }
        if (typeSpan) {
            typeSpan.text(deleteType);
        }
        deleteModal.css("display", "flex").hide().fadeIn();
    });

    buttonDeleteCancel.on("click", () => {
        currentId = null;
        deleteType = null;
        postCategory = null;
        deleteModal.show().fadeOut();
    });

    buttonDeleteConfirm.on("click", function() {
        if (currentId) {
            handleDelete(deleteType, postCategory, currentId, deleteModal, () => location.reload());
        }
    });

    checkAndShowSuccessAlert();
});