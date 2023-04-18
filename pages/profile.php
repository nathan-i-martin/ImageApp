<?php
    include("../includes/models.php");
    include("../includes/elements/validate.php");

    include("../includes/data_processors/follow.php");
    include("../includes/data_processors/unfollow.php");

    if(empty($_GET['username'])) throw new UnexpectedValueException("You must define a username to access this page!");
    $username = clean_input($_GET['username']);

    $user = null;
    $feed = null;
    try {
        $user = User::searchUser($username);
    } catch(Exception $e) {
        header("Location: 404.php?e=Hmmm... It looks like that user cannot be found.");
    }

    $isClient = false;
    if($user->getUsername() == $client->getUsername()) $isClient = true;

    try {
        $feed = $user->fetchProfileFeed();
    } catch(Exception $e) {}

    $isFollowed = false;
    try {
        $isFollowed = $client->isFollowing($user->getId());
    } catch(Exception $e) {
        echo $e->getMessage();
        $isFollowed = false;
    }
?>
<!DOCTYPE html>
<html>
    <?php
        $title = $user->getUsername()."'s Profile";
        include("../includes/elements/head.php");
    ?>
    <body class="bg-neutral-300 pt-50px overflow-x-hidden sm:bg-gradient-to-br sm:from-cyan-300 sm:via-purple-300 sm:to-orange-300">
        <?php
            $currentPage = CurrentPage::Profile;
            include("../includes/elements/navbar.php");
        ?>
        <div class="w-screen">
            <div class="w-screen min-h-screen bg-neutral-100 border-x border-x-neutral-400 pt-50px pb-50px sm:w-600px mx-auto">
                <div class="block font-bold fixed bottom-10px text-right w-screen sm:w-600px z-50">
                    <a href="logout.php" class="inline text-neutral-700 hover:text-red-700 font-bold bg-neutral-100 rounded-md px-7px pb-4px drop-shadow-lg duration-200 m-15px">Logout</a>
                </div>
                <div class="align-center py-10px">
                    <div class="relative w-1/4 aspect-square rounded-full overflow-hidden shadow-md mx-auto">
                        <img class="absolute top-0 left-0 w-full aspect-square object-cover" src="<?php echo $user->getUserImage()->getImage(); ?>" alt="<?php echo $user->getUserImage()->getAlt(); ?>">
                    </div>
                    <div class="relative w-2/3 mx-auto mt-15px">
                        <?php if(!$isClient) { ?>
                            <span class="text-neutral-700 font-bold text-2xl"><?php echo $user->getUsername(); ?></span>
                            <?php if($isFollowed) { ?>
                                <a href="<?php clean_input($_SERVER["PHP_SELF"]); ?>?action=unfollow&id=<?php echo $user->getId(); ?>&returnQuery=username=<?php echo $user->getUsername(); ?>" class="absolute right-0 top-0 text-right block bg-gray-300 font-bold cursor-pointer px-10px py-5px pt-4px rounded" alt="Unfollow <?php echo $user->getUsername(); ?>">Unfollow</a>
                            <?php } else { ?>
                                <a href="<?php clean_input($_SERVER["PHP_SELF"]); ?>?action=follow&id=<?php echo $user->getId(); ?>&returnQuery=username=<?php echo $user->getUsername(); ?>" class="absolute right-0 top-0 text-right block bg-sky-500 font-bold cursor-pointer px-10px py-5px pt-4px rounded" alt="Follow <?php echo $user->getUsername(); ?>">Follow</a>
                        <?php } } else { ?>
                            <span class="text-neutral-700 font-bold text-2xl"><?php echo $user->getUsername(); ?></span>
                                <a href="editProfile.php" class="absolute right-0 top-0 text-right block bg-gray-300 font-bold cursor-pointer px-10px py-5px pt-4px rounded" alt="Edit your profile">Edit Profile</a>
                        <?php } ?>
                    </div>
                </div>
                <p class="w-2/3 mx-auto text-neutral-600 pt-10px"><?php echo $user->getDescription(); ?></p>
                <div class="flex flex-wrap justify-start m-1px mt-40px items-center border-t-1 border-neutral-500">
                    <?php if($feed == null) { ?>
                        <div class="w-full h-full border-t border-neutral-200 pb-10px">
                            <p class="w-2/3 mx-auto text-neutral-600 pt-30px text-center"><?php echo $isClient ? "You haven't posted anything yet." : "This user hasn't posted anything yet." ?></p>
                        </div>
                    <?php } else foreach ($feed as $post) { ?>
                            <a href="post.php?id=<?php echo $post->getId(); ?>" class="basis-1/3 aspect-square bg-neutral-200 content-center grid overflow-hidden border border-neutral-100">
                                <img class="w-full mx-auto object-contain" src="<?php echo $post->getPostImage()->getImage(); ?>" alt="<?php echo $post->getPostImage()->getAlt(); ?>" />
                            </a>
                    <?php } ?>
                </div>
            </div>
        </div>
    </body>
</html>