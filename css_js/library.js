/* ==========================================================================
   LIBRARY MODULE (SELF-CONTAINED & BUG-FIXED)
   ========================================================================== */

let currentLibPage = 1;
const totalLibPages = 2;

// Helper to reliably find OSAS Title, Blue Back Button, and Background Cards
function getOsasBackgroundElements() {
    const elements = [];

    // 1. Get OSAS Cards Container
    const cards = document.querySelector(".osas-cards") || document.querySelector(".cards-container");
    if (cards) elements.push(cards);

    // 2. Find blue main back buttons (excluding red subpanel buttons)
    const allButtons = document.querySelectorAll("button, .btn, div[role='button']");
    allButtons.forEach(btn => {
        if (btn.innerText.includes("BACK") && btn.id !== "backToOsasBtn" && btn.id !== "backToOsasFromLib") {
            elements.push(btn);
        }
    });

    // 3. Find OSAS Title text
    const headers = document.querySelectorAll("h1, h2, h3, .title, .osas-title, header");
    headers.forEach(h => {
        if (h.innerText.includes("OFFICE OF STUDENT AFFAIRS") || h.classList.contains("osas-title")) {
            elements.push(h);
        }
    });

    return elements;
}

function injectLibraryHTML() {
    if (document.getElementById("libraryPage")) return;

    const subpageHTML = `
    <div class="hud-subpage-lib" id="libraryPage" style="display: none;">
        <div class="hud-subpage-inner-lib">
            
            <div class="hud-top-bar-lib">
                <button class="hud-btn-red-lib" id="backToOsasFromLib" onclick="closeLibrary()">
                    <span class="x-mark">✕</span> BACK TO OSAS
                </button>
                
                <div class="hud-pagination-lib">
                    <button class="hud-nav-btn-lib" id="prevLibPage" onclick="changeLibraryPage('prev')" disabled>◄ PREV</button>
                    <span class="hud-page-indicator-lib" id="libPageIndicator">PAGE 1 OF 2</span>
                    <button class="hud-nav-btn-lib active" id="nextLibPage" onclick="changeLibraryPage('next')">NEXT ►</button>
                </div>
            </div>

            <div class="hud-content-card-lib">
                <h2 class="hud-card-title-lib">SECTION 2: LIBRARY SERVICES & GUIDELINES</h2>
                
                <div class="hud-card-body-lib">
                    <div class="lib-page-content" id="libPage1">
                        <p class="hud-text-highlight-lib">2.1 Library Access & Borrowing Regulations:</p>
                        <ul class="hud-list-lib">
                            <li><strong>Validated Student ID Required:</strong> Students must present a valid CGCI ID to enter the library and borrow resources.</li>
                            <li><strong>Borrowing Limit:</strong> Students are allowed to check out up to 3 books at a time for 3 days.</li>
                            <li><strong>Overdue Fines:</strong> Late returns are subject to standard daily library fines per material.</li>
                        </ul>

                        <p class="hud-text-highlight-lib" style="margin-top: 12px;">2.2 Conduct Inside the Learning Resource Center:</p>
                        <ul class="hud-list-lib">
                            <li><strong>Silence & Decorum:</strong> Keep noise levels to a minimum; loud discussions are strictly prohibited.</li>
                            <li><strong>Food & Drinks:</strong> No eating or open drink containers allowed inside the reading area.</li>
                        </ul>
                    </div>

                    <div class="lib-page-content" id="libPage2" style="display: none;">
                        <p class="hud-text-highlight-lib">2.3 Digital & Online Resources:</p>
                        <ul class="hud-list-lib">
                            <li><strong>E-Library Workstations:</strong> Computers are available for academic research and OPAC catalog searching.</li>
                            <li><strong>Internet Usage:</strong> Browsing non-academic content or social media on library terminals is strictly restricted.</li>
                        </ul>

                        <p class="hud-text-highlight-lib" style="margin-top: 12px;">2.4 Clearance & Lost Material Policy:</p>
                        <ul class="hud-list-lib">
                            <li><strong>Lost Books:</strong> Must be reported immediately and replaced with an identical or updated edition.</li>
                            <li><strong>Semester Clearance:</strong> All outstanding loans and fines must be settled for semester clearance approval.</li>
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

function openLibrary() {
    let libPage = document.getElementById("libraryPage");
    if (!libPage) {
        injectLibraryHTML();
        libPage = document.getElementById("libraryPage");
    }

    // Blur OSAS background, title, and lock blue back button
    const osasElements = getOsasBackgroundElements();
    osasElements.forEach(el => {
        el.classList.add("osas-element-disabled");
        if (el.tagName === "BUTTON") {
            el.disabled = true;
        }
    });

    libPage.classList.remove("closing");
    libPage.classList.add("active");
    currentLibPage = 1;
    updateLibraryPagination();
}

function closeLibrary() {
    const libPage = document.getElementById("libraryPage");

    if (libPage) {
        libPage.classList.remove("active");
        libPage.classList.add("closing");

        setTimeout(() => {
            libPage.style.display = "none";
            libPage.classList.remove("closing");
        }, 240);
    }

    // Unblur and unlock OSAS controls
    const osasElements = getOsasBackgroundElements();
    osasElements.forEach(el => {
        el.classList.remove("osas-element-disabled");
        if (el.tagName === "BUTTON") {
            el.disabled = false;
        }
    });
}

function changeLibraryPage(direction) {
    if (direction === 'next' && currentLibPage < totalLibPages) {
        currentLibPage++;
    } else if (direction === 'prev' && currentLibPage > 1) {
        currentLibPage--;
    }
    updateLibraryPagination();
}

function updateLibraryPagination() {
    const page1 = document.getElementById("libPage1");
    const page2 = document.getElementById("libPage2");
    const prevBtn = document.getElementById("prevLibPage");
    const nextBtn = document.getElementById("nextLibPage");
    const indicator = document.getElementById("libPageIndicator");

    if (page1 && page2) {
        page1.style.display = "none";
        page2.style.display = "none";

        if (currentLibPage === 1) {
            page1.style.display = "block";
        } else if (currentLibPage === 2) {
            page2.style.display = "block";
        }

        if (prevBtn) {
            if (currentLibPage === 1) {
                prevBtn.classList.remove("active");
                prevBtn.disabled = true;
            } else {
                prevBtn.classList.add("active");
                prevBtn.disabled = false;
            }
        }

        if (nextBtn) {
            if (currentLibPage === totalLibPages) {
                nextBtn.classList.remove("active");
                nextBtn.disabled = true;
            } else {
                nextBtn.classList.add("active");
                nextBtn.disabled = false;
            }
        }
    }

    if (indicator) {
        indicator.textContent = `PAGE ${currentLibPage} OF ${totalLibPages}`;
    }
}

// Global Click Delegation (Guarantees the library card trigger works)
document.addEventListener("click", (e) => {
    const libCard = e.target.closest("#libraryCard");
    if (libCard) {
        openLibrary();
    }
});

document.addEventListener("DOMContentLoaded", () => {
    injectLibraryHTML();
});