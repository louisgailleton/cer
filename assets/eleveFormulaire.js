/*   INSCRIPTION   */

// Rend obligatoire le téléphone et le mail du parent si l'élève est mineur
let inputDateNaiss = document.getElementById("eleve_formulaire_dateNaiss");
inputDateNaiss.addEventListener("change", function (){
    let dateNaiss = inputDateNaiss.value;
    let dateJour = new Date();
    dateNaiss = new Date(dateNaiss);
    let age = dateJour.getFullYear() - dateNaiss.getFullYear();
    let m = dateJour.getMonth() - dateNaiss.getMonth();
    if (m < 0 || (m === 0 && dateJour.getDate() < dateNaiss.getDate()))
    {
        age--;
    }
    let telParent = document.getElementById("eleve_formulaire_telParent");
    let mailParent = document.getElementById("eleve_formulaire_mailParent");
    if(age < 18 && !telParent.hasAttribute("required")) {
        let span = document.createElement("span");
        let span2 = document.createElement("span");
        span.classList.add("badge", "badge-danger", "badge-pill");
        span.innerText = "REQUIS";
        span2.classList.add("badge", "badge-danger", "badge-pill");
        span2.innerText = "REQUIS";

        telParent.setAttribute("required", "required");
        telParent.previousElementSibling.innerHTML += " ";
        telParent.previousElementSibling.append(span);
        mailParent.setAttribute("required", "required");
        mailParent.previousElementSibling.innerHTML += " ";
        mailParent.previousElementSibling.append(span2);
    } else if(age >= 18 && telParent.hasAttribute("required")) {
        telParent.removeAttribute("required");
        telParent.previousElementSibling.removeChild(telParent.previousElementSibling.firstElementChild);
        mailParent.removeAttribute("required");
        mailParent.previousElementSibling.removeChild(mailParent.previousElementSibling.firstElementChild);
    }
});

// Récupération du select et des input
let select = document.getElementById("eleve_formulaire_statutSocial");
let selectLycee = document.getElementById("eleve_formulaire_lycee");
let autreLycee = document.getElementById("inputLyceeAutre");
let inputAutreLycee = document.getElementById("eleve_formulaire_lyceeAutre");

let inputLycee = document.getElementById("inputLycee");
let inputMetier = document.getElementById("inputMetier");
let inputNomSociete = document.getElementById("inputNomSociete");

// Affichage des infos liées au statut social si la page est rechargée (erreur dans la date naiss)
if (select.value === 'Lycéen.ne') {
    affichageLycee();
    if(selectLycee.value === "Autre lycée") {
        autreLycee.style.display = 'inline';
    } else {
        autreLycee.style.display = 'none';
    }
}
else if (select.value === 'Salarié.e') {
    affichageSalarie();
}
else {
    affichageAutre();
}

// Fonction pour afficher l'input "autre lycée"
selectLycee.addEventListener('change', (eventLycee) => {
    if(eventLycee.target.value === "Autre lycée") {
        autreLycee.style.display = 'inline';
    } else {
        autreLycee.style.display = 'none';
    }
})

// EventListener sur statut social
select.addEventListener('change', (event) => {
    // Si l'élève choisit lycée, on affiche l'input lycée et on masque les autres
    if (event.target.value === 'Lycéen.ne') {
        affichageLycee();
        if(selectLycee.value === "Autre lycée") {
            autreLycee.style.display = 'inline';
        } else {
            autreLycee.style.display = 'none';
        }
    } else if (event.target.value === 'Salarié.e') {
        affichageSalarie();
    }
    else {
        affichageAutre();
    }
});

function affichageLycee(){
    inputLycee.style.display = 'inline';
    document.getElementById("eleve_formulaire_lycee").setAttribute('required', 'required');

    inputMetier.style.display = 'none';
    inputNomSociete.style.display = 'none';
    document.getElementById("eleve_formulaire_metier").removeAttribute('required');
    document.getElementById("eleve_formulaire_nomSociete").removeAttribute('required');
}

function affichageSalarie(){
    inputMetier.style.display = 'inline';
    inputNomSociete.style.display = 'inline';
    document.getElementById("eleve_formulaire_metier").setAttribute('required', 'required');
    document.getElementById("eleve_formulaire_nomSociete").setAttribute('required', 'required');

    inputLycee.style.display = 'none';
    autreLycee.style.display = 'none';
    document.getElementById("eleve_formulaire_lycee").removeAttribute('required');
}

function affichageAutre(){
    inputLycee.style.display = 'none';
    inputMetier.style.display = 'none';
    inputNomSociete.style.display = 'none';
    autreLycee.style.display = 'none';
    document.getElementById("eleve_formulaire_lycee").removeAttribute('required');
    document.getElementById("eleve_formulaire_metier").removeAttribute('required');
    document.getElementById("eleve_formulaire_nomSociete").removeAttribute('required');
}