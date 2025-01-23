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
3. [Logical database schema](#logical-database-schema)
4. [Setup](#setup)
5. [Functional description](#functional-description)
   - [Registration and login](#registration-and-login)
   - [Posts and comments](#posts-and-comments)
   - [Manage posts and comments](#manage-posts-and-comments)
   - [Contact form](#contact-form)
   - [Games](#games)
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
4. In the `db` directory, create a `db-connect.php` file and define a configuration class for the database. The database name should match the database created in previous step with the imported data:
```php
class MySQLConfig {
  public const SERVER = "database_addreess";
  public const USER = "username";
  public const PASSWORD = "password";
  public const DATABASE = "database_name";
}
```
5. Correct the URI path to the server in the `js/config.js` file to one that matches your server structure. For example, if you put the `blog` directory directly in `htdocs` then the correct URI would be:
```javascript
export const SERVER_URI = "/blog/db/api";
```

## Functional description
### Registration and login
Users can register an account by filling out a registration form, triggered by pressing the `Zalouj się` (Login) button in the upper right corner of the page and then goin to `Zarejestruj się` (Register). 

Registration is secured by a simple mathematical CAPTCHA, consisting of solving a verbal mathematical operation (the answer is not case-sensitive and does not distinguish between Polish diacritics).<br>
<img src="https://github.com/krystianbeduch/programming-blog/blob/main/blog/images/readme-screenshots/users/modal-captcha-math.png" alt="Modal captcha math" title="Modal captcha math">

Before creating an account, the availability of a username and email address is verified.<br>
<img src="https://github.com/krystianbeduch/programming-blog/blob/main/blog/images/readme-screenshots/users/modal-register.png" alt="Register form" title="Register form">

The newly created account is initially inactive, it must be activated by the administrator in the user management panel. Only after the administrator activates the account can the user log in.<br>
<img src="https://github.com/krystianbeduch/programming-blog/blob/main/blog/images/readme-screenshots/users/alert-register.png" alt="Alert register" title="Alert register">

Login form:<br>
<img src="https://github.com/krystianbeduch/programming-blog/blob/main/blog/images/readme-screenshots/users/modal-login.png" alt="Login form" title="Login form">

When loggin in, it is immediately checked whether a user with the given name exists, if so, a login attempt is made, after which the following scenarios may occur:
- inactive account <br> <img src="https://github.com/krystianbeduch/programming-blog/blob/main/blog/images/readme-screenshots/users/alert-login-error-account-is-not-active.png" alt="Alert login error account is not active" title="Alert login error account is not active">
- wrong password <br> <img src="https://github.com/krystianbeduch/programming-blog/blob/main/blog/images/readme-screenshots/users/alert-login-error-wrong-password.png" alt="Alert login error wrong password" title="Alert login error wrong password">
- correct login <br> <img src="https://github.com/krystianbeduch/programming-blog/blob/main/blog/images/readme-screenshots/users/alert-login-success.png" alt="Alert login success" title="Alert login success">

### Posts and comments
<img src="https://github.com/krystianbeduch/programming-blog/blob/main/blog/images/readme-screenshots/posts-comments/java-posts.png" alt="Java posts" title="Java posts">

Users can view posts from the selected category and add comment to them. Comments can also be added by non-logged-in users. Logged in users can:
- Create posts in the selected category
- Manage their posts (editing, deleting)
- Delete their comments
The displayed posts can be filtered by date - a specific day or a time period.<br>
<img src="https://github.com/krystianbeduch/programming-blog/blob/main/blog/images/readme-screenshots/posts-comments/java-posts-date.png" alt="Java posts date filter" title="Java posts date filter">

A post archive is also available, where you can view posts from all categories from a selected month.<br>
<img src="https://github.com/krystianbeduch/programming-blog/blob/main/blog/images/readme-screenshots/posts-comments/post-archive.png" alt="Post archive" title="Post archive">

The forms for adding and comments have been secured by a graphical CAPTCHA that relies on selecting the correct figure<br>
<img src="https://github.com/krystianbeduch/programming-blog/blob/main/blog/images/readme-screenshots/posts-comments/add-comment.png" alt="Add comment" title="Add comment">

The content of posts and comments supports BBCode - a message formatting language. The following options are available:
- bold
- bold
- underline
- strikethrough
- unordered list
- quote
- link
- HTML tag used explicitly <br>
<img src="https://github.com/krystianbeduch/programming-blog/blob/main/blog/images/readme-screenshots/posts-comments/content-bbcode.png" alt="Content BBCode" title="Content BBCode">

You can also add an attachment to your posts in the form of an image file, the supported formats are jpg, jpeg, png, gif, bmp and svg, and the maximum size of the attachment is 5MB. Attachments are stored in the database as binary data (BLOB).<br>
<img src="https://github.com/krystianbeduch/programming-blog/blob/main/blog/images/readme-screenshots/posts-comments/add-post.png" alt="Add post" title="Add post">

Before adding a post or comment, users can check your post to verify the correct formatting of BBCode text. The exception is HTML tags, which are written using the characters &amp;&lt; and &amp;&gt;<br>
<img src="https://github.com/krystianbeduch/programming-blog/blob/main/blog/images/readme-screenshots/posts-comments/add-comment-preview.png" alt="Add comment preview" title="Add comment preview">

### Manage posts and comments
Users can manage thir posts by selecting `Zarządzaj postami` (Manage posts) in the drop-down menu.<br>
<img src="https://github.com/krystianbeduch/programming-blog/blob/main/blog/images/readme-screenshots/posts-comments/user-account-menu.png" alt="User account menu" title="User account menu">

Users can preview their posts, go directly to a post, edit it in a dedicateted form or delete it.<br>
<img src="https://github.com/krystianbeduch/programming-blog/blob/main/blog/images/readme-screenshots/posts-comments/user-posts.png" alt="User posts" title="User posts">

There is also a table with posting statistics in an abbreviated form.<br>
<img src="https://github.com/krystianbeduch/programming-blog/blob/main/blog/images/readme-screenshots/posts-comments/user-posts-stats.png" alt="User posts table stats" title="User posts table stats">

In the post editing form, the user selects what he wants to change through buttons, and can also manage the graphic attachment.<br>
<img src="https://github.com/krystianbeduch/programming-blog/blob/main/blog/images/readme-screenshots/posts-comments/edit-post.png" alt="Edit post" title="Edit post">

The fields that have been changed are marked in blue font color.<br>
<img src="https://github.com/krystianbeduch/programming-blog/blob/main/blog/images/readme-screenshots/posts-comments/edit-post-changes-made.png" alt="Edit post changes made" title="Edit post changes made">

Deleting a post is done directly through modal windows called on the post.<br>
<img src="https://github.com/krystianbeduch/programming-blog/blob/main/blog/images/readme-screenshots/posts-comments/delete-post.png" alt="Delete post" title="Delete post">

Editing of account data by a user is similar to editing a post. When editing a username or email, its availability is checked.<br>
<img src="https://github.com/krystianbeduch/programming-blog/blob/main/blog/images/readme-screenshots/posts-comments/edit-profile.png" alt="Edit profile" title="Edit profile">

### Administration panel
The administration panel is available for administrators to manage posts and users. The panel is visible only to users with appropriate permissions.<br>
<img src="https://github.com/krystianbeduch/programming-blog/blob/main/blog/images/readme-screenshots/admin/admin-aside.png" alt="Admin panel on aside" title="Admin panel on aside"><br>
<img src="https://github.com/krystianbeduch/programming-blog/blob/main/blog/images/readme-screenshots/admin/admin-user-stats.png" alt="Admin user stats" title="Admin user stats">

Attempts by users without administrative privileges to access the panel via the URL end up redirecting to a 401 error page.

In the user management section, the administrator has access to a full list of users, which can be managed by:
- Editing users <br><img src="https://github.com/krystianbeduch/programming-blog/blob/main/blog/images/readme-screenshots/admin/admin-edit-user.png" alt="Admin edit user" title="Admin edit user">
- Change account activity - this operation is possible by clicking the `Aktywność` (Acitivity) column on the selected user <br> <img src="https://github.com/krystianbeduch/programming-blog/blob/main/blog/images/readme-screenshots/admin/admin-change-user-activity.png" alt="Admin change user activity" title="Admin change user activity">
- However, the administrator cannot change the activity ofhis own account <br><img src="https://github.com/krystianbeduch/programming-blog/blob/main/blog/images/readme-screenshots/admin/admin-users-activity-locked.png" alt="Admin users activity locked" title="Admin users activity locked">
- Deleting a user account <br><img src="https://github.com/krystianbeduch/programming-blog/blob/main/blog/images/readme-screenshots/admin/admin-delete-user.png" alt="Admin delete user" title="Admin delete user">

In the post management section, the administrator has access to all posts from all categories.<br>
<img src="https://github.com/krystianbeduch/programming-blog/blob/main/blog/images/readme-screenshots/admin/admin-all-posts.png" alt="Admin all posts" title="Admin all posts">

The preview can be narrowed down to a selected category.<br>
<img src="https://github.com/krystianbeduch/programming-blog/blob/main/blog/images/readme-screenshots/admin/admin-posts-selected-category.png" alt="Admin posts selected category" title="Admin posts selected category">

The administrator can delete posts directly from the panel or while on the post page – he can also delete comments there.<br>
<img src="https://github.com/krystianbeduch/programming-blog/blob/main/blog/images/readme-screenshots/admin/post-with-admin-options.png" alt="Post with admin options" title="Post with admin options">

### Contact form
The blog has a contact form that allows you to send a message to the blog author. This component requires a properly configured SMTP server to function properly. The project uses `sendmail`, which is available in the XAMPP package.

Configuration for XAMPP:

`php.ini` file:
```ini
[mail function]
SMTP = smtp.gmail.com 					# SMTP server address
smtp_port = 587 					# SMTP server port number for TLS connections
sendmail_from = mail@gmail.com 				# sender address
sendmail_path = \"C:\xampp\sendmail\sendmail.exe\" -t"	# path to sendmail program
```

`sendmail.ini` file:
```ini
[sendmail]
smtp_server = smtp.gmail.com 	# SMTP server address
smtp_port = 587 		# SMTP server port number for TLS connections
auth_username = mail@gmail.com 	# sender address
auth_password = password 	# application password
```

### Games
For blog users there are also games available:<br>
<img src="https://github.com/krystianbeduch/programming-blog/blob/main/blog/images/readme-screenshots/games/games.png" alt="Games" title="Games"><br>
<table>
    <tr>
        <td><img src="https://github.com/krystianbeduch/programming-blog/blob/main/blog/images/readme-screenshots/games/blackjack.png" alt="Blackjack" title="Black" height="700"></td>
        <td><img src="https://github.com/krystianbeduch/programming-blog/blob/main/blog/images/readme-screenshots/games/snake.png" alt="Snake" title="Snake" height="700"></td>
    </tr>
	<td>Blackjack 🠉</td>
	<td>Snake 🠉</td>
    <tr>
        <tr>
        <td><img src="https://github.com/krystianbeduch/programming-blog/blob/main/blog/images/readme-screenshots/games/whack-a-mole.png" alt="Whack A Mole" title="Whack A Mole" height="700"></td>
        <td><img src="https://github.com/krystianbeduch/programming-blog/blob/main/blog/images/readme-screenshots/games/drag-racers.png" alt="Drag Racers" title="Drag Racers" height="700"></td>
    </tr>
	<td>Whack A Mole 🠉</td>
	<td>Drag Racers 🠉</td>
    <tr>
</table>



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

