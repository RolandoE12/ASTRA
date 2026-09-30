/* ==========================================================================
   PROPERTY & SUPPLIES MODULE (SELF-CONTAINED & MATCHING HUD DESIGN)
   ========================================================================== */

let currentSupPage = 1;
const totalSupPages = 2;

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

function injectSuppliesHTML() {
    if (document.getElementById("suppliesPage")) return;

    const subpageHTML = `
    <div class="hud-subpage-sup" id="suppliesPage" style="display: none;">
        <div class="hud-subpage-inner-sup">
            
            <!-- TOP CONTROLS BAR -->
            <div class="hud-top-bar-sup">
                <button class="hud-btn-red-sup" id="backToOsasFromSup" onclick="closeSuppliesPanel()">
                    <span class="x-mark">✕</span> BACK TO OSAS
                </button>
                
                <div class="hud-pagination-sup">
                    <button class="hud-nav-btn-sup" id="prevSupPage" onclick="changeSuppliesPage('prev')" disabled>◄ PREV</button>
                    <span class="hud-page-indicator-sup" id="supPageIndicator">PAGE 1 OF 2</span>
                    <button class="hud-nav-btn-sup active" id="nextSupPage" onclick="changeSuppliesPage('next')">NEXT ►</button>
                </div>
            </div>

            <!-- MAIN CONTENT CARD -->
            <div class="hud-content-card-sup">
                <h2 class="hud-card-title-sup">SECTION 6: PROPERTY & SUPPLIES GUIDELINES</h2>
                
                <div class="hud-card-body-sup">
                    <!-- PAGE 1 -->
                    <div class="sup-page-content" id="supPage1" style="display: block;">
                        <p class="hud-text-highlight-sup">6.1 Equipment & Facility Borrowing:</p>
                        <ul class="hud-list-sup">
                            <li><strong>Requisition Form:</strong> Property and event equipment requests must be submitted at least three (3) working days prior to the activity.</li>
                            <li><strong>Student Organization Use:</strong> Borrowing must be officially endorsed by the recognized organization adviser.</li>
                            <li><strong>Inspection:</strong> All equipment must be inspected for existing damages prior to turn-over.</li>
                        </ul>

                        <p class="hud-text-highlight-sup" style="margin-top: 12px;">6.2 Proper Care & Accountability:</p>
                        <ul class="hud-list-sup">
                            <li><strong>Borrower Responsibility:</strong> Requisitioning parties are held directly responsible for safe handling and return.</li>
                            <li><strong>Damage & Loss Liability:</strong> Broken, damaged, or lost items must be repaired or replaced by the borrowing party.</li>
                        </ul>
                    </div>

                    <!-- PAGE 2 -->
                    <div class="sup-page-content" id="supPage2" style="display: none;">
                        <p class="hud-text-highlight-sup">6.3 Uniforms & Merchandise Supplies:</p>
                        <ul class="hud-list-sup">
                            <li><strong>Official Merchandise:</strong> School uniforms, PE gear, and official merchandise are distributed strictly through the Supplies Office.</li>
                            <li><strong>Exchange Policy:</strong> Defective or wrong-sized merchandise may be exchanged within five (5) days accompanied by the official receipt.</li>
                        </ul>

                        <p class="hud-text-highlight-sup" style="margin-top: 12px;">6.4 Campus Property Preservation:</p>
                        <ul class="hud-list-sup">
                            <li><strong>Facility Maintenance:</strong> Unauthorized moving of desks, AV equipment, or laboratory property is prohibited.</li>
                            <li><strong>Vandalism Zero Tolerance:</strong> Any destruction of university property will be dealt with severely under student discipline codes.</li>
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
function openSuppliesPanel() {
    injectSuppliesHTML();
    
    const supPage = document.getElementById("suppliesPage");
    if (!supPage) return;

    // Apply blur and disable clicks on OSAS title, background cards, and blue back button
    getOsasBackgroundElements().forEach(el => {
        el.classList.add("osas-element-disabled");
        if (el.tagName === "BUTTON") {
            el.disabled = true;
        }
    });

    currentSupPage = 1;
    updateSuppliesPagination();

    supPage.style.display = "block";
    supPage.classList.remove("closing");
    supPage.classList.add("active");
}

// CLOSE FUNCTION
function closeSuppliesPanel() {
    const supPage = document.getElementById("suppliesPage");

    if (supPage) {
        supPage.classList.remove("active");
        supPage.classList.add("closing");

        setTimeout(() => {
            supPage.style.display = "none";
            supPage.classList.remove("closing");
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

function changeSuppliesPage(direction) {
    if (direction === 'next' && currentSupPage < totalSupPages) {
        currentSupPage++;
    } else if (direction === 'prev' && currentSupPage > 1) {
        currentSupPage--;
    }
    updateSuppliesPagination();
}

function updateSuppliesPagination() {
    const page1 = document.getElementById("supPage1");
    const page2 = document.getElementById("supPage2");
    const prevBtn = document.getElementById("prevSupPage");
    const nextBtn = document.getElementById("nextSupPage");
    const indicator = document.getElementById("supPageIndicator");

    if (page1 && page2) {
        page1.style.display = (currentSupPage === 1) ? "block" : "none";
        page2.style.display = (currentSupPage === 2) ? "block" : "none";
    }

    if (prevBtn) {
        if (currentSupPage === 1) {
            prevBtn.classList.remove("active");
            prevBtn.disabled = true;
        } else {
            prevBtn.classList.add("active");
            prevBtn.disabled = false;
        }
    }

    if (nextBtn) {
        if (currentSupPage === totalSupPages) {
            nextBtn.classList.remove("active");
            nextBtn.disabled = true;
        } else {
            nextBtn.classList.add("active");
            nextBtn.disabled = false;
        }
    }

    if (indicator) {
        indicator.textContent = `PAGE ${currentSupPage} OF ${totalSupPages}`;
    }
}

// Global Click Delegation (Supports multiple card IDs)
document.addEventListener("click", (e) => {
    const supCard = e.target.closest("#suppliesCard, #propertySuppliesCard, #equipmentCard");
    if (supCard) {
        openSuppliesPanel();
    }
});

document.addEventListener("DOMContentLoaded", injectSuppliesHTML);