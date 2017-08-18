<?php
$doc = JFactory::getDocument();

JHTML::stylesheet("fullcalendar.css","components/com_job_management/assets/fullcalendar/");
JHTML::script("fullcalendar.min.js","components/com_job_management/assets/fullcalendar/");

$data = array();
if( count($this->jobs) > 0 ) foreach ($this->jobs AS $j){
    $data[] = array(
            'title'=>$j->title,
        'start'=> date('Y-m-d',strtotime($j->date_start)),
        'end'=> date('Y-m-d',strtotime($j->date_end)),
    );
}

?>
<h1 class="pb-3"><?php echo $doc->getTitle() ?></h1>

<div id='calendar'></div>


<script>
    $(document).ready(function() {

        jQuery('#calendar').fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title',
                right:''
                //right: 'month,agendaWeek,agendaDay,listWeek'
            },
            defaultDate: '<?php echo date("Y-m-d")?>',
            navLinks: true, // can click day/week names to navigate views
           // editable: true,
            eventLimit: true, // allow "more" link when too many events
            events: <?php echo str_replace('"',"'",json_encode($data,JSON_UNESCAPED_UNICODE)); ?>
        });

    });


</script>