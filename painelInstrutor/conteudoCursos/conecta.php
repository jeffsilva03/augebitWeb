<?php
$host = "localhost";
$login = "root";
$password = "";
$bd = "semestral_3b";

$tabela1 = "atividades";
$tabela2 = "aula";
$tabela3 = "avaliacao";
$tabela4 = "objetivo";

$mysqli = new mysqli($host, $login, $password, $bd);

if ($mysqli->connect_error) {
    die("Erro na conexão com o banco de dados: " . $mysqli->connect_error);
}
?>

