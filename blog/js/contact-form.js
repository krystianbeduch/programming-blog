$(document).ready(() => {
    const form = $("form#contact-form");


    form.on("submit", function(e){
        let isValid = true;

        $(this).find("input, textarea").each(function() {
            if (!this.checkValidity()) {
                isValid = false;
                $(this).addClass("is-invalid").removeClass("is-valid");
            }
            else {
                $(this).addClass("is-valid").removeClass("is-invalid");
            }
        });

        // Jeśli formularz nie jest poprawny, zapobiegamy wysłaniu
        if (!isValid) {
            e.preventDefault();
            e.stopPropagation();
        }
        form.addClass("was-validated");
    })
});