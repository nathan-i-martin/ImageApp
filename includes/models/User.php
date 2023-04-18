<?php
class User extends MiniUser {
    protected string $description;

    function __construct(?int $id, string $username, ?string $description, Image $profileImage) {
        $this->id = $id;
        $this->username = $username;
        $this->description = $description ?? "";
        $this->profileImage = $profileImage;
    }

    function __destruct() { }
    
    /**
     * Get the description associated with the user's account.
     * @return string The description.
     */
    function getDescription() {
        return $this->description;
    }
    
    /**
     * Set the description associated with the user's account.
     * @param string $description The description to set.
     */
    function setDescription(string $description) {
        $this->description = $description;
        $this->unsyncOrigin();
    }
    
    /**
     * Set the username associated with the user's account.
     * @param string $username The username to set.
     */
    function setUsername(string $username) {
        $this->username = $username;
        $this->unsyncOrigin();
    }
    
    /**
     * Save the image associated with the user's account.
     * This will delete the the old image from the database.
     * @param Image $image The image to set.
     */
    function saveImage(Image $image) {
        $oldImageId = $this->getUserImage()->getId();
        $this->setUserImage($image);

        $mysql = MySQL::loadConfig("main");
        $connection = $mysql->connect();

        $query = "
            UPDATE users
            SET userImageId_fk = :userImageId_fk
            WHERE userId = :userId;
        ";
        $statement = $connection->prepare($query);
        $statement->bindParam(':userId', $this->getId(), PDO::PARAM_INT);
        $statement->bindParam(':userImageId_fk', $this->getUserImage()->getId(), PDO::PARAM_INT);
        if(!$statement->execute()) throw new PDOException("MySQL statement failed!");

        $this->syncOrigin();

        $mysql->close();

        try {
            Image::delete($oldImageId);
        } catch(Exception $e) {}

        $this->syncOrigin();
    }

    /**
     * Updates the database to match the user.
     * 
     * This method cannot save, nor update the password. You must use the `savePassword()` method to do that.
     * @throws PDOException If the MySQL statement fails.
     */
    function save() {
        $mysql = MySQL::loadConfig("main");
        $connection = $mysql->connect();

        $query = "
            UPDATE users SET 
            username = :username,
            userDescription = :userDescription,
            userImageId_fk = :userImageId_fk
            WHERE userId = :userId;
        ";
        $statement = $connection->prepare($query);
        $statement->bindParam(':userId', $this->getId(), PDO::PARAM_INT);
        $statement->bindParam(':username', $this->getUsername(), PDO::PARAM_STR);
        $statement->bindParam(':userDescription', $this->getDescription(), PDO::PARAM_STR);
        $statement->bindParam(':userImageId_fk', $this->getUserImage()->getId(), PDO::PARAM_INT);
        if(!$statement->execute()) throw new PDOException("MySQL statement failed!");

        $this->syncOrigin();

        $mysql->close();
    }

    /**
     * Create a new user.
     * @param string $username The username.
     * @param string $password The password.
     * @return int The id of the new user.
     * @throws PDOException If the MySQL statement fails.
     */
    static function create(string $username, string $password) {
        $mysql = MySQL::loadConfig("main");
        $connection = $mysql->connect();

        $query = "
            INSERT INTO users (username, userPassword)
            VALUES (:username, :userPassword);
        ";
        $statement = $connection->prepare($query);
        $statement->bindParam(':username', $username, PDO::PARAM_STR);
        $statement->bindParam(':userPassword', $password, PDO::PARAM_STR);
        if(!$statement->execute()) throw new PDOException("MySQL statement failed!");

        return $connection->lastInsertId();
    }

    /**
     * Delete the User from the database.
     * @throws PDOException If the MySQL statement fails.
     * @throws RuntimeException If the User was never saved to begin with.
     */
    function delete() {
        /**
         * This method won't really be getting used in this final project.
         * It's just here for principle.
         */

        if(!$this->origin == DataInstance::Virtual) throw new RuntimeException("This user doesn't exist! How could it be deleted? You must `save()` it first.");

        $mysql = MySQL::loadConfig("main");
        $connection = $mysql->connect();

        $query = "
            DELETE FROM users WHERE userId = :userId;
        ";
        $statement = $connection->prepare($query);
        $statement->bindParam(':userId', $this->id, PDO::PARAM_INT);
        if(!$statement->execute()) throw new PDOException("MySQL statement failed!");

        $mysql->close();
    }

