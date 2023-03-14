<?php
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

    include_once("../models/quotes.php");
    $quotes = new Quotes($client);

    /* 'get' request. */

    if ($method === "GET") {

        /* if 'id' query parameter provided. */

        if (array_key_exists("id", $_GET)) {
            $id = $_GET["id"];

            $result = $quotes->get_quote($id);

            echo $result;
            exit();    
        }

        /* if 'categoryId' and 'authorId' query parameters provided. */

        if (array_key_exists("categoryId", $_GET) && array_key_exists("authorId", $_GET)) {
            $category = $_GET["categoryId"];
            $author = $_GET["authorId"];

            $result = $quotes->get_quotes_by_category_and_author($category, $author);

            echo $result;
            exit();    
        }
        
        /* if 'categoryId' query parameter was provided. */
        
        if (array_key_exists("categoryId", $_GET)) {
            $id = $_GET["categoryId"];
            $result = $quotes->get_quotes_by_category($id);
            echo $result;
            exit();    
        }

        /* if 'authorId' query parameter was provided. */

        if (array_key_exists("authorId", $_GET)) {
            $id = $_GET["authorId"];
            $result = $quotes->get_quotes_by_author($id);
            echo $result;
            exit();    
        }
        
        /* otherwise, fetch all quotes. */

        $result = $quotes->get_quotes();
        echo $result;
        exit();
    }

    /* remaining requests have body. */
            
    include_once("../helpers.php");
    $body = decode(file_get_contents("php://input"));
    
    /* 'post' request. */

    if ($method === "POST") {

        /* if request does not provide quote, category, or author. */

        if (!array_key_exists("quote", $body) || !array_key_exists("categoryId", $body) || !array_key_exists("authorId", $body)) {
            $result = encode(["message" => "Missing Required Parameters"]);
            echo $result;
            exit();
        }

        /* otherwise, create quote. */

        $quote = $body["quote"];
        $category = $body["categoryId"];
        $author = $body["authorId"];

        $result = $quotes->post_quote($quote, $category, $author);
        echo $result;
        exit();
    }

    /* 'put' request. */

    if ($method === "PUT") {

        /* if request does not provide quote, category, and author. */
        
        if (!array_key_exists("quote", $body) || !array_key_exists("categoryId", $body) || !array_key_exists("authorId", $body)) {
            $result = encode(["message" => "Missing Required Parameters"]);
            echo $result;
            exit();
        }
  
        /* otherwise, update author. */

        $id = $body["id"];
        $quote = $body["quote"];
        $category = $body["categoryId"];
        $author = $body["authorId"];

        $result = $quotes->update_quote($id, $quote, $category, $author);
        echo $result;

        exit();
    }

    /* 'delete' request. */

    if ($method === "DELETE") {

        /* if request does not provide identifier for author. */

        if (!array_key_exists("id", $body)) {
            $result = encode(["message" => "Missing Required Parameters"]);
            echo $result;
            exit();
        }
        
        /* otherwise, delete author. */

        $id = $body["id"];
        $result = $quotes->delete_quote($id);
        echo $result;
        exit();
    }
?>