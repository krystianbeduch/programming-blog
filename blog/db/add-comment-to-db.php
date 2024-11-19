<?php
try {
    $postId = $_POST['post-id'];
    $nick = $_POST['nick'];
    $email = $_POST['email'];
    $content = $_POST['comment'];
    $conn = new mysqli("localhost", "root", "", "blog");
    $query = "INSERT INTO posts (topic, nick, email, comment) VALUES              ('${topic}', '${nick}', '${email}', '${comment}')";
//    echo $query;
    $result = $conn->query($query);
//    echo $result;

    $query = "SELECT * FROM posts";
    $result = $conn->query($query);
    while ($row = $result->fetch_assoc()) {
        echo $row['topic'] . " ";
        echo $row['nick'] . " ";
        echo $row['email'] . " ";
        echo $row['comment'] . " ";
        echo "<br>";
    }
    $conn->close();
}
catch (mysqli_sql_exception $e) {
    echo "Błąd połączenia z bazą: ".$e->getMessage();
}
catch (Exception $e) {
    echo "Błąd: " . $e->getMessage();
}



//    echo $conn->connect_error;




?>