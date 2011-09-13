<?php /* Smarty version 2.6.7, created on 2011-09-08 22:49:14
         compiled from admin/userlogin.tpl.html */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Log In · Memeje Admin</title>
<?php $this->assign('css', $this->_tpl_vars['util']->get_values_from_config('ADMIN_THEME')); ?>
<link rel="stylesheet" type="text/css" href="http://memeja.com/templates/css_theme/css/<?php echo $this->_tpl_vars['css']['css']; ?>
.css" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.js"></script>
<script type="text/javascript" src="http://memeja.com/libsext/js/jquery.validate.js"></script>
<script type="text/javascript" src="http://memeja.com/templates/flexyjs/flexymessage.js"></script>
<?php echo '
<script type="text/javascript">
$(document).ready(function() {
	$(\'#uname\').focus();	
 });
function open_div() {
	document.getElementById(\'forgot_pass\').style.display=\'\';
	document.getElementById(\'inner_div\').style.display=\'none\';
	$(\'#adm_mess\').html(\'\');
	$(\'#usr_name\').val(\'\');
 }
function open_log_div() {
	if(document.getElementById(\'inner_div\').style.display ==\'none\') {
		document.getElementById(\'forgot_pass\').style.display=\'none\';
		document.getElementById(\'inner_div\').style.display=\'\';
	 }else {
		return false;
	 }
 }
function validate_login(){
	var validator=$("#login_form").validate({
	   	rules: {
		   	username: {
				required:true
			 },
			password:{
				required: true,
				minlength: 6
			 }
		 },
		messages: {
			username:{
				required:flexymsg.required

			 },
			password:{
				required:flexymsg.required,
				minlength:flexymsg.minlength
			 }
		 }
	 });
	x=validator.form();
	return x;
 }
function forgot_pass() {
	var url="http://memeja.com/flexyadmin/index.php";
	//var usr_name = $(\'#usr_name\').val();
	var validator=$("#lost_pass").validate({
	   	rules: {
			usr_name: {
				required:true,
				email:true
			 }
		 },
		messages: {
			usr_name:{
				required:flexymsg.required,
				email:flexymsg.email
			 }
		 }
	 });
	x=validator.form();
	return x;
	//if(x) {
		//$.post(url,{"page":"user","ce":"0","choice":"recover_pass","usr_name":usr_name },function(res){
			//open_log_div();
			//$(\'#adm_mess\').html(res);
		// });
	// }
 }
</script>
'; ?>

</head>
    <body id="login">

		<div class="box box-50 altbox">
			<div class="boxin">
				<div class="header">
					<h3>Memeje Admin</h3>
					<ul>
						<li><a href="javascript:void(0);" onclick="return open_log_div();">login</a></li><!-- .active for active tab -->
						<li><a href="javascript:void(0);" onclick="open_div();">lost password</a></li>
					</ul>
				</div>
                <div id="adm_mess" align="center">
                </div>		
				<div class="inner-form" id="inner_div">
				   <form class="table" id="login_form" action="http://memeja.com/user/set_login/ce/0" method="post" onsubmit="return validate_login();"><!-- Default forms (table layout) set_login  LBL_ADMIN_SITE_URL-->
                	<input type="hidden" name="admin_st" id="admin_st" value="1" />
						<div class="msg msg-info">
							<p>Just click <strong>Log in</strong>.</p>
						</div>
						<div>
                        				<font color="red"><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/messages.tpl.html", 'smarty_include_vars' => array('module' => 'global')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></font>
                        			</div>
						<table cellspacing="0">
							<tr>
								<th><label for="some1">Username:</label></th>
								<td><input class="txt" type="text" id="uname" name="username" /></td>
							</tr>
							<tr>
								<th><label for="some3">Password:</label></th>
								<td><input class="txt pwd" type="password" id="pass" name="password" /></td><!-- class error for wrong filled inputs -->
							</tr>
							<tr>
								<th></th>
								<td class="tr proceed">
									<input class="button" type="submit" value="Log In" />
								</td>
							</tr>
						</table>
                   		</form>
		         </div>
		
                    <div id="forgot_pass" style="display:none">
			<form id="lost_pass" method="post" action="http://memeja.com/flexyadmin/user/recover_pass/ce/0" onsubmit="return forgot_pass();">
						<table cellspacing="0">
							<tr>
                                    <div class="msg msg-info">
                                        <p><strong>Enter your Email Id</strong></p>
                                    </div>
								<td><input class="txt" type="text" id="usr_name" name="usr_name" /></td><!-- class error for wrong filled inputs -->
							</tr>
							<tr>
								<td class="tl proceed"><input class="button" type="submit" value="Submit" onclick="return forgot_pass();"/>
								</td>
							</tr>
						</table>
			</form>
                    </div>			
			</div>
		</div>
    </body>
</html>