<?php /* Smarty version 2.6.7, created on 2011-09-12 23:22:17
         compiled from user/login_form.tpl.html */ ?>

<!-- Template: user/login_form.tpl.html Start 12/09/2011 23:22:17 --> 
 <?php echo '
<script type="text/javascript">
    function validate_login(){
		validator=$("#loginform").validate({
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
</script>
'; ?>

<div id="logintable">
<!--<div id="new_user">
<h1>New Users</h1>
<div style="height:182px"><p>Text to be put here...</p></div>
<div class="create_account"><a href="http://memeja.com/user/register">Create An Account</a></div>
</div>
-->
<div id="register_user">
<h1>Registered Users</h1>
<p>If you have an account with us, please log in.</p>
<form id="loginform" name="loginform" action="http://memeja.com/user/set_login" method="post" onsubmit="return validate_login()">
<table class="userinfo" width="100%">
	<tr>
	  <td colspan="2">Username :</td>
	  </tr>
	<tr>
    	<td colspan="2">
    	  <input type="text" name="username" id="uname" class="fields" />
  	  </td>
        </tr>
    <tr>
      <td colspan="2">Password :</td>
      </tr>
    <tr>
	<td colspan="2">
	<input type="password" name="password" id="pass" class="fields" />
	</td>
    </tr>
    <!--<tr>
	<td class="tl" colspan="2"><input type="checkbox" name="rem" id="rem" value="1"/>Remember Me</td>
    </tr>
    <tr>
     	<td colspan="2"><div id="load_captcha"></div></td>
    </tr>
    <tr>
     	<td colspan="2"></td>
    </tr>-->
    <tr>
    	<td valign="middle"><a href="http://memeja.com/user/forgot_pwd">Forgot Your Password?</a></td>
        <td valign="middle" class="tr"><input type="submit" value="Login" class="inputbtn"/></td>
    </tr>
    <tr>
        <td colspan="2">
	    <fb:login-button perms="email,user_checkins,publish_stream">
		Login with Facebook
	    </fb:login-button>
	</td>
    </tr>
</table>
</form>
</div>
</div>
<div class="clear"></div>
<div id="fb-root"></div>
<?php echo '
<script type="text/javascript">
    FB.Event.subscribe(\'auth.login\', function (response) {
          window.location = "http://memeja.com/user/facebook_info";
     });

</script>
'; ?>


<!-- Template: user/login_form.tpl.html End --> 