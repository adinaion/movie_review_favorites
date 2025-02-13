<?php
ob_start();
session_start();
require_once 'includes/header.php';


$json_file_path = 'assets/movie-favorites.json';

//functie citire, verificare si returnare continut fisier sub forma de array
function read_favorite_data($file_path)
{
    if (!file_exists($file_path)) {
        //daca fisierul nu exista, returnam un array gol
        return [];
    }

    $file_content = file_get_contents($file_path);
    return json_decode($file_content, true) ?: [];
}

//functie pentru salvarea de continut sub forma de JSON(sir de caractere) in fisier
function save_favorite_data($file_path, $data)
{
    file_put_contents($file_path, json_encode($data, JSON_PRETTY_PRINT));
}

//initializare continut fisier intr-un array
$favorite_data = read_favorite_data($json_file_path);

//initializare lista filme favorite daca nu exista
if (!isset($_SESSION['favorites'])) {
    $_SESSION['favorites'] = [];
}

//verificare existenta cookie 'favorite movies'
$favorite_movies = [];
if (isset($_COOKIE['favorite_movies'])) {
    //preluam valoarea cookie-ului si o decodificam in array
    $favorite_movies = json_decode($_COOKIE['favorite_movies'], true);

    //verificare daca decodificarea a esuat (ex cand cookie-ul nu are format JSON valid)
    if (!is_array($favorite_movies)) {
        $favorite_movies = [];
    }
}

//procesare  cerere POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['favorite_movie'])) {
    $movie_id = (int)($_GET['movie_id']);
    $favorite_movie = (int)($_POST['favorite_movie']);

    //adaugare sau stergere film in lista de favorite (array-ul asociativ din $_SESSION)
    if ($favorite_movie === 1) {
        //adaugare in lista de favorite
        if (!in_array($movie_id, $_SESSION['favorites'])) {
            $_SESSION['favorites'][] = $movie_id;

            //setez un cookie pt filmul adaugat la favorite
            // setcookie('favorite_movies', $movie_id, time()+60*60*24*365);
            if (!in_array($movie_id, $favorite_movies)) {
                $favorite_movies[] = $movie_id;
            }
        }

        //prelucrare adaugare in fisier movie_favorites.json
        if (isset($favorite_data[$movie_id])) {
            $favorite_data[$movie_id]++;
        } else {
            $favorite_data[$movie_id] = 1;
        }
    } else {
        //stergere din lista de favorite
        if (($key = array_search($movie_id, $_SESSION['favorites'])) !== false) {
            unset($_SESSION['favorites'][$key]);

            //update cookie pt film eliminat din lista de favorite
            //setcookie('favorite_movies', '', time() - 1);
            if (($key = array_search($movie_id, $favorite_movies)) !== false) {
                unset($favorite_movies[$key]);
            }
        }

        //prelucrare stergere in fisier movie_favorites.json
        if (isset($favorite_data[$movie_id]) && $favorite_data[$movie_id] > 0) {
            $favorite_data[$movie_id]--;
        }
    }

    //salvez lista actualizata si codificata in JSON ca valoare pt cookie-ul meu
    setcookie('favorite_movies', json_encode($favorite_movies), time() + 60 * 60 * 24 * 365);

    //salvare continut prelucrat final in fisier
    save_favorite_data($json_file_path, $favorite_data);
}



