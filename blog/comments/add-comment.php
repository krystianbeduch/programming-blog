<?php
session_start();
$_SESSION["errors"] = [];
$_SESSION["formData"] = $_POST;

    if (isset($_POST["nick"]) && isset($_POST["email"]) && isset($_POST["comment"])) {
        $url = $_POST["url"];
        if (allInputsFilled()) {
            if (emailValidation($_POST["email"])) {
                $_SESSION['errors']["email"] = "Email niepoprawny";
                header("Location:".$url);
                exit();
            }

            echo "Dodano komentarz<br><br>";
            $topic = $_POST["topic"];
            $nick = $_POST["nick"];
            $email = $_POST["email"];
            $comment = $_POST["comment"];

            echo "Temat: ".$topic."<br>";
            echo "Nick: ".$nick."<br>Email: ".$email."<br>Comment: ".$comment;
        }
        else {
            if (empty($_POST["nick"])) {
                $_SESSION['errors']["nick"] = "Nick jest wymagany";
            }
            if (empty($_POST["email"])) {
                $_SESSION['errors']["email"] = "Email jest wymagany";
            }
            if (empty($_POST["comment"])) {
                $_SESSION['errors']["comment"] = "Komentarz jest wymagany";
            }
            header("Location:".$url);
            exit();
        }
    }

    function allInputsFilled(): bool {
        return !empty($_POST["nick"]) && !empty($_POST["email"]) && !empty($_POST["comment"]);
    }

    function emailValidation($email): bool {
        $pattern = "/^([a-zA-Z0-9]{1,})@([a-zA-Z0-9]{2,10})\\.(pl|com)$/";
        return !preg_match($pattern, $email);
    }

    // Przetwarzanie BBCode
//    $comment = convertBBCodeToHTML($_POST["comment"]);
?>
