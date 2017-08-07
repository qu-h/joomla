<form action="index.php" method="post" name="adminForm">

    <table cellspacing="0" cellpadding="0" border="0" width="100%">
        <tr>
            <td valign="top">
                <table  class="adminform">
                    <tr>
                        <td>
                            <label for="title">
                                <?php echo JText::_( 'Tên Nhóm công việc' ); ?>
                            </label>
                        </td>
                        <td>
                            <input class="inputbox" type="text" name="title" id="title" size="40" maxlength="255" value="<?php echo $row->title; ?>" />
                        </td>

                    </tr>

                    <tr>
                        <td>
                            <label>
                                <?php echo JText::_( 'Published' ); ?>
                            </label>
                        </td>
                        <td>
                            <?php echo $lists['status']; ?>
                        </td>

                    </tr>
                </table>

            </td>
            <td valign="top" width="320">
                <table width="100%">
                    <?php
                    if ( $row->id ) {
                        ?>
                        <tr>
                            <td>
                                <strong><?php echo JText::_( 'Group ID' ); ?>:</strong>
                            </td>
                            <td>
                                <?php echo $row->id; ?>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                    <tr>
                        <td>
                            <strong><?php echo JText::_( 'Status' ); ?></strong>
                        </td>
                        <td>
                            <?php echo $row->status > 0 ? JText::_( 'Published' ) : ($row->status < 0 ? JText::_( 'Archived' ) : JText::_( 'Draft Unpublished' ) );?>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <strong><?php echo JText::_( 'Created' ); ?></strong>
                        </td>
                        <td>
                            <?php
                            if ( $row->created == $nullDate ) {
                                echo JText::_( 'New document' );
                            } else {
                                echo JHTML::_('date',  $row->created,  JText::_('DATE_FORMAT_LC2') );
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong><?php echo JText::_( 'Modified' ); ?></strong>
                        </td>
                        <td>
                            <?php
                            if ( $row->modified == $nullDate ) {
                                echo JText::_( 'Not modified' );
                            } else {
                                echo JHTML::_('date',  $row->modified, JText::_('DATE_FORMAT_LC2'));
                            }
                            ?>
                        </td>
                    </tr>
                </table>
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
<?=JHTML::_('behavior.keepalive');?>