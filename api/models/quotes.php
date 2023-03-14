<?php
    /* helper functions to handle json. */

    include_once("helpers.php");
    include_once("../helpers.php");

    /* this model uses other models. */

    include_once("categories.php");
    include_once("authors.php");

    class Quotes {

        private $client;

        public function __construct($client) {
            $this->client = $client;
        }

        /* each method corresponds to a particular request. */

        /* get all quotes. */

        public function get_quotes() {
            $query = 
                "SELECT *
                 FROM quotes;"
            ;

            try {
                $statement = $this->client->connection->prepare($query);
                $statement->execute();
                $result = $statement->fetchAll();
    
                if (empty($result))
                    return encode(["message" => "no quotes found"]);
    
                return encode($result);
            }

            catch(PDOException $error) {
                return encode($error);
            }
        }

        /* get quotes by a specific author. */

        public function get_quotes_by_author($id) {
            $query = 
                "SELECT *
                 FROM quotes
                 WHERE author = :id;"
            ;

            try {
                $statement = $this->client->connection->prepare($query);
                $statement->execute(['id' => $id]);
                $result = $statement->fetchAll();
    
                if (empty($result))
                    return encode(["message" => "no quotes found"]);
    
                return encode($result);
            }

            catch(PDOException $error) {
                return encode($error);
            }
        }

        /* get quotes by a specific category. */

        public function get_quotes_by_category($id) {
            $query = 
                "SELECT *
                 FROM quotes
                 WHERE category = :id;"
            ;

            try {
                $statement = $this->client->connection->prepare($query);
                $statement->execute(['id' => $id]);
                $result = $statement->fetchAll();
    
                if (empty($result))
                    return encode(["message" => "no quotes found"]);
    
                return encode($result);
            }

            catch(PDOException $error) {
                return encode($error);
            }
        }

        /* get quotes by a specific category and author. */

        public function get_quotes_by_category_and_author($category, $author) {
            $query = 
                "SELECT *
                 FROM quotes
                 WHERE 
                    category = :category AND 
                    author = :author;"
            ;

            try {
                $statement = $this->client->connection->prepare($query);
                $statement->execute(['category' => $category, "author" => $author]);
                $result = $statement->fetchAll();
    
                if (empty($result))
                    return encode(["message" => "no quotes found"]);
    
                return encode($result);
            }

            catch(PDOException $error) {
                return encode($error);
            }
        }


        /* post a quote. */

        public function post_quote($quote, $category_id, $author_id) {
            $query = 
                "INSERT INTO quotes(quote, category, author)
                 VALUES (:quote, :category, :author)
                 RETURNING *;"
            ;

            try {
                $statement = $this->client->connection->prepare($query);
                $statement->execute(['quote' => $quote, 'category' => $category_id, 'author' => $author_id]);
                $result = $statement->fetch();
    
                if (empty($result))
                    return encode(["message" => "category could not be created"]);
    
                return "created quote (" . $result["id"] . ", " . $result["quote"] . ", " . $result["author"] . ", " . $result["category"] . ")";
            }
            
            catch(PDOException) {
                
                /* if exception thrown, the category and/or author identifiers are wrong.  */
                /* to determine which identifiers are wrong, test them on their respective models. */

                $categories = new Categories($this->client);
                $authors = new Authors($this->client);

                $tests = [
                    "category_id" => decode($categories->get_category($category_id)),
                    "author_id" => decode($authors->get_author($author_id))
                ];

                if (is_array($tests["category_id"]) && array_key_exists("message", $tests["category_id"]))
                    $tests["category_id"] = false;
                
                if (is_array($tests["author_id"]) && array_key_exists("message", $tests["author_id"]))
                    $tests["author_id"] = false;

                /* if both identifiers were wrong. */

                if (!$tests["category_id"] && !$tests["author_id"])
                    return encode(["message" => "category_id and author_id not found"]);
                
                /* if only 'category_id' wrong. */

                if (!$tests["category_id"])
                    return encode(["message" => "category_id not found"]);
                
                /* if only 'author_id' wrong. */

                if (!$tests["author_id"])
                    return encode(["message" => "author_id not found"]);
            }
        }

        /* update a quote. */

        public function update_quote($id, $quote, $category, $author) {
            $query = 
                "UPDATE quotes
                 SET quote = :quote, category = :category, author = :author
                 WHERE id = :id
                 RETURNING *;"
            ;

            try {
                $statement = $this->client->connection->prepare($query);
                $statement->execute(['id' => $id, 'quote' => $quote, 'category' => $category, 'author' => $author]);
                $result = $statement->fetch();

                if (empty($result))
                    return encode(["message" => "no quotes found"]);

                return "updated quote (" . $result["id"] . ", " . $result["quote"] . ", " . $result["author"] . ", " . $result["category"] . ")";
            }
            
            catch(PDOException $error) {
                return encode($error);
            }
        }

        /* delete a quote. */

        public function delete_quote($id) {
            $query = 
                "DELETE FROM quotes
                 WHERE id = :id
                 RETURNING id;"
            ;

            try {
                $statement = $this->client->connection->prepare($query);
                $statement->execute(['id' => $id]);
                $result = $statement->fetch();

                if (empty($result))
                    return encode(["message" => "no quotes found"]);

                return encode($result);
            }
            
            catch(PDOException $error) {
                return encode($error);
            }
        }

    }
?>