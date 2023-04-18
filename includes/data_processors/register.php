<?php
$errors = array(
    "generic" => "",
    "username" => "",
    "password" => "",
    "password-verify" => ""
);

if ($_SERVER["REQUEST_METHOD"] == "POST")
    (function() {
        global $errors;

        $requiredData = array("username","password","password-verify");
        foreach ($requiredData as $value) {
            if(empty($_POST[$value])) {
                $errors[$value] = "Please enter a value for ". ucwords(str_replace("_"," ",$value));
                return;
            }
        }

        $username = clean_input($_POST["username"]);
        if(strlen($username) < 5) {
            $errors['username'] = "Username must be greater than 5 characters.";
            return;
        } else if(strlen($username) > 32) {
            $errors['username'] = "Username must be less than 32 characters.";
            return;
        }

        $password = clean_input($_POST["password"]);
        $password_verify = clean_input($_POST["password-verify"]);
        if(!($password == $password_verify)) {
            $errors['password'] = "The two passwords you provided don't match.";
            return;
        }
        unset($password_verify); // It feels better to unset this value ASAP
        $password = password_hash($password, PASSWORD_DEFAULT);
        if(User::exists($username)) {
            $errors['username'] = "That username is already in use.";
            return;
        }

        try {
            $userId = User::create($username,$password);
            unset($password); // It feels better to unset this value ASAP

            $session = Session::start();

            $session->userId = $userId;
            if(Client::isLoggedIn())
                header("Location: feed.php");
        } catch(Exception $e) {
            $errors["generic"] = "There was an issue registering your account. Please try again.";
        }
    })();
