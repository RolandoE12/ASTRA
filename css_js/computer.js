/* ==========================================================================
   COMPUTER & E-LIBRARY MODULE (UPDATED & MATCHING OSAS DESIGN)
   ========================================================================== */

let currentCompPage = 1;
const totalCompPages = 2;

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

function injectComputerHTML() {
    if (document.getElementById("computerPage")) return;

    const subpageHTML = `
    <div class="hud-subpage-comp" id="computerPage" style="display: none;">
        <div class="hud-subpage-inner-comp">
            
            <!-- TOP CONTROLS BAR -->
            <div class="hud-top-bar-comp">
                <button class="hud-btn-red-comp" id="backToOsasFromComp" onclick="closeComputerPanel()">
                    <span class="x-mark">✕</span> BACK TO OSAS
                </button>
                
                <div class="hud-pagination-comp">
                    <button class="hud-nav-btn-comp" id="prevCompPage" onclick="changeComputerPage('prev')" disabled>◄ PREV</button>
                    <span class="hud-page-indicator-comp" id="compPageIndicator">PAGE 1 OF 2</span>
                    <button class="hud-nav-btn-comp active" id="nextCompPage" onclick="changeComputerPage('next')">NEXT ►</button>
                </div>
            </div>

            <!-- MAIN CONTENT CARD -->
            <div class="hud-content-card-comp">
                <h2 class="hud-card-title-comp">SECTION 3: COMPUTER & E-LIBRARY GUIDELINES</h2>
                
                <div class="hud-card-body-comp">
                    <!-- PAGE 1 -->
                    <div class="comp-page-content" id="compPage1" style="display: block;">
                        <p class="hud-text-highlight-comp">3.1 Computer Laboratory & E-Library Usage:</p>
                        <ul class="hud-list-comp">
                            <li><strong>Authorized Access:</strong> Computer terminals are strictly reserved for registered students with active accounts.</li>
                            <li><strong>Academic Use Only:</strong> Systems are provided for research, coursework, and educational purposes.</li>
                            <li><strong>Session Duration:</strong> Standard user sessions are limited during peak hours to ensure equal access for all students.</li>
                        </ul>

                        <p class="hud-text-highlight-comp" style="margin-top: 12px;">3.2 Acceptable Use Policy:</p>
                        <ul class="hud-list-comp">
                            <li><strong>Prohibited Content:</strong> Accessing unauthorized sites, gaming, or streaming non-academic media is strictly forbidden.</li>
                            <li><strong>Software Integrity:</strong> Downloading, installing unapproved software, or altering system settings is prohibited.</li>
                        </ul>
                    </div>

                    <!-- PAGE 2 -->
                    <div class="comp-page-content" id="compPage2" style="display: none;">
                        <p class="hud-text-highlight-comp">3.3 Hardware Care & Safety:</p>
                        <ul class="hud-list-comp">
                            <li><strong>Equipment Handling:</strong> Handle all peripherals (keyboards, mice, monitors) with care. Report hardware faults immediately.</li>
                            <li><strong>Food & Drinks:</strong> Liquids and consumables are strictly forbidden near computer workstations.</li>
                        </ul>

                        <p class="hud-text-highlight-comp" style="margin-top: 12px;">3.4 Printing & External Devices:</p>
                        <ul class="hud-list-comp">
                            <li><strong>External Drives:</strong> USB drives must be scanned for viruses prior to connecting to laboratory terminals.</li>
                            <li><strong>Printing Services:</strong> Printing services are subject to resource availability and standard laboratory fees.</li>
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
function openComputerPanel() {
    injectComputerHTML();
    
    const compPage = document.getElementById("computerPage");
    if (!compPage) return;

    // Apply blur and disable clicks on OSAS title, background cards, and blue back button
    getOsasBackgroundElements().forEach(el => {
        el.classList.add("osas-element-disabled");
        if (el.tagName === "BUTTON") {
            el.disabled = true;
        }
    });

    currentCompPage = 1;
    updateComputerPagination();

    compPage.style.display = "block";
    compPage.classList.remove("closing");
    compPage.classList.add("active");
}

// CLOSE FUNCTION
function closeComputerPanel() {
    const compPage = document.getElementById("computerPage");

    if (compPage) {
        compPage.classList.remove("active");
        compPage.classList.add("closing");

        setTimeout(() => {
            compPage.style.display = "none";
            compPage.classList.remove("closing");
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

function changeComputerPage(direction) {
    if (direction === 'next' && currentCompPage < totalCompPages) {
        currentCompPage++;
    } else if (direction === 'prev' && currentCompPage > 1) {
        currentCompPage--;
    }
    updateComputerPagination();
}

function updateComputerPagination() {
    const page1 = document.getElementById("compPage1");
    const page2 = document.getElementById("compPage2");
    const prevBtn = document.getElementById("prevCompPage");
    const nextBtn = document.getElementById("nextCompPage");
    const indicator = document.getElementById("compPageIndicator");

    if (page1 && page2) {
        page1.style.display = (currentCompPage === 1) ? "block" : "none";
        page2.style.display = (currentCompPage === 2) ? "block" : "none";
    }

    if (prevBtn) {
        if (currentCompPage === 1) {
            prevBtn.classList.remove("active");
            prevBtn.disabled = true;
        } else {
            prevBtn.classList.add("active");
            prevBtn.disabled = false;
        }
    }

    if (nextBtn) {
        if (currentCompPage === totalCompPages) {
            nextBtn.classList.remove("active");
            nextBtn.disabled = true;
        } else {
            nextBtn.classList.add("active");
            nextBtn.disabled = false;
        }
    }

    if (indicator) {
        indicator.textContent = `PAGE ${currentCompPage} OF ${totalCompPages}`;
    }
}

// Global Click Delegation (Supports multiple card IDs)
document.addEventListener("click", (e) => {
    const compCard = e.target.closest("#computerCard, #eLibraryCard, #computerLabCard");
    if (compCard) {
        openComputerPanel();
    }
});

document.addEventListener("DOMContentLoaded", injectComputerHTML);  