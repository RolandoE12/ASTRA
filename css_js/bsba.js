/* ==========================================================================
   BSBA MODULE (WITH BACKDROP BLUR OVERLAY & BUTTON LOCK)
   ========================================================================== */

let currentBsbaPage = 1;
const totalBsbaPages = 3;

function injectBsbaHTML() {
    if (document.getElementById("bsbaPage")) return;

    const defaultPlaceholder = "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='150' height='150' viewBox='0 0 150 150'><rect width='100%' height='100%' fill='%23020e1e'/><text x='50%' y='50%' dominant-baseline='middle' text-anchor='middle' fill='%2300f0ff' font-family='sans-serif' font-size='20'>BSBA</text></svg>";

    const subpageHTML = `
    <!-- DARK BACKDROP BLUR OVERLAY -->
    <div class="hud-overlay-bscs" id="bsbaOverlay" style="display: none;" onclick="closeBsbaPanel()"></div>

    <!-- MAIN PANEL -->
    <div class="hud-subpage" id="bsbaPage" style="display: none;">
        <div class="hud-subpage-inner">
            
            <!-- TOP CONTROLS BAR -->
            <div class="hud-top-bar">
                <button class="hud-btn-red" id="backToCoursesFromBsbaBtn" onclick="closeBsbaPanel()">
                    <span class="x-mark">✕</span> BACK TO COURSES
                </button>
                
                <div class="hud-pagination">
                    <button class="hud-nav-btn" id="prevBsbaPage" onclick="changeBsbaPage('prev')" disabled>◄ PREV</button>
                    <span class="hud-page-indicator" id="bsbaPageIndicator">PAGE 1 OF 3</span>
                    <button class="hud-nav-btn active" id="nextBsbaPage" onclick="changeBsbaPage('next')">NEXT ►</button>
                </div>
            </div>

            <!-- MAIN CARD WITH FIXED HEIGHT -->
            <div class="hud-content-card">
                <h2 class="hud-card-title">BACHELOR OF SCIENCE IN BUSINESS ADMINISTRATION (BSBA)</h2>
                
                <div class="hud-card-body">
                    <!-- PAGE 1 -->
                    <div class="act-page-content" id="bsbaPage1">
                        <div class="bscs-details-container">
                            <div class="bscs-image-wrapper">
                                <img id="bsbaCourseImg" src="assets/bsba.jpg" onerror="this.onerror=null; this.src='${defaultPlaceholder}';">
                            </div>
                            <div class="bscs-text-content">
                                <p class="hud-text-highlight">1.1 Program Overview:</p>
                                <p style="margin-top: 0; margin-bottom: 12px; color: rgba(255,255,255,0.85);">
                                    The <strong>Bachelor of Science in Business Administration (BSBA)</strong> program equips students with critical thinking, financial literacy, and managerial expertise necessary to navigate modern corporate environments and business ventures.
                                </p>
                                <p class="hud-text-highlight">1.2 Core Focus Areas:</p>
                                <ul class="hud-list">
                                    <li><strong>Business Management:</strong> Strategic planning, organizational leadership, and operations.</li>
                                    <li><strong>Financial Acumen:</strong> Financial analysis, budgeting, and managerial accounting.</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- PAGE 2 -->
                    <div class="act-page-content" id="bsbaPage2" style="display: none;">
                        <p class="hud-text-highlight">2.1 Curriculum & Specialization Tracks:</p>
                        <ul class="hud-list">
                            <li><strong>Marketing Management:</strong> Consumer behavior, digital marketing strategies, and brand positioning.</li>
                            <li><strong>Human Resource Management:</strong> Talent acquisition, labor relations, and organizational development.</li>
                            <li><strong>Financial Management:</strong> Corporate finance, investment principles, and risk management.</li>
                            <li><strong>Entrepreneurship & Innovation:</strong> Business plan formulation, market research, and startup management.</li>
                        </ul>
                    </div>

                    <!-- PAGE 3 -->
                    <div class="act-page-content" id="bsbaPage3" style="display: none;">
                        <p class="hud-text-highlight">3.1 Graduate Career Opportunities:</p>
                        <ul class="hud-list">
                            <li><strong>Business Development Manager:</strong> Identifying market growth strategies and strategic partnerships.</li>
                            <li><strong>Marketing Strategist / Specialist:</strong> Designing targeted promotional campaigns and brand growth.</li>
                            <li><strong>Financial Analyst:</strong> Evaluating investment risks, budgets, and corporate assets.</li>
                            <li><strong>Operations & HR Officer:</strong> Streamlining administrative workflows and personnel management.</li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
    `;

    document.body.insertAdjacentHTML("beforeend", subpageHTML);
}

