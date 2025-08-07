function onModal() {
    const modal = document.getElementById("customModal");
    const span = modal.querySelector(".close");

    const modalBody = document.getElementById("modal-body");

    window.openModal = function(templateId, context = {}) {
        const template = document.getElementById(templateId);
        if (!template) return;

        modalBody.innerHTML = template.innerHTML;
        modal.style.display = "block";
    }

    /*if (context.secondTitle) {
        const titleElem = modalBody.querySelector("#secondTitle");
        if (titleElem) {
            titleElem.textContent = context.secondTitle;
        }
    }

    // Injecte le lien (href du bouton Publier)
    if (context.sortieLink) {
        const linkElem = modalBody.querySelector("a.btn");
        if (linkElem) {
            linkElem.href = context.sortieLink;
        }
    }*/

    span.onclick = function() {
        modal.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    }

    // Ajoute le comportement aux boutons dynamiquement
    document.querySelectorAll(".open-modal-button").forEach(button => {
        button.addEventListener("click", () => {
            const templateId = button.dataset.modalId;
            const secondTitle = button.dataset.secondTitle;
            const sortieLink = button.dataset.sortieLink;

            openModal(templateId, {
                secondTitle,
                sortieLink
            });
        });
    });
}


// Pour DOM classique
// document.addEventListener('DOMContentLoaded', onModal);

// Pour navigation Turbo
//document.addEventListener('turbo:load', onModal);