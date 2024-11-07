document.addEventListener("DOMContentLoaded", () => {
   const textarea = document.getElementById("comment");
   const bbcodeAddB_Button = document.getElementById("bbcode-add-b-button");
   const bbcodeAddI_Button = document.getElementById("bbcode-add-i-button");
   const bbcodeAddU_Button = document.getElementById("bbcode-add-u-button");
   const bbcodeAddS_Button = document.getElementById("bbcode-add-s-button");
   const bbcodeAddLi_Button = document.getElementById("bbcode-add-li-button");
   const bbcodeAddQuote_Button = document.getElementById("bbcode-add-quote-button");
   const bbcodeAddLink_Button = document.getElementById("bbcode-add-link-button");

   const insertBBCodeTextFormat = (tagStart, tagEnd) => {
       const start = textarea.selectionStart;
       const end = textarea.selectionEnd;
       const text = textarea.value;

       // Wstaw BBCode w wybranej pozycji
       const selectedText = text.substring(start, end);
       textarea.value = text.substring(0, start) + tagStart +
           selectedText + tagEnd + text.substring(end);

       // Ustaw kursor na koncu wstawionego kodu
       textarea.setSelectionRange(start + tagStart.length, start + tagStart.length + selectedText.length);
       textarea.focus();
   };

    // Dodanie zdarzenia click do przycisku bez natychmiastowego wywołania funkcji
    bbcodeAddB_Button.addEventListener("click", () => insertBBCodeTextFormat("[b]", "[/b]"));
    bbcodeAddI_Button.addEventListener("click", () => insertBBCodeTextFormat("[i]", "[/i]"));
    bbcodeAddU_Button.addEventListener("click", () => insertBBCodeTextFormat("[u]", "[/u]"));
    bbcodeAddS_Button.addEventListener("click", () => insertBBCodeTextFormat("[s]", "[/s]"));
    bbcodeAddLi_Button.addEventListener("click", () => {
        // Dodanie pelnej listy
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const selectedText = textarea.value.substring(start, end);

        // Przeksztalc kazda linię tekstu w element listy
        const listItems = selectedText
            .split("\n") // Przeksztalc na tablice
            .map(line => line.trim()) // Usun dodatkowe spacje
            .filter(line => line.length > 0) // Usun puste linie
            .map(line => `[li]${line}[/li]`) // Dodaj tagi [li] do kazdej linii
            .join(""); // Polacz w całosc

        // Dodaj otaczające tagi [ul] i [ul]
        const listBBCode = `[ul]${listItems}[/ul]`;
        textarea.value = textarea.value.substring(0, start) + listBBCode + textarea.value.substring(end);

        // Ustaw kursor po wstawionej liście
        textarea.setSelectionRange(start + listBBCode.length, start + listBBCode.length);
        textarea.focus();
    });

    bbcodeAddQuote_Button.addEventListener("click", () => insertBBCodeTextFormat("[quote]", "[/quote]"));
    bbcodeAddLink_Button.addEventListener("click", () => {
        const url = prompt("Podaj pełny adres URL", "https://")
        if (url) {
            insertBBCodeTextFormat(`[url=${url}]`, "[/url]");
        }
    });
});