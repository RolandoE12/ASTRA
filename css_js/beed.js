/* ==========================================================================
   BEED MODULE (WITH BACKDROP BLUR OVERLAY & BUTTON LOCK)
   ========================================================================== */

let currentBeedPage = 1;
const totalBeedPages = 3;

function injectBeedHTML() {
    if (document.getElementById("beedPage")) return;

    const defaultPlaceholder = "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='150' height='150' viewBox='0 0 150 150'><rect width='100%' height='100%' fill='%23020e1e'/><text x='50%' y='50%' dominant-baseline='middle' text-anchor='middle' fill='%2300f0ff' font-family='sans-serif' font-size='20'>BEED</text></svg>";

    const subpageHTML = `
    <!-- DARK BACKDROP BLUR OVERLAY -->
    <div class="hud-overlay-bscs" id="beedOverlay" style="display: none;" onclick="closeBeedPanel()"></div>

    <!-- MAIN PANEL -->
    <div class="hud-subpage" id="beedPage" style="display: none;">
        <div class="hud-subpage-inner">
            
            <!-- TOP CONTROLS BAR -->
            <div class="hud-top-bar">
                <button class="hud-btn-red" id="backToCoursesFromBeedBtn" onclick="closeBeedPanel()">
                    <span class="x-mark">✕</span> BACK TO COURSES
                </button>
                
                <div class="hud-pagination">
                    <button class="hud-nav-btn" id="prevBeedPage" onclick="changeBeedPage('prev')" disabled>◄ PREV</button>
                    <span class="hud-page-indicator" id="beedPageIndicator">PAGE 1 OF 3</span>
                    <button class="hud-nav-btn active" id="nextBeedPage" onclick="changeBeedPage('next')">NEXT ►</button>
                </div>
            </div>

            <!-- MAIN CARD WITH FIXED HEIGHT -->
            <div class="hud-content-card">
                <h2 class="hud-card-title">BACHELOR OF ELEMENTARY EDUCATION (BEED)</h2>
                
                <div class="hud-card-body">
                    <!-- PAGE 1 -->
                    <div class="act-page-content" id="beedPage1">
                        <div class="bscs-details-container">
                            <div class="bscs-image-wrapper">
                                <img id="beedCourseImg" src="assets/images/beed_preview.jpg" alt="BEED Course Image" onerror="this.onerror=null; this.src='${defaultPlaceholder}';">
                            </div>
                            <div class="bscs-text-content">
                                <p class="hud-text-highlight">1.1 Program Overview:</p>
                                <p style="margin-top: 0; margin-bottom: 12px; color: rgba(255,255,255,0.85);">
                                    The <strong>Bachelor of Elementary Education (BEED)</strong> program is designed to equip future teachers with comprehensive instructional strategies, child development expertise, and multi-subject mastery for primary education.
                                </p>
                                <p class="hud-text-highlight">1.2 Core Focus Areas:</p>
                                <ul class="hud-list">
                                    <li><strong>Child Development & Pedagogy:</strong> Teaching foundational literacy, numeracy, and holistic early learning.</li>
                                    <li><strong>Generalist Instruction:</strong> Preparing educators to teach fundamental subjects across grades 1 to 6.</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- PAGE 2 -->
                    <div class="act-page-content" id="beedPage2" style="display: none;">
                        <p class="hud-text-highlight">2.1 Elementary Curriculum & Learning Strategies:</p>
                        <ul class="hud-list">
                            <li><strong>Early Childhood & Child Psychology:</strong> Understanding cognitive, emotional, and social development in young learners.</li>
                            <li><strong>Teaching Elementary Subjects:</strong> Specialized methodologies for Elementary Science, Math, Language Arts, and Social Studies.</li>
                            <li><strong>Instructional Materials Development:</strong> Creating engaging visual aids, learning kits, and interactive classroom tools.</li>
                            <li><strong>Classroom Management & Observation:</strong> Building positive learning environments and early field study practice.</li>
                        </ul>
                    </div>

                    <!-- PAGE 3 -->
                    <div class="act-page-content" id="beedPage3" style="display: none;">
                        <p class="hud-text-highlight">3.1 Career Opportunities:</p>
                        <ul class="hud-list">
                            <li><strong>Licensed Elementary School Teacher:</strong> Educator in public and private elementary schools (Grades 1–6).</li>
                            <li><strong>Early Childhood Educator:</strong> Specialist in kindergarten and early learning development centers.</li>
                            <li><strong>Learning Material Designer:</strong> Author and developer of elementary workbooks and educational content.</li>
                            <li><strong>Private Tutor / Educational Specialist:</strong> Providing customized learning interventions and academic support.</li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
    `;

    document.body.insertAdjacentHTML("beforeend", subpageHTML);
}

