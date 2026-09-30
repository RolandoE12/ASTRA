/*
  no-zoom.js
  Disables page zoom in/out (and the double-tap "maximize" zoom on mobile)
  everywhere on the site, at every screen size / media query breakpoint.

  The viewport meta tag alone only stops pinch-zoom on some mobile browsers.
  This script also blocks:
    - Ctrl/Cmd + "+" / "-" / "=" / "0"   (keyboard zoom)
    - Ctrl/Cmd + mouse wheel              (desktop trackpad / mouse zoom)
    - Pinch gestures                      (touch devices, Safari gesture events)
    - Double-tap zoom                     (mobile browsers)
*/

(function () {

  // 1) Block Ctrl/Cmd + "+", "-", "=", "0"
  document.addEventListener("keydown", function (e) {
    const isZoomKey = ["+", "-", "=", "0"].includes(e.key);
    if ((e.ctrlKey || e.metaKey) && isZoomKey) {
      e.preventDefault();
    }
  }, { passive: false });

  // 2) Block Ctrl/Cmd + mouse wheel (desktop browser zoom)
  document.addEventListener("wheel", function (e) {
    if (e.ctrlKey || e.metaKey) {
      e.preventDefault();
    }
  }, { passive: false });

  // 3) Block pinch-zoom gestures (Safari-specific gesture events)
  document.addEventListener("gesturestart", function (e) {
    e.preventDefault();
  }, { passive: false });

  document.addEventListener("gesturechange", function (e) {
    e.preventDefault();
  }, { passive: false });

  document.addEventListener("gestureend", function (e) {
    e.preventDefault();
  }, { passive: false });

  // 4) Block multi-touch pinch-zoom on touch devices
  document.addEventListener("touchmove", function (e) {
    if (e.touches.length > 1) {
      e.preventDefault();
    }
  }, { passive: false });

  // 5) Block double-tap-to-zoom on mobile
  let lastTouchEnd = 0;
  document.addEventListener("touchend", function (e) {
    const now = Date.now();
    if (now - lastTouchEnd <= 300) {
      e.preventDefault();
    }
    lastTouchEnd = now;
  }, { passive: false });

})();
