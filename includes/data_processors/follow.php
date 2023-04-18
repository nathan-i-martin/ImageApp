<?php

if(isset($_GET["action"]) && $_GET["action"] == "follow")
    (function() {
        global $client;

        $requiredData = array("id");
        foreach ($requiredData as $value) {
            if(empty($_GET[$value])) {
                throw new UnexpectedValueException("Please enter a value for ". ucwords(str_replace("_"," ",$value)));
            }
        }

        $userId = clean_input($_GET["id"]);

        if(!is_numeric($userId)) throw new InvalidArgumentException("ID must be a number!");

        if(!$client->follow($userId)) throw new RuntimeException("Unable to follow that user!");

        $returnQuery = "";
        if(isset($_GET["returnQuery"]))
            $returnQuery = "?".clean_input($_GET["returnQuery"]);
        header("Location: ".clean_input($_SERVER["PHP_SELF"])."$returnQuery");
    })();
