/* **************************************************************** */
/* AFFICHAGE DES ÉLÈVES SELON L'ÉTAT DE LEUR DOSSIER */
/* **************************************************************** */

let btnMenuEnAttente = document.getElementById("btnMenuEnAttente");
let btnMenuATraiter = document.getElementById("btnMenuATraiter");
let btnMenuSaisiANTS = document.getElementById("btnMenuSaisiANTS");
let btnMenuEnregistre = document.getElementById("btnMenuEnregistre");

/* Affichage de la page sur laquelle la sécrétaire avant une actualisation */
let choixMenu = getCookie("menuListeEleves");
$(document).ready( function (){
    if(choixMenu === "enAttente") {
        menuEnAttente();
    } else if (choixMenu === "aTraiter") {
        $("#tableauATraiter_wrapper").css("display", "block");
        menuATraiter();
    } else if (choixMenu === "saisiANTS") {
        menuSaisiANTS();
    } else if (choixMenu === "enregistre") {
        menuEnregistre();
    } else {
        menuEnAttente();
    }
});

btnMenuEnAttente.addEventListener("click", function () {
    menuEnAttente();
});

btnMenuATraiter.addEventListener("click", function () {
    menuATraiter();
});

btnMenuSaisiANTS.addEventListener("click", function () {
    menuSaisiANTS();
});

btnMenuEnregistre.addEventListener("click", function () {
    menuEnregistre();
});

function menuEnAttente() {
    activerBoutonMenu(btnMenuEnAttente);
    desactiverBoutonMenu(btnMenuATraiter);
    desactiverBoutonMenu(btnMenuSaisiANTS);
    desactiverBoutonMenu(btnMenuEnregistre);

    $("#tableauEnAttente_wrapper").css("display", "block");
    $("#tableauATraiter_wrapper").css("display", "none");
    $("#tableauSaisiANTS_wrapper").css("display", "none");
    $("#tableauEnregistre_wrapper").css("display", "none");

    document.getElementById("listePJ").style.display = "none";
    document.cookie = "menuListeEleves=enAttente";
}

function menuATraiter() {
    // On change le bouton actif du menu
    desactiverBoutonMenu(btnMenuEnAttente);
    activerBoutonMenu(btnMenuATraiter);
    desactiverBoutonMenu(btnMenuSaisiANTS);
    desactiverBoutonMenu(btnMenuEnregistre);

    $("#tableauEnAttente_wrapper").css("display", "none");
    $("#tableauATraiter_wrapper").css("display", "block");
    $("#tableauSaisiANTS_wrapper").css("display", "none");
    $("#tableauEnregistre_wrapper").css("display", "none");

    document.getElementById("listePJ").style.display = "block";
    document.getElementById("validerANTS").style.display = "none";
    document.cookie = "menuListeEleves=aTraiter";
}

function menuSaisiANTS() {
    desactiverBoutonMenu(btnMenuEnAttente);
    desactiverBoutonMenu(btnMenuATraiter);
    activerBoutonMenu(btnMenuSaisiANTS);
    desactiverBoutonMenu(btnMenuEnregistre);

    $("#tableauEnAttente_wrapper").css("display", "none");
    $("#tableauATraiter_wrapper").css("display", "none");
    $("#tableauSaisiANTS_wrapper").css("display", "block");
    $("#tableauEnregistre_wrapper").css("display", "none");

    document.getElementById("listePJ").style.display = "block";
    document.getElementById("validerANTS").style.display = "list-item";
    document.cookie = "menuListeEleves=saisiANTS";
}

function menuEnregistre() {
    desactiverBoutonMenu(btnMenuEnAttente);
    desactiverBoutonMenu(btnMenuATraiter);
    desactiverBoutonMenu(btnMenuSaisiANTS);
    activerBoutonMenu(btnMenuEnregistre);

    $("#tableauEnAttente_wrapper").css("display", "none");
    $("#tableauATraiter_wrapper").css("display", "none");
    $("#tableauSaisiANTS_wrapper").css("display", "none");
    $("#tableauEnregistre_wrapper").css("display", "block");

    document.getElementById("listePJ").style.display = "none";
    document.cookie = "menuListeEleves=enregistre";
}

// Fonctions pour changer le bouton actif du menu
function activerBoutonMenu(bouton) {
    bouton.parentElement.classList.add('active');
    bouton.parentElement.setAttribute("aria-current", "page");
}
function desactiverBoutonMenu(bouton) {
    bouton.parentElement.classList.remove('active')
    bouton.parentElement.removeAttribute("aria-current");
    bouton.removeAttribute("disabled");
}

