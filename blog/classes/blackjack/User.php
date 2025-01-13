<?php

namespace blackjack\blackjack;

class User extends Player {

    public function drawCard(Deck $deck): void {
        // Gracz moze dobierac do 5 kart
        if ($this->getDeckCount() < 5) {
            $this->addCard($deck->drawCard());
        }
    }

    #[\Override]
    public function showDeck(): void {
        parent::showDeck();
        $this->createCheckboxesForAces();
    }

    public function createCheckboxesForAces() : void {
        echo "<div class='checkbox-container'>";
        foreach ($this->deck as $index => $card) {
            if ($card->getName() == "A" && $card->getValue() == 11) {
                echo "<label class='ace-checkbox-label'>
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
}