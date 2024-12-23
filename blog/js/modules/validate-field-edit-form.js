export const validateField = (field, label, labelTextOriginal) => {
    // const fieldName = field.attr("name");
    // const labelTextOriginal = labelsOriginal[fieldName];
    if (!field[0].validity.valid) {
        fieldIsInvalid(field, label);
        if (field[0].validity.tooShort) {
            label.text(`${labelTextOriginal} (wartość za krótka)`);
        }
        else if (field[0].validity.valueMissing) {
            label.text(`${labelTextOriginal} (brak wartości)`);
        }
        else if (field[0].validity.typeMismatch) {
            label.text(`${labelTextOriginal} (niepoprawny format)`);
        }
        return false;
    }
    else {
        fieldIsValid(field, label)
        label.text(labelTextOriginal);
        return true;
    }
}; // validateField()

export const fieldIsValid = (field, label) => {
    label.addClass("valid-label").removeClass("invalid-label");
    field.addClass("invalid-field").removeClass("valid-field");
    field.addClass("valid-input").removeClass("invalid-input")
    // return true;
};

export const fieldIsInvalid = (field, label) => {
    label.removeClass("valid-label").addClass("invalid-label");
    field.removeClass("invalid-field").addClass("valid-field");
    field.removeClass("valid-input").addClass("invalid-input");
    // return false;
};

export const fieldStyleReset = (field, label) => {
    field
        .removeClass("invalid-field")
        .removeClass("valid-input")
        .removeClass("invalid-input")
        .removeClass("valid-input");
    label
        .removeClass("invalid-label")
        .removeClass("valid-label");
    // return true;
};

export const toggleButtons = (editButtons, button, closeButton, show = true) => {
    const buttons = $(editButtons).add($("button[type='submit']"));
    buttons.each(function() {
        $(this).toggle(show);
    });
    button.show();
    closeButton.toggle(!show);

    if (button.text() === "Zmień") {
        button.text("Zapisz");
    }
    else {
        button.text("Zmień");
    }
};

export const enableField = (input) => {
    input.removeAttr("disabled readonly").attr("required", true).focus();
};

export const disableField = (input, edit = true) => {
    if (!edit) {
        input.removeAttr("readonly").attr("disabled", true);
    }
    else {
        input.attr("readonly", true).removeAttr("required");
    }
};