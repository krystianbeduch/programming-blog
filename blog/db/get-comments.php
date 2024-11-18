<?php
function getComments(string $topic) : array {
    try {
        $conn = new mysqli("localhost", "root", "", "blog");

        $query = "SELECT * FROM posts WHERE topic = '${topic}'";
        $result = $conn->query($query);
        $conn->close();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    catch (mysqli_sql_exception $e) {
        echo "Błąd połączenia z bazą: ".$e->getMessage();
        exit;
    }
    catch (Exception $e) {
        echo "Błąd: " . $e->getMessage();
        exit;
    }

}
getComments("php");





//    echo $conn->connect_error;




?>