/* ==========================================================================
   STUDENT ACTIVITIES MODULE (DIRECT DOM BLUR & BUTTON LOCK)
   ========================================================================== */

let currentActPage = 1;
const totalActPages = 3;

function injectStudentActivitiesHTML() {
    if (document.getElementById("studentActivitiesPage")) return;

    const subpageHTML = `
    <div class="hud-subpage" id="studentActivitiesPage" style="display: none;">
        <div class="hud-subpage-inner">
            
            <!-- TOP CONTROLS BAR -->
            <div class="hud-top-bar">
                <button class="hud-btn-red" id="backToOsasBtn" onclick="closeStudentActivities()">
                    <span class="x-mark">✕</span> BACK TO OSAS
                </button>
                
                <div class="hud-pagination">
                    <button class="hud-nav-btn" id="prevActPage" onclick="changeStudentPage('prev')" disabled>◄ PREV</button>
                    <span class="hud-page-indicator" id="actPageIndicator">PAGE 1 OF 3</span>
                    <button class="hud-nav-btn active" id="nextActPage" onclick="changeStudentPage('next')">NEXT ►</button>
                </div>
            </div>

            <!-- MAIN CARD WITH FIXED HEIGHT -->
            <div class="hud-content-card">
                <h2 class="hud-card-title">SECTION 1: STUDENT ACTIVITIES & ORGANIZATIONS</h2>
                
                <div class="hud-card-body">
                    <!-- PAGE 1 -->
                    <div class="act-page-content" id="actPage1">
                        <p class="hud-text-highlight">1.1 College Supreme Student Council (CSSC) & Student Organizations:</p>
                        <ul class="hud-list">
                            <li><strong>CSSC Governing Body:</strong> The CSSC is the highest governing student body, mainly composed of the president and governors elected by students to represent their departments.</li>
                            <li><strong>Faculty Supervision:</strong> The Faculty-in-Charge of Student Affairs and Services, as adviser, is responsible for organizing co-/extra-curricular activities to be participated in by the entire studentry.</li>
                        </ul>

                        <p class="hud-text-highlight" style="margin-top: 12px;">1.2 Student Fraternities, Sororities, and Organizations Requirements:</p>
                        <ul class="hud-list">
                            <li>Students may organize fraternities, sororities, and organizations by submitting a <strong>Letter of Intent</strong> addressed to the School President.</li>
                            <li>Must submit the organization's <strong>Constitution and By-Laws</strong> along with a <strong>Notarized Oath of Undertaking</strong>.</li>
                        </ul>
                    </div>

                    <!-- PAGE 2 -->
                    <div class="act-page-content" id="actPage2" style="display: none;">
                        <p class="hud-text-highlight">1.3 Socio-Cultural and Athletics Events:</p>
                        <ul class="hud-list">
                            <li>Students through the Socio-Cultural and Sports Committee of the school and CSSC or recognized organizations may hold events like acquaintance parties, intramurals, literary/musical contests, etc.</li>
                            <li>Proposals must be approved by CGCI Administration <strong>one month before schedule</strong>.</li>
                        </ul>

                        <p class="hud-text-highlight" style="margin-top: 12px;">1.5 Activities and Class Attendance Policy:</p>
                        <ul class="hud-list">
                            <li><strong>Class Excuse Policy:</strong> Joining campus activities does not excuse a student from attending classes.</li>
                            <li><strong>Informed Instructors:</strong> Students involved in in and out of campus activities should notify concerned instructors and make necessary arrangements so as not to jeopardize their studies.</li>
                        </ul>
                    </div>

                    <!-- PAGE 3 -->
                    <div class="act-page-content" id="actPage3" style="display: none;">
                        <p class="hud-text-highlight">Off-Campus Activities & Off-Campus Compliance:</p>
                        <ul class="hud-list">
                            <li>Out-of-campus activities like seminars, field trips, and conferences must comply with <strong>CMO No. 63 Series of 2017</strong> (Local Off-Campus Activity guidelines by CHED).</li>
                        </ul>

                        <p class="hud-text-highlight" style="margin-top: 12px;">1.5.1 Posting and Announcement Guidelines:</p>
                        <ul class="hud-list">
                            <li><strong>Prior Approval:</strong> Posting on CGCI bulletin boards should have prior approval of the OSAS.</li>
                            <li><strong>Duration Limit:</strong> Duration of posting is no more than one (1) week. It is the responsibility of the organization to remove the posted materials.</li>
                            <li><strong>Disciplinary Action:</strong> Anyone caught removing, despoiling, or mutilating announcements or posted materials will be subjected to disciplinary action.</li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
    `;

    const osasPage = document.getElementById("osasPage") || document.body;
    osasPage.insertAdjacentHTML("beforeend", subpageHTML);
}

