/* ==========================================================================
   GUIDANCE & COUNSELING MODULE (SELF-CONTAINED & MATCHING HUD DESIGN)
   ========================================================================== */

let currentGuidancePage = 1;
const totalGuidancePages = 2;

// Helper to reliably find OSAS Title, Blue Back Button, and Background Cards
function getOsasBackgroundElements() {
    const elements = [];

    // 1. Target OSAS Cards Container
    const cards = document.querySelector(".osas-cards") || document.querySelector(".cards-container");
    if (cards) elements.push(cards);

    // 2. Target Main Blue Back Button (exclude red subpanel exit buttons)
    const allButtons = document.querySelectorAll("button, .btn, div[role='button']");
    allButtons.forEach(btn => {
        if (btn.innerText.includes("BACK") && !btn.id.toLowerCase().includes("osasfrom") && btn.id !== "backToOsasBtn") {
            elements.push(btn);
        }
    });

    // 3. Target OSAS Header / Title Text
    const headers = document.querySelectorAll("h1, h2, h3, .title, .osas-title, header");
    headers.forEach(h => {
        if (h.innerText.includes("OFFICE OF STUDENT AFFAIRS") || h.classList.contains("osas-title")) {
            elements.push(h);
        }
    });

    return elements;
}

function injectGuidanceHTML() {
    if (document.getElementById("guidancePage")) return;

    const subpageHTML = `
    <div class="hud-subpage-guidance" id="guidancePage" style="display: none;">
        <div class="hud-subpage-inner-guidance">
            
            <!-- TOP CONTROLS BAR -->
            <div class="hud-top-bar-guidance">
                <button class="hud-btn-red-guidance" id="backToOsasFromGuidance" onclick="closeGuidancePanel()">
                    <span class="x-mark">✕</span> BACK TO OSAS
                </button>
                
                <div class="hud-pagination-guidance">
                    <button class="hud-nav-btn-guidance" id="prevGuidancePage" onclick="changeGuidancePage('prev')" disabled>◄ PREV</button>
                    <span class="hud-page-indicator-guidance" id="guidancePageIndicator">PAGE 1 OF 2</span>
                    <button class="hud-nav-btn-guidance active" id="nextGuidancePage" onclick="changeGuidancePage('next')">NEXT ►</button>
                </div>
            </div>

            <!-- MAIN CONTENT CARD -->
            <div class="hud-content-card-guidance">
                <h2 class="hud-card-title-guidance">SECTION 4: GUIDANCE & COUNSELING SERVICES</h2>
                
                <div class="hud-card-body-guidance">
                    <!-- PAGE 1 -->
                    <div class="guidance-page-content" id="guidancePage1" style="display: block;">
                        <p class="hud-text-highlight-guidance">4.1 Counseling & Student Support Services:</p>
                        <ul class="hud-list-guidance">
                            <li><strong>Individual & Group Counseling:</strong> Available to assist students with personal, academic, and social concerns.</li>
                            <li><strong>Confidentiality Policy:</strong> All counseling sessions and student records are kept strictly confidential.</li>
                            <li><strong>Counseling Intake:</strong> Walk-ins are accepted, but scheduling appointments via the Guidance Office is encouraged.</li>
                        </ul>

                        <p class="hud-text-highlight-guidance" style="margin-top: 12px;">4.2 Testing & Assessment Services:</p>
                        <ul class="hud-list-guidance">
                            <li><strong>Psychometric Testing:</strong> Administers standardized personality, aptitude, and intelligence assessments.</li>
                            <li><strong>Career Guidance:</strong> Provides evaluation results to help students align academic pursuits with career path goals.</li>
                        </ul>
                    </div>

                    <!-- PAGE 2 -->
                    <div class="guidance-page-content" id="guidancePage2" style="display: none;">
                        <p class="hud-text-highlight-guidance">4.3 Behavioral Clearances & Exit Interviews:</p>
                        <ul class="hud-list-guidance">
                            <li><strong>Exit Interview Requirement:</strong> Graduating or transferring students must complete an exit interview at the Guidance Office.</li>
                            <li><strong>Good Moral Certificate:</strong> Issuance of Good Moral Character certificates is facilitated upon record evaluation.</li>
                        </ul>

                        <p class="hud-text-highlight-guidance" style="margin-top: 12px;">4.4 Peer Facilitator Group & Seminars:</p>
                        <ul class="hud-list-guidance">
                            <li><strong>Peer Facilitator Organization:</strong> Student volunteers trained to offer peer support and community assistance.</li>
                            <li><strong>Mental Health Seminars:</strong> Periodic workshops on stress management, self-care, and wellness.</li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
    `;

    const osasContainer = document.getElementById("osasPage") || document.querySelector(".osas-container") || document.body;
    osasContainer.insertAdjacentHTML("beforeend", subpageHTML);
}

