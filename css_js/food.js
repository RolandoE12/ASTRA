/* ==========================================================================
   FOOD & DRINKS / CANTEEN SERVICES MODULE (SELF-CONTAINED & MATCHING HUD)
   ========================================================================== */

let currentFoodPage = 1;
const totalFoodPages = 2;

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

function injectFoodHTML() {
    if (document.getElementById("foodPage")) return;

    const subpageHTML = `
    <div class="hud-subpage-food" id="foodPage" style="display: none;">
        <div class="hud-subpage-inner-food">
            
            <!-- TOP CONTROLS BAR -->
            <div class="hud-top-bar-food">
                <button class="hud-btn-red-food" id="backToOsasFromFood" onclick="closeFoodPanel()">
                    <span class="x-mark">✕</span> BACK TO OSAS
                </button>
                
                <div class="hud-pagination-food">
                    <button class="hud-nav-btn-food" id="prevFoodPage" onclick="changeFoodPage('prev')" disabled>◄ PREV</button>
                    <span class="hud-page-indicator-food" id="foodPageIndicator">PAGE 1 OF 2</span>
                    <button class="hud-nav-btn-food active" id="nextFoodPage" onclick="changeFoodPage('next')">NEXT ►</button>
                </div>
            </div>

            <!-- MAIN CONTENT CARD -->
            <div class="hud-content-card-food">
                <h2 class="hud-card-title-food">SECTION 7: FOOD & CANTEEN SERVICES</h2>
                
                <div class="hud-card-body-food">
                    <!-- PAGE 1 -->
                    <div class="food-page-content" id="foodPage1" style="display: block;">
                        <p class="hud-text-highlight-food">7.1 Campus Canteen & Dining Area Usage:</p>
                        <ul class="hud-list-food">
                            <li><strong>Clean As You Go (CLAYGO):</strong> All students are strictly required to clear their dining tables and dispose of trash properly after eating.</li>
                            <li><strong>Operating Hours:</strong> Canteen services are available during regular school hours; dining outside designated areas is discouraged.</li>
                            <li><strong>Seating Courtesy:</strong> Dining tables are shared resources. Reserving seats during peak rush hours is prohibited.</li>
                        </ul>

                        <p class="hud-text-highlight-food" style="margin-top: 12px;">7.2 Health & Food Safety Standards:</p>
                        <ul class="hud-list-food">
                            <li><strong>Vendor Sanitation:</strong> All food concessionaires comply with strict sanitary permits and regular health inspections.</li>
                            <li><strong>Nutritious Options:</strong> Canteen menus prioritize healthy, affordable, and balanced meals for students.</li>
                        </ul>
                    </div>

                    <!-- PAGE 2 -->
                    <div class="food-page-content" id="foodPage2" style="display: none;">
                        <p class="hud-text-highlight-food">7.3 Prohibited Items & Consumption Policies:</p>
                        <ul class="hud-list-food">
                            <li><strong>Alcohol & Controlled Substances:</strong> Bringing or consuming alcoholic beverages anywhere on campus grounds is grounds for expulsion.</li>
                            <li><strong>Restricted Areas:</strong> Food and colored drinks are strictly forbidden inside laboratories, computer rooms, and libraries.</li>
                        </ul>

                        <p class="hud-text-highlight-food" style="margin-top: 12px;">7.4 Waste Management & Sustainability:</p>
                        <ul class="hud-list-food">
                            <li><strong>Waste Segregation:</strong> Dispose of food wastes, recyclables, and single-use plastics into their designated color-coded bins.</li>
                            <li><strong>Reusable Containers:</strong> Students are encouraged to bring personal tumblers and reusable food containers.</li>
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
function openFoodPanel() {
    injectFoodHTML();
    
    const foodPage = document.getElementById("foodPage");
    if (!foodPage) return;

    // Apply blur and disable clicks on OSAS title, background cards, and blue back button
    getOsasBackgroundElements().forEach(el => {
        el.classList.add("osas-element-disabled");
        if (el.tagName === "BUTTON") {
            el.disabled = true;
        }
    });

    currentFoodPage = 1;
    updateFoodPagination();

    foodPage.style.display = "block";
    foodPage.classList.remove("closing");
    foodPage.classList.add("active");
}

// CLOSE FUNCTION
function closeFoodPanel() {
    const foodPage = document.getElementById("foodPage");

    if (foodPage) {
        foodPage.classList.remove("active");
        foodPage.classList.add("closing");

        setTimeout(() => {
            foodPage.style.display = "none";
            foodPage.classList.remove("closing");
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

function changeFoodPage(direction) {
    if (direction === 'next' && currentFoodPage < totalFoodPages) {
        currentFoodPage++;
    } else if (direction === 'prev' && currentFoodPage > 1) {
        currentFoodPage--;
    }
    updateFoodPagination();
}

function updateFoodPagination() {
    const page1 = document.getElementById("foodPage1");
    const page2 = document.getElementById("foodPage2");
    const prevBtn = document.getElementById("prevFoodPage");
    const nextBtn = document.getElementById("nextFoodPage");
    const indicator = document.getElementById("foodPageIndicator");

    if (page1 && page2) {
        page1.style.display = (currentFoodPage === 1) ? "block" : "none";
        page2.style.display = (currentFoodPage === 2) ? "block" : "none";
    }

    if (prevBtn) {
        if (currentFoodPage === 1) {
            prevBtn.classList.remove("active");
            prevBtn.disabled = true;
        } else {
            prevBtn.classList.add("active");
            prevBtn.disabled = false;
        }
    }

    if (nextBtn) {
        if (currentFoodPage === totalFoodPages) {
            nextBtn.classList.remove("active");
            nextBtn.disabled = true;
        } else {
            nextBtn.classList.add("active");
            nextBtn.disabled = false;
        }
    }

    if (indicator) {
        indicator.textContent = `PAGE ${currentFoodPage} OF ${totalFoodPages}`;
    }
}

// Global Click Delegation (Supports multiple card IDs)
document.addEventListener("click", (e) => {
    const foodCard = e.target.closest("#foodCard, #foodServicesCard, #canteenCard, #foodAndDrinksCard");
    if (foodCard) {
        openFoodPanel();
    }
});

document.addEventListener("DOMContentLoaded", injectFoodHTML);