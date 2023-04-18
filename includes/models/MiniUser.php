<?php
class MiniUser {
    static ?Image $defaultImage = null;

    protected DataInstance $origin = DataInstance::Virtual;
    protected ?int $id;
    protected string $username;
    protected ?Image $profileImage;

    static function init() {
        try {
            MiniUser::$defaultImage = Image::fetchImage(1);
        } catch(Exception $e) {
            // The loading of this image CANNOT fail, so even if we can't get it from the database, we have a hard coded version here.
            MiniUser::$defaultImage = new Image(1,"iVBORw0KGgoAAAANSUhEUgAAAFoAAABaCAYAAAA4qEECAAAABmJLR0QA/wD/AP+gvaeTAAADi0lEQVR4nO2cTUtVURSGH9MiBUsqJYhMy8I+iAbRxASptBo47MsaBP2A/kKTIJoFFSH9C0chRaFWGEVUphlC2aiENAujLG2wrmAXO7dz7tlr7+tZD7yj+7H2etl337PXWWeDYRiGYRiGYRhGoJT5HkABmoCjQAvQDGwBqnOvTQPjwAjQD9wBxjyMsWSpAM4CD4H5mBoAuoBy9VGXGMeAUeIbnK8RoEN57CVBFXCb4g3OVzdQqZhH0NQBT0nf5AU9AWrVsgmUOuRn7srkxUtJZs2uwu1MXmpmr1bJLDBcrMmFdEsls4DoQN/kBR1XyC8IKkjnEi6phsnIdfY5/Jm8oNPOswyAR/g3ut95lp5pwr/J88Ac0Og4179YoRkMKRCFQBnKW3Rto1uU40XRqhlM2+idyvGiaNYMpm30ZuV4UdRrBtM2urrwW9RYoxlM+w7LvHK8Qqjlrz2jM4sZrYS20T+V40XxQzOYttFfleNFMa0ZTNvoD8rxohjXDKZt9GvleFEMawbTNnpAOV4Uy7qCtw2pnIVQvWtwnKt3knQgpa0+51kGQBf+jT7pPMsAKEenl+NfGiIj9wwB2vFn9CGF/IKiG32Tb6pkFhiVSPeQlsmPyWinEkg/nMZ6PQxsUMopWGqBQdzO5Mw2OOZTifTFuViTM7tcRHEE+ZkXa/AQGby6iEs5cAapRcTZrs8hO75TBHhDI/SnshqRRpdWpFUh/6ms98ifaR/yVNY7D2M0DMMwDMMwDMMobULcGa4FdgF7gN3ADmAdUJN7rSb3vqlFmgTeIDWOV0j/iGonUiFCMHojUgA6DLQBW1P63jHgPnAXuAd8TOl7S4p9wFVk9mncXZkDXgBXgL0K+XmlHrgIPEPH3EJl1EvAdqcZK3MQ6CGMDqWlZnov0EkYy2hsVgEXgJf4N/N/9Rw4D6x04IcTOoG3+DcuqUaBEwQ8ww8gd0V8G5WWHgD7U3WoSCqQP5Zf+Dcnbf0GrhHActKA9Dz7NsS1BpEDA7zQhuzKfJugpUmUnx8nF/B7kQMvRc2geHDAemDCcUIhawKpvcQiSZ/wZaQukVWqEN96XQfyefBUKIr9dFmSi/IZ7LzPb8Q8qSFJ69SnBJ9ZbsQuuSYxuifBZ5YbKh5sAj7jf530pSkUT9JpB744TihETeKhHbgRuIFchcwmHHgpaDaX43Wkm9UwDMMwDMMwDMMwDCPz/AHwMcyzZEtWEAAAAABJRU5ErkJggg==","A black outline of a human");
        }
    }

    private function __construct(int $id, string $username, Image $profileImage) {
        $this->id = $id;
        $this->username = $username;
        $this->profileImage = $profileImage;
    }

    function __destruct() { }

    /**
     * Get the ID of the user.
     * @return ?int The ID. or `null` if no ID has been set.
     */
    function getId() {
        return $this->id;
    }
    
    /**
     * Get the username of the user.
     * @return string The username.
     */
    function getUsername() {
        return $this->username;
    }
    
    /**
     * Get the profile image of the user.
     * @return Image The image. If no image is set for this user, return the default user image.
     */
    function getUserImage() {
        return $this->profileImage ?? MiniUser::$defaultImage;
    }
    
    /**
     * Set the profile image of the user.
     * @param Image The image to set.
     */
    function setUserImage(Image $image) {
        return $this->profileImage = $image;
    }

    /**
     * Set this users' origin to be `Synced`.
     * If the user is already `Synced` this does nothing.
     */
    function syncOrigin() {
        $this->origin = DataInstance::Synced;
    }

    /**
     * Set this users's origin to be `Unsynced`.
     * If the post is already `Unsynced` this does nothing.
     */
    function unsyncOrigin() {
        $this->origin = DataInstance::Unsynced;
    }

