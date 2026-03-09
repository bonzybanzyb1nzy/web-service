<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

$host = "db";
$db   = "5cinf";
$user = "root";
$pass = "admin";

try{
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
}catch(PDOException $e){
    http_response_code(500);
    echo json_encode(["errore" => "Connessione fallita"]);
    exit();
}

$richiesta = $_SERVER['REQUEST_METHOD'];


// ------------------- GET -------------------
if($richiesta == "GET"){

    if(isset($_GET["id"])){

        $id = intval($_GET["id"]);

        $sql = "SELECT id,nome,email FROM users WHERE id=:id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(":id",$id,PDO::PARAM_INT);
        $stmt->execute();

        $utente = $stmt->fetch(PDO::FETCH_ASSOC);

        if($utente){
            echo json_encode($utente);
        }else{
            echo json_encode(["messaggio"=>"Utente non trovato"]);
        }

    }

    elseif(isset($_GET["nome"])){

        $nome = "%".$_GET["nome"]."%";

        $sql = "SELECT id,nome,email FROM users WHERE nome LIKE :nome";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":nome",$nome);
        $stmt->execute();

        $lista = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($lista);

    }

    else{

        $sql = "SELECT id,nome,email FROM users";
        $stmt = $pdo->query($sql);

        $lista = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($lista);

    }

}


// ------------------- POST -------------------
elseif($richiesta == "POST"){

    $input = json_decode(file_get_contents("php://input"), true);

    if(!isset($input["nome"]) || !isset($input["email"])){
        http_response_code(400);
        echo json_encode(["errore"=>"Dati mancanti"]);
        exit();
    }

    $nome  = trim($input["nome"]);
    $email = trim($input["email"]);

    if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
        echo json_encode(["errore"=>"Email non valida"]);
        exit();
    }


    $check = $pdo->prepare("SELECT id FROM users WHERE email=:email");
    $check->execute([":email"=>$email]);

    if($check->rowCount() > 0){
        echo json_encode(["errore"=>"Email già presente"]);
        exit();
    }


    $sql = "INSERT INTO users(nome,email) VALUES(:nome,:email)";
    $stmt = $pdo->prepare($sql);

    if($stmt->execute([
        ":nome"=>$nome,
        ":email"=>$email
    ])){
        echo json_encode([
            "messaggio"=>"Utente inserito",
            "id"=>$pdo->lastInsertId()
        ]);
    }else{
        echo json_encode(["errore"=>"Errore inserimento"]);
    }

}


// metodo non valido
else{
    http_response_code(405);
    echo json_encode(["errore"=>"Metodo non supportato"]);
}

?>