<?php

if(isset($_GET["action"]) && $_GET["action"] == "updateProfile")
    (function() {
        global $errors;
        global $client;

        $requiredData = array("username");
        foreach ($requiredData as $value) {
            if(empty($_POST[$value])) {
                $errors[$value] = "Please enter a value for ". ucwords($value);
                return;
            }
        }

        $username = clean_input($_POST["username"]);
        if(strlen($username) < 5) {
            $errors['updateProfile-username'] = "Username must be greater than 5 characters.";
            return;
        } else if(strlen($username) > 32) {
            $errors['updateProfile-username'] = "Username must be less than 32 characters.";
            return;
        }
        if(!($username == $client->getUsername()))
            if(User::exists($username)) {
                $errors['updateProfile-username'] = "That username is already in use.";
                return;
            }

        $description = clean_input($_POST["description"]);
        if(strlen($username) > 32) {
            $errors['updateProfile-description'] = "Description can't be larger than 255 characters.";
            return;
        }

        try {
            $client->setDescription($description);
            $client->setUsername($username);

            if(!empty($_FILES['image']['tmp_name'])) {
                $fileSize = $_FILES["image"]["size"];
                if ($fileSize > FileSize::fromMB(16)) {
                    $errors["updateProfile-image"] = "Your image is too large. Max file size is 16MB. Yours was ".floor(FileSize::toMB($fileSize))."MB";
                    return;
                }
        
                $imageBlob = file_get_contents($_FILES['image']['tmp_name']);

                $image = Image::upload($imageBlob,$username."'s profile photo");

                $client->saveImage($image);
            }

            $client->save();

            header("Location: profile.php?username=".$username);
        } catch(Exception $e) {
            $errors["updateProfile-generic"] = "There was an issue updating your profile. Please try again.";
        }
    })();
