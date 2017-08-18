<?php
defined('_JEXEC') or die('Restricted access');
$return = "index.php?option=com_job_management";
$view = JRequest::getCmd( 'view' );
if( strlen($view) > 0 ){
    $return .= "&view=$view";
}
$task = JRequest::getCmd( 'task' );
if( strlen($task) > 0 ){
    $return .= "&task=$task";
}
$layout = JRequest::getCmd( 'layout' );
if( strlen($layout) > 0 ){
    $return .= "&layout=$layout";
}
?>

<form action="<?php echo JRoute::_( 'index.php?option=com_user'); ?>" method="post" name="login" id="form-login"  class="k_horiz">
	<span class="input">
		<span>
			<label for="modlgn_username"><?php echo JText::_('Username') ?></label>
			<input id="modlgn_username" type="text" name="username" class="inputbox" alt="username" size="18" />
		</span>
		<span>
			<label for="modlgn_passwd"><?php echo JText::_('Password') ?></label>
			<input id="modlgn_passwd" type="password" name="passwd" class="inputbox" size="18" alt="password" />
		</span>
		<?php if(JPluginHelper::isEnabled('system', 'remember')) : ?>

		<span id="form-login-remember">
			<label for="modlgn_remember"><?php/* echo JText::_('Remember me') */?></label>
			<input id="modlgn_remember" type="checkbox" name="remember" class="inputbox" value="yes" alt="Remember Me" />
		</span>

		<?php endif; ?>
		<span>
    		<input type="submit" name="Submit" class="fb_button_go" value="<?php echo JText::_('LOGIN') ?>" />
			<input type="hidden" name="option" value="com_user" />
			<input type="hidden" name="task" value="login" />
			<input type="hidden" name="return" value="<?php echo base64_encode($return); ?>" />
		<?php echo JHTML::_( 'form.token' ); ?>
		</span>

	</span>
</form>