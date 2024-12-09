$(document).ready(() => {
    const gamePointsSpan = $("#game-points");
    const gameTimerSpan = $("#game-timer");
    const gameResultsP = $("#game-results");
    const level1Button = $("#whack-level-1");
    const level2Button = $("#whack-level-2");
    const level3Button = $("#whack-level-3");
    const endGameButton = $("#whack-end-game");
    const table = $("table");

    let gameTime, gameTimer, moleRoleTimer,whackTimer, moleTimer, gamePlayed, points;

    level1Button.on("click", function() {
        disableButtons($(this));
        moleTimer = 2000;
        startGame();
    });

    level2Button.on("click", function() {
        disableButtons($(this));
        moleTimer = 1000;
        startGame();
    });

    level3Button.on("click", function() {
        disableButtons($(this));
        moleTimer = 500;
        startGame();
    });

    endGameButton.on("click", () => {
        endGame();
    });


    const disableButtons = (clickedButton) => {
      $("button.whack-level-button").each(function() {
         if ($(this).is(clickedButton)) {
             $(this).addClass("clicked-button");
         }
         $(this).attr("disabled", "true");
         $(this).removeClass("hover-style");
      });
      endGameButton.removeAttr("disabled");
      endGameButton.addClass("hover-style");
    };

    const activeButtons = () => {
        $("button.whack-level-button").each(function() {
            $(this).removeClass("clicked-button");
            $(this).removeAttr("disabled");
            $(this).addClass("hover-style");
        });
    };

    const startGame = () => {
        createBoard();
        setGameTimer();
        gameTime = 60;
        points = 0;
        gameTimerSpan.text(gameTime);
        gamePointsSpan.text(points);
        gamePlayed = true;
        gameResultsP.empty();
    };

    const createBoard = () => {
        const img = "<img src='../images/mole.png' alt='mole'/>";
        for (let i = 0; i < 4; i++) {
            $("table").append("<tr>");
            for (let j = 0; j < 4; j++) {
                const cell = $("table tr").last();
                cell.append(`<td id="${i}${j}"> ${img}`);
                cell.find("img").hide();
            }
        }
    };

    const randMole = () => {
        if (gamePlayed) {
            // Usun wszystkie krety przed dodaniem nowego
            $("td").removeClass("selected").find("img").hide();

            // Wylosuj dziure
            let i = Math.floor(Math.random() * 4);
            let j = Math.floor(Math.random() * 4);
            const cell = $(`td[id="${i}${j}"]`);

            // Pokaz kreta
            cell.addClass("selected").find("img").show();

            // Po sekundzie schowaj kreta
            whackTimer = setTimeout( () => {
                cell.removeClass("selected").find("img").hide();
            }, moleTimer);
        }
    };



    // Obsługa kliknięcia na komórki
    table.on("click", "td", function() {
        if (gamePlayed && $(this).hasClass("selected")) {
            gamePointsSpan.text(++points);
            clearInterval(whackTimer);
            $(this).removeClass("selected").find("img").hide();
        }
    });

    const endGame = () => {
        gamePlayed = false;
        table.empty();
        endGameButton.attr("disabled", "true");
        endGameButton.removeClass("hover-style");
        $("td").each(function() {
            $(this).removeClass("selected").find("img").hide();
        });
        gameResultsP.html(`KONIEC GRY<br>Zdobyłeś ${points} punktów`);
        activeButtons();
        clearInterval(whackTimer);
        clearInterval(gameTimer);
        clearInterval(moleRoleTimer);
    };
    const setGameTimer = () => {
        moleRoleTimer = setInterval(randMole, moleTimer);

        // Czas gry
        gameTimer = setInterval(() => {
            if (gamePlayed) {
                gameTimerSpan.text(gameTime--);
                if (gameTime < 0) {
                    endGame();
                }
            }
        }, 1000);
    };
});