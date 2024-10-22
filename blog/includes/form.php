<form id="add-comment-form" action="../comments/add-comment.php" method="post">
    <fieldset>
        <legend>Dodaj komentarz</legend>

<!--        <h4>Dodaj komentarz</h4>-->
        <input type="hidden" name="url" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
        <label for="topic">Temat:</label>
        <input type="text" name="topic" id="topic" value="HTML" readonly>

        <label for="nick">Nickname:</label>
        <input type="text" name="nick" id="nick">
        <span class="error" style="color: red;">
            <?php echo isset($_SESSION['errors']['nick']) ? $_SESSION['errors']['nick'] : ''; ?>
        </span>

        <label for="email">Email:</label>
        <input type="text" name="email" id="email">
        <span class="error" style="color: red;">
            <?php echo isset($_SESSION['errors']['email']) ? $_SESSION['errors']['email'] : ''; ?>
        </span>

        <label for="comment">Treść komentarza:</label>
        <textarea name="comment" id="comment"></textarea>
        <span class="error" style="color: red;">
            <?php echo isset($_SESSION['errors']['comment']) ? $_SESSION['errors']['comment'] : ''; ?>
        </span>

        <button type="submit">Dodaj komentarz</button>
    </fieldset>
</form>

<?php
unset($_SESSION['errors']);
?>
