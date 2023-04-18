<?php
$errors = array(
    "generic" => "",
    "username" => "",
    "password" => "",
);

if ($_SERVER["REQUEST_METHOD"] == "POST")
    (function() {
        global $errors;

        $requiredData = array("username","password");
        foreach ($requiredData as $value) {
            if(empty($_POST[$value])) {
                $errors[$value] = "Please enter a value for ". ucwords(str_replace("_"," ",$value));
                return;
            }
        }

        $username = clean_input($_POST["username"]);
        if(strlen($username) > 32) {
            $errors['username'] = "Username must be less than 32 characters.";
            return;
        }

        $password = clean_input($_POST["password"]);
        try {
            Client::login($username,$password);
        } catch(UnderflowException $e) {
            $errors['generic'] = "No user with that username exists.";
            return;
        } catch(UnexpectedValueException $e) {
            $errors['generic'] = "Your password was invalid.";
            return;
        } catch(Exception $e) {
            $errors['generic'] = "There was an issue logging in.";
            $errors['generic'] = $e->getMessage();
            return;
        }
    })();
