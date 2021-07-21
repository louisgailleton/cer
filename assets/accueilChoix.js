$(document).ready(function() {
    $('[data-toggle="popover"]').popover({
       placement: 'right',
       trigger: 'hover'
    });
 });

 $('#myModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget) // Button that triggered the modal
    var titre = button.data('nom')
    var type = button.data('type') // Extract info from data-* attributes
    var modal = $(this)

    modal.find('.modal-title').text(titre)
})