$(document).ready(() => {
    const userMenuDiv = $("#user-menu");

    if (userMenuDiv.length) {
        const dropdownMenu = $("#dropdown-menu");

        userMenuDiv.on("click", () => {
            if (dropdownMenu.is(":visible")) {
                dropdownMenu.fadeOut();
            }
            else {
                dropdownMenu.fadeIn();
            }
        });

        // Zamknij menu uzytkownika
        const closeUserMenu = () => {
            dropdownMenu.fadeOut();
        };

        // Zamkniecie menu po kliknieciu poza jego zawartoscia
        $(window).on("click", (e) => {
            if (!userMenuDiv.is(e.target) && userMenuDiv.has(e.target).length === 0) {
                closeUserMenu();
            }
        });
    }
});