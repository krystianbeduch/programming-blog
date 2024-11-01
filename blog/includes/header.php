<script src="../js/modal.js"></script>

<header>
    <h1>Blog programistyczny</h1>
    <a href="#" class="login-link" onclick="openLoginModal()">Zaloguj się</a>
</header>


<div id="auth-modal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeAuthModal()">&times;</span>
        <!-- Formularz logowania -->
        <div id="login-container" class="auth-form">
            <h2>Zaloguj się</h2>
            <form action="" id="login-form" name="login">
                <label for="username">Nazwa użytkownika:</label>
                <input type="text" id="username" name="username" required>

                <label for="password">Hasło:</label>
                <input type="password" id="password" name="password" required>

                <button type="submit">Zaloguj się</button>
            </form>
            <p class="toggle-auth">Utwórz konto: <a href="#" onclick="showRegisterForm()">Zarejestruj się</a></p>
        </div>

        <!-- Formularz rejestracji -->
        <div id="register-container" class="auth-form" style="display: none;">
            <h2>Zarejestruj się</h2>
            <form action="" id="register-form" name="register">
                <label for="reg-username">Nazwa użytkownika:</label>
                <input type="text" id="reg-username" name="username" required placeholder="Wprowadź nazwę użytkownika" minlength="4">

                <label for="reg-email">Email:</label>
                <input type="email" id="reg-email" name="email" required  placeholder="np. email@example.com">

                <label for="reg-password">Hasło:</label>
                <input type="password" id="reg-password" name="password" required minlength="8" placeholder="Min. 8 znaków">

                <label for="confirm-password">Potwierdź hasło:</label>
                <input type="password" id="confirm-password" name="confirm-password" required minlength="8" placeholder="Wprowadź ponownie hasło">

                <button type="submit">Zarejestruj się</button>
            </form>
            <p class="toggle-auth">Masz już konto? <a href="#" onclick="showLoginForm()">Zaloguj się</a></p>
        </div>

    </div>
</div>
