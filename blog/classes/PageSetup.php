<?php
require_once "../includes/posts-functions.php";
require_once "../db/mysql-operation.php";
require_once "../db/posts-management.php";
require_once "Pagination.php";
class PageSetup {
    public string $language;
    public string $languageHeader;
    public int $totalPosts;
    public array $posts;
    public int $postsPerPage;
    public int $currentPage;
    public Pagination $pagination;

    public function __construct(?int $userId = null, ?string $month = null) {
        // Ustalenie biezacej strony
        $this->currentPage = isset($_GET["page"]) && is_numeric($_GET["page"]) ? (int)$_GET["page"] : 1;

        if ($userId) {
            // Pobranie postow uzytkownika
            $this->posts = getUserPosts($userId);
        }
        else if ($month) {
            $this->posts = getPosts(month: $month);
        }
        else {
            // Pobranie jezyka na podstawie nazwy pliku
            $this->language = basename($_SERVER["PHP_SELF"], ".php");

            // Pobranie postow dla jezyka
            $this->posts = $this->getPosts($this->language);

            // Ustalenie nazwy naglowkowej dla strony
            $this->languageHeader = $this->getLanguageHeader();

            // Przefiltrowanie postow po dacie
            $this->filterPostsByDate();
        }

        // Liczba wszystkich postow
        $this->totalPosts = count($this->posts);

        // Liczba postow na strone
        $this->postsPerPage = 3;

        // Obiekt Pagination z odpowiednimi danymi
        $this->pagination = new Pagination($this->currentPage, $this->totalPosts, $this->postsPerPage);
    }

    private function getLanguageHeader(): string {
        // Pobranie nazwy kategorii
        $categoryName = $this->posts[0]["category_name"];
        if ($categoryName == "Cpp") {
            // Zmiana nazwy kategorii z cpp na c++
            $categoryName = "c++";
        }
        else if ($categoryName == "Csharp") {
            // Zmiana nazwy kategorii z csharp na c#
            $categoryName = "c#";
        }
        // Zwrocenie nazwy z pierwsza litera jako duza
        return ucfirst($categoryName);
    }

    private function filterPostsByDate(): void {
        // Filtrowanie postow po dacie jesli filtr jest ustawiony
        $startDate = $_GET["startDate"] ?? null;
        $endDate = $_GET["endDate"] ?? null;
        if ($startDate) {
            $startDate = date_format(date_create($startDate), "Y-m-d");
        }
        if ($endDate) {
            $endDate = date_format(date_create($endDate), "Y-m-d");
        }

        if ($startDate || $endDate) {
            $filteredPosts = [];

            foreach ($this->posts as $post) {
                // Wyodrebnij date (yyyy-mm-dd) z pola created_at
                $postDate = substr($post["created_at"], 0, 10);

                // Jesli tylko startDate, sprawdz rownosc
                if ($startDate && !$endDate && $postDate === $startDate) {
                    $filteredPosts[] = $post;
                }
                // Jesli startDate i endDate, sprawdz zakres
                else if ($startDate && $endDate && $postDate >= $startDate && $postDate <= $endDate) {
                    $filteredPosts[] = $post;
                }
            }
            $this->posts = $filteredPosts;
        }
    }

    private function getPosts(string $language): array {
        // getPosts z mysql-operation.php
        return getPosts($language);
    }

    // Gettery dla obiektu Pagination
    public function getCurrentPage(): int {
        return $this->pagination->getCurrentPage();
    }

    public function getTotalPages(): int {
        return $this->pagination->getTotalPages();
    }

    public function getOffset(): int {
        return $this->pagination->getOffset();
    }
}