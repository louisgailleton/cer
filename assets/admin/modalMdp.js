$(document).ready(function () {
  baseUrl = "/";
  $("[name='editGerant']").on("click", function () {
    $("#mdpModal").modal("show");
    $("[name='idGerant']").val($(this).val())

  });

});
