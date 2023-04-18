<nav class="fixed left-0 top-0 w-screen h-50px block bg-neutral-200 border-b-neutral-300 border-b drop-shadow z-50">
    <div class="w-screen h-full border-x border-x-neutral-400 sm:w-600px mx-auto">
        <div class="flex flex-row h-full w-full justify-between items-center">
            <?php if($currentPage == CurrentPage::Feed || $currentPage == CurrentPage::Profile) { ?>
                <div class="basis-1/6 flex-none">
                    <a href="createPost.php" class="relative left-10px w-40px h-40px block overflow-hidden rounded-md cursor-pointer bg-neutral-200 text-sky-600 duration-300 hover:bg-neutral-300" tabindex=0 title="Create a post">
                        <span class="absolute -left-[16px] -top-[22px] text-7xl text-center duration-300 hover:text-sky-700">﹢</span>
                    </a>
                </div>
                <div class="flex-1 mx-5px">
                    <a class="font-tangerine text-3xl text-center text-transparent hover:text-transparent" href="feed.php" alt="Go to the top of the Feed page." tabindex=0 title="Go to top fo feed page">
                        <h1 class="bg-clip-text bg-gradient-to-r from-orange-500 to-pink-500">Image-App</h1>
                    </a>
                </div>
                <div class="basis-1/6 flex-none mx-5px">
                    <a href="profile.php?username=<?php echo $client->getUsername(); ?>" class="relative right-10px w-40px aspect-square block float-right overflow-hidden rounded-full cursor-pointer bg-neutral-200 duration-300 hover:bg-neutral-300" tabindex=0 title="Go to your profile">
                        <img class="absolute top-0 left-0 w-full aspect-square object-cover" src="<?php echo $client->getUserImage()->getImage(); ?>" alt="<?php echo $client->getUserImage()->getAlt(); ?>" />
                    </a>
                </div>
            <?php } ?>
            <?php if($currentPage == CurrentPage::Profile) { ?>
            <?php } ?>
            <?php if($currentPage == CurrentPage::Post) { ?>
                <div class="basis-1/6 flex-none">
                    <a href="feed.php" class="relative left-10px w-40px h-40px block overflow-hidden rounded-md cursor-pointer bg-neutral-200 text-red-700 duration-300 hover:bg-neutral-300" tabindex=0 title="Back to profile">
                        <span class="absolute -left-[12px] -top-[20px] text-7xl rotate-45 text-center duration-300 hover:text-sky-700">﹢</span>
                    </a>
                </div>
                <div class="flex-1 mx-5px">
                    <a class="font-tangerine text-3xl text-center text-transparent hover:text-transparent" href="feed.php" alt="Go to the top of the Feed page." tabindex=0 title="Go to top fo feed page">
                        <h1 class="bg-clip-text bg-gradient-to-r from-orange-500 to-pink-500">Image-App</h1>
                    </a>
                </div>
                <div class="basis-1/6 flex-none mx-5px">
                    <span class="text-neutral-400 font-semibold">Post Preview</span>
                </div>
            <?php } ?>
            <?php if($currentPage == CurrentPage::ViewPost) { ?>
                <div class="basis-1/6 flex-none">
                    <a href="profile.php?username=<?php echo $post->getAuthor()->getUsername(); ?>" class="relative left-10px w-40px h-40px block overflow-hidden rounded-md cursor-pointer bg-neutral-200 text-red-700 duration-300 hover:bg-neutral-300 hover:text-red-700" tabindex=0 title="Cancel">
                        <span class="absolute left-[6px] top-[3px] text-3xl rotate-180 text-center duration-300">▶︎</span>
                    </a>
                </div>
                <div class="flex-1 mx-5px">
                    <a class="font-tangerine text-3xl text-center text-transparent hover:text-transparent" href="feed.php" alt="Go to the Feed page." tabindex=0 title="Go to top fo feed page">
                        <h1 class="bg-clip-text bg-gradient-to-r from-orange-500 to-pink-500">Image-App</h1>
                    </a>
                </div>
                <div class="basis-1/6 flex-none mx-5px">
                    <a href="profile.php?username=<?php echo $client->getUsername(); ?>" class="relative right-10px w-40px aspect-square block float-right overflow-hidden rounded-full cursor-pointer bg-neutral-200 duration-300 hover:bg-neutral-300" tabindex=0 title="Go to your profile">
                        <img class="absolute top-0 left-0 w-full aspect-square object-cover" src="<?php echo $client->getUserImage()->getImage(); ?>" alt="<?php echo $client->getUserImage()->getAlt(); ?>" />
                    </a>
                </div>
            <?php } ?>
            <?php if($currentPage == CurrentPage::ProfileEditor) { ?>
                <div class="basis-1/6 flex-none">
                    <a href="profile.php?username=<?php echo $client->getUsername(); ?>" class="relative left-10px w-40px h-40px block overflow-hidden rounded-md cursor-pointer bg-neutral-200 text-red-700 duration-300 hover:bg-neutral-300" tabindex=0 title="Cancel">
                        <span class="absolute -left-[12px] -top-[20px] text-7xl rotate-90 text-center duration-300 hover:text-sky-700">▶︎</span>
                    </a>
                </div>
                <div class="flex-1 mx-5px">
                    <a class="font-tangerine text-3xl text-center text-transparent hover:text-transparent" href="feed.php" alt="Go to the Feed page." tabindex=0 title="Go to top fo feed page">
                        <h1 class="bg-clip-text bg-gradient-to-r from-orange-500 to-pink-500">Image-App</h1>
                    </a>
                </div>
                <div class="basis-1/6 flex-none mx-5px">
                    <span class="text-neutral-400 font-semibold">Profile Editor</span>
                </div>
            <?php } ?>
            <?php if($currentPage == CurrentPage::LoggedOut) { ?>
                <div class="flex-1 mx-5px">
                    <h1 class="font-tangerine text-3xl text-center cursor-default">Image-App</h1>
                </div>
            <?php } ?>
        </div>
    </div>
</nav>
