$('#modalPorteOuverte').on('show.bs.modal', function (event) {
    let button = $(event.relatedTarget)
    let idPo = button.data('idpo');
    let placedispo = button.data('placedispo');
    let nbPersonne = button.data('nbpersonne');
    let inputNbPersonne = document.getElementById("eleve_porte_ouverte_nbPersonne");
    let inputId = document.getElementById("eleve_porte_ouverte_idPorteOuverte");

    inputId.setAttribute("value", idPo);

    if (placedispo < 3) {
        inputNbPersonne.setAttribute("max", placedispo);
        inputNbPersonne.value = 1;
    }

    if(nbPersonne !== "") {
        if(placedispo + nbPersonne === 2) {
            inputNbPersonne.setAttribute("max", "2");
            inputNbPersonne.value = nbPersonne;
        } else if(placedispo + nbPersonne === 1) {
            inputNbPersonne.setAttribute("max", "1");
            inputNbPersonne.value = nbPersonne;
        }
        else {
            inputNbPersonne.value = nbPersonne;
            inputNbPersonne.setAttribute("max", "3");
        }
    }
})