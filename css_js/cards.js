/*=========================================
FEATURE CARD EVENTS
=========================================*/

const coreMapCard = document.getElementById("coreMapCard");
const informationCard = document.getElementById("informationCard");
const featuresCard = document.getElementById("featuresCard");

/*==============================*/

const coreMapPage = document.getElementById("coreMapPage");
const backCoreMap = document.getElementById("backCoreMap");
const coreMapViewer = document.getElementById("coreMapViewer");
const closeCoreMapViewer = document.getElementById("closeCoreMapViewer");
const coreMapViewerTitle = document.getElementById("coreMapViewerTitle");
const coreMapViewerText = document.getElementById("coreMapViewerText");
const featuresPage = document.getElementById("featuresPage");
const backFeatures = document.getElementById("backFeatures");

coreMapCard.addEventListener("click", function(){

    if (typeof clearSuggestions === "function") clearSuggestions();

    mainElements.forEach(el => {
        if(el) el.style.display = "none";
    });

    informationPage.style.display = "none";
    coreMapPage.style.display = "block";
    document.body.classList.add("info-mode");
    addSystemMessage("Opening CORE MAP...");

});

backCoreMap.addEventListener("click", function(){

    coreMapViewer.style.display = "none";
    coreMapPage.style.display = "none";

    mainElements.forEach(el => {
        if(el) el.style.display = "";
    });

    document.body.classList.remove("info-mode");

    if (typeof loadSuggestions === "function") loadSuggestions("Core Gateway College student services tuition admission courses");

});

document.querySelectorAll(".core-location-card").forEach(card => {

    card.addEventListener("click", function(){

        const title = this.dataset.title || "CORE LOCATION";

        coreMapViewerTitle.textContent = title;

        coreMapViewerText.textContent = this.dataset.fullMap === "true"
            ? "Full campus map of CORE Gateway College Inc."
            : "Map guide for " + title + ".";

        coreMapViewer.style.display = "block";

    });

});

closeCoreMapViewer.addEventListener("click", function(){
    coreMapViewer.style.display = "none";
});


/* =========================================
   EVENTS PAGE DOM & EVENT LISTENERS (NEW)
========================================= */
const eventsCard = document.getElementById("eventsCard");
const eventsPage = document.getElementById("eventsPage");
const backEvents = document.getElementById("backEvents");

if (eventsCard && eventsPage) {
    eventsCard.addEventListener("click", function () {
        informationPage.style.display = "none";
        eventsPage.style.display = "block";
    });
}

if (backEvents && eventsPage) {
    backEvents.addEventListener("click", function () {
        eventsPage.style.display = "none";
        informationPage.style.display = "block";
    });
}

/*==============================*/

informationCard.addEventListener("click", function () {
    openInformation();
});

const coursesCard = document.getElementById("coursesCard");
const coursesPage = document.getElementById("coursesPage");
const backInfo = document.getElementById("backInfo");

const osasCard = document.getElementById("osasCard");
const osasPage = document.getElementById("osasPage");
const backOsas = document.getElementById("backOsas");



coursesCard.addEventListener("click", function () {

    informationPage.style.display = "none";
    coursesPage.style.display = "block";

    });

backInfo.addEventListener("click", function () {

    coursesPage.style.display = "none";
    informationPage.style.display = "block";

});

osasCard.addEventListener("click", function () {

    informationPage.style.display = "none";
    osasPage.style.display = "block";

});

backOsas.addEventListener("click", function () {

    osasPage.style.display = "none";
    informationPage.style.display = "block";

});

const admissionCard = document.getElementById("admissionCard");
const admissionPage = document.getElementById("admissionPage");
const backAdmission = document.getElementById("backAdmission");


admissionCard.addEventListener("click", function(){

    informationPage.style.display = "none";
    admissionPage.style.display = "block";

});

backAdmission.addEventListener("click", function(){

    admissionPage.style.display = "none";
    informationPage.style.display = "block";

});



/*==========================
FRESHMEN PAGE
==========================*/

const freshmenCard = document.getElementById("freshmenCard");

