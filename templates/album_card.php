<?php
$albumName  = htmlspecialchars($album['album_name']);
$artistName = htmlspecialchars($album['artists']);
$albumDate  = htmlspecialchars($album['album_date']);
$cover      = htmlspecialchars($album['album_cover'] ?: 'assets/img/default_cover.jpg');
$albumId    = urlencode($album['album_id']);
?>
<div class="album-card">
    <img src="<?= $cover ?>" alt="Copertina di <?= $albumName ?> di <?= $artistName ?>" loading="lazy">
    <div class="album-info">
        <strong>
            <a href="album.php?id=<?= $albumId ?>"><?= $albumName ?></a>
        </strong>
        <span><?= $artistName ?></span>
        <span><?= $albumDate ?></span>
    </div>
</div>
