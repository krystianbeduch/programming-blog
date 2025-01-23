<?php

class DateFilter {
    public ?string $startDate;
    public ?string $endDate;

    public function __construct() {
        $this->startDate = $_GET["startDate"] ?? null;
        $this->endDate = $_GET["endDate"] ?? null;
    }
    // Funkcja sprawdzajaca, czy daty są ustawione
    public function hasFilterEndDate(): bool {
        return !is_null($this->endDate);
    }

    // Funkcja generujaca link do paginacji z filtrami
    public function getDateParams(): string {
        $params = "";
        if ($this->startDate) {
            $params .= "&startDate=" . urlencode($this->startDate);
        }
        if ($this->endDate) {
            $params .= "&endDate=" . urlencode($this->endDate);
        }
        return $params;
    }
}