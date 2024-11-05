document.addEventListener("DOMContentLoaded", () => {
   const textarea = document.getElementById("comment");
   const bbbcodeAddB_Button = document.getElementById("bbcode-add-b-button");

   const insertBBCode = (tagStart, tagEnd) => {
       const start = textarea.selectionStart;
       const end = textarea.selectionEnd;
       const text = textarea.value;

       // Wstaw BBCode w wybranej pozycji
       const selectedText = text.substring(start, end);
       textarea.value = text.substring(0, start) + tagStart +
           selectedText + tagEnd + text.substring(end);

       // Ustaw kursor na koncu wstawionego kodu
       // textarea.setSelectionRange(st)
       textarea.focus();
   };

    // Dodanie zdarzenia click do przycisku bez natychmiastowego wywołania funkcji
    bbbcodeAddB_Button.addEventListener("click", () => insertBBCode("[b]", "[/b]"));
});