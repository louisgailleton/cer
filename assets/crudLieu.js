$(document).ready(function () {
  var i = 1;

  //Modif Indispo

  $("#btnLieu").click(function (event) {
    lieu = $("#lieu_libelle").val();

    if (!lieu.trim()) {
      event.preventDefault();
      $('[data-toggle="errorLieu"]').popover("show");
    }
  });

  $("input").focus(function () {
    $('[data-toggle="errorLieu"]').popover("dispose");
  });

  //Ajout lieu
  $("#btnPlus").click(function () {
    $("#btnMoins").fadeIn("slow");
    i++;

    $("#lieux").append(
      '<div id="' +
        i +
        '" class="d-flex justify-content-center"><span>#' +
        i +
        '</span><div class="col-12"><label for="lieu' +
        i +
        '" class="col-2 col-form-label">Lieu</label><input id="lieu' +
        i +
        '" class="form-control" required="required" type="text" placeholder="Entrez un lieu de rendez-vous"></div></div>'
    );
  });

  $("#btnAddLieu").click(function () {

    var erreur = false;
    var entry = document.querySelector("[data-moniteur]");
    var moniteurId = entry.dataset.moniteur;

    var index = 0;
    var listeLieux = new Array();
    while (index != i) {
      index++;
      lieu = $("#lieu" + index).val();
      if (lieu.trim()) {
        listeLieux.push({
          lieu: $("#lieu" + index).val(),
        });
      } else {
        erreur = true;
      }
    }
    if (!erreur) {
      let url = `/admin/${moniteurId}/newLieu`;
      let xhr = new XMLHttpRequest();

      // INCROYAAABLE MEC CA ATTEND LA MISE EN BDD AVANT DE REFRESH
      xhr.onreadystatechange = function (e) {
        if (xhr.readyState === 4) {
          if (xhr.status === 200) {
            window.location.replace("/admin/" + moniteurId + "/lieu");
          }
        }
      };
      xhr.open("POST", url);
      xhr.send(JSON.stringify(listeLieux));
    } else {
      listeLieux = new Array();
      $('[data-toggle="errorLieu"]').popover("show");
    }
  });

  //Enlever ligne indispo
  $("#btnMoins").click(function () {
    $("#" + i).remove();
    i--;
    if (i < 2) {
      $("#btnMoins").hide();
    }
  });
});
