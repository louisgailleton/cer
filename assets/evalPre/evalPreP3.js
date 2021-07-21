/* CODE POUR LES ÉTAPES D'INSTALLATION DE LA PARTIE 3 */

// Récupération des checkboxes
let checkboxes = document.getElementsByName("etape");
// Création d'un tableau qui contiendra les réponses
// Et récupération de l'input qui recevra le tableau contenant les réponse
let tableauEtape = [];
let checkboxesCochees = 0;
let inputTableauEtape = document.getElementById("form_installation");
let messageErreur = document.getElementById("messageErreurEval");
let btnValidation = document.getElementById("validationEvalPre");

// Boucle sur toutes les checkboxes
for(let i = 0; i < checkboxes.length; i++) {
    // Lors d'un clique sur une checkbox
    checkboxes[i].addEventListener("click", function(){
        // Si on l'a coche
        if(checkboxes[i].checked) {
            // Boucle sur tous les éléments de la liste
            for(let j = 0; j < 8; j++) {
                let elementListe = document.getElementById("installation" + [j]);
                // Dès qu'un élément est vide, on écrit la valeur de la checkbox dedans
                if(elementListe.innerText === ""){
                    elementListe.innerText = checkboxes[i].value;
                    // Ajout de la valeur de la checkbox dans le tablau
                    tableauEtape.push(checkboxes[i].id)
                    break;
                }
            }
        }
        // Si on l'a déchoche
        else {
            // Boucle sur tous les éléments de la liste
            for(let j = 0; j < 8; j++) {
                let elementListe = document.getElementById("installation" + [j]);
                // Récupération de l'élément de la liste qui correspond à la checkbox décochée
                if(elementListe.innerText === checkboxes[i].value){
                    // On supprime la valeur de la checkbox du tableau
                    tableauEtape.splice(tableauEtape.indexOf(checkboxes[i].id), 1);
                    // Avec cette boucle on fait remonter les éléments de la liste pour remplacer celui qui vient d'être supprimé
                    for(let k = j; k < 8; k++) {
                        let elementListe = document.getElementById("installation" + [k])
                        if(k < 7) {
                            elementListe.innerText = document.getElementById("installation" + [k + 1]).innerText;
                        } else {
                            elementListe.innerText = "";
                        }
                    }
                }
            }
        }

        if( checkboxes[i].checked) {
            checkboxesCochees++;
        } else {
            checkboxesCochees--;
        }
        if(checkboxesCochees === 8) {
            inputTableauEtape.value = tableauEtape;
            verifCochee();
        } else {
            messageErreur.style.display = "block";
            messageErreur.innerText = "Une ou plusieurs réponses n'ont pas été validées"
            messageErreur.style.color = "red";
            btnValidation.style.display = "none";
        }
    });
}

let nbCheckboxp3Q2 = 0;
let checkboxp3Q2 = document.getElementsByName("form[p3Q2][]");

for(let i = 0; i < checkboxp3Q2.length; i++) {
    checkboxp3Q2[i].addEventListener("click", function(){
        if (checkboxp3Q2[i].checked) {
            nbCheckboxp3Q2++;
        } else {
            nbCheckboxp3Q2--;
        }
        verifCochee();
    });
}

function verifCochee() {
    if(nbCheckboxp3Q2 >= 1 && checkboxesCochees === 8) {
        btnValidation.style.display = "block";
        messageErreur.style.display = "none";
    } else {
        messageErreur.style.display = "block";
        messageErreur.innerText = "Une ou plusieurs réponses n'ont pas été validées"
        messageErreur.style.color = "red";
        btnValidation.style.display = "none";
    }
}