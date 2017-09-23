<?php
$db		=& JFactory::getDBO();
$doc = JFactory::getDocument();
$height = 150;

?>
<style>
    .name, .finished, .late, .not_finished {
        height: <?=$height?>px;
        line-height: <?=$height?>px;
    }
    .finished, .late, .not_finished {
        font-size: 150%;
        font-weight: bold;
    }
    .finished {
        color: #3366cc;
    }
    .late {
        color: #dc3912;
    }
    .not_finished {
        color: #ff9900;
    }
</style>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    google.charts.load("current", {packages:["corechart"]});
    google.charts.setOnLoadCallback(drawChart);
    function drawChart() {
        $('div.row.staff').each(function(){
            var row = $(this);
            var uid = row.attr('uid');
            var data = google.visualization.arrayToDataTable([
                ['Status', 'Item'],
                ['Hoàn Thành',      parseInt(row.find('.finished').text()) ],
                ['Hoàn thành trễ',  parseInt(row.find('.late').text())],
                ['Chưa hoàn thành', parseInt(row.find('.not_finished').text())]
            ]);

            var options = {
                legend: 'none',
                is3D: true,
            };

            chart_elem = document.getElementById('piechart_3d_'+uid);
            var chart = new google.visualization.PieChart(document.getElementById('piechart_3d_'+uid));
            chart.draw(data, options);

        });
    }
</script>

<div class="clearfix">
    <h1 class="pb-3"><?php echo $doc->getTitle() ?></h1>

    <form action="" method="post" name="adminForm" class="col-12">

        <div class="row">

            <div class="col-6">
                <?php echo JHTML::_('JobForm.companys', "company_id",'Công ty',$this->company_id,4,'jobs'); ?>
            </div>
            <div class="col-6">
                <?php echo JHTML::_('JobForm.groups', "group_id",'Phòng ban',$this->group_id,4,'jobs'); ?>
            </div>
            <div class="col-6">
                <?php echo JHTML::_('JobForm.inputdate', "date_from",'Từ ngày',$this->date_from,4,'jobs'); ?>
            </div>
            <div class="col-6">
                <?php echo JHTML::_('JobForm.inputdate', "date_to",'Đến ngày',$this->date_to,4,'jobs'); ?>
            </div>
        </div>
        <input type="hidden" name="option" value="com_job_management" />
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="view" value="jobreport" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo JHTML::_( 'form.token' ); ?>
    </form>

    <?php if( count($this->users) > 0 ) : ?>
        <div class="row" uid="<?=$uid?>">
            <div class="col-md-3">Nhân viên</div>
            <div class="col-2 text-center">Hoàn thành</div>
            <div class="col-2 text-center">Hoành thành trễ</div>
            <div class="col-2 text-center">Chưa hoàn thành</div>

        </div>
        <hr/>
    <?php foreach ($this->users AS $uid=>$u): ?>
    <div class="row staff" uid="<?=$uid?>">
        <div class="col-md-3 name"><?php echo JHTMLJobMg::GetUserDetail($uid) ?></div>
        <div class="col-2 text-center finished"><?=$u['finished']?></div>
        <div class="col-2 text-center late"><?=$u['late']?></div>
        <div class="col-2 text-center not_finished"><?=$u['not_finished']?></div>
        <div class="col-3 text-center"><div id="piechart_3d_<?=$uid?>" style="width: 100%; height: <?=$height?>px; "></div></div>
    </div>
    <?php endforeach; endif; ?>
</div>

