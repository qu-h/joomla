<script language="javascript" type="text/javascript">
    <!--
    var sectioncategories = new Array;
    <?php
    $i = 0;
    foreach ($sectioncategories as $k=>$items) {
        foreach ($items as $v) {
            echo "sectioncategories[".$i++."] = new Array( '$k','".addslashes( $v->id )."','".addslashes( $v->title )."' );\n\t\t";
        }
    }
    ?>

    function submitbutton(pressbutton)
    {
        var form = document.adminForm;

        if ( pressbutton == 'menulink' ) {
            if ( form.menuselect.value == "" ) {
                alert( "<?php echo JText::_( 'Please select a Menu', true ); ?>" );
                return;
            } else if ( form.link_name.value == "" ) {
                alert( "<?php echo JText::_( 'Please enter a Name for this menu item', true ); ?>" );
                return;
            }
        }

        if (pressbutton == 'cancel') {
            submitform( pressbutton );
            return;
        }
        if( pressbutton =="updateformval" ){
            submitform( pressbutton );
            return true;
        }
        var text = <?php echo $editor->getContent( 'content' ); ?>
        if (form.title.value == ""){
            alert( "<?php echo JText::_( 'Phải nhập vào tên công việc', true ); ?>" );
        } else if (form.groupid.value == "-1"){
            alert( "<?php echo JText::_( 'Phải chọn nhóm công việc', true ); ?>" );
        } else {
            <?php
            echo $editor->save( 'content' );
            ?>
            submitform( pressbutton );
        }
    }
    //-->
</script>
<?php
JHTML::stylesheet("css/bootstrap.min.css","components/com_job_management/assets/");
$controllerName = JRequest::getCmd( 'c', 'job' );
?>
<form action="index.php" method="post" name="adminForm">


    <table cellspacing="0" cellpadding="0" border="0" width="100%">
        <tr>
            <td valign="top adminform ">
                <div class="row p-3">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label><?php echo JText::_( 'Tên công việc' ); ?></label>
                            <input class="form-control" type="text" name="title" id="title"  value="<?php echo $row->title; ?>" placeholder="Tên công việc" />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?php echo JText::_( 'Nhóm việc' ); ?></label>
                            <?php echo JHTMLJobMg::CompanySelelect($row->companyid,"companyid",true); ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?php echo JText::_( 'Nhóm việc' ); ?></label>
                            <?php echo JHTMLJobMg::GroupSelect($row,true,"groupid"); ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?php echo JText::_( 'Published' ); ?></label>
                            <?php echo JHTMLJobMg::PublishSelelect($row->status,"status"); ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label><?php echo JText::_( 'Select User' ); ?></label>
                            <?php
                            if( !$_POST || empty($_POST) ){
                                JRequest::setVar( 'groupid', $row->groupid );
                            }
                            echo JHTMLJobMg::SelectMultiUsers(NULL,"users",$row->id,'job');
                            ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label><?php echo JText::_( 'Content' ); ?></label>
                            <?php echo $editor->display( 'content',  $row->content , '100%', '550', '75', '20' ) ; ?>
                        </div>

                    </div>
                </div>


            </td>
            <td valign="top" width="320">
                <?php
                //JobManagementView::_displayArticleStats($row, $lists);

                echo $pane->startPane("content-pane");

                echo $pane->startPanel( JText::_( 'Parameters - Job' ), "detail-page" );
                echo $form->render('detail');
                echo $pane->endPanel();

                echo $pane->endPane();
                ?>
            </td>
        </tr>
    </table>

    <input type="hidden" name="id" value="<?php echo $row->id; ?>" />
    <input type="hidden" name="cid[]" value="0" />
    <input type="hidden" name="mask" value="0" />
    <input type="hidden" name="option" value="<?php echo JRequest::getCmd( 'option' );?>" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="c" value="job" />
    <?php echo JHTML::_( 'form.token' ); ?>
</form>
<?php
echo JHTML::_('behavior.keepalive');