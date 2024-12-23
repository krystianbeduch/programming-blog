<?php
class Pagination {
    private int $currentPage;
    private int $totalPages;
    private int $offset;

    public function __construct(int $currentPage, int $totalPosts, int $postsPerPage) {
        $this->totalPages = (int) ceil($totalPosts / $postsPerPage);
        $this->currentPage = max(1, min($currentPage, $this->totalPages));
        $this->offset = ($this->currentPage - 1) * $postsPerPage;
    }

    public function getCurrentPage(): int {
        return $this->currentPage;
    }

    public function getTotalPages(): int {
        return $this->totalPages;
    }

    public function getOffset(): int {
        return $this->offset;
    }
}