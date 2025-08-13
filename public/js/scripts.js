function init() {

    if (init.done) return;
    init.done = true;

    initMenu();
    onLieuChangeListener();
    initDropDownAccueil();
    initModal();
}

/*************************************************************************/
// MENU RESPONSIVE
/*************************************************************************/

function initMenu() {

    const btnBurger = document.querySelector('.btnBurger');
    const menu = document.querySelector('nav ul');
    const nav = document.querySelector('nav');

    btnBurger.addEventListener('click', () => {
        // menu.classList.toggle('open');
        nav.classList.toggle('open');
    });

}

/*************************************************************************/
// MODAL MODAL
/*************************************************************************/

function initModal() {
    // var modal = document.getElementById("customModal")

    const modal = document.getElementById("customModal");

    document.querySelectorAll("[data-template]").forEach(btn => {
        btn.addEventListener("click", () => displayModal(btn));
    });

    if (modal != null) {
        modal.querySelector(".close").onclick = function () {
            modal.style.display = "none";
        };
    }

    window.addEventListener("click", (event) => {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    });
}


function displayModal(item) {

    template = item.dataset.template;
    if (!template) return;

    const modal = document.getElementById("customModal");

    let modalBody = document.getElementById("modal-body");

    modalBody.innerHTML = document.getElementById(template).innerHTML;
    modal.style.display = "block";

    const secondTItle = document.getElementById("secondTitle");
    secondTItle.innerHTML = item.dataset.secondTitle;

    // try{
    const modalLink = document.getElementById("modalLink");
    if(modalLink){
        modalLink.href = item.dataset.link;

    }else{
        console.log("il n'y a pas de a tout est normal")
    }


    const modalLink2 = document.getElementById("secondLink");
    if (modalLink2) {
        modalLink2.href = item.dataset.secondLink;
    }
}

/*************************************************************************/
// DropDown form accueil
/*************************************************************************/

function initDropDownAccueil() {


    console.log("initDropDownAccueil")

    document.querySelectorAll('.dropdown-toggle').forEach(btn => {
        btn.addEventListener('click', function () {
            const parent = this.closest('.dropdown-multi');
            parent.classList.toggle('open');
        });
    });

// Clique en dehors :: ferme tous les dropdowns
    document.addEventListener('click', function (e) {
        document.querySelectorAll('.dropdown-multi').forEach(drop => {
            if (!drop.contains(e.target)) {
                drop.classList.remove('open');
            }
        });
    });
}


/*************************************************************************/
// Affichage dynamique du lieu sur la page créatiOn modif d'une sortie
/*************************************************************************/

function onLieuChangeListener() {
    const selectLieu = document.getElementById('sortie_lieu');
    const adresseSpan = document.getElementById('lieuAdresse');
    const cpSpan = document.getElementById('lieuCodePostal');
    const coorSpan = document.getElementById('lieuCoor');

    if (!selectLieu) return;

    selectLieu.addEventListener('change', function () {
        const lieuId = this.value;

        if (lieuId) {
            fetch('/sortie/lieu/adresse/' + lieuId)
                .then(response => response.json())
                .then(data => {
                    adresseSpan.textContent = data.adresse;
                    cpSpan.textContent = data.code_postal;
                    coorSpan.textContent = data.lat + ' / ' + data.long;
                })
                .catch(error => {
                    console.error('Erreur AJAX :', error);
                });
        } else {
            adresseSpan.textContent = '_____________';
            cpSpan.textContent = '_____________';
            coorSpan.textContent = '_____________';
        }
    });
}


////////////////////////////////////////////////////
// Pour DOM classique
document.addEventListener('DOMContentLoaded', init);

// Pour navigation Turbo
document.addEventListener('turbo:load', init);