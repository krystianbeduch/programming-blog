<?php

namespace blackjack;

class Player {
    private array $deck = [];

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
            echo "<img src='{$card->getImagePath()}' alt='{$card->getName()} {$card->getColor()}'>";
        }
    }

    public function createCheckboxesForAces() : void {
        echo "<div class='checkbox-container'>";
        foreach ($this->deck as $index => $card) {
            if ($card->getName() == "A" && $card->getValue() == 11) {
                echo
                "<label class='ace-checkbox-label'>
                <input type='checkbox' name='changeAceValue[]' value='{$index}'> Zmień " . $card->getName() . " " . $card->getColor() . " na 1 
                </label>";
            }
        }
        echo "</div>";
    }

    public function changeAceValues(array $indexes): void {
        foreach ($indexes as $index) {
            if ($this->deck[$index]->getName() == "A") {
                $this->deck[$index]->setValue(1);
            }
        }
    }

    public function getDeck(): array {
        return $this->deck;
    }

    public function getDeckCount(): int {
        return count($this->deck);
    }
}