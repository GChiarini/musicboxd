<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

$filters = [
    'artist' => $_GET['artist'] ?? null,
    'year'   => $_GET['year'] ?? null,
    'title'  => $_GET['title'] ?? null,
    'type'   => $_GET['type'] ?? null
];

$albums = getAlbums($conn, $filters);

include 'includes/header.php';
?>

<main>
    <h1>Album Musicali</h1>
    <?php include 'templates/filter_form.php'; ?>

    <div class="albumGrid">
        <?php if (!empty($albums)): ?>
            <?php foreach ($albums as $album): ?>
                <?php include 'templates/album_card.php'; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Nessun album trovato con questi criteri.</p>
        <?php endif; ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?>