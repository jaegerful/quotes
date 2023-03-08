<?php
    include_once("helpers.php");

    class Authors {

        private $client;

        public function __construct($client) {
            $this->client = $client;
        }

        /* each method corresponds to a particular request. */

        /* get all authors. */

        public function get_authors() {
            $query = 
                "SELECT *
                 FROM authors;"
            ;

            try {
                $statement = $this->client->connection->prepare($query);
                $statement->execute();
                $result = $statement->fetchAll();
    
                if (empty($result))
                    return encode(["message" => "authors not found"]);
    
                return encode($result);
            }

            catch(PDOException $error) {
                return encode($error);
            }
        }

        /* get a specific author. */

        public function get_author($id) {
            $query = 
                "SELECT *
                 FROM authors
                 WHERE id = :id;"
            ;

            try {
                $statement = $this->client->connection->prepare($query);
                $statement->execute(['id' => $id]);
                $result = $statement->fetch();
    
                if (empty($result))
                    return encode(["message" => "author_id not found"]);
    
                return encode($result);
            }

            catch(PDOException $error) {
                return encode($error);
            }
        }

        /* post an author. */

        public function post_author($name) {
            $query = 
                "INSERT INTO authors(author)
                 VALUES (:name)
                 RETURNING *;"
            ;

            try {
                $statement = $this->client->connection->prepare($query);
                $statement->execute(['name' => $name]);
                $result = $statement->fetch();
    
                if (empty($result))
                    return encode(["message" => "category could not be created"]);
    
                return "created author (" . $result["id"] . ", " . $result["author"] . ")";
            }
            
            catch(PDOException $error) {
                return encode($error);
            }
        }

        /* update name of author. */

        public function update_author($id, $name) {
            $query = 
                "UPDATE authors
                 SET author = :name
                 WHERE id = :id
                 RETURNING *;"
            ;

            try {
                $statement = $this->client->connection->prepare($query);
                $statement->execute(['name' => $name, 'id' => $id]);
                $result = $statement->fetch();

                if (empty($result))
                    return encode(["message" => "category could not be updated"]);

                return "updated author (" . $result["id"] . ", " . $result["author"] . ")";
            }
            
            catch(PDOException $error) {
                return encode($error);
            }
        }

        /* delete an author. */

        public function delete_author($id) {
            $query = 
                "DELETE FROM authors
                 WHERE id = :id
                 RETURNING id;"
            ;

            try {
                $statement = $this->client->connection->prepare($query);
                $statement->execute(['id' => $id]);
                $result = $statement->fetch();

                if (empty($result))
                    return encode(["message" => "author could not be deleted"]);

                return encode($result);
            }
            
            catch(PDOException $error) {
                return encode($error);
            }
        }

    }
?>