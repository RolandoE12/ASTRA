/* ==========================================================================
   K-12 MODULE (BASIC EDUCATION PROGRAM)
   ========================================================================== */

let currentK12Page = 1;
const totalK12Pages = 3;

function injectK12HTML() {
    if (document.getElementById("k12Page")) return;

    const defaultPlaceholder = "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='150' height='150' viewBox='0 0 150 150'><rect width='100%' height='100%' fill='%23020e1e'/><text x='50%' y='50%' dominant-baseline='middle' text-anchor='middle' fill='%2300f0ff' font-family='sans-serif' font-size='20'>K-12</text></svg>";

    const subpageHTML = `
    <!-- DARK BACKDROP BLUR OVERLAY -->
    <div class="hud-overlay-bscs" id="k12Overlay" style="display: none;" onclick="closeK12Panel()"></div>

    <!-- MAIN PANEL -->
    <div class="hud-subpage" id="k12Page" style="display: none;">
        <div class="hud-subpage-inner">
            
            <!-- TOP CONTROLS BAR -->
            <div class="hud-top-bar">
                <button class="hud-btn-red" id="backToCoursesFromK12Btn" onclick="closeK12Panel()">
                    <span class="x-mark">✕</span> BACK TO COURSES
                </button>
                
                <div class="hud-pagination">
                    <button class="hud-nav-btn" id="prevK12Page" onclick="changeK12Page('prev')" disabled>◄ PREV</button>
                    <span class="hud-page-indicator" id="k12PageIndicator">PAGE 1 OF 3</span>
                    <button class="hud-nav-btn active" id="nextK12Page" onclick="changeK12Page('next')">NEXT ►</button>
                </div>
            </div>

            <!-- MAIN CARD WITH FIXED HEIGHT -->
            <div class="hud-content-card">
                <h2 class="hud-card-title">K TO 12 BASIC EDUCATION PROGRAM</h2>
                
                <div class="hud-card-body">
                    <!-- PAGE 1 -->
                    <div class="act-page-content" id="k12Page1">
                        <div class="bscs-details-container">
                            <div class="bscs-image-wrapper">
                                <img id="k12CourseImg" src="assets/images/k12_preview.jpg" alt="K-12 Program Image" onerror="this.onerror=null; this.src='${defaultPlaceholder}';">
                            </div>
                            <div class="bscs-text-content">
                                <p class="hud-text-highlight">1.1 Program Overview:</p>
                                <p style="margin-top: 0; margin-bottom: 12px; color: rgba(255,255,255,0.85);">
                                    The <strong>K to 12 Program</strong> covers Kindergarten and 12 years of basic education to provide sufficient time for mastery of concepts and skills, develop lifelong learners, and prepare graduates for tertiary education or workforce integration.
                                </p>
                                <p class="hud-text-highlight">1.2 Structure & Phases:</p>
                                <ul class="hud-list">
                                    <li><strong>Primary & Junior High:</strong> Kindergarten to Grade 10 (Core foundational literacy, science, and math).</li>
                                    <li><strong>Senior High School (SHS):</strong> Grades 11 and 12 (Specialized career and academic tracks).</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- PAGE 2 -->
                    <div class="act-page-content" id="k12Page2" style="display: none;">
                        <p class="hud-text-highlight">2.1 Senior High School Academic Tracks:</p>
                        <ul class="hud-list">
                            <li><strong>STEM (Science, Tech, Engineering, Math):</strong> Advanced coursework in calculus, physics, chemistry, and research.</li>
                            <li><strong>ABM (Accountancy, Business, & Management):</strong> Fundamentals of commerce, financial management, and entrepreneurship.</li>
                            <li><strong>HUMSS (Humanities & Social Sciences):</strong> Creative writing, political theory, communication, and social research.</li>
                            <li><strong>GAS (General Academic Strand):</strong> Versatile elective combinations for flexible college pathway choices.</li>
                        </ul>
                    </div>

                    <!-- PAGE 3 -->
                    <div class="act-page-content" id="k12Page3" style="display: none;">
                        <p class="hud-text-highlight">3.1 Technical-Vocational & Exit Pathways:</p>
                        <ul class="hud-list">
                            <li><strong>TVL (Technical-Vocational-Livelihood):</strong> Hands-on training in ICT, industrial arts, and home economics with National Certifications (NC II).</li>
                            <li><strong>Higher Education Readiness:</strong> Seamless transition to college and university degree programs.</li>
                            <li><strong>Employment & Entrepreneurship:</strong> Direct workforce qualification and business start-up readiness upon graduation.</li>
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
function getK12BackgroundElements() {
    const elements = [];

    // 1. Get Cards Container
    const cards = document.querySelector(".courses-container") || 
                  document.querySelector(".cards-container") || 
                  document.querySelector(".osas-cards");
    if (cards) elements.push(cards);

    // 2. Target buttons acting as Back Buttons (excluding K12 internal exit)
    const allButtons = document.querySelectorAll("button, .btn, div[role='button']");
    allButtons.forEach(btn => {
        if (btn.innerText && btn.innerText.includes("BACK") && btn.id !== "backToCoursesFromK12Btn") {
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
function openK12Panel() {
    let k12Page = document.getElementById("k12Page");
    let k12Overlay = document.getElementById("k12Overlay");

    if (!k12Page || !k12Overlay) {
        injectK12HTML();
        k12Page = document.getElementById("k12Page");
        k12Overlay = document.getElementById("k12Overlay");
    }

    // Apply blur and block clicks on background elements
    const bgElements = getK12BackgroundElements();
    bgElements.forEach(el => {
        el.classList.add("osas-element-disabled");
        if (el.tagName === "BUTTON") {
            el.disabled = true;
        }
    });

    // Display Overlay and Panel
    if (k12Overlay) k12Overlay.style.display = "block";
    
    k12Page.classList.remove("closing");
    k12Page.classList.add("active");
    k12Page.style.display = "block";
    
    currentK12Page = 1;
    updateK12Pagination();
}

// CLOSE FUNCTION
function closeK12Panel() {
    const k12Page = document.getElementById("k12Page");
    const k12Overlay = document.getElementById("k12Overlay");

    if (k12Page) {
        k12Page.classList.remove("active");
        k12Page.classList.add("closing");

        setTimeout(() => {
            k12Page.style.display = "none";
            if (k12Overlay) k12Overlay.style.display = "none";
            k12Page.classList.remove("closing");
        }, 240);
    }

    // Remove blur and re-enable background buttons
    const bgElements = getK12BackgroundElements();
    bgElements.forEach(el => {
        el.classList.remove("osas-element-disabled");
        if (el.tagName === "BUTTON") {
            el.disabled = false;
        }
    });
}

// PAGINATION CHANGE FUNCTION
function changeK12Page(direction) {
    if (direction === 'next' && currentK12Page < totalK12Pages) {
        currentK12Page++;
    } else if (direction === 'prev' && currentK12Page > 1) {
        currentK12Page--;
    }
    updateK12Pagination();
}

// UPDATE PAGINATION UI & BUTTON STATES
function updateK12Pagination() {
    const page1 = document.getElementById("k12Page1");
    const page2 = document.getElementById("k12Page2");
    const page3 = document.getElementById("k12Page3");
    const prevBtn = document.getElementById("prevK12Page");
    const nextBtn = document.getElementById("nextK12Page");
    const indicator = document.getElementById("k12PageIndicator");

    if (page1 && page2 && page3) {
        page1.style.display = "none";
        page2.style.display = "none";
        page3.style.display = "none";

        if (currentK12Page === 1) {
            page1.style.display = "block";
        } else if (currentK12Page === 2) {
            page2.style.display = "block";
        } else if (currentK12Page === 3) {
            page3.style.display = "block";
        }

        if (prevBtn) {
            if (currentK12Page === 1) {
                prevBtn.classList.remove("active");
                prevBtn.disabled = true;
            } else {
                prevBtn.classList.add("active");
                prevBtn.disabled = false;
            }
        }

        if (nextBtn) {
            if (currentK12Page === totalK12Pages) {
                nextBtn.classList.remove("active");
                nextBtn.disabled = true;
            } else {
                nextBtn.classList.add("active");
                nextBtn.disabled = false;
            }
        }
    }

    if (indicator) {
        indicator.textContent = `PAGE ${currentK12Page} OF ${totalK12Pages}`;
    }
}

// INITIALIZE EVENT LISTENERS ON DOM LOAD
document.addEventListener("DOMContentLoaded", () => {
    injectK12HTML();

    const k12Cards = document.querySelectorAll("#k12Card, .k12-card");
    k12Cards.forEach(card => {
        card.addEventListener("click", openK12Panel);
    });
});