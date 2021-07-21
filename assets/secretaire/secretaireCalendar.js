var eventsEleve = new Array();
var currentRequest = null;
var getRequest = null;
var eleveId;
var lieuId;
var moniteurId;
var start;
var end;
var color = "";
var moniteurNom = "";
var tableDispoNoFilter;
var personneSelected = false;
var searchRequest = null;
var checkRequest = null;
var otherRequestPending = null;
var evenementEnCours;
var listeIndispoTypes = new Array();
const entryTout = document.querySelectorAll("[data-tout]");

const entryInfoTout = Array.from(entryTout).map((item) =>
  JSON.parse(item.dataset.tout)
);

const entryMoniteurs = document.querySelectorAll("[data-moniteurs]");

const entryInfoMoniteurs = Array.from(entryMoniteurs).map((item) =>
  JSON.parse(item.dataset.moniteurs)
);
const entryTotalsIndispo = document.querySelectorAll("[data-total-indispo]");

const totalIndispo = Array.from(entryTotalsIndispo).map((item) =>
  JSON.parse(item.dataset.totalIndispo)
);

const baseUrl = "/";

var sunday = 0;

$(document).ready(function () {
  $.ajax({
    url: `${baseUrl}secretaire/getLieux`,
    type: "GET",
    dataType: "JSON",
    data: { type: "exam" },
    success: function (data) {
      data.lieux.forEach((element) => {
        $("#lieuExam").append(
          $("<option>", {
            value: element.id,
            text: element.libelle,
          })
        );
      });
    },
  });
  setInterval(function () {
    if (otherRequestPending == null && checkRequest == null) {
      checkRequest = $.ajax({
        url: `${baseUrl}secretaire/getIndispo`,
        type: "GET",
        dataType: "JSON",
        success: function (data) {
          checkRequest = null;
          otherRequestPending = null;
          if (data != parseInt(totalIndispo[0])) {
            $("#message").fadeIn();
            $("#message").html(
              "Le planning n'est plus à jour. <br> (Cliquez pour mettre le planning à jour)"
            );
          } else {
            totalIndispo[0] = data;
            $("#message").fadeOut();
          }
        },
      });
    }
  }, 20000);
  let calendarElt = document.querySelector("#calendar");
  var calendar = new FullCalendar.Calendar(calendarElt, {
    themeSystem: "bootstrap",
    selectable: true,
    nowIndicator: true,
    navLinks: true,
    firstDay: 1,
    allDaySlot: false,
    slotMinTime: "07:00:00",
    slotMaxTime: "20:00:00",
    height: "auto",
    initialView: "resourceCustomMondayView",
    datesAboveResources: true,
    locale: "fr",
    timeZone: "local",
    editable: true,
    eventResizableFromStart: true,
    headerToolbar: {
      start: "prev,next today",
      center: "title",
      end: "resourceCustomMondayView,resourceTimeGridDay",
    },
    buttonText: {
      today: "Aujourd'hui",
      resourceTimeGridDay: "Jour",
      resourceCustomMondayView: "Planning",
    },
    views: {
      resourceCustomMondayView: {
        type: "resourceTimeGrid",
        duration: { weeks: 1 },
      },

      resourceTimeGridDay: {
        titleFormat: { year: "numeric", month: "long", day: "2-digit" },
      },
    },

    dayMinWidth: 40,
    resources: entryInfoMoniteurs[0],
    resourceOrder: "title",
    events: entryInfoTout[0],

    eventDidMount: function (data) {
      if (data.event.extendedProps.type == "rdv") {
        if (data.event.extendedProps.lieu) {
          $(data.el)
            .find(".fc-event-title")
            .append(
              "<div><span>" + data.event.extendedProps.lieu + "</span></div>"
            );
        }
      } else if (data.event.extendedProps.type == "exam") {
        $(data.el)
          .find(".fc-event-title")
          .append(
            "<div><span>" +
              data.event.extendedProps.nbPlace +
              " places</span><div><span>Numéro " +
              data.event.extendedProps.numero +
              "</span></div></div>"
          );
      }

      if (
        data.event.extendedProps.type == "rdv" ||
        data.event.extendedProps.type == "exam" ||
        data.event.extendedProps.type == "absence"
      ) {
        let contentTooltip =
          moment(data.event.start).format("H:mm") +
          "h - " +
          moment(data.event.end).format("H:mm") +
          "h<br>";
        if (data.event.extendedProps.type == "exam") {
          contentTooltip +=
            "Lieu: " +
            data.event.extendedProps.lieu +
            "<br>Nb Place: " +
            data.event.extendedProps.nbPlace +
            "<br>Numéro: " +
            data.event.extendedProps.numero;
        } else {
          contentTooltip += "Eleve: " + data.event.title;

          data.event.extendedProps.lieu != ""
            ? (contentTooltip += "<br>Lieu: " + data.event.extendedProps.lieu)
            : "";
        }
        $(data.el).tooltip({
          trigger: "hover",
          html: true,
          title: contentTooltip,
        });
      }
    },

    resourceLabelDidMount: function (el) {
      $(el.el).css("cursor", "pointer");
      $(el.el).on("click", function () {
        let nomSemaineType;
        entryInfoMoniteurs[0].forEach((element) => {
          if (element.id == $(el.el).attr("data-resource-id")) {
            nomSemaineType = element.username;
            idSemaineType = element.id;
          }
        });
        getIndispoType($(el.el).attr("data-resource-id"));

        $("#semaineTypeModal").modal("show");

        $("#semaineTypeTitle").html(
          "Établissez la semaine type de " + nomSemaineType
        );
      });

      $(el.el).on("mouseenter", function () {
        $(el.el).css("color", "#007aff");
      });

      $(el.el).on("mouseleave", function () {
        $(el.el).css("color", "#0F2645");
      });
    },
    selectOverlap: function (event) {
      if ($("#radioDispo").is(":checked")) {
        return (
          event.extendedProps.type == "indispo" ||
          event.extendedProps.type == "rdv" ||
          event.extendedProps.type == "dispo" ||
          event.extendedProps.type == "preExam"
        );
      } else if ($("#radioResa").is(":checked")) {
        return (
          event.extendedProps.type == "preExam" ||
          event.extendedProps.type == "dispo"
        );
      }

      if (personneSelected) {
        return event.extendedProps.type == "dispo";
      }
    },

    eventDrop: function (changeInfo) {
      $(".tooltip").tooltip("hide");
      resources = changeInfo.event.getResources();
      moniteurId = resources[0].id;
      if (changeInfo.event.extendedProps.type == "exam") {
        newExam(
          changeInfo.event.id,
          moment(changeInfo.event.start).format("YYYY-MM-DD HH:mm:ss"),
          moment(changeInfo.event.end).format("YYYY-MM-DD HH:mm:ss"),
          changeInfo.event.extendedProps.lieu_id,
          changeInfo.event.extendedProps.numero,
          changeInfo.event.extendedProps.nbPlace,
          moniteurId
        );
      } else if (changeInfo.event.extendedProps.type == "rdv") {
        {
          newRdv(
            changeInfo.event.id,
            changeInfo.event.extendedProps.eleve_id,
            moniteurId,
            moment(changeInfo.event.start).format("YYYY-MM-DDTHH:mm:ss"),
            moment(changeInfo.event.end).format("YYYY-MM-DDTHH:mm:ss"),
            changeInfo.event.extendedProps.lieu_id
          );
        }
      } else if (
        changeInfo.event.extendedProps.type == "indispo" ||
        changeInfo.event.extendedProps.type == "preExam"
      ) {
        newIndispo(
          changeInfo.event.id,
          moniteurId,
          moment(changeInfo.event.start).format("YYYY-MM-DDTHH:mm:ss"),
          moment(changeInfo.event.end).format("YYYY-MM-DDTHH:mm:ss")
        );
      }
    },
    eventResize: function (eventResizeInfo) {
      resources = eventResizeInfo.event.getResources();
      moniteurId = resources[0].id;
      $(".tooltip").tooltip("hide");
      if (eventResizeInfo.event.extendedProps.type == "exam") {
        eventResizeInfo.revert();
      } else if (eventResizeInfo.event.extendedProps.type == "rdv") {
        if (
          eventResizeInfo.event.extendedProps.compteur -
            diff_hours(
              eventResizeInfo.event.end,
              eventResizeInfo.event.start
            ) >=
          0
        ) {
          newRdv(
            eventResizeInfo.event.id,
            eventResizeInfo.event.extendedProps.eleve_id,
            moniteurId,
            moment(eventResizeInfo.event.start).format("YYYY-MM-DDTHH:mm:ss"),
            moment(eventResizeInfo.event.end).format("YYYY-MM-DDTHH:mm:ss"),
            eventResizeInfo.event.extendedProps.lieu_id
          );
          let nbHeure =
            diff_hours(eventResizeInfo.event.end, eventResizeInfo.event.start) -
            diff_hours(
              eventResizeInfo.oldEvent.end,
              eventResizeInfo.oldEvent.start
            );
          heureEleve(
            eventResizeInfo.event.extendedProps.eleve_id,
            nbHeure < 0,
            Math.abs(nbHeure)
          );
        } else {
          alert("Cet élève n'a plus assez d'heure sur son compte");
          eventResizeInfo.revert();
        }
      } else if (
        eventResizeInfo.event.extendedProps.type == "indispo" ||
        eventResizeInfo.event.extendedProps.type == "preExam"
      ) {
        newIndispo(
          eventResizeInfo.event.id,
          moniteurId,
          moment(eventResizeInfo.event.start).format("YYYY-MM-DDTHH:mm:ss"),
          moment(eventResizeInfo.event.end).format("YYYY-MM-DDTHH:mm:ss")
        );
      }
    },
    eventClick: function (event) {
      evenementEnCours = event.event;

      if (evenementEnCours.display != "background") {
        resources = event.event.getResources();
        moniteurId = resources[0].id;
        end = event.event.end;
        start = event.event.start;

        resourceTitle = resources[0].title;
        if (evenementEnCours.extendedProps.type == "rdv") {
          if (evenementEnCours.start >= Date.now()) {
            $("#CancelModal").modal("show");
          }
        } else if (evenementEnCours.extendedProps.type == "absence") {
          if (evenementEnCours.start >= Date.now()) {
            if (personneSelected) {
              $("#nomRemplacement").html(
                "Confirmez-vous que vous voulez attribuer " +
                  eleveEnCours.prenom +
                  " " +
                  eleveEnCours.nom +
                  " à ce rendez-vous ?"
              );
              $("#replaceModal").modal("show");
            } else {
              $("#titreResa").html(
                "Réservation le " +
                  moment(evenementEnCours.start).format("DD/MM/YYYY") +
                  " de " +
                  moment(evenementEnCours.start).format("HH:mm") +
                  " à " +
                  moment(evenementEnCours.end).format("HH:mm") +
                  " pour " +
                  resourceTitle
              );
              getDispo(evenementEnCours.start, evenementEnCours.end);
              getExam();
              getSl();
              getLieuxMoniteur(
                evenementEnCours.extendedProps.moniteur_id,
                "moniteur"
              );

              $("#resaModal").modal("show");
            }
          }
        } else if (evenementEnCours.extendedProps.type == "exam") {
          start = new moment(start);
          $("#dateExam").val(start.format("YYYY-MM-DD"));
          $("#heureExam").val(start.format("HH:mm"));
          $("#examModal").modal("show");
          $("#lieuExam").val(evenementEnCours.extendedProps.lieu_id);
          $("#nbPlaceExam").val(evenementEnCours.extendedProps.nbPlace);
          $("#numeroExam").val(evenementEnCours.extendedProps.numero);

          $("#deleteExam").show();
          $("#titreExamlModal").html("Modification Examen");
        } else if (evenementEnCours.extendedProps.type == "indispo") {
          deleteIndispo(evenementEnCours.id);
        } else if (evenementEnCours.extendedProps.type == "indispoType") {
          deleteIndispoType(evenementEnCours.id);
        }
      }
    },
    selectAllow: function (select) {
      return moment().diff(select.start) <= 0;
    },
    select: function (event) {
      evenementEnCours = null;
      start = event.start;
      end = event.end;
      moniteurId = parseInt(event.resource.id);

      if ($("#radioExam").is(":checked")) {
        $("#deleteExam").hide();
        start = new moment(start);
        $("#dateExam").val(start.format("YYYY-MM-DD"));
        $("#heureExam").val(start.format("HH:mm"));
        $("#examModal").modal("show");
        $("#titreExamlModal").html("Création Examen");
      } else {
        moniteurNom = event.resource.extendedProps.username;
        color = event.resource.extendedProps.color;
        $(".dispoRow").remove();
        var select = document.getElementById("lieu");
        var length = select.options.length;

        for (i = length - 1; i >= 0; i--) {
          select.options[i] = null;
        }

        var select = document.getElementById("selectEleveLieu");
        var length = select.options.length;
        for (i = length - 1; i >= 0; i--) {
          select.options[i] = null;
        }
        if ($("#radioDispo").is(":checked")) {
          newIndispo(
            null,
            moniteurId,
            moment(start).format("YYYY-MM-DDTHH:mm:ss"),
            moment(end).format("YYYY-MM-DDTHH:mm:ss")
          );
        } else if ($("#radioResa").is(":checked")) {
          getLieuxMoniteur(event.resource.id, "moniteur");
          if (personneSelected) {
            $("#ModalLieuResa").modal("show");

            $("#titreModalLieu").html(
              "Rendez-vous le  " +
                moment(event.start).format("DD/MM/YYYY") +
                " de " +
                moment(event.start).format("HH:mm") +
                " à " +
                moment(event.end).format("HH:mm") +
                " pour " +
                $("#search").val() +
                " avec " +
                event.resource.title
            );
          } else {
            $("#titreResa").html(
              "Réservation le " +
                moment(event.start).format("DD/MM/YYYY") +
                " de " +
                moment(event.start).format("HH:mm") +
                " à " +
                moment(event.end).format("HH:mm") +
                " pour " +
                event.resource.title
            );
            getDispo(event.start, event.end);
            getExam();
            getSl();

            $("#resaModal").modal("show");
          }
        }
      }
    },
  });
  calendar.render();

  let calendarElementSemaineType = document.querySelector(
    "#calendarSemaineType"
  );
  var calendarSemaineType = new FullCalendar.Calendar(
    calendarElementSemaineType,
    {
      themeSystem: "bootstrap",
      selectable: true,
      firstDay: 1,
      allDaySlot: false,
      slotMinTime: "07:00:00",
      slotMaxTime: "20:00:00",
      height: "auto",
      initialView: "timeGridWeek",
      locale: "fr",
      timeZone: "local",
      headerToolbar: true,
      editable: true,
      eventResizableFromStart: true,
      dayHeaderContent: (args) => {
        let jour = moment(args.date).format("dddd");
        return convertEnToFr(jour);
      },
      eventOverlap: false,
      selectOverlap: false,
      select: function (event) {
        calendarSemaineType.addEvent({
          start: event.start,
          end: event.end,
          color: "grey",
          display: "block",
          type: "indispoType",
        });
        listeIndispoTypes.push({
          id: null,
          moniteur: idSemaineType,
          start: moment(event.start).format("HH:mm:ss"),
          end: moment(event.end).format("HH:mm:ss"),
          day: moment(event.start).weekday(),
        });
      },
      eventClick: function (event) {
        if (event.event.id) {
          deleteIndispoType(event.event.id);
        } else {
          event.event.remove();
          removeIndex = listeIndispoTypes
            .map(function (item) {
              return item.start.toString();
            })
            .indexOf(event.event.start.toString());

          listeIndispoTypes.splice(removeIndex, 1);
        }
      },
      eventChange: function (event) {
        if (!event.event.id) {
          removeIndex = listeIndispoTypes
            .map(function (item) {
              return item.start.toString();
            })
            .indexOf(event.event.start.toString());

          listeIndispoTypes.splice(removeIndex, 1);
        } else {
          if (listeIndispoTypes.length != 0) {
            listeIndispoTypes.forEach((element) => {
              if (element.id == event.event.id) {
                removeIndex = listeIndispoTypes
                  .map(function (item) {
                    return item.id.toString();
                  })
                  .indexOf(event.event.id.toString());

                listeIndispoTypes.splice(removeIndex, 1);
              }
            });
          }
        }
        listeIndispoTypes.push({
          id: event.event.id ? event.event.id : null,
          moniteur: idSemaineType,
          start: moment(event.event.start).format("HH:mm"),
          end: moment(event.event.end).format("HH:mm"),
          day: moment(event.event.start).weekday(),
        });
      },
    }
  );

  $(".moniteur").on("click", function () {
    calendar.getResources().forEach((element) => {
      calendar.getResourceById(element.id).remove();
    });

    $("#tous").prop("checked", false);
    var rdvsMoniteur = new Array();

    $("input:checkbox:checked").each(function () {
      moniteurId = $(this).val().toString();
      entryInfoMoniteurs[0].forEach((element) => {
        if (element.id == moniteurId) {
          calendar.addResource(element);
        }
      });
      calendar.getResourceById(moniteurId);
      entryInfoTout[0].forEach((element) => {
        if (element.moniteur_id == moniteurId) {
          if (element.type == "rdv" || element.type == "absence") {
            rdvsMoniteur.push({
              id: element.id,
              lieu: element.lieu,
              eleve_id: element.eleve_id,
              resourceId: moniteurId,
              moniteur_id: moniteurId,
              title: element.title,
              start: element.start,
              end: element.end,
              backgroundColor: element.backgroundColor,
              borderColor: element.borderColor,
              textColor: "#000000",
              type: "rdv",
              absence: element.absence,
              display: $("#radioDispo").is(":checked") ? "background" : "block",
            });
          } else {
            if (element.type == "indispo") {
              rdvsMoniteur.push({
                id: element.id,
                resourceId: moniteurId,
                moniteur_id: moniteurId,
                start: element.start,
                end: element.end,
                color: "grey",
                display: $("#radioDispo").is(":checked")
                  ? "block"
                  : "background",
                type: "indispo",
              });
            } else {
              if (element.type == "exam") {
                rdvsMoniteur.push({
                  id: element.id,
                  resourceId: moniteurId,
                  lieu: element.lieu,
                  moniteur_id: moniteurId,
                  title: element.title,
                  start: element.start,
                  end: element.end,
                  backgroundColor: "#FF1493",
                  borderColor: "#FF1493",
                  textColor: "black",
                  nbPlace: element.nbPlace,
                  numero: element.numero,
                  type: "exam",
                  display: $("#radioDispo").is(":checked")
                    ? "background"
                    : "block",
                });
              } else {
                if (element.type == "preExam") {
                  rdvsMoniteur.push({
                    id: element.id,
                    moniteur_id: moniteurId,
                    resourceId: moniteurId,
                    start: element.start,
                    end: element.end,
                    color: element.color,
                    display: $("#radioDispo").is(":checked")
                      ? "block"
                      : "background",
                    type: "preExam",
                  });
                }
              }
            }
          }
        }
      });
    });
    if (moniteurId == "on" || moniteurId == null) {
      entryInfoMoniteurs[0].forEach((element) => {
        calendar.addResource(element);
      });
      if (personneSelected) {
        renderEvent(calendar, eventsEleve);
      } else {
        renderEvent(calendar, entryInfoTout[0]);
      }
      $("#tous").prop("checked", true);
      $("input:checkbox").prop("checked", true);
    } else {
      moniteurId = null;
      if (personneSelected) {
        renderEvent(calendar, eventsEleve);
      } else {
        renderEvent(calendar, rdvsMoniteur);
      }
    }
    calendar.render();
  });

  $("#tous").on("click", function () {
    calendar.getResources().forEach((element) => {
      calendar.getResourceById(element.id).remove();
    });
    entryInfoMoniteurs[0].forEach((element) => {
      calendar.addResource(element);
    });
    $("input:checkbox").prop("checked", true);
    renderEvent(calendar, entryInfoTout[0]);
  });

  $("#annuleAgence").on("change", function () {
    if ($(this).is(":checked") || $("#annuleEleve").is(":checked")) {
      $("#submitAnnulation").prop("disabled", false);
    } else {
      $("#submitAnnulation").prop("disabled", true);
    }
  });

  $("#annuleEleve").on("change", function () {
    if ($(this).is(":checked") || $("#annuleAgence").is(":checked")) {
      $("#submitAnnulation").prop("disabled", false);
    } else {
      $("#submitAnnulation").prop("disabled", true);
    }
  });

  $("#cancelButton").on("click", function () {
    $("#submitAnnulation").prop("disabled", true);
    $("#annuleEleve").prop("checked", false);
    $("#annuleAgence").prop("checked", false);
  });

  $("#submitAnnulation").on("click", function () {
    if ($("#annuleAgence").is(":checked")) {
      if (
        confirm(
          "Confirmez-vous que l'agence est à l'initative de l'annulation ?"
        )
      ) {
        calendar.getEvents().forEach((element) => {
          resources = element.getResources();
          let resourceIds = resources.map(function (resource) {
            return resource.id;
          });
          if (
            element.extendedProps.type == "indispo" &&
            parseInt(resourceIds[0]) ==
              parseInt(evenementEnCours.extendedProps.moniteur_id)
          ) {
            if (
              evenementEnCours.start.toString() == element.start.toString() &&
              evenementEnCours.end.toString() == element.end.toString()
            ) {
              entryInfoTout[0].forEach((entry, index) => {
                if (parseInt(element.id) == entry.id) {
                  entryInfoTout[0].splice(index, 1);
                }
                index++;
              });
              (id = element.id), element.remove();
            }
          }
        });
        deleteIndispo(id);
        deleteRdv(evenementEnCours.id);
        heureEleve(
          eleveId,
          true,
          diff_hours(evenementEnCours.end, evenementEnCours.start)
        );

        evenementEnCours.remove();
        $("#CancelModal").modal("hide");
        entryInfoTout[0].forEach((element, index) => {
          if (evenementEnCours.id == element.id) {
            entryInfoTout[0].splice(index, 1);
          }
          index++;
        });
      }
    } else if ($("#annuleEleve").is(":checked")) {
      // ON VERIFIE QUE DIMANCHE NE SE TROUVE PAS ENTRE AJD ET LA DATE CLIQUE
      if (evenementEnCours.start.getDay() != 0) {
        for (
          var i = new Date(Date.now());
          i <= evenementEnCours.start;
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
      if (
        Date.parse(evenementEnCours.start) - (172800000 - sunday) <=
        Date.now()
      ) {
        sunday = 0;
        //ON PASSE LE RDV EN ROUGE
        majRdv(evenementEnCours.id);

        calendar.getEvents().forEach((element) => {
          resources = element.getResources();
          let resourceIds = resources.map(function (resource) {
            return resource.id;
          });

          if (
            element.extendedProps.type == "indispo" &&
            parseInt(resourceIds[0]) ==
              parseInt(evenementEnCours.extendedProps.moniteur_id)
          ) {
            let indispoStart = element.start;
            let indispoEnd = element.end;

            if (
              indispoStart >= evenementEnCours.start &&
              indispoEnd <= evenementEnCours.end
            ) {
              entryInfoTout[0].forEach((entry, index) => {
                if (parseInt(element.id) == entry.id) {
                  entryInfoTout[0].splice(index, 1);
                }
                index++;
              });
              (id = element.id), element.remove();
            }
          }
        });
        deleteIndispo(id);
      } else {
        heureEleve(
          eleveId,
          true,
          diff_hours(evenementEnCours.end, evenementEnCours.start)
        );
        deleteRdv(evenementEnCours.id);

        calendar.getEvents().forEach((element) => {
          resources = element.getResources();
          let resourceIds = resources.map(function (resource) {
            return resource.id;
          });
          if (
            element.extendedProps.type == "indispo" &&
            parseInt(resourceIds[0]) ==
              parseInt(evenementEnCours.extendedProps.moniteur_id)
          ) {
            if (
              evenementEnCours.start.toString() == element.start.toString() &&
              evenementEnCours.end.toString() == element.end.toString()
            ) {
              entryInfoTout[0].forEach((entry, index) => {
                if (parseInt(element.id) == entry.id) {
                  entryInfoTout[0].splice(index, 1);
                }
                index++;
              });
              element.remove();
              id = element.id;
            }
          }
        });
        deleteIndispo(id);
      }
      evenementEnCours.remove();
      $("#CancelModal").modal("hide");
      entryInfoTout[0].forEach((element, index) => {
        if (evenementEnCours.id == element.id) {
          entryInfoTout[0].splice(index, 1);
        }
        index++;
      });
    }

    $("#submitAnnulation").prop("disabled", true);
    $("#annuleEleve").prop("checked", false);
    $("#annuleAgence").prop("checked", false);
  });

  $("#btnAuto").on("change", function () {
    currentRequest = getAuto($(this).is(":checked"));
  });

  $("#semaineTypeModal").on("shown.bs.modal", function () {
    $("#calendar").css("display", "none");
    calendarSemaineType.render();
  });

  $("#semaineTypeModal").on("hidden.bs.modal", function () {
    $("#calendar").css("display", "");
  });

  $("#radioOff").on("click", function () {
    if (personneSelected) {
      renderEvent(calendar, eventsEleve);
    } else {
      renderEvent(calendar, entryInfoTout[0]);
    }
    calendar.getEvents().forEach((element) => {
      if (element.id == "") {
        element.remove();
      }
    });
  });

  $("#message").on("click", function () {
    $("#message").fadeOut();
    getAll();
  });

  $(document).on("click", "input:radio[name=selectEleve]", function () {
    $("#submitResa").popover("dispose");
    eleveId = parseInt($(this).val());
    goToByScroll("lieu");
    if ($("#lieu").val() != "default") {
      $("#submitResa").prop("disabled", false);
    } else {
      $("#submitResa").prop("disabled", true);
    }
  });
  $("#lieu").on("change", function () {
    if ($("input:radio[name=selectEleve]").is(":checked")) {
      $("#submitResa").prop("disabled", false);
    } else {
      $("#submitResa").prop("disabled", true);
    }
  });

  $("#cancelResa").on("click", function () {
    $("#submitResa").prop("disabled", true);
    $("#lieu").val("default");
  });

  $("#cancelResaEleve").on("click", function () {
    $("#selectEleveLieu").val("default");
  });

  $("#submitLieuEleve").on("click", function () {
    lieuId = parseInt($("#selectEleveLieu").val());

    if (eleveEnCours.compteur - diff_hours(end, start) >= 0) {
      $("#lieu").val("default");
      $("#ModalLieuResa").modal("hide");
      heureEleve(eleveId, false, diff_hours(end, start));

      newRdv(
        null,
        eleveId,
        moniteurId,
        new moment(start).format("YYYY-MM-DDTHH:mm:ss"),
        new moment(end).format("YYYY-MM-DDTHH:mm:ss"),
        lieuId
      );
    } else {
      $("[data-toggle='popoverLieuEleve']").popover("show");
    }
  });
  $("#submitResa").on("click", function () {
    findEleve(eleveId);

    if (eleveEnCours.compteur - diff_hours(end, start) >= 0) {
      lieuId = parseInt($("#lieu").val());
      $("#submitResa").prop("disabled", true);
      $("#resaModal").modal("hide");
      if (evenementEnCours === null) {
        heureEleve(eleveId, false, diff_hours(end, start));
        newRdv(
          null,
          eleveId,
          moniteurId,
          new moment(start).format("YYYY-MM-DDTHH:mm:ss"),
          new moment(end).format("YYYY-MM-DDTHH:mm:ss"),
          lieuId
        );
      } else {
        heureEleve(
          eleveId,
          false,
          diff_hours(evenementEnCours.end, evenementEnCours.start)
        );
        replaceEleve(evenementEnCours.id, eleveId);
      }
    } else {
      $("#submitResa").popover("show");
    }
  });
  function goToByScroll(id) {
    $("#resaModal").animate(
      {
        scrollTop: $("#" + id).offset().top,
      },
      "slow"
    );
  }
  function diff_hours(dt2, dt1) {
    var diff = (dt2.getTime() - dt1.getTime()) / 1000;
    diff /= 60 * 60;
    return Math.abs(diff);
  }

  $("#celluleDuree").on("click", function () {
    $("[data-toggle='formDuree']").popover({
      html: true,
      sanitize: false,
    });
    $("[data-toggle='formDuree']").popover("show");
  });

  $("#resaModal").on("click", function (e) {
    if (e.target.id != "celluleDuree" && e.target.id != "duree") {
      $("[data-toggle='formDuree']").popover("hide");
    }
  });

  $(document).on("keyup change", "#filtreHeure", function () {
    var value = $(this).val().toLowerCase();
    $("#dispoContent tr").filter(function () {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
    });
  });

  $(document).on("click", "#resetButton", function () {
    $("#dispoTable").html(tableDispoNoFilter);
  });

  $("#btnOkExam").on("click", function (e) {
    if (verifInputExam()) {
      dateExam = new moment($("#dateExam").val() + " " + $("#heureExam").val());
      dateDebut = dateExam.format("YYYY-MM-DD HH:mm:ss");
      dateFin = dateExam
        .add(35 * $("#nbPlaceExam").val(), "m")
        .format("YYYY-MM-DD HH:mm:ss");
      newExam(
        evenementEnCours ? evenementEnCours.id : "",
        dateDebut,
        dateFin,
        $("#lieuExam").val(),
        $("#numeroExam").val(),
        $("#nbPlaceExam").val(),
        moniteurId
      );

      $("#btnOkExam").prop("disabled", true);
      $("#examModal").modal("hide");
      $("input[name=examen]").val("");
    }
  });

  $("#deleteExam").on("click", function (e) {
    $("#examModal").modal("hide");
    deleteExam(evenementEnCours.id);
  });

  $(".closeExamen").on("click", function (e) {
    $("#btnOkExam").prop("disabled", true);
    $("input[name=examen]").val("");
  });
  $("[name=examen]").on("change keyup", function (e) {
    if (verifInputExam()) {
      $("#btnOkExam").prop("disabled", false);
    } else {
      $("#btnOkExam").prop("disabled", true);
    }
  });

  function verifInputExam() {
    var isValid = true;

    $("input[name=examen]").each(function () {
      if (!$(this).val()) {
        isValid = false;
      }
    });
    return isValid;
  }

  //SEARCHBAR

  $("#search").on("keyup ", function () {
    var minlength = 2;
    var that = this;
    var value = $(this).val();
    if (value.length == 0) {
      $(".infoCache").addClass("hide");
    }
    if (personneSelected) {
      personneSelected = false;
      renderEvent(calendar, entryInfoTout[0]);
      $(".infoCache").addClass("hide");
    }
    searchTimeout = setTimeout(function () {
      if (value.length >= minlength) {
        if (searchRequest != null) searchRequest.abort();

        searchRequest = searchEleve(value, that);
      }
    }, 1000);
  });

  $("#result").on("click", "li", function () {
    $("[data-toggle='popoverLieuEleve']").popover("dispose");
    eleveId = parseInt($(this).val());
    personneSelected = true;
    var click_text = $(this).text();
    if (click_text != "Aucun élève") {
      $("#search").val($.trim(click_text));
      $("#result").html("");
      id = $(this).val();
      getInfoEleve(id);
    }
  });

  $(".fc-resourceCustomMondayView-button, .fc-resourceTimeGridDay-button").on(
    "click",
    function () {
      if (personneSelected) {
        renderEvent(calendar, eventsEleve);
      } else {
        renderEvent(calendar, entryInfoTout[0]);
      }
      $("#radioOff").prop("checked", true);
    }
  );
  function convertToDateMoment(element) {
    switch (element.jour) {
      case "1":
        return {
          id: element.id,
          start: moment()
            .startOf("isoweek")
            .set("hour", moment(element.start.date).get("hour"))
            .set("minute", moment(element.start.date).get("minute"))
            .format("YYYY-MM-DDTHH:mm:ss"),

          end: moment()
            .startOf("isoweek")
            .set("hour", moment(element.end.date).get("hour"))
            .set("minute", moment(element.end.date).get("minute"))

            .format("YYYY-MM-DDTHH:mm:ss"),
          color: "grey",
          display: "block",
          type: "indispoType",
        };

      case "2":
        return {
          id: element.id,
          start: moment()
            .startOf("week")
            .add("days", 2)
            .set("hour", moment(element.start.date).get("hour"))
            .set("minute", moment(element.start.date).get("minute"))

            .format("YYYY-MM-DDTHH:mm:ss"),

          end: moment()
            .startOf("week")
            .add("days", 2)
            .set("hour", moment(element.end.date).get("hour"))
            .set("minute", moment(element.end.date).get("minute"))

            .format("YYYY-MM-DDTHH:mm:ss"),
          color: "grey",
          display: "block",
          type: "indispoType",
        };

      case "3":
        return {
          id: element.id,

          start: moment()
            .startOf("week")
            .add("days", 3)
            .set("hour", moment(element.start.date).get("hour"))
            .set("minute", moment(element.start.date).get("minute"))

            .format("YYYY-MM-DDTHH:mm:ss"),

          end: moment()
            .startOf("week")
            .add("days", 3)
            .set("hour", moment(element.end.date).get("hour"))
            .set("minute", moment(element.end.date).get("minute"))

            .format("YYYY-MM-DDTHH:mm:ss"),
          color: "grey",
          display: "block",
          type: "indispoType",
        };

      case "4":
        return {
          id: element.id,

          start: moment()
            .startOf("week")
            .add("days", 4)
            .set("hour", moment(element.start.date).get("hour"))
            .set("minute", moment(element.start.date).get("minute"))

            .format("YYYY-MM-DDTHH:mm:ss"),

          end: moment()
            .startOf("week")
            .add("days", 4)
            .set("hour", moment(element.end.date).get("hour"))
            .set("minute", moment(element.end.date).get("minute"))

            .format("YYYY-MM-DDTHH:mm:ss"),
          color: "grey",
          display: "block",
          type: "indispoType",
        };

      case "5":
        return {
          id: element.id,

          start: moment()
            .startOf("week")
            .add("days", 5)
            .set("hour", moment(element.start.date).get("hour"))
            .set("minute", moment(element.start.date).get("minute"))

            .format("YYYY-MM-DDTHH:mm:ss"),

          end: moment()
            .startOf("week")
            .add("days", 5)
            .set("hour", moment(element.end.date).get("hour"))
            .set("minute", moment(element.end.date).get("minute"))

            .format("YYYY-MM-DDTHH:mm:ss"),
          color: "grey",
          display: "block",
          type: "indispoType",
        };

      case "6":
        return {
          id: element.id,

          start: moment()
            .endOf("week")
            .set("hour", moment(element.start.date).get("hour"))
            .set("minute", moment(element.start.date).get("minute"))

            .format("YYYY-MM-DDTHH:mm:ss"),

          end: moment()
            .endOf("week")
            .set("hour", moment(element.end.date).get("hour"))
            .set("minute", moment(element.end.date).get("minute"))

            .format("YYYY-MM-DDTHH:mm:ss"),
          color: "grey",
          display: "block",
          type: "indispoType",
        };

      case "0":
        return {
          id: element.id,

          start: moment()
            .endOf("isoweek")
            .set("hour", moment(element.start.date).get("hour"))
            .set("minute", moment(element.start.date).get("minute"))

            .format("YYYY-MM-DDTHH:mm:ss"),

          end: moment()
            .endOf("isoweek")
            .set("hour", moment(element.end.date).get("hour"))
            .set("minute", moment(element.end.date).get("minute"))

            .format("YYYY-MM-DDTHH:mm:ss"),
          color: "grey",
          display: "block",
          type: "indispoType",
        };

      default:
        break;
    }
  }
  function convertEnToFr(jour) {
    switch (jour) {
      case "Monday":
        return "Lundi";
      case "Tuesday":
        return "Mardi";
      case "Wednesday":
        return "Mercredi";
      case "Thursday":
        return "Jeudi";
      case "Friday":
        return "Vendredi";
      case "Saturday":
        return "Samedi";
      case "Sunday":
        return "Dimanche";
    }
  }

  function getIndispoType(id) {
    $.ajax({
      url: `${baseUrl}secretaire/getIndispoType`,
      type: "GET",
      dataType: "JSON",
      data: { id: id },
      success: function (data) {
        eventIndisposType = [];
        data.indisposType.forEach((element) => {
          element = convertToDateMoment(element);
          eventIndisposType.push(element);
        });
        removeFakeEvents(calendarSemaineType);
        renderEvent(calendarSemaineType, eventIndisposType);
      },
    });
  }

  function getLieuxMoniteur(id, type) {
    $.ajax({
      url: `${baseUrl}secretaire/getLieux`,
      type: "GET",
      dataType: "JSON",
      data: { id: id, type: type },
      beforeSend: function () {
        // Before we send the request, remove the .hidden class from the spinner and default to inline-block.
        $("#loaderLieu").show();
      },
      success: function (data) {
        data.lieux.forEach((element) => {
          $("#lieu").append(
            $("<option>", {
              value: element.id,
              text: element.libelle,
            })
          );
          if (personneSelected) {
            $("#selectEleveLieu").append(
              $("<option>", {
                value: element.id,
                text: element.libelle,
              })
            );
          }
          if (element.libelle == "CER Garibaldi") {
            $("#selectEleveLieu").val(element.id);
            $("#lieu").val(element.id);
          }
        });
      },
      complete: function () {
        // Set our complete callback, adding the .hidden class and hiding the spinner.
        $("#loaderLieu").hide();
      },
    });
  }

  function getDispo(start, end) {
    $.ajax({
      url: `${baseUrl}secretaire/getDispo`,
      type: "GET",
      dataType: "JSON",
      data: { start: start, end: end },
      beforeSend: function () {
        // Before we send the request, remove the .hidden class from the spinner and default to inline-block.
        $("#loaderDispo").show();
      },
      success: function (data) {
        elevesDispo = data.eleves;
        data.eleves.forEach((element) => {
          $("#dispoTable > tbody:last-child").append(
            "<tr class='dispoRow' id='" +
              element.id +
              "'><td>" +
              element.nom +
              "</td><td>" +
              element.prenom +
              "</td><td>" +
              element.tel +
              "</td><td>" +
              element.duree +
              "h</td><td>" +
              element.haposer +
              "h</td><td>" +
              element.compteur +
              "h</td> <td><input class='form-check-input' type='radio' name='selectEleve' value='" +
              element.id +
              "'></td></tr>"
          );
        });

        tableDispoNoFilter = $("#dispoTable").html();
      },
      complete: function () {
        // Set our complete callback, adding the .hidden class and hiding the spinner.
        $("#loaderDispo").hide();
      },
    });
  }

  function getExam() {
    $.ajax({
      url: `${baseUrl}secretaire/getExam`,
      type: "GET",
      dataType: "JSON",
      beforeSend: function () {
        // Before we send the request, remove the .hidden class from the spinner and default to inline-block.
        $("#loaderExam").show();
      },
      success: function (data) {
        elevesExam = data.elevesExam;
        elevesExam.forEach((element) => {
          $("#examTable > tbody:last-child").append(
            "<tr class='dispoRow' id='" +
              element.id +
              "'><td>" +
              element.nom +
              "</td><td>" +
              element.prenom +
              "</td><td>" +
              element.tel +
              "</td><td>" +
              element.date +
              "</td><td>" +
              element.haposer +
              "h</td><td>" +
              element.compteur +
              "h</td> <td><input class='form-check-input' type='radio' name='selectEleve' value='" +
              element.id +
              "'></td></tr>"
          );
        });
      },
      complete: function () {
        // Set our complete callback, adding the .hidden class and hiding the spinner.
        $("#loaderExam").hide();
      },
    });
  }

  function getSl() {
    $.ajax({
      url: `${baseUrl}secretaire/getSl`,
      type: "GET",
      dataType: "JSON",
      beforeSend: function () {
        // Before we send the request, remove the .hidden class from the spinner and default to inline-block.
        $("#loaderSl").show();
      },
      success: function (data) {
        elevesSl = data.elevesSl;
        elevesSl.forEach((element) => {
          $("#slTable > tbody:last-child").append(
            "<tr class='dispoRow' id='" +
              element.id +
              "'><td>" +
              element.nom +
              "</td><td>" +
              element.prenom +
              "</td><td>" +
              element.tel +
              "</td><td>" +
              element.haposer +
              "h</td><td>" +
              element.compteur +
              " h</td><td><input class='form-check-input' type='radio' name='selectEleve' value='" +
              element.id +
              "'></td></tr>"
          );
        });
      },
      complete: function () {
        // Set our complete callback, adding the .hidden class and hiding the spinner.
        $("#loaderSl").hide();
      },
    });
  }
  function deleteIndispo(id) {
    otherRequestPending = $.ajax({
      type: "DELETE",
      url: `${baseUrl}secretaire/deleteIndispo/${id}`,

      success: function () {
        otherRequestPending = null;
        getAll();
      },
    });
  }

  function getIndispo() {
    if (checkRequest == null) {
      checkRequest = $.ajax({
        url: `${baseUrl}secretaire/getIndispo`,
        type: "GET",
        dataType: "JSON",
        success: function (data) {
          checkRequest = null;
          totalIndispo[0] = data;
        },
      });
    }
  }
  function deleteRdv(id) {
    $.ajax({
      type: "DELETE",
      url: `${baseUrl}secretaire/${id}/deleteRdv`,
      success: function () {
        getAll();
      },
    });
  }

  function getAll() {
    getRequest = $.ajax({
      url: `${baseUrl}secretaire/getAll/${
        $("#radioDispo").is(":checked") ? 1 : 0
      }`,
      type: "GET",
      dataType: "JSON",

      success: function (data) {
        entryInfoTout[0] = data.tout;

        renderEvent(calendar, data.tout);
        getIndispo(); 
      },
      beforeSend: function () {
        if (getRequest != null) {
          getRequest.abort();
        }
      },
    });
  }

  function heureEleve(id, incremente, nbHeure) {
    $.ajax({
      url: `${baseUrl}secretaire/heureEleve`,
      type: "PUT",
      data: {
        eleve: id,
        incremente: incremente,
        nbHeure: nbHeure,
      },
    });
  }

  function newIndispoType(liste) {
    $.ajax({
      url: `${baseUrl}secretaire/newIndispoType`,
      type: "PUT",
      data: {
        listeIndispoTypes: liste,
      },
      success: function () {
        listeIndispoTypes = new Array();
        getIndispoType(idSemaineType);
        getAll();
      },
    });
  }

  function deleteIndispoType(id) {
    $.ajax({
      url: `${baseUrl}secretaire/deleteIndispoType/${id}`,
      type: "DELETE",
      success: function () {
        getIndispoType(idSemaineType);
        getAll();
      },
    });
  }

  function deleteExam(id) {
    $.ajax({
      url: `${baseUrl}secretaire/${id}/deleteExam`,
      type: "DELETE",
      success: function () {
        getAll();
      },
    });
  }

  function majRdv() {
    $.ajax({
      type: "PUT",
      url: `${baseUrl}secretaire/${evenementEnCours.id}/majRdv`,
    });
  }
  function newIndispo(id, moniteur, start, end) {
    if (checkRequest != null) {
      checkRequest.abort();
    }
    otherRequestPending = $.ajax({
      url: `${baseUrl}secretaire/newIndispo`,
      type: "PUT",
      data: {
        id: id,
        moniteur,
        moniteur,
        start: start,
        end: end,
      },

      success: function () {
        otherRequestPending = null;
        if (personneSelected) {
          getInfoEleve(eleveEnCours.id);
          getIndispo();
        }
        getAll();
      },
    });
  }

  function searchEleve(value, that) {
    $.ajax({
      type: "GET",
      url: `${baseUrl}secretaire/searchEleve`,
      data: {
        q: value,
      },
      dataType: "text",
      beforeSend: function () {
        // Before we send the request, remove the .hidden class from the spinner and default to inline-block.
        $("#loaderEleves").removeClass("hide");
      },
      success: function (msg) {
        //we need to check if the value is the same
        $("#result").html("");
        if (value == $(that).val()) {
          var result = JSON.parse(msg);
          if (result != "Aucun élève") {
            result.forEach((element) => {
              $("#result").append(
                '<li value="' +
                  element.id +
                  '" class="list-group-item link-class">' +
                  element.prenom +
                  " " +
                  element.nom
              );
            });
          } else {
            $("#result").append(
              '<li class="list-group-item">' + result + "</li>"
            );
          }
        }
      },
      complete: function () {
        // Set our complete callback, adding the .hidden class and hiding the spinner.
        $("#loaderEleves").addClass("hide");
      },
    });
  }
  function getAuto(auto) {
    $.ajax({
      type: "PUT",
      data: { auto: auto },
      url: `${baseUrl}secretaire/auto`,
      beforeSend: function () {
        if (currentRequest != null) {
          currentRequest.abort();
        }
      },
    });
  }

  function attributionPlace(lieu) {
    $.ajax({
      type: "POST",
      data: { lieu: lieu },
      url: `${baseUrl}secretaire/attributionPlace`,
    });
  }

  function newRdv(id, eleve, moniteur, start, end, lieu) {
    otherRequestPending = $.ajax({
      url: `${baseUrl}secretaire/newRdv`,
      type: "PUT",
      data: {
        id: id,
        eleve: eleve,
        moniteur: moniteur,
        start: start,
        end: end,
        lieu: lieu,
      },
      success: function () {
        otherRequestPending = null;
        if (personneSelected) {
          getInfoEleve(eleve);
          getIndispo();
        } else {
          getAll();
        }
      },
    });
  }
  function newExam(id, start, end, lieu, numero, nbPlace, moniteur) {
    $.ajax({
      url: `${baseUrl}secretaire/newExam`,
      type: "PUT",
      data: {
        id: id,
        start: start,
        end: end,
        lieu: lieu,
        numero: numero,
        nbPlace: nbPlace,
        moniteur: moniteur,
      },
      success: function () {
        getAll();
        attributionPlace($("#lieuExam").val());
      },
    });
  }
  function getInfoEleve(id) {
    $(".infoCache").addClass("hide");

    $("#examenEleve, #forfaitEleve, #compteurEleve, #haposerEleve").text("");
    $.ajax({
      type: "GET",
      url: `${baseUrl}secretaire/${id}/getInfoEleve/${
        $("#radioDispo").is(":checked") ? 1 : 0
      }`,

      success: function (data) {
        eventsEleve = data.events;
        eleveEnCours = data.infoEleve[0];
        $(".infoCache").removeClass("hide");
        if (data.examens[0]) {
          dateExamen = moment(data.examens[0].dateExamen.date);
          dateExamen = dateExamen.format("DD/MM/YYYY");
          lieuExamen = data.examens[0].lieuExamen;
        }
        if (data.forfaits) {
          data.forfaits.forEach((element) => {
            $("#forfaitEleve").text(element.forfaits);
          });
        }

        if (data.infoEleve[0].compteur) {
          $("#compteurEleve").text(data.infoEleve[0].compteur + "h");
        }
        if (parseInt(data.infoEleve[0].haposer)) {
          $("#haposerEleve").text(data.infoEleve[0].haposer + "h");
        } else {
          $("#haposerEleve").text("0h");
        }
        if (data.examens[0] && data.monoShortlist[0]) {
          $("#examenEleve").text(
            "Examen le " +
              dateExamen +
              " - " +
              lieuExamen +
              " - Short-list - " +
              data.monoShortlist[0].moniteur
          );
        } else if (data.examens[0] && !data.monoShortlist[0]) {
          $("#examenEleve").text("Examen le " + dateExamen);
        } else if (!data.examens[0] && data.monoShortlist[0]) {
          $("#examenEleve").text(
            "Short-list - " + data.monoShortlist[0].moniteur
          );
        }
        //gestion calendrier
        renderEvent(calendar, eventsEleve);
      },
    });
  }
  function renderEvent(calendar, events) {
    calendar.setOption("events");
    calendar.setOption("events", events);
    $(".fc-event-title").css(
      "visibility",
      $("#radioDispo").is(":checked") ? "hidden" : "visible"
    );
  }

  function replaceEleve(idEvent, idEleve) {
    $.ajax({
      url: `${baseUrl}secretaire/replaceEleve`,
      type: "PUT",
      data: {
        event: idEvent,
        eleve: idEleve,
      },
      success: function () {
        getAll();
      },
    });
  }
  function findEleve(id) {
    elevesDispo.forEach((eleve) => {
      if (eleve.id == id) {
        eleveEnCours = eleve;
      }
    });

    elevesSl.forEach((eleve) => {
      if (eleve.id == id) {
        eleveEnCours = eleve;
      }
    });

    elevesExam.forEach((eleve) => {
      if (eleve.id == id) {
        eleveEnCours = eleve;
      }
    });
  }

  $("#submitReplace").on("click", function () {
    replaceEleve(evenementEnCours.id, eleveEnCours.id);
    $("#replaceModal").modal("hide");
  });
  $("#selecteur").on("change", function () {
    if ($("#radioDispo").is(":checked")) {
      entryInfoTout[0].forEach((element) => {
        if (
          element["type"] == "rdv" ||
          element["type"] == "exam" ||
          element["type"] == "absence"
        ) {
          element["display"] = "background";
        } else {
          if (element["type"] == "indispo" || element["type"] == "preExam") {
            element["display"] = "block";
          }
        }
      });
      renderEvent(calendar, entryInfoTout[0]);
    } else {
      entryInfoTout[0].forEach((element) => {
        if (
          element["type"] == "rdv" ||
          element["type"] == "exam" ||
          element["type"] == "absence"
        ) {
          element["display"] = "block";
        } else {
          if (element["type"] == "indispo" || element["type"] == "preExam") {
            element["display"] = "background";
          }
        }
      });
      renderEvent(calendar, entryInfoTout[0]);
    }
  });

  $("#submitSemaineType").on("click", function () {
    if (listeIndispoTypes.length != 0) {
      checkRdvInIndispo(idSemaineType, listeIndispoTypes);
      $("#RdvToCancelTable > tbody").empty();
    }
  });
  function removeFakeEvents(calendar) {
    calendar.getEvents().forEach((element) => {
      if (!(element.id != "")) {
        element.remove();
      }
    });
  }
  function checkRdvInIndispo(id, liste) {
    $.ajax({
      url: `${baseUrl}secretaire/checkRdvInIndispo/${id}`,
      type: "GET",
      data: {
        listeIndispoTypes: liste,
      },
      success: function (data) {
        if (data.length != 0) {
          $("#cancelListeRdv").modal("show");
          listeRdv = data;
console.log(listeRdv);
          listeRdv.forEach((element) => {
            $("#RdvToCancelTable > tbody:last-child").append(
              "<tr id=" +
                element.id +
                '><th scope="row">' +
                element.date +
                "</th>" +
                "<td>" +
                element.eleve +
                "</td>" +
                "<td>" +
                element.telEleve +
                "</td>" +
                '<td><button value="' +
                element.id +
                '" type="button" name="deleteRdvAndIndispo" class="btn btn-danger">Supprimer</button></td>' +
                "</tr>"
            );
          });
        } else {
          newIndispoType(listeIndispoTypes);
          $("#semaineTypeModal").modal("hide");
        }
      },
    });
  }
  $(document).on("click", "button[name='deleteRdvAndIndispo']", function () {
    $("#" + $(this).val()).remove();
    if ($("#RdvToCancelTable > tbody").children().length == 0) {
      $("#cancelListeRdv").modal("hide");
    }

    deleteRdv($(this).val());
    listeRdv.forEach((element) => {
      if (element.id == $(this).val()) {
        startSemaineType = element.start;
        endSemaineType = element.end;
        eleveId = element.eleve_id;
      }
    });

    calendar.getEvents().forEach((element) => {
      resources = element.getResources();
      let resourceIds = resources.map(function (resource) {
        return resource.id;
      });
      if (
        element.extendedProps.type == "indispo" &&
        parseInt(resourceIds[0]) == idSemaineType
      ) {
        if (
          moment(startSemaineType).isSame(moment(element.start)) &&
          moment(endSemaineType).isSame(moment(element.end))
        ) {
          entryInfoTout[0].forEach((entry, index) => {
            if (parseInt(element.id) == entry.id) {
              entryInfoTout[0].splice(index, 1);
            }
            index++;
          });
          element.remove();
          id = element.id;
        }
      }
    });
    deleteIndispo(id);
    startSemaineType = new Date(startSemaineType);
    endSemaineType = new Date(endSemaineType);
    heureEleve(eleveId, true, diff_hours(startSemaineType, endSemaineType));
  });
});
$(document).on("click", function (event) {
  if (!event.target.getAttribute("name")) {
    $("#result").html("");
  }
});
