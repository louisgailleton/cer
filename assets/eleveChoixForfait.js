let caseForfait = document.getElementsByClassName("caseForfait");
let contenuForfait = document.getElementById("contenuForfait");
let nomForfait = document.getElementById("forfaitChoisi");
let prixForfait = document.getElementById("prixForfait");
for(let i = 0; i < caseForfait.length; i++) {
    caseForfait[i].addEventListener("click", function(){
        for(let j = 0; j < caseForfait.length; j++) {
            if(i === j) {
                caseForfait[i].classList.add("caseForfaitChoisi");
            } else {
                caseForfait[j].classList.remove("caseForfaitChoisi");
            }
        }
        nomForfait.innerHTML = caseForfait[i].dataset.nom;
        nomForfait.style.color = "black";
        contenuForfait.innerHTML = caseForfait[i].dataset.contenu;
        prixForfait.innerHTML = caseForfait[i].dataset.prix + "€";
    });
}
let contenuPanier = document.getElementById("contenuPanierForfait");
document.getElementById("ajoutForfaitPanier").addEventListener("click", function (){
    if(contenuForfait.innerHTML === "") {
        nomForfait.innerText = "Vous devez choisir un forfait";
        nomForfait.style.color = "red";
    } else {
        contenuPanier.innerHTML = "<div class='prixForfaitPanier'>" + prixForfait.innerText + "</div>" + nomForfait.innerText;
        contenuPanier.style.color = "black";
        document.getElementById("signerContrat").style.display = "block";
        document.getElementById("form_forfait").value = nomForfait.innerText;
    }
});

document.getElementById("signerContrat").addEventListener("click", function (){
   alert("La suite du site est en cours de développement.\nIl va falloir patienter encore quelques jours.");
});
