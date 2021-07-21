$(document).ready(function () {
  var i = 1;
  //Modif mono
  $("#btnModifier").click(function (event) {
    if ($("#moniteur_password").val() !== $("#confirmMdp").val()) {
      event.preventDefault();
      $('[data-toggle="mdp"]').popover("show");
    }
  });

  $("input").focus(function () {
    $('[data-toggle="mdp"]').popover("dispose");
  });

  //Modif Indispo

  $("#btnIndispo").click(function (event) {
    start = new moment($("#indisponibilite_start").val());
    end = new moment($("#indisponibilite_end").val());

    if (end.isSameOrBefore(start)) {
      event.preventDefault();
      $('[data-toggle="errorDate"]').popover("show");
    }
  });

  $("input").focus(function () {
    $('[data-toggle="errorDate"]').popover("dispose");
  });

  //Ajout indispo
  $("#btnPlus").click(function () {
    $("#btnMoins").fadeIn("slow");
    i++;

    $("#indispos").append(
      "<div id='" +
        i +
        "' class='d-flex justify-content-center'><span>#" +
        i +
        "</span><div class='col-6'><label for='debut" +
        i +
        "'class='col-2 col-form-label'>Début</label><input class='form-control' type='datetime-local' id='debut" +
        i +
        "'></div><div class='col-6'><label for='fin" +
        i +
        "'class='col-2 col-form-label'>Fin</label><input class='form-control' type='datetime-local' id='fin" +
        i +
        "'></div></div>"
    );
  });

  $("#btnAddIndispo").click(function () {
    var erreur = false;
    var entry = document.querySelector("[data-moniteur]");
    var moniteurId = entry.dataset.moniteur;

    var index = 0;
    var listeIndispos = new Array();
    while (index != i) {
      index++;
      if ($("#debut" + index).val() < $("#fin" + index).val()) {
        listeIndispos.push({
          start: $("#debut" + index).val(),
          end: $("#fin" + index).val(),
        });
      } else {
        erreur = true;
      }
    }
    if (!erreur) {
      let url = `/admin/${moniteurId}/newIndispo`;
      let xhr = new XMLHttpRequest();

      xhr.onreadystatechange = function (e) {
        if (xhr.readyState === 4) {
          if (xhr.status === 200) {
            window.location.replace("/admin/" + moniteurId + "/indispo");
          }
        }
      };
      xhr.open("POST", url);
      xhr.send(JSON.stringify(listeIndispos));
    } else {
      listeIndispos = new Array();
      $('[data-toggle="errorDate"]').popover("show");
    }
  });

  //Enlever ligne indispo
  $("#btnMoins").click(function () {
    $("#" + i).remove();
    i--;
    if (i < 2) {
      $("#btnMoins").fadeOut("slow");
    }
  });
});
