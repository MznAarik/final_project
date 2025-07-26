@extends('admin.layouts.app')

<style>
    .container {
        height: calc(100vh - 90px);
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 20px;
        font-family: Arial, sans-serif;
    }

    #video {
        width: 100%;
        max-width: 500px;
        border: 2px solid #333;
    }

    #canvas {
        display: none;
    }

    #result {
        margin-top: 20px;
        padding: 15px;
        border: 1px solid #ccc;
        width: 100%;
        max-width: 500px;
        background-color: #f9f9f9;
        border-radius: 5px;
        word-break: break-all;
        max-height: 300px;
        overflow-y: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }

    th,
    td {
        padding: 8px;
        border: 1px solid #ddd;
        text-align: left;
    }

    th {
        background-color: #007bff;
        color: white;
    }

    .success {
        background-color: #d4edda;
        color: #155724;
    }

    .error {
        background-color: #f8d7da;
        color: #721c24;
    }

    button {
        padding: 10px 20px;
        margin: 10px;
        color: white;
        cursor: pointer;
        font-weight: bold;
        padding: 0.6rem 1.2rem;
        background: linear-gradient(100deg, rgb(204, 0, 0), rgb(255, 51, 0));
        border-width: initial;
        border-style: none;
        border-color: initial;
        border-image: initial;
        border-radius: 8px;
        transition: background 0.3s, transform 0.3s;
    }

    button:hover {
        transform: translateY(-2px);
        background: linear-gradient(135deg, rgb(255, 51, 0), rgb(204, 0, 0));
    }

    .scan-entry {
        margin-bottom: 15px;
    }

    .tip {
        color: #555;
        font-size: 14px;
        margin-top: 10px;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>

@section('content')
    <main class="container">
        <h2>Scan QR Code</h2>
        <button id="startScan">Scan QR Code</button>
        <video id="video" autoplay playsinline style="display: none;"></video>
        <canvas id="canvas"></canvas>
        <div id="result">Scan result will appear here.</div>
        <p class="tip">Tip: Hold QR code 10–30 cm from webcam, ensure good lighting, and avoid screen glare.</p>
        <audio id="beep" src="/sounds/beep.mp3" preload="auto"></audio>
        </div>

        <script>
            const video = document.getElementById('video');
            const canvas = document.getElementById('canvas');
            const ctx = canvas.getContext('2d');
            const resultDiv = document.getElementById('result');
            const startScanButton = document.getElementById('startScan');
            const beep = document.getElementById('beep');
            let scanning = false;
            let stream = null;
            let lastScanTime = 0;
            const COOLDOWN_MS = 5000;

            startScanButton.addEventListener('click', async () => {
                if (!scanning) {
                    try {
                        stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
                        video.srcObject = stream;
                        video.style.display = 'block';
                        startScanButton.textContent = 'Stop Scanning';
                        scanning = true;
                        console.log('Webcam started: ', stream.getVideoTracks()[0].label);
                        resultDiv.textContent = 'Webcam active, scanning for QR code...';

                        video.onloadedmetadata = () => {
                            if (video.videoWidth > 0 && video.videoHeight > 0) {
                                console.log('Video dimensions: ', video.videoWidth, 'x', video.videoHeight);
                                scan();
                            } else {
                                resultDiv.textContent = 'Error: Video stream has invalid dimensions.';
                                resultDiv.classList.add('error');
                                console.error('Invalid video dimensions');
                                stopScanning();
                            }
                        };
                    } catch (err) {
                        resultDiv.textContent = `Error accessing camera: ${err.message}`;
                        resultDiv.classList.add('error');
                        console.error('Camera access error: ', err);
                        stopScanning();
                    }
                } else {
                    stopScanning();
                }
            });

            async function scan() {
                if (!scanning || !video.srcObject) {
                    console.log('Scanning stopped or no video source');
                    resultDiv.textContent = 'Scanning stopped.';
                    return;
                }

                if (video.videoWidth === 0 || video.videoHeight === 0) {
                    console.warn('Video not ready, retrying...');
                    requestAnimationFrame(scan);
                    return;
                }

                const now = Date.now();
                if (now - lastScanTime < COOLDOWN_MS) {
                    console.log(`Cooldown active, ${Math.round((COOLDOWN_MS - (now - lastScanTime)) / 1000)}s remaining`);
                    requestAnimationFrame(scan);
                    return;
                }

                try {
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                    const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                    console.log('Processing frame, dimensions: ', canvas.width, 'x', canvas.height);
                    const code = jsQR(imageData.data, imageData.width, imageData.height, {
                        inversionAttempts: 'dontInvert'
                    });

                    const timestamp = new Date().toLocaleString('en-US', { timeZone: 'Asia/Kathmandu' });

                    if (code) {
                        console.log('QR code detected: ', code.data);
                        let url, encryptedData;
                        try {
                            url = new URL(code.data);
                            encryptedData = url.searchParams.get('data') || 'No data parameter';
                        } catch (err) {
                            console.warn('URL parsing failed, displaying raw data: ', err.message);
                            url = { href: code.data };
                            encryptedData = 'Not a valid URL';
                        }

                        // Play beep sound
                        beep.play().catch(err => console.warn('Beep sound failed: ', err.message));
                        lastScanTime = now;

                        // Send to backend for validation
                        const response = await fetch(`/admin/verify-ticket?data=${encodeURIComponent(encryptedData)}`, {
                            headers: { 'Accept': 'application/json' }
                        });
                        console.log(response);
                        const responseData = await response.json();
                        console.log('Response data:', responseData);
                        // Update UI with scan and validation result
                        const scanResult = `
                                                                                                                                                                                                                                                                                                                                                                            <div class="scan-entry">
                                                                                                                                                                                                                                                                                                                                                                                <h4>Scanned at ${timestamp}</h4>
                                                                                                                                                                                                                                                                                                                                                                                <table>
                                                                                                                                                                                                                                                                                                                                                                                    <tr><th>Field</th><th>Value</th></tr>
                                                                                                                                                                                                                                                                                                                                                                                    <tr><td>Validation</td><td class="${responseData.status}">${responseData.message}</td></tr>
                                                                                                                                                                                                                                                                                                                                                                                </table>
                                                                                                                                                                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                                                                                                                                                                        `;
                        console.log('Updating resultDiv with: ', scanResult);
                        resultDiv.innerHTML = scanResult + resultDiv.innerHTML;
                        resultDiv.classList.remove('error');
                        console.log('Cooldown started for 6 seconds');
                    }
                } catch (err) {
                    const timestamp = new Date().toLocaleString('en-US', { timeZone: 'Asia/Kathmandu' });
                    const errorMsg = `<p class="error">Error processing QR code: ${err.message} at ${timestamp}</p>`;
                    console.error('Error processing frame: ', err);
                    resultDiv.innerHTML = errorMsg + resultDiv.innerHTML;
                    resultDiv.classList.add('error');
                    lastScanTime = now;
                }

                requestAnimationFrame(scan);
            }

            function stopScanning() {
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                    stream = null;
                }
                video.srcObject = null;
                video.style.display = 'none';
                startScanButton.textContent = 'Scan QR Code';
                scanning = false;
                resultDiv.innerHTML = 'Scan result will appear here.';
                resultDiv.classList.remove('error');
                lastScanTime = 0;
                console.log('Scanning stopped, cooldown reset');
            }
        </script>
    </main>
@endsection