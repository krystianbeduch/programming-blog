<?php

namespace blackjack;

class Deck {
    private array $cards;

    public function __construct() {
        $this->cards = $this->createDeck();
    }

    private function createDeck(): array {
        $colors = ["Pik", "Kier", "Trefl", "Karo"];
        // Wino, Serce, Zoladz, Dzwonek

        $values = [
            "2" => 2,
            "3" => 3,
            "4" => 4,
            "5" => 5,
            "6" => 6,
            "7" => 7,
            "8" => 8,
            "9" => 9,
            "10" => 10,
            "J" => 10,
            "Q" => 10,
            "K" => 10,
            "A" => 11
        ];
        $deck = [];
        foreach ($values as $name => $value) {
            foreach ($colors as $color) {
                $deck[] = new Card($name, $color, $value);
            }
        }
        return $deck;
    }

    public function drawCard(): ?Card {
        if (empty($this->cards)) {
            return null;
        }
        return array_splice($this->cards, array_rand($this->cards), 1)[0];
    }
}