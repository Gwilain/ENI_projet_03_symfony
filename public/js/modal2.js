const modal = document.getElementById("customModal")

function displayModal(item) {

    template = item.dataset.template;
    if (!template) return;

    modalBody = document.getElementById("modal-body");

    modalBody.innerHTML = document.getElementById(template).innerHTML;
    modal.style.display = "block";

    const secondTItle = document.getElementById("secondTitle");
    secondTItle.innerHTML = item.dataset.secondTitle;


    const modalLink = document.getElementById("modalLink");
    modalLink.href = item.dataset.sortieLink;
    const modalLink2 = document.getElementById("secondLink");
    if( modalLink2 ){
        modalLink2.href = item.dataset.sortieSecondLink;
    }
}


modal.querySelector(".close").onclick = function () {
    modal.style.display = "none";
}

window.onclick = function (event) {
    if (event.target === modal) {
        modal.style.display = "none";
    }
}