/* ==========================================================================
   BAPS MODULE (BACHELOR OF ARTS IN POLITICAL SCIENCE)
   ========================================================================== */

let currentBapsPage = 1;
const totalBapsPages = 3;

function injectBapsHTML() {
    if (document.getElementById("bapsPage")) return;

    const defaultPlaceholder = "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='150' height='150' viewBox='0 0 150 150'><rect width='100%' height='100%' fill='%23020e1e'/><text x='50%' y='50%' dominant-baseline='middle' text-anchor='middle' fill='%2300f0ff' font-family='sans-serif' font-size='20'>BAPS</text></svg>";

    const subpageHTML = `
    <!-- DARK BACKDROP BLUR OVERLAY -->
    <div class="hud-overlay-bscs" id="bapsOverlay" style="display: none;" onclick="closeBapsPanel()"></div>

    <!-- MAIN PANEL -->
    <div class="hud-subpage" id="bapsPage" style="display: none;">
        <div class="hud-subpage-inner">
            
            <!-- TOP CONTROLS BAR -->
            <div class="hud-top-bar">
                <button class="hud-btn-red" id="backToCoursesFromBapsBtn" onclick="closeBapsPanel()">
                    <span class="x-mark">✕</span> BACK TO COURSES
                </button>
                
                <div class="hud-pagination">
                    <button class="hud-nav-btn" id="prevBapsPage" onclick="changeBapsPage('prev')" disabled>◄ PREV</button>
                    <span class="hud-page-indicator" id="bapsPageIndicator">PAGE 1 OF 3</span>
                    <button class="hud-nav-btn active" id="nextBapsPage" onclick="changeBapsPage('next')">NEXT ►</button>
                </div>
            </div>

            <!-- MAIN CARD WITH FIXED HEIGHT -->
            <div class="hud-content-card">
                <h2 class="hud-card-title">BACHELOR OF ARTS IN POLITICAL SCIENCE (BAPS)</h2>
                
                <div class="hud-card-body">
                    <!-- PAGE 1 -->
                    <div class="act-page-content" id="bapsPage1">
                        <div class="bscs-details-container">
                            <div class="bscs-image-wrapper">
                                <img id="bapsCourseImg" src="assets/images/baps_preview.jpg" alt="BAPS Course Image" onerror="this.onerror=null; this.src='${defaultPlaceholder}';">
                            </div>
                            <div class="bscs-text-content">
                                <p class="hud-text-highlight">1.1 Program Overview:</p>
                                <p style="margin-top: 0; margin-bottom: 12px; color: rgba(255,255,255,0.85);">
                                    The <strong>Bachelor of Arts in Political Science (BAPS)</strong> program provides a comprehensive study of governance systems, political theory, public policy, and international relations.
                                </p>
                                <p class="hud-text-highlight">1.2 Core Focus Areas:</p>
                                <ul class="hud-list">
                                    <li><strong>Governance & Public Administration:</strong> Analysis of political institutions, state power, and policy execution.</li>
                                    <li><strong>Law & Legal Studies:</strong> Foundational knowledge in constitutional law, jurisprudence, and civil liberties.</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- PAGE 2 -->
                    <div class="act-page-content" id="bapsPage2" style="display: none;">
                        <p class="hud-text-highlight">2.1 Political Science Curriculum & Areas of Study:</p>
                        <ul class="hud-list">
                            <li><strong>Comparative Politics & International Relations:</strong> Studying global political dynamics, diplomacy, and foreign affairs.</li>
                            <li><strong>Political Theory & Ideologies:</strong> Classical and modern perspectives on democracy, ethics, and power.</li>
                            <li><strong>Public Policy Analysis:</strong> Methods for evaluating civic policies, legislation, and community development.</li>
                            <li><strong>Research Methodology in Social Sciences:</strong> Quantitative and qualitative analysis of political trends and behavior.</li>
                        </ul>
                    </div>

                    <!-- PAGE 3 -->
                    <div class="act-page-content" id="bapsPage3" style="display: none;">
                        <p class="hud-text-highlight">3.1 Career Opportunities:</p>
                        <ul class="hud-list">
                            <li><strong>Legal Track / Pre-Law Student:</strong> Foundational preparation for Law School (Juris Doctor).</li>
                            <li><strong>Public Policy Analyst:</strong> Assessing policies for government agencies, NGOs, and think tanks.</li>
                            <li><strong>Foreign Service / Diplomatic Officer:</strong> Working in embassy administration, trade, or international bodies.</li>
                            <li><strong>Political Consultant / Researcher:</strong> Strategy development, polling, and campaign management.</li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
    `;

    document.body.insertAdjacentHTML("beforeend", subpageHTML);
}

