<?php

namespace blackjack\blackjack;

class Game {
    private static ?Game $instance = null;
    private bool $isGameOver;
    private Deck $deck;
    private User $user;
    private Croupier $croupier;


    public function __construct() {
        $this->isGameOver = false;
        $this->deck = new Deck();
        $this->user = new User();
        $this->croupier = new Croupier();
        $this->initializeGame();
    }

    public static function getInstance(): ?Game {
        if (self::$instance == null) {
            self::$instance = new Game();
        }
        return self::$instance;
    }

    private function initializeGame(): void {
        // Losuj po 2 karty na start
        $this->user->addCard($this->deck->drawCard());
        $this->user->addCard($this->deck->drawCard());
        $this->croupier->addCard($this->deck->drawCard());
        $this->croupier->addCard($this->deck->drawCard());
    }

    public function getGameResults(): void {
        $userPoints = $this->user->getPoints();
        $croupierPoints = $this->croupier->getPoints();

        $finalUserPoints = abs(21 - $userPoints);
        $finalCroupierPoints = abs(21 - $croupierPoints);
        if ($finalUserPoints > $finalCroupierPoints) {
            echo "<p style='color: var(--delete-text)'>Krupier wygrał</p>";
        }
        else if ($finalCroupierPoints == $finalUserPoints) {
            echo "<p style='color: #EEAD00'>Remis</p>";
        }
        else {
            echo "<p style='color: var(--primary-color)'>Gracz wygrał</p>";
        }
    }
    public function getIsGameOver(): bool {
        return $this->isGameOver;
    }

    public function setIsGameOver(bool $isGameOver): void {
        $this->isGameOver = $isGameOver;
    }

    public function getUser(): User {
        return $this->user;
    }
    public function getCroupier(): Croupier {
        return $this->croupier;
    }

    public function getDeck(): Deck {
        return $this->deck;
    }

    // Zabezpieczamy przed klonowaniem obiektu Singletona
    private function __clone() {}

    // Zabezpieczamy przed serializowaniem obiektu Singletona
    public function __wakeup() {}
}