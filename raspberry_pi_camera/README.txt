ASTRA Raspberry Pi 4 USB CAMERA
===============================

This folder provides a simple LAN MJPEG camera server for a USB webcam connected to a Raspberry Pi 4.

1. Connect the USB webcam to the Pi.
2. Install Python/OpenCV:
   sudo apt update
   sudo apt install -y python3-opencv
3. Copy camera_server.py to the Pi.
4. Run:
   python3 camera_server.py
5. Find the Pi IP address:
   hostname -I
6. Test from another device on the same LAN:
   http://PI_IP:8080/stream
7. In ASTRA, edit database/camera_config.php and set stream_url to:
   http://PI_IP:8080/stream
   and snapshot_url to:
   http://PI_IP:8080/snapshot

IMPORTANT
---------
- Keep port 8080 on the trusted LAN only; do not port-forward it to the public internet.
- The ASTRA page is admin-session protected, but the Pi stream itself is a separate LAN service.
- If ASTRA is served over HTTPS, a browser may block an HTTP camera stream as mixed content. In that case use HTTPS/reverse-proxy the Pi stream, or serve the ASTRA admin page over HTTP on the trusted LAN.
- This server is intentionally simple and does not provide user authentication. Network isolation/firewalling is recommended.

OPTIONAL AUTOSTART
------------------
Create /etc/systemd/system/astra-camera.service:

[Unit]
After=network-online.target
Wants=network-online.target

[Service]
User=pi
WorkingDirectory=/home/pi/astra-camera
ExecStart=/usr/bin/python3 /home/pi/astra-camera/camera_server.py
Restart=always
RestartSec=3

[Install]
WantedBy=multi-user.target

Then:
 sudo systemctl daemon-reload
 sudo systemctl enable --now astra-camera
