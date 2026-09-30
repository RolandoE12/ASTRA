#!/usr/bin/env python3
"""Simple LAN MJPEG server for an ASTRA Raspberry Pi 4 USB webcam.
Run with: python3 camera_server.py
Then use http://PI_IP:8080/stream in ASTRA database/camera_config.php.
"""
from http.server import BaseHTTPRequestHandler, ThreadingHTTPServer
import cv2
import time

HOST = "0.0.0.0"
PORT = 8080
CAMERA_INDEX = 0
WIDTH = 1280
HEIGHT = 720
FPS = 20

class Handler(BaseHTTPRequestHandler):
    def do_GET(self):
        if self.path == "/snapshot":
            ok, frame = camera.read()
            if not ok:
                self.send_error(503, "Camera unavailable")
                return
            ok, data = cv2.imencode('.jpg', frame, [int(cv2.IMWRITE_JPEG_QUALITY), 85])
            if not ok:
                self.send_error(500, "JPEG encoding failed")
                return
            payload = data.tobytes()
            self.send_response(200)
            self.send_header('Content-Type', 'image/jpeg')
            self.send_header('Content-Length', str(len(payload)))
            self.send_header('Cache-Control', 'no-store')
            self.end_headers()
            self.wfile.write(payload)
            return

        if self.path not in ("/", "/stream"):
            self.send_error(404)
            return
        if self.path == "/":
            body = b'<html><body style="background:#07131f;color:white;font-family:sans-serif"><h2>ASTRA Raspberry Pi Camera</h2><img src="/stream" style="max-width:100%"></body></html>'
            self.send_response(200); self.send_header('Content-Type','text/html'); self.send_header('Content-Length',str(len(body))); self.end_headers(); self.wfile.write(body); return

        self.send_response(200)
        self.send_header('Age','0')
        self.send_header('Cache-Control','no-cache, private')
        self.send_header('Pragma','no-cache')
        self.send_header('Content-Type','multipart/x-mixed-replace; boundary=frame')
        self.end_headers()
        try:
            while True:
                ok, frame = camera.read()
                if not ok:
                    time.sleep(.1); continue
                ok, data = cv2.imencode('.jpg', frame, [int(cv2.IMWRITE_JPEG_QUALITY), 80])
                if not ok: continue
                payload = data.tobytes()
                self.wfile.write(b'--frame\r\nContent-Type: image/jpeg\r\nContent-Length: ' + str(len(payload)).encode() + b'\r\n\r\n' + payload + b'\r\n')
                self.wfile.flush()
                time.sleep(1/FPS)
        except (BrokenPipeError, ConnectionResetError):
            pass

    def log_message(self, fmt, *args):
        return

camera = cv2.VideoCapture(CAMERA_INDEX)
camera.set(cv2.CAP_PROP_FRAME_WIDTH, WIDTH)
camera.set(cv2.CAP_PROP_FRAME_HEIGHT, HEIGHT)
camera.set(cv2.CAP_PROP_FPS, FPS)
if not camera.isOpened():
    raise SystemExit('USB camera could not be opened. Check the webcam and /dev/video0.')

print(f'ASTRA camera server: http://0.0.0.0:{PORT}/stream')
try:
    ThreadingHTTPServer((HOST, PORT), Handler).serve_forever()
finally:
    camera.release()
