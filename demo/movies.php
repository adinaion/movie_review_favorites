<?php require_once("includes/header.php"); ?>

<h1>Movies</h1>

<div class="row justify-content-center">

    <?php
    foreach ($movies as $movie) {
    ?>

        <div class="col-lg-3 col-sm-12 col-md-12" id="<?php echo $movie['id']; ?>">
            <?php require("includes/archive-movie.php"); ?>
        </div>

    <?php
    }
    ?>

</div>

<?php require_once("includes/footer.php"); ?>