if (!empty($_GET) && isset($_GET['movie_id']) && (int)($_GET['movie_id']) >= 1 && (int)($_GET['movie_id']) <= 146) {

    $movie_array = array_values(array_filter($movies, function ($movie) {
        return $movie['id'] == $_GET['movie_id'];
    }));


    //verificam daca filmul accesat este deja in lista de favorite
    $is_favorite = in_array($_GET['movie_id'], $favorite_movies);

    $additions_count = isset($favorite_data[$_GET['movie_id']]) ? $favorite_data[$_GET['movie_id']] : 0;



    // Variabilă pentru a verifica dacă formularul a fost trimis
    $form_submitted = false;

    // Conectare la baza de date
    $conn = new mysqli('localhost', 'php-user', 'php-password', 'php-proiect', 10010);

    // Verificare conexiune
    if ($conn->connect_error) {
        die('Eroare la conectarea la baza de date: ' . $conn->connect_error);
    }

    // Creare tabelă dacă nu există
    $conn->query("
        CREATE TABLE IF NOT EXISTS reviews (
            id INT AUTO_INCREMENT PRIMARY KEY,
            movie_id INT NOT NULL,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            message TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
");

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
        // Procesăm datele formularului
        $name = $conn->real_escape_string($_POST['name']);
        $email = $conn->real_escape_string($_POST['email']);
        $message = $conn->real_escape_string($_POST['message']);
        $movie_id = (int)$_GET['movie_id'];

        // Salvăm review-ul în baza de date
        $stmt = $conn->prepare("
            INSERT INTO reviews (movie_id, name, email, message) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param('isss', $movie_id, $name, $email, $message);
        $stmt->execute();
        $stmt->close();

        // Setăm variabila pentru a indica faptul că formularul a fost trimis
        $form_submitted = true;
    }

?>

    <h1> <?php echo $movie_array['0']['title']; ?> </h1>

    <div class="row">
        <div class="col-lg-4 col-sm-12 col-md-12">
            <img width="80%" src="<?php echo $movie_array['0']['posterUrl']; ?>">
        </div>
        <div class="col-lg-8 col-sm-12 col-md-12">
            <strong><?php echo $movie_array['0']['year']; ?></strong>
            <br>
            <strong><?php echo $movie_array['0']['title']; ?></strong>

            <!-- badge pentru numarul de adaugari la favorite-->
            <span class="badge bg-primary" style="margin-left:10px;">
                <?php echo $additions_count; ?>
            </span>

            <form action="" method="POST">
                <input type="hidden" name="favorite_movie" value="<?php echo $is_favorite ? 0 : 1; ?>">
                <button class="btn btn-outline-primary" type="submit">
                    <?php echo $is_favorite ? 'Sterge din favorite' : 'Adauga la favorite'; ?>
                </button>
            </form>

            <br>
            <?php echo $movie_array['0']['plot']; ?>
            <br>
            <strong>Directed by</strong> <?php echo $movie_array['0']['director']; ?>
            <br>
            <strong>Runtime</strong> <?php echo runtime_prettier((int)$movie_array['0']['runtime']); ?>
            <br>
            <strong>Cast</strong>
            <ul>
                <?php
                $people_array = explode(', ', $movie_array['0']['actors']);
                foreach ($people_array as $person) {
                ?>
                    <li><?php echo $person; ?></li>
                <?php } ?>
            </ul>
            <strong>Genres </strong>
            <?php
            $genres_array = $movie_array['0']['genres'];

            foreach ($genres_array as $genre) {
                echo $genre;
                if ($genre === $genres_array[count($genres_array) - 1])
                    echo '.';
                else
                    echo ', ';
            }
            ?>

        </div>


        <?php if ($form_submitted): ?>
            <div class="alert alert-info" role="alert">
                Mulțumim pentru review! Mesajul tău a fost trimis cu succes.
            </div>
        <?php else: ?>
            <form method="POST" action="">

                <label for="name" class="form-label">Nume</label>
                <input type="text" class="form-control" id="name" name="name" required>

                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>

                <label for="message" class="form-label">Mesajul tău</label>
                <textarea class="form-control" id="message" name="message" rows="3" required></textarea>


                <input class="form-check-input" type="checkbox" id="agree" name="agree" required>
                <label class="form-check-label" for="agree">
                    Sunt de acord cu procesarea datelor cu caracter personal
                </label>
                <br>
                <button type="submit" name="submit_review" class="btn btn-primary">Trimite Review</button>
            </form>
        <?php endif; ?>
    </div>

    <!-- Afișare review-uri -->
    <h3>Review-uri pentru acest film:</h3>
    <?php
    // Selectăm toate review-urile pentru filmul curent
    $movie_id = (int)$_GET['movie_id'];
    $result = $conn->query("SELECT name, message FROM reviews WHERE movie_id = $movie_id ORDER BY created_at DESC");

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='card mb-3'>";
            echo "<div class='card-body'>";
            echo "<h5 class='card-title'>" . htmlspecialchars($row['name']) . "</h5>";
            echo "<p class='card-text'>" . htmlspecialchars($row['message']) . "</p>";
            echo "</div>";
            echo "</div>";
        }
    } else {
        echo "<p>Nu există review-uri pentru acest film.</p>";
    }
} else {
    ?>
    <h1>Ai ajuns la această pagină în mod incorect.</h1>
    <a class="btn btn-primary" href="movies.php" role="button">Let's go back</a>

<?php
}
ob_end_flush();
require_once('includes/footer.php');
?>