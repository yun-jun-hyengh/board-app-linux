<?php
    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    /* 도커 컨테이너 안에서 내 컴퓨터를 찾아가게 만드는 주소(도메인) */
    // $host = 'host.docker.internal';
    // $port = '3306';
    // $dbname = 'board';
    // $username = 'root';
    // $password = '123456';

    // 실서버용
    $host = 'host.docker.internal';
    $port = '3306';
    $dbname = 'board';
    $username = 'jun';
    $password = '123456';

    try {
        $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";

        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("<h1 style='color: red;'> DB 연결실패: " . $e->getMessage() . "</h1>");
    }
?>