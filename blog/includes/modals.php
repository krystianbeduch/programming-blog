<script src="../js/modal.js"></script>

<div id="auth-modal" class="modal">
    <div class="modal-content">
        <span id="close-modal" class="close-modal" accesskey="&#27">&times;</span>

        <!-- Formularz logowania -->
        <div id="login-container" class="auth-form">
            <h2>Zaloguj się</h2>
            <form action="../includes/forms.php" id="login-form" name="login" method="post" autocomplete="off">
                <input type="hidden" value="loginUser" name="action">
                <input type="hidden" name="url" value="<?= $_SERVER["REQUEST_URI"]; ?>">

                <label for="login-username">Nazwa użytkownika:</label>
                <input type="text" id="login-username" name="username" minlength="4" required>
                <span class="error-message"></span>

                <label for="login-password">Hasło:</label>
                <input type="password" id="login-password" name="password" minlength="6" required>
                <span class="error-message"></span>

                <button type="submit">Zaloguj się</button>
            </form>
            <p class="toggle-auth">Utwórz konto: <a href="#">Zarejestruj się</a></p>

        </div>

        <!-- Formularz rejestracji -->
        <div id="register-container" class="auth-form" style="display: none;">
            <h2>Zarejestruj się</h2>
            <form action="../includes/forms.php" id="register-form" name="register-user" method="post">
                <input type="hidden" value="registerUser" name="action">
                <input type="hidden" value="user" name="role">

                <label for="reg-username">Nazwa użytkownika:</label>
                <input type="text" id="reg-username" name="username" required placeholder="Min. 4 znaki" minlength="4">
                <span class="error-message"></span>

                <label for="reg-email">Email:</label>
                <input type="email" id="reg-email" name="email" required placeholder="np. email@example.com">
                <span class="error-message"></span>

                <label for="reg-password">Hasło:</label>
                <input type="password" id="reg-password" name="password" required minlength="6" placeholder="Min. 6 znaków">
                <span class="error-message"></span>

                <label for="confirm-password">Potwierdź hasło:</label>
                <input type="password" id="confirm-password" name="confirm-password" required minlength="6" placeholder="Wprowadź ponownie hasło">
                <span class="error-message"></span>

                <label for="about">O mnie</label>
                <textarea name="about" id="about" cols="30" rows="3" placeholder="Wpisz kilka zdań o sobie (nieobowiązkowe)"></textarea>

                <button type="button" id="captcha-button">Potwierdź, że jesteś człowiekiem</button>
                <button type="submit" id="register-submit" disabled>Zarejestruj się</button>
            </form>
            <p class="toggle-auth">Masz już konto? <a href="#">Zaloguj się</a></p>
        </div>

        <!-- Kontener na CAPTCHE -->
        <div id="captcha-modal" class="modal" style="display: none;">
            <div class="modal-content">
                <span id="close-modal-captcha" class="close-modal" accesskey="&#27">&times;</span>
                <p id="captcha-exercise"></p>
                <label for="captcha-exercise-answer">Wprowadź wynik słownie:</label>
                <input type="text" id="captcha-exercise-answer">
                <button id="captcha-verify">Zweryfikuj</button>
            </div>
        </div>

    </div>
</div>