    /**
     * Get the feed of this user's profile.
     * @return array An array of posts.
     * @throws PDOException If the MySQL statement fails.
     * @throws UnderflowException If no feed was found for the user.
     * @throws RuntimeException If the User was never saved.
     */
    function fetchProfileFeed() {
        if(!$this->origin == DataInstance::Virtual) throw new RuntimeException("This user doesn't exist! How could it have a profile feed? You must `save()` it first.");

        $mysql = MySQL::loadConfig("main");
        $connection = $mysql->connect();

        $query = "
            SELECT p.postId, i.imageId, i.imageBlob, i.imageAlt
            FROM posts AS p
            INNER JOIN images AS i ON p.imageId_fk=i.imageId
            WHERE p.authorId_fk = :userId
            ORDER BY p.postDate DESC
            LIMIT 50;
        ";
        $statement = $connection->prepare($query);
        $statement->bindParam(':userId', $this->id, PDO::PARAM_INT);
        if(!$statement->execute()) throw new PDOException("MySQL statement failed!");
       
        $feed = array();
        $statement->setFetchMode(PDO::FETCH_ASSOC);
        foreach($statement->fetchAll() as $row) {
            $requiredColumns = array("postId","imageId","imageBlob","imageAlt");
            foreach ($requiredColumns as $value) {
                if(!array_key_exists($value, $row)) throw new RuntimeException("The data needed to construct a post is missing! Expected: $value");
            }

            try {
                $post = MiniPost::parseMiniPost(
                    $row["postId"],
                    $row["imageId"],
                    $row["imageBlob"],
                    $row["imageAlt"]
                );

                array_push($feed, $post);
            } catch(Exception $e) {} // We just skip the post that's giving us issues and try another one
        }
    
        if(empty($feed)) throw new UnderflowException("No feed was found for this user!");

        $mysql->close();

        return $feed;
    }

    /**
     * Get the feed for this user.
     * @return array An array of posts.
     * @throws PDOException If the MySQL statement fails.
     * @throws UnderflowException If no feed was found for the user.
     * @throws RuntimeException If the User was never saved.
     */
    function fetchFeed() {
        if(!$this->origin == DataInstance::Virtual) throw new RuntimeException("This user doesn't exist! How could it have a feed? You must `save()` it first.");

        $mysql = MySQL::loadConfig("main");
        $connection = $mysql->connect();

        $query = "
            SELECT p.postId, p.authorId_fk, p.postDate, p.postDescription, i.imageId, i.imageBlob, i.imageAlt
            FROM posts AS p
            LEFT JOIN usersFollowing AS uf ON p.authorId_fk=uf.followedUser_fk
            INNER JOIN images AS i ON p.imageId_fk=i.imageId
            WHERE uf.rootUser_fk = :userId
            ORDER BY p.postDate DESC
            LIMIT 50;
        ";
        $statement = $connection->prepare($query);
        $statement->bindParam(':userId', $this->id, PDO::PARAM_INT);
        if(!$statement->execute()) throw new PDOException("MySQL statement failed!");
       
        $feed = array();
        $statement->setFetchMode(PDO::FETCH_ASSOC);
        foreach($statement->fetchAll() as $row) {
            $requiredColumns = array("postId","authorId_fk","postDate","postDescription","imageId","imageBlob","imageAlt");
            foreach ($requiredColumns as $value) {
                if(!array_key_exists($value, $row)) throw new RuntimeException("The data needed to construct a post is missing! Expected: $value");
            }

            try {
                $post = Post::parsePost(
                    $row["postId"],
                    $row["authorId_fk"],
                    $row["postDate"],
                    $row["postDescription"],
                    $row["imageId"],
                    $row["imageBlob"],
                    $row["imageAlt"]
                );

                array_push($feed, $post);
            } catch(Exception $e) {} // We just skip the post that's giving us issues and try another one
        }
    
        if(empty($feed)) throw new UnderflowException("No feed was found for this user!");

        $mysql->close();

        return $feed;
    }

    /**
     * Follow a user.
     * @param int $userId The ID of the user to follow.
     * @return bool `true` if the user was successfully followed. `false` otherwise.
     * @throws PDOException If the MySQL statement fails.
     * @throws RuntimeException If the User was never saved.
     */
    function follow(int $userId) {
        if(!$this->origin == DataInstance::Virtual) throw new RuntimeException("This user doesn't exist! How could it follow other users? You must `save()` it first.");

        $mysql = MySQL::loadConfig("main");
        $connection = $mysql->connect();

        $query = "
            INSERT IGNORE INTO usersFollowing (rootUser_fk, followedUser_fk)
            VALUES (:rootUser, :userToFollow);
        ";
        $statement = $connection->prepare($query);
        $statement->bindParam(':rootUser', $this->id, PDO::PARAM_INT);
        $statement->bindParam(':userToFollow', $userId, PDO::PARAM_INT);
        try {
            if(!$statement->execute()) throw new PDOException("MySQL statement failed!");
            return true;
        } catch(Exception $e) {
            return false;
        }
    }

