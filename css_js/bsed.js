/* ==========================================================================
   BSED MODULE (WITH BACKDROP BLUR OVERLAY & BUTTON LOCK)
   ========================================================================== */

let currentBsedPage = 1;
const totalBsedPages = 3;

function injectBsedHTML() {
    if (document.getElementById("bsedPage")) return;

    const defaultPlaceholder = "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='150' height='150' viewBox='0 0 150 150'><rect width='100%' height='100%' fill='%23020e1e'/><text x='50%' y='50%' dominant-baseline='middle' text-anchor='middle' fill='%2300f0ff' font-family='sans-serif' font-size='20'>BSED</text></svg>";

    const subpageHTML = `
    <!-- DARK BACKDROP BLUR OVERLAY -->
    <div class="hud-overlay-bscs" id="bsedOverlay" style="display: none;" onclick="closeBsedPanel()"></div>

    <!-- MAIN PANEL -->
    <div class="hud-subpage" id="bsedPage" style="display: none;">
        <div class="hud-subpage-inner">
            
            <!-- TOP CONTROLS BAR -->
            <div class="hud-top-bar">
                <button class="hud-btn-red" id="backToCoursesFromBsedBtn" onclick="closeBsedPanel()">
                    <span class="x-mark">✕</span> BACK TO COURSES
                </button>
                
                <div class="hud-pagination">
                    <button class="hud-nav-btn" id="prevBsedPage" onclick="changeBsedPage('prev')" disabled>◄ PREV</button>
                    <span class="hud-page-indicator" id="bsedPageIndicator">PAGE 1 OF 3</span>
                    <button class="hud-nav-btn active" id="nextBsedPage" onclick="changeBsedPage('next')">NEXT ►</button>
                </div>
            </div>

            <!-- MAIN CARD WITH FIXED HEIGHT -->
            <div class="hud-content-card">
                <h2 class="hud-card-title">BACHELOR OF SECONDARY EDUCATION (BSED)</h2>
                
                <div class="hud-card-body">
                    <!-- PAGE 1 -->
                    <div class="act-page-content" id="bsedPage1">
                        <div class="bscs-details-container">
                            <div class="bscs-image-wrapper">
                                <img id="bsedCourseImg" src="assets/images/bsed_preview.jpg" alt="BSED Course Image" onerror="this.onerror=null; this.src='${defaultPlaceholder}';">
                            </div>
                            <div class="bscs-text-content">
                                <p class="hud-text-highlight">1.1 Program Overview:</p>
                                <p style="margin-top: 0; margin-bottom: 12px; color: rgba(255,255,255,0.85);">
                                    The <strong>Bachelor of Secondary Education (BSED)</strong> program prepares future educators with advanced pedagogical skills, subject-matter expertise, and modern instructional strategies for high school instruction.
                                </p>
                                <p class="hud-text-highlight">1.2 Core Focus Areas:</p>
                                <ul class="hud-list">
                                    <li><strong>Pedagogy & Teaching Methods:</strong> Curriculum design, classroom management, and learning assessment.</li>
                                    <li><strong>Subject Specialization:</strong> Deep-dive focus in major fields like Mathematics, English, or Science.</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- PAGE 2 -->
                    <div class="act-page-content" id="bsedPage2" style="display: none;">
                        <p class="hud-text-highlight">2.1 Curriculum & Educational Foundations:</p>
                        <ul class="hud-list">
                            <li><strong>Educational Psychology:</strong> Understanding learner development, cognitive styles, and motivation.</li>
                            <li><strong>Technology in Teaching:</strong> Integrating digital platforms and multimedia tools into classroom lesson plans.</li>
                            <li><strong>Assessment & Evaluation:</strong> Designing valid testing methodologies, rubrics, and grading metrics.</li>
                            <li><strong>Field Study & Practice Teaching:</strong> Hands-on classroom observation and supervised internship teaching.</li>
                        </ul>
                    </div>

                    <!-- PAGE 3 -->
                    <div class="act-page-content" id="bsedPage3" style="display: none;">
                        <p class="hud-text-highlight">3.1 Graduate Career Pathways:</p>
                        <ul class="hud-list">
                            <li><strong>Licensed Secondary School Teacher:</strong> Educator in public/private high schools (Junior & Senior High).</li>
                            <li><strong>Curriculum Specialist:</strong> Developing educational modules, learning materials, and syllabi.</li>
                            <li><strong>Educational Consultant / Administrator:</strong> Managing academic departments and student guidance.</li>
                            <li><strong>Corporate Trainer / Academic Writer:</strong> Designing instructional workshops and authoring textbooks.</li>
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
function getBsedBackgroundElements() {
    const elements = [];

    // 1. Get Cards Container
    const cards = document.querySelector(".courses-container") || 
                  document.querySelector(".cards-container") || 
                  document.querySelector(".osas-cards");
    if (cards) elements.push(cards);

    // 2. Target buttons acting as Back Buttons (excluding BSED internal exit)
    const allButtons = document.querySelectorAll("button, .btn, div[role='button']");
    allButtons.forEach(btn => {
        if (btn.innerText && btn.innerText.includes("BACK") && btn.id !== "backToCoursesFromBsedBtn") {
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
function openBsedPanel() {
    let bsedPage = document.getElementById("bsedPage");
    let bsedOverlay = document.getElementById("bsedOverlay");

    if (!bsedPage || !bsedOverlay) {
        injectBsedHTML();
        bsedPage = document.getElementById("bsedPage");
        bsedOverlay = document.getElementById("bsedOverlay");
    }

    // Apply blur and block clicks on background elements
    const bgElements = getBsedBackgroundElements();
    bgElements.forEach(el => {
        el.classList.add("osas-element-disabled");
        if (el.tagName === "BUTTON") {
            el.disabled = true;
        }
    });

    // Display Overlay and Panel
    if (bsedOverlay) bsedOverlay.style.display = "block";
    
    bsedPage.classList.remove("closing");
    bsedPage.classList.add("active");
    bsedPage.style.display = "block";
    
    currentBsedPage = 1;
    updateBsedPagination();
}

// CLOSE FUNCTION
function closeBsedPanel() {
    const bsedPage = document.getElementById("bsedPage");
    const bsedOverlay = document.getElementById("bsedOverlay");

    if (bsedPage) {
        bsedPage.classList.remove("active");
        bsedPage.classList.add("closing");

        setTimeout(() => {
            bsedPage.style.display = "none";
            if (bsedOverlay) bsedOverlay.style.display = "none";
            bsedPage.classList.remove("closing");
        }, 240);
    }

    // Remove blur and re-enable background buttons
    const bgElements = getBsedBackgroundElements();
    bgElements.forEach(el => {
        el.classList.remove("osas-element-disabled");
        if (el.tagName === "BUTTON") {
            el.disabled = false;
        }
    });
}

// PAGINATION CHANGE FUNCTION
function changeBsedPage(direction) {
    if (direction === 'next' && currentBsedPage < totalBsedPages) {
        currentBsedPage++;
    } else if (direction === 'prev' && currentBsedPage > 1) {
        currentBsedPage--;
    }
    updateBsedPagination();
}

// UPDATE PAGINATION UI & BUTTON STATES
function updateBsedPagination() {
    const page1 = document.getElementById("bsedPage1");
    const page2 = document.getElementById("bsedPage2");
    const page3 = document.getElementById("bsedPage3");
    const prevBtn = document.getElementById("prevBsedPage");
    const nextBtn = document.getElementById("nextBsedPage");
    const indicator = document.getElementById("bsedPageIndicator");

    if (page1 && page2 && page3) {
        page1.style.display = "none";
        page2.style.display = "none";
        page3.style.display = "none";

        if (currentBsedPage === 1) {
            page1.style.display = "block";
        } else if (currentBsedPage === 2) {
            page2.style.display = "block";
        } else if (currentBsedPage === 3) {
            page3.style.display = "block";
        }

        if (prevBtn) {
            if (currentBsedPage === 1) {
                prevBtn.classList.remove("active");
                prevBtn.disabled = true;
            } else {
                prevBtn.classList.add("active");
                prevBtn.disabled = false;
            }
        }

        if (nextBtn) {
            if (currentBsedPage === totalBsedPages) {
                nextBtn.classList.remove("active");
                nextBtn.disabled = true;
            } else {
                nextBtn.classList.add("active");
                nextBtn.disabled = false;
            }
        }
    }

    if (indicator) {
        indicator.textContent = `PAGE ${currentBsedPage} OF ${totalBsedPages}`;
    }
}

// INITIALIZE EVENT LISTENERS ON DOM LOAD
document.addEventListener("DOMContentLoaded", () => {
    injectBsedHTML();

    const bsedCards = document.querySelectorAll("#bsedCard, .bsed-card");
    bsedCards.forEach(card => {
        card.addEventListener("click", openBsedPanel);
    });
});