// Collect background elements to blur and disable during active modal
function getBapsBackgroundElements() {
    const elements = [];

    // 1. Get Cards Container
    const cards = document.querySelector(".courses-container") || 
                  document.querySelector(".cards-container") || 
                  document.querySelector(".osas-cards");
    if (cards) elements.push(cards);

    // 2. Target buttons acting as Back Buttons (excluding BAPS internal exit)
    const allButtons = document.querySelectorAll("button, .btn, div[role='button']");
    allButtons.forEach(btn => {
        if (btn.innerText && btn.innerText.includes("BACK") && btn.id !== "backToCoursesFromBapsBtn") {
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
function openBapsPanel() {
    let bapsPage = document.getElementById("bapsPage");
    let bapsOverlay = document.getElementById("bapsOverlay");

    if (!bapsPage || !bapsOverlay) {
        injectBapsHTML();
        bapsPage = document.getElementById("bapsPage");
        bapsOverlay = document.getElementById("bapsOverlay");
    }

    // Apply blur and block clicks on background elements
    const bgElements = getBapsBackgroundElements();
    bgElements.forEach(el => {
        el.classList.add("osas-element-disabled");
        if (el.tagName === "BUTTON") {
            el.disabled = true;
        }
    });

    // Display Overlay and Panel
    if (bapsOverlay) bapsOverlay.style.display = "block";
    
    bapsPage.classList.remove("closing");
    bapsPage.classList.add("active");
    bapsPage.style.display = "block";
    
    currentBapsPage = 1;
    updateBapsPagination();
}

// CLOSE FUNCTION
function closeBapsPanel() {
    const bapsPage = document.getElementById("bapsPage");
    const bapsOverlay = document.getElementById("bapsOverlay");

    if (bapsPage) {
        bapsPage.classList.remove("active");
        bapsPage.classList.add("closing");

        setTimeout(() => {
            bapsPage.style.display = "none";
            if (bapsOverlay) bapsOverlay.style.display = "none";
            bapsPage.classList.remove("closing");
        }, 240);
    }

    // Remove blur and re-enable background buttons
    const bgElements = getBapsBackgroundElements();
    bgElements.forEach(el => {
        el.classList.remove("osas-element-disabled");
        if (el.tagName === "BUTTON") {
            el.disabled = false;
        }
    });
}

// PAGINATION CHANGE FUNCTION
function changeBapsPage(direction) {
    if (direction === 'next' && currentBapsPage < totalBapsPages) {
        currentBapsPage++;
    } else if (direction === 'prev' && currentBapsPage > 1) {
        currentBapsPage--;
    }
    updateBapsPagination();
}

// UPDATE PAGINATION UI & BUTTON STATES
function updateBapsPagination() {
    const page1 = document.getElementById("bapsPage1");
    const page2 = document.getElementById("bapsPage2");
    const page3 = document.getElementById("bapsPage3");
    const prevBtn = document.getElementById("prevBapsPage");
    const nextBtn = document.getElementById("nextBapsPage");
    const indicator = document.getElementById("bapsPageIndicator");

    if (page1 && page2 && page3) {
        page1.style.display = "none";
        page2.style.display = "none";
        page3.style.display = "none";

        if (currentBapsPage === 1) {
            page1.style.display = "block";
        } else if (currentBapsPage === 2) {
            page2.style.display = "block";
        } else if (currentBapsPage === 3) {
            page3.style.display = "block";
        }

        if (prevBtn) {
            if (currentBapsPage === 1) {
                prevBtn.classList.remove("active");
                prevBtn.disabled = true;
            } else {
                prevBtn.classList.add("active");
                prevBtn.disabled = false;
            }
        }

        if (nextBtn) {
            if (currentBapsPage === totalBapsPages) {
                nextBtn.classList.remove("active");
                nextBtn.disabled = true;
            } else {
                nextBtn.classList.add("active");
                nextBtn.disabled = false;
            }
        }
    }

    if (indicator) {
        indicator.textContent = `PAGE ${currentBapsPage} OF ${totalBapsPages}`;
    }
}

// INITIALIZE EVENT LISTENERS ON DOM LOAD
document.addEventListener("DOMContentLoaded", () => {
    injectBapsHTML();

    const bapsCards = document.querySelectorAll("#bapsCard, .baps-card");
    bapsCards.forEach(card => {
        card.addEventListener("click", openBapsPanel);
    });
});