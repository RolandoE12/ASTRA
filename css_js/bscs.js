/* ==========================================================================
   BSCS MODULE (WITH BACKDROP BLUR OVERLAY & BUTTON LOCK)
   ========================================================================== */

let currentBscsPage = 1;
const totalBscsPages = 3;

function injectBscsHTML() {
    if (document.getElementById("bscsPage")) return;

    const defaultPlaceholder = "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='150' height='150' viewBox='0 0 150 150'><rect width='100%' height='100%' fill='%23020e1e'/><text x='50%' y='50%' dominant-baseline='middle' text-anchor='middle' fill='%2300f0ff' font-family='sans-serif' font-size='20'>BSCS</text></svg>";

    const subpageHTML = `
    <!-- DARK BACKDROP BLUR OVERLAY -->
    <div class="hud-overlay-bscs" id="bscsOverlay" style="display: none;" onclick="closeBscsPanel()"></div>

    <!-- MAIN PANEL -->
    <div class="hud-subpage" id="bscsPage" style="display: none;">
        <div class="hud-subpage-inner">
            
            <!-- TOP CONTROLS BAR -->
            <div class="hud-top-bar">
                <button class="hud-btn-red" id="backToCoursesFromBscsBtn" onclick="closeBscsPanel()">
                    <span class="x-mark">✕</span> BACK TO COURSES
                </button>
                
                <div class="hud-pagination">
                    <button class="hud-nav-btn" id="prevBscsPage" onclick="changeBscsPage('prev')" disabled>◄ PREV</button>
                    <span class="hud-page-indicator" id="bscsPageIndicator">PAGE 1 OF 3</span>
                    <button class="hud-nav-btn active" id="nextBscsPage" onclick="changeBscsPage('next')">NEXT ►</button>
                </div>
            </div>

            <!-- MAIN CARD WITH FIXED HEIGHT -->
            <div class="hud-content-card">
                <h2 class="hud-card-title">BACHELOR OF SCIENCE IN COMPUTER SCIENCE (BSCS)</h2>
                
                <div class="hud-card-body">
                    <!-- PAGE 1 -->
                    <div class="act-page-content" id="bscsPage1">
                        <div class="bscs-details-container">
                            <div class="bscs-image-wrapper">
                                <img id="bscsCourseImg" src="assets/bscs.jpg" onerror="this.onerror=null; this.src='${defaultPlaceholder}';">
                            </div>
                            <div class="bscs-text-content">
                                <p class="hud-text-highlight">1.1 Program Overview:</p>
                                <p style="margin-top: 0; margin-bottom: 12px; color: rgba(255,255,255,0.85);">
                                    The <strong>Bachelor of Science in Computer Science (BSCS)</strong> program focuses on computing concepts, algorithmic problem-solving, and software engineering to develop efficient web, mobile, and desktop applications.
                                </p>
                                <p class="hud-text-highlight">1.2 Core Focus Areas:</p>
                                <ul class="hud-list">
                                    <li><strong>Software Engineering:</strong> Full-stack web & mobile application development.</li>
                                    <li><strong>Algorithms & Data Structures:</strong> Logic formulation & system optimization.</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- PAGE 2 -->
                    <div class="act-page-content" id="bscsPage2" style="display: none;">
                        <p class="hud-text-highlight">2.1 Curriculum & Technical Specializations:</p>
                        <ul class="hud-list">
                            <li><strong>Database Management Systems:</strong> Relational database (MySQL) management, schema design, and query optimization.</li>
                            <li><strong>Computer Networking:</strong> Protocol analysis, router configuration, and network topology simulation.</li>
                            <li><strong>Object-Oriented Programming:</strong> Modular code architecture, design patterns, and application security.</li>
                            <li><strong>Networking & Hardware Integration:</strong> Microcontroller interfacing, OS principles, and network setups.</li>
                        </ul>
                    </div>

                    <!-- PAGE 3 -->
                    <div class="act-page-content" id="bscsPage3" style="display: none;">
                        <p class="hud-text-highlight">3.1 Graduate Career Outcomes:</p>
                        <ul class="hud-list">
                            <li><strong>Full-Stack Web Developer:</strong> Designing dynamic web systems and back-end API services.</li>
                            <li><strong>Software Engineer / Systems Analyst:</strong> Architecting enterprise software solutions.</li>
                            <li><strong>Database Administrator (DBA):</strong> Managing relational database infrastructure and data safety.</li>
                            <li><strong>Network / Systems Administrator:</strong> Overseeing infrastructure, hardware setups, and local networks.</li>
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
function getCoursesBackgroundElements() {
    const elements = [];

    // 1. Get Cards Container
    const cards = document.querySelector(".courses-container") || 
                  document.querySelector(".cards-container") || 
                  document.querySelector(".osas-cards");
    if (cards) elements.push(cards);

    // 2. Target buttons acting as Back Buttons (excluding BSCS internal exit)
    const allButtons = document.querySelectorAll("button, .btn, div[role='button']");
    allButtons.forEach(btn => {
        if (btn.innerText && btn.innerText.includes("BACK") && btn.id !== "backToCoursesFromBscsBtn") {
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
function openBscsPanel() {
    let bscsPage = document.getElementById("bscsPage");
    let bscsOverlay = document.getElementById("bscsOverlay");

    if (!bscsPage || !bscsOverlay) {
        injectBscsHTML();
        bscsPage = document.getElementById("bscsPage");
        bscsOverlay = document.getElementById("bscsOverlay");
    }

    // Apply blur and block clicks on background elements
    const bgElements = getCoursesBackgroundElements();
    bgElements.forEach(el => {
        el.classList.add("osas-element-disabled");
        if (el.tagName === "BUTTON") {
            el.disabled = true;
        }
    });

    // Display Overlay and Panel
    if (bscsOverlay) bscsOverlay.style.display = "block";
    
    bscsPage.classList.remove("closing");
    bscsPage.classList.add("active");
    bscsPage.style.display = "block";
    
    currentBscsPage = 1;
    updateBscsPagination();
}

// CLOSE FUNCTION
function closeBscsPanel() {
    const bscsPage = document.getElementById("bscsPage");
    const bscsOverlay = document.getElementById("bscsOverlay");

    if (bscsPage) {
        bscsPage.classList.remove("active");
        bscsPage.classList.add("closing");

        setTimeout(() => {
            bscsPage.style.display = "none";
            if (bscsOverlay) bscsOverlay.style.display = "none";
            bscsPage.classList.remove("closing");
        }, 240);
    }

    // Remove blur and re-enable background buttons
    const bgElements = getCoursesBackgroundElements();
    bgElements.forEach(el => {
        el.classList.remove("osas-element-disabled");
        if (el.tagName === "BUTTON") {
            el.disabled = false;
        }
    });
}

// PAGINATION CHANGE FUNCTION
function changeBscsPage(direction) {
    if (direction === 'next' && currentBscsPage < totalBscsPages) {
        currentBscsPage++;
    } else if (direction === 'prev' && currentBscsPage > 1) {
        currentBscsPage--;
    }
    updateBscsPagination();
}

// UPDATE PAGINATION UI & BUTTON STATES
function updateBscsPagination() {
    const page1 = document.getElementById("bscsPage1");
    const page2 = document.getElementById("bscsPage2");
    const page3 = document.getElementById("bscsPage3");
    const prevBtn = document.getElementById("prevBscsPage");
    const nextBtn = document.getElementById("nextBscsPage");
    const indicator = document.getElementById("bscsPageIndicator");

    if (page1 && page2 && page3) {
        page1.style.display = "none";
        page2.style.display = "none";
        page3.style.display = "none";

        if (currentBscsPage === 1) {
            page1.style.display = "block";
        } else if (currentBscsPage === 2) {
            page2.style.display = "block";
        } else if (currentBscsPage === 3) {
            page3.style.display = "block";
        }

        if (prevBtn) {
            if (currentBscsPage === 1) {
                prevBtn.classList.remove("active");
                prevBtn.disabled = true;
            } else {
                prevBtn.classList.add("active");
                prevBtn.disabled = false;
            }
        }

        if (nextBtn) {
            if (currentBscsPage === totalBscsPages) {
                nextBtn.classList.remove("active");
                nextBtn.disabled = true;
            } else {
                nextBtn.classList.add("active");
                nextBtn.disabled = false;
            }
        }
    }

    if (indicator) {
        indicator.textContent = `PAGE ${currentBscsPage} OF ${totalBscsPages}`;
    }
}

// INITIALIZE EVENT LISTENERS ON DOM LOAD
document.addEventListener("DOMContentLoaded", () => {
    injectBscsHTML();

    const bscsCards = document.querySelectorAll("#bscsCard, .bscs-card");
    bscsCards.forEach(card => {
        card.addEventListener("click", openBscsPanel);
    });
});