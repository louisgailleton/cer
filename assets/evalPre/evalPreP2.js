/* Code pour afficher les 4 images de la question 1 */

let q1A = document.getElementById("form_p2Q1_0");
let labelQ1A = q1A.labels[0];
labelQ1A.innerHTML = "<img src='../../evalPre/carreBleu.png' alt='Panneau carré bleu'>";


let q1B = document.getElementById("form_p2Q1_1");
let labelQ1B = q1B.labels[0];
labelQ1B.innerHTML = "<img src='../../evalPre/rondBleu.png' alt='Panneau rond bleu'>";

let q1C = document.getElementById("form_p2Q1_2");
let labelQ1C = q1C.labels[0];
labelQ1C.innerHTML = "<img src='../../evalPre/rondBlanc.png' alt='Panneau rond blanc avec contour rouge'>";

let q1D = document.getElementById("form_p2Q1_3");
let labelQ1D = q1D.labels[0];
labelQ1D.innerHTML = "<img src='../../evalPre/rondBleuRouge.png' alt='Panneau carré bleu avec contour rouge et barre diagonale rouge'>";

/* On vérifie si au moins une checkbox de chaque question est cochée */
let nbCheckboxp2Q4 = 0;
let nbCheckboxp2Q8 = 0;
let checkboxp2Q4 = document.getElementsByName("form[p2Q4][]");
let checkboxp2Q8 = document.getElementsByName("form[p2Q8][]");

for(let i = 0; i < checkboxp2Q4.length; i++) {
    checkboxp2Q4[i].addEventListener("click", function(){
        if (checkboxp2Q4[i].checked) {
            nbCheckboxp2Q4++;
        } else {
            nbCheckboxp2Q4--;
        }
        verifCochee();
    });
}
for(let i = 0; i < checkboxp2Q8.length; i++) {
    checkboxp2Q8[i].addEventListener("click", function(){
        if (checkboxp2Q8[i].checked) {
            nbCheckboxp2Q8++;
        } else {
            nbCheckboxp2Q8--;
        }
        verifCochee();
    });
}

function verifCochee() {
    let messageErreur = document.getElementById("messageErreurEval");
    let btnValidation = document.getElementById("validationEvalPre");
    if(nbCheckboxp2Q4 >= 1 &&  nbCheckboxp2Q8 >= 1) {
        btnValidation.style.display = "block";
        messageErreur.style.display = "none";
    } else {
        messageErreur.style.display = "block";
        messageErreur.innerText = "Une ou plusieurs réponses n'ont pas été validées"
        messageErreur.style.color = "red";
        btnValidation.style.display = "none";
    }
}