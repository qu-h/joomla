$(document).ready(function () {
    // $('input.date').datepicker({
    //     keyboardNavigation: false,
    //     calendarWeeks: true,
    //     autoclose: true,
    //     toggleActive: true
    // });
    //
    // $(".input-group-addon.fa-calendar").click(function () {
    //     $(this).parents(".form-group").find('input.date').datepicker('show');
    // });
    //
    //
    $('.form_datetime').datetimepicker({
        weekStart: 1,
        todayBtn:  1,
        autoclose: 1,
        todayHighlight: 1,
        startView: 2,
        forceParse: 0,
        showMeridian: 1
    });
    $('.form_date').datetimepicker({
        weekStart: 1,
        todayBtn:  1,
        autoclose: 1,
        todayHighlight: 1,
        startView: 2,
        minView: 2,
        forceParse: 1
    });


    var modal = '<div class="modal fade in" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: block; padding-right: 17px;">\n' +
      '<div class="loader"></div>'+
        '</div>'+
        '<div class="modal-backdrop fade show"></div>'
    ;

    $('form').submit(function() {
        //$("body").addClass("modal-open").append(modal);
        $("body").append(modal);
    });

});

