<?php
$errors = array(
    "generic" => "",
    "image" => "",
    "description" => "",
    "alt" => ""
);

if ($_SERVER["REQUEST_METHOD"] == "POST")
    (function() {
        global $errors;
        global $client;

        $requiredData = array("description","alt");
        foreach ($requiredData as $value) {
            if(empty($_POST[$value])) {
                $errors[$value] = "Please enter a value for ". ucwords($value);
                return;
            }
        }
        if(!isset($_FILES["image"])) {
            $errors[$value] = "Please upload an image to be posted.";
            return;
        }

        $description = clean_input($_POST["description"]);
        if(strlen($description) > 255) {
            $errors['description'] = "The post description must be less than 255 characters.";
            return;
        }

        $alt = clean_input($_POST["alt"]);
        if(strlen($alt) < 15) {
            $errors['alt'] = "Please use more to describe your image.";
            return;
        } else if(strlen($alt) > 255) {
            $errors['alt'] = "The image alt text must be less than 255 characters.";
            return;
        }

        
        $fileSize = $_FILES["image"]["size"];
        if ($fileSize > FileSize::fromMB(16)) {
            $errors["image"] = "Your image is too large. Max file size is 16MB. Yours was ".floor(FileSize::toMB($fileSize))."MB";
            return;
        }

        $imageBlob = file_get_contents($_FILES['image']['tmp_name']);

        try {
            $image = Image::upload($imageBlob,$alt);
            $post = Post::create($client->toMiniUser(),$image,$description);
            $post->publish();

            header("Location: post.php?id=".$post->getId());
        } catch(Exception $e) {
            $errors["generic"] = "There was an issue posting your image!";
        }
    })();
