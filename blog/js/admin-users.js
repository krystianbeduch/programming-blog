import {
    // showErrorAlert, showSuccessAlert,
    checkAndShowSuccessAlert, handleDelete
} from "./modules/delete-utils.js";

$(document).ready(() => {
    const table = $("#admin-users-tab");
    const aboutMeTd = $("td.about-me-col");
    const aboutMeModalCloseButton = $("button.close-preview-button");
    const buttonDeleteUser = $("button.delete-button");
    const deleteModal = $(".delete-modal");
    const buttonDeleteUserCancel = $("button#cancel-button");
    const buttonDeleteUserConfirm = $("button#confirm-button")
    const SERVER_URI = "/US/blog/db/api";
    let currentUserId;

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

    buttonDeleteUser.on("click", function() {
        currentUserId = $(this).data('user-id'); // Pobierz ID uzytkownika
        deleteModal.css('display', 'flex').hide().fadeIn();
    });

    buttonDeleteUserCancel.on("click", () =>{
        currentUserId = null;
        console.log(currentUserId);
        deleteModal.show().fadeOut();
    });

    buttonDeleteUserConfirm.on("click", function() {
        if (currentUserId) {
            handleDelete("user", currentUserId, deleteModal, () => location.reload());
        }
    });
            // try {
        //     $.ajax({
        //         url: `${SERVER_URI}/delete-endpoint.php`,
        //         method: "DELETE",
        //         contentType: "application/json",
        //         data: JSON.stringify({
        //             type: "user",
        //             id: currentUserId
        //         }),
        //         dataType: "json",
        //         // headers: {
        //         //     "Content-Type": "application/json"
        //         // },
        //         success: (response) => {
        //             console.log(response);
        //             if (response.success) {
        //                 // Zapisujemy informacje o sukcesie w sessionStorage
        //                 sessionStorage.setItem("successMessage", `Użytkownik o numerze ${currentUserId} usunięty.`);
        //                 location.reload();
        //             }
        //             else {
        //                 showErrorAlert(response.message);
        //             }
        //         },
        //         error: (xhr, status, error) => {
        //             console.error("AJAX request failed:", status, error);  // Logujemy status i blad zapytania
        //             console.error("Response Text:", xhr.responseText);  // Logujemy tresc odpowiedzi serwera
        //             const errorMessage = JSON.parse(xhr.responseText);
        //             if (errorMessage.message) {
        //                 showErrorAlert(errorMessage.message);
        //             }
        //             else {
        //                 showErrorAlert(xhr.responseText);
        //             }
        //         }
        //     }); // ajax
        // } // if currentPostId
        // deleteModal.show().fadeOut();
    // }); // buttonDeletePostConfirm click

    // const showErrorAlert = (message) => {
    //     const alertDiv = $("<div>", {
    //         class: "alert alert-danger",
    //         html: `<strong>Błąd!</strong> ${message}`
    //     });
    //     $("header").prepend(alertDiv);
    // }
    //
    //
    // const showSuccessAlert = (message) => {
    //     const alertDiv = $("<div>", {
    //         class: "alert alert-success",
    //         html: `<strong>Sukces!</strong> ${message}`
    //     });
    //     $("header").prepend(alertDiv);
    // };
    //
    // // Sprawdzamy, czy jest zapisany komunikat o sukcesie
    // const successMessage = sessionStorage.getItem("successMessage");
    // if (successMessage) {
    //     showSuccessAlert(successMessage);
    //     // Po wyswietleniu, usuwamy komunikat z sessionStorage
    //     sessionStorage.removeItem("successMessage");
    // }
    checkAndShowSuccessAlert();
});