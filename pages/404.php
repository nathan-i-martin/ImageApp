<?php
    include("../includes/models.php");
    include("../includes/elements/validate.php");
?>
<!DOCTYPE html>
<html>
    <?php
        $title = "404";
        include("../includes/elements/head.php");
    ?>
    <body class="bg-neutral-300 pt-50px overflow-x-hidden sm:bg-gradient-to-br sm:from-cyan-300 sm:via-purple-300 sm:to-orange-300">
        <?php
            $currentPage = CurrentPage::Feed;
            include("../includes/elements/navbar.php");
        ?>
        <div class="w-screen">
            <div class="w-screen min-h-screen bg-neutral-100 border-x border-x-neutral-400 pt-50px pb-50px sm:w-600px mx-auto">
                <div class="align-center py-10px">
                    <h2 class="text-neutral-700 font-bold text-7xl text-center">404</h2>
                </div>
                <p class="w-2/3 mx-auto text-neutral-600 pt-10px text-center"><?php echo clean_input($_GET["e"] ?? "Hmmm... We couldn't find that page.") ?></p>
            </div>
        </div>
    </body>
</html>