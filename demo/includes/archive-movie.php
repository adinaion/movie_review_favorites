    <div class="card">
        <img src="<?php echo $movie['posterUrl']; ?>" class="card-img-top" alt="...">
        <div class="card-body">
            <h5 class="card-title"><strong> <?php echo $movie['title']; ?> </strong></h5>
            <p class="card-text"> <?php
                                    $plot_result = $movie['plot'];
                                    if (strlen($plot_result) > 100) {
                                        $plot_result = substr($plot_result, 0, 100);
                                        echo $plot_result . '...';
                                    } else {
                                        echo $plot_result;
                                    }
                                    ?> </p>
            <a href="movie.php?movie_id=<?php echo $movie['id']; ?>" class="btn btn-primary">Read more</a>
        </div>
    </div>