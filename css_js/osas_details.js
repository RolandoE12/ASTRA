/* =========================================================
   OSAS UNIVERSAL DETAIL INFORMATION PANEL
   Every OSAS card opens the SAME Courses-style HUD panel.
   PREV/NEXT changes only the content pages of the selected card.
   ========================================================= */

(function(){

    const DATA = {
        studentActivitiesCard: {
            title: "SECTION 1: STUDENT ACTIVITIES & ORGANIZATIONS",
            image: "assets/os1.jpeg",
            pages: [
                {
                    html: `<p class="osas-detail-highlight">1.1 Student Organizations:</p>
                    <ul class="osas-detail-list">
                        <li><strong>CSSC:</strong> The College Supreme Student Council represents the student body and coordinates student-related activities.</li>
                        <li><strong>Recognized Organizations:</strong> Student groups may conduct activities under the supervision and approval of the school administration.</li>
                    </ul>
                    <p class="osas-detail-highlight" style="margin-top:12px;">1.2 Organization Requirements:</p>
                    <ul class="osas-detail-list">
                        <li>Submit a <strong>Letter of Intent</strong> addressed to the School President.</li>
                        <li>Submit the organization's <strong>Constitution and By-Laws</strong> and required oath of undertaking.</li>
                    </ul>`
                },
                {
                    html: `<p class="osas-detail-highlight">1.3 Socio-Cultural & Athletics:</p>
                    <ul class="osas-detail-list">
                        <li>Students and recognized organizations may conduct acquaintance parties, intramurals, contests, seminars and similar activities.</li>
                        <li>Activity proposals should be submitted for administrative approval before the scheduled activity.</li>
                    </ul>
                    <p class="osas-detail-highlight" style="margin-top:12px;">1.4 Class Attendance:</p>
                    <ul class="osas-detail-list">
                        <li>Participation in activities does not automatically excuse a student from classes.</li>
                        <li>Students should coordinate with concerned instructors and complete missed academic requirements.</li>
                    </ul>`
                },
                {
                    html: `<p class="osas-detail-highlight">1.5 Off-Campus Activities:</p>
                    <ul class="osas-detail-list">
                        <li>Seminars, field trips and conferences outside campus must follow applicable school and CHED requirements.</li>
                        <li>Students are expected to follow all approved schedules, safety rules and activity instructions.</li>
                    </ul>`
                }
            ]
        },

        libraryCard: {
            title: "SECTION 2: LIBRARY SERVICES & GUIDELINES",
            image: "assets/os2.jpeg",
            pages: [
                {
                    html: `<p class="osas-detail-highlight">2.1 Library Access & Borrowing:</p>
                    <ul class="osas-detail-list">
                        <li><strong>Student ID:</strong> Present a valid school ID when entering and borrowing library resources.</li>
                        <li><strong>Borrowing:</strong> Books and other materials must be returned within the period specified by the library.</li>
                        <li><strong>Overdue Materials:</strong> Late returns may be subject to applicable library fines.</li>
                    </ul>
                    <p class="osas-detail-highlight" style="margin-top:12px;">2.2 Conduct:</p>
                    <ul class="osas-detail-list">
                        <li>Maintain silence and proper decorum inside the learning resource area.</li>
                        <li>Eating and open drinks are not allowed in the reading area.</li>
                    </ul>`
                },
                {
                    html: `<p class="osas-detail-highlight">2.3 Digital Resources:</p>
                    <ul class="osas-detail-list">
                        <li><strong>E-Library Workstations:</strong> Computers may be used for academic research and catalog searching.</li>
                        <li><strong>Internet Use:</strong> Library terminals should primarily be used for academic purposes.</li>
                    </ul>
                    <p class="osas-detail-highlight" style="margin-top:12px;">2.4 Lost Materials:</p>
                    <ul class="osas-detail-list">
                        <li>Lost materials should be reported immediately to library staff.</li>
                        <li>Outstanding loans and fines should be settled before clearance.</li>
                    </ul>`
                }
            ]
        },

        computerCard: {
            title: "SECTION 3: COMPUTER SERVICES",
            image: "assets/os3.jpeg",
            pages: [
                {
                    html: `<p class="osas-detail-highlight">3.1 Computer Laboratory Access:</p>
                    <ul class="osas-detail-list">
                        <li>Students should use laboratory computers for authorized academic activities.</li>
                        <li>Keep workstations clean and report damaged equipment to the assigned personnel.</li>
                    </ul>
                    <p class="osas-detail-highlight" style="margin-top:12px;">3.2 Responsible Use:</p>
                    <ul class="osas-detail-list">
                        <li>Do not install unauthorized software or change system configurations without permission.</li>
                        <li>Protect school accounts, files and equipment from unauthorized access.</li>
                    </ul>`
                },
                {
                    html: `<p class="osas-detail-highlight">3.3 Laboratory Rules:</p>
                    <ul class="osas-detail-list">
                        <li>Food and drinks should not be brought near computer equipment.</li>
                        <li>Students must log out of accounts and leave the workstation ready for the next user.</li>
                    </ul>
                    <p class="osas-detail-highlight" style="margin-top:12px;">3.4 Equipment Care:</p>
                    <ul class="osas-detail-list">
                        <li>Do not unplug, move or modify laboratory equipment without authorization.</li>
                        <li>Report hardware or network problems to the laboratory personnel.</li>
                    </ul>`
                }
            ]
        },

        guidanceCard: {
            title: "SECTION 4: GUIDANCE & COUNSELING SERVICES",
            image: "assets/os4.jpeg",
            pages: [
                {
                    html: `<p class="osas-detail-highlight">4.1 Guidance Services:</p>
                    <ul class="osas-detail-list">
                        <li><strong>Individual Counseling:</strong> Students may seek assistance regarding academic, personal and adjustment concerns.</li>
                        <li><strong>Academic Guidance:</strong> Guidance services may assist students in planning and addressing academic concerns.</li>
                    </ul>
                    <p class="osas-detail-highlight" style="margin-top:12px;">4.2 Confidentiality:</p>
                    <ul class="osas-detail-list">
                        <li>Student concerns are handled professionally and with appropriate confidentiality, subject to applicable rules and safety requirements.</li>
                    </ul>`
                },
                {
                    html: `<p class="osas-detail-highlight">4.3 Student Support:</p>
                    <ul class="osas-detail-list">
                        <li>Students may be referred to appropriate school personnel or services when additional support is needed.</li>
                        <li>Appointments and office procedures should be followed when requesting guidance services.</li>
                    </ul>`
                }
            ]
        },

        publicationCard: {
            title: "SECTION 5: STUDENT PUBLICATION",
            image: "assets/os5.jpeg",
            pages: [
                {
                    html: `<p class="osas-detail-highlight">5.1 Student Publication:</p>
                    <ul class="osas-detail-list">
                        <li><strong>Open Contributions:</strong> Registered students may submit literary works, artwork and news articles following publication procedures.</li>
                        <li><strong>Originality:</strong> Submitted materials must be original and properly attributed.</li>
                    </ul>
                    <p class="osas-detail-highlight" style="margin-top:12px;">5.2 Editorial Responsibility:</p>
                    <ul class="osas-detail-list">
                        <li>Editors and contributors are expected to follow ethical journalism and school publication standards.</li>
                    </ul>`
                },
                {
                    html: `<p class="osas-detail-highlight">5.3 Print & Digital Releases:</p>
                    <ul class="osas-detail-list">
                        <li>Newsletters, magazines and digital releases should be reviewed before official distribution.</li>
                        <li>Content should avoid libelous, defamatory or otherwise prohibited material.</li>
                    </ul>
                    <p class="osas-detail-highlight" style="margin-top:12px;">5.4 Publication Resources:</p>
                    <ul class="osas-detail-list">
                        <li>Publication funds and equipment should be used for legitimate publication activities.</li>
                        <li>Financial and resource use should follow applicable school procedures.</li>
                    </ul>`
                }
            ]
        },

        healthSafetyCard: {
            title: "SECTION 6: HEALTH & SAFETY",
            image: "assets/os6.jpeg",
            pages: [
                {
                    html: `<p class="osas-detail-highlight">6.1 Campus Health & Safety:</p>
                    <ul class="osas-detail-list">
                        <li>Students should follow campus safety instructions and immediately report accidents, hazards or unsafe conditions.</li>
                        <li>Emergency exits, safety equipment and designated response areas must remain accessible.</li>
                    </ul>
                    <p class="osas-detail-highlight" style="margin-top:12px;">6.2 Personal Safety:</p>
                    <ul class="osas-detail-list">
                        <li>Students should act responsibly and avoid activities that may endanger themselves or others.</li>
                    </ul>`
                },
                {
                    html: `<p class="osas-detail-highlight">6.3 Health Practices:</p>
                    <ul class="osas-detail-list">
                        <li>Observe proper hygiene and sanitation throughout campus.</li>
                        <li>Seek assistance from the appropriate school health personnel when feeling unwell or when an injury occurs.</li>
                    </ul>
                    <p class="osas-detail-highlight" style="margin-top:12px;">6.4 Emergency Response:</p>
                    <ul class="osas-detail-list">
                        <li>Follow instructions from authorized personnel during emergencies and evacuations.</li>
                    </ul>`
                }
            ]
        },

        suppliesCard: {
            title: "SECTION 7: PROPERTY & SUPPLIES GUIDELINES",
            image: "assets/os7.jpeg",
            pages: [
                {
                    html: `<p class="osas-detail-highlight">7.1 Equipment & Facility Borrowing:</p>
                    <ul class="osas-detail-list">
                        <li><strong>Requisition:</strong> Equipment requests should be submitted using the required school procedure before an activity.</li>
                        <li><strong>Inspection:</strong> Borrowed equipment should be checked for existing damage before turnover.</li>
                    </ul>
                    <p class="osas-detail-highlight" style="margin-top:12px;">7.2 Accountability:</p>
                    <ul class="osas-detail-list">
                        <li>Borrowers are responsible for the safe handling and timely return of school property.</li>
                        <li>Lost or damaged property should be reported and addressed according to school policy.</li>
                    </ul>`
                },
                {
                    html: `<p class="osas-detail-highlight">7.3 Uniforms & Merchandise:</p>
                    <ul class="osas-detail-list">
                        <li>Official uniforms, PE gear and school merchandise should be obtained through authorized school channels.</li>
                        <li>Exchange requests should follow the applicable receipt and merchandise policy.</li>
                    </ul>
                    <p class="osas-detail-highlight" style="margin-top:12px;">7.4 Property Preservation:</p>
                    <ul class="osas-detail-list">
                        <li>Do not move or modify desks, AV equipment or laboratory property without authorization.</li>
                        <li>School property must be protected from damage or unauthorized use.</li>
                    </ul>`
                }
            ]
        },

        foodCard: {
            title: "SECTION 8: FOOD & CANTEEN SERVICES",
            image: "assets/os8.jpeg",
            pages: [
                {
                    html: `<p class="osas-detail-highlight">8.1 Canteen & Dining Area:</p>
                    <ul class="osas-detail-list">
                        <li><strong>Clean As You Go:</strong> Clear dining tables and dispose of waste properly after eating.</li>
                        <li><strong>Shared Seating:</strong> Dining tables are shared resources and should be used courteously.</li>
                    </ul>
                    <p class="osas-detail-highlight" style="margin-top:12px;">8.2 Food Safety:</p>
                    <ul class="osas-detail-list">
                        <li>Observe sanitation requirements and keep food preparation and dining areas clean.</li>
                    </ul>`
                },
                {
                    html: `<p class="osas-detail-highlight">8.3 Food Consumption Policies:</p>
                    <ul class="osas-detail-list">
                        <li>Follow school restrictions on food, drinks and prohibited substances on campus.</li>
                        <li>Food and drinks should not be brought into areas where they could damage equipment or violate safety rules.</li>
                    </ul>
                    <p class="osas-detail-highlight" style="margin-top:12px;">8.4 Waste Management:</p>
                    <ul class="osas-detail-list">
                        <li>Segregate food waste, recyclables and other trash into designated containers.</li>
                        <li>Reusable containers and tumblers are encouraged where practical.</li>
                    </ul>`
                }
            ]
        }
    };

    let selectedCard = null;
    let currentPage = 1;

    function getBackground(){
        return [
            document.querySelector("#osasPage > h2"),
            document.querySelector(".osas-cards"),
            document.getElementById("backOsas")
        ].filter(Boolean);
    }

    function inject(){
        if(document.getElementById("osasDetailPanel")) return;

        document.body.insertAdjacentHTML("beforeend", `
            <div class="osas-detail-overlay" id="osasDetailOverlay"></div>
            <div class="osas-detail-panel" id="osasDetailPanel">
                <div class="osas-detail-inner">
                    <div class="osas-detail-topbar">
                        <button class="osas-detail-back" id="osasDetailBack">
                            <span>✕</span> BACK TO OSAS
                        </button>
                        <div class="osas-detail-pagination">
                            <button class="osas-detail-nav" id="osasDetailPrev" disabled>◄ PREV</button>
                            <span class="osas-detail-indicator" id="osasDetailIndicator">PAGE 1 OF 1</span>
                            <button class="osas-detail-nav active" id="osasDetailNext">NEXT ►</button>
                        </div>
                    </div>
                    <div class="osas-detail-card">
                        <h2 class="osas-detail-title" id="osasDetailTitle"></h2>
                        <div class="osas-detail-body" id="osasDetailBody"></div>
                    </div>
                </div>
            </div>
        `);

        document.getElementById("osasDetailBack").addEventListener("click", close);
        document.getElementById("osasDetailOverlay").addEventListener("click", close);
        document.getElementById("osasDetailPrev").addEventListener("click", ()=>change(-1));
        document.getElementById("osasDetailNext").addEventListener("click", ()=>change(1));
    }

    function open(cardId){
        const data = DATA[cardId];
        if(!data) return;

        inject();
        selectedCard = cardId;
        currentPage = 1;

        getBackground().forEach(el=>el.classList.add("osas-detail-background-disabled"));

        const overlay = document.getElementById("osasDetailOverlay");
        const panel = document.getElementById("osasDetailPanel");
        overlay.classList.add("active");
        panel.classList.remove("closing");
        panel.classList.add("active");

        render();
    }

    function close(){
        const panel = document.getElementById("osasDetailPanel");
        const overlay = document.getElementById("osasDetailOverlay");
        if(!panel) return;

        panel.classList.remove("active");
        panel.classList.add("closing");

        setTimeout(()=>{
            panel.classList.remove("closing");
            panel.style.display = "none";
            overlay.classList.remove("active");
        },240);

        getBackground().forEach(el=>el.classList.remove("osas-detail-background-disabled"));
    }

    function change(direction){
        const data = DATA[selectedCard];
        if(!data) return;

        const max = data.pages.length;

        if(direction > 0 && currentPage < max) currentPage++;
        if(direction < 0 && currentPage > 1) currentPage--;

        render();
    }

    function render(){
        const data = DATA[selectedCard];
        if(!data) return;

        document.getElementById("osasDetailTitle").textContent = data.title;
        document.getElementById("osasDetailBody").innerHTML = data.pages[currentPage-1].html;

        const indicator = document.getElementById("osasDetailIndicator");
        const prev = document.getElementById("osasDetailPrev");
        const next = document.getElementById("osasDetailNext");

        indicator.textContent = `PAGE ${currentPage} OF ${data.pages.length}`;

        prev.disabled = currentPage === 1;
        prev.classList.toggle("active", currentPage > 1);

        next.disabled = currentPage === data.pages.length;
        next.classList.toggle("active", currentPage < data.pages.length);
    }

    document.addEventListener("DOMContentLoaded", ()=>{
        Object.keys(DATA).forEach(id=>{
            const card = document.getElementById(id);
            if(card) card.addEventListener("click", ()=>open(id));
        });
    });

})();
