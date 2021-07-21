const { parseHTML } = require("jquery");

const entryRdv = document.querySelectorAll("[data-rdv]");
const baseUrl = "/"
const rdvs = Array.from(entryRdv).map((item) => JSON.parse(item.dataset.rdv));
var otherId;
$(document).ready(function () {
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
    initialView: "timeGridDay",
    locale: "fr",
    timeZone: "Europe/Paris",
    customButtons: {
      weekPopUp: {
        text: "Semaine",
        click: function () {
          $("#semaineModal").modal("show");
        },
      },
    },
    eventDidMount: function (arg) {
      $(arg.el).prepend(
        arg.event.extendedProps.done === true
          ? "<i class='pastille pastilleVerte fas fa-circle'></i>"
          : "<i class='pastille pastilleRouge fas fa-circle'></i>"
      );
    },
    eventClick: function (event) {
      evenementEnCours = event.event;
      getOptionsRdv(event.event.id);
      $("#optionModal").modal("show");
    },
    headerToolbar: {
      start: "prev,next",
      center: "title",
      end: "today,weekPopUp",
    },
    events: rdvs[0],
    buttonText: {
      today: "Aujourd'hui",
    },
  });

  let calendarEltSemaine = document.querySelector("#calendarSemaine");
  let calendarSemaine = new FullCalendar.Calendar(calendarEltSemaine, {
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
    timeZone: "Europe/Paris",
    headerToolbar: {
      start: "prev,next",
      center: "title",
      end: "today",
    },
    events: rdvs[0],
    buttonText: {
      today: "Aujourd'hui",
    },

    eventDidMount: function (arg) {
      $(arg.el).prepend(
        arg.event.extendedProps.done === true
          ? "<i class='pastille pastilleVerte fas fa-circle'></i>"
          : "<i class='pastille pastilleRouge fas fa-circle'></i>"
      );
    },
    eventClick: function (event) {
      evenementEnCours = event.event;
      getOptionsRdv(event.event.id);
      $("#optionModal").modal("show");
    },
  });
  calendar.render();

  $("#semaineModal").on("shown.bs.modal", function () {
    calendarSemaine.render();
  });

  $("#btnDone").on("change", function () {
    verifRadioMotif();
  });

  $("input[name=radioMotif]").on("click", function () {
    $("#selectIncident").val("default");
    $("#inputAutre").val("");
    if ($(this).attr("id") == "radioIncident") {
      $("#divIncident").removeClass("hide");
    } else {
      $("#divIncident").addClass("hide");
    }
    verifRadioMotif();
  });

  $("#selectIncident").on("change", function () {
    verifRadioMotif();
  });

  $("#inputAutre").on("keyup ", function () {
    $("input[name=radioMotif]").each(function () {
      $(this).prop("checked", false);
    });
    $("#divIncident").addClass("hide");
    $("#selectIncident").val("default");
    verifRadioMotif();
  });

  function verifRadioMotif() {
    var isValid = false;
    $("input[name=radioMotif]").each(function () {
      if ($(this).is(":checked") && $(this).attr("id") != "radioIncident") {
        isValid = true;
      }
    });

    if ($("#btnDone").is(":checked")) {
      isValid = true;
    }
    if ($("#selectIncident").val() != null) {
      isValid = true;
    }

    if ($("#inputAutre").val().length != 0) {
      isValid = true;
    }

    if (isValid) {
      $("#submitOption").prop("disabled", false);
    } else {
      $("#submitOption").prop("disabled", true);
    }

    return isValid;
  }

  $("#submitOption").on("click", function () {
    $.ajax({
      url: `${baseUrl}moniteur/${evenementEnCours.id}/majRdv`,
      type: "PUT",
      data: {
        done: $("#btnDone").is(":checked"),
        motif:
          $("input[name=radioMotif]:checked").attr("id") != "radioIncident"
            ? $("input[name=radioMotif]:checked").attr("id")
            : $("#selectIncident").val(),
        otherMotif: $("#inputAutre").val(),
        otherId: otherId,
      },
      success: function (data) {
        rdvs[0].forEach((element) => {
          if (element.id == evenementEnCours.id) {
            element.done = data;
          }
        });
        renderEvents();
        $("#optionModal").modal("hide");
      },
    });
  });

  function renderEvents() {
    calendar.setOption("events");
    calendar.setOption("events", rdvs[0]);
    calendarSemaine.setOption("events");
    calendarSemaine.setOption("events", rdvs[0]);
  }

  function getOptionsRdv(id) {
    $.ajax({
      url: `${baseUrl}moniteur/${id}/getOptionsRdv`,
      type: "GET",
      dataType: "JSON",
      success: function (data) {
        $("#divIncident").addClass("hide");
        $("#inputAutre").val("");
        if (data.motif) {
          if (data.incident) {
            $("#radioIncident").prop("checked", true);
            $("#divIncident").removeClass("hide");
            $("#selectIncident").val(data.motif);
          } else if (data.other) {
            otherId = data.otherId;
            $("#inputAutre").val(data.otherMotif);
          } else {
            $("input[name=radioMotif]").each(function () {
              if ($(this).attr("id") == data.motif) {
                $(this).prop("checked", true);
              }
            });
          }
        } else {
          $("input[name=radioMotif]").each(function () {
            $(this).prop("checked", false);
          });
        }

        if (data.done) {
          $("#btnDone").bootstrapToggle("on");
          $("input[name=radioMotif]").each(function () {
            $(this).prop("disabled", true);
          });
          $("#inputAutre").prop("disabled", true);
          $("#submitOption").prop("disabled", true);
          $("#btnDone").bootstrapToggle("disable");
          $("#selectIncident").prop("disabled", true);
        } else {
          $("#btnDone").bootstrapToggle("off");
          $("input[name=radioMotif]").each(function () {
            $(this).prop("disabled", false);
          });
          $("#inputAutre").prop("disabled", false);
          $("#submitOption").prop("disabled", false);
          $("#btnDone").bootstrapToggle("enable");
          $("#selectIncident").prop("disabled", false);
          verifRadioMotif();
        }
      },
    });
  }
});
