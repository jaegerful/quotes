<?php
    include_once("helpers.php");

    class Categories {

        private $client;

        public function __construct($client) {
            $this->client = $client;
        }

        /* each method corresponds to a particular request. */

        /* get all categories. */

        public function get_categories() {
            $query = 
                "SELECT *
                 FROM categories;"
            ;

            try {
                $statement = $this->client->connection->prepare($query);
                $statement->execute();
                $result = $statement->fetchAll();
    
                if (empty($result))
                    return encode(["message" => "categories Not Found"]);
    
                return encode($result);
            }

            catch(PDOException $error) {
                return encode($error);
            }
        }

        /* get a specific category. */

        public function get_category($id) {
            $query = 
                "SELECT *
                 FROM categories
                 WHERE id = :id;"
            ;

            try {
                $statement = $this->client->connection->prepare($query);
                $statement->execute(['id' => $id]);
                $result = $statement->fetch();
    
                if (empty($result))
                    return encode(["message" => "category_id Not Found"]);
    
                return encode($result);
            }

            catch(PDOException $error) {
                return encode($error);
            }
        }

        /* post a category. */

        public function post_category($name) {
            $query = 
                "INSERT INTO categories(category)
                 VALUES (:name)
                 RETURNING *;"
            ;

            try {
                $statement = $this->client->connection->prepare($query);
                $statement->execute(['name' => $name]);
                $result = $statement->fetch();
    
                if (empty($result))
                    return encode(["message" => "category could not be created"]);
    
                return encode($result);
            }
            
            catch(PDOException $error) {
                return encode($error);
            }
        }

        /* update name of a category. */

        public function update_category($id, $name) {
            $query = 
                "UPDATE categories
                 SET category = :name
                 WHERE id = :id
                 RETURNING *;"
            ;

            try {
                $statement = $this->client->connection->prepare($query);
                $statement->execute(['name' => $name, 'id' => $id]);
                $result = $statement->fetch();

                if (empty($result))
                    return encode(["message" => "category_id Not Found"]);

                return encode($result);
            }
            
            catch(PDOException $error) {
                return encode($error);
            }
        }

        /* delete a category. */

        public function delete_category($id) {
            $query = 
                "DELETE FROM categories
                 WHERE id = :id
                 RETURNING id;"
            ;

            try {
                $statement = $this->client->connection->prepare($query);
                $statement->execute(['id' => $id]);
                $result = $statement->fetch();

                if (empty($result))
                    return encode(["message" => "category_id Not Found"]);

                return encode($result);
            }
            
            catch(PDOException $error) {
                return encode($error);
            }
        }

    }
?>