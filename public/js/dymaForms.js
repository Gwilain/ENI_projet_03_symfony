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

// Pour DOM classique
document.addEventListener('DOMContentLoaded', onLieuChangeListener);

// Pour navigation Turbo
document.addEventListener('turbo:load', onLieuChangeListener);