// OPEN FUNCTION
function openGuidancePanel() {
    injectGuidanceHTML();
    
    const guidancePage = document.getElementById("guidancePage");
    if (!guidancePage) return;

    // Apply blur and disable clicks on OSAS title, background cards, and blue back button
    getOsasBackgroundElements().forEach(el => {
        el.classList.add("osas-element-disabled");
        if (el.tagName === "BUTTON") {
            el.disabled = true;
        }
    });

    currentGuidancePage = 1;
    updateGuidancePagination();

    guidancePage.style.display = "block";
    guidancePage.classList.remove("closing");
    guidancePage.classList.add("active");
}

// CLOSE FUNCTION
function closeGuidancePanel() {
    const guidancePage = document.getElementById("guidancePage");

    if (guidancePage) {
        guidancePage.classList.remove("active");
        guidancePage.classList.add("closing");

        setTimeout(() => {
            guidancePage.style.display = "none";
            guidancePage.classList.remove("closing");
        }, 240);
    }

    // Restore background elements and re-enable blue back button
    getOsasBackgroundElements().forEach(el => {
        el.classList.remove("osas-element-disabled");
        if (el.tagName === "BUTTON") {
            el.disabled = false;
        }
    });
}

function changeGuidancePage(direction) {
    if (direction === 'next' && currentGuidancePage < totalGuidancePages) {
        currentGuidancePage++;
    } else if (direction === 'prev' && currentGuidancePage > 1) {
        currentGuidancePage--;
    }
    updateGuidancePagination();
}

function updateGuidancePagination() {
    const page1 = document.getElementById("guidancePage1");
    const page2 = document.getElementById("guidancePage2");
    const prevBtn = document.getElementById("prevGuidancePage");
    const nextBtn = document.getElementById("nextGuidancePage");
    const indicator = document.getElementById("guidancePageIndicator");

    if (page1 && page2) {
        page1.style.display = (currentGuidancePage === 1) ? "block" : "none";
        page2.style.display = (currentGuidancePage === 2) ? "block" : "none";
    }

    if (prevBtn) {
        if (currentGuidancePage === 1) {
            prevBtn.classList.remove("active");
            prevBtn.disabled = true;
        } else {
            prevBtn.classList.add("active");
            prevBtn.disabled = false;
        }
    }

    if (nextBtn) {
        if (currentGuidancePage === totalGuidancePages) {
            nextBtn.classList.remove("active");
            nextBtn.disabled = true;
        } else {
            nextBtn.classList.add("active");
            nextBtn.disabled = false;
        }
    }

    if (indicator) {
        indicator.textContent = `PAGE ${currentGuidancePage} OF ${totalGuidancePages}`;
    }
}

// Global Click Delegation (Supports multiple card IDs)
document.addEventListener("click", (e) => {
    const guidanceCard = e.target.closest("#guidanceCard, #guidanceServicesCard, #counselingCard");
    if (guidanceCard) {
        openGuidancePanel();
    }
});

document.addEventListener("DOMContentLoaded", injectGuidanceHTML);