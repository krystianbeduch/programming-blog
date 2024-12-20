$(document).ready(() => {
    const table = $("#admin-users-tab");
    const aboutMeTd = $("td.about-me-col");
    const aboutMeModalCloseButton = $("button.close-preview-button");

    aboutMeTd.on("click", function() {
        const aboutMeFullVal = $(this).data("about-me");
        $("#preview-content").text(aboutMeFullVal);
        $("#preview-container").fadeIn();
    });

    aboutMeModalCloseButton.on("click", () => {
        $("#preview-container").fadeOut();
    });
});