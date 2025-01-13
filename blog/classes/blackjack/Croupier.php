<?php

namespace blackjack\blackjack;

class Croupier extends Player {

    public function croupierDrawCard(Deck $deck): void {
        // Krupier dobiera karty dopoki nie osiagnie min 16 punktow
        while ($this->getPoints() < 16) {
            $this->addCard($deck->drawCard());
        }
    }
}