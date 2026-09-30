/* ==========================================================================
   STUDENT PUBLICATION MODULE (SELF-CONTAINED & MATCHING HUD DESIGN)
   ========================================================================== */

let currentPubPage = 1;
const totalPubPages = 2;

// Helper to find OSAS Title, Blue Back Button, and Background Cards
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

function injectPublicationHTML() {
    if (document.getElementById("publicationPage")) return;

    const subpageHTML = `
    <div class="hud-subpage-pub" id="publicationPage" style="display: none;">
        <div class="hud-subpage-inner-pub">
            
            <!-- TOP CONTROLS BAR -->
            <div class="hud-top-bar-pub">
                <button class="hud-btn-red-pub" id="backToOsasFromPub" onclick="closePublicationPanel()">
                    <span class="x-mark">✕</span> BACK TO OSAS
                </button>
                
                <div class="hud-pagination-pub">
                    <button class="hud-nav-btn-pub" id="prevPubPage" onclick="changePublicationPage('prev')" disabled>◄ PREV</button>
                    <span class="hud-page-indicator-pub" id="pubPageIndicator">PAGE 1 OF 2</span>
                    <button class="hud-nav-btn-pub active" id="nextPubPage" onclick="changePublicationPage('next')">NEXT ►</button>
                </div>
            </div>

            <!-- MAIN CONTENT CARD -->
            <div class="hud-content-card-pub">
                <h2 class="hud-card-title-pub">SECTION 5: STUDENT PUBLICATION GUIDELINES</h2>
                
                <div class="hud-card-body-pub">
                    <!-- PAGE 1 -->
                    <div class="pub-page-content" id="pubPage1" style="display: block;">
                        <p class="hud-text-highlight-pub">5.1 Editorial Autonomy & Press Freedom:</p>
                        <ul class="hud-list-pub">
                            <li><strong>Press Integrity:</strong> The Official Student Publication operates with journalistic independence under Campus Journalism standards.</li>
                            <li><strong>Responsibility:</strong> Writers and editors are expected to uphold truth, accuracy, and ethical reporting standards.</li>
                            <li><strong>Editorial Board Selection:</strong> Board members are chosen through competitive examinations facilitated by qualified advisers.</li>
                        </ul>

                        <p class="hud-text-highlight-pub" style="margin-top: 12px;">5.2 Submissions & Article Contributions:</p>
                        <ul class="hud-list-pub">
                            <li><strong>Open Contributions:</strong> All registered students are eligible to submit literary works, artwork, and news articles.</li>
                            <li><strong>Plagiarism Policy:</strong> Submissions must be original. Plagiarized materials will lead to automatic rejection and disciplinary action.</li>
                        </ul>
                    </div>

                    <!-- PAGE 2 -->
                    <div class="pub-page-content" id="pubPage2" style="display: none;">
                        <p class="hud-text-highlight-pub">5.3 Print & Digital Release Standards:</p>
                        <ul class="hud-list-pub">
                            <li><strong>Publication Formats:</strong> Newsletters, magazines, and digital press releases are reviewed prior to university-wide distribution.</li>
                            <li><strong>Code of Ethics:</strong> Libelous, profane, or defamatory content targeting individuals or groups is strictly prohibited.</li>
                        </ul>

                        <p class="hud-text-highlight-pub" style="margin-top: 12px;">5.4 Publication Funds & Resources:</p>
                        <ul class="hud-list-pub">
                            <li><strong>Fund Utilization:</strong> Student publication fees collected during enrollment are strictly allocated for printing, equipment, and journalism workshops.</li>
                            <li><strong>Transparency:</strong> Financial reporting and auditing are conducted at the end of each academic term.</li>
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
function openPublicationPanel() {
    injectPublicationHTML();
    
    const pubPage = document.getElementById("publicationPage");
    if (!pubPage) return;

    // Apply blur and disable clicks on background elements
    getOsasBackgroundElements().forEach(el => {
        el.classList.add("osas-element-disabled");
        if (el.tagName === "BUTTON") {
            el.disabled = true;
        }
    });

    currentPubPage = 1;
    updatePublicationPagination();

    pubPage.style.display = "block";
    pubPage.classList.remove("closing");
    pubPage.classList.add("active");
}

// CLOSE FUNCTION
function closePublicationPanel() {
    const pubPage = document.getElementById("publicationPage");

    if (pubPage) {
        pubPage.classList.remove("active");
        pubPage.classList.add("closing");

        setTimeout(() => {
            pubPage.style.display = "none";
            pubPage.classList.remove("closing");
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

function changePublicationPage(direction) {
    if (direction === 'next' && currentPubPage < totalPubPages) {
        currentPubPage++;
    } else if (direction === 'prev' && currentPubPage > 1) {
        currentPubPage--;
    }
    updatePublicationPagination();
}

function updatePublicationPagination() {
    const page1 = document.getElementById("pubPage1");
    const page2 = document.getElementById("pubPage2");
    const prevBtn = document.getElementById("prevPubPage");
    const nextBtn = document.getElementById("nextPubPage");
    const indicator = document.getElementById("pubPageIndicator");

    if (page1 && page2) {
        page1.style.display = (currentPubPage === 1) ? "block" : "none";
        page2.style.display = (currentPubPage === 2) ? "block" : "none";
    }

    if (prevBtn) {
        if (currentPubPage === 1) {
            prevBtn.classList.remove("active");
            prevBtn.disabled = true;
        } else {
            prevBtn.classList.add("active");
            prevBtn.disabled = false;
        }
    }

    if (nextBtn) {
        if (currentPubPage === totalPubPages) {
            nextBtn.classList.remove("active");
            nextBtn.disabled = true;
        } else {
            nextBtn.classList.add("active");
            nextBtn.disabled = false;
        }
    }

    if (indicator) {
        indicator.textContent = `PAGE ${currentPubPage} OF ${totalPubPages}`;
    }
}

// Global Click Delegation (Supports multiple card IDs)
document.addEventListener("click", (e) => {
    const pubCard = e.target.closest("#publicationCard, #studentPublicationCard, #journalismCard");
    if (pubCard) {
        openPublicationPanel();
    }
});

document.addEventListener("DOMContentLoaded", injectPublicationHTML);