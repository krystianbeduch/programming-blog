<?php

namespace blackjack\blackjack;

class Card {
    private string $name;
    private string $color;
    private int $value;

    public function __construct(string $name, string $color, int $value) {
        $this->name = $name;
        $this->color = $color;
        $this->value = $value;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getColor(): string {
        return $this->color;
    }

    public function getValue(): int {
        return $this->value;
    }

    public function setValue(int $value): void {
        $this->value = $value;
    }

    public function getImagePath(): string {
        $cardName = strtolower($this->name);
        $cardColor = strtolower($this->color);
        return "../images/blackjack/{$cardName}_{$cardColor}.png";
    }

    public function getImageOfBackCard(): string {
        return "../images/blackjack/back.png";
    }
}