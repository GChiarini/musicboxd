<?php
/**
 * =========================================
 * FUNZIONI DATABASE PRINCIPALI
 * =========================================
 * - getAlbums()         → Ottiene lista album filtrabile
 * - addArtist()         → Aggiunge un artista singolo
 * - addAlbum()          → Aggiunge un album (base)
 * - findOrCreateArtist()→ Ritorna ID artista, creandolo se non esiste
 * - addAlbumFull()      → Crea un album + collega più artisti
 */

// ----------------------
// LEGGI ALBUM
// ----------------------
function getAlbums($conn, $filters = []) {
    $sql = "
        SELECT 
            al.id AS album_id,
            al.album_name,
            al.album_date,
            al.album_cover,
            al.album_type,
            al.album_genre,
            al.album_label,
            GROUP_CONCAT(ar.artist_name SEPARATOR ' & ') AS artists
        FROM album al
        LEFT JOIN album_artist aa ON al.id = aa.album_id
        LEFT JOIN artist ar ON aa.artist_id = ar.id
    ";

    $conditions = [];
    $params = [];
    $types = "";

    if (!empty($filters['artist'])) {
        $conditions[] = "ar.artist_name LIKE ?";
        $params[] = "%" . trim($filters['artist']) . "%";
        $types .= "s";
    }

    if (!empty($filters['year'])) {
        $conditions[] = "YEAR(al.album_date) = ?";
        $params[] = (int)$filters['year'];
        $types .= "i";
    }

    if (!empty($filters['title'])) {
        $conditions[] = "al.album_name LIKE ?";
        $params[] = "%" . trim($filters['title']) . "%";
        $types .= "s";
    }

    if (!empty($filters['type'])) {
        $conditions[] = "al.album_type LIKE ?";
        $params[] = "%" . trim($filters['type']) . "%";
        $types .= "s";
    }

    if ($conditions) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }

    $sql .= " GROUP BY al.id ORDER BY al.album_date DESC";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Errore prepare getAlbums(): " . $conn->error);
        return [];
    }

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    if (!$stmt->execute()) {
        error_log("Errore execute getAlbums(): " . $stmt->error);
        return [];
    }

    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// ----------------------
// AGGIUNGI ARTISTA
// ----------------------
function addArtist($conn, $artist_name, $real_name = null, $city = null) {
    $artist_name = trim($artist_name);
    $real_name   = trim($real_name ?? '');
    $city        = trim($city ?? '');

    if ($artist_name === '') {
        return false;
    }

    $stmt = $conn->prepare("INSERT INTO artist (artist_name, real_name, city) VALUES (?, ?, ?)");
    if (!$stmt) {
        error_log("Errore prepare addArtist(): " . $conn->error);
        return false;
    }

    $stmt->bind_param("sss", $artist_name, $real_name, $city);

    if (!$stmt->execute()) {
        error_log("Errore execute addArtist(): " . $stmt->error);
        return false;
    }

    return $conn->insert_id;
}

// ----------------------
// AGGIUNGI ALBUM BASE
// ----------------------
function addAlbum($conn, $title, $date, $type, $artist_ids = []) {
    $title = trim($title);
    $type  = trim($type);
    $date  = trim($date);

    if ($title === '' || $date === '') {
        return false;
    }

    $stmt = $conn->prepare("INSERT INTO album (album_name, album_date, album_type) VALUES (?, ?, ?)");
    if (!$stmt) {
        error_log("Errore prepare addAlbum(): " . $conn->error);
        return false;
    }

    $stmt->bind_param("sss", $title, $date, $type);

    if (!$stmt->execute()) {
        error_log("Errore execute addAlbum(): " . $stmt->error);
        return false;
    }

    $album_id = $conn->insert_id;

    // Collega artisti se presenti
    if (!empty($artist_ids)) {
        $rel = $conn->prepare("INSERT INTO album_artist (album_id, artist_id) VALUES (?, ?)");
        foreach ($artist_ids as $artist_id) {
            $artist_id = (int)$artist_id;
            $rel->bind_param("ii", $album_id, $artist_id);
            $rel->execute();
        }
    }

    return $album_id;
}

// ----------------------
// CERCA O CREA ARTISTA
// ----------------------
function findOrCreateArtist($conn, $name) {
    $name = trim($name);
    if ($name === '') return false;

    // Cerca artista esistente
    $stmt = $conn->prepare("SELECT id FROM artist WHERE artist_name = ?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        return $row["id"];
    }

    // Se non esiste, crea nuovo artista
    $stmt = $conn->prepare("INSERT INTO artist (artist_name) VALUES (?)");
    $stmt->bind_param("s", $name);
    $stmt->execute();

    return $conn->insert_id;
}

// ----------------------
// CREA ALBUM COMPLETO + COLLEGAMENTO ARTISTI
// ----------------------
function addAlbumFull($conn, $data, $artist_ids = []) {
    $data = array_map('trim', $data);

    $stmt = $conn->prepare("
        INSERT INTO album 
        (album_name, album_date, album_genre, album_cover, album_type, album_label)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    if (!$stmt) {
        error_log("Errore prepare addAlbumFull(): " . $conn->error);
        return false;
    }

    $stmt->bind_param(
        "ssssss",
        $data['name'],
        $data['date'],
        $data['genre'],
        $data['cover'],
        $data['type'],
        $data['label']
    );

    if (!$stmt->execute()) {
        error_log("Errore execute addAlbumFull(): " . $stmt->error);
        return false;
    }

    $album_id = $conn->insert_id;

    // Collega gli artisti
    if (!empty($artist_ids)) {
        $rel = $conn->prepare("INSERT INTO album_artist (album_id, artist_id) VALUES (?, ?)");
        foreach ($artist_ids as $artist_id) {
            $artist_id = (int)$artist_id;
            $rel->bind_param("ii", $album_id, $artist_id);
            $rel->execute();
        }
    }

    return $album_id;
}
?>
