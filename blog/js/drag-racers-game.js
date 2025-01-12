$(document).ready(() => {
    const canvas = $("canvas")[0];
    const ctx = canvas.getContext("2d");
    const canvasWidth = canvas.width;
    const canvasHeight = canvas.height;
    const roadWidth = canvasWidth / 2;
    const progress1Bar = $("#car1-progress");
    const progress2Bar = $("#car2-progress");

    let cars, obstacles, carWinColor, isGameOver, gameInterval;
    const obstacleSpeed = 2;
    const obstacleFrequency = 700;
    const availableKeys = [
        "ArrowUp", "ArrowDown", "ArrowLeft", "ArrowRight",
        "W", "S", "A", "D", "w", "s", "a", "d"
    ];

    // Klawisze aktywne
    let pressedKeys;

    const startGameInfo = () => {
        isGameOver = true; // Poczatkowo gra zatrzymana
        ctx.fillStyle = "#fff"; // Kolor tekstu
        ctx.font = "20px Arial"; // Czcionka
        ctx.textAlign = "center"; // Wyrównanie do lewej
        ctx.fillText("Aby rozpocząć kliknij Enter", canvasWidth / 2, canvasHeight / 2); // Wyświetlenie tekstu
    }

    startGameInfo();

    $(document).on("keydown", (e) => {
        if (isGameOver && e.key === "Enter") {
            e.preventDefault();
            startGame();
        }
        if (availableKeys.includes(e.key)) {
            e.preventDefault();
            pressedKeys.add(e.key);
        }
    });

    $(document).on("keyup", (e) => {
        if (availableKeys.includes(e.key)) {
            e.preventDefault();
            pressedKeys.delete(e.key);
        }
    });

    const startGame = () => {
        cars = [
            {
                x: roadWidth / 2 - 25, y: canvasHeight - 120, width: 50, height: 100,
                color: "#db0f27", velocityX: 0, velocityY: 0, isDelayed: false
            },
            {
                x: roadWidth + roadWidth / 2 - 25, y: canvasHeight - 120, width: 50, height: 100,
                color: "#6693f5", velocityX: 0, velocityY: 0, isDelayed: false
            }
        ];
        progress1Bar.val(0);
        progress2Bar.val(0);
        isGameOver = false;
        obstacles = [];
        pressedKeys = new Set();
        clearInterval(gameInterval);
        gameInterval = setInterval(generateObstacle, obstacleFrequency);
        gameLoop();
    }; // startGame()

    const handleMovement = () => {
        // Pojazd 1
        if (pressedKeys.has("ArrowUp"))
            cars[0].velocityY = -2;
        else if (pressedKeys.has("ArrowDown"))
            cars[0].velocityY = 2;
        else
            cars[0].velocityY = 0;

        if (pressedKeys.has("ArrowLeft"))
            cars[0].velocityX = -2;
        else if (pressedKeys.has("ArrowRight"))
            cars[0].velocityX = 2;
        else
            cars[0].velocityX = 0;

        // Aktualizacja pozycji
        cars[0].x += cars[0].velocityX;
        cars[0].y += cars[0].velocityY;

        // Pojazd 2
        if (pressedKeys.has("w") || pressedKeys.has("W"))
            cars[1].velocityY = -2;
        else if (pressedKeys.has("s") || pressedKeys.has("S"))
            cars[1].velocityY = 2;
        else
            cars[1].velocityY = 0;

        if (pressedKeys.has("a") || pressedKeys.has("A"))
            cars[1].velocityX = -2;
        else if (pressedKeys.has("d") || pressedKeys.has("D"))
            cars[1].velocityX = 2;
        else
            cars[1].velocityX = 0;

        // Aktualizacja pozycji
        cars[1].x += cars[1].velocityX;
        cars[1].y += cars[1].velocityY;

        // Zabepieczenie przed wyjechaniem za plansze
        cars.forEach(car => {
            car.x = Math.max(0, Math.min(canvas.width - car.width, car.x));
            car.y = Math.max(0, Math.min(canvas.height - car.height, car.y));
        });
    }; // handleMovement()

    const drawRect = (x, y, width, height, color) => {
      ctx.fillStyle = color;
      ctx.fillRect(x, y, width, height);
    };

    const generateObstacle = () => {
        let x = Math.round(Math.random() * (canvas.width - 50));
        obstacles.push({x, y: -50, width: 50, height: 50, color: "#f81894"});
    };

    const drawObstacles = () => {
        obstacles.forEach((obstacle, index) => {
            obstacle.y += obstacleSpeed;

            // Usun przeszkode, jesli jest poza ekranem
            if (obstacle.y > canvas.height) {
                obstacles.splice(index, 1);
            }
            drawRect(obstacle.x, obstacle.y, obstacle.width, obstacle.height, obstacle.color);
        });
    }; // drawObstacles()

    const checkCollisions = () => {
        cars.forEach(car => {
            obstacles.forEach((obstacle, index) => {
                if (
                    car.x < obstacle.x + obstacle.width &&
                    car.x + car.width > obstacle.x &&
                    car.y < obstacle.y + obstacle.height &&
                    car.y + car.height > obstacle.y
                ) {
                    // Postep auta zatrzymany na sekunde
                    car.isDelayed = true;
                    obstacles.splice(index, 1);
                    setTimeout(() => car.isDelayed = false, 1000);
                }
            }); // obstacles
        }); // cars
    }; // checkCollisions()

    const updateProgressBars = () => {
        if (!cars[0].isDelayed) {
            progress1Bar.val(Math.min(progress1Bar.val() + 0.05, 100));
        }
        if (!cars[1].isDelayed) {
            progress2Bar.val(Math.min(progress2Bar.val() + 0.05, 100));
        }
    }; // updateProgressBars()

    const drawRoad = () => {
        ctx.fillStyle = "#333";
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        // Linie pasow
        ctx.strokeStyle = "#fff";
        ctx.lineWidth = 5;
        ctx.setLineDash([20, 15]);
        ctx.beginPath();
        ctx.moveTo(roadWidth, 0);
        ctx.lineTo(roadWidth, canvas.height);
        ctx.stroke();
        ctx.setLineDash([]);
    }; // drawRoad()

    const checkWin = () => {
        if (progress1Bar.val() >= 100) {
            carWinColor = cars[0].color;
            resetGame("czerwone");
        }
        if (progress2Bar.val() >= 100) {
            carWinColor = cars[1].color;
            resetGame("niebieskie");
        }
    }; // checkWin()

    const resetGame = (winner) => {
        isGameOver = true;
        ctx.clearRect(0, 0, canvasWidth, canvasHeight);
        ctx.fillStyle = carWinColor;
        ctx.font = "20px Arial";
        ctx.textAlign = "center";
        ctx.fillText(`Wygrało auto ${winner}!`, canvasWidth / 2, (canvasHeight / 2) - 30);
        ctx.fillText("Naciśnij Enter aby rozpocząć na nowo", canvasWidth / 2, (canvasHeight / 2));
    }; // resetGame()

    $(document).on("keydown", (e) => {
        if (isGameOver && e.key === "Enter") {
            startGame();
        }
    });

    const gameLoop = () => {
        if (!isGameOver) {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            drawRoad();
            handleMovement();
            cars.forEach(car => drawRect(car.x, car.y, car.width, car.height, car.color));
            drawObstacles();
            checkCollisions();
            updateProgressBars();
            checkWin();
            requestAnimationFrame(gameLoop);
        }
    }; // gameLoop()
});