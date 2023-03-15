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

        /* if 'category_id' and 'author_id' query parameters provided. */

        if (array_key_exists("category_id", $_GET) && array_key_exists("author_id", $_GET)) {
            $category_id = $_GET["category_id"];
            $author_id = $_GET["author_id"];

            $result = $quotes->get_quotes_by_category_and_author($category_id, $author_id);

            echo $result;
            exit();    
        }
        
        /* if 'category_id query parameter was provided. */
        
        if (array_key_exists("category_id", $_GET)) {
            $category_id = $_GET["category_id"];
            $result = $quotes->get_quotes_by_category($category_id);
            echo $result;
            exit();    
        }

        /* if 'author_id' query parameter was provided. */

        if (array_key_exists("author_id", $_GET)) {
            $author_id = $_GET["author_id"];
            $result = $quotes->get_quotes_by_author($author_id);
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

        if (!array_key_exists("quote", $body) || !array_key_exists("category_id", $body) || !array_key_exists("author_id", $body)) {
            $result = encode(["message" => "Missing Required Parameters"]);
            echo $result;
            exit();
        }

        /* otherwise, create quote. */

        $quote = $body["quote"];
        $category_id = $body["category_id"];
        $author_id = $body["author_id"];

        $result = $quotes->post_quote($quote, $category_id, $author_id);
        echo $result;
        exit();
    }

    /* 'put' request. */

    if ($method === "PUT") {

        /* if request does not provide quote, category, and author. */
        
        if (!array_key_exists("quote", $body) || !array_key_exists("category_id", $body) || !array_key_exists("author_id", $body)) {
            $result = encode(["message" => "Missing Required Parameters"]);
            echo $result;
            exit();
        }
  
        /* otherwise, update author. */

        $id = $body["id"];
        $quote = $body["quote"];
        $category_id = $body["category_id"];
        $author_id = $body["author_id"];

        $result = $quotes->update_quote($id, $quote, $category_id, $author_id);
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