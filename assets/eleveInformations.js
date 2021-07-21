/*   Mon Compte   */

// Rend obligatoire le téléphone et le mail du parent si l'élève est mineur
let inputDateNaiss = document.getElementById("eleve_informations_dateNaiss");
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
    let telParent = document.getElementById("eleve_informations_telParent");
    let mailParent = document.getElementById("eleve_informations_mailParent");
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
let select = document.getElementById("eleve_informations_statutSocial");
let selectLycee = document.getElementById("eleve_informations_lycee");
let autreLycee = document.getElementById("inputLyceeAutre2");
let inputAutreLycee = document.getElementById("eleve_informations_lyceeAutre");

let blockLycee = document.getElementById("inputLycee2");
let blockMetier = document.getElementById("inputMetier2");
let blockNomSociete = document.getElementById("inputNomSociete2");

let inputLycee = document.getElementById("eleve_informations_lycee");
let inputMetier = document.getElementById("eleve_informations_metier");
let inputNomSociete =  document.getElementById("eleve_informations_nomSociete");


// Affichage du statut social dès le chargement de la page
let statutSocial = document.getElementById("statutSocial").value;
let lycee = document.getElementById("lycee").value;
let lyceeAutre = document.getElementById("lyceeAutre").value;
let metier = document.getElementById("metier").value;
let nomSociete = document.getElementById("nomSociete").value;

select.value = statutSocial;
if (statutSocial === 'Lycéen.ne') {
    afficherBlockLycee();
    selectLycee.value = lycee;
    if(lycee === "Autre lycée") {
        autreLycee.style.display = 'inline';
        inputAutreLycee.setAttribute('required', 'required');
    } else {
        autreLycee.style.display = 'none';
        inputAutreLycee.removeAttribute('required');
        inputAutreLycee.value = "";
        inputMetier.value = metier;
        inputNomSociete.value = nomSociete;
    }
}
else if (statutSocial === 'Salarié.e') {
    afficherBlockSalarie();
}
else {
    afficherAutreBlock();
}

// Affichage des différent statut social selon le choix de la liste déroulante
select.addEventListener('change', (event) => {
    // Si l'élève choisit lycée, on affiche l'input lycée et on masque les autres
    if (event.target.value === 'Lycéen.ne') {
        afficherBlockLycee();
        if(lycee === "Autre lycée") {
            autreLycee.style.display = 'inline';
            inputAutreLycee.setAttribute('required', 'required');
            inputAutreLycee.value = lyceeAutre;
        } else {
            autreLycee.style.display = 'none';
            inputAutreLycee.removeAttribute('required');
            inputAutreLycee.value = "";
        }
    }
    else if (event.target.value === 'Salarié.e') {
        inputMetier.value = metier;
        inputNomSociete.value = nomSociete;
        afficherBlockSalarie();
    }
    else {
        afficherAutreBlock();
    }
});

// Fonction pour afficher l'input "autre lycée"
selectLycee.addEventListener('change', (eventLycee) => {
    if(eventLycee.target.value === "Autre lycée") {
        autreLycee.style.display = 'inline';
        inputAutreLycee.setAttribute('required', 'required');
    } else {
        autreLycee.style.display = 'none';
        inputAutreLycee.removeAttribute('required');
        inputAutreLycee.value = "";
    }
})

function afficherBlockLycee() {
    blockLycee.style.display = 'inline';
    inputLycee.setAttribute('required', 'required');

    blockMetier.style.display = 'none';
    blockNomSociete.style.display = 'none';
    inputMetier.removeAttribute('required');
    inputNomSociete.removeAttribute('required');
    inputMetier.value = "";
    inputNomSociete.value = "";
}

function afficherBlockSalarie() {
    blockMetier.style.display = 'inline';
    blockNomSociete.style.display = 'inline';
    inputMetier.setAttribute('required', 'required');
    inputNomSociete.setAttribute('required', 'required');

    blockLycee.style.display = 'none';
    autreLycee.style.display = 'none';
    inputLycee.removeAttribute('required');
    inputAutreLycee.removeAttribute('required');
}

function afficherAutreBlock() {
    blockLycee.style.display = 'none';
    autreLycee.style.display = 'none';
    blockMetier.style.display = 'none';
    blockNomSociete.style.display = 'none';
    inputLycee.removeAttribute('required');
    inputAutreLycee.removeAttribute('required');
    inputMetier.removeAttribute('required');
    inputNomSociete.removeAttribute('required');

    inputAutreLycee.value = "";
    inputMetier.value = "";
    inputNomSociete.value = "";
}