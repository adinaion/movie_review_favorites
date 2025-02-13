<?php require_once('includes/header.php'); ?>

<?php if (!empty($_GET) && isset($_GET['search'])) { ?>

    <?php if (strlen($_GET['search']) < 3) { ?>
        <h1>Vă rugăm să introduceți mai multe caractere în câmpul de căutare.</h1>
    <?php
        exit();
    } ?>

    <h1>Search results for: <?php echo $_GET['search']; ?> </h1>
    <?php require('includes/search-form.php'); ?>

    <?php
    $fraza_cautata = $_GET['search'];
    $movies_searched = array_values(array_filter($movies, function ($movie) use ($fraza_cautata) {
        return stripos($movie['title'], $fraza_cautata) !== false;
    }));

    if (!empty($movies_searched)) {
    ?>
        <div class="row justify-content-center">
            <?php
            foreach ($movies_searched as $movie) {
            ?>

                <div class="col-lg-3 col-sm-12 col-md-12" id="<?php echo $movie['id']; ?>">
                    <?php require("includes/archive-movie.php"); ?>
                </div>

            <?php
            }
            ?>
        </div>
    <?php
    } else { ?>
        <h1>Zero rezultate pentru această căutare. Reformulați cererea de căutare.</h1>
    <?php
    }
} else { ?>
    <h1>Ați accesat această pagină în mod greșit.</h1>
<?php } ?>

<?php require_once('includes/footer.php'); ?>