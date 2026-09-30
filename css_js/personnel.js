/*=========================================
CGCI PERSONNEL PUBLIC ORGANIZATIONAL CHART
=========================================*/

const personnelCard = document.getElementById("personnelCard");
const personnelPage = document.getElementById("personnelPage");
const personnelDetailsPage = document.getElementById("personnelDetailsPage");
const backPersonnel = document.getElementById("backPersonnel");
const backDepartment = document.getElementById("backDepartment");
const departmentTitle = document.getElementById("departmentTitle");
const departmentDescription = document.getElementById("departmentDescription");
const orgZoomReset = document.getElementById("orgZoomReset");

if (personnelCard) {
    personnelCard.addEventListener("click", function(){
        informationPage.style.display="none";
        personnelPage.style.display="block";
    });
}

if (backPersonnel) {
    backPersonnel.addEventListener("click",function(){
        personnelPage.style.display="none";
        informationPage.style.display="block";
    });
}

document.querySelectorAll(".personnel-department-card").forEach(function(card){
    card.addEventListener("click",function(){
        const id = this.dataset.id;
        const name = this.querySelector(".department-name").innerText.trim();
        const section = document.getElementById("department" + id);

        departmentTitle.innerHTML = name;
        departmentDescription.innerHTML = section?.dataset.description || "Organizational chart";

        personnelPage.style.display="none";
        personnelDetailsPage.style.display="block";

        document.querySelectorAll(".department-personnel").forEach(function(department){
            department.style.display="none";
        });

        if (section) section.style.display="block";

        const scroller = document.querySelector(".org-chart-scroll");
        if (scroller) scroller.scrollLeft = 0;
        window.scrollTo(0,0);
    });
});

if (backDepartment) {
    backDepartment.addEventListener("click",function(){
        personnelDetailsPage.style.display="none";
        personnelPage.style.display="block";
    });
}

if (orgZoomReset) {
    orgZoomReset.addEventListener("click", function(){
        const scroller = document.querySelector(".org-chart-scroll");
        if (scroller) scroller.scrollLeft = 0;
        window.scrollTo(0,0);
    });
}
