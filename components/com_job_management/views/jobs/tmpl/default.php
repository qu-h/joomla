<?php
$db		=& JFactory::getDBO();
$doc = JFactory::getDocument();

$close_allow = JHTML::_('jobMg.isClose');
$add_allow = JHTML::_('jobMg.isAdd');
?>
<div class="clearfix">
    <h1 class="pb-3"><?php echo $doc->getTitle() ?></h1>

        <form action="" method="post" name="adminForm" class="col-12">
            <?php if ( $add_allow ): ?>
            <div class="row mb-3">
                <div class="col-4">
                    <a class="btn btn-success text-white" href="<?php echo JRoute::_( 'index.php?option=com_job_management&task=add&view=job' ); ?>" >Thêm Công Việc</a>
                </div>
            </div>
            <?php endif; ?>
            <div class="row">

                <div class="col-6">
                    <?php echo JHTML::_('JobForm.inputdate', "date_from",'Từ ngày',$this->date_from,4,'jobs'); ?>
                </div>
                <div class="col-6">
                    <?php echo JHTML::_('JobForm.inputdate', "date_to",'Đến ngày',$this->date_to,4,'jobs'); ?>
                </div>
            </div>


            <table class="table table-hover table-responsive" >
                <thead>
                <tr>
                    <td style="display: none">
                        <?php echo JText::_( 'Num' ); ?>
                    </td>
                    <th class="text-left">
                        <?php echo JHTML::_('grid.sort',   'Tên Công Việc', 'g.title', @$lists['order_Dir'], @$lists['order'] ); ?>
                    </th>
                    <th style="width: 5%;" class="text-center">
                        <?php echo JText::_( 'Reply' ); ?>
                    </th>
                    <th style="width: 5%;" class="text-center">
                        <?php echo JText::_( 'User' ); ?>
                    </th>

                    <th width="2%" nowrap="nowrap">
                        <?php echo JHTML::_('grid.sort',   'Ngày Bắt đầu', 'g.date_start', @$lists['order_Dir'], @$lists['order'] ); ?>
                    </th>
                    <th width="2%" nowrap="nowrap">
                        <?php echo JHTML::_('grid.sort',   'Ngày Kết Thúc', 'g.date_end', @$lists['order_Dir'], @$lists['order'] ); ?>
                    </th>
                    <th  nowrap="nowrap" style="width: 8%;">
                        <?php echo JHTML::_('grid.sort',   'Cấp Độ', 'g.access', @$lists['order_Dir'], @$lists['order'] ); ?>
                    </th>
                    <th class="text-left" nowrap="nowrap" >
                        <?php echo JHTML::_('grid.sort',   'Nhóm Việc', 'section_name', @$lists['order_Dir'], @$lists['order'] ); ?>
                    </th>

                    <th width="1%" class="title">Quá hạn</th>
                    <th width="1%" class="title">Đã xem</th>
                    <?php if ( $close_allow ): ?>
                        <th>Đóng</th>
                    <?php endif; ?>
                </tr>
                </thead>

                <tbody>
                <?php
                if( isset($this->jobs) AND !empty($this->jobs) ): foreach ($this->jobs AS $i=>$row) :
                    $is_over_time = ( strtotime($row->date_end) < time()   ) ? true : false;
                    if ( $row->status == -1 && strtotime($row->date_end) >= strtotime($row->modified)  ){
                        $is_over_time = false;
                    }

                ?>
                        <tr class="<?php echo "row".($i); ?>">
                            <td style="display: none"><?php echo JHTML::_('grid.checkedout',   $row, $i );?></td>
                            <td>
                                <a href="<?php echo JRoute::_( 'index.php?option=com_job_management&task=view&jid='. $row->id ); ?>">
                                    <?php echo htmlspecialchars($row->title, ENT_QUOTES); ?></a>
                            </td>
                            <td align="center" >
                                <a href="<?php echo JRoute::_( 'index.php?option=com_job_management&c=reply&jid='. $row->id ); ?>" class="count_reply" >
                                    <span class="fa fa-comments-o font-size-23" ></span>
                                    <i><?php echo JHTML::_('jobMg.ReplyCount',   $row)?></i>
                                </a>
                            </td>
                            <td class="text-center" >
                    <span class="count_uid ">
                        <span class="fa fa-user font-size-23"></span>
                        <i><?php echo JHTML::_('jobMg.UidCount',   $row)?></i>
                    </span>
                            </td>
                            <td align="center" nowrap="nowrap">
                                <?php
                                echo JHTML::_('jobMg.DateFormat',   $row->date_start);
                                ?>
                            </td>
                            <td align="center" nowrap="nowrap" >
                                <?php
                                $date_end= JHTML::_('jobMg.DateFormat',  $row->date_end);
                                if( $is_over_time ){
                                    echo '<span class="txt-danger">'.$date_end.'</span>';
                                } else {
                                    echo $date_end;
                                }
                                ?>
                            </td>
                            <td align="center"><?php echo JHTML::_('jobMg.level',   $row, $i)?></td>

                            <td><?php echo $row->section_name; ?></td>

                            <td class="userfront">
                                <?php if( $is_over_time ):?>
                                    <i class="fa fa-ban red "></i>
                                <?php else : ?>
                                    <i class="fa fa-check"></i>
                                <?php endif; ?>
                            </td>
                            <td class="userfront">
                                <?php if( $row->viewed ==1 ):?>
                                    <i class="fa fa-eye"></i>
                                <?php else : ?>
                                    <i class="fa fa-eye-slash red"></i>
                                <?php endif; ?>
                            </td>
                            <?php if ( $close_allow ): ?>
                                <td align="center" class="userfront" ><?php echo JHTMLJobMg::JobClose($row,$i) ?></td>
                            <?php endif; ?>
                        </tr>
                <?php
                endforeach;
                else : ?>
                    <tr><td class="text-center" colspan="10">Không có công việc nào</td></tr>
                <?php endif; ?>
                </tbody>
            </table>


            <input type="hidden" name="option" value="com_job_management" />
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="view" value="jobs" />
            <input type="hidden" name="boxchecked" value="0" />
            <input type="hidden" name="redirect" value="<?php echo $redirect;?>" />
            <input type="hidden" name="filter_order" value="<?php echo $lists['order']; ?>" />
            <input type="hidden" name="filter_order_Dir" value="<?php echo $lists['order_Dir']; ?>" />
            <?php echo JHTML::_( 'form.token' ); ?>
        </form>
</div>