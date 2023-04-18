<?php

class Post extends MiniPost {
    private MiniUser $author;
    private string $description;
    private int $date; // Date is a unix epoch
    private DataInstance $origin;

    private function __construct(?int $id, Image $image, MiniUser $author, string $description, int $date, DataInstance $origin) {
        $this->id = $id;
        $this->image = $image;
        $this->author = $author;
        $this->description = $description;
        $this->date = $date;
        $this->origin = $origin;
    }

    function __destruct() { }

    /**
     * Return the post's date in standard `YYYY-MM-DD HH:MM:SS` format.
     * @return string The date.
     */
    function getDate() {
        return date("Y-m-d H:i:s", $this->date);
    }

    /**
     * Get the author of the post.
     * @returns MiniUser The author.
     */
    function getAuthor() {
        return $this->author;
    }
    
    /**
     * Get the description associated with the post.
     * @returns string The description.
     */
    function getDescription() {
        return $this->description;
    }

    /**
     * Set the ID of this post.
     * @param int $id The ID to set.
     * @throws UnexpectedValueException If the ID is already set.
     * @throws BadMethodCallException If the origin of this post is Permanent.
     * @throws PDOException If the MySQL statement fails.
     */
    private function setPostId(int $id) {
        if(isset($this->id)) throw new UnexpectedValueException("ID is already set! You can't change it!");
        if($this->origin == DataInstance::Permanent) throw new BadMethodCallException("This post is from a remote origin! You can't change it's id!");

        $this->id = $id;
    }

    /**
     * Set this post's origin to be `Permanent`.
     * If the post is already `Permanent` this does nothing.
     */
    private function permanizeOrigin() {
        $this->origin = DataInstance::Permanent;
    }

    /**
     * Publish the virtual post and save it to the database.
     * @throws PDOException If the MySQL statement fails.
     */
    function publish() {
        $mysql = MySQL::loadConfig("main");
        $connection = $mysql->connect();

        $query = "
            INSERT INTO posts (authorId_fk, postDescription, imageId_fk, postDate)
            VALUES (:userId, :description, :imageId, :postTime);        
        ";
        $statement = $connection->prepare($query);
        $statement->bindParam(':userId',        $this->author->getId(), PDO::PARAM_INT);
        $statement->bindParam(':description',   $this->description,     PDO::PARAM_STR);
        $statement->bindParam(':imageId',       $this->image->getId(),  PDO::PARAM_INT);
        $statement->bindParam(':postTime',      $this->date,            PDO::PARAM_INT);
        if(!$statement->execute()) throw new PDOException("MySQL statement failed!");

        $postId = $connection->lastInsertId();

        $this->setPostId($postId);
        $this->permanizeOrigin();
    }
    
    /**
     * Fetch as post by ID.
     * @param int $id The ID of the post to fetch
     * @return Post The post.
     * @throws PDOException If the MySQL statement fails.
     * @throws UnderflowException If no feed was found for the user.
     */
    static function fetchPost(int $id) {
        $mysql = MySQL::loadConfig("main");
        $connection = $mysql->connect();

        $query = "
            SELECT p.postId, p.authorId_fk, p.postDate, p.postDescription, i.imageId, i.imageBlob, i.imageAlt
            FROM posts AS p
            INNER JOIN images AS i ON p.imageId_fk=i.imageId
            WHERE p.postId = :postId
            LIMIT 1;
        ";
        $statement = $connection->prepare($query);
        $statement->bindParam(':postId', $id, PDO::PARAM_INT);
        if(!$statement->execute()) throw new PDOException("MySQL statement failed!");
       
        $post = null;
        $statement->setFetchMode(PDO::FETCH_ASSOC);
        foreach($statement->fetchAll() as $userRow) {
            $requiredColumns = array("postId","authorId_fk","postDate","postDescription","imageId","imageBlob","imageAlt");
            foreach ($requiredColumns as $value) {
                if(empty($userRow[$value])) throw new RuntimeException("The data needed to construct a post is missing! Expected: $value");
            }
            $post = Post::parsePost(
                $userRow["postId"],
                $userRow["authorId_fk"],
                $userRow["postDate"],
                $userRow["postDescription"],
                $userRow["imageId"],
                $userRow["imageBlob"],
                $userRow["imageAlt"]
            );
        }
    
        if(empty($post)) throw new UnderflowException("No post was found with ID: $id");

        $mysql->close();

        return $post;
    }

    /**
     * Create a new post. This post can then be published.
     * @param MiniUser $author The author of the post.
     * @param Image $image The image associated with the post.
     * @param string $description The description associated with the post.
     * @return Post A Virtual post that is ready to be published.
     */
    static function create(MiniUser $author, Image $image, string $description) {
        return new Post(
            null,
            $image,
            $author,
            $description,
            time(),
            DataInstance::Virtual
        );
    }

    /**
     * Create a new post using database information.
     * @param int $id The ID of the post;
     * @param string $imageBlob The Blob of the image associated with the post.
     * @param string $imageAlt The Alt text for the image associated with the post.
     * @param string $description The description associated with the post.
     * @param string $imageBlob The Blob for the image associated with the post.
     * @param string $imageAlt The Alt text for the image associated with the post.
     * @return Post A Permanent post.
     */
    static function parsePost(int $id, int $authorId, int $date, string $description, int $imageId, string $imageBlob, string $imageAlt) {

        $image = new Image(
            $imageId,
            $imageBlob,
            $imageAlt
        );

        try {
            /*
                In a case similar to this implementation of `.fetchUser()`, where this method is being called
                multiple times, some of which are looking for the same user;
                I would implement some form of cache.
                However, that would be outside of the scope of this project.
            */
            $author = MiniUser::fetchUser($authorId);
        } catch(Exception $e) {
            throw new RuntimeException("Unable to fetch the user for this post!");
        }

        return new Post(
            $id,
            $image,
            $author,
            $description,
            $date,
            DataInstance::Permanent
        );
    }
    
    /**
     * Create a new post using database information.
     * @param int $id The ID of the post;
     * @param int $imageId The ID of the image;
     * @param string $imageBlob The Blob of the image associated with the post.
     * @param string $imageAlt The Alt text for the image associated with the post.
     * @return MiniPost A Permanent post.
     * @deprecated This method can only be used on the MiniPost.
     */
    static function parseMiniPost(int $id, int $imageId, string $imageBlob, string $imageAlt) {
        throw new Exception("This method can only be used on the MiniPost.");
    }
}