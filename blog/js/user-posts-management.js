$(document).ready(() => {
    const table = $("table.user-posts-stats");
    const buttonToggle = $("button.toggle-stats-table");
    const buttonDeletePost = $("button.delete-post-button");
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
        console.log(currentPostId);
        deleteModal.css('display', 'flex').hide().fadeIn();
    });

    buttonDeletePostCancel.on("click", () =>{
        currentPostId = null;
        console.log(currentPostId);
        deleteModal.show().fadeOut();
    });

    buttonDeletePostConfirm.on("click", function() {
        if (currentPostId) {
            // try {
                $.ajax({
                    url: `${SERVER_URI}/delete-user-post.php`,
                    method: "DELETE",
                    contentType: "application/json",  // Upewnij się, że jest ustawiony odpowiedni nagłówek
                    data: JSON.stringify({
                        postId: currentPostId
                    }),
                    dataType: "json",
                    // headers: {
                    //     "Content-Type": "application/json"
                    // },
                    success: (response) => {
                        console.log(response);
                        if (response.success) {
                            // Zapisujemy informacje o sukcesie w sessionStorage
                            sessionStorage.setItem("successMessage", `Post o numerze ${currentPostId} usunięty.`);
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
                }); // ajax
        } // if currentPostId
        deleteModal.show().fadeOut();
    }); // buttonDeletePostConfirm click

    const showErrorAlert = (message) => {
        const alertDiv = $("<div>", {
            class: "alert alert-danger",
            html: `<strong>Błąd!</strong> ${message}`
        });
        $("header").prepend(alertDiv);
    }


    const showSuccessAlert = (message) => {
        const alertDiv = $("<div>", {
            class: "alert alert-success",
            html: `<strong>Sukces!</strong> ${message}`
        });
        $("header").prepend(alertDiv);
    };

    // Sprawdzamy, czy jest zapisany komunikat o sukcesie
    const successMessage = sessionStorage.getItem("successMessage");
    if (successMessage) {
        showSuccessAlert(successMessage);
        // Po wyswietleniu, usuwamy komunikat z sessionStorage
        sessionStorage.removeItem("successMessage");
    }
});