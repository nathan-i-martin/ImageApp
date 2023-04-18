<?php
    include("../includes/models.php");
    include("../includes/elements/validate.php");

    $errors = array(
        "pageGeneric" => "",
        "updateProfile-generic" => "",
        "updateProfile-username" => "",
        "updateProfile-description" => "",
        "updateProfile-image" => "",
        "updatePassword-generic" => "",
        "updatePassword-password" => "",
        "updatePassword-password_verify" => ""
    );
    include("../includes/data_processors/updateProfile.php");
    include("../includes/data_processors/updatePassword.php");

    try {
        $feed = $client->fetchProfileFeed();
    } catch(Exception $e) {}
?>
<!DOCTYPE html>
<html>
    <?php
        $title = "Edit Profile";
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
                <form class="anchor-form" action="<?php htmlspecialchars($_SERVER["PHP_SELF"]);?>?action=updateProfile" method="POST" enctype="multipart/form-data">
                    <div class="align-center py-10px">
                        <div class="relative w-1/4 aspect-square rounded-full overflow-hidden shadow-md mx-auto">
                            <img class="absolute top-0 left-0 w-full aspect-square object-cover anchor-imagePreview" src="<?php echo $client->getUserImage()->getImage(); ?>" alt="Your profile photo">
                            <input 
                                class="absolute -top-100px anchor-image anchor-input" 
                                name="image" 
                                type="file" 
                                id="input-file_updateProfile-image"
                                accept="image/*"
                                tabindex=-1>
                        </div>
                        <br>
                        <label class="block w-fit mx-auto text-sky-500 cursor-pointer" for="input-file_updateProfile-image" tabindex=0>
                            Change Profile Image
                        </label>
                        <span class="text-red-500 text-center block anchor-input-file_updateProfile-image-error"><?php echo $errors["updateProfile-image"]; ?></span>
                    </div>
                    <div class="w-3/4 mx-auto">
                        <div class="relative">
                            <label class="font-bold" for="input-text_updateProfile-username">Username<span class="text-red-500">*</span></label>
                            <input
                                class="text-neutral-700 font-bold text-2xl w-full h-full py-11px pt-8px bg-transparent duration-500 shadow-none px-0 focus:shadow-inset-lg focus:px-20px hover:shadow-inset-sm hover:px-20px rounded-md"
                                type="text"
                                name="username"
                                id="input-text_updateProfile-username"
                                placeholder="Username"
                                value="<?php echo $client->getUsername(); ?>"
                                required
                                aria-required="true"
                            >
                            <span class="text-red-500"><?php echo $errors["updateProfile-username"]; ?></span>
                        </div>
                        <br>
                        <br>
                        <label class="font-bold" for="input-textarea_updateProfile-description">Description</label><br>
                        <div class="relative shadow rounded-md overflow-hidden h-150px mt-5px">
                            <div class="h-full">
                                <span class="absolute right-20px bottom-11px text-neutral-400 font-bold"><span class="anchor-characterCount"><?php echo strlen($client->getDescription()); ?></span>/255</span>
                                <textarea 
                                    class="resize-none w-full h-full bg-transparent overflow-y-auto duration-500 shadow-none px-0 py-0 focus:px-20px focus:py-11px focus:shadow-inset-lg hover:px-20px hover:py-11px hover:shadow-inset-sm anchor-description anchor-input anchor-textarea"
                                    id="input-textarea_updateProfile-description"
                                    name="description"
                                    placeholder="Say something about yourself..."><?php echo $client->getDescription(); ?></textarea>
                            </div>
                        </div>
                        <span class="text-red-500"><?php echo $errors["updateProfile-description"]; ?></span>
                        <div class="relative py-10px">
                            <input
                                class="w-full h-full px-20px py-11px pt-8px bg-sky-600 duration-200 hover:bg-sky-500 cursor-pointer text-neutral-100 font-bold rounded-md"
                                type="submit"
                                value="Save Changes"
                            >
                            <span class="text-red-500"><?php echo $errors["updateProfile-generic"]; ?></span>
                        </div>
                    </div>
                </form>
                <form class="anchor-form" action="<?php htmlspecialchars($_SERVER["PHP_SELF"]);?>?action=updatePassword" method="POST" enctype="multipart/form-data">
                    <div class="mt-25px w-3/4 mx-auto">
                        <div class="relative">
                            <label class="font-bold" for="input-text_updatePassword-password">Password<span class="text-red-500">*</span></label>
                            <input
                                class="text-neutral-700 font-bold text-2xl w-full h-full py-11px pt-8px px-20px bg-transparent duration-500 shadow-inset-sm hover:shadow-inset-lg focus:shadow-inset-lg rounded-md"
                                type="password"
                                name="password"
                                id="input-text_updatePassword-password"
                                placeholder="New Password"
                                required
                                aria-required="true"
                            >
                            <span class="text-red-500"><?php echo $errors["updatePassword-password"]; ?></span>
                        </div>
                        <br>
                        <br>
                        <!-- If this were an actual social media platform I would also probably require the user to enter their old password as well -->
                        <div class="relative">
                            <label class="font-bold" for="input-text_updatePassword-password_verify">Verify Password<span class="text-red-500">*</span></label>
                            <input
                                class="text-neutral-700 font-bold text-2xl w-full h-full py-11px pt-8px px-20px bg-transparent duration-500 shadow-inset-sm hover:shadow-inset-lg focus:shadow-inset-lg rounded-md"
                                type="password"
                                name="password_verify"
                                id="input-text_updatePassword-password_verify"
                                placeholder="Password Verify"
                                required
                                aria-required="true"
                            >
                            <span class="text-red-500"><?php echo $errors["updatePassword-password_verify"]; ?></span>
                        </div>
                        <div class="relative py-10px">
                            <input
                                class="w-full h-full px-20px py-11px pt-8px bg-sky-600 duration-200 hover:bg-sky-500 cursor-pointer text-neutral-100 font-bold rounded-md"
                                type="submit"
                                value="Update Password"
                            >
                            <span class="text-red-500"><?php echo $errors["updatePassword-generic"]; ?></span>
                        </div>
                    </div>
                </form>
                <div class="flex flex-wrap justify-start m-1px mt-40px items-center border-t-1 border-neutral-500">
                    <?php if(empty($feed)) { ?>
                        <div class="w-full h-full border-t border-neutral-200 pb-10px">
                            <p class="w-2/3 mx-auto text-neutral-600 pt-30px text-center">You haven't posted anything yet.</p>
                        </div>
                    <?php } else foreach ($feed as $post) { ?>
                            <a href="post.php?id=<?php echo $post->getId(); ?>" class="basis-1/3 aspect-square bg-neutral-200 content-center grid overflow-hidden border border-neutral-100">
                                <img class="w-full mx-auto object-contain" src="<?php echo $post->getPostImage()->getImage(); ?>" alt="<?php echo $post->getPostImage()->getAlt(); ?>" />
                            </a>
                    <?php } ?>
                </div>
            </div>
        </div>
        <script>
            "use strict";
            $(() => {
                $(".anchor-image").on("change",(e) => {
                    const fileSizeKB = Math.round(e.target.files[0].size / 1024);
                    console.log(fileSizeKB);
                    if(fileSizeKB > 16000) {
                        $(".anchor-input-file_updateProfile-image-error").text("Your image must be under 16MB!");
                        return;
                    }

                    const url = URL.createObjectURL(e.target.files[0]);

                    $(".anchor-imagePreview").prop("src",url);
                    $(".anchor-imagePreview").prop("alt","A picture, the one you choose to upload from your filesystem.");
                });
            });
        </script>
    </body>
</html>