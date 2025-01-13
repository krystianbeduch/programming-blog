<?php

namespace blackjack\blackjack;

abstract class Player {
    protected array $deck = [];

    public function addCard(Card $card): void {
        $this->deck[] = $card;
    }

    public function getPoints(): int {
        $points = 0;
        foreach ($this->deck as $card) {
            $points += $card->getValue();
        }
        return $points;
    }

    public function showDeck(): void {
        foreach ($this->deck as $card) {
            echo "<img src='{$card->getImagePath()}' alt='{$card->getName()} {$card->getColor()}' title='{$card->getName()} {$card->getColor()}'>";
        }
    }

    public function getDeck(): array {
        return $this->deck;
    }

    public function getDeckCount(): int {
        return count($this->deck);
    }
}