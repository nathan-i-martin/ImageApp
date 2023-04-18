<?php
    include("../includes/models.php");
    $client = Client::getClient();
    if($client instanceof Client)
        header("Location: feed.php");

    include("../includes/data_processors/register.php");
?>
<!DOCTYPE html>
<html>
    <?php
        $title = "Register";
        include("../includes/elements/head.php");
    ?>
    <body class="bg-neutral-300 pt-50px overflow-x-hidden sm:bg-gradient-to-br sm:from-cyan-300 sm:via-purple-300 sm:to-orange-300">
        <?php
            $currentPage = CurrentPage::LoggedOut;
            include("../includes/elements/navbar.php");
        ?>
        <div class="w-screen">
            <div class="w-screen min-h-screen bg-neutral-300 border-x border-x-neutral-400 pt-50px pb-50px bg-gradient-to-br from-cyan-300 via-purple-300 to-orange-300 sm:w-600px sm:bg-neutral-300 sm:bg-none mx-auto">
                <div class="mx-auto w-5/6 h-auto border-t bg-neutral-100 border-neutral-200 pb-10px rounded-lg">
                    <span class="text-center block pt-10px text-4xl">
                        <a class="font-thin text-pink-900" href="login.php" alt="Account login page">Login</a> |
                        <span class="font-bold">Register</span>
                    </span>
                    <form class="anchor-form" action="<?php echo clean_input($_SERVER["PHP_SELF"]);?>" method="POST">
                        <div class="relative p-10px px-40px">
                            <label for="input-text_username">Username<span class="text-red-500">*</span></label>
                            <input
                                class="w-full px-20px py-11px pt-8px bg-transparent shadow-inset-sm rounded-md"
                                type="text"
                                name="username"
                                id="input-text_username"
                                placeholder="Username"
                                required
                                aria-required="true"
                            >
                            <span class="text-red-500"><?php echo $errors["username"]; ?></span>
                        </div>
                        <div class="relative pt-15px px-40px">
                            <label for="input-password_password">Password<span class="text-red-500">*</span></label>
                            <input
                                class="w-full px-20px py-11px pt-8px bg-transparent shadow-inset-sm rounded-md"
                                type="password"
                                name="password"
                                id="input-password_password"
                                placeholder="Password"
                                required
                                aria-required="true"
                            >
                            <span class="text-red-500"><?php echo $errors["password"]; ?></span>
                        </div>
                        <div class="relative py-10px px-40px">
                            <label for="input-password_password-verify">Verify Password<span class="text-red-500">*</span></label>
                            <input
                                class="w-full px-20px py-11px pt-8px bg-transparent shadow-inset-sm rounded-md"
                                type="password"
                                name="password-verify"
                                id="input-password_password-verify"
                                placeholder="Verify your password"
                                required
                                aria-required="true"
                            >
                            <span class="text-red-500"><?php echo $errors["password-verify"]; ?></span>
                        </div>
                        <div class="relative p-10px px-40px">
                            <input
                                class="w-full px-20px py-11px pt-8px bg-sky-600 duration-200 hover:bg-sky-500 cursor-pointer text-neutral-100 font-bold rounded-md"
                                type="submit"
                                value="Register >>>"
                            >
                            <span class="text-red-500"><?php echo $errors["generic"]; ?></span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>