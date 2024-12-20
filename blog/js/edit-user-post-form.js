import { validateField, fieldStyleReset, toggleButtons,
    enableField, disableField
} from "./modules/validate-field-edit-form.js";

$(document).ready(() => {
    const form = $("#edit-user-post");

    // Referencje do pol formularza
    const inputs = {
        title: form.find("input[name='title']"),
        content: form.find("textarea"),
        file: form.find("input[type='file']"),
        attachmentId: form.find("input[name='attachment-id']")
    };

    // Referencje do etykiet
    const labels = {
        title: form.find(`label[for="${inputs.title.attr('id')}"`),
        content: form.find(`label[for="${inputs.content.attr('id')}"`),
        file: form.find(`label[for="${inputs.file.attr('id')}`)
    };

    // Oryginalne tresci etykiet
    const labelsOriginal = {
        title: labels.title.text(),
        content: labels.content.text()
    }

    let editCorrect = true; // Flaga poprawnej edycji pola

    const editButtons = $(".edit-field-form-button");

    const bbcodeButtons = $("button.bbcode-info");
    bbcodeButtons.hide();
    const bbcodePreviewButton = $("button#preview-button");
    const bbcodeClosePreviewButton = $("button.close-preview-button");
    const closeButtons = $("button[name*='close']");

    const handleEditableField = (fieldInput, label, labelTextOriginal, button, closeButton) => {
        if (button.text() === "Zmień") {
            toggleButtons(editButtons, button, closeButton, false);
            enableField(fieldInput);
            label.removeClass("edited-label");

            if (fieldInput.attr("name") === "content") {
                bbcodeButtons.show();
            }

            // Zapisz oryginalna wartosc
            fieldInput.data("originalValue", fieldInput.val());

            fieldInput.on("blur", function() {
                const newValue = fieldInput.val();
                if (newValue !== fieldInput.data("originalValue")) {
                    const fieldName = fieldInput.attr("name");
                    if (fieldName === "title") {
                        const labelTextOriginal = labelsOriginal[fieldName];
                        editCorrect = validateField($(this), label, labelTextOriginal);
                    }
                }
                else {
                    fieldStyleReset($(this), label);
                    editCorrect = true;
                }
            }); // fieldInput blur
        } // if button text Zmien
        else if (button.text() === "Zapisz" && editCorrect) {
            toggleButtons(editButtons, button, closeButton);
            disableField(fieldInput);
            label.addClass("edited-label").removeClass("invalid-label").removeClass("valid-label");
            $("button[type='submit']").show();
            bbcodeButtons.hide();
        } // else if button text Zapisz
    }; // handleEditableField()

    const convertBBCodeToHTML = (bbcode) => {
        let html = bbcode;
        html = html.replace(/\[b](.*?)\[\/b]/g, "<strong>$1</strong>");
        html = html.replace(/\[i](.*?)\[\/i]/g, "<em>$1</em>");
        html = html.replace(/\[u](.*?)\[\/u]/g, "<u>$1</u>");
        html = html.replace(/\[ul](.*?)\[\/ul]/g, "<ul>$1</ul>");
        html = html.replace(/\[li](.*?)\[\/li]/g, "<li>$1</li>");
        html = html.replace(/\[quote](.*?)\[\/quote]/g, "<q>$1</q>");
        html = html.replace(/\[url=(.*?)](.*?)\[\/url]/g, '<a href="$1" target="_blank">$2</a>');
        return html.replace(/\n/g, "<br>"); // Zamiana nowej linii na <br>
    }; // convertBBCodeToHTML()

    editButtons.on("click", function(e) {
        e.preventDefault();
        const fieldType = $(this).attr("name");
        const closeButton = closeButtons.filter(`[name="close-${fieldType}"]`);
        if (fieldType === "title") {
            handleEditableField(
                inputs.title,
                labels.title,
                labels.title.text(),
                $(this),
                closeButton
            );
        }
        else if (fieldType === "content") {
            handleEditableField(
                inputs.content,
                labels.content,
                labels.content.text(),
                $(this),
                closeButton
            );
        }
    }); // editButtons click

    closeButtons.on("click", function() {
        closeButtons.hide();
        editButtons.show();
        editButtons.text("Zmień");

        const buttonName = $(this).attr("name");

        // Usuwamy 'close-' z nazwy przycisku
        const fieldName = buttonName.replace("close-", "");
        const input = inputs[fieldName];
        const label = labels[fieldName];
        disableField(input, false);
        fieldStyleReset(input, label);
        input.val(input.data("originalValue"));
        bbcodeButtons.hide();
    }); // closeButtons click

    bbcodePreviewButton.on("click", () => {
        const bbcodeContent = inputs.content.val();
        const htmlContent = convertBBCodeToHTML(bbcodeContent);
        $("#preview-content").html(htmlContent);
        $("#preview-container").fadeIn();
    });

    bbcodeClosePreviewButton.on("click", () => {
        $("#preview-container").fadeOut();
    });

    inputs.file.on("change", function() {
        // Sprawdzenie, czy wybrano plik
        if (this.files && this.files.length > 0) {
            labels.file.addClass("edited-label");
            $("button[type='submit']").show();
            inputs.attachmentId.removeAttr("disabled");
        } else {
            labels.file.removeClass("edited-label");
            inputs.attachmentId.attr("disabled", true);
            $("button[type='submit']").hide();
        }
    });
});