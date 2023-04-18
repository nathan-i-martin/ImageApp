<?php
    include("../includes/models.php");
    include("../includes/elements/validate.php");

    include("../includes/data_processors/createPost.php");
    
    $defaultImage = null;
    try {
        $defaultImage = Image::fetchImage(2);
    } catch(Exception $e) {
        $defaultImage = new Image(2, "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", "Placeholder Image");
    }
?>
<!DOCTYPE html>
<html>
    <?php
        $title = "Create Post";
        include("../includes/elements/head.php");
    ?>
    <body class="bg-neutral-300 pt-50px overflow-x-hidden sm:bg-gradient-to-br sm:from-cyan-300 sm:via-purple-300 sm:to-orange-300">
        <?php
            $currentPage = CurrentPage::Post;
            include("../includes/elements/navbar.php");
        ?>
        <div class="w-screen">
            <div class="w-screen min-h-screen bg-neutral-300 border-x border-x-neutral-400 pt-10px pb-50px sm:w-600px mx-auto">
                <div class="block font-bold fixed bottom-10px text-right w-screen sm:w-600px z-50">
                    <a href="logout.php" class="inline text-neutral-700 hover:text-red-700 font-bold bg-neutral-100 rounded-md px-7px pb-4px drop-shadow-lg duration-200 m-15px">Logout</a>
                </div>
                <div class="mx-auto w-5/6 h-auto border-t bg-neutral-100 border-neutral-200 pb-10px rounded-lg">
                    <form class="anchor-form" action="<?php htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST" enctype="multipart/form-data">
                        <div class="relative w-full h-47px border-t-1 border-neutral-200">
                            <span class="absolute px-4px py-2px mx-3px my-6px bg-neutral-100">
                                <img class="w-30px aspect-square fill-neutral-600 inline-block rounded-full" src="<?php echo $client->getUserImage()->getImage(); ?>" alt="Your profile photo" />
                                <span class="relative top-1px px-5px inline-block text-neutral-900 font-bold"><?php echo $client->getUsername(); ?></span>
                            </span>
                        </div>
                        <div class="relative w-full h-auto aspect-square bg-gradient-to-r from-cyan-200 to-blue-200 overflow-hidden">
                            <img class="absolute top-0 left-0 w-full h-auto aspect-square anchor-imagePreview border-none object-cover" src="<?php echo $defaultImage->getImage(); ?>" alt="<?php echo $defaultImage->getAlt(); ?>" />
                            <label class="flex absolute top-0 left-0 cursor-pointer font-bold text-3xl w-full h-full items-center text-center" for="input-upload_image">
                                <div class="mx-auto px-50px py-50px pb-[55px] outline-neutral-700 rounded-md outline-dashed duration-500 backdrop-blur-md bg-neutral-100/[.4] hover:px-60px hover:py-60px hover:pb-[65px]">
                                    <span class="inline w-full text-center text-neutral-700">Upload Image</span>
                                </div>
                            </label>
                            <input 
                                class="absolute -top-100px anchor-image anchor-input" 
                                name="image" 
                                type="file" 
                                id="input-upload_image"
                                accept="image/*"
                                required
                                aria-required="true"
                                tabindex=0>
                        </div>
                        <span class="text-red-500 px-10px anchor-input-upload_image-error"><?php echo $errors["image"]; ?></span>
                        <div class="relative p-10px pt-25px">
                            <span class="absolute top-5px left-10px text-neutral-400 text-xs font-bold">Posted on: <?php echo date("Y-m-d H:i:s", substr(time(), 0 , -3)); ?></span>
                            <span class="absolute top-5px right-10px text-rose-800 opacity-75 text-xs">all fields are required</span>
                            <label class="font-bold" for="input-text_description">Description</label><br>
                            <div class="relative shadow rounded-md overflow-hidden h-150px mt-5px">
                                <div class="shadow-inset-lg h-full">
                                    <span class="absolute right-20px bottom-11px text-neutral-400 font-bold"><span class="anchor-characterCount">0</span>/255</span>
                                    <textarea 
                                        class="resize-none w-full h-full px-20px py-11px bg-transparent overflow-y-auto anchor-description anchor-input anchor-textarea"
                                        id="input-text_description"
                                        name="description"
                                        placeholder="Say something about your post..."
                                        required
                                        aria-required="true"></textarea>
                                </div>
                            </div>
                            <span class="text-red-500 anchor-input-text_description-error"><?php echo $errors["description"]; ?></span>
                            <br>
                            <label class="font-bold" for="input-text_alt">Alt Description</label><br>
                            <p class="text-neutral-600 text-sm">
                                Explain what your image looks like.<br>
                                This will be helpful to those who may be vision impaired or have other disabilities
                                preventing them from seeing your image.
                            </p>
                            <div class="relative shadow rounded-md overflow-hidden h-150px mt-5px">
                                <div class="shadow-inset-lg h-full">
                                    <span class="absolute right-20px bottom-11px text-neutral-400 font-bold"><span class="anchor-characterCount">0</span>/255</span>
                                    <textarea 
                                        class="resize-none w-full h-full px-20px py-11px bg-transparent overflow-y-auto anchor-alt anchor-input anchor-textarea"
                                        id="input-text_alt"
                                        name="alt"
                                        placeholder="Say something about your post..."
                                        required
                                        aria-required="true"></textarea>
                                </div>
                            </div>
                            <span class="text-red-500 anchor-input-text_alt-error"><?php echo $errors["alt"]; ?></span>
                            <input
                                class="w-full h-full px-20px py-11px pt-8px mt-20px duration-200 cursor-pointer font-bold rounded-md anchor-postButton"
                                type="submit"
                                value="Post >>>"
                            >
                            <span class="text-red-500"><?php echo $errors["generic"]; ?></span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <script>
            "use strict";
            $(() => {

                const turnPostButton_Gray = () => {
                    $(".anchor-postButton").removeClass("text-neutral-100");
                    $(".anchor-postButton").removeClass("bg-sky-600");
                    $(".anchor-postButton").removeClass("hover:bg-sky-500");

                    $(".anchor-postButton").addClass("text-neutral-800");
                    $(".anchor-postButton").addClass("bg-neutral-400");

                    $(".anchor-postButton").prop("title","");
                    $(".anchor-postButton").prop("disabled",true);
                }

                const turnPostButton_Blue = () => {
                    $(".anchor-postButton").addClass("text-neutral-100");
                    $(".anchor-postButton").addClass("bg-sky-600");
                    $(".anchor-postButton").addClass("hover:bg-sky-500");

                    $(".anchor-postButton").removeClass("text-neutral-800");
                    $(".anchor-postButton").removeClass("bg-neutral-400");
                    $(".anchor-postButton").prop("title","Post picture");
                    $(".anchor-postButton").prop("disabled",false);
                }

                $(".anchor-image").on("change",(e) => {
                    const fileSizeKB = Math.round(e.target.files[0].size / 1024);
                    console.log(fileSizeKB);
                    if(fileSizeKB > 16000) {
                        $(".anchor-input-upload_image-error").text("Your image must be under 16MB!");
                        return;
                    }
                    const url = URL.createObjectURL(e.target.files[0]);
                    $(".anchor-imagePreview").prop("src",url);
                    $(".anchor-imagePreview").prop("alt","A picture, the one you choose to upload from your filesystem.");
                });

                $(".anchor-input").on("change",(e) => {
                    const image = $(".anchor-image").val();
                    const imageAlt = $(".anchor-alt").val();
                    const description = $(".anchor-description").val();

                    if(!image) return turnPostButton_Gray();
                    if(!imageAlt) return turnPostButton_Gray();
                    if(!description) return turnPostButton_Gray();

                    if(description.length > 255) {
                        $(".anchor-input-text_description-error").text("Description must be under 255 characters.");
                        return turnPostButton_Gray();
                    }
                    if(imageAlt.length < 15) {
                        $(".anchor-input-text_alt-error").text("Please use more to describe your image.");
                        return turnPostButton_Gray();
                    } else {
                        $(".anchor-input-text_alt-error").text("");
                    }
                    if(imageAlt.length > 255) {
                        $(".anchor-input-text_alt-error").text("Image Alt must be under 255 characters.");
                        return turnPostButton_Gray();
                    }

                    turnPostButton_Blue();
                });
                
                $(".anchor-input").val("");
                turnPostButton_Gray();
            });
        </script>
    </body>
</html>