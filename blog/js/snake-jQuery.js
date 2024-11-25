$(document).ready(function () {
    const canvas = $("#game-canvas")[0]; // Pobieranie elementu canvas
    const ctx = canvas.getContext("2d"); // Kontekst 2D do rysowania
    const canvasWidth = canvas.width;
    const canvasHeight = canvas.height;
    const gridSize = 20; // Wielkosc jednej komorki siatki
    const gameInfo = $("#game-info");

    let snake, direction, gameInterval, isGameOver, isPaused, score;
    let gameSpeed, goldenFoodTimeout;
    let food = {};
    let goldenFood = {}
    let goldenFoodDelay = getRandomGoldenFoodDelay();

    // Funkcja do resetowania gry
    const resetGame = () => {
        //  Poczatkowa pozycja weza
        //  Waz to tablica segmentow, kazdy segment to obiekt {x, y}
        snake = [{ x: 0, y: 0 }]; //
        direction = "RIGHT"; // Początkowy kierunek
        isGameOver = false; // Zresetowanie stanu konca gry
        gameSpeed = 200; // Poczatkowa predkosc gry w ms
        spawnFood() // Generowanie pierwszego jedzenia
        goldenFood = null; // Na poczatku nie ma zlotego jedzenia
        goldenFoodTimeout = setTimeout(() => {
            generateGoldenFood(); // Pierwsze zlote jedzenie po losowym czasie
        }, getRandomGoldenFoodDelay(5, 10));

        gameInfo.text(""); // Czyszczenie komunikatu
        score = 0; // Restowanie punktow
        clearInterval(gameInterval); // Zatrzymanie poprzedniej gry, jesli trwala
        gameInterval = setInterval(updateGame, gameSpeed); // Rozpoczecie nowej gry
    };


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

        // Dodanie nowej głowy na początku tablicy
        snake.unshift(head);

        // Usunięcie ostatniego segmentu węża (symuluje ruch)
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
        ctx.fillText(`Punkty: ${score}`, 10, canvasHeight - 10); // Wyświetlenie tekstu
    };

    // Koniec gry
    const endGame = () => {
        isGameOver = true;
        clearInterval(gameInterval);
        gameInfo.html(`Koniec gry! Uzyskałeś ${score} punktów. <br>Kliknij R aby rozpocząć nową grę`);
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
            score++;
            // Dodanie segmentu weza
            snake.push({ x: food.x, y: food.y });
            spawnFood();
            adjustGameSpeed();
        }

        // Zlote jedzenie
        if (goldenFood && head.x === goldenFood.x && head.y === goldenFood.y) {
            score += 3;
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
            checkSelfCollision();
            checkFoodCollision();
            drawSnake();
            drawFood();
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

    // Obsluga zdarzenia klawiatury
    $(document).keydown(function (e) {
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
                    gameInfo.text("");
                    if (isPaused) {
                        gameInfo.text("Gra zapauzowana! Wciśnij P, aby kontynuować");
                    }
                    e.preventDefault();
                    break;

            }
        }
        else if (e.key === "r" || e.key === "R") {
            resetGame();
        }

    });

    // Uruchomienie gry w interwale (ruch co 150ms)
    // gameInterval = setInterval(updateGame, 150);
    resetGame();
});