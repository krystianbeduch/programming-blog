<?php
function convertBBCodeToHTML(string $text): string {
    $text = html_entity_decode($text);
    $text = htmlspecialchars($text, ENT_QUOTES | ENT_HTML5, "UTF-8");
    /*
    \[b] - znacznik [b]
    \[\/b] - znacznik [/b]
    . - dowolny znak wraz ze znakiem nowej linii (ze wzgledu na ustawiona flage s
    * - zero lub wiecej poprzedzajacego elementu (czyli kropki)
    ? - wyrazenie nongreedy - dopasowanie zatrzyma sie na pierwszym wystapieniu [/b]
    (.*?) - cale wyrazenie dopasowuje dowolny tekst miedzy znacznikami, zachowujac ten tekst jako grupe do pozniejszego uzycia jako $1
    */

    $text = preg_replace("/\[b](.*?)\[\/b]/s", "<strong>$1</strong>", $text);
    $text = preg_replace("/\[i](.*?)\[\/i]/s", "<em>$1</em>", $text);
    $text = preg_replace("/\[u](.*?)\[\/u]/s", "<u>$1</u>", $text);
    $text = preg_replace("/\[s](.*?)\[\/s]/s", "<s>$1</s>", $text);
    $text = preg_replace("/\[ul](.*?)\[\/ul]/s", "<ul>$1</ul>", $text);
    $text = preg_replace("/\[li](.*?)\[\/li]/s", "<li>$1</li>", $text);
    $text = preg_replace("/\[quote](.*?)\[\/quote]/s", "<q>$1</q>", $text);
    $text = preg_replace("/\[url=(.*?)](.*?)\[\/url]/s", "<a href='$1' target='_blank'>$2</a>", $text);

    // Zamiana [html] na bezposredni kod HTML
    $text = preg_replace_callback("/\[html](.*?)\[\/html]/s", function ($matches) {
        // Kodowanie tekstu na HTML tak, aby <header> stał się &lt;header&gt;
        return htmlspecialchars("&lt;" . $matches[1] . "&gt;", ENT_QUOTES | ENT_HTML5, "UTF-8");
    }, $text);

    // Zamiana podwojnych cudzysłowow na pojedyncze
    $text = str_replace('"', "'", $text);

    return nl2br($text); // Zamiana nowych linii na <br>
}

function convertHTMLToBBCode(string $text): string {
    // Zamiana znacznika <strong> na [b]
    $text = preg_replace("/<strong>(.*?)<\/strong>/s", "[b]$1[/b]", $text);
    // Zamiana znacznika <em> na [i]
    $text = preg_replace("/<em>(.*?)<\/em>/s", "[i]$1[/i]", $text);
    // Zamiana znacznika <u> na [u]
    $text = preg_replace("/<u>(.*?)<\/u>/s", "[u]$1[/u]", $text);
    // Zamiana znacznika <s> na [s]
    $text = preg_replace("/<s>(.*?)<\/s>/s", "[s]$1[/s]", $text);
    // Zamiana znacznika <ul> na [ul]
    $text = preg_replace("/<ul>(.*?)<\/ul>/s", "[ul]$1[/ul]", $text);
    // Zamiana znacznika <li> na [li]
    $text = preg_replace("/<li>(.*?)<\/li>/s", "[li]$1[/li]", $text);
    // Zamiana znacznika <q> na [quote]
    $text = preg_replace("/<q>(.*?)<\/q>/s", "[quote]$1[/quote]", $text);
    // Zamiana znacznika <a> na [url=]
    $text = preg_replace_callback("/<a\s+href=['\"](.*?)['\"].*?>(.*?)<\/a>/is", function($matches) {
        // Zwracamy poprawnie przetworzony link BBCode
        return "[url=" . $matches[1] . "]" . $matches[2] . "[/url]";
    }, $text);

    // Usunięcie <br> na nowe linie
    $text = preg_replace("/<br\s*\/?>/i", "\n", $text);

    // Usuwanie dodatkowych pustych linii
    $text = preg_replace("/(\n\s*){2,}/", "\n", $text);

    // Kodowanie specjalnych znakow HTML
    return trim(htmlentities($text, ENT_QUOTES | ENT_HTML5, "UTF-8"));
}