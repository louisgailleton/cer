import { jsPDF } from "jspdf";
const doc = new jsPDF();

const baseUrl = "/";

//RECUPERATION DE TOUS LES EVENEMENTS A RENDRE
const entryInfoElements = document.querySelectorAll("[data-entry-info]");

const entryInfoObjects = Array.from(entryInfoElements).map((item) =>
  JSON.parse(item.dataset.entryInfo)
);

//RECUPERATION DE TOUTES LES INDISPOS
const entryIndispos = document.querySelectorAll("[data-indispos]");

const toutesIndispo = Array.from(entryIndispos).map((item) =>
  JSON.parse(item.dataset.indispos)
);

//RECUPERATION DE TOUS LES LIEUX
const entryLieux = document.querySelectorAll("[data-lieux]");

const toutLieux = Array.from(entryLieux).map((item) =>
  JSON.parse(item.dataset.lieux)
);

//RECUPERATION DE LA DATE D'EXAMEN
const entryExamen = document.querySelectorAll("[data-examen]");

const dateExamen = Array.from(entryExamen).map((item) =>
  JSON.parse(item.dataset.examen)
);
var dateExam = moment(dateExamen[0].date);

const entryAac = document.querySelectorAll("[data-aac]");

const Aac = Array.from(entryAac).map((item) => JSON.parse(item.dataset.aac));

const entryAuto = document.querySelectorAll("[data-auto]");

const auto = Array.from(entryAuto).map((item) => JSON.parse(item.dataset.auto));

//COULEUR DES EVENTS
if (auto[0]) {
  var CouleurFond = "#0f2645";
  var CouleurBordure = "#0f2645";
  var CouleurTexte = "#000000";
} else {
  entryInfoObjects.forEach((elements) => {
    elements.forEach((element) => {
      if (
        element["type"] == "rdv" ||
        element["type"] == "exam" ||
        element["type"] == "indispo"
      ) {
        element["display"] = "background";
      } else {
        if (element["type"] == "dispo") {
          element["display"] = "block";
        }
      }
    });
  });

  var CouleurFond;
  var CouleurBordure;
  var CouleurTexte;
}

//INITITATION DES TABLEAUX
var listeDispos = new Array();

//BOOLEEN DE DETECTION DES CHANGEMENTS
var modeDispo = !auto[0];
//DATE ACTUELLE EN MILLISECONDES
var ajd = Date.now();

//DIVERSES VARIABLES A UTILISER A PLUSIEURS ENDROITS
var debut;
var fin;
var removeIndex;
var sunday = 0;
var idIndispo;
var rdvRequestPending = null;
var getRequest = null;

var dateWeekD;
var dateWeekF;

