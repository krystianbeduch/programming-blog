const SERVER_URI = "/US/blog/db/api";
export const checkAvailability = async (type, value) => {
    try {
        // Wyslanie zapytania GET do API za pomoca jQuery.ajax()
        // Zwracamy odpowiedz z serwera w formacie JSON
        return await $.ajax({
            url: `${SERVER_URI}/check-availability.php`,
            method: "GET",
            data: {
                type: type, // typ zapytania
                value: value // wartość do sprawdzenia
            },
            dataType: "json", // Oczekiwany format odpowiedzi
            headers: {
                "Content-Type": "application/json"
            }
        });
    } catch (error) {
        console.error("Błąd podczas sprawdzania dostępności nazwy użytkownika:", error);
        return {
            success: false,
            message: "Wystąpił błąd podczas komunikacji z serwerem."
        };
    }
}; // checkAvailability()