/* **************************************************************** */
/* AFFICHAGE DES INFORMATIONS DE L'ÉLÈVE */
/* **************************************************************** */

//  On supprime l'élément vide de la liste des lycées
document.getElementById("secretaire_eleve_lycee").firstChild.remove();

// Lorsque la secrétaire modifie un élève pour met à jours ses pièces jointes
// la page est actualisée. Avec ces lignes, on affiche automatiquement les informations
// liées au statut social (lycée et/ou lycéeAutre ou métier et nom société)
let statutSocialEleve = document.getElementById("secretaire_eleve_statutSocial").value;
let lyceeEleve = document.getElementById("secretaire_eleve_lycee").value;
let lyceeAutreEleve = document.getElementById("secretaire_eleve_lyceeAutre").value;
if(statutSocialEleve != null) {
    affichageInfoStatutSocial(statutSocialEleve, lyceeEleve, lyceeAutreEleve);
}

// Modification du formulaire de validation des pièces jointes
/*let inputEphoto = document.getElementById("secretaire_pj_ephoto");
affichageFormPj(inputEphoto);
let inputCNI = document.getElementById("secretaire_pj_cni");
affichageFormPj(inputCNI);
let inputJustifDom= document.getElementById("secretaire_pj_justifdom");
affichageFormPj(inputJustifDom);
let inputAttestHeb = document.getElementById("secretaire_pj_attestheb");
affichageFormPj(inputAttestHeb);
let inputAttestJDC = document.getElementById("secretaire_pj_attestjdc");
affichageFormPj(inputAttestJDC);
let inputAutreP = document.getElementById("secretaire_pj_autrep");
affichageFormPj(inputAutreP);*/

/*function affichageFormPj(input) {
    input.classList.add("row");
    // Récupération du groupe : btn radio + label
    input.firstElementChild.style.marginRight = "20px";
    // Récupération du label et ajout d'un fa icon
    input.firstElementChild.lastElementChild.innerHTML = "<i class=\"fa fa-check\" aria-hidden=\"true\"></i>";
    input.lastElementChild.lastElementChild.innerHTML = "<i class=\"fa fa-times\" aria-hidden=\"true\"></i>";
}*/