// Collect background containers and buttons to blur/disable
function getBeedBackgroundElements() {
    const elements = [];

    // 1. Get Cards Container
    const cards = document.querySelector(".courses-container") || 
                  document.querySelector(".cards-container") || 
                  document.querySelector(".osas-cards");
    if (cards) elements.push(cards);

    // 2. Target buttons acting as Back Buttons (excluding BEED internal exit)
    const allButtons = document.querySelectorAll("button, .btn, div[role='button']");
    allButtons.forEach(btn => {
        if (btn.innerText && btn.innerText.includes("BACK") && btn.id !== "backToCoursesFromBeedBtn") {
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
function openBeedPanel() {
    let beedPage = document.getElementById("beedPage");
    let beedOverlay = document.getElementById("beedOverlay");

    if (!beedPage || !beedOverlay) {
        injectBeedHTML();
        beedPage = document.getElementById("beedPage");
        beedOverlay = document.getElementById("beedOverlay");
    }

    // Apply blur and block clicks on background elements
    const bgElements = getBeedBackgroundElements();
    bgElements.forEach(el => {
        el.classList.add("osas-element-disabled");
        if (el.tagName === "BUTTON") {
            el.disabled = true;
        }
    });

    // Display Overlay and Panel
    if (beedOverlay) beedOverlay.style.display = "block";
    
    beedPage.classList.remove("closing");
    beedPage.classList.add("active");
    beedPage.style.display = "block";
    
    currentBeedPage = 1;
    updateBeedPagination();
}

// CLOSE FUNCTION
function closeBeedPanel() {
    const beedPage = document.getElementById("beedPage");
    const beedOverlay = document.getElementById("beedOverlay");

    if (beedPage) {
        beedPage.classList.remove("active");
        beedPage.classList.add("closing");

        setTimeout(() => {
            beedPage.style.display = "none";
            if (beedOverlay) beedOverlay.style.display = "none";
            beedPage.classList.remove("closing");
        }, 240);
    }

    // Remove blur and re-enable background buttons
    const bgElements = getBeedBackgroundElements();
    bgElements.forEach(el => {
        el.classList.remove("osas-element-disabled");
        if (el.tagName === "BUTTON") {
            el.disabled = false;
        }
    });
}

// PAGINATION CHANGE FUNCTION
function changeBeedPage(direction) {
    if (direction === 'next' && currentBeedPage < totalBeedPages) {
        currentBeedPage++;
    } else if (direction === 'prev' && currentBeedPage > 1) {
        currentBeedPage--;
    }
    updateBeedPagination();
}

// UPDATE PAGINATION UI & BUTTON STATES
function updateBeedPagination() {
    const page1 = document.getElementById("beedPage1");
    const page2 = document.getElementById("beedPage2");
    const page3 = document.getElementById("beedPage3");
    const prevBtn = document.getElementById("prevBeedPage");
    const nextBtn = document.getElementById("nextBeedPage");
    const indicator = document.getElementById("beedPageIndicator");

    if (page1 && page2 && page3) {
        page1.style.display = "none";
        page2.style.display = "none";
        page3.style.display = "none";

        if (currentBeedPage === 1) {
            page1.style.display = "block";
        } else if (currentBeedPage === 2) {
            page2.style.display = "block";
        } else if (currentBeedPage === 3) {
            page3.style.display = "block";
        }

        if (prevBtn) {
            if (currentBeedPage === 1) {
                prevBtn.classList.remove("active");
                prevBtn.disabled = true;
            } else {
                prevBtn.classList.add("active");
                prevBtn.disabled = false;
            }
        }

        if (nextBtn) {
            if (currentBeedPage === totalBeedPages) {
                nextBtn.classList.remove("active");
                nextBtn.disabled = true;
            } else {
                nextBtn.classList.add("active");
                nextBtn.disabled = false;
            }
        }
    }

    if (indicator) {
        indicator.textContent = `PAGE ${currentBeedPage} OF ${totalBeedPages}`;
    }
}

// INITIALIZE EVENT LISTENERS ON DOM LOAD
document.addEventListener("DOMContentLoaded", () => {
    injectBeedHTML();

    const beedCards = document.querySelectorAll("#beedCard, .beed-card");
    beedCards.forEach(card => {
        card.addEventListener("click", openBeedPanel);
    });
});