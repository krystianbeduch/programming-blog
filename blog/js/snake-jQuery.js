$(document).ready(function () {
    const canvas = $("#game-canvas")[0]; // Pobieranie elementu canvas
    const ctx = canvas.getContext("2d"); // Kontekst 2D do rysowania
    const canvasWidth = canvas.width;
    const canvasHeight = canvas.height;
    const gridSize = 20; // Wielkosc jednej komorki siatki
    const gameInfo = $("#game-info");
    const SERVER_URI = "/US/blog/db/api";
    const snakeScores = $("#snake-scores");

    let snake, direction, gameInterval, isGameOver, isPaused, gameScore;
    let gameSpeed, goldenFoodTimeout;
    let food = {};
    let goldenFood = {};
    let obstacles = [];
    let teleports = [];

    startGameInfo();

    // Funkcja do resetowania gry
    const resetGame = () => {
        //  Poczatkowa pozycja weza
        //  Waz to tablica segmentow, kazdy segment to obiekt {x, y}
        snake = [{ x: 0, y: 0 }]; //
        direction = "RIGHT"; // Początkowy kierunek
        isGameOver = false; // Zresetowanie stanu konca gry
        gameSpeed = 200; // Poczatkowa predkosc gry w ms
        spawnFood() // Generowanie pierwszego jedzenia
        clearInterval(goldenFoodTimeout); // Zatrzymanie poprzedniego timera
        goldenFood = null; // Na poczatku nie ma zlotego jedzenia
        goldenFoodTimeout = setTimeout(() => {
            generateGoldenFood(); // Pierwsze zlote jedzenie po losowym czasie
        }, getRandomGoldenFoodDelay(5, 10));

        obstacles = [];
        generateObstacles(5);

        teleports = [];
        generateTeleports();

        gameInfo.empty(); // Czyszczenie komunikatu
        gameScore = 0; // Restowanie punktow
        clearInterval(gameInterval); // Zatrzymanie poprzedniej gry, jesli trwala
        gameInterval = setInterval(updateGame, gameSpeed); // Rozpoczecie nowej gry

        getUserScores();
    };

    function startGameInfo() {
        isPaused = true; // Poczatkowo gra zatrzymana
        ctx.fillStyle = "#fff"; // Kolor tekstu
        ctx.font = "20px Arial"; // Czcionka
        ctx.textAlign = "center"; // Wyrównanie do lewej
        ctx.fillText("Aby rozpocząć kliknij P", canvasWidth / 2, canvasHeight / 2); // Wyświetlenie tekstu
    }

    // Rysowanie planszy
    const drawBoard = () => {
        // Wyczyszczenie planszy
        ctx.clearRect(0, 0, canvasWidth, canvasHeight);
        ctx.strokeStyle = "#333";
        for (let x = 0; x < canvasWidth; x += gridSize) {
            for (let y = 0; y < canvasHeight; y += gridSize) {
                // Rysowanie siatki
                ctx.strokeRect(x, y, gridSize, gridSize);
            }
        }
    };

    // Rysowanie weza
    const drawSnake = () => {
        // ctx.fillStyle = "#0f0"; // Kolor weza
        snake.forEach((segment, index) => {
            ctx.fillStyle = index === 0 ? "#218838" : "#0f0";
            // Rysowanie kazdego segmentu weza
            ctx.fillRect(segment.x, segment.y, gridSize, gridSize);
        });
    };

    // Przesuwanie weza
    const moveSnake = () => {
        // Kopituj aktualna glowe weza
        const head = { ...snake[0] };

        // Aktualizacja wspolrzednych glowy weza w zaleznosci od kierunku
        switch(direction) {
            case "UP":
                head.y -= gridSize;
                break;
            case "DOWN":
                head.y += gridSize;
                break;
            case "LEFT":
                head.x -= gridSize;
                break;
            case "RIGHT":
                head.x += gridSize;
                break;
        }

        // Dodanie nowej glowy na poczatku tablicy
        snake.unshift(head);

        // Usuniecie ostatniego segmentu weza (symuluje ruch)
        snake.pop();
    };

    // Sprawdzenie czy waz nie wyjechal za plansze
    const checkGameOver = () => {
        const head = snake[0];

        if (
            head.x < 0 ||
            head.x >= canvasWidth ||
            head.y < 0 ||
            head.y >= canvasHeight
        ) {
            endGame();
        }
    };

    // Rysowania punktacji
    const drawScore = () => {
        ctx.fillStyle = "#fff"; // Kolor tekstu
        ctx.font = "20px Arial"; // Czcionka
        ctx.textAlign = "left"; // Wyrównanie do lewej
        ctx.fillText(`Punkty: ${gameScore}`, 10, canvasHeight - 10); // Wyświetlenie tekstu
    };

    // Koniec gry
    const endGame = () => {
        clearTimeout(goldenFoodTimeout);
        isGameOver = true;
        clearInterval(gameInterval);
        // gameInfo.html(`<!--Koniec gry! Uzyskałeś ${score} punktów. <br>Kliknij R aby rozpocząć nową grę-->`);
        ctx.clearRect(0, 0, canvasWidth, canvasHeight);
        ctx.fillStyle = "#fff"; // Kolor tekstu
        ctx.font = "20px Arial"; // Czcionka
        ctx.textAlign = "center"; // Wyrównanie do lewej
        ctx.fillText("KONIEC GRY!", canvasWidth / 2, (canvasHeight / 2) - 30); // Wyswietlenie tekstu
        ctx.fillText(`Uzyskałeś ${gameScore} punktów`, canvasWidth / 2, (canvasHeight / 2)); // Wyswietlenie tekstu
        ctx.fillText("Aby rozpocząć ponownie wciśnij R", canvasWidth / 2, (canvasHeight / 2) + 60); // Wyswietlenie tekstu
        ctx.fillText("Jeśli chcesz zapisać wynik wciśnij H", canvasWidth / 2, (canvasHeight / 2) + 90); // Wyswietlenie tekstu
    };

    // Generuj jedzenie na planszy
    const spawnFood = () => {
        food = {
            x: Math.floor(Math.random() * (canvasWidth / gridSize)) * gridSize,
            y: Math.floor(Math.random() * (canvasHeight / gridSize)) * gridSize,
        };
    };

    // Rysuj jedzenie
    const drawFood = () => {
        if (food) {
            ctx.fillStyle = "#00f"; // Niebieski kolor
            ctx.shadowColor = "#00f"; // Niebieski cień
            ctx.shadowBlur = 10; // Efekt świecenia
            ctx.beginPath();
            ctx.arc(food.x + gridSize / 2, food.y + gridSize / 2, gridSize / 2, 0, Math.PI * 2);
            ctx.fill();
            ctx.shadowBlur = 0; // Reset cienia dla innych elementów
        }
        if (goldenFood) {
            ctx.fillStyle = "#d4af37"; // Niebieski kolor
            ctx.shadowColor = "#d4af37"; // Niebieski cień
            ctx.shadowBlur = 10; // Efekt świecenia
            ctx.beginPath();
            ctx.arc(goldenFood.x + gridSize / 2, goldenFood.y + gridSize / 2, gridSize / 2, 0, Math.PI * 2);
            ctx.fill();
            ctx.shadowBlur = 0; // Reset cienia dla innych elementów
        }
    };

    // Zebranie jedzenia
    const checkFoodCollision = () => {
        const head = snake[0]; // Glowa weza

        // Zwykle jedzenie
        if (head.x === food.x && head.y === food.y) {
            gameScore++;
            // Dodanie segmentu weza
            snake.push({ x: food.x, y: food.y });
            spawnFood();
            adjustGameSpeed();
        }

        // Zlote jedzenie
        if (goldenFood && head.x === goldenFood.x && head.y === goldenFood.y) {
            gameScore += 3;
            // Dodanie segmentu weza
            snake.push({ x: food.x, y: food.y });
            goldenFood = null;
            adjustGameSpeed("gold");
            scheduleNextGoldenFood();
        }
    };

    const checkSelfCollision = () => {
        const head = snake[0]; // Glowa weza
        // Sprawdzenie czy glowa weza uderzyla w jakikolwiek segment ciala
        for (let i = 1; i < snake.length; i++) {
            const segment = snake[i];
            if (head.x === segment.x && head.y === segment.y) {
                endGame();
            }
        }
    }

    const adjustGameSpeed = (foodType = "blue") => {
        // Przyspieszamy gre po kazdym zjedzeniu
        if (gameSpeed > 80) { // Minimalna wartosc opoznienia
            if (foodType === "blue") {
                gameSpeed -= 5; // Zmniejsz czas opoznienia
            }
            else if (foodType === "gold") {
                gameSpeed -= 15; // Zmniejsz czas opoznienia trzykrotnie
            }
            clearInterval(gameInterval) // Zatrzymaj poprzedni interwal
            gameInterval = setInterval(updateGame, gameSpeed); // Uruchamiamy nowe interwal
      }
    };

    // Aktualizacja gry
    const updateGame = () => {
        if (!isGameOver && !isPaused) {
            drawBoard();
            moveSnake();
            drawSnake();
            drawFood();
            drawObstacles();
            drawTeleports();
            checkSelfCollision();
            checkFoodCollision();
            checkObstacleCollision();
            checkTeleportCollision();
            drawScore();
            checkGameOver();
        }
    };

    function getRandomGoldenFoodDelay (min, max)  {
        let time = Math.floor(Math.random() * (max - min + 1) + min) * 1000;
        console.log(`Zlote jedzenie po :${time}`);
        // Losowy czas od 5 do 10 sekund
        return time;
    }

    const generateGoldenFood = () => {
      goldenFood = {
          x: Math.floor(Math.random() * (canvasWidth / gridSize)) * gridSize,
          y: Math.floor(Math.random() * (canvasHeight / gridSize)) * gridSize
      }
    };

    const scheduleNextGoldenFood = () => {
        clearTimeout(goldenFoodTimeout); // Zatrzymaj poprzedni timeout
        goldenFoodTimeout = setTimeout(() => {
            generateGoldenFood();
        }, getRandomGoldenFoodDelay(7, 15))
    };

    const saveUserScore = () => {
        const userEntity = {};

        // Wykonaj zapytanie do API w celu pobrania nazwy uzytkownika
        fetch(`${SERVER_URI}/snake.php?getUserName`, {
            method: "GET",
            headers: {
                "Content-Type": "application/json"
            }
        })
            .then((response) => {
                if (response.status === 404) {
                    // Jesli uzytkownik nie jest zalogowany, popros o wprowadzenie nazwy
                    userEntity.username = prompt("Podaj nazwę użytkownika");
                    // Zwroc nowy obiekt
                    return userEntity;
                } else {
                    // Jesli uzytkownik jest zalogowany, przeparsuj jąajako JSON
                    return response.json();
                }

            })
            .then((result) => {
                if (result && result.username) {
                    // Jeśli API zwroclło dane, zaktualiuj userEntity
                    userEntity.username = result.username;
                }

                userEntity.score = gameScore;

                // Teraz wykonaj zapytanie POST, aby zapisac wynik uzytkownika
                return fetch(`${SERVER_URI}/snake.php`, {
                    method: "POST",
                    body: JSON.stringify(userEntity),
                    headers: {
                        "Content-Type": "application/json"
                    }
                });
            })
            .then((postResponse) => postResponse.json())
            .then((postResult) => {
                console.log(postResult);
                // Sprawdzamy, czy success jest true w odpowiedzi
                if (postResult.success) {
                    gameInfo.text(postResult.message);
                    getUserScores();
                }
                else {
                  alert(postResult.message);
                }
            })
            .catch(error => console.error(error));
    }; // saveUserScore()

    const getUserScores = () => {
        fetch(
            `${SERVER_URI}/snake.php`, {
            method: "GET",
            headers: {
                "Content-Type": "application/json"
            }
        })
            .then((response) => response.json())
            .then((data) => {
                // Sprawdzamy, czy success jest true w odpowiedzi
                if (data.success) {
                    // Usuniecie poprzednich wierszy w tabeli wynikow
                    $("#snake-scores > tbody > tr").remove();
                    if (data.scores.length > 0) {
                        data.scores.forEach(user => {
                            // Dodawanie wiersza do tabeli
                            snakeScores.append(`<tr><td>${user.user_name}</td><td>${user.score}</td></tr>`);
                        });
                    }
                    else {
                        snakeScores.append("<tr><td>Brak wyników</td><td>NA</td></tr>");
                    }
                }
                else {
                    alert(data.message);
                }
            })
            .catch(error => console.error(error));
    } // getUserScores()

    const generateObstacles = (count) => {
        while (obstacles.length < count) {
            const obstacle = {
                x: Math.floor(Math.random() * canvasWidth/gridSize) * gridSize,
                y: Math.floor(Math.random() * canvasHeight/gridSize) * gridSize,
            };

            // Sprawdzenie czy przeszkoda nie pojawi sie na wezu lub jedzeniu
            if (
                (snake.some(segment => segment.x === obstacle.x && segment.y === obstacle.y)) ||
                (food && food.x === obstacle.x && food.y === obstacle.y) ||
                (goldenFood && goldenFood.x === obstacle.x && goldenFood.y === obstacle.y)
            ) {
                continue; // Jesli koliduje, sprobuj ponownie
            }
            obstacles.push(obstacle);
        } // while
    } // generateObstacles()

    const drawObstacles = () => {
        ctx.shadowBlur = 5;
        obstacles.forEach(obstacle => {
            ctx.fillStyle = "#8B0000";
            ctx.shadowColor = "#8B0000";
            ctx.fillRect(obstacle.x, obstacle.y, gridSize, gridSize);
        });
        ctx.shadowBlur = 0; // Reset cienia
    };

    const checkObstacleCollision = () => {
        const head = snake[0];
        if (obstacles.some(obstacle => head.x === obstacle.x && head.y === obstacle.y)) {
            endGame();
        }
    };

    const generateTeleports = () => {
        while (teleports.length < 2) {
            const teleport = {
                x: Math.floor(Math.random() * canvasWidth/gridSize) * gridSize,
                y: Math.floor(Math.random() * canvasHeight/gridSize) * gridSize,
            };

            // Sprawdzenie czy teleport nie pojawi sie na wezu, jedzeniu lub przeszkodzie
            if (
                (teleports.some(t => t.x === teleport.x && t.y === teleport.y)) ||
                (snake.some(segment => segment.x === teleport.x && segment.y === teleport.y)) ||
                (food && food.x === teleport.x && food.y === teleport.y) ||
                (goldenFood && goldenFood.x === teleport.x && goldenFood.y === teleport.y) ||
                (obstacles.some(obstacle => obstacle.x === teleport.x && obstacle.y === teleport.y))
            ) {
                continue; // Jesli koliduje, sprobuj ponownie
            }
            teleports.push(teleport);
        }
    }; // generateTeleports()

    const drawTeleports = () => {
        ctx.shadowBlur = 15; // Efekt świecenia
        teleports.forEach(teleport => {
            ctx.fillStyle = "#800080";
            ctx.shadowColor = "#800080";
            ctx.fillRect(teleport.x, teleport.y, gridSize, gridSize);
        });
        ctx.shadowBlur = 0; // Reset cienia
    };

    // Funkcja obsługująca kolizję z teleportami
    const checkTeleportCollision = () => {
        const head = snake[0];

        // Sprawdzanie, czy głowa węża dotknęła jednego z teleportów
        const teleportIndex = teleports.findIndex(teleport => teleport.x === head.x && teleport.y === head.y);
        if (teleportIndex !== -1) {
            // Przenieś węża do drugiego teleportu
            const otherTeleportIndex = teleportIndex === 0 ? 1 : 0;
            snake[0].x = teleports[otherTeleportIndex].x;
            snake[0].y = teleports[otherTeleportIndex].y;
        }
    }; // checkTeleportCollision()


    // Obsluga zdarzenia klawiatury
    $(document).on("keydown", (e) => {
        if (!isGameOver) {
            switch (e.key) {
                case "ArrowUp":
                    e.preventDefault(); // Zatrzymanie domsylnego przewijania strony
                    if (direction !== "DOWN") {
                        direction = "UP";
                    }
                    break;
                case "ArrowDown":
                    e.preventDefault();
                    if (direction !== "UP") {
                        direction = "DOWN";
                    }
                    break;
                case "ArrowLeft":
                    e.preventDefault();
                    if (direction !== "RIGHT") {
                        direction = "LEFT";
                    }
                    break;
                case "ArrowRight":
                    e.preventDefault();
                    if (direction !== "LEFT") {
                        direction = "RIGHT";
                    }
                    break;
                case "r":
                case "R":
                    // Restart w trakcie gry
                    resetGame();
                    break;
                case "e":
                case "E":
                    endGame();
                    break;
                case "p":
                case "P":
                    isPaused = !isPaused; // Wlacz/wylacz pauze
                    gameInfo.empty();
                    if (isPaused) {
                        gameInfo.text("Gra zapauzowana! Wciśnij P, aby kontynuować");
                        clearInterval(goldenFoodTimeout);
                    }
                    else {
                        scheduleNextGoldenFood();
                    }
                    e.preventDefault();
                    break;

            } // switch
        } // if !isGameOver
        else if (e.key === "r" || e.key === "R") {
            resetGame();
        }
        else if (e.key === "h" || e.key === "H") {
            saveUserScore();
        }
    }); // keydown event

    resetGame();
});