    /**
     * Return the current user as a MiniUser
     * @return MiniUser A MiniUser.
     */
    function toMiniUser() {
        return new MiniUser(
            $this->id,
            $this->username,
            $this->profileImage
        );
    }

    /**
     * Save the user's password to the database.
     * 
     * This method does NOT parse the password. If you pass it a raw string, that will be inserted into the database.
     * @return string $password The password to set.
     * @throws PDOException If the MySQL statement fails.
     */
    function savePassword(string $password) {
        $mysql = MySQL::loadConfig("main");
        $connection = $mysql->connect();

        $query = "
            UPDATE users 
            SET userPassword = :password
            WHERE userId = :userId;
        ";
        $statement = $connection->prepare($query);
        $statement->bindParam(':userId', $this->id, PDO::PARAM_INT);
        $statement->bindParam(':password', $password, PDO::PARAM_STR);
        if(!$statement->execute()) throw new PDOException("MySQL statement failed!");

        $mysql->close();
    }

    /**
     * Create a post as this user.
     * @param Image $image The image associated with this post.
     * @param string $description The description associated with this post.
     * @return Post A Virtual post that is ready to be published.
     * @throws RuntimeException If the User was never saved.
     */
    function createPost(Image $image, string $description) {
        if(!$this->isSaved) throw new RuntimeException("This user doesn't exist! How could it post something? You must `save()` it first.");

        return Post::create($this, $image, $description);
    }

    /**
     * Fetches all users other than the current user.
     * @return MiniUser[] An array of users.
     * @throws RuntimeException If the proper columns of data are missing from the database's response.
     * @throws UnderflowException If there was no user with this ID.
     * @throws PDOException If the MySQL statement fails.
     */
    function fetchAllUsers() {
        $mysql = MySQL::loadConfig("main");
        $connection = $mysql->connect();

        $query = "
            SELECT u.userId, u.username, i.imageId, i.imageBlob, i.imageAlt
            FROM users AS u
            LEFT JOIN images AS i ON u.userImageId_fk=i.imageId
            WHERE u.userId != :userId
            LIMIT 10;
        ";
        $statement = $connection->prepare($query);
        $statement->bindParam(':userId', $this->id, PDO::PARAM_INT);
        if(!$statement->execute()) throw new PDOException("MySQL statement failed!");
       
        $users = array();
        $statement->setFetchMode(PDO::FETCH_ASSOC);
        foreach($statement->fetchAll() as $row) {
            $requiredColumns = array("userId","username", "imageId","imageBlob","imageAlt");
            foreach ($requiredColumns as $value) {
                if(!array_key_exists($value, $row)) throw new RuntimeException("The data needed to construct a user is missing! Expected: $value");
            }

            $image = null;
            if($row["imageId"] != null)
                $image = new Image(
                    $row["imageId"],
                    $row["imageBlob"],
                    $row["imageAlt"]
                );

            $user = new MiniUser(
                $row["userId"],
                $row["username"],
                $image
            );
            $user->syncOrigin();

            array_push($users, $user);
        }
    
        if(empty($users)) throw new UnderflowException("No users could be found!");

        $mysql->close();

        return $users;
    }

    /**
     * Fetches a particular user via an ID.
     * @param string $id The ID of the user to fetch.
     * @return MiniUser A user.
     * @throws RuntimeException If the proper columns of data are missing from the database's response.
     * @throws UnderflowException If there was no user with this ID.
     * @throws PDOException If the MySQL statement fails.
     */
    static function fetchUser(int $id) {
        $mysql = MySQL::loadConfig("main");
        $connection = $mysql->connect();

        $query = "
            SELECT u.userId, u.username, i.imageId, i.imageBlob, i.imageAlt
            FROM users AS u
            LEFT JOIN images AS i ON u.userImageId_fk=i.imageId
            WHERE u.userId = :userId;
        ";
        $statement = $connection->prepare($query);
        $statement->bindParam(':userId', $id, PDO::PARAM_INT);
        if(!$statement->execute()) throw new PDOException("MySQL statement failed!");
       
        $user = null;
        $statement->setFetchMode(PDO::FETCH_ASSOC);
        foreach($statement->fetchAll() as $row) {
            $requiredColumns = array("userId","username", "imageId","imageBlob","imageAlt");
            foreach ($requiredColumns as $value) {
                if(!array_key_exists($value, $row)) throw new RuntimeException("The data needed to construct a user is missing! Expected: $value");
            }

            $image = null;
            if($row["imageId"] != null)
                $image = new Image(
                    $row["imageId"],
                    $row["imageBlob"],
                    $row["imageAlt"]
                );

            $user = new MiniUser(
                $row["userId"],
                $row["username"],
                $image
            );
            $user->syncOrigin();
        }
    
        if(empty($user)) throw new UnderflowException("No user was found with ID: $id");

        $mysql->close();

        return $user;
    }
}