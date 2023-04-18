<?php

include("cleanInput.php");
include("models/FileSize.php");

include("models/DataInstance.php");

include("models/CurrentPage.php");
include("models/MySQL.php");
include("models/Image.php");

include("models/MiniUser.php");
include("models/MiniPost.php");

include("models/User.php");
include("models/Post.php");

include("models/Session.php");
include("models/Client.php");

$loggedInUser = User::fetchUser(1);