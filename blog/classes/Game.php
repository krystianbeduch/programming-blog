<?php

namespace blackjack;

class Game {
    private Deck $deck;
    private Player $user;
    private Player $croupier;

    public function __construct() {
        $this->deck = new Deck();
        $this->user = new Player();
        $this->croupier = new Player();
        $this->initializeGame();
    }

    public function __destruct() {
        echo "Nowa gra";
    }

    private function initializeGame(): void {
        $this->user->addCard($this->deck->drawCard());
        $this->user->addCard($this->deck->drawCard());
        $this->croupier->addCard($this->deck->drawCard());
        $this->croupier->addCard($this->deck->drawCard());
    }

    public function userDrawCard(): void {
        if (count($this->user->getDeck()) < 5) {
            $this->user->addCard($this->deck->drawCard());
        }
    }

    public function croupierDrawCard(): void {
        while ($this->croupier->getPoints() < 16) {
            $this->croupier->addCard($this->deck->drawCard());
        }
    }

    public function getUserPoints(): int {
        return $this->user->getPoints();
    }

    public function getCroupierPoints(): int {
        return $this->croupier->getPoints();
    }

    public function showUserDeck(): void {
        $this->user->showDeck();
        $this->user->createCheckboxesForAces();
    }

    public function showCroupierDeck(): void {
        $this->croupier->showDeck();
    }

    public function getCroupierDeck(): array {
        return $this->croupier->getDeck();
    }

    public function getGameResults(): void {
        $userPoints = $this->getUserPoints();
        $croupierPoints = $this->getCroupierPoints();

        $finalUserPoints = abs(21 - $userPoints);
        $finalCroupierPoints = abs(21 - $croupierPoints);
        if ($finalUserPoints > $finalCroupierPoints) {
            echo "<p style='color: red'>Krupier wygrał</p>";
        }
        else if ($finalCroupierPoints == $finalUserPoints) {
            echo "<p style='color: #EEAD00'>Remis</p>";
        }
        else {
            echo "<p style='color: #4CAF50'>Gracz wygrał</p>";
        }
    }

    public function getDeck(): Deck {
        return $this->deck;
    }

    public function setDeck(Deck $deck): void {
        $this->deck = $deck;
    }

    public function getUser(): Player {
        return $this->user;
    }

    public function setUser(Player $user): void {
        $this->user = $user;
    }

    public function getCroupier(): Player {
        return $this->croupier;
    }

    public function setCroupier(Player $croupier): void {
        $this->croupier = $croupier;
    }




}