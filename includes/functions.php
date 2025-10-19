
<?php
function getAlbums(PDO $conn, array $filters = []): array {
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

    if (!empty($filters['artist'])) {
        $conditions[] = "ar.artist_name LIKE ?";
        $params[] = "%" . $filters['artist'] . "%";
    }
    if (!empty($filters['year'])) {
        $conditions[] = "YEAR(al.album_date) = ?";
        $params[] = $filters['year'];
    }
    if (!empty($filters['title'])) {
        $conditions[] = "al.album_name LIKE ?";
        $params[] = "%" . $filters['title'] . "%";
    }
    if (!empty($filters['type'])) {
        $conditions[] = "al.album_type LIKE ?";
        $params[] = "%" . $filters['type'] . "%";
    }

    if ($conditions) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }

    $sql .= " GROUP BY al.id ORDER BY al.album_date DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function addArtist(PDO $conn, string $artist_name, ?string $real_name = null, ?string $city = null): ?int {
    $stmt = $conn->prepare("INSERT INTO artist (artist_name, real_name, city) VALUES (?, ?, ?)");
    $stmt->execute([$artist_name, $real_name, $city]);
    return $conn->lastInsertId();
}

function addAlbum(PDO $conn, string $title, string $date, string $type, array $artist_ids = []): ?int {
    $stmt = $conn->prepare("INSERT INTO album (album_name, album_date, album_type) VALUES (?, ?, ?)");
    $stmt->execute([$title, $date, $type]);
    $album_id = $conn->lastInsertId();

    if (!empty($artist_ids)) {
        $rel = $conn->prepare("INSERT INTO album_artist (album_id, artist_id) VALUES (?, ?)");
        foreach ($artist_ids as $artist_id) {
            $rel->execute([$album_id, $artist_id]);
        }
    }
    return $album_id;
}

function findOrCreateArtist(PDO $conn, string $name): ?int {
    $stmt = $conn->prepare("SELECT id FROM artist WHERE artist_name = ?");
    $stmt->execute([$name]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) return $row['id'];

    $stmt = $conn->prepare("INSERT INTO artist (artist_name) VALUES (?)");
    $stmt->execute([$name]);
    return $conn->lastInsertId();
}

function addAlbumFull(PDO $conn, array $data, array $artist_ids = []): ?int {
    $stmt = $conn->prepare("INSERT INTO album (album_name, album_date, album_genre, album_cover, album_type, album_label) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $data['name'],
        $data['date'],
        $data['genre'],
        $data['cover'],
        $data['type'],
        $data['label']
    ]);
    $album_id = $conn->lastInsertId();

    if (!empty($artist_ids)) {
        $rel = $conn->prepare("INSERT INTO album_artist (album_id, artist_id) VALUES (?, ?)");
        foreach ($artist_ids as $artist_id) {
            $rel->execute([$album_id, $artist_id]);
        }
    }
    return $album_id;
}

function addsong(PDO $conn, int $album_id, array $song_data, array $artist_roles): ?int {
    $stmt = $conn->prepare("INSERT INTO song (album_id, song_number, title, rating, tags, release_date) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $album_id,
        $song_data['song_number'],
        $song_data['title'],
        $song_data['rating'],
        $song_data['tags'],
        $song_data['release_date']
    ]);
    $song_id = $conn->lastInsertId();

    $rel = $conn->prepare("INSERT INTO song_artist (song_id, artist_id, role) VALUES (?, ?, ?)");
    foreach ($artist_roles as $role => $artist_ids) {
        foreach ($artist_ids as $artist_id) {
            $rel->execute([$song_id, $artist_id, $role]);
        }
    }
    return $song_id;
}

function getAlbumById(PDO $conn, int $id): ?array {
    $sql = "
        SELECT 
            al.id AS album_id,
            al.album_name,
            al.album_date,
            al.album_cover,
            GROUP_CONCAT(ar.artist_name SEPARATOR '; ') AS artists
        FROM album al
        LEFT JOIN album_artist aa ON al.id = aa.album_id
        LEFT JOIN artist ar ON aa.artist_id = ar.id
        WHERE al.id = ?
        GROUP BY al.id
        LIMIT 1
    ";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}

function getsongsByAlbum(PDO $conn, int $album_id): array {
    $stmt = $conn->prepare("SELECT id, song_number, title, rating, tags, release_date FROM song WHERE album_id = ? ORDER BY song_number ASC");
    $stmt->execute([$album_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAllArtists(PDO $conn): array {
    $stmt = $conn->query("SELECT id, artist_name FROM artist ORDER BY artist_name ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