    /**
     * Unfollow a user.
     * @param int $userId The ID of the user to unfollow.
     * @return bool `true` if the user was successfully unfollowed. `false` otherwise.
     * @throws PDOException If the MySQL statement fails.
     * @throws RuntimeException If the User was never saved.
     */
    function unfollow(int $userId) {
        if(!$this->origin == DataInstance::Virtual) throw new RuntimeException("This user doesn't exist! How could it unfollow other users? You must `save()` it first.");

        $mysql = MySQL::loadConfig("main");
        $connection = $mysql->connect();

        $query = "
            DELETE FROM usersfollowing
            WHERE rootUser_fk = :rootUser AND followedUser_fk = :userToUnfollow;
        ";
        $statement = $connection->prepare($query);
        $statement->bindParam(':rootUser', $this->id, PDO::PARAM_INT);
        $statement->bindParam(':userToUnfollow', $userId, PDO::PARAM_INT);
        try {
            if(!$statement->execute()) throw new PDOException("MySQL statement failed!");
            return true;
        } catch(Exception $e) {
            return false;
        }
    }

    /**
     * Check if the user is following someone.
     * @param int $userId The ID of the particular user.
     * @return bool `true` if this user is following the requested userId. `false` otherwise. Also returns false if an error occors
     */
    function isFollowing(int $userId) {
        try {
            $mysql = MySQL::loadConfig("main");
            $connection = $mysql->connect();

            $query = "
                SELECT COUNT(*)
                FROM usersFollowing
                WHERE rootUser_fk = :rootUserId AND followedUser_fk = :followedUserId
                LIMIT 1;
            ";
            $statement = $connection->prepare($query);
            $statement->bindParam(':rootUserId', $this->id, PDO::PARAM_INT);
            $statement->bindParam(':followedUserId', $userId, PDO::PARAM_INT);
            if(!$statement->execute()) throw new PDOException("MySQL statement failed!");
            
            $rowCount = $statement->fetchAll()[0]["COUNT(*)"];

            if(!isset($rowCount)) return false;
            if($rowCount == 0) return false;

            return true;
        } catch(Exception $e) {
            return false;
        }
    }

    /**
     * Fetches a particular user.
     * @param int $id The ID of the user to fetch.
     * @return User A user.
     * @throws RuntimeException If the proper columns of data are missing from the database's response.
     * @throws UnderflowException If there was no user with this id.
     * @throws PDOException If the MySQL statement fails.
     */
    static function fetchUser(int $id) {
        $mysql = MySQL::loadConfig("main");
        $connection = $mysql->connect();

        $query = "
            SELECT u.userId, u.username, u.userDescription, i.imageId, i.imageBlob, i.imageAlt
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
            $requiredColumns = array("userId","username","userDescription","imageId","imageBlob","imageAlt");
            foreach ($requiredColumns as $value) {
                if(!array_key_exists($value, $row)) throw new RuntimeException("The data needed to construct a user is missing! Expected: $value");
            }

            $image = new Image(
                $row["imageId"],
                $row["imageBlob"],
                $row["imageAlt"]
            );

            $user = new User(
                $row["userId"],
                $row["username"],
                $row["userDescription"],
                $image
            );
            $user->syncOrigin();
        }
    
        if(empty($user)) throw new UnderflowException("No user was found with the ID: $id");

        $mysql->close();

