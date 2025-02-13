<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ion Florina-Adina</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <?php define('initiale', 'IFA');?>

    <nav class="navbar navbar-expand-lg fixed-top" style="background-color:rgb(219, 101, 203);">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"> <?php echo initiale; ?> </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                    <?php
                    $menu_items = [
                        [
                            "title" => "Home",
                            "link" => "index.php"
                        ],
                        [
                            "title" => "Movies",
                            "link" => "movies.php"
                        ],
                        [
                            "title" => "Contact",
                            "link" => "contact.php"
                        ]
                    ];

                    foreach ($menu_items as $item) { ?>

                        <li class="nav-item">
                            <a class="nav-link <?php if (basename($_SERVER['PHP_SELF']) === $item['link']) echo ' active'; ?>" href="<?php echo $item['link']; ?>"
                                <?php if (basename($_SERVER['PHP_SELF']) === $item['link']) echo 'aria-current="page"'; ?>> <?php echo $item['title']; ?> </a>
                        </li>

                    <?php
                    }
                    ?>

                </ul>

                <?php require('includes/search-form.php'); ?>

            </div>
        </div>
    </nav>

    <div class="container">


        <?php $movies = json_decode(file_get_contents('./assets/movies-list-db.json'), true)['movies'];?>


        <?php include("includes/functions.php"); ?>