$(document).ready(function () {
  setInterval(function () {
    $.ajax({
      url: `${baseUrl}eleve/getAuto`,
      type: "GET",
      dataType: "JSON",
      success: function (data) {
        if (data.auto != auto[0]) {
          auto[0] = data.auto;
          $("#interruptorPopover").popover("dispose");
          if (data.auto) {
            $("#interruptor").bootstrapToggle("enable");
            $("#interruptorPopover").popover({
              content: function () {
                return "Le site vient de passer en automatique, vous avez désormais accès à plus de fonctionnalités !";
              },
            });
            $("#interruptorPopover").popover("show");

            if ($("#interruptor").prop("checked")) {
              $modeDispo = true;

              CouleurFond = "#20a0ff";
              CouleurBordure = "#20a0ff";
              CouleurTexte = "#000000";
            } else {
              $modeDispo = false;
              CouleurFond = "#83DE7C";
              CouleurBordure = "#83DE7C";
              CouleurTexte = "#000000";
            }
          } else {
            $modeDispo = true;
            entryInfoObjects.forEach((elements) => {
              elements.forEach((element) => {
                if (
                  element["type"] == "rdv" ||
                  element["type"] == "exam" ||
                  element["type"] == "indispo"
                ) {
                  element["display"] = "background";
                } else {
                  if (element["type"] == "dispo") {
                    element["display"] = "block";
                  }
                }
              });
            });
            $("#interruptor").bootstrapToggle("on");
            $("#interruptor").bootstrapToggle("disable");
            $("#interruptorPopover").popover({
              content: function () {
                return "Le site vient de passer en manuel, votre accès vient donc d'être limité.";
              },
            });
            $("#interruptorPopover").popover("show");
          }
        }
      },
    });
  }, 20000);

  var el = $("body"),
    x = 300,
    originalColor = el.css("background");
  let calendarElt = document.querySelector("#calendar");
  let calendar = new FullCalendar.Calendar(calendarElt, {
    themeSystem: "bootstrap",
    nowIndicator: true,
    eventResizableFromStart: true,
    firstDay: 1,
    allDaySlot: false,
    slotMinTime: "07:00:00",
    slotMaxTime: "20:00:00",
    height: "auto",
    initialView: "timeGridWeek",
    locale: "fr",
    selectable: true,
    timeZone: "local",
    headerToolbar: {
      start: "prev,next",
      center: "title",
      end: "today",
    },
    editable: !auto[0],
    events: entryInfoObjects[0],
    buttonText: {
      today: "Aujourd'hui",
    },
    //EMPECHE DE SELECT DANS LE PASSE
    selectAllow: function (select) {
      return moment().diff(select.start) <= 0;
    },

    eventDidMount: function (data) {
      if (data.event.extendedProps.description) {
        $(data.el)
          .find(".fc-event-title")
          .append(
            "<div><span>" +
              data.event.extendedProps.description +
              "</span></div>"
          );
      }
      if (data.event.extendedProps.type == "exam") {
        $(data.el)
          .find(".fc-event-title")
          .append(
            "<div><span id='convocation' class='underline'>Convocation.pdf</span></div>"
          );
      }
      if (
        data.event.extendedProps.type == "rdv" ||
        data.event.extendedProps.type == "exam"
      ) {
        let contentTooltip = data.event.title;

        data.event.extendedProps.description
          ? (contentTooltip +=
              "<br>Moniteur: " + data.event.extendedProps.description)
          : "";
        $(data.el).tooltip({
          trigger: "hover",
          html: true,
          title: contentTooltip,
        });
      }
    },
    //LORS DU DEPLACEMENT ET/OU DE LA MODIFICTION D'UNE DISPO
    eventChange: function (event) {
      newDispo(
        event.event.id,
        moment(event.event.start).format("YYYY-MM-DDTHH:mm:ss"),
        moment(event.event.end).format("YYYY-MM-DDTHH:mm:ss")
      );
    },

    //PERMET DE SELECTIONNER UN CRENEAU HORAIRE PAR DESSUS UN AUTRE EVENEMENT SI ON POSE UNE DISPO
    selectOverlap: function (event) {
      if (!modeDispo) {
        return event.extendedProps.type == "dispo";
      } else {
        return (
          event.extendedProps.type == "indispo" ||
          event.extendedProps.type == "rdv" ||
          event.extendedProps.type == "exam"
        );
      }
    },

    eventAllow: function (dropInfo) {
      return moment().diff(dropInfo.start) <= 0;
    },
    //EMPECHE LES EVENEMENTS D'ETRE LES UN SUR LES AUTRES, SAUF POUR LES DISPOS
    eventOverlap: function (movingEvent) {
      return movingEvent.extendedProps.type !== "dispo";
    },
    //QUAND ON CLIQUE SUR UN EVENEMENT
    eventClick: function (info) {
      $(".tooltip").tooltip("hide");
      // ON GET LA VALEUR DE L'EVENEMENT CLIQUE
      var dateClick = info.event.start;
      //ON ACTUALISE LA VALEUR DE AJD A CHAQUE CLIQUE
      ajd = Date.now();

      if (
        dateClick >= ajd &&
        info.event.extendedProps.etat == "annule" &&
        auto[0]
      ) {
        if (
          confirm(
            "Voulez-vous re-confirmez ce rendez-vous ? (Aucune heure supplementaire ne sera déduite)"
          )
        ) {
          $.ajax({
            url: `${baseUrl}eleve/${info.event.id}/majRdv/${0}`,
            type: "PUT",
            success: function () {
              getAll();
            },
          });
        }
      } else {
        // SI ON CLIQUE SUR UNE DISPO OU UN RDV QUI NE SONT PAS EN ARRIERE PLAN
        // ET
        // QUE L'ETAT DE L'EVENEMENT CLIQUE N'EST PAS ANNULE
        // ET
        // QUE LA DATE DU CLIQUE N'EST PAS DANS LE PASSE
        if (
          (info.event.extendedProps.type == "dispo" &&
            info.event.display != "background") ||
          (info.event.extendedProps.type == "rdv" &&
            info.event.display != "background" &&
            auto[0] &&
            info.event.extendedProps.etat != "annule" &&
            dateClick >= ajd)
        ) {
          //SI L'EVENEMENT A UN ID
          if (info.event.id !== "") {
            //SI C'EST UNE DISPO
            if (info.event.extendedProps.type == "dispo") {
              info.event.remove();
              deleteDispo(info.event.id);
            }
            //SI AUTRE QUE DISPO (RDV)
            else {
              //RECUPERE INDISPO LIE A CE RDV
              toutesIndispo[0].forEach((element) => {
                element.start = Date.parse(element.start);
                element.end = Date.parse(element.end);
                let dateStart = Date.parse(info.event.start);
                let dateEnd = Date.parse(info.event.end);

                if (
                  info.event.extendedProps.moniteur_id == element.moniteur_id &&
                  dateStart == element.start &&
                  element.moniteur_id &&
                  dateEnd == element.end
                ) {
                  idIndispo = element.id;
                }
              });
              // ON VERIFIE QUE DIMANCHE NE SE TROUVE PAS ENTRE AJD ET LA DATE CLIQUE
              if (info.event.start.getDay() != 0) {
                for (
                  var i = new Date(ajd);
                  i <= info.event.start;
                  i.setDate(i.getDate() + 1)
                ) {
                  // SI Y'A UN DIMANCHE, ON DEFINIT LA VALEUR DE SUNDAY A L'EQUIVALENT DE 24H EN MS
                  if (i.getDay() == 0) {
                    sunday = 86400000;
                    break;
                  } else {
                    sunday = 0;
                  }
                }
              }
              // SI LA DATE CLIQUE - 48H (- 24H si il y a un dimanche) EST AVANT AJD
              if (Date.parse(info.event.start) - (172800000 - sunday) <= ajd) {
                sunday = 0;

                // SI L'ELEVE CONFIRME VOULOIR SUPPRIMER
                if (
                  confirm(
                    "Etes-vous sûr de vouloir supprimer ce rendez-vous ? (Vous n'allez pas être recréditer de vos heures)"
                  )
                ) {
                  //ON PASSE LE RDV EN ROUGE
                  $.ajax({
                    url: `${baseUrl}eleve/${info.event.id}/majRdv/${1}`,
                    type: "PUT",
                    success: function () {
                      getAll();
                    },
                  });

                  // ON LE PASSE EN ROUGE SUR L'INTERFACE AUSSI AVANT LE RELOAD
                  entryInfoObjects[0].forEach((element) => {
                    if (element.id == info.event.id) {
                      element.backgroundColor = "#ff0000";
                      element.borderColor = "#ff0000";
                    }
                  });

                  toutesIndispo[0].forEach((element) => {
                    element.start = Date.parse(element.start);
                    element.end = Date.parse(element.end);
                    dateStart = Date.parse(info.event.start);
                    dateEnd = Date.parse(info.event.end);

                    if (
                      info.event.extendedProps.moniteur_id ==
                        element.moniteur_id &&
                      dateStart == element.start &&
                      element.moniteur_id &&
                      dateEnd == element.end
                    ) {
                      idIndispo = element.id;
                    }
                  });
                  $.ajax({
                    type: "DELETE",
                    data: { id: idIndispo },
                    url: `${baseUrl}eleve/${idIndispo}/deleteIndispo`,
                  });
                }
              }
              // SI IL Y A PLUS DE 48H
              else {
                sunday = 0;
                // ET QUE L'ELEVE CONFIRME
                if (
                  confirm(
                    "Etes-vous sur de vouloir supprimer ce rendez-vous ? (Vous allez etre recrediter de vos heures)"
                  )
                ) {
                  //ON SUPPRIME LE RDV
                  $.ajax({
                    type: "DELETE",
                    data: { id: info.event.id },
                    url: `${baseUrl}eleve/${info.event.id}/deleteRdv`,
                  });

                  //ON SUPPRIME L'INDISPO LIE
                  $.ajax({
                    type: "DELETE",
                    data: { id: idIndispo },
                    url: `${baseUrl}eleve/${idIndispo}/deleteIndispo`,
                  });

                  $.ajax({
                    url: `${baseUrl}eleve/heure`,
                    type: "PUT",
                    data: {
                      incremente: true,
                      nbHeure: diff_hours(info.event.end, info.event.start),
                    },
                    success: function () {
                      getAll();
                      getCompteur();
                    },
                  });
                  // ON LES SUPPRIME AUSSI DANS L'INTERFACE
                  removeIndex = entryInfoObjects[0]
                    .map(function (item) {
                      return item.id.toString();
                    })
                    .indexOf(idIndispo.toString());
                  entryInfoObjects[0].splice(removeIndex, 1);

                  removeIndex = entryInfoObjects[0]
                    .map(function (item) {
                      return item.id.toString();
                    })
                    .indexOf(info.event.id.toString());
                  entryInfoObjects[0].splice(removeIndex, 1);
                  //ANIMATION BOUTON ET RERENDER EVENT
                  init();
                  //RELOAD LA PAGE POUR RECUPERER LES NOUVELLES INFOS EN BDD
                  getAll();
                }
              }
            }
          } else {
            if (info.event.extendedProps.type == "dispo") {
              //ON SUPPRIME LA DISPO DANS L'INTERFACE
              info.event.remove();
            }
          }
        }
      }
    },

    // LORS DE LA SELECTION D'UNE PLAGE HORAIRE
    select: function (e) {
      $('[data-toggle="submitButton"]').popover("dispose");
      $("#interruptorPopover").popover("dispose");
      // SI MODEDISPO
      if (modeDispo) {
        newDispo(
          null,
          moment(e.start).format("YYYY-MM-DDTHH:mm:ss"),
          moment(e.end).format("YYYY-MM-DDTHH:mm:ss")
        );
      } else {
        if ($("input:checkbox:checked").length > 0) {
          if (diff_hours(e.end, e.start) > 0.5) {
            let jourDebut = new moment(e.start);
            let jourFin = new moment(e.end);
            if (
              Aac[0] ||
              jourDebut.date() < 20 ||
              jourDebut.date() > 26 ||
              dateExam.isAfter(jourDebut)
            ) {
              if (Aac[0] && getNombreHeureSemaine(e.start) >= 6) {
                $("#interruptorPopover").popover({
                  content: function () {
                    var message =
                      "Vous avez deja pris vos 6 heures cette semaine.";
                    return message;
                  },
                });

                $("#interruptorPopover").popover("show");
                return;
              } else {
                if (getNombreHeureSemaine(e.start) >= 2) {
                  $("#interruptorPopover").popover({
                    content: function () {
                      var message =
                        "Vous avez deja pris vos 2 heures cette semaine.";
                      return message;
                    },
                  });

                  $("#interruptorPopover").popover("show");
                  return;
                }
              }
              if (
                jourDebut.isBefore(jourFin) &&
                jourDebut.day() == jourFin.day()
              ) {
                //reinit options lieux
                $("#divLieu").hide();

                var select = document.getElementById("moniteur");
                var length = select.options.length;
                for (let i = length - 1; i >= 1; i--) {
                  select.options[i] = null;
                }
                $("#moniteur").val("default");
                $("#lieu").val("default");

                var moniteursId = new Array();
                $("input:checkbox:checked").each(function () {
                  moniteursId.push($(this).val());
                });

                moniteursId.forEach((elements) => {
                  $("#moniteur").append(
                    $("<option>", {
                      value: elements,
                      text: $("#" + elements)
                        .next("td label")
                        .html(),
                    })
                  );
                });

                // SI PAS EN MODE DISPO (DONC RDV PAR DEFAUT)
                // ON AFFICHE LE FORMULAIRE DANS UNE POP UP
                $("#rdvModal").modal("show");
                let dateRdv = new moment(e.start);
                dateRdv = dateRdv.format("DD/MM/YYYY");

                let heureDebutRdv = new moment(e.start);
                heureDebutRdv = heureDebutRdv.format("HH:mm");

                let heureFinRdv = new moment(e.end);
                heureFinRdv = heureFinRdv.format("HH:mm");

                $("#debutRdv").html(heureDebutRdv);
                $("#finRdv").html(heureFinRdv);
                $("#dateRdv").html(dateRdv);
              } else {
                alert(
                  "Vous ne pouvez pas prendre un rendez-vous sur plusieurs jours"
                );
                calendar.unselect();
              }
            } else {
              $("#interruptorPopover").popover({
                content: function () {
                  var message =
                    "Vous n'êtes pas autorisé à prendre rendez-vous entre le 20 et le 26.";
                  return message;
                },
              });

              $("#interruptorPopover").popover("show");
            }
          }
        } else {
          $("#moniteurPopover").popover({
            content: function () {
              return "Sélectionnez d'abord au moins un moniteur.";
            },
          });
          $("#moniteurPopover").popover("show");
        }
      }
      // ON STOCKE LES VALEURS DANS DES VARIABLES
      debut = e.start;
      fin = e.end;
    },
  });

  // QUAND IL ENVOIE UN RDV
  $("#submitButton").click(function () {
    let nbHeureEnCours = diff_hours(fin, debut);
    // ON RECUP LE LIEU
    var lieuId = $("#lieu").val();
    var moniteurId = $("#moniteur").val();
    if (lieuId && moniteurId) {
      if (Aac[0] && getNombreHeureSemaine(debut) + nbHeureEnCours > 6) {
        $('[data-toggle="submitButton"]').popover({
          content: function () {
            return "Vous ne pouvez pas mettre plus de 6 heures sur la meme semaine";
          },
        });
        $('[data-toggle="submitButton"]').popover("show");
        return;
      } else if (getNombreHeureSemaine(debut) + nbHeureEnCours > 2) {
        $('[data-toggle="submitButton"]').popover({
          content: function () {
            return "Vous ne pouvez pas mettre plus de 2 heures sur la meme semaine";
          },
        });
        $('[data-toggle="submitButton"]').popover("show");
        return;
      }
      $.ajax({
        url: `${baseUrl}eleve/getCompteur`,
        type: "GET",
        success: function (data) {
          let compteur = data.compteur[0].compteurHeure;
          if (compteur - nbHeureEnCours >= 0) {
            if (rdvRequestPending == null) {
              rdvRequestPending = $.ajax({
                url: `${baseUrl}eleve/newRdv`,
                type: "POST",
                data: {
                  moniteur: parseInt(moniteurId),
                  start: moment(debut).format("YYYY-MM-DDTHH:mm:ss"),
                  end: moment(fin).format("YYYY-MM-DDTHH:mm:ss"),
                  lieu: parseInt(lieuId),
                },
                success: function () {
                  rdvRequestPending = null;
                  getAll();

                  let debutPlageHoraire = new moment($("#heuresStart").val())
                    .set("hour", 7)
                    .format("YYYY-MM-DDTHH:mm:ss");

                  let finPlageHoraire = new moment($("#heuresEnd").val())
                    .set("hour", 20)
                    .format("YYYY-MM-DDTHH:mm:ss");
                  getHeureCommune(debutPlageHoraire, finPlageHoraire);
                  $.ajax({
                    url: `${baseUrl}eleve/heure`,
                    type: "PUT",
                    data: {
                      incremente: false,
                      nbHeure: nbHeureEnCours,
                    },
                    success: function () {
                      getCompteur();
                    },
                  });
                  el.css({
                    transition: "background 0.2s ease-in-out",
                    background: "#27a844",
                  });
                  setTimeout(function () {
                    el.css({
                      transition: "background 0.2s ease-in-out",
                      background: originalColor,
                    });
                  }, x);
                },
              });
            }

            $("#rdvModal").modal("hide");
            init();
          } else {
            alert("Vous n'avez plus assez d'heure sur votre compte.");
          }
          // SINON ON LE NOTIFIE QU'IL N'A PAS CHOISI DE LIEU
        },
      });
    }
  });

  function minTommss(minutes) {
    var sign = minutes < 0 ? "-" : "";
    var min = Math.floor(Math.abs(minutes));
    var sec = Math.floor((Math.abs(minutes) * 60) % 60);
    return (
      sign + (min < 10 ? "0" : "") + min + ":" + (sec < 10 ? "0" : "") + sec
    );
  }

  $("#duplique").click(function () {
    CouleurFond = "#20a0ff";
    CouleurBordure = "#20a0ff";
    CouleurTexte = "#000000";

    let OneWeekLater = new Date();
    OneWeekLater.setDate(OneWeekLater.getDate() + 7);

    if (
      confirm(
        "Etes-vous sûr de vouloir dupliquer vos disponibilités à la semaine suivante ?"
      )
    ) {
      entryInfoObjects[0].forEach((elements) => {
        if (elements.type == "dispo") {
          let dateDispo = Date.parse(elements.start);
          if (dateDispo >= dateWeekD && dateDispo <= dateWeekF) {
            elements.start = new Date(elements.start);
            elements.start.setDate(elements.start.getDate() + 7);

            elements.end = new Date(elements.end);
            elements.end.setDate(elements.end.getDate() + 7);

            listeDispos.push({
              title: "Dispo",
              start: moment(elements.start).format("YYYY-MM-DDTHH:mm:ss"),
              end: moment(elements.end).format("YYYY-MM-DDTHH:mm:ss"),
              type: "dispo",
            });
          }
        }
      });
      if (listeDispos.length != 0) {
        $.ajax({
          url: `${baseUrl}eleve/dupliqueDispo`,
          type: "POST",
          data: {
            listeDispos: listeDispos,
          },
          success: function () {
            getAll();
            listeDispos = new Array();

            let debutPlageHoraire = new moment($("#heuresStart").val())
              .set("hour", 7)
              .format("YYYY-MM-DDTHH:mm:ss");

            let finPlageHoraire = new moment($("#heuresEnd").val())
              .set("hour", 20)
              .format("YYYY-MM-DDTHH:mm:ss");
            getHeureCommune(debutPlageHoraire, finPlageHoraire);
          },
        });
      }
      el.css({
        transition: "background 0.2s ease-in-out",
        background: "#17a2b7",
      });
      setTimeout(function () {
        el.css({
          transition: "background 0.2s ease-in-out",
          background: originalColor,
        });
      }, x);
    }
  });

  $("#interruptor").on("change", function () {
    if ($(this).prop("checked")) {
      modeDispo = true;
      $("#modeSentence").html("Sélectionnez vos dispos sur le planning.");

      el.css({
        transition: "background 0.2s ease-in-out",
        background: "transparent",
      });
      setTimeout(function () {
        el.css({
          transition: "background 0.2s ease-in-out",
          background: originalColor,
        });
      }, x);
      $(".fc-highlight").css("background", "#20a0ff");
      CouleurFond = "#20a0ff";
      CouleurBordure = "#20a0ff";
      CouleurTexte = "#000000";

      calendar.setOption("selectable", "true");
      calendar.setOption("editable", "true");

      entryInfoObjects.forEach((elements) => {
        elements.forEach((element) => {
          if (
            element["type"] == "rdv" ||
            element["type"] == "indispo" ||
            element["type"] == "exam"
          ) {
            element["display"] = "background";
          } else {
            if (element["type"] == "dispo") {
              element["display"] = "block";
            }
          }
        });
      });
      calendar.setOption("events");
      calendar.setOption("events", entryInfoObjects[0]);
    } else {
      init();
    }
  });

  $("#btnPlanning").click(function () {
    init();

    el.css({
      transition: "background 0.2s ease-in-out",
      background: "#0f2645",
    });
    setTimeout(function () {
      el.css({
        transition: "background 0.2s ease-in-out",
        background: originalColor,
      });
    }, x);
  });
  // ON REMET A L'ETAT INITIAL EN RE ACTUALISANT LES EVENEMENTS (SANS BDD ICI)
  function init() {
    if (auto[0]) {
      $("#modeSentence").html("Posez vos heures de conduite sur le planning.");
      calendar.setOption("editable", false);
      modeDispo = false;
      if ($("#interruptor").prop("checked")) {
        $("#interruptor").bootstrapToggle("off");
      }

      entryInfoObjects.forEach((elements) => {
        elements.forEach((element) => {
          if (element["type"] == "rdv" || element["type"] == "exam") {
            element["display"] = "block";
          } else {
            if (element["type"] == "dispo" || element["type"] == "indispo") {
              element["display"] = "background";
            }
          }
        });
      });
    }

    listeDispos = new Array();

    calendar.getEvents().forEach((element) => {
      element.remove();
    });
    calendar.setOption("events"); //TOUT SUPPRIMER
    calendar.setOption("events", entryInfoObjects[0]);

    calendar.render();
  }

  calendar.render();
  verifDispo();
  indispoFusion();

  $(".fc-prev-button").click(function () {
    verifDispo();
  });

  $(".fc-next-button").click(function () {
    verifDispo();
  });

  function verifDispo() {
    var dispoAffiche = false;

    dateWeekD = calendar.getDate();
    dateWeekD.setHours(1);

    dateWeekF = calendar.getDate();
    dateWeekF = dateWeekF.setDate(dateWeekF.getDate() + 6);
    dateWeekF = dateWeekF + 82800000;

    ajd = Date.now();

    entryInfoObjects[0].forEach((elements) => {
      if (elements.type == "dispo") {
        let dateDispo = Date.parse(elements.start);
        let dateAutorise = Date.now();
        dateAutorise -= 604800000;
        if (
          dateDispo >= dateWeekD &&
          dateDispo <= dateWeekF &&
          dateDispo >= dateAutorise
        ) {
          dispoAffiche = true;
        }
      }
    });
    if (!dispoAffiche) {
      $("#duplique").attr("disabled", "disabled");
    } else {
      $("#duplique").attr("disabled", false);
    }
  }

  $(".form-check-input").click(function () {
    $("#moniteurPopover").popover("dispose");
    indispoFusion();
  });

  function indispoFusion() {
    $("#interruptorPopover").popover("dispose");
    var moniteursId = new Array();
    $("input:checkbox:checked").each(function () {
      moniteursId.push($(this).val());
    });
    entryInfoObjects[0] = entryInfoObjects[0].filter(function (el) {
      return el.type != "indispo";
    });

    if (moniteursId.length == 0 && !modeDispo) {
      init();
    } else {
      if (moniteursId.length == 1) {
        toutesIndispo[0].forEach((elements) => {
          moniteursId.forEach((element) => {
            if (element == elements.moniteur_id) {
              entryInfoObjects[0].push({
                moniteur_id: element,
                start: elements.start,
                end: elements.end,
                color: "grey",
                display: "background",
                type: "indispo",
              });
            }
          });
        });
      } else {
        let tabDispo = new Array();
        moniteursId.forEach((element) => {
          toutesIndispo[0].forEach((elements) => {
            if (element == elements.moniteur_id) {
              tabDispo.push({
                moniteur_id: element,
                start: elements.start,
                end: elements.end,
                color: "grey",
                display: "background",
                type: "indispo",
              });
            }
          });
        });

        tabDispo.forEach((element) => {
          let limite = moment(element.end);
          let debutP = moment(element.start);
          let finP = moment(element.start);
          debutP.subtract(30, "minutes");
          while (!debutP.isSame(limite) && finP.isBefore(limite)) {
            debutP.add(30, "minutes");
            finP.add(30, "minutes");
            let i = 0;
            moniteursId.forEach((id) => {
              tabDispo.forEach((elements) => {
                if (elements.moniteur_id == id) {
                  if (
                    debutP >= moment(elements.start) &&
                    finP <= moment(elements.end)
                  ) {
                    i++;
                  }
                }
              });
            });
            if (i == moniteursId.length) {
              entryInfoObjects[0].push({
                moniteur_id: element,
                start: debutP.format("YYYY-MM-DD HH:mm:ss"),
                end: finP.format("YYYY-MM-DD HH:mm:ss"),
                color: "grey",
                display: "background",
                type: "indispo",
              });
            }
          }
        });
      }
      calendar.setOption("events"); //TOUT SUPPRIMER
      calendar.setOption("events", entryInfoObjects[0]);
      calendar.render();
    }
  }

  $("#moniteur").change(function () {
    var select = document.getElementById("lieu");
    var length = select.options.length;
    for (let i = length - 1; i >= 0; i--) {
      select.options[i] = null;
    }

    toutLieux[0].forEach((element) => {
      if (element.moniteur_id == parseInt($("#moniteur").val())) {
        $("#lieu").append(
          $("<option>", {
            value: element.id,
            text: element.libelle,
          })
        );
      } else if (element.moniteur_id == null) {
        $("#lieu").append(
          $("<option>", {
            value: element.id,
            text: element.libelle,
          })
        );
        $("#lieu").val(element.id);
      }
    });

    $("#divLieu").show();
  });

  $(".fc-dayGridMonth-button").click(function () {
    $(".fc-event-time").hide();
  });

  function getAll() {
    getRequest = $.ajax({
      url: `${baseUrl}eleve/getAll/${
        $("#interruptor").prop("checked") ? 0 : 1
      }`,
      type: "GET",
      dataType: "JSON",

      success: function (data) {
        entryInfoObjects[0] = data.eventEleve;
        console.log(entryInfoObjects[0]);
        toutesIndispo[0] = data.indispos;
        renderEvent(calendar, data.eventEleve);
        verifDispo();
        indispoFusion();
      },
      beforeSend: function () {
        if (getRequest != null) {
          getRequest.abort();
        }
      },
    });
  }

  function getCompteur() {
    $.ajax({
      url: `${baseUrl}eleve/getCompteur`,
      type: "GET",
      success: function (data) {
        let compteur = data.compteur[0].compteurHeure;
        $("#compteur").html(minTommss(compteur) + "h");
      },
    });
  }

  function getHeureCommune(debutPlageHoraire, finPlageHoraire) {
    $.ajax({
      url: `${baseUrl}eleve/getHeureCommune/`,
      type: "GET",
      dataType: "JSON",
      data: {
        debutPlageHoraire: debutPlageHoraire,
        finPlageHoraire: finPlageHoraire,
      },
      success: function (data) {
        let i = 0;
        data.forEach((element) => {
          i++;
          $("#nom_" + i).html(element.prenom + " " + element.nom.toUpperCase());
          $("#heure_" + i).html(element.nbHeureCommun);
        });
      },
    });
  }
  function renderEvent(calendar, events) {
    calendar.setOption("events");
    calendar.setOption("events", events);
  }

  function diff_hours(dt2, dt1) {
    var diff = (dt2.getTime() - dt1.getTime()) / 1000;
    diff /= 60 * 60;
    return Math.abs(diff);
  }
  Date.prototype.toJSON = function () {
    return new moment(this).format("YYYY-MM-DDTHH:mm:ss");
  };

  $("caption").click(function () {
    $("#heureModal").modal("show");
  });

  $("#submitButtonHeure").click(function () {
    if ($("#heuresStart").val() && $("#heuresEnd").val()) {
      let dateDebut = new moment($("#heuresStart").val()).set("hour", 7);
      let dateFin = new moment($("#heuresEnd").val()).set("hour", 20);
      if (dateDebut.isSameOrBefore(dateFin)) {
        $("#debut").html(dateDebut.format("DD/MM/YYYY"));
        $("#fin").html(dateFin.format("DD/MM/YYYY"));
        getHeureCommune(
          dateDebut.format("YYYY-MM-DDTHH:mm:ss"),
          dateFin.format("YYYY-MM-DDTHH:mm:ss")
        );
        $("#heureModal").modal("hide");
      } else {
        $('[data-toggle="submitButtonHeure"]').popover("show");
      }
    } else {
      $('[data-toggle="submitButtonHeure"]').popover("show");
    }
  });
  $("#heuresStart").change(function () {
    $('[data-toggle="submitButtonHeure"]').popover("dispose");
  });
  $("#heuresEnd").change(function () {
    $('[data-toggle="submitButtonHeure"]').popover("dispose");
  });

  function deleteDispo(id) {
    $.ajax({
      type: "DELETE",
      data: { id: id },
      url: `${baseUrl}eleve/deleteDispo/${id}`,
    });
  }

  function newDispo(id, start, end) {
    $.ajax({
      url: `${baseUrl}eleve/newDispo`,
      type: "PUT",
      data: {
        id: id,
        start: start,
        end: end,
      },
      success: function () {
        getAll();

        let debutPlageHoraire = new moment($("#heuresStart").val())
          .set("hour", 7)
          .format("YYYY-MM-DDTHH:mm:ss");

        let finPlageHoraire = new moment($("#heuresEnd").val())
          .set("hour", 20)
          .format("YYYY-MM-DDTHH:mm:ss");
        getHeureCommune(debutPlageHoraire, finPlageHoraire);
      },
    });
  }

  function getNombreHeureSemaine(date) {
    date = moment(date);
    date.isoWeekday(1);

    let debut = moment(date);
    let fin = moment(date);

    let debutWeek = debut.startOf("isoweek");
    let finWeek = fin.endOf("isoweek");

    var nbHeure = 0;
    entryInfoObjects[0].forEach((elements) => {
      if (elements.type == "rdv" && elements.etat != "annule") {
        let debutRdvDate = moment(elements.start);
        let finRdvDate = moment(elements.end);

        if (
          debutRdvDate.isSameOrAfter(debutWeek) &&
          debutRdvDate.isSameOrBefore(finWeek)
        ) {
          nbHeure += finRdvDate.diff(debutRdvDate, "hours");
        }
      }
    });
    return nbHeure;
  }

  $("#convocation").on("click", function () {
    $.ajax({
      url: `${baseUrl}eleve/convocation/`,
      type: "GET",
      dataType: "JSON",
      success: function (data) {
        doc.text("CONVOCATION A L'EXAMEN DE CONDUITE", 45, 15);
        doc.text("Bonjour " + data.prenom + ",", 20, 45);
        doc.text(
          "Vous avez été programmé à l'examen de conduite du " +
            data.date +
            " sur le \ncircuit de " +
            data.lieuExamen +
            ".",
          20,
          65
        );
        doc.text(
          "Veuillez vous présenter à l'agence " +
            data.agence +
            " le " +
            data.date +
            ",\nà " +
            data.heureExamen +
            " muni d'une pièce d'identité en cours de validité.",
          20,
          85
        );
        doc.text("Merci", 20, 130);
        doc.text("La Direction", 20, 140);

        doc.save("convocation.pdf");
      },
    });
  });
});
