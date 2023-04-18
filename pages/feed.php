<?php
    include("../includes/models.php");
    include("../includes/elements/validate.php");

    include("../includes/data_processors/follow.php");
    include("../includes/data_processors/unfollow.php");

    try {
        $feed = $client->fetchFeed();
    } catch(Exception $e) {
        $feed = null;
    }

    try {
        $users = $client->fetchAllUsers();
    } catch(Exception $e) {
        $users = null;
    }
?>
<!DOCTYPE html>
<html>
    <?php
        $title = "Your Feed";
        include("../includes/elements/head.php");
    ?>
    <body class="bg-neutral-300 pt-50px overflow-x-hidden sm:bg-gradient-to-br sm:from-cyan-300 sm:via-purple-300 sm:to-orange-300">
        <?php
            $currentPage = CurrentPage::Feed;
            include("../includes/elements/navbar.php");
        ?>
        <div class="w-screen">
            <div class="w-screen min-h-screen bg-neutral-100 border-x border-x-neutral-400 pt-10px pb-50px sm:w-600px mx-auto">
                <div class="block font-bold fixed bottom-10px text-right w-screen sm:w-600px z-50">
                    <a href="logout.php" class="inline text-neutral-700 hover:text-red-700 font-bold bg-neutral-100 rounded-md px-7px pb-4px drop-shadow-lg duration-200 m-15px">Logout</a>
                </div>
                <div class="w-full h-full border-t border-neutral-200 pb-10px">
                    <p class="w-2/3 mx-auto text-neutral-600 pt-30px text-center"><?php if(empty($feed))
                        { ?>You aren't following anyone! Follow people to see their posts!<?php }
                        else { ?>Follow more people to see their posts!<?php } ?>
                    </p>
                </div>
                <div class="my-20px text-center"><?php
                    if(!empty($users))
                        foreach($users as $user) {
                            $isFollowed = $client->isFollowing($user->getId()); ?>
                    <div class="inline-block h-200px w-[125px] relative bg-neutral-200 rounded-md mx-10px">
                        <a class="absolute top-0 left-0 h-200px w-[125px] p-10px" href="profile.php?username=<?php echo $user->getUsername(); ?>" tabindex=0 title="Go to <?php echo $user->getUsername(); ?>'s profile">
                            <div class="text-center w-[100px] mx-auto aspect-square fill-neutral-600 block rounded-full overflow-hidden">
                                <img class="object-cover inline-block v" src="<?php echo $user->getUserImage()->getImage(); ?>" alt="<?php echo $user->getUserImage()->getAlt(); ?>" />
                            </div>
                            <span class="relative top-1px px-5px block text-center text-neutral-900 font-bold"><?php echo $user->getUsername(); ?></span>
                        </a>
                        <span class="absolute bottom-15px block w-[125px] text-center">
                            <?php if($isFollowed) { ?>
                                <a href="<?php clean_input($_SERVER["PHP_SELF"]); ?>?action=unfollow&id=<?php echo $user->getId(); ?>" class="text-center inline-block bg-neutral-300 font-bold cursor-pointer px-10px py-5px pt-4px rounded" alt="Unfollow <?php echo $user->getUsername(); ?>">Unfollow</a>
                            <?php } else { ?>
                                <a href="<?php clean_input($_SERVER["PHP_SELF"]); ?>?action=follow&id=<?php echo $user->getId(); ?>" class="text-center inline-block bg-sky-500 font-bold cursor-pointer px-10px py-5px pt-4px rounded" alt="Follow <?php echo $user->getUsername(); ?>">Follow</a>
                            <?php } ?>
                        </span>
                    </div>
                    <?php } ?>
                </div>
                <?php
                    if(!empty($feed))
                        foreach ($feed as $post) { 
                    ?>
                    <div class="w-full h-full border-t border-neutral-200 pb-10px">
                        <div class="relative w-full h-47px border-t-1 border-neutral-200">
                            <a href="profile.php?username=<?php echo $post->getAuthor()->getUsername(); ?>" class="absolute px-4px py-2px mx-3px my-6px rounded cursor-pointer bg-neutral-100 duration-200 hover:bg-neutral-300" tabindex=0 title="Go to <?php echo $post->getAuthor()->getUsername(); ?>'s profile">
                                <img class="w-30px aspect-square fill-neutral-600 inline-block rounded-full" src="<?php echo $post->getAuthor()->getUserImage()->getImage(); ?>" alt="<?php echo $post->getAuthor()->getUserImage()->getAlt(); ?>" />
                                <span class="relative top-1px px-5px inline-block text-neutral-900 font-bold"><?php echo $post->getAuthor()->getUsername(); ?></span>
                            </a>
                            <a href="<?php clean_input($_SERVER["PHP_SELF"]); ?>?action=unfollow&id=<?php echo $post->getAuthor()->getId(); ?>" class="absolute right-10px top-10px text-neutral-400 font-semibold cursor-pointer duration-300 hover:text-red-600" alt="Unfollow <?php echo $post->getAuthor()->getUsername(); ?>" tabindex=0>Unfollow</a>
                        </div>
                        <div class="w-full h-auto aspect-square bg-neutral-200 content-center grid">
                            <img class="w-full mx-auto object-contain" src="<?php echo $post->getPostImage()->getImage(); ?>" alt="<?php echo $post->getPostImage()->getAlt(); ?>" />
                        </div>
                        <div class="relative p-10px pt-25px">
                            <span class="absolute top-5px left-10px text-neutral-400 text-xs font-bold">Posted on: <?php echo $post->getDate(); ?></span>
                            <p class="break-words">
                                <a class="font-bold" href="profile.php?username=<?php echo $post->getAuthor()->getUsername(); ?>" tabindex=0>
                                    <?php echo $post->getAuthor()->getUsername(); ?>
                                </a>
                                <?php echo $post->getDescription(); ?>
                            </p>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </body>
</html>