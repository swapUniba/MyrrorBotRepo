<?php
$servername = "sql212.epizy.com";
$username = "epiz_23987663";
$password = "xJodtMmqrsm";
$db = "epiz_23987663_MyrrorDb";

// Create connection
$conn = new mysqli($servername, $username, $password, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

//Recupero il nome e la password inseriti dall'utente
$email      = trim($_POST['email']);
$password  = trim($_POST['password']);
    
$sql = "SELECT * FROM Login";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        $emailR = $row["email"];
        $passwordR = $row["password"];

        if($email == $emailR && $password == $passwordR){
               $messaggio = 'LoginOk';
               echo $messaggio;
        }
    }

    if(!$messaggio){
        $messaggio = 'LoginNo';
        echo $messaggio;
    }
} else {
    echo "0 results";
}
$conn->close();


?>

					