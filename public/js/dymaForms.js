document.addEventListener('DOMContentLoaded', function () {
    console.log("DYANFORM JS IS READY");

    const selectLieu = document.getElementById('sortie_lieu'); // adapte l'ID
    const adresseSpan = document.getElementById('lieuAdresse');
    const cpSpan = document.getElementById('lieuCodePostal');
    const coorSpan = document.getElementById('lieuCoor');

    selectLieu.addEventListener('change', function () {

        // console.log("ON LIEU CHANGE !!! ID = " + this.value);

        const lieuId = this.value;

        if (lieuId) {
            // console.log("ON TENTE DE CHARGER  le json :::");
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
            // vider si aucun lieu sélectionné
            adresseSpan.textContent = '_____________';
            cpSpan.textContent = '_____________';
            coorSpan.textContent = '_____________';
        }
    });
});