<?php
$albumName  = htmlspecialchars($album['album_name'], ENT_QUOTES, 'UTF-8');
$artistName = htmlspecialchars($album['artists'], ENT_QUOTES, 'UTF-8');
$albumDate  = htmlspecialchars($album['album_date'], ENT_QUOTES, 'UTF-8');
$cover      = htmlspecialchars($album['album_cover'] ?: 'assets/img/default_cover.jpg', ENT_QUOTES, 'UTF-8');
$albumId    = urlencode($album['album_id']);
?>

<div class="album-card" role="group" aria-label="Album: <?= $albumName ?> di <?= $artistName ?>">
    <img src="<?= $cover ?>" loading="lazy" decoding="async" width="200" height="200" />

    <div class="album-info">
        <strong>
            <a href="album.php?id=<?= $albumId ?>"
                            aria-label="Vai alla pagina di <?= $albumName ?> di <?= $artistName ?>">
                <?= $albumName ?>
            </a>
        </strong>
        <span><?= $artistName ?></span>
        <span><?= $albumDate ?></span>
    </div>
</div>