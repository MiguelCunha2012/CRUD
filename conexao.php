<?php
$host = 'localhost';
$dbname = 'banco_usuarios';
$dbusername = 'root';
$dbpassword = '';
$pdo = new PDO("mysql:host=$host;dbname=$dbname", $dbusername, $dbpassword);
    try{    
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        //echo"Conexão bem sucedida!";
    }catch(PDOException $e){
        //echo "Erro de conexao: " . $e->getMessage();
    }
