<form method="GET" class="filter-form">
    <label for="artist">Artista:</label>
    <input type="text" id="artist" name="artist"
           value="<?= htmlspecialchars($_GET['artist'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

    <label for="year">Anno:</label>
    <input type="number" id="year" name="year"
           value="<?= htmlspecialchars($_GET['year'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

    <label for="title">Titolo:</label>
    <input type="text" id="title" name="title"
           value="<?= htmlspecialchars($_GET['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

    <label for="type">Tipo:</label>
    <input type="text" id="type" name="type"
           value="<?= htmlspecialchars($_GET['type'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

    <button type="submit">Filtra</button>
    <a href="index.php" class="reset-link">Reset</a>
</form>