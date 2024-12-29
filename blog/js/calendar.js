$(document).ready(() => {
    // Ustawienia jezykowe dla kalendarzy
    $.datepicker.regional["pl"] = {
        closeText: "Zamknij",
        prevText: "Poprzedni",
        nextText: "Następny",
        currentText: "Dziś",
        monthNames: ["Styczeń", "Luty", "Marzec", "Kwiecień", "Maj", "Czerwiec", "Lipiec", "Sierpień", "Wrzesień", "Październik", "Listopad", "Grudzień"],
        monthNamesShort: ["Sty", "Lut", "Mar", "Kwi", "Maj", "Cze", "Lip", "Sie", "Wrz", "Paź", "Lis", "Gru"],
        dayNames: ["Niedziela", "Poniedziałek", "Wtorek", "Środa", "Czwartek", "Piątek", "Sobota"],
        dayNamesShort: ["Nie", "Pon", "Wto", "Śro", "Czw", "Pią", "Sob"],
        dayNamesMin: ["Nd", "Pn", "Wt", "Śr", "Cz", "Pt", "So"],
        weekHeader: "Tydz",
        dateFormat: "dd.mm.yy",
        firstDay: 1,
        isRTL: false, // Czy kalendarzy ma byc od prawej do lewej
        showMonthAfterYear: false,
        yearSuffix: ""
    };
    $.datepicker.setDefaults($.datepicker.regional["pl"]);

    const startDatePicker = $("#start-date");
    const endDatePicker = $("#end-date");
    const singleDateCheckbox = $("#single-date");
    const endDateContainer = $("#end-date-con");
    const button = $("#calendar-button");

    const enableEndDate = () => {
        // Wlaczamy date koncowa, gdy wybieramy przedzial
        endDateContainer.show();
        endDatePicker.prop("disabled", false);
        startDatePicker.datepicker("option", "maxDate", "today");
    }

    const disableEndDate = () => {
        endDateContainer.hide();
        endDatePicker.prop("disabled", true).val("");
        startDatePicker.datepicker("option", "maxDate", "today");
    };

    const formatDateToISO = (dateStr) => {
        const dateParts = dateStr.split(".");
        const year = dateParts[2];
        const month = dateParts[1] - 1;
        const day = dateParts[0];
        return new Date(year, month, day);
    };

    const startDateValue = startDatePicker.val();
    const endDateValue = endDatePicker.val();

    // Ustawienie poczatkowe w zaleznosci od checkboxa
    if (singleDateCheckbox.is(":checked")) {
        enableEndDate();
    }
    else {
        disableEndDate();
    }

    startDatePicker.datepicker({
        changeMonth: true,
        changeYear: true,
        minDate: new Date(2024, 9, 1),
        maxDate: "today",
        dateFormat: "dd.mm.yy",
        showAnim: "blind",
        showButtonPanel: true,
        onClose: selectedDate => {
            // Zmiana minDate dla daty koncowej po wybraniu daty poczatkowej
            if (singleDateCheckbox.is(":checked")) {
                endDatePicker.datepicker("option", "minDate", selectedDate);
            }
        }
    });

    endDatePicker.datepicker({
        changeMonth: true,
        changeYear: true,
        minDate: new Date(2024, 9, 1),
        maxDate: "today",
        dateFormat: "dd.mm.yy",
        showAnim: "blind",
        showButtonPanel: true,
        onClose: selectedDate => {
            // Zmiana maxDate dla daty poczatkowej po wybraniu daty koncowej
            if (singleDateCheckbox.is(":checked")) {
                startDatePicker.datepicker("option", "maxDate", selectedDate);
            }
        }
    });

    // Ustawienie przedzialu dat, jesli wartości sa juz ustawione (po przeladowaniu strony)
    if (startDateValue || endDateValue) {
        // Ustawienie przedzialow na kalendarzach po odswiezeniu strony
        const startDate = formatDateToISO(startDatePicker.val());
        const endDate = formatDateToISO(endDatePicker.val());
        startDatePicker.datepicker("option", "maxDate", endDate);
        endDatePicker.datepicker("option", "minDate", startDate);
    }

    // Zmiana trybu na pojedyncza datę lub przedzial dat
    singleDateCheckbox.change(function() {
        if ($(this).is(":checked")) {
            enableEndDate()
        }
        else {
            disableEndDate();
        }
    });

    button.on("click", () => {
        const currentURL = window.location.pathname
            .split("/")
            .pop();
        const startDate = startDatePicker.val();
        const endDate = endDatePicker.val();
        const dataToSend = { startDate: startDate };
        if (endDate.length) {
            dataToSend.endDate = endDate;
        }

        if (startDate && currentURL) {
            // Przygotowanie parametrow do URL
            const queryParams = new URLSearchParams(dataToSend).toString();

            // Przekierowanie na nowy URL z parametrami
            window.location.href = `${currentURL}?${queryParams}`;
        }
    });
});