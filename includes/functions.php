<?php
function getAlbums($conn, $filters = []) {
    $sql = "
        SELECT 
            al.id AS album_id,
            al.album_name,
            al.album_date,
            al.album_cover,
            al.album_type,
            GROUP_CONCAT(ar.artist_name SEPARATOR ' & ') AS artists
        FROM album al
        LEFT JOIN album_artist aa ON al.id = aa.album_id
        LEFT JOIN artist ar ON aa.artist_id = ar.id
        WHERE 1=1
    ";

    $params = [];
    $types = "";

    if (!empty($filters['artist'])) {
        $sql .= " AND ar.artist_name LIKE ? ";
        $params[] = "%" . $filters['artist'] . "%";
        $types .= "s";
    }

    if (!empty($filters['year'])) {
        $sql .= " AND YEAR(al.album_date) = ? ";
        $params[] = $filters['year'];
        $types .= "i";
    }

    if (!empty($filters['title'])) {
        $sql .= " AND al.album_name LIKE ? ";
        $params[] = "%" . $filters['title'] . "%";
        $types .= "s";
    }

    if (!empty($filters['type'])) {
        $sql .= " AND al.album_type LIKE ? ";
        $params[] = "%" . $filters['type'] . "%";
        $types .= "s";
    }

    $sql .= " GROUP BY al.id ORDER BY al.album_date DESC";

    $stmt = $conn->prepare($sql);

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $albums = [];
    while ($row = $result->fetch_assoc()) {
        $albums[] = $row;
    }

    return $albums;
}



function addArtist($conn, $artist_name, $real_name, $city) {
    $stmt = $conn->prepare("INSERT INTO artist (artist_name, real_name, city) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $artist_name, $real_name, $city);
    return $stmt->execute();
}

function addAlbum($conn, $titolo, $artista, $anno) {
    $stmt = $conn->prepare("INSERT INTO album (album_name, artist_name, album_date) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $titolo, $artista, $anno);
    return $stmt->execute();
}
?>