const freshmenPage = document.getElementById("freshmenPage");

const backFreshmen = document.getElementById("backFreshmen");

freshmenCard.addEventListener("click",function(){

    admissionPage.style.display="none";

    freshmenPage.style.display="block";

});

backFreshmen.addEventListener("click",function(){

    freshmenPage.style.display="none";

    admissionPage.style.display="block";

});



/*==========================
 TRANSFEREES PAGE
==========================*/

const transfereesCard = document.getElementById("transfereesCard");
const transfereesPage = document.getElementById("transfereesPage");
const backTransferees = document.getElementById("backTransferees");

transfereesCard.addEventListener("click", function(){

    admissionPage.style.display = "none";
    transfereesPage.style.display = "block";

});

backTransferees.addEventListener("click", function(){

    transfereesPage.style.display = "none";
    admissionPage.style.display = "block";

});

/*==========================
 REQUIREMENTS PAGE
==========================*/

const requirementsCard = document.getElementById("requirementsCard");
const requirementsPage = document.getElementById("requirementsPage");
const backRequirements = document.getElementById("backRequirements");

requirementsCard.addEventListener("click", function(){

    admissionPage.style.display = "none";
    requirementsPage.style.display = "block";

});

backRequirements.addEventListener("click", function(){

    requirementsPage.style.display = "none";
    admissionPage.style.display = "block";

});


/*==========================
 SCHOLARSHIPS PAGE
==========================*/

const scholarshipsCard = document.getElementById("scholarshipsCard");
const scholarshipsPage = document.getElementById("scholarshipsPage");
const backScholarships = document.getElementById("backScholarships");

scholarshipsCard.addEventListener("click", function(){

    admissionPage.style.display = "none";
    scholarshipsPage.style.display = "block";

});

backScholarships.addEventListener("click", function(){

    scholarshipsPage.style.display = "none";
    admissionPage.style.display = "block";

});



/*==============================*/

featuresCard.addEventListener("click",function(){

    if (typeof clearSuggestions === "function") clearSuggestions();

    mainElements.forEach(el => {
        if (el) el.style.display = "none";
    });

    featuresPage.style.display = "block";
    document.body.classList.add("info-mode");
    addSystemMessage("Welcome to ASTRA FEATURES. Explore what I can help you with.");

});

backFeatures.addEventListener("click", function(){

    featuresPage.style.display = "none";

    mainElements.forEach(el => {
        if (el) el.style.display = "";
    });

    document.body.classList.remove("info-mode");

    if (typeof loadSuggestions === "function") loadSuggestions("Core Gateway College student services tuition admission courses");

});

/*==============================
Hover Sound (Optional)
==============================*/

document.querySelectorAll(".feature-card").forEach(card=>{

    card.addEventListener("mouseenter",()=>{

        card.style.transform="translateY(-8px) scale(1.04)";

    });

    card.addEventListener("mouseleave",()=>{

        card.style.transform="translateY(0) scale(1)";

    });

});

/*=========================================
 INFORMATION PAGE SYSTEM
=========================================*/


const informationPage =
document.getElementById("informationPage");


const mainElements = [

document.querySelector(".greeting"),

document.querySelector(".title"),

document.getElementById("clock"),

document.getElementById("date"),

document.querySelector(".top-cards"),

document.querySelector(".chat-window")

];

const inputArea = document.querySelector(".input-area");


function openInformation(){

    if (typeof clearSuggestions === "function") clearSuggestions();

    mainElements.forEach(el=>{

        if(el){

            el.style.display="none";

        }

    });


    informationPage.style.display="block";


    // keep input pinned to the bottom (handled by CSS per breakpoint,
    // see .info-mode .input-area in style.css)
    document.body.classList.add("info-mode");

}


document.getElementById("backMain")
.addEventListener("click",function(){


    informationPage.style.display="none";


    mainElements.forEach(el=>{

        if(el){

            el.style.display="";

        }

    });


    document.body.classList.remove("info-mode");

    if (typeof loadSuggestions === "function") loadSuggestions("Core Gateway College student services tuition admission courses");


});
