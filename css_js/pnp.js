/* ==========================================================================
   PNP STATION MODULE (WITH BACKDROP BLUR OVERLAY & BUTTON LOCK)
   ========================================================================== */

function injectPnpHTML() {
    if (document.getElementById("pnpPage")) return;

    const defaultPlaceholder = "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='150' height='150' viewBox='0 0 150 150'><rect width='100%' height='100%' fill='%23020e1e'/><text x='50%' y='50%' dominant-baseline='middle' text-anchor='middle' fill='%2300f0ff' font-family='sans-serif' font-size='20'>PNP</text></svg>";

    const subpageHTML = `
    <!-- DARK BACKDROP BLUR OVERLAY -->
    <div class="hud-overlay-bscs" id="pnpOverlay" style="display: none;" onclick="closePnpPanel()"></div>

    <!-- MAIN PANEL -->
    <div class="hud-subpage" id="pnpPage" style="display: none;">
        <div class="hud-subpage-inner">
            
            <!-- TOP CONTROLS BAR (NO PREV/NEXT BUTTONS) -->
            <div class="hud-top-bar">
                <button class="hud-btn-red" id="backToEmergencyFromPnpBtn" onclick="closePnpPanel()">
                    <span class="x-mark">✕</span> BACK TO EMERGENCY HOTLINE
                </button>
            </div>

            <!-- MAIN CARD WITH FIXED HEIGHT -->
            <div class="hud-content-card">
                <h2 class="hud-card-title">PHILIPPINE NATIONAL POLICE (PNP STATION)</h2>
                
                <div class="hud-card-body">
                    <!-- SINGLE PAGE CONTENT -->
                    <div class="act-page-content" id="pnpPage1">
                        <div class="bscs-details-container">
                            
                            <!-- LEFT COLUMN: PREVIEW IMAGE & CONTACT INFO -->
                            <div class="bscs-image-wrapper">
                                <img id="pnpStationImg" src="assets/images/police_preview.jpg" alt="PNP Station Image" onerror="this.onerror=null; this.src='${defaultPlaceholder}';">
                            </div>

                            <!-- RIGHT COLUMN: DETAILS & PROTOCOLS -->
                            <div class="bscs-text-content">
                                <p class="hud-text-highlight">1.1 Emergency Hotline & Contact Numbers:</p>
                                <ul class="hud-list">
                                    <li><strong>National Emergency:</strong> <a href="tel:117" style="color:#00f0ff; font-weight:bold; text-decoration:none;">117 / 911</a></li>
                                    <li><strong>Local Police Desk:</strong> <a href="tel:0443217654" style="color:#00f0ff; font-weight:bold; text-decoration:none;">(044) 321-7654</a></li>
                                    <li><strong>Mobile Hotline:</strong> <span style="color:#ffffff;">+63 917 123 4567</span></li>
                                </ul>

                                <p class="hud-text-highlight" style="margin-top: 14px;">1.2 Services & Assistance offered:</p>
                                <ul class="hud-list">
                                    <li><strong>Immediate Tactical Response:</strong> On-campus and local area emergency dispatch.</li>
                                    <li><strong>Crime Prevention Patrol:</strong> Security presence in and around the university area.</li>
                                    <li><strong>Blotter & Investigation:</strong> Formal reporting and investigation for law enforcement concerns.</li>
                                </ul>

                                <p class="hud-text-highlight" style="margin-top: 14px;">1.3 Station Location:</p>
                                <p style="margin-top: 0; margin-bottom: 0; color: rgba(255,255,255,0.85);">
                                    San Jose City Police Station, Municipal Hall Compound, San Jose City, Nueva Ecija
                                </p>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    `;

    document.body.insertAdjacentHTML("beforeend", subpageHTML);
}

// Function to collect background containers and buttons to blur/disable
function getPnpBackgroundElements() {
    const elements = [];

    // 1. Get Cards Container
    const cards = document.querySelector(".emergency-cards-grid") || 
                  document.querySelector(".info-cards-grid") || 
                  document.querySelector(".osas-cards");
    if (cards) elements.push(cards);

    // 2. Target buttons acting as Back Buttons (excluding PNP internal exit)
    const allButtons = document.querySelectorAll("button, .btn, div[role='button']");
    allButtons.forEach(btn => {
        if (btn.innerText && btn.innerText.includes("BACK") && btn.id !== "backToEmergencyFromPnpBtn") {
            elements.push(btn);
        }
    });

    // 3. Target Header / Title text
    const headers = document.querySelectorAll("h1, h2, h3, .hud-main-title");
    headers.forEach(h => {
        if (h.innerText && (h.innerText.includes("EMERGENCY") || h.innerText.includes("INFORMATION"))) {
            elements.push(h);
        }
    });

    return elements;
}

// OPEN FUNCTION
function openPnpPanel() {
    let pnpPage = document.getElementById("pnpPage");
    let pnpOverlay = document.getElementById("pnpOverlay");

    if (!pnpPage || !pnpOverlay) {
        injectPnpHTML();
        pnpPage = document.getElementById("pnpPage");
        pnpOverlay = document.getElementById("pnpOverlay");
    }

    // Apply blur and block clicks on background elements
    const bgElements = getPnpBackgroundElements();
    bgElements.forEach(el => {
        el.classList.add("osas-element-disabled");
        if (el.tagName === "BUTTON") {
            el.disabled = true;
        }
    });

    // Display Overlay and Panel
    if (pnpOverlay) pnpOverlay.style.display = "block";
    
    pnpPage.classList.remove("closing");
    pnpPage.classList.add("active");
    pnpPage.style.display = "block";
}

// CLOSE FUNCTION
function closePnpPanel() {
    const pnpPage = document.getElementById("pnpPage");
    const pnpOverlay = document.getElementById("pnpOverlay");

    if (pnpPage) {
        pnpPage.classList.remove("active");
        pnpPage.classList.add("closing");

        setTimeout(() => {
            pnpPage.style.display = "none";
            if (pnpOverlay) pnpOverlay.style.display = "none";
            pnpPage.classList.remove("closing");
        }, 240);
    }

    // Remove blur and re-enable background buttons
    const bgElements = getPnpBackgroundElements();
    bgElements.forEach(el => {
        el.classList.remove("osas-element-disabled");
        if (el.tagName === "BUTTON") {
            el.disabled = false;
        }
    });
}

// INITIALIZE EVENT LISTENERS ON DOM LOAD
document.addEventListener("DOMContentLoaded", () => {
    injectPnpHTML();

    const pnpCards = document.querySelectorAll("#pnpCard, .pnp-card, #pnpCardBtn");
    pnpCards.forEach(card => {
        card.addEventListener("click", openPnpPanel);
    });
});