        return $user;
    }

    /**
     * Searches for a particular user by username.
     * @param string $username The username of the user to fetch.
     * @return User A user.
     * @throws RuntimeException If the proper columns of data are missing from the database's response.
     * @throws UnderflowException If there was no user with this username.
     * @throws PDOException If the MySQL statement fails.
     */
    static function searchUser(string $username) {
        $mysql = MySQL::loadConfig("main");
        $connection = $mysql->connect();

        $query = "
            SELECT u.userId, u.username, u.userDescription, i.imageId, i.imageBlob, i.imageAlt
            FROM users AS u
            LEFT JOIN images AS i ON u.userImageId_fk=i.imageId
            WHERE u.username = :username
            LIMIT 1;
        ";
        $statement = $connection->prepare($query);
        $statement->bindParam(':username', $username, PDO::PARAM_STR);
        if(!$statement->execute()) throw new PDOException("MySQL statement failed!");
       
        $user = null;
        $statement->setFetchMode(PDO::FETCH_ASSOC);
        foreach($statement->fetchAll() as $row) {
            $requiredColumns = array("userId","username","userDescription","imageId","imageBlob","imageAlt");
            foreach ($requiredColumns as $value) {
                if(!array_key_exists($value, $row)) throw new RuntimeException("The data needed to construct a user is missing! Expected: $value");
            }

            $image = new Image(
                $row["imageId"],
                $row["imageBlob"],
                $row["imageAlt"]
            );

            $user = new User(
                $row["userId"],
                $row["username"],
                $row["userDescription"],
                $image
            );
            $user->syncOrigin();
        }
    
        if(empty($user)) throw new UnderflowException("No user was found with the username: $username");

        $mysql->close();

        return $user;
    }

    /**
     * Fetch all of the users that are followers of a particular user.
     * @param int $userId The ID the the particular user.
     * @return array An array of Users.
     * @throws RuntimeException If the proper columns of data are missing from the database's response.
     * @throws UnderflowException If there are no followers for this user.
     * @throws PDOException If the MySQL statement fails.
     */
    static function fetchFollowersFor(int $userId) {
        $mysql = MySQL::loadConfig("main");
        $connection = $mysql->connect();

        $query = "
            SELECT u.userId, u.username, i.imageId, i.imageBlob, i.imageAlt
            FROM usersFollowing AS uf
            INNER JOIN users AS u ON uf.rootUser_fk=u.userId
            LEFT JOIN images AS i ON u.userImageId_fk=i.imageId
            WHERE uf.followedUser_fk = :userId;
        ";
        $statement = $connection->prepare($query);
        $statement->bindParam(':userId', $userId, PDO::PARAM_INT);
        if(!$statement->execute()) throw new PDOException("MySQL statement failed!");
       
        $users = array();
        $statement->setFetchMode(PDO::FETCH_ASSOC);
        foreach($statement->fetchAll() as $row) {
            $requiredColumns = array("userId","username","imageId","imageBlob","imageAlt");
            foreach ($requiredColumns as $value) {
                if(!array_key_exists($value, $row)) throw new RuntimeException("The data needed to construct a user is missing! Expected: $value");
            }

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

            array_push($users,$user);
        }
    
        if(empty($users)) throw new UnderflowException("This user has no followers!");

        $mysql->close();
    
        return $users;
    }

    /**
     * Fetch all of the users that a particular user is following.
     * @param int $userId The ID the the particular user.
     * @return array An array of Users.
     * @throws RuntimeException If the proper columns of data are missing from the database's response.
     * @throws UnderflowException If this user isn't following anyone.
     * @throws PDOException If the MySQL statement fails.
     */
    static function fetchFollowersOf(int $userId) {
        $mysql = MySQL::loadConfig("main");
        $connection = $mysql->connect();

        $query = "
            SELECT u.userId, u.username, i.imageId, i.imageBlob, i.imageAlt
            FROM usersFollowing AS uf
            INNER JOIN users AS u ON uf.followedUser_fk=u.userId
            LEFT JOIN images AS i ON u.userImageId_fk=i.imageId
            WHERE uf.rootUser_fk = :userId;
        ";
        $statement = $connection->prepare($query);
        $statement->bindParam(':userId', $userId, PDO::PARAM_INT);
        if(!$statement->execute()) throw new PDOException("MySQL statement failed!");
       
        $users = array();
        $statement->setFetchMode(PDO::FETCH_ASSOC);
        foreach($statement->fetchAll() as $row) {
            $requiredColumns = array("userId","username","imageId","imageBlob","imageAlt");
            foreach ($requiredColumns as $value) {
                if(!array_key_exists($value, $row)) throw new RuntimeException("The data needed to construct a user is missing! Expected: $value");
            }

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

            array_push($users,$user);
        }
    
        if(empty($users)) throw new UnderflowException("This user has no followers!");

        $mysql->close();
    
        return $users;
    }

    /**
     * Check if a user with the defined username exists
     * @param string $username The username.
     * @return bool `true` if the user exists. `false` otherwise.
     */
    static function exists(string $username) {
        try {
            User::searchUser($username); // No need to worry about the return of this. All we care about is that it didn't throw an error (i.e. it DID get a user)
            return true;
        } catch(Exception $e) {
            return false;
        }
    }
    
}
