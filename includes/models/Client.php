<?php

class Client extends User {
    private function __construct(int $id, string $username, ?string $description, ?Image $profileImage) {
        $this->id = $id;
        $this->username = $username;
        $this->description = $description ?? "";
        $this->profileImage = $profileImage;
    }

    /**
     * Login using user credentials.
     * If the login is successful the client will be redirected to their feed.
     * @param string $username The username of the account to login to.
     * @param string $password The password of the account.
     * @throws PDOException If the MySQL statement fails.
     * @throws RuntimeException If the database fails to return all the needed data.
     * @throws UnderflowException If no user was found with the provided username.
     * @throws UnexpectedValueException If The password is invalid.
     */
    static function login(string $username, string $password) {
        $mysql = MySQL::loadConfig("main");
        $connection = $mysql->connect();

        $query = "
            SELECT u.userId, u.username, u.userPassword, u.userDescription, i.imageId, i.imageBlob, i.imageAlt
            FROM users AS u
            LEFT JOIN images AS i ON u.userImageId_fk=i.imageId
            WHERE u.username = :username;
        ";
        $statement = $connection->prepare($query);
        $statement->bindParam(':username', $username, PDO::PARAM_STR);
        if(!$statement->execute()) throw new PDOException("MySQL statement failed!");
       
        $user = null;
        $statement->setFetchMode(PDO::FETCH_ASSOC);
        foreach($statement->fetchAll() as $row) {
            $requiredColumns = array("userId","username","userPassword","userDescription","imageId","imageBlob","imageAlt");
            foreach ($requiredColumns as $value) {
                if(!array_key_exists($value, $row)) throw new RuntimeException("The data needed to construct a user is missing! Expected: $value");
            }

            $image = new Image(
                $row["imageId"],
                $row["imageBlob"],
                $row["imageAlt"]
            );

            $user = new Client(
                $row["userId"],
                $row["username"],
                $row["userDescription"],
                $image
            );
            $user->syncOrigin(true);
        }
    
        if(empty($user)) throw new UnderflowException("No user was found with the username: $username");

        $mysql->close();

        if(!password_verify($password,$row["userPassword"])) throw new UnexpectedValueException("The password was invalid!");

        $session = Session::start();

        $session->userId = $user->getId();
        if(Client::isLoggedIn())
            header("Location: feed.php");
    }

    /**
     * Log out the client and redirect the client to the login page.
     * @param string $message The message to show the client.
     */
    static function logout(string $message = "Please log in to continue.") {
        $session = Session::start();

        $session->kill();

        header("Location: login.php?e=$message");
    }
    
    /**
     * Checks if the current session is logged in.
     * @return bool `true` if the current session is logged. True does not mean that the session is an actual user account. Only that the session exists and contains the proper data.
     * @throws RuntimeException If `userId` is not set.
     * @throws InvalidArgumentException if `userId` is not a number.
     */
    static function isLoggedIn() {
        $session = Session::start();

        try {
            if(!$session->isset("userId")) throw new RuntimeException("There is no set userId!");
            if(!is_numeric($session->userId)) throw new InvalidArgumentException("The userId is not a number!");
    
            return true;
        } catch(Exception $e) {
            return false;
        }
    }
    
    /**
     * Checks if the current session has a user account associated with it.
     * @return ?Client Returns a `Client` if a user matching the session could be found. `null` otherwise.
     */
    static function getClient() {
        $session = Session::start();

        if(!Client::isLoggedIn()) return null;
        try {
            if(!is_numeric((int) $session->userId)) {
                Client::logout("Your login session is not valid. Please login to continue.");
                return;
            }
            $user = User::fetchUser((int) $session->userId);
            return new Client(
                $user->getId(),
                $user->getUsername(),
                $user->getDescription(),
                $user->getUserImage()
            );
        } catch(Exception $e) {
            return null;
        }
    }
}