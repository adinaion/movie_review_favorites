<form action="search-results.php" method="GET" class="d-flex" role="search">
    <input value="<?php
                    if (!empty($_GET) && isset($_GET['search']))
                        echo $_GET['search'];
                    ?>" name="search" class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
    <button class="btn btn-outline-primary" type="submit">Search</button>
</form>