// On récupère toutes les lignes des élèves
let lignesEleve = document.getElementsByClassName("ligneEleve");
for(let i = 0; i < lignesEleve.length; i++) {

    // JSON contenant les informations de l'élèves
    let json = JSON.parse(lignesEleve[i].dataset.infoeleve);

    // Lors d'un clique sur une ligne
    lignesEleve[i].addEventListener("click", function(){

        /*  *************************** */
        /* AFFICHAGE D'UN ÉLÈVE ET DE SES INFORMATIONS
        /*  *************************** */
        
        // On récupère le json des informations de l'élève
        document.getElementById("secretaire_eleve_id").value = json.id;
        document.getElementById("secretaire_eleve_prenom").value = json.prenom;
        document.getElementById("secretaire_eleve_nom").value = json.nom;
        document.getElementById("secretaire_eleve_autrePrenoms").value = json.autrePrenoms;
        document.getElementById("secretaire_eleve_nomUsage").value = json.nomUsage;
        document.getElementById("secretaire_eleve_mail").value = json.mail;
        document.getElementById("secretaire_eleve_dateNaiss").value = (json.dateNaiss != null) ? json.dateNaiss.date.split(' ')[0] : "";
        document.getElementById("secretaire_eleve_telephone").value = json.telephone;
        document.getElementById("secretaire_eleve_telParent").value = json.telephoneParent;
        document.getElementById("secretaire_eleve_adresse").value = json.adresse;
        document.getElementById("secretaire_eleve_ville").value = json.ville;
        document.getElementById("secretaire_eleve_cp").value = json.cp;
        document.getElementById("secretaire_eleve_paysNaiss").value = json.paysNaiss;
        document.getElementById("secretaire_eleve_depNaiss").value = json.depNaiss;
        document.getElementById("secretaire_eleve_villeNaiss").value = json.villeNaiss;
        document.getElementById("secretaire_eleve_statutSocial").value = json.statutSocial;
        document.getElementById("secretaire_eleve_lunette").value = json.lunette;
        document.getElementById("secretaire_eleve_lycee").value = json.lycee;
        document.getElementById("secretaire_eleve_lyceeAutre").value = json.lyceeAutre;
        document.getElementById("secretaire_eleve_metier").value = json.metier;
        document.getElementById("secretaire_eleve_nomSociete").value = json.nomSociete;

        /*  *************************** */
        /* AFFICHAGE DES PIÈCES JOINTES
        /*  *************************** */
        if(lignesEleve[i].className.includes("aTraiter") || lignesEleve[i].className.includes("saisiANTS")) {
            let jsonPJ = lignesEleve[i].dataset.pj.split("}");
            jsonPJ.pop();
            jsonPJ.forEach(pj => {
                pj += '}';
                pj = JSON.parse(pj);
                affichagePJ(pj)
            });
        }

        function affichagePJ(pj) {
            if(document.getElementById(pj.nomFichierUnique) === null) {
                let liPJ = document.createElement("li");

                let inputValidePJ = document.createElement("input");
                inputValidePJ.setAttribute("type", "radio");
                inputValidePJ.setAttribute("name", pj.nomFichier + pj.id);
                inputValidePJ.setAttribute("id", "valide" + pj.nomFichier + pj.id);
                inputValidePJ.setAttribute("value", "valide");
                inputValidePJ.setAttribute("required", "required");

                let inputRefusePJ = document.createElement("input");
                inputRefusePJ.setAttribute("type", "radio");
                inputRefusePJ.setAttribute("name", pj.nomFichier + pj.id);
                inputRefusePJ.setAttribute("id", "refuse" + pj.nomFichier + pj.id);
                inputRefusePJ.setAttribute("value", "refuse");
                inputRefusePJ.setAttribute("required", "required");
                if(pj.etat === "1") {
                    inputRefusePJ.checked = true;
                }

                let labelValidePJ = document.createElement("label");
                labelValidePJ.setAttribute("for", "valide" + pj.nomFichier);
                labelValidePJ.innerHTML = "<i class=\"fa fa-check\" aria-hidden=\"true\"></i>";
                let labelRefusePJ = document.createElement("label");
                labelRefusePJ.setAttribute("for", "refuse" + pj.nomFichier);
                labelRefusePJ.innerHTML = "<i class=\"fa fa-times\" aria-hidden=\"true\"></i>";

                let lienPJ = document.createElement("a");
                document.getElementById("idEleve").value = pj.idEleve;
                lienPJ.setAttribute("href", "/piecesjointes/" + pj.nomFichierUnique);
                lienPJ.setAttribute("target", "_blank");
                lienPJ.setAttribute("id", pj.nomFichierUnique);
                lienPJ.innerText = pj.nomFichier;

                liPJ.appendChild(lienPJ);
                liPJ.appendChild(inputValidePJ);
                liPJ.insertBefore(labelValidePJ, inputValidePJ);
                liPJ.appendChild(inputRefusePJ);
                liPJ.insertBefore(labelRefusePJ, inputRefusePJ);
                document.getElementById("liste" + pj.typePJ).appendChild(liPJ);
            }
        }

        affichageInfoStatutSocial(json.statutSocial, json.lycee, json.lyceeAutre);
    });
}

// Fonction regroupant toutes les fonctions permettant d'afficher les
// informations liées au statut social
function affichageInfoStatutSocial(statutSocial, lycee, lyceeAutre) {

    let select = document.getElementById("secretaire_eleve_statutSocial");
    let selectLycee = document.getElementById("secretaire_eleve_lycee");

    let blockLycee = document.getElementById("infoLycee");
    let autreLycee = document.getElementById("infoLyceeAutre");
    let blockMetier = document.getElementById("infoMetier");
    let blockNomSociete = document.getElementById("infoSociete");

    let inputLycee = document.getElementById("secretaire_eleve_lycee");
    let inputAutreLycee = document.getElementById("secretaire_eleve_lyceeAutre");
    let inputMetier = document.getElementById("secretaire_eleve_metier");
    let inputNomSociete =  document.getElementById("secretaire_eleve_nomSociete");

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
        }
    }
    else if (statutSocial === 'Salarié.e') {
        afficherBlockSalarie();
    }
    else {
        afficherAutreBlock();
    }

    // Affichage des différents statuts sociaux selon le choix de la liste déroulante
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
            }
        }
        else if (event.target.value === 'Salarié.e') {
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
        }
    })

    function afficherBlockLycee() {
        blockLycee.style.display = 'inline';
        inputLycee.setAttribute('required', 'required');

        blockMetier.style.display = 'none';
        blockNomSociete.style.display = 'none';
        inputMetier.removeAttribute('required');
        inputNomSociete.removeAttribute('required');
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
    }
}

// Fonction pour récupérer la valeur du cookie
function getCookie(cname) {
    let name = cname + "=";
    let decodedCookie = decodeURIComponent(document.cookie);
    let ca = decodedCookie.split(';');
    for(let i = 0; i <ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) === ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) === 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}