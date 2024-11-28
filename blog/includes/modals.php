<script src="../js/modal.js"></script>

<div id="auth-modal" class="modal">
    <div class="modal-content">
        <span id="close-modal" accesskey="&#27">&times;</span>
        <!-- Formularz logowania -->
        <div id="login-container" class="auth-form">
            <h2>Zaloguj się</h2>
            <form action="../db/mysql-operation.php" id="login-form" name="login" method="post">
                <input type="hidden" value="loginUser" name="action">

                <label for="login-username">Nazwa użytkownika:</label>
                <input type="text" id="login-username" name="username" minlength="4" required>
                <span class="error-message"></span>

                <label for="login-password">Hasło:</label>
                <input type="password" id="login-password" name="password" minlength="8" required>
                <span class="error-message"></span>

                <button type="submit">Zaloguj się</button>
            </form>
            <p class="toggle-auth">Utwórz konto: <a href="#">Zarejestruj się</a></p>

        </div>
        <?php
//        echo "julzaw: " . password_hash("julzaw", PASSWORD_DEFAULT) . "<br>";
//        echo "amawoz: " . password_hash("amawoz", PASSWORD_DEFAULT) . "<br>";
//        echo "korcza: " . password_hash("korcza", PASSWORD_DEFAULT) . "<br>";
//        echo "ariprz: " . password_hash("ariprz", PASSWORD_DEFAULT) . "<br>";
//        echo "olabor: " . password_hash("olabor", PASSWORD_DEFAULT) . "<br>";
        ?>

        <!-- Formularz rejestracji -->
        <div id="register-container" class="auth-form" style="display: none;">
            <h2>Zarejestruj się</h2>
            <form action="../db/mysql-operation.php" id="register-form" name="register-user" method="post">
                <input type="hidden" value="registerUser" name="action">
                <input type="hidden" value="user" name="role">

                <label for="reg-username">Nazwa użytkownika:</label>
                <input type="text" id="reg-username" name="username" required placeholder="Wprowadź nazwę użytkownika (min. 4 znaki)" minlength="4">
                <span class="error-message"></span>

                <label for="reg-email">Email:</label>
                <input type="email" id="reg-email" name="email" required  placeholder="np. email@example.com">
                <span class="error-message"></span>

                <label for="reg-password">Hasło:</label>
                <input type="password" id="reg-password" name="password" required minlength="8" placeholder="Min. 8 znaków">
                <span class="error-message"></span>

                <label for="confirm-password">Potwierdź hasło:</label>
                <input type="password" id="confirm-password" name="confirm-password" required minlength="8" placeholder="Wprowadź ponownie hasło">
                <span class="error-message"></span>

                <label for="about">O mnie</label>
                <textarea name="about" id="about" cols="30" rows="3" placeholder="Wpisz kilka zdań o sobie (nieobowiązkowe)"></textarea>

                <button type="submit">Zarejestruj się</button>
            </form>
            <p class="toggle-auth">Masz już konto? <a href="#">Zaloguj się</a></p>
        </div>

    </div>
</div>
