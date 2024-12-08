document.addEventListener("DOMContentLoaded", () => {
   const userMenuDiv = document.getElementById("user-menu");

    if (userMenuDiv) {
        const dropdownMenu = document.getElementById("dropdown-menu");

        userMenuDiv.addEventListener("click", () => {
            const isMenuVisible = dropdownMenu.style.display === "block";
            dropdownMenu.style.display = isMenuVisible ? "none" : "block";
        });

        // Zamknij menu uzytkownika
        const closeUserMenu = () => {
            dropdownMenu.style.display = "none";
        };

        // Zamkniecie menu po kliknieciu poza jego zawartoscia
        window.addEventListener("click", (e) => {
            if (!userMenuDiv.contains(e.target)) {
                closeUserMenu();
            }
        });
    }





});