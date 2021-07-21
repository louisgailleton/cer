import { jsPDF } from "jspdf";
/* AFFICHAGE MODAL PJ */
$('#modalPJ').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget) // Button that triggered the modal
    var titre = button.data('nom')
    var type = button.data('type') // Extract info from data-* attributes
    var modal = $(this)

    let typePJ = document.getElementById("pieces_jointes_typePJ");
    typePJ.setAttribute("value", type)
    modal.find('.modal-title').text(titre)
})


let msg = document.getElementById("messageAttestation");
/* QUESTIONNAIRE JUSTIF DOMICILE */
document.getElementById("btnJustifOui").addEventListener("click", function(){
    msg.style.display = "block";
    msg.innerText = "Vous n'avez pas besoin d'une attestation d'hébergement";
    msg.style.color = "green";
    msg.style.textAlign = "center";
});

document.getElementById("btnJustifNon").addEventListener("click", function(){
    msg.style.display = "none";
});

document.getElementById("telechargementAttestation").addEventListener("click", function () {
    let prenomHeb = document.getElementById("prenomHeb").value;
    let nomHeb = document.getElementById("nomHeb").value;
    let adresseHeb = document.getElementById("adresseHeb").value;

    let prenomEleve = document.getElementById("prenomEleve").value;
    let nomEleve = document.getElementById("nomEleve").value;
    let dteNaissEleve = document.getElementById("dteNaissEleve").value;
    let villeNaissEleve = document.getElementById("villeNaissEleve").value;

    let msgAttesHeb = document.getElementById("messageQuestionnaire");
    let date = new Date()
    let dateJour = date.getDate()+"/"+(date.getMonth()+1)+"/"+date.getFullYear();

    if (prenomHeb !== "" && nomHeb !== "" && adresseHeb !== "") {
        if (nomHeb === nomEleve && prenomHeb === prenomEleve) {
            msgAttesHeb.innerText = "Vous n'avez pas besoin d'attestation d'hébergement";
            msgAttesHeb.style.color = "green";
            document.getElementById("bntPjDomicile").style.display = "block";
        } else {
            msgAttesHeb.innerText = "";
            let p = "Je soussigné(e) " + prenomHeb + " " + nomHeb +
                "\ndéclare sur l'honneur héberger à mon domicile \n" +
                prenomEleve + " " + nomEleve +
                "\nné le " + dteNaissEleve + " à " + villeNaissEleve + " à l'adresse suivante :" +
                "\n" + adresseHeb +
                "\nLe " + dateJour +
                "\nSignature \n\n" +
                prenomHeb + " " + nomHeb;

            let doc = new jsPDF();
            doc.text(p, 10, 10, {
                maxWidth: 200
            });
            doc.save("attestationHebergement.pdf");
            document.getElementById("consigneAttestation").style.display = "block";
        }
    } else {
        msgAttesHeb.innerText = "Un ou plusieurs champs n'ont pas été remplis";
        msgAttesHeb.style.color = "red";
    }
});