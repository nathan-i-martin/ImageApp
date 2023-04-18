<?php

class Image {
    private int $id;
    private string $image;
    private string $alt;

    function __construct(int $id, string $image, string $alt) {
        $this->id = $id;
        $this->image = $image;
        $this->alt = $alt;
    }

    function __destruct() { }

    /**
     * Returns the image's id.
     * @return int The image id.
     */
    function getId() {
        return $this->id;
    }

    /**
     * Returns the image's blob data in base64 format.
     * @return string The image blob.
     */
    function getImage() {
        if($this->isEncoded()) return "data:image/png;base64,".$this->image;
        return "data:image/png;base64,".base64_encode($this->image);
    }

    /**
     * Returns the image's alt text.
     * @return string The image alt text.
     */
    function getAlt() {
        return $this->alt;
    }

    /**
     * Check if the image is already base64 encoded.
     * @return bool `true` if the image is already a valid bade64 string. `false` otherwise.
     */
    function isEncoded() {
        return (base64_encode(base64_decode($this->image, true)) === $this->image);
    }

    /**
     * Delete the image from the database.
     * @param int $id The ID of the image to delete.
     * @throws PDOException If the MySQL statement fails.
     */
    static function delete(int $id) {
        if($id == 1 || $id == 2) return; // IDs 1 and 2 are special images. we don't ever want to delete them!

        $mysql = MySQL::loadConfig("main");
        $connection = $mysql->connect();

        $query = "
            DELETE FROM images WHERE imageId = :imageId;
        ";
        $statement = $connection->prepare($query);
        $statement->bindParam(':imageId', $id, PDO::PARAM_INT);
        if(!$statement->execute()) throw new PDOException("MySQL statement failed!");
    }

    /**
     * Fetches a particular image.
     * @param int $id The ID of the image to fetch.
     * @return Image An Image.
     * @throws RuntimeException If the proper columns of data are missing from the database's response.
     * @throws UnderflowException If there was no image with this id.
     * @throws PDOException If the MySQL statement fails.
     */
    static function fetchImage(int $id) {
        $mysql = MySQL::loadConfig("main");
        $connection = $mysql->connect();

        $query = "
            SELECT i.imageId, i.imageBlob, i.imageAlt
            FROM images AS i
            WHERE i.imageId = :imageId;
        ";
        $statement = $connection->prepare($query);
        $statement->bindParam(':imageId', $id, PDO::PARAM_INT);
        if(!$statement->execute()) throw new PDOException("MySQL statement failed!");
       
        $image = null;
        $statement->setFetchMode(PDO::FETCH_ASSOC);
        foreach($statement->fetchAll() as $row) {
            $requiredColumns = array("imageId","imageBlob","imageAlt");
            foreach ($requiredColumns as $value) {
                if(!array_key_exists($value, $row)) throw new RuntimeException("The data needed to construct an image is missing! Expected: $value");
            }

            $image = new Image(
                $row["imageId"],
                $row["imageBlob"],
                $row["imageAlt"]
            );
        }
    
        if(empty($image)) throw new UnderflowException("No image was found with the ID: $id");

        $mysql->close();

        return $image;
    }

    /**
     * Upload an image to the database.
     * @param string $image The image to upload.
     * @param string $alt The alt text associated with the image.
     * @return Image An Image.
     * @throws PDOException If the MySQL statement fails.
     */
    static function upload(string $image, string $alt) {
        $mysql = MySQL::loadConfig("main");
        $connection = $mysql->connect();

        $query = "
            INSERT INTO images (imageBlob, imageAlt)
            VALUES (:imageBlob, :imageAlt);
        ";
        $statement = $connection->prepare($query);
        $statement->bindParam(':imageBlob', $image, PDO::PARAM_LOB);
        $statement->bindParam(':imageAlt', $alt, PDO::PARAM_STR);
        if(!$statement->execute()) throw new PDOException("MySQL statement failed!");

        return new Image(
            $connection->lastInsertId(),
            $image,
            $alt
        );
    }
}
