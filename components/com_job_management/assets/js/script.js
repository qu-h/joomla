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
});

