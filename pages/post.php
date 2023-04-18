<?php
    include("../includes/models.php");
    include("../includes/elements/validate.php");

    include("../includes/data_processors/follow.php");
    include("../includes/data_processors/unfollow.php");

    $post = null;
    try {
        if(empty($_GET["id"])) throw new UnexpectedValueException("You must provide the ID of the post you want to view!");

        $postId = clean_input($_GET["id"]);
        
        if(!is_numeric($postId)) throw new InvalidArgumentException("You must provide a number for the ID of the post you want to view!");

        $post = Post::fetchPost($postId);
    } catch(Exception $e) {
        header("Location: 404.php?e=Hmmm... It looks like that post cannot be found.");
    }

    $isClient = false;
    if($post->getAuthor()->getUsername() == $client->getUsername()) $isClient = true;

    $isFollowed = false;
    try {
        $isFollowed = $client->isFollowing($post->getAuthor()->getId());
    } catch(Exception $e) {
        echo $e->getMessage();
        $isFollowed = false;
    }
?>
<!DOCTYPE html>
<html>
    <?php
        $title = $post->getAuthor()->getUsername()."'s Post";
        include("../includes/elements/head.php");
    ?>
    <body class="bg-neutral-300 pt-50px overflow-x-hidden sm:bg-gradient-to-br sm:from-cyan-300 sm:via-purple-300 sm:to-orange-300">
        <?php
            $currentPage = CurrentPage::ViewPost;
            include("../includes/elements/navbar.php");
        ?>
        <div class="w-screen min-h-screen">
            <div class="w-screen bg-neutral-100 border border-neutral-400 mt-10px pb-50px sm:w-600px mx-auto rounded-none sm:rounded-lg">
                <div class="block font-bold fixed bottom-10px text-right w-screen sm:w-600px z-50">
                    <a href="logout.php" class="inline text-neutral-700 hover:text-red-700 font-bold bg-neutral-100 rounded-md px-7px pb-4px drop-shadow-lg duration-200 m-15px">Logout</a>
                </div>
                <div class="w-full h-full pb-10px">
                    <div class="relative w-full h-47px border-t-1 border-neutral-200">
                        <a href="profile.php?username=<?php echo $post->getAuthor()->getUsername(); ?>" class="absolute px-4px py-2px mx-3px my-6px rounded cursor-pointer bg-neutral-100 duration-200 hover:bg-neutral-300" tabindex=0 title="Go to <?php echo $post->getAuthor()->getUsername(); ?>'s profile">
                            <img class="w-30px aspect-square fill-neutral-600 inline-block rounded-full" src="<?php echo $post->getAuthor()->getUserImage()->getImage(); ?>" alt="<?php echo $post->getAuthor()->getUserImage()->getAlt(); ?>" />
                            <span class="relative top-1px px-5px inline-block text-neutral-900 font-bold"><?php echo $post->getAuthor()->getUsername(); ?></span>
                        </a>
                        <?php if(!$isClient)
                                if($isFollowed) { ?>
                                <a href="<?php clean_input($_SERVER["PHP_SELF"]); ?>?action=unfollow&id=<?php echo $post->getAuthor()->getId(); ?>&returnQuery=id=<?php echo $post->getId(); ?>" class="absolute right-20px top-10px text-neutral-400 font-semibold cursor-pointer duration-300 hover:text-red-600" alt="Unfollow <?php echo $post->getAuthor()->getUsername(); ?>" tabindex=0>Unfollow</a>
                            <?php } else { ?>
                                <a href="<?php clean_input($_SERVER["PHP_SELF"]); ?>?action=follow&id=<?php echo $post->getAuthor()->getId(); ?>&returnQuery=id=<?php echo $post->getId(); ?>" class="absolute right-20px top-10px text-sky-500 font-semibold cursor-pointer duration-300 hover:text-sky-600" alt="Follow <?php echo $post->getAuthor()->getUsername(); ?>" tabindex=0>Follow</a>
                        <?php } ?>
                    </div>
                    <div class="w-full h-auto aspect-square bg-neutral-200 content-center grid">
                        <img class="w-full mx-auto object-contain" src="<?php echo $post->getPostImage()->getImage(); ?>" alt="<?php echo $post->getPostImage()->getAlt(); ?>" />
                    </div>
                    <div class="relative p-10px pt-25px">
                        <span class="absolute top-5px left-10px text-neutral-400 text-xs font-bold">Posted on: <?php echo $post->getDate(); ?></span>
                        <p>
                            <a class="font-bold" href="profile.php?username=<?php echo $post->getAuthor()->getUsername(); ?>" tabindex=0>
                                <?php echo $post->getAuthor()->getUsername(); ?>
                            </a>
                            <?php echo $post->getDescription(); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>