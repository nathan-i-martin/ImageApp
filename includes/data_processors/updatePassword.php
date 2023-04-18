<?php

if(isset($_GET["action"]) && $_GET["action"] == "updatePassword")
    (function() {
        global $errors;
        global $client;

        $requiredData = array("password","password_verify");
        foreach ($requiredData as $value) {
            if(empty($_POST[$value])) {
                $errors[$value] = "Please enter a value for ". ucwords(str_replace("_"," ",$value));
                return;
            }
        }

        $password = clean_input($_POST["password"]);
        $password_verify = clean_input($_POST["password_verify"]);
        if(!($password == $password_verify)) {
            $errors['updatePassword-password_verify'] = "The two passwords you provided don't match.";
            return;
        }
        unset($password_verify); // It feels better to unset this value ASAP
        $password = password_hash($password, PASSWORD_DEFAULT);

        try {
            $client->savePassword($password);
            unset($password); // It feels better to unset this value ASAP

            header("Location: editProfile.php");
        } catch(Exception $e) {
            $errors["generic"] = "There was an issue updating your profile. Please try again.";
        }
    })();
