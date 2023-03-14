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
                "SELECT id, quote, author_id as \"author\", category_id as \"category\"
                 FROM quotes;"
            ;

            try {
                $statement = $this->client->connection->prepare($query);
                $statement->execute();
                $result = $statement->fetchAll();
    
                if (empty($result))
                    return encode(["message" => "No Quotes Found"]);
    
                return encode($result);
            }

            catch(PDOException $error) {
                return encode($error);
            }
        }

        /* get quote. */

        public function get_quote($id) {
            $query = 
                "SELECT id, quote, author_id as \"author\", category_id as \"category\"
                 FROM quotes
                 WHERE id = :id;"
            ;

            try {
                $statement = $this->client->connection->prepare($query);
                $statement->execute(['id' => $id]);
                $result = $statement->fetch();
    
                if (empty($result))
                    return encode(["message" => "No Quotes Found"]);
    
                return encode($result);
            }

            catch(PDOException $error) {
                return encode($error);
            }
        }

        /* get quotes by a specific author. */

        public function get_quotes_by_author($author_id) {
            $query = 
                "SELECT id, quote, author_id as \"author\", category_id as \"category\"
                 FROM quotes
                 WHERE author_id = :id;"
            ;

            try {
                $statement = $this->client->connection->prepare($query);
                $statement->execute(['id' => $author_id]);
                $result = $statement->fetchAll();
    
                if (empty($result))
                    return encode(["message" => "No Quotes Found"]);
    
                return encode($result);
            }

            catch(PDOException $error) {
                return encode($error);
            }
        }

        /* get quotes by a specific category. */

        public function get_quotes_by_category($category_id) {
            $query = 
                "SELECT id, quote, author_id as \"author\", category_id as \"category\"
                 FROM quotes
                 WHERE category_id = :id;"
            ;

            try {
                $statement = $this->client->connection->prepare($query);
                $statement->execute(['id' => $category_id]);
                $result = $statement->fetchAll();
    
                if (empty($result))
                    return encode(["message" => "No Quotes Found"]);
    
                return encode($result);
            }

            catch(PDOException $error) {
                return encode($error);
            }
        }

        /* get quotes by a specific category and author. */

        public function get_quotes_by_category_and_author($category_id, $author_id) {
            $query = 
                "SELECT *
                 FROM quotes
                 WHERE 
                    category_id = :category AND 
                    author_id = :author;"
            ;

            try {
                $statement = $this->client->connection->prepare($query);
                $statement->execute(['category' => $category_id, "author" => $author_id]);
                $result = $statement->fetchAll();
    
                if (empty($result))
                    return encode(["message" => "No Quotes Found"]);
    
                return encode($result);
            }

            catch(PDOException $error) {
                return encode($error);
            }
        }

        /* error handler used by both 'post_quote' and 'update_quote'. */
        /* determines which identifier(s) are wrong, returning an appropriate error message. */

        private function handler($category_id, $author_id) {
            $categories = new Categories($this->client);
            $authors = new Authors($this->client);

            $results = [
                "category_id" => decode($categories->get_category($category_id)),
                "author_id" => decode($authors->get_author($author_id))
            ];

            $tests = [
                "category_id" => true,
                "author_id" => true
            ];

            if (is_array($results["category_id"]) && array_key_exists("message", $results["category_id"]))
                $tests["category_id"] = false;
            
            if (is_array($results["author_id"]) && array_key_exists("message", $results["author_id"]))
                $tests["author_id"] = false;

            /* if both identifiers were wrong. */

            if (!$tests["category_id"] && !$tests["author_id"])
                return encode(["message" => "category_id and author_id Not Found"]);
            
            /* if only 'category_id' wrong. */

            if (!$tests["category_id"])
                return encode($results["category_id"]);
            
            /* if only 'author_id' wrong. */

            if (!$tests["author_id"])
                return encode($results["author_id"]);
        }


        /* post a quote. */

        public function post_quote($quote, $category_id, $author_id) {
            $query = 
                "INSERT INTO quotes(quote, category_id, author_id)
                 VALUES (:quote, :category, :author)
                 RETURNING *;"
            ;

            try {
                $statement = $this->client->connection->prepare($query);
                $statement->execute(['quote' => $quote, 'category' => $category_id, 'author' => $author_id]);
                $result = $statement->fetch();
    
                if (empty($result))
                    return encode(["message" => "quote could not be created"]);
    
                return encode($result);
            }
            
            catch(PDOException) {
                
                /* if exception thrown, the category and/or author identifiers are wrong.  */
                /* use 'handler' to determine which identifier(s) are wrong. */

                return $this->handler($category_id, $author_id);
            }
        }

        /* update a quote. */

        public function update_quote($id, $quote, $category_id, $author_id) {
            $query = 
                "UPDATE quotes
                 SET quote = :quote, category_id = :category, author_id = :author
                 WHERE id = :id
                 RETURNING *;"
            ;

            try {
                $statement = $this->client->connection->prepare($query);
                $statement->execute(['id' => $id, 'quote' => $quote, 'category' => $category_id, 'author' => $author_id]);
                $result = $statement->fetch();

                if (empty($result))
                    return encode(["message" => "No Quotes Found"]);

                return encode($result);
            }
            
            catch(PDOException) {

                /* if exception thrown, the category and/or author identifiers are wrong.  */
                /* use 'handler' to determine which identifier(s) are wrong. */

                return $this->handler($category_id, $author_id);
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
                    return encode(["message" => "No Quotes Found"]);

                return encode($result);
            }
            
            catch(PDOException $error) {
                return encode($error);
            }
        }

    }
?>