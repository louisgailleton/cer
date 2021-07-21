/* CODE POUR AFFICHER LE CONTENU D'UN FORFAIT */
let detailForfait = document.getElementById("contenuForfaitSelectionne");
let inforForfaitSelectionne = document.getElementById("infoForfaitSelectionne");
let btnForfaitBoutique = document.getElementsByClassName("btnForfaitBoutique");
let btnAjoutForfaitPannierBoutique = document.getElementById("ajouterForfaitPannierBoutique");
for(let i = 0; i < btnForfaitBoutique.length; i++) {
    btnForfaitBoutique[i].addEventListener("click", function(){
        if(btnForfaitBoutique[i].dataset.nom == null) {
            alert("Vous devez valider les étapes précédentes avant de choisir votre forfait");
        } else {
            inforForfaitSelectionne.innerText = btnForfaitBoutique[i].dataset.nom + " - " + btnForfaitBoutique[i].dataset.prix + "€";
            detailForfait.innerHTML = btnForfaitBoutique[i].dataset.contenu;
            btnAjoutForfaitPannierBoutique.style.display = "block";
        }
    });
}

$(document).ready(function() {
    $('[data-toggle="popover"]').popover({
       placement: 'top',
       trigger: 'hover'
    });
});

document.getElementById("btn_payer").addEventListener("click", function (){
   alert("Le site est encore en développement.\nIl va falloir patienter encore quelques jours avant de pouvoir utiliser la boutique.");
});