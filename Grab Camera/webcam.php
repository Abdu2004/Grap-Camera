<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['image'])) {
    $imageData = $_POST['image'];

    // Decode the base64 image
    $imageParts = explode(";base64,", $imageData);
    $imageTypeAux = explode("image/", $imageParts[0]);
    $imageType = $imageTypeAux[1];
    $imageBase64 = base64_decode($imageParts[1]);

    // Set the folder path
    $folderPath = 'storage/emulated/0/siwes/Practice/';

    // Ensure the folder exists
    if (!file_exists($folderPath)) {
        mkdir($folderPath, 0777, true); // Create the folder with appropriate permissions
    }

    // Generate a unique filename
    $fileName = uniqid() . '.' . $imageType;
    $filePath = $folderPath . $fileName;

    // Save the image
    file_put_contents($filePath, $imageBase64);

    echo "Image successfully uploaded to folder: <a href='$filePath'>$filePath</a>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Webcam Capture</title>
</head>
<body>
    <h1>Webcam Access Demo</h1>
    <p>Click the button below to allow camera access and capture an image.</p>
    <video id="video" autoplay></video>
    <canvas id="canvas" style="display:none;"></canvas>
    <button id="capture">Capture Photo</button>
    <img id="photo" alt="Captured Photo" style="display:none;"/>

    <form id="uploadForm" method="POST" style="display:none;">
        <input type="hidden" name="image" id="imageData">
        <button type="submit">Upload Image</button>
    </form>

    <script>
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const photo = document.getElementById('photo');
        const imageData = document.getElementById('imageData');
        const uploadForm = document.getElementById('uploadForm');

        // Request camera access
        navigator.mediaDevices.getUserMedia({ video: true })
            .then(stream => {
                video.srcObject = stream;

                // Add event listener to ensure video is ready before capturing
                video.addEventListener('canplay', () => {
                    console.log("Webcam is ready.");
                    video.play();
                });
            })
            .catch(err => {
                console.error("Error accessing camera: ", err);
            });

        document.getElementById('capture').addEventListener('click', () => {
            const context = canvas.getContext('2d');

            // Ensure the canvas dimensions match the video dimensions
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;

            // Draw the video frame onto the canvas
            context.drawImage(video, 0, 0, canvas.width, canvas.height);

            // Convert the canvas content to an image
            const data = canvas.toDataURL('image/png');
            photo.src = data;
            photo.style.display = 'block';
            uploadForm.style.display = 'block';

            // Prepare the image data for upload
            imageData.value = data;
        });
    </script>
</body>
</html>