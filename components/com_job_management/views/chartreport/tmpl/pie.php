<?php
$doc = JFactory::getDocument();
?>
<h1 class="pb-3"><?php echo $doc->getTitle() ?></h1>

<form action="" method="post" name="adminForm" class="col-12">
    <div class="row">

        <div class="col-6">
            <?php echo JHTML::_('JobForm.inputdate', "date_from",'Từ ngày',$this->date_from,4,'jobs'); ?>
        </div>
        <div class="col-6">
            <?php echo JHTML::_('JobForm.inputdate', "date_to",'Đến ngày',$this->date_to,4,'jobs'); ?>
        </div>
    </div>

    <input type="hidden" name="option" value="com_job_management" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="view" value="chartreport" />
    <input type="hidden" name="layout" value="<?php echo JRequest::getCmd( 'layout') ?>" />
    <?php echo JHTML::_( 'form.token' ); ?>
</form>

<?php
if( isset($this->companys) AND count($this->companys) > 0 ) :foreach ($this->companys AS $company): ?>

    <div id="company<?php echo $company->id ?>" class="companypie"  style="min-width: 310px; height: 400px; max-width: 600px; margin: 0 auto"></div>

    <script type="text/javascript">
        $(document).ready(function () {

            Highcharts.chart('company<?php echo $company->id ?>', {
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'pie'
                },
                title: {
                    text: 'Việc của công ty <?php echo $company->name ?> từ ngày <?php echo JHTML::_('JobMg.DateFormat',$this->date_from,false,'d/m/Y') ?>'
                },
                tooltip: { pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>' },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: false
                        },
                        showInLegend: true
                    }
                },
                series: [{
                    name: 'Tỉ Lệ',
                    colorByPoint: true,
                    data: <?php echo $company->json ?>
                }]
            });
        });
    </script>
<?php  endforeach; endif; ?>
