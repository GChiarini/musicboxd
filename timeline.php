<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Timeline Album</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .album-card {
            max-width: 180px;
            margin-top: 40px;
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 10px;
            background: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .album-card:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }
        .album-card img {
            max-width: 180px;
            height: auto;
            border-radius: 4px;
            margin-bottom: 8px;
        }
        .album-info strong {
            display: block;
            margin-bottom: 4px;
        }
        .album-genre {
            margin-top: 4px;
            font-size: 13px;
            color: #666;
        }

        #albumTimeline {
            display: flex;
            flex-wrap: wrap;     /* per più righe se necessario */
            gap: 16px;           /* spazio tra le card */
            justify-content: center;  /* centra le card orizzontalmente */
        }
    </style>
</head>
<body>

<?php

include 'db.php';

$albums = [];
$sql = "SELECT album_name, artist_name, album_date, album_genre, album_cover 
        FROM album ORDER BY album_date DESC";
$res = $conn->query($sql);
if ($res && $res->num_rows > 0) {
    while($row = $res->fetch_assoc()) {
        $albums[] = $row;
    }
}
$conn->close();
?>

<!-- Timeline scrollabile -->
<div id="albumTimeline">

</div>

<script>
// Dataset da PHP → JS
const albums = <?php echo json_encode($albums); ?>;
const timeline = document.getElementById("albumTimeline");

// Render timeline con card sotto linea e marker anno
function renderTimeline(data) {
    if (data.length === 0) {
        timeline.innerHTML = "<p>Nessun album trovato</p>";
        return;
    }

    let html = "";

    data.forEach(a => {
        const year = new Date(a.album_date).getFullYear();

        // Card album
        html += `
            <div class="album-card">
                    <img src="${a.album_cover}" alt="${a.album_name} - ${a.artist_name}">
                    <div class="album-info">
                        <strong>${a.album_name}</strong>
                        <span>${a.artist_name}</span><br>
                        <span>${a.album_date}</span>
                        <div class="album-genre">${a.album_genre}</div>
                    </div>
            </div>
        `;
    });

    timeline.innerHTML = html;
}

// Init
renderTimeline(albums);
</script>

</body>
</html>