// Function to collect background containers and buttons to blur/disable
function getBsbaBackgroundElements() {
    const elements = [];

    // 1. Get Cards Container
    const cards = document.querySelector(".courses-container") || 
                  document.querySelector(".cards-container") || 
                  document.querySelector(".osas-cards");
    if (cards) elements.push(cards);

    // 2. Target buttons acting as Back Buttons (excluding BSBA internal exit)
    const allButtons = document.querySelectorAll("button, .btn, div[role='button']");
    allButtons.forEach(btn => {
        if (btn.innerText && btn.innerText.includes("BACK") && btn.id !== "backToCoursesFromBsbaBtn") {
            elements.push(btn);
        }
    });

    // 3. Target Header / Title text
    const headers = document.querySelectorAll("h1, h2, h3, .title, header");
    headers.forEach(h => {
        if (h.innerText && (h.innerText.includes("COURSES") || h.classList.contains("courses-title"))) {
            elements.push(h);
        }
    });

    return elements;
}

// OPEN FUNCTION
function openBsbaPanel() {
    let bsbaPage = document.getElementById("bsbaPage");
    let bsbaOverlay = document.getElementById("bsbaOverlay");

    if (!bsbaPage || !bsbaOverlay) {
        injectBsbaHTML();
        bsbaPage = document.getElementById("bsbaPage");
        bsbaOverlay = document.getElementById("bsbaOverlay");
    }

    // Apply blur and block clicks on background elements
    const bgElements = getBsbaBackgroundElements();
    bgElements.forEach(el => {
        el.classList.add("osas-element-disabled");
        if (el.tagName === "BUTTON") {
            el.disabled = true;
        }
    });

    // Display Overlay and Panel
    if (bsbaOverlay) bsbaOverlay.style.display = "block";
    
    bsbaPage.classList.remove("closing");
    bsbaPage.classList.add("active");
    bsbaPage.style.display = "block";
    
    currentBsbaPage = 1;
    updateBsbaPagination();
}

// CLOSE FUNCTION
function closeBsbaPanel() {
    const bsbaPage = document.getElementById("bsbaPage");
    const bsbaOverlay = document.getElementById("bsbaOverlay");

    if (bsbaPage) {
        bsbaPage.classList.remove("active");
        bsbaPage.classList.add("closing");

        setTimeout(() => {
            bsbaPage.style.display = "none";
            if (bsbaOverlay) bsbaOverlay.style.display = "none";
            bsbaPage.classList.remove("closing");
        }, 240);
    }

    // Remove blur and re-enable background buttons
    const bgElements = getBsbaBackgroundElements();
    bgElements.forEach(el => {
        el.classList.remove("osas-element-disabled");
        if (el.tagName === "BUTTON") {
            el.disabled = false;
        }
    });
}

// PAGINATION CHANGE FUNCTION
function changeBsbaPage(direction) {
    if (direction === 'next' && currentBsbaPage < totalBsbaPages) {
        currentBsbaPage++;
    } else if (direction === 'prev' && currentBsbaPage > 1) {
        currentBsbaPage--;
    }
    updateBsbaPagination();
}

// UPDATE PAGINATION UI & BUTTON STATES
function updateBsbaPagination() {
    const page1 = document.getElementById("bsbaPage1");
    const page2 = document.getElementById("bsbaPage2");
    const page3 = document.getElementById("bsbaPage3");
    const prevBtn = document.getElementById("prevBsbaPage");
    const nextBtn = document.getElementById("nextBsbaPage");
    const indicator = document.getElementById("bsbaPageIndicator");

    if (page1 && page2 && page3) {
        page1.style.display = "none";
        page2.style.display = "none";
        page3.style.display = "none";

        if (currentBsbaPage === 1) {
            page1.style.display = "block";
        } else if (currentBsbaPage === 2) {
            page2.style.display = "block";
        } else if (currentBsbaPage === 3) {
            page3.style.display = "block";
        }

        if (prevBtn) {
            if (currentBsbaPage === 1) {
                prevBtn.classList.remove("active");
                prevBtn.disabled = true;
            } else {
                prevBtn.classList.add("active");
                prevBtn.disabled = false;
            }
        }

        if (nextBtn) {
            if (currentBsbaPage === totalBsbaPages) {
                nextBtn.classList.remove("active");
                nextBtn.disabled = true;
            } else {
                nextBtn.classList.add("active");
                nextBtn.disabled = false;
            }
        }
    }

    if (indicator) {
        indicator.textContent = `PAGE ${currentBsbaPage} OF ${totalBsbaPages}`;
    }
}

// INITIALIZE EVENT LISTENERS ON DOM LOAD
document.addEventListener("DOMContentLoaded", () => {
    injectBsbaHTML();

    const bsbaCards = document.querySelectorAll("#bsbaCard, .bsba-card");
    bsbaCards.forEach(card => {
        card.addEventListener("click", openBsbaPanel);
    });
});