<?php
// Raspberry Pi camera stream configuration.
// Change this URL to the MJPEG stream exposed by your Raspberry Pi.
// Example: http://192.168.1.50:8080/stream
return [
    'name' => 'ASTRA Robot Camera',
    'stream_url' => 'http://192.168.1.50:8080/stream',
    'snapshot_url' => 'http://192.168.1.50:8080/snapshot',
    'location' => 'Raspberry Pi 4 / Robot',
];
