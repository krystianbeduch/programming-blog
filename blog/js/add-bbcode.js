$(document).ready(() => {
    const textarea = $("#content");
    const buttons = {
        bold: $("#bbcode-add-b-button"),
        italic: $("#bbcode-add-i-button"),
        underline: $("#bbcode-add-u-button"),
        strikethrough: $("#bbcode-add-s-button"),
        list: $("#bbcode-add-li-button"),
        quote: $("#bbcode-add-quote-button"),
        link: $("#bbcode-add-link-button"),
    };

    const insertBBCodeTextFormat = (tagStart, tagEnd) => {
        const start = textarea.prop("selectionStart");
        const end = textarea.prop("selectionEnd");
        const text = textarea.val();
        const selectedText = text.substring(start, end);

        // Wstaw BBCode w wybranej pozycji
        textarea.val(text.substring(0, start) + tagStart + selectedText + tagEnd + text.substring(end));
        textarea[0].setSelectionRange(start + tagStart.length, start + tagStart.length + selectedText.length);
        textarea.focus();
    }; // insertBBCodeTextFormat()

    const insertList = () => {
        const start = textarea.prop("selectionStart");
        const end = textarea.prop("selectionEnd");
        const text = textarea.val();
        const selectedText = text.substring(start, end);

        // Przeksztalc kazda linie tekstu w element listy
        const listItems = selectedText
            .split("\n") // Przeksztalc na tablice
            .map(line => line.trim()) // Usun dodatkowe spacje
            .filter(line => line.length > 0) // Usun puste linie
            .map(line => `[li]${line}[/li]`) // Dodaj tagi [li] do kazdej linii
            .join(""); // Polacz w całosc

        // Dodaj otaczajace tagi [ul] i [ul]
        const listBBCode = `[ul]${listItems}[/ul]`;
        textarea.val(text.substring(0, start) + listBBCode + text.substring(end));

        // Ustaw kursor po wstawionej liscie
        textarea[0].setSelectionRange(start + listBBCode.length, start + listBBCode.length);
        textarea.focus();
    }; // insertList()

    const insertLink = () => {
        const url = prompt("Podaj pełny adres URL", "https://")
        if (url) {
            insertBBCodeTextFormat(`[url=${url}]`, "[/url]");
        }
    };

    buttons.bold.on("click", () => insertBBCodeTextFormat("[b]", "[/b]"));
    buttons.italic.on("click", () => insertBBCodeTextFormat("[i]", "[/i]"));
    buttons.underline.on("click", () => insertBBCodeTextFormat("[u]", "[/u]"));
    buttons.strikethrough.on("click", () => insertBBCodeTextFormat("[s]", "[/s]"));
    buttons.list.on("click", insertList);
    buttons.quote.on("click", () => insertBBCodeTextFormat("[quote]", "[/quote]"));
    buttons.link.on("click", insertLink);
});