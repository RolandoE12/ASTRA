function openEmergencyPanel() {
    // 1. Hide the Information section
    const infoPage = document.getElementById("informationPage");
    if (infoPage) {
        infoPage.style.display = "none";
    }

    // 2. Display the Emergency Hotline section
    const emergencyPage = document.getElementById("emergencyHotlinePage");
    if (emergencyPage) {
        emergencyPage.style.display = "block";
    }
}

function closeEmergencyPanel() {
    // 1. Hide the Emergency Hotline section
    const emergencyPage = document.getElementById("emergencyHotlinePage");
    if (emergencyPage) {
        emergencyPage.style.display = "none";
    }

    // 2. Bring back the Information section
    const infoPage = document.getElementById("informationPage");
    if (infoPage) {
        infoPage.style.display = "block";
    }
}