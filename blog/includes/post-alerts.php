<?php if (isset($_SESSION["addPostAlert"]) && $_SESSION["addPostAlert"]["result"]): ?>
    <div class="alert alert-success">
        <strong>Sukces!</strong> Dodano nowy post
    </div>
    <?php
        unset($_SESSION["addPostAlert"]);
    endif ?>

<?php if (isset($_SESSION["addPostAlert"]) && !$_SESSION["addPostAlert"]["result"]): ?>
    <div class="alert alert-danger">
        <strong>Błąd!</strong> <?php echo $_SESSION["addPostAlert"]["error"] ?>
    </div>
    <?php
        unset($_SESSION["addPostAlert"]);
    endif ?>