<head>
    <!-- Need to implement page-specific title's down the road... -->
    <title>Image-App | <?php echo $title; ?></title>
    <script src="https://code.jquery.com/jquery-3.6.1.slim.min.js" integrity="sha256-w8CvhFs7iHNVUtnSP0YKEg00p9Ih13rlL9zGqvLdePA=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://use.typekit.net/ixn3gur.css">
    <style>
        a:hover {
            color: black;
        }
    </style>
    <script src="../includes/elements/textareaCounter.js"></script>
    <!--
        If this was a React app or something similar I would specifically implement
        a build process to generate a production version for tailwind.
        Since tailwind isn't the focus of this class, I'm not concerned about using
        the dev code.
    -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script type="module">
        import config from "../../tailwind.config.js";
        tailwind.config = config;
    </script>
</head>