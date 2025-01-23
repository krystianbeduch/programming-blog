# Programming Blog
The project was created as part of my IT studies as part of a course on the PHP language.

The application is a thematic blog about programming languages. 
The languages have been divided into several groups to allow an orderly presentation of some of their capabilities:
- Frontend: HTML, CSS, JavaScript, TypeScript
- Backend: PHP, Java, Python, C#, Ruby
- Low-level and embedded systems: Assembler, C, C++
- Databases: SQL
- Mobile devices: Kotlin, Swift

### Contents
1. [Application functionality](#application-functionality)
2. [Technology](#technology)
3. [LogicalDatabaseSchema](#logical-database-schema)
4. [Setup](#setup)
5. [Functional description](#functional-description)
   - [Setting the board dimensions](#setting-the-board-dimensions)
   - [Board generation](#board-generation)
   - [Smooth animation](#smooth-animation)
   - [Card reveal](#card-reveal)
   - [Game information](#game-information)
   - [Pause and finish the game](#pause-and-finish-the-game)
   - [Saving game scores](#saving-game-scores)
   - [List of top players](#list-of-top-players)
   - [Responsive design](#responsive-design)
   - [Unit test for API](#unit-test-for-api)
6. [Algorithm for generating a board with cards](#algorithm-for-generating-a-board-with-cards)

## Application functionality
- Accounts for users with access to the management panel of the user's account and posts. By default there are 5 accounts, the passwords for them are the same as their name:
  - julzaw
  - amawoz
  - korcza
  - ariprz
  - olabor - Administrator   
- Posts categorization system - division into programming languages
- Add, edit and delete posts for registered users 
- Comment system available for both registered and unregistered users
- Graphical editing of post content and comments through to a simple WYSIWYG editor (based on BBCode), supporting basic formatting tags
- Protection against bots through a custom CAPTCHA
- Add attachments to posts as image files
- Administrative accounts with access to the administration panel. Administrators can:
  - Delete posts and comments
  - Manage users, including:
    - Manage account activity status (active/inactive)
    - Edit data (including password) and permissions
    - Delete user accounts

## Technology
- Frontend:
  - HTML/CSS
  - JavaScript
  - jQuery 3.7.1
  - jQuery UI 1.14.1
  - Bootstrap 5.3.3
- Backend:
  - PHP 8.2
  - MariaDB 10.4.32 (MySQL) from the XAMPP package

## Logical database schema
![LogicalDatabaseSchema](https://github.com/krystianbeduch/programming-blog/blob/main/blog/db/schema-logical.png)

## Setup  
1. Install and configure a web server that supports PHP 8.2 and a MySQL/MariaDB database. You can use the XAMPP package for this
2. Clone or download the repository from Github:
```bash
git clone https://github.com/krystianbeduch/programming-blog.git
```
3. Create a MySQL database using the `create-table.sql` and `insert.sql` files available in `db/schemaSQL`. The name of the database is arbitrary.
4. In the `db` directory, create a `db-connect.php` file and define a configuration class for the database:
```php
class MySQLConfig {
  public const SERVER = "database_addreess";
  public const USER = "username";
  public const PASSWORD = "password";
  public const DATABASE = "database_name";
}
```
The database name should match the database created in previous step with the imported data.

5. Correct the URI path to the server in the `js/config.js` file to one that matches your server structure. For example, if you put the `blog` directory directly in `htdocs` then the correct URI would be:
```javascript
export const SERVER_URI = "/blog/db/api";
```

## Functional description
### Registration and login
Users can register an account by filling out a registration form, triggered by pressing the `Zalouj się` (Login) button in the upper right corner of the page and then goin to `Zarejestruj się` (Register). 

Registration is secured by a simple mathematical CAPTCHA, consisting of solving a verbal mathematical operation (the answer is not case-sensitive and does not distinguish between Polish diacritics). 
  
<img src="https://github.com/krystianbeduch/memory-game/blob/main/blog/images/readme-screenshots/users/modal-captcha-math.png" alt="Modal captcha math" title="Modal captcha math">

Before creating an account, the availability of a username and email address is verified.

<img src="https://github.com/krystianbeduch/memory-game/blob/main/blog/images/readme-screenshots/users/modal-register.png" alt="Register form" title="Register form">

The newly created account is initially inactive, it must be activated by the administrator in the user management panel. Only after the administrator activates the account can the user log in.

<img src="https://github.com/krystianbeduch/memory-game/blob/main/blog/images/readme-screenshots/users/alert-register.png" alt="Alert register" title="Alert register">

Login form:

<img src="https://github.com/krystianbeduch/memory-game/blob/main/blog/images/readme-screenshots/users/modal-login.png" alt="Login form" title="Login form">

When loggin in, it is immediately checked whether a user with the given name exists, if so, a login attempt is made, after which the following scenarios may occur:
- inactive account <br> <img src="https://github.com/krystianbeduch/memory-game/blob/main/blog/images/readme-screenshots/users/alert-login-error-account-is-not-active.png" alt="Alert login error account is not active" title="Alert login error account is not active">
- wrong password <br> <img src="https://github.com/krystianbeduch/memory-game/blob/main/blog/images/readme-screenshots/users/alert-login-error-wrong-password.png" alt="Alert login error wrong password" title="Alert login error wrong password">
- correct login <br> <img src="https://github.com/krystianbeduch/memory-game/blob/main/blog/images/readme-screenshots/users/alert-login-success.png" alt="Alert login success" title="Alert login success">









============================================================================================================================================================================================================
### Board generation
Each game generates a board with a random selection of images from the available 32, arranged in a random order.
<table>
    <tr>
        <td><img src="https://github.com/krystianbeduch/memory-game/blob/main/public/images/readme-screenshots/generate-board-first-example.png" alt="Generate board first example" title="Generate board first example" height="400"></td>
        <td><img src="https://github.com/krystianbeduch/memory-game/blob/main/public/images/readme-screenshots/generate-board-second-example.png" alt="Generate board second example" title="Generate board second example" height="400"></td>
    </tr>
    <tr>
        <td><p>First example deck 🠉</p></td>
        <td><p>Second example deck 🠉</p></td>
    </tr>
</table>

### Smooth animation
Card selection is animated using the `react-spring` library

![CardAnimation](https://github.com/krystianbeduch/memory-game/blob/main/public/images/readme-screenshots/card-animation.gif)

### Card reveal
During the game, a player can only reveal 2 cards at a time. If the cards match, they remain uncovered, otherwise they turn over.

![RevealingCards](https://github.com/krystianbeduch/memory-game/blob/main/public/images/readme-screenshots/revealing-cards.gif)

### Game information
During the game, information about the number of moves made, points scored and game time is displayed. When the game is over, the final score is displayed.
<table>
    <tr>
        <td><img src="https://github.com/krystianbeduch/memory-game/blob/main/public/images/readme-screenshots/game-info.png" alt="Game info" title="Generate info" height="600"></td>
        <td><img src="https://github.com/krystianbeduch/memory-game/blob/main/public/images/readme-screenshots/game-info-end.png" alt="Game info end" title="Generate info end" height="600"></td>
    </tr>
    <tr>
        <td><p>Information during the game 🠉</p></td>
        <td><p>Information after the game 🠉</p></td>
    </tr>    
</table>

### Pause and finish the game
A player can pause the game. Game time is then stopped and the player cannot continue to reveal cards until the game resumes. There is also an option to end the game early, which results in a redraw of the deck.

### Saving game scores
After completing the game, the player has the option to save his game scores by entering his name in the appropriate field. The name should be between 3 and 50 characters. 
In case of an error (including a server error), the user will receive appropriate message. The scores are stored in a non-relational MongoDB database. The data is sent via an API.
<table>
    <tr>
        <td><img src="https://github.com/krystianbeduch/memory-game/blob/main/public/images/readme-screenshots/save-score.png" alt="Save score" title="Save score" height="700"></td>
        <td><img src="https://github.com/krystianbeduch/memory-game/blob/main/public/images/readme-screenshots/save-score-warning.png" alt="Save score - warning" title="Save score - warning" height="700"></td>
    </tr>
    <tr>
        <td><p>Save score 🠉</p></td>
        <td><p>Save score - warning 🠉</p></td>
    </tr>
    <tr>
        <td><img src="https://github.com/krystianbeduch/memory-game/blob/main/public/images/readme-screenshots/save-score-failed.png" alt="Save score - failed" title="Save score - failed" height="700"></td>
        <td><img src="https://github.com/krystianbeduch/memory-game/blob/main/public/images/readme-screenshots/save-score-successfully.png" alt="Save score - successfully" title="Save score - successfully" height="700"></td>
    </tr>
    <tr>
        <td><p>Save score - failed 🠉</p></td>
        <td><p>Save score - successfully 🠉</p></td>
    </tr>
    <tr>
        <td colspan="2"><img src="https://github.com/krystianbeduch/memory-game/blob/main/public/images/readme-screenshots/save-score-mongodb.png" alt="Save score - MongoDB" title="Save score - MongoDB"></td>
    </tr>
    <tr>
        <td colspan="2">Scores in MongoDB database 🠉</td>
    </tr>
</table>

### List of top players
Below the board there is a table with the results of the top 10 players. The results are sortedy by board size and the score achieved (number of moves, time). The table allows you to filter the results by board size.
<table>
    <tr><td><img src="https://github.com/krystianbeduch/memory-game/blob/main/public/images/readme-screenshots/best-scores-all.png" alt="All best scores" title="All best scores"></td></tr>
    <tr><td><p>Table with all results 🠉</p></td>
    <tr><td><img src="https://github.com/krystianbeduch/memory-game/blob/main/public/images/readme-screenshots/best-scores-4x4.png" alt="4x4 best scores" title="4x4 best scores"></td></tr>
    <tr><td><p>Table of 4x4 baord results 🠉</p></td>
</table>

### Responsive design
The app is adapted to smaller devices. At smaller screen resolutions, the tabs, buttons and table resize to maintain proper readability and usability.
<table>
    <tr>
        <td><img src="https://github.com/krystianbeduch/memory-game/blob/main/public/images/readme-screenshots/responsive-768.png" alt="Responsive 768" title="Responsive 768"></td>
        <td><img src="https://github.com/krystianbeduch/memory-game/blob/main/public/images/readme-screenshots/responsive-480.png" alt="Responsive 480" title="Responsive 480"></td>
    </tr>
    <tr>
        <td><p>Devices with a screen width of less than 786px 🠉</p></td>
        <td><p>Devices with a screen width of less than 480px 🠉</p></td>
    </tr>    
</table>

### Unit test for API
Unit tests have been created for GET and POST API operations to validate the correct execution of requests. Test cases include:
- Returning results from the database during a GET operation
- Inserting results to the database during a POST operation
- Returning HTTP 400 (Bad Request) status when attempting to send invalid data with a POST
  
![APIUnitTests](https://github.com/krystianbeduch/memory-game/blob/main/public/images/readme-screenshots/api-unit-tests.png)

## Algorithm for generating a board with cards
### 1. Passing data to the Board component in App.tsx
```typescript
<Board
    board={board}
    selectedCards={selectedCards}
    handleCardClick={handleCardClick}
    numRows={numRows}
    numCols={numCols}
/>
```
In the `App.tsx` file, the `Board` component is called and the followind data is passed to it:
- board - array of the cards to be displayed on the board
- selectedCards - array of the cards that have been clicked by the player
- handleCardClick - card click handler function that manages the card matching logic
- numRows and numCols - specify the number of rows and columns on the board

### 2. Rendering the Board component
```typescript
const Board: React.FC<BoardProps> = ({ board, selectedCards, handleCardClick, numRows, numCols }) => {

    const gridStyle: React.CSSProperties = {
        gridTemplateColumns: `repeat(${numCols}, 1fr)`,
        gridTemplateRows: `repeat(${numRows}, 1fr)`,
        width: `${numCols}${numCols}0px`,
    };

    return (
        <div className="board" style={gridStyle}>
            {board.map((card, index) => (
                <Card
                    key={card.id}
                    image={card.image}
                    isFlipped={selectedCards.includes(card) 
				|| card.isMatched}
                    isMatched={selectedCards.includes(card)}
                    onClick={() => handleCardClick(card)}
                    id={index}
                />
            ))}
        </div>
    );
};
```
The `Board` component receives the data and starts creating the board. A `gridStyle` object is created containing dynamic grid settings using CSS Grid, which adjusts the number of rows and columns and also the width of the board based on the data provided.

### 3. Mapping cards to card components
`board.map()` iterates through all the cards in the board array. The following properties are passed for each card:
- key={card.id} - a unique identifier for each card so that React can manage the DOM elements
- image={card.image} - the source of the card image
- isFlipped - a flag that determines whether the card is clicked of matched
- isMatched - a flag that determines whether the card has benn matched to a pair
- onClick={() => handleCardClick(card)} - function that is called when a card is clicked
- id={index} - card identifier, which is used to assign a card number in the code

### 4. Rendering the Card component
```typescript
const Card: React.FC<CardProps> = ({ id, image, isFlipped, isMatched, onClick }) => {

    const flip = useSpring({
        transform: isFlipped ? 'rotateY(180deg)' : 'rotateY(0deg)',
        config: { tension: 200, friction: 10 }
    })

    return (
        <div className="card-container" onClick={onClick}>
            <animated.div className="card" style={flip}>
                <div className="card-front">
                    <img src={image} alt={`Card ${id}`}/>
                </div>
                <div className="card-back">?</div>
            </animated.div>
        </div>
    );
};
```

The `Card` component is created for each card, which performs operations:
- Animation of card rotation - the `flip` variable is defined using `useSpring` from `react-spring` to animate the rotation of the card. This variable controls the card's transformation, setting it to rotated (`rotate(180deg)`) or non-rotated (`rotate(0deg)`). Animation is applied to the card's div element.
- Render the front and back of the card:
	- card-front - contains the image that is visible when the card is inverted
 	- card-back - contains a question mark (?), which is visible when the card is face-up
- Depending on whether the card is flipped, the animation changes its transformation.
