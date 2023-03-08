<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    /* response headers. */

    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json");

    $method = $_SERVER["REQUEST_METHOD"];

    /* 'options' request. */

    if ($method === "OPTIONS") {
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
        header("Access-Control-Allow-Headers: Origin, Accept, Content-Type, X-Requested-With");
        
        exit();
    }

    include_once("../database/database.php");
    $client = new DatabaseClient();

    include_once("../models/authors.php");
    $authors = new Authors($client);

    /* 'get' request. */

    if ($method === "GET") {

        /* if 'id' query parameter was provided. */

        if (array_key_exists("id", $_GET)) {
            $id = $_GET["id"];
            $result = $authors->get_author($id);
            echo $result;
            exit();    
        }
        
        /* otherwise, fetch all authors. */

        $result = $authors->get_authors();
        echo $result;
        exit();
    }

    /* remaining requests have body. */
            
    include_once("../helpers.php");
    $body = decode(file_get_contents("php://input"));
    
    /* 'post' request. */

    if ($method === "POST") {

        /* if request does not provide name for new author. */

        if (!array_key_exists("author", $body)) {
            $result = encode(["message" => "missing required parameters"]);
            echo $result;
            exit();
        }

        /* otherwise, create author. */

        $name = $body["author"];
        $result = $authors->post_author($name);
        echo $result;
        exit();
    }

    /* 'put' request. */

    if ($method === "PUT") {

        /* if request does not provide identifier and new name for author. */
        
        if (!array_key_exists("id", $body) || !array_key_exists("author", $body)) {
            $result = encode(["message" => "missing required parameters"]);
            echo $result;
            exit();
        }
  
        /* otherwise, update author. */

        $id = $body["id"];
        $name = $body["author"];

        $result = $authors->update_author($id, $name);
        echo $result;

        exit();
    }

    /* 'delete' request. */

    if ($method === "DELETE") {

        /* if request does not provide identifier for author. */

        if (!array_key_exists("id", $body)) {
            $result = encode(["message" => "missing required parameters"]);
            echo $result;
            exit();
        }
        
        /* otherwise, delete author. */

        $id = $body["id"];
        $result = $authors->delete_author($id);
        echo $result;
        exit();
    }
?>