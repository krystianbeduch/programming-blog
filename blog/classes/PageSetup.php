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

    public function __construct(?int $userId = null) {
        // Ustalenie biezacej strony
        $this->currentPage = isset($_GET["page"]) && is_numeric($_GET["page"]) ? (int)$_GET["page"] : 1;

        if ($userId) {
            // Pobranie postow uzytkownika
            $this->posts = getUserPosts($userId);
        }
        else {
            // Pobranie jezyka na podstawie nazwy pliku
            $this->language = basename($_SERVER["PHP_SELF"], ".php");

            // Pobranie postow dla jezyka
            $this->posts = $this->getPosts($this->language);



            // Ustalenie nazwy naglowkowej
            $this->languageHeader = $this->getLanguageHeader();

            $this->filterPostsByDate();
        }

        $this->totalPosts = count($this->posts);

        // Liczba postow na strone
        $this->postsPerPage = 3;

        // Obiekt Pagination z odpowiednimi danymi
        $this->pagination = new Pagination($this->currentPage, $this->totalPosts, $this->postsPerPage);
    }

    private function getLanguageHeader(): string {
        $categoryName = $this->posts[0]["category_name"];
        if ($categoryName == "Cpp") {
            $categoryName = "C++";
        }
        else if ($categoryName == "Csharp") {
            $categoryName = "C#";
        }
        return $categoryName;
    }

    private function filterPostsByDate(): void {
        $startDate = $_GET["startDate"] ?? null;
        $endDate = $_GET["endDate"] ?? null;
        if ($startDate) {
            $startDate = date_format(date_create($startDate), "Y-m-d");
//            print_r($startDate);
        }
        if ($endDate) {
            $endDate = date_format(date_create($endDate), "Y-m-d");
        }
//        echo $startDate . " - " . $endDate . "<br>";
//        print_r(date_create($startDate));
//        echo date_format(strtotime($startDate), "yyyy-mm-dd");



        if ($startDate || $endDate) {
            $filteredPosts = [];

            foreach ($this->posts as $post) {
                // Wyodrębnij datę (yyyy-mm-dd) z pola created_at
                $postDate = substr($post["created_at"], 0, 10);
//                $postDate = strtotime($post["created_at"]);
//                echo $postDate . " <Br>";
//                echo $startDate;

                // Jeśli tylko startDate, sprawdź równość
                if ($startDate && !$endDate && $postDate === $startDate) {
                    $filteredPosts[] = $post;
                }
                // Jeśli startDate i endDate, sprawdź zakres
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