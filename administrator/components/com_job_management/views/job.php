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

        // do field validation
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

<form action="index.php" method="post" name="adminForm">

    <table cellspacing="0" cellpadding="0" border="0" width="100%">
        <tr>
            <td valign="top">
                <table  class="adminform">
                    <tr>
                        <td>
                            <label for="title">
                                <?php echo JText::_( 'Tên công việc' ); ?>
                            </label>
                        </td>
                        <td>
                            <input class="inputbox" type="text" name="title" id="title" size="40" maxlength="255" value="<?php echo $row->title; ?>" />
                        </td>
                        <td>
                            <label>
                                <?php echo JText::_( 'Published' ); ?>
                            </label>
                        </td>
                        <td>
                            <?php echo $lists['status']; ?>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <label for="sectionid">
                                <?php echo JText::_( 'Job Group' ); ?>
                            </label>
                        </td>
                        <td>
                            <?php echo $lists['groupid']; ?>
                        </td>

                    </tr>
                </table>
                <table class="adminform">
                    <tr>
                        <td>
                            <?php
                            // parameters : areaname, content, width, height, cols, rows
                            echo $editor->display( 'content',  $row->text , '100%', '550', '75', '20' ) ;
                            ?>
                        </td>
                    </tr>
                </table>
            </td>
            <td valign="top" width="320" style="padding: 7px 0 0 5px">
                <?php
                JobManagementView::_displayArticleStats($row, $lists);

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
    <input type="hidden" name="cid[]" value="<?php echo $row->id; ?>" />
    <input type="hidden" name="version" value="<?php echo $row->version; ?>" />
    <input type="hidden" name="mask" value="0" />
    <input type="hidden" name="option" value="<?php echo $option;?>" />
    <input type="hidden" name="task" value="" />
    <?php echo JHTML::_( 'form.token' ); ?>
</form>
<?php
echo JHTML::_('behavior.keepalive');