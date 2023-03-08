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

    include_once("../models/categories.php");
    $categories = new Categories($client);

    /* 'get' request. */

    if ($method === "GET") {

        /* if 'id' query parameter was provided. */

        if (array_key_exists("id", $_GET)) {
            $id = $_GET["id"];
            $result = $categories->get_category($id);
            echo $result;
            exit();    
        }
        
        /* otherwise, fetch all categories. */

        $result = $categories->get_categories();
        echo $result;
        exit();
    }

    /* remaining requests have body. */
            
    include_once("../helpers.php");
    $body = decode(file_get_contents("php://input"));
    
    /* 'post' request. */

    if ($method === "POST") {

        /* if request does not provide name for new category. */

        if (!array_key_exists("category", $body)) {
            $result = encode(["message" => "missing required parameters"]);
            echo $result;
            exit();
        }

        /* otherwise, create category. */

        $name = $body["category"];
        $result = $categories->post_category($name);
        echo $result;
        exit();
    }

    /* 'put' request. */

    if ($method === "PUT") {

        /* if request does not provide identifier and new name for category. */
        
        if (!array_key_exists("id", $body) || !array_key_exists("category", $body)) {
            $result = encode(["message" => "missing required parameters"]);
            echo $result;
            exit();
        }
  
        /* otherwise, update category. */

        $id = $body["id"];
        $name = $body["category"];

        $result = $categories->update_category($id, $name);
        echo $result;

        exit();
    }

    /* 'delete' request. */

    if ($method === "DELETE") {

        /* if request does not provide identifier for category. */

        if (!array_key_exists("id", $body)) {
            $result = encode(["message" => "missing required parameters"]);
            echo $result;
            exit();
        }
        
        /* otherwise, delete category. */

        $id = $body["id"];
        $result = $categories->delete_category($id);
        echo $result;
        exit();
    }
?>