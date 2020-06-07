<head>
    <title>Teste MariaDB</title>
</head>
<?php
$host = "mariadb";
$username = "test";
$password = "dbpassword";
$db = "test";
try {
$conn = new PDO("mysql:host=$host;dbname=$db", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
echo "<h2>Success!!!<h2>";
} catch(PDOException $e) {
echo 'ERROR: ' . $e->getMessage();
}
?>
