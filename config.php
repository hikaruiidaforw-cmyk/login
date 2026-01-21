<?php
// NewsAPI設定


// データベース接続
function getDbConnection() {
    try {
        $pdo = new PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        die('データベース接続エラー: ' . $e->getMessage());
    }
}


function db_conn()
{
    try {


        $pdo = new PDO('mysql:dbname=' . $db_name . ';charset=utf8;host=' . $db_host, $db_id, $db_pw);
        return $pdo;
    } catch (PDOException $e) {
        exit('DB Connection Error:' . $e->getMessage());
    }
}

//SQLエラー
function sql_error($stmt)
{
    //execute（SQL実行時にエラーがある場合）
    $error = $stmt->errorInfo();
    exit('SQLError:' . $error[2]);
}

function loginCheck(){
    if(!isset($_SESSION['chk_ssid']) || $_SESSION['chk_ssid'] !== session_id()  ){
        exit('LOGIN ERROR');
       }
       
       session_regenerate_id(true);
       $_SESSION['chk_ssid'] =session_id();
}