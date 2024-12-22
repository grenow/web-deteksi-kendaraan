<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Real-time Object Detection</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h1 class="text-center mb-4">Real-time Object Detection</h1>

        <!-- Form Upload -->
        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title">Upload Video</h3>
            </div>
            <div class="card-body">
                <form id="upload-form" action="upload.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="video" class="form-label">Choose a video file:</label>
                        <input type="file" id="video" name="video" class="form-control" accept="video/*" required>
                    </div>
                    <button type="submit" class="btn btn-success">Upload</button>
                </form>
            </div>
        </div>

        <!-- List of Videos -->
        <div class="card shadow mb-4">
            <div class="card-header bg-secondary text-white">
                <h3 class="card-title">Available Videos</h3>
            </div>
            <div class="card-body">
                <ul id="video-list" class="list-group">
                    <!-- Video links will be dynamically populated here -->
                </ul>
            </div>
        </div>

        <!-- Video Stream -->
        <div class="card shadow">
            <div class="card-header bg-info text-white">
                <h3 class="card-title"></h3>
            </div>
            <div class="card-body text-center">
                <video id="video-stream" controls style="max-width: 100%; height: auto;">
                    <source src="" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Fetch daftar video dari server
        async function fetchVideos() {
            const response = await fetch('/videos.php');
            const data = await response.json();
            const videoList = document.getElementById('video-list');
            videoList.innerHTML = '';

            data.videos.forEach(video => {
                const listItem = document.createElement('li');
                listItem.className = 'list-group-item d-flex justify-content-between align-items-center';

                const link = document.createElement('a');
                link.href = `/video/${video}`;
                link.textContent = video;
                link.className = 'text-decoration-none';
                link.onclick = function (e) {
                    e.preventDefault();
                    playVideo(link.href);
                };

                const playButton = document.createElement('button');
                playButton.textContent = 'Play';
                playButton.className = 'btn btn-sm btn-primary';
                playButton.onclick = function (e) {
                    e.preventDefault();
                    playVideo(link.href);
                };

                listItem.appendChild(link);
                listItem.appendChild(playButton);
                videoList.appendChild(listItem);
            });
        }

        // Mainkan video stream
        function playVideo(url) {
            const videoElement = document.getElementById('video-stream');
            videoElement.src = url;
        }

        // Fetch video saat halaman dimuat
        window.onload = fetchVideos;
    </script>
</body>
</html>
