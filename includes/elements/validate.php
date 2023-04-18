<?php

$client = Client::getClient();

if(!($client instanceof Client))
    header("Location: login.php?e=Please login to continue");