// Function to collect OSAS title, main blue back button, and background cards
function getOsasBackgroundElements() {
    const elements = [];

    // 1. Get OSAS Cards Container
    const cards = document.querySelector(".osas-cards") || document.querySelector(".cards-container");
    if (cards) elements.push(cards);

    // 2. Target all buttons in OSAS view that act as Back Buttons
    const allButtons = document.querySelectorAll("button, .btn, div[role='button']");
    allButtons.forEach(btn => {
        // Find the blue button containing "BACK" (excluding the red subpanel back button)
        if (btn.innerText.includes("BACK") && btn.id !== "backToOsasBtn" && btn.id !== "backToOsasFromLib") {
            elements.push(btn);
        }
    });

    // 3. Target Header / Title text
    const headers = document.querySelectorAll("h1, h2, h3, .title, .osas-title, header");
    headers.forEach(h => {
        if (h.innerText.includes("OFFICE OF STUDENT AFFAIRS") || h.classList.contains("osas-title")) {
            elements.push(h);
        }
    });

    return elements;
}

// OPEN FUNCTION
function openStudentActivities() {
    let studentPage = document.getElementById("studentActivitiesPage");
    if (!studentPage) {
        injectStudentActivitiesHTML();
        studentPage = document.getElementById("studentActivitiesPage");
    }

    // Apply blur and block clicks on OSAS title, cards, and blue back button
    const osasElements = getOsasBackgroundElements();
    osasElements.forEach(el => {
        el.classList.add("osas-element-disabled");
        if (el.tagName === "BUTTON") {
            el.disabled = true; // Fully disables click event
        }
    });

    studentPage.classList.remove("closing");
    studentPage.classList.add("active");
    currentActPage = 1;
    updateStudentPagination();
}

// CLOSE FUNCTION
function closeStudentActivities() {
    const studentPage = document.getElementById("studentActivitiesPage");

    if (studentPage) {
        studentPage.classList.remove("active");
        studentPage.classList.add("closing");

        setTimeout(() => {
            studentPage.style.display = "none";
            studentPage.classList.remove("closing");
        }, 240);
    }

    // Remove blur and re-enable blue back button
    const osasElements = getOsasBackgroundElements();
    osasElements.forEach(el => {
        el.classList.remove("osas-element-disabled");
        if (el.tagName === "BUTTON") {
            el.disabled = false;
        }
    });
}

function changeStudentPage(direction) {
    if (direction === 'next' && currentActPage < totalActPages) {
        currentActPage++;
    } else if (direction === 'prev' && currentActPage > 1) {
        currentActPage--;
    }
    updateStudentPagination();
}

function updateStudentPagination() {
    const page1 = document.getElementById("actPage1");
    const page2 = document.getElementById("actPage2");
    const page3 = document.getElementById("actPage3");
    const prevBtn = document.getElementById("prevActPage");
    const nextBtn = document.getElementById("nextActPage");
    const indicator = document.getElementById("actPageIndicator");

    if (page1 && page2 && page3) {
        page1.style.display = "none";
        page2.style.display = "none";
        page3.style.display = "none";

        if (currentActPage === 1) {
            page1.style.display = "block";
        } else if (currentActPage === 2) {
            page2.style.display = "block";
        } else if (currentActPage === 3) {
            page3.style.display = "block";
        }

        if (prevBtn) {
            if (currentActPage === 1) {
                prevBtn.classList.remove("active");
                prevBtn.disabled = true;
            } else {
                prevBtn.classList.add("active");
                prevBtn.disabled = false;
            }
        }

        if (nextBtn) {
            if (currentActPage === totalActPages) {
                nextBtn.classList.remove("active");
                nextBtn.disabled = true;
            } else {
                nextBtn.classList.add("active");
                nextBtn.disabled = false;
            }
        }
    }

    if (indicator) {
        indicator.textContent = `PAGE ${currentActPage} OF ${totalActPages}`;
    }
}

document.addEventListener("DOMContentLoaded", () => {
    injectStudentActivitiesHTML();

    const studentCard = document.getElementById("studentActivitiesCard");
    if (studentCard) {
        studentCard.addEventListener("click", openStudentActivities);
    }
});