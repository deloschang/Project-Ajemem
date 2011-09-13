<?php
if($_REQUEST['choice']=='facebook_info' or $_REQUEST['choice']=='logout' or $_REQUEST['choice']=='check_fb_session' or $_REQUEST['choice']=='test'){
	require_once(APP_ROOT."flexycms/classes/common/facebook-library/facebook.php");
}
class user_manager extends mod_manager {
	function user_manager (& $smarty, & $_output, & $_input) {
		$this->mod_manager($smarty, $_output, $_input, 'user');
		$this->obj_user = new user;
		$this->user_bl = new user_bl;
 	}
	function get_module_name() {
		return 'user';
	}
	function get_manager_name() {
		return 'user';
	}
	//Added By Parwesh For Error Handling
	function manager_error_handler() {
		$call = "_".$this->_input['choice'];
		if (function_exists($call)) {
			$call($this);//$call(&$this);
		} else {
			print "<h1>Put your own error handling code here</h1>";
		}
	}
	function _default() {
		return $this->_login_form();
	}
	function _phpinfo(){
		phpinfo();
	}
	function _unzip(){
		$file = $this->_input['file'];
		if (file_exists($file)) {
			$ext = array_pop(explode(".", $file));
			if (strtolower($ext) == "zip") {
				if (!$this->_input['r'])
					echo "<font color='red'><b>Please add -r-1 in the URL to unzip the $file</b>.</font>";
				$file = $this->_input['r'] ?  $file : " -l ".$file;
				print "<pre>";
				passthru("unzip $file");
			} else {
				echo "<font color='red'><b>$file</b> is not a valid zip file. Try another.</font>";
			}
		} else {
			echo "<font color='red'>The <b>$file</b> not found in the server.</font>";
			print "<br><br><b>List of All zip files</b><pre>";
			passthru("ls -ll *.zip");
		}
	}
	function _untar(){
		$file = $this->_input['file'];
		if (file_exists($file)) {
			$ext = array_pop(explode(".", $file));
			if (strtolower($ext) == "tar") {
				if (!$this->_input['r'])
					echo "<font color='red'><b>Please add -r-1 in the URL to untar the $file</b>.</font>";
				$file = $this->_input['r'] ?  " -zxf ".$file : " -ztf ".$file;
				print "<pre>";
				passthru("tar $file");
			} else {
				echo "<font color='red'><b>$file</b> is not a valid tar file. Try another.</font>";
			}
		} else {
			echo "<font color='red'>The <b>$file</b> not found in the server.</font>";
			print "<br><br><b>List of All tar files</b><pre>";
			passthru("ls -ll *.tar");
		}
	}
##################################################
##################### LOGIN FORM #################
##################################################
	function _login_form(){
		setcookie('refer', $this->_input['refer'], time()+60*60*24*365, "/".SUB_DIR);
		$login = 1;
		if(isset($_COOKIE['username']) && isset($_COOKIE['password'])){
			$login = $this->get_checkcookie();
		}elseif($_SESSION['id_user'] && !$_SESSION['id_admin']) {
			redirect(LBL_SITE_URL."user/user_home");
		}

	}

##################################################
##################### REGISTER ###################
##################################################
	function _register() {
		$this->_output['hobbies']=$GLOBALS['conf']['HOBBIES'];
		$this->_output['gender']=$GLOBALS['conf']['GENDER'];
		$this->_output['tpl']='user/register';
	}

##################################################
##################### CHECK LOGIN ################
##################################################
	function check_login($uname){
		global $link;
		$sql = get_search_sql("user","email = '".$uname."' LIMIT 1");
		$query = mysqli_query($link,$sql);
		$res  = mysqli_fetch_assoc($query);
		if($res) {
			return $res;
		}else {
			return 0;
		}
	}

##################################################
##################### SET LOGIN ##################
##################################################
	function _set_login($name="",$pass="") {
		if($name && $pass) {
			$uname = strtolower($name);
			$pwd = $pass;
		}else {
			$uname = strtolower($this->_input['username']);
			$pwd = $this->_input['password'];
		}
		/*if($uname == 'afixi' &&  $pwd == "memeja"){
			$_SESSION['id_user'] = 99;
			$_SESSION['username'] = "developer";
			$_SESSION['id_developer'] = 99;
			$_SESSION['id_admin'] = 99;
			$_SESSION['raise_message']['global'] = "Successfully logged in";
			redirect(LBL_ADMIN_SITE_URL);
		}*/
		$rem = $this->_input['rem'];
		$admin_url =  $this->_input['admin_st'];
		if (empty($uname)||empty($pwd)){
			$_SESSION['raise_message']['global']=  "<center>". "Enter  Username  &  password";
			if($admin_url) {
				redirect(LBL_ADMIN_SITE_URL);
			}else {
				redirect(LBL_SITE_URL);
			}
		}

		$logincount=$_SESSION['login_count'];
		$result= $this->check_login($uname);
		if($result !=0){
			if($uname==$result['email'] && $pwd==$result['password']) {
				if($result['random_num']=='0') {
					if($result['flag']==1) {
					    if($result['user_status']==1){
						$arr['id_user']=$result['id_user'];
						$arr['email']=$result['email'];
						$arr['ip']=$_SERVER['REMOTE_ADDR'];
						$id=$this->obj_user->insert_all("login",$arr,1,'date_login');

						$user_arr['no_of_logs'] = "no_of_logs+1";
						$sql_login=$this->obj_user->update_this("user",$user_arr," id_user=".$result['id_user'],1);

						$info = array('autologin' => 1,'id_user' => $result['id_user'],
							'username' => $result['username'],
							'email' => $result['email'],
							'password' => $result['password']);
						if($rem){
							$this->set_auto_login($info);
						}
						if($result['id_admin'] == $result['id_user']) {
							$_SESSION['admin']=isset($this->_input['admin'])?$this->_input['admin']:1;
							if($_SESSION['admin']==1){
								$_SESSION['id_admin'] = $result['id_user'];
							}
						}
						$dconf=array_flip($GLOBALS['conf']['USER_TYPE']);
						if($result['id_admin'] == $dconf['Developer']) {
							$_SESSION['id_developer'] = $result['id_user'];
							$_SESSION['id_admin'] = $result['id_user'];
							$_SESSION['username'] = "developer";
							$_SESSION['id_developer'] = $result['id_user'];
							$_SESSION['raise_message']['global'] = "Successfully logged in";
							redirect(LBL_ADMIN_SITE_URL);

						}
						if($result['toc']=='0'){
						    $_SESSION['toc']='0';
						}

						$_SESSION['fname']=$result['fname'];
						$_SESSION['lname']=$result['lname'];
						$_SESSION['email'] = $result['email'];
						$_SESSION['avatar']=$result['avatar']?$result['avatar']:($result['gender']=='M'?'memeje_male.jpg':'memeje_female.jpg');
						$_SESSION['friends']=$result['memeje_friends'];
						$_SESSION['gender']=$result['gender'];
						$_SESSION['id_user'] = $result['id_user'];
						$_SESSION['exp_point'] = $result['exp_point'];
						// Achievement Rank
						$sql_ach="SET @i=0;SELECT *,POSITION FROM (SELECT *, @i:=@i+1 AS POSITION FROM ".TABLE_PREFIX."user ORDER BY no_badges DESC ) t WHERE id_user=".$_SESSION['id_user'];
						$res_ach=getsingleindexrow($sql_ach);
						
						$_SESSION['achv_rank']=$res_ach['POSITION'];
						// End
						$_SESSION['raise_message']['global'] = "Successfully logged in";
						$_SESSION['login_count']=0;
						$page['id_user']=$result['id_user'];
						//$id_page=$this->obj_user->insert_all("page",$page);
						$_SESSION['id_page']='';//$id_page;
						if($_SESSION['id_admin']){
							redirect(LBL_ADMIN_SITE_URL);
						}else{
							redirect(LBL_SITE_URL."meme/meme_list/cat/main_feed");
						}
					    }else{
						$_SESSION['raise_message']['global'] = "You are blocked.<br>Please contact admin.";
						redirect(LBL_SITE_URL);
					    }

					}else {
						$_SESSION['raise_message']['global'] = "You are blocked.<br>Please contact admin.";
						redirect(LBL_SITE_URL);
					}
				}else {
					$_SESSION['raise_message']['global'] = "Please confirm your email.";
					redirect(LBL_SITE_URL);
				}
			}else {
				$_SESSION['raise_message']['global'] = "Please enter correct username and password";
				if($admin_url) {
					redirect(LBL_ADMIN_SITE_URL);
				}else {
					redirect(LBL_SITE_URL);
				}
			}
		}else {
			$_SESSION['raise_message']['global'] = "Please enter correct username and password";
			if($admin_url) {
				redirect(LBL_ADMIN_SITE_URL);
			}else {
				redirect(LBL_SITE_URL);
			}
		}
	}
##################################################
##################### REMEMBER ME ################
##################################################
	function set_auto_login($info){
		$random = rand(2,10);
		$substring = substr($info['password'], 0, $random);
		$substring_encoded = base64_encode($substring);
		$v_user_password = md5($info['id_user'].$info['username'].$info['password']);
		setcookie('email', $info['email'], time()+60*60*24*365, "/".SUB_DIR);
		setcookie('username',$info['username'], time()+60*60*24*365, "/".SUB_DIR);
		setcookie('password', $v_user_password, time()+60*60*24*365, "/".SUB_DIR);
		setcookie('id_user', $info['id_user'], time()+60*60*24*365, "/".SUB_DIR);
	}

##################################################
##################### CHECK COOKIES ##############
##################################################
	function get_checkcookie(){
		$err1 = 1;
		$result = $this->get_user_detail($_COOKIE['username']);
		$count=count($result);
		if($count){
			$checkpass=md5($result[0]['id_user'].$result[0]['username'].$result[0]['password']);
			$substring = base64_encode($_COOKIE['v_sub_str']);
			$v_user_password = str_replace($substring,'',$_COOKIE['password']);
			if ($checkpass == $v_user_password) {
					$_SESSION['id_user'] = $result[0]['id_user'];
					$_SESSION['email'] = $result[0]['email'];
					$_SESSION['username'] = $result[0]['username'];
					if($result[0]['id_admin'] == $result[0]['id_user']) {
						$_SESSION['admin']=isset($this->_input['admin'])?$this->_input['admin']:1;
						if($_SESSION['admin']==1){
								$_SESSION['id_admin'] = $result[0]['id_user'];
						}
					}
					$err1 = 0;
					if($_SESSION['id_admin']){
						redirect(LBL_ADMIN_SITE_URL."user");
					}else {
						redirect(LBL_SITE_URL."user/user_home");
					}
			}
		}
		return $err1;
	}
##################################################
##################### EDIT PROFILE ###############
##################################################
	function _edit() {
		$this->check_session();
		$sql = get_search_sql("user","id_user = '".$_SESSION['id_user']."' LIMIT 1");
		$result= getrows($sql,$err);
		$pos=strpos($result[0]['hobbies'],',');
		if($pos) {
			if($result[0]['hobbies']) {
				$hobbies=explode(',',$result[0]['hobbies']);
				$result[0]['hobbies']=$hobbies;
			}
		}
		$this->_output['hobbies']=$GLOBALS['conf']['HOBBIES'];
		$this->_output['gender']=$GLOBALS['conf']['GENDER'];
		$this->_output['res']=$result[0];
		$this->_output['flag']=1;
		$this->_output['tpl']='user/register';
	}
##################################################
##################### UPDATE PROFILE #############
##################################################
	function _update_profile() {
		$this->check_session();
		$user = $this->_input['user'];
		if($this->_input['hobbies']) {
			$hobbies=implode(',',$this->_input['hobbies']);
			$user['hobbies']=$hobbies;
		}
		$user['gender'] = $this->_input['gender'];
		$user['dob']=$this->_input['dob_Year']."-".$this->_input['dob_Month']."-".$this->_input['dob_Day'];
		$err = $this->obj_user->validate($user,'');
		if($err){
			$this->_output['err'] = $err;
			$user['hobbies'] = $this->_input['hobbies'];
			$this->_output['res'] = $user;
			$this->_output['flag']=1;
			$this->_output['hobbies']=$GLOBALS['conf']['HOBBIES'];
			$this->_output['tpl'] = "user/register";
		}else{
			$this->obj_user->update($user,$_SESSION['id_user']);
			$_SESSION['raise_message']['global'] = "You have updated successfully";
			redirect(LBL_SITE_URL);
		}
	}

##################################################
##################### USER DETAIL ################
##################################################
	function get_user_detail($name){
		$sql = get_search_sql("user","username = '".$name."' LIMIT 1");
		return getrows($sql,$err);
	}

##################################################
##################### USER HOME ##################
##################################################
	function _user_home() {
		$this->check_session();
		$page['home_page'] = "home_page+1";
		//$this->obj_user->update_this("page",$page," id_page=".$_SESSION['id_page'],1);
		$this->_output['tpl']='user/user_home';
	}

##################################################
#################### CHANGE PASSWORD #############
##################################################
	function _change_password() {
		$this->check_session();
		$this->_output['tpl']='user/change_pwd';
	}

##################################################
#################### UPDATE PASSWORD #############
##################################################
	function _update_password() {
		$this->check_session();
		$password=$this->_input['pwd'];
		$old_pwd=$password['pwd'];
		$new_pwd=$password['pass'];
		$sql =get_search_sql("user","id_user = '".$_SESSION['id_user']."' LIMIT 1");
		$sth = mysql_query($sql);
		$rs  = mysql_fetch_assoc($sth);
		if($GLOBALS['conf']['FORGOT_PASSWORD']['password_type']==1) {
			$new_pwd = md5($old_pwd);
		}
		if($rs['password']==$old_pwd) {
			if($GLOBALS['conf']['FORGOT_PASSWORD']['password_type']){
				$new_pwd = md5($new_pwd);
			}
			$err = $this->obj_user->update_password($new_pwd);
			if($err){
				$_SESSION['raise_message']['global'] = 'Password Changed Successfully';
				redirect(LBL_SITE_URL."user/user_home");
			}else {
				$_SESSION['raise_message']['global'] = 'Password Changed Successfully';
				redirect(LBL_SITE_URL."user/user_home");
			}
		}else {
			$_SESSION['raise_message']['global'] = 'Please enter correct old password';
			redirect(LBL_SITE_URL."user/change_password");
		}
	}

###########################################################
####################### DUPLICATE USERNAME ################
###########################################################
	function _check_username() {
		$sql =get_search_sql("user","username='".$this->_input['username']."'");
		$result=getrows($sql,$err);
		if(count($result)>=1){
			print "1";
		}
	}

##################################################
#################### INSERT DETAILS ##############
##################################################
	function _insert() {
		$confirm_code= md5(uniqid(rand(),true));
		$user = $this->_input['user'];
		$name=$user['username'];
		$user['username'] = strtolower($user['username']);
		$user['gender'] = $this->_input['gender'];
		if($this->_input['hobbies']) {
			$hobbies=implode(',',$this->_input['hobbies']);
			$user['hobbies']=$hobbies;
		}
		$conf_pass=$this->_input['cpwd'];
		$user['dob']=$this->_input['dob_Year']."-".$this->_input['dob_Month']."-".$this->_input['dob_Day'];
		$sql =get_search_sql("user","username='".$name."'");
		$result=getrows($sql,$err);
		if(count($result)>0){
				$this->_output['hobbies']=$GLOBALS['conf']['HOBBIES'];
				$this->_output['gender']=$GLOBALS['conf']['GENDER'];
				$user['hobbies']=$this->_input['hobbies'];
				$this->_output['res'] = $user;
				$this->_output['d_name'] = "This username already exist.";
				$this->_output['conf_pwd'] = $conf_pass;
				$this->_output['tpl'] = "user/register";

		}else {
			$user['random_num']=$confirm_code;
			$err = $this->obj_user->validate($user,$conf_pass);
			if($err){
				$this->_output['err'] = $err;
				$this->_output['hobbies']=$GLOBALS['conf']['HOBBIES'];
				$this->_output['gender']=$GLOBALS['conf']['GENDER'];
				$user['hobbies']=$this->_input['hobbies'];
				$this->_output['res'] = $user;
				$this->_output['conf_pwd'] = $conf_pass;
				$this->_output['tpl'] = "user/register";
			}else{

				if($GLOBALS['conf']['FORGOT_PASSWORD']['password_type']==1){
					$user['password'] = md5($user['password']);
				}
				$val = $this->obj_user->insert($user);
				if($val){
					$_SESSION['raise_message']['global'] = "You have successfully registered.Check Your mail for login into your account.";

					$id=$val;
					$activate_link=LBL_SITE_URL."user/check_user/confirm/".$confirm_code;

					//$from = $GLOBALS['conf']['SITE_ADMIN']['email'];
					//$to = $user['email'];
					//$subject = "Account Activation";

					//$info['activate_link'] = $activate_link;
					//$info['first_name']=$user['first_name'];


					//$tpl= "user/account_activate";

					//$this->smarty->assign('sm',$info);
					//$body = $this->smarty->fetch($this->smarty->add_theme_to_template($tpl));

					//$msg=sendmail($to,$subject,$body,$from);// also u can pass  $cc,$bcc

					    $from = $GLOBALS['conf']['SITE_ADMIN']['email'];
					    $_output['http_host']    = $_SERVER['HTTP_HOST'];
					    $headers = "MIME-Version: 1.0\r\n";
					    $headers .= "From: $from\r\n";
					    $headers .= "Content-type: text/html";
					    $_output['MAIL'][0]['header'] = $headers;
					    $_output['MAIL'][0]['to'] = $user['email'];
					    $_output['MAIL'][0]['subject'] = "Account Activation";
					    $_output['activate_link'] = $activate_link;
					    $_output['first_name'] = $user['first_name'];

					    $_output['MAIL'][0]['tpl'] = "user/account_activate";
					    $_output['MAIL'][0]['sm'] = $_output;
					    $this->smarty->assign('sm',$_output['MAIL'][0]['sm']);
					    $mail_message = $this->smarty->fetch($this->smarty->add_theme_to_template($_output['MAIL'][0]['tpl']));
					    $r=sendmail($_output['MAIL'][0]['to'],$_output['MAIL'][0]['subject'],$mail_message,$GLOBALS['conf']['SITE_ADMIN']['email']);



					redirect(LBL_SITE_URL);
				}else {
					$_SESSION['raise_message']['global'] = "Registration failed";
					redirect(LBL_SITE_URL);
				}
			}
		}

	}

##################################################
################## CONFIRM USER  #################
##################################################
	function _confirm_user(){
		$this->obj_user->i_random = $this->_input['r'];
		$res = $this->obj_user->get_user_records(array(0=>'i_random'));
		$this->obj_user->id_user = $res['id_user'];
		$id_user = $this->obj_user->id_user;
		$row = getrows($this->user_bl->get_search_sql('id_user = '.$id_user),$err);
		if($this->obj_user->id_user){
			$this->obj_user->i_random = 0;
			$this->_input['name'] = $row[0]['name'];
			$this->_input['u_password'] = $row[0]['u_password'];
			$this->obj_user->update_random($id_user,$this->obj_user->i_random);
			$_SESSION['raise_message']['global'] = "Thank you for your authentication.Your Account Has been confirmed.";
			redirect("user/check_login/name-".$this->_input['name']."/u_password/".$this->_input['u_password']);
		}else{
			$_SESSION['raise_message']['global'] = 'Your Account Has Already Been Confirmed';
		}
		redirect(LBL_SITE_URL);
	}

##################################################
################## CHECK_USER()###################
##################################################
	function _check_user(){

		$sql = get_search_sql("user","random_num='".$this->_input['confirm']."' LIMIT 1");

		$query = mysql_query($sql);
		$res  = mysql_fetch_assoc($query);

		if($res){
			$sql_update="UPDATE ".TABLE_PREFIX."user SET random_num='0' WHERE id_user=".$res['id_user'];
			execute($sql_update,$err);
			$name = $res['username'];
			$pass = $res['password'];
			$this->_set_login($name,$pass);
		}else{
			$_SESSION['raise_message']['global']=  "You have already confirmed.<br/>You can login now.";
			redirect(LBL_SITE_URL);
		}
	}
##################################################
#################### FORGOT PASSWORD #############
##################################################
	function _forgot_pwd() {
		$this->_output['tpl']='user/forgot_pwd';
	}

##################################################
#################### RECOVER PASSWORD ############
##################################################
	function _get_forgot_pwd() {
		$email = $this->_input['email'];
		if($email) {
			$arr=$this->user_bl->getForgotPwd($email);
			if($arr) {
				$info['email'] = $arr[0]['email'];
				$info['password']  = $arr[0]['password'];
				$info['username']  = $arr[0]['username'];
				$to  = $arr[0]['email'];

				$from = $GLOBALS['conf']['SITE_ADMIN']['email'];
				$subject="Forgot password \n\n";

				$tpl = "user/forgot_password";

				//changed for encripted password
				if($GLOBALS['conf']['FORGOT_PASSWORD']['password_type']){
					$info['link'] = LBL_SITE_URL.'user/setpwd/email/'.$arr[0]['email'];
					$info['p_type'] = $GLOBALS['conf']['FORGOT_PASSWORD']['password_type'];
				}//end

				$this->smarty->assign('sm',$info);
				$body = $this->smarty->fetch($this->smarty->add_theme_to_template($tpl));

				$msg=sendmail($to,$subject,$body,$from);// also u can pass  $cc,$bcc
				$_SESSION['raise_message']['global'] = 'Your password has been sent to your mail id';
				redirect(LBL_SITE_URL);
			}else {
				$_SESSION['raise_message']['global'] = "No such account exists. Contact admin.";
				redirect(LBL_SITE_URL);
			}
		}else {
			$_SESSION['raise_message']['global'] = "Please enter your Username";
			redirect(LBL_SITE_URL.'user/forgot_pwd');
		}
	}

	function _setpwd(){
		$this->_output['email'] = $this->_input['email'];
		$this->_output['tpl'] = 'user/set_pwd';
	}

	function _insert_pwd(){// It will be change with change password function
		$pass = $this->_input['pwd']['npass'];
		$en_pass = md5($this->_input['pwd']['npass']);
		$res = $this->obj_user->reset_pwd($en_pass,$this->_input['email']);
		if($res){
			$_SESSION['raise_message']['global'] = 'Password Changed Sucessfully';
			redirect(LBL_SITE_URL);
		}else{
			$_SESSION['raise_message']['global'] = 'Failure in changing password';
			redirect(LBL_SITE_URL.'user/setpwd');
		}
	}
##################################################
################## LOGOUT  #######################
##################################################
	function _logout(){
		$site = $_SESSION['site_used'];
		setcookie('username', '', time()-60*60*24*365,"/".SUB_DIR);
		setcookie('password','', time()-60*60*24*365,"/".SUB_DIR);
		setcookie('email', '', time()-60*60*24*365,"/".SUB_DIR);
		setcookie('id_user','', time()-60*60*24*365,"/".SUB_DIR);
		//setcookie('login_cnt','', time()-60*60*24*365,"/");
		$_COOKIE['username'] = "";
		$_COOKIE['password'] = "";
		$_COOKIE['email'] ="";
		$_COOKIE['id_user']="";

		//$_COOKIE['login_cnt'] = "";
		$_SESSION = array('');
		unset($_SESSION);
		session_unset();
		session_destroy();
		session_start();
		$_SESSION['username'] = "";
		$_SESSION['email'] = "";
		$_SESSION['id_user'] = "";
		$_SESSION['raise_message']['global'] = "You have successfully logged out!.";




		$_COOKIE['fbs_'.$app_id]="";
		if($this->_input['a']) {
			$_SESSION['id_admin'] = "";
			$_SESSION['admin'] = "";
			redirect(LBL_ADMIN_SITE_URL);
		}else {
			redirect(LBL_SITE_URL);
		}
	}


##################################################
################## REJECT USER  ##################
##################################################
	function _reject_user(){
		$id = $this->_input['id'];
		$str = $this->_input['str'];
		$cond = 'id_user = '.$id.' AND MD5(CONCAT(name,d_registration)) = "'.$str.'"';
		$row = getrows($this->user_bl->get_search_sql($cond),$err);
		if(count($row) == 0){
			$_SESSION['raise_message']['global'] = 'No such user exist';
			redirect(LBL_SITE_URL);
		}
		$sql = "DELETE FROM ".TABLE_PREFIX."user
				WHERE ".$cond;
		execute($sql,$err);
		$from					= $GLOBALS['conf']['SITE_ADMIN']['email'];
		$to						= $row[0]['v_email'];
		$subject				= $GLOBALS['conf']['SITE_ADMIN']['registration_subject'];
		$info	= $row[0]['name'];

		$info['u_password']	= $row[0]['u_password'];
		$info['confirm_link']= LBL_SITE_URL;

		$to = $to;
		$subject = $subject;
		$tpl = "user/user_reg_reject";

		$this->smarty->assign('sm',$info);
		$body = $this->smarty->fetch($this->smarty->add_theme_to_template($tpl));

		$msg=sendmail($to,$subject,$body,$from);// also u can pass  $cc,$bcc
		$_SESSION['raise_message']['global']="<center>The user account has been Rejected.Confirmation mail has been send to the user.</center>";
		redirect(LBL_SITE_URL);
	}
##################################################
################## BLOCK USER  ###################
##################################################

	function _block_user(){
		$type = $this->_input['t'];
		$id_user = $this->_input['uid'];
		$sql = "SELECT id_forum FROM ".TABLE_PREFIX."user
				WHERE id_user = ".$id_user." LIMIT 1";
		$res = getrows($sql,$err);
		$id_forum = $res[0]['id_forum'];
		$mystring = 'xyz,'.$_SESSION['wfids'].',';
		if(strrpos($mystring,'23,')){
			$sql = "UPDATE ".TABLE_PREFIX."user
						SET i_random = ";
			if($type == 'b'){
				$sql .= "-1";
			}elseif($type == 'ub'){
				$sql .= "0";
			}
			$sql .= " WHERE id_user = ".$id_user;
			$err = execute($sql,$err);
			if($err){
				if($type == 'b'){
					$_SESSION['raise_message']['global'] = 'User blocked successfully';
				}elseif($type == 'ub'){
					$_SESSION['raise_message']['global'] = 'User unblocked successfully';
				}
				redirect(LBL_SITE_URL.'forum/search');
			}
		}else{
			$_SESSION['raise_message']['global'] = 'You are not authorised';
			redirect(LBL_SITE_URL);
		}
	}

##################################################
################## CLEAR  ########################
##################################################
	function _clear() {
		exec("rm var/cache/*.* -f");
		exec("rm var/templates_admin_c/*.* -f");
		exec("rm var/templates_c/*.* -f");
	}

##################################################
################## LIST USER #####################
##################################################
	function _listuser(){
		if($_SESSION['access'] & $GLOBALS['conf']['ACCESS_RIGHT']['add_user']){
			$sql="SELECT * FROM ".TABLE_PREFIX."user WHERE 1";
			$uri='user/listuser';
			if($this->_input['fname']){
				$sql.=" AND fname LIKE '".$this->_input['fname']."%'";
				$uri.='-fname-'.$this->_input['fname'];
			}
			if($this->_input['u_status'] >= "0"){
				$sql.=" AND u_status=".$this->_input['u_status'];
				$uri.='-u_status-'.$this->_input['u_status'];
			}
			$results_params = Array ('URI' => $uri, 'SQL' => $sql, 'MODULE' => 'user', 'TPL' => 'user/list_user', 'CACHE_PREPEND' => 'sef|message|list|', 'RESULTS_UNDER' => 'res', 'DEBUG' => 1,'DEF_FIELD'=>'fname' );
		$this->user_bl->setupresults($results_params);
		$this->_output['cache_id'] =$this->user_bl->get_cache_id();
		$this->_output['tpl'] = "user/list_user";
		$this->_output['u_status']=$GLOBALS['conf']['U_STATUS'];
		$this->_output['su_status']=$this->_input['u_status'];
		$this->_output['fname']=$this->_input['fname'];

		if (!$this->cache_alive()) {
			$this->_output = array_merge($this->_output,$this->user_bl->get_output());
			$this->_output['TITLE'] = "User List";
			$this->_output['use_session'] = "1";
			$this->_output['choice'] = 'list_user';
		}
		return true;
//			$this->_output['res']=getrows($sql,$err);
//			$this->_output['tpl']='user/list_user';
		}else{
 			$_SESSION['raise_message']['global']="You are not authorised person";
		}
	}

##################################################
################## ADD USER  #####################
##################################################
	function _adduser($input=''){
		if($_SESSION['access'] & $GLOBALS['conf']['ACCESS_RIGHT']['add_user']){
			$status=array_pop($GLOBALS['conf']['U_STATUS']);
			$this->_output=$input;
			$this->_output['shortname']='';
			$this->_output['status']=$GLOBALS['conf']['U_STATUS'];
			$this->_output['add']='ADD';
			$this->_output['smt']='Submit';
			if($this->_input['cid']){
				$this->_output['cid'] =	$this->_input['cid'];
				$this->_output['u_status']=$GLOBALS['conf']['USER_STATUS']['client'];
			}
			$this->_output['choice'] ='insertuser';
			$this->_output['tpl']='user/add_user';
		}else{
 			$_SESSION['raise_message']['global']="You are not authorised person";
			redirect('user/listuser');
		}
	}

##################################################
################## INSERT USER  ##################
##################################################
	function _insertuser(){
	//print_r($this->_input);exit;
		$dpl_cond=" shortname = '".$this->_input['shortname']."'";
		$dplusr=get_search_sql('user',$dpl_cond);
		$dplres=getrows($dplusr,$err);
		if(mysql_affected_rows()){
			print "Alias name exists,Re-enter";
			$this->_adduser($this->_input);

		}else{
		$this->obj_user->loadfromarr($this->_input);
		$res = $this->obj_user->insert();
		redirect('user/listuser');
		}
	}

##################################################
################## EDIT USER  ####################
##################################################
	function _edituser(){
		if($_SESSION['access'] & $GLOBALS['conf']['ACCESS_RIGHT']['add_user']){
			$cond = "id_user = ".$this->_input['id'];
			$sql=get_search_sql('user',$cond);
			$res=mysql_fetch_assoc(mysql_query($sql));
			$this->_output=$res;
			$this->_output['acc']=$GLOBALS['conf']['ACCESS_RIGHT'];
			$status=array_pop($GLOBALS['conf']['U_STATUS']);
			$this->_output['status']=$GLOBALS['conf']['U_STATUS'];
			$this->_output['add']='EDIT';
			$this->_output['smt']='Update';

			if($this->_input['cid']){
				$this->_output['cid'] =	$this->_input['cid'];
				$this->_output['u_status']=$GLOBALS['conf']['USER_STATUS']['client'];
			}
			$this->_output['choice'] ='updateuser';
			$this->_output["HTTP_REFERER"] = $_SERVER['HTTP_REFERER'];
			$this->_output['tpl']='user/add_user';
		 }else{
 			$_SESSION['raise_message']['global']="You are not authorised person";
		 }
	}

##################################################
################## UPDATE USER  ##################
##################################################
	function _updateuser(){
		$cond =$this->_input['id_user'];
		$this->obj_user->loadfromarr($this->_input);
		$this->obj_user->update('',$cond);
		$_SESSION['raise_message']['global']="Successfully Updated";

//////////////  Update the assignee name in project table    ////////////////
		$psql="UPDATE ".TABLE_PREFIX."project SET assignee_name = REPLACE(assignee_name,'".$this->_input['oldname']."','".$this->_input['shortname']."')";
		mysql_query($psql);
		////////   END   ////////

//////////////  Update the Assignee User,Reviewers,Assigner in Task table    ////////////////
		$tsql="UPDATE ".TABLE_PREFIX."task SET
			assign_users = REPLACE(assign_users,'".$this->_input['oldname']."','".$this->_input['shortname']."'),
			assigner = REPLACE(assigner,'".$this->_input['oldname']."','".$this->_input['shortname']."'),
			reviewers = REPLACE(reviewers,'".$this->_input['oldname']."','".$this->_input['shortname']."')";
		mysql_query($tsql);
		////////   END   ////////
		//redirect('page-user-choice-listuser');
		header('Location: '.$this->_input["HTTP_REFERER"]);
		exit;
	}

##################################################
################## DELETE USER  ##################
##################################################
	function _deleteuser(){
		if($_SESSION['access'] & $GLOBALS['conf']['ACCESS_RIGHT']['add_user']){
			$this->obj_user->delete($this->_input['id']);
			$_SESSION['raise_message']['global']="Successfully Deleted";
			redirect('user/listuser');
		}else{
			$_SESSION['raise_message']['global']="You are not authorised person";
		}
	}

##################################################
###################check_session #################
##################################################
	function check_session() {
		if(!$_SESSION['id_user']) {
			$_SESSION['raise_message']['global'] = "Please login to access this site.";
			redirect(LBL_SITE_URL);
		}
	}

#####################################################
#Checking for repeative failure having same username#
#####################################################
	function chk_prev_uname($uname){
		$ipcount=$_SESSION['ip_count'];
		if(isset($_SESSION['ip_uname'])){
		    if($_SESSION['ip_uname']==$uname){
			    $ipcount++;
			    $_SESSION['ip_count']=$ipcount;
		    }else{
			    $_SESSION['ip_count']=0;
		    }
		}else{
		    $_SESSION['ip_uname']=$uname;
		}
	}

######################################################
########Set language session #########################
######################################################
	function _setlang() {
		$_SESSION['lang']=$this->_input['lang']?$this->_input['lang']:'';
		//print $_SERVER['HTTP_REFERER']."dddddddddddd";exit;
		redirect($_SERVER['HTTP_REFERER']);
	}

#######################################################
##############getResult ###############################
#######################################################
	function getResult($tbl,$cond){
		$sql="SELECT * FROM ".TABLE_PREFIX.$tbl." WHERE ".$cond;
		return mysql_fetch_assoc(mysql_query($sql));
	}

#######################################################
##############Set the Session Variatbles ##############
#######################################################
	function _setsession() {
		$sn = $this->_input['name'];
		$sv = $this->_input['value'];
		$_SESSION[$sn] = $sv;
	}

#######################################################
#############Unset the Session Variatbles ############
#######################################################
	function _unsetsession() {
		$sn = $this->_input['name'];
		unset($_SESSION[$sn]);
		$this->_printsession();
	}
#######################################################
#############Print the Session Variatbles ############
#######################################################
	function _printsession() {
		print "<pre>";
		print_r($_SESSION);
	}
#######################################################
#############Change the Profile Picture################
#######################################################
	function _edit_avatar(){
	    $this->_output['tpl']="user/change_avatar";
	}
	function _preview(){
	    if ($_FILES['img_name']['name']){
		    $time= strtotime("now");
		    $rid=$time."_";
		    $uploadDir  = APP_ROOT.$GLOBALS['conf']['IMAGE']['preview_orig'];
		    $thumbnailDir = APP_ROOT.$GLOBALS['conf']['IMAGE']['preview_thumb'];
		    $thumb_height = $GLOBALS['conf']['IMAGE']['thumb_height'];
		    $thumb_width = $GLOBALS['conf']['IMAGE']['thumb_width'];
		    $fname = $rid.convert_me($_FILES['img_name']['name']);
		    $file_tmp=$_FILES['img_name']['tmp_name'];
		    @copy($file_tmp, $uploadDir.$fname);
		    $copy_thumb=copy($uploadDir.$fname, $thumbnailDir.$fname);
		    $this->r = new thumbnail_manager($uploadDir.$fname,$thumbnailDir.$fname);
		    $this->r->get_container_thumb($thumb_height,$thumb_width,0,0);
		    ob_clean();
		    echo $fname;exit;
	    }
	}

	/*
	* image_upload
	* @return type
	*/
	function _image_upload() {
	ob_clean();
	$del = $this->unlink_files();
	if ($this->_input['server_img']) {
	    $img_name = substr($this->_input['server_img'],(strpos($this->_input['server_img'],"_")+1));
	    $sql = "SELECT avatar FROM " . TABLE_PREFIX . "user WHERE id_user = " .$_SESSION['id_user']." LIMIT 1";
	    $res = getsingleindexrow($sql);
	    $orig_path = APP_ROOT . $GLOBALS['conf']['IMAGE']['avtar_orig'];
	    $thumb_path = APP_ROOT . $GLOBALS['conf']['IMAGE']['avtar_thumb'];
	    if ($res['avatar']) {
		if(file_exists($orig_path .$res['avatar']))
		    unlink($orig_path . $res['avatar']);
		if(file_exists($thumb_path . $res['avatar']))
		    unlink($thumb_path . $res['avatar']);
	    }
	   // $img_msg = $this->obj_user->update_this("user",$arr,"id_user=".$_SESSION['id_user']);
	    $img_msg = $this->obj_user->update_image_name($img_name);
	    if ($img_msg) {
		$preview_path = APP_ROOT.$GLOBALS['conf']['IMAGE']['preview_orig'].$this->_input['server_img'];
		$orig_path.=$_SESSION['id_user']."_".$img_name;
		$thumb_path.=$_SESSION['id_user']."_".$img_name;
		copy($preview_path,$orig_path);
		copy($orig_path, $thumb_path);
		$thumb_height = $GLOBALS['conf']['IMAGE']['thumb_height'];
		$thumb_width = $GLOBALS['conf']['IMAGE']['thumb_width'];
		$thumb_object = new thumbnail_manager($orig_path, $thumb_path);
		$thumb_object->get_container_thumb($thumb_width, $thumb_height, 0, 0);
		echo $_SESSION['id_user']."_".$img_name;
		$_SESSION['avatar']=$_SESSION['id_user']."_".$img_name;
		exit;
	    }
	} else {
	    print "No image Uploaded";
	    exit;
	}
	}
	/*
	* unlink_files
	*/
	function unlink_files(){
		for($i=0;$i<2;$i++){
		   if($i==0){
			   $file_arr=glob(APP_ROOT.$GLOBALS['conf']['IMAGE']['preview_orig']."*");
		   }else{
			   $file_arr=glob(APP_ROOT.$GLOBALS['conf']['IMAGE']['preview_thumb']."*");
		   }
		   if(!is_array($file_arr)){
			   $file_arr=array();
		   }
		   $yes_time_stamp=strtotime("-1 day");
		   foreach($file_arr as $val){
		       $file_time_stamp=filemtime($val);
		       if($file_time_stamp <= $yes_time_stamp){
			       @unlink($val);
			       //print $val." :: ".date('d-m-Y',$file_time_stamp)."<br>";
		       }
		   }
		}
		return;
	}
	function _getExperience(){
	    global $link;
	    if(!$_SESSION['id_user']){
		exit("2");
	    }
	    $tot_points=100000;
	    $sql="SELECT exp_point FROM ".TABLE_PREFIX."user WHERE id_user=".$_SESSION['id_user']." LIMIT 1";
	    $res=mysqli_fetch_assoc(mysqli_query($link,$sql));
	    if(!$res){
		exit("2");
	    }
	    if(($_SESSION['exp_point']==$res['exp_point']) && $this->_input['chk']=='1'){
		exit("1");
	    }else{
		    $_SESSION['exp_point']=$res['exp_point'];
		    $percent=round((($res['exp_point']/$tot_points)*100),2);
		    $this->_output['points']=$percent;
		    $this->_output['tpl']="user/experience_bar";
	    }
	}
        function _getFriends(){
	    $sql=$this->user_bl->get_frnds_sql();
	    $this->_output['frnds']=getrows($sql,$err);
	}
	function _set_login_time(){
	    if($_SESSION['id_user']){
		$this->obj_user->update_user_login_time($user);
	    }
	}
	function _first_login_msg(){
	    global $link;
	    if($this->_input['upd']=='upd'){
		$sql="UPDATE ".TABLE_PREFIX."user SET toc=1 WHERE id_user=".$_SESSION['id_user'];
		mysqli_query($link,$sql);
		$_SESSION['toc']='1';
	    }
	    if($_SESSION['toc']=='0'){
		$this->_output['tpl']="user/first_login_msg";
	    }
	}
	function _check_fb_session(){
		$arr=$this->decrypt_fb_data();
		$facebook = $arr[0];
		$data = $arr[1];
		$fb_login_sts=$facebook->api_client->users_getLoggedInUser();
		if($fb_login_sts=='-1'){
			$_SESSION['fb_login']='';
		}
		print $fb_login_sts;exit;
	}
	function decrypt_fb_data(){
		$api_key=$GLOBALS['conf']['FACEBOOK']['api_key'];
		$secret=$GLOBALS['conf']['FACEBOOK']['secret_key'];
		$app_id=$GLOBALS['conf']['FACEBOOK']['app_id'];
		$arr = explode('&',trim(stripslashes($_COOKIE['fbs_'.$app_id]),'"'));
		foreach($arr as $k => $v){
			$key = substr($v,0,strpos($v,'='));
			$val = substr($v,strpos($v,'=')+1);
			$data[$key] = $val;
		}
		$session_key = $data['session_key'];
		$arr[0] = new Facebook($api_key,$secret,$session_key);
		$arr[1] = $data;
		return $arr;
	}
	function _facebook_info(){
	    global $link;

	    $arr=$this->decrypt_fb_data();
	    $facebook = $arr[0];
	    $data = $arr[1];

	    //Retrieve user information of facebook
	    $fb_user_info=$facebook->api_client->users_getInfo($data['uid'], array('uid','about_me','activities','affiliations','birthday','birthday_date','books','contact_email','current_location','education_history','email','email_hashes','family','has_added_app','hometown_location','hs_info','interests','is_app_user',' is_blocked','locale','meeting_for','meeting_sex','movies','music','name','first_name','middle_name','last_name','notes_count','pic','pic_with_logo','pic_big','pic_big_with_logo',' pic_small','pic_small_with_logo','pic_square','pic_square_with_logo','political','profile_blurb','profile_update_time','profile_url','proxied_email',' quotes','relationship_status',' religion',' sex',' significant_other_id','status','timezone',' tv','username','wall_count','website','work_history'));

	    $user_details=$fb_user_info[0];

	    if(!$user_details['uid']){
		redirect(LBL_SITE_URL);
	    }

	    $sql="SELECT * FROM ".TABLE_PREFIX."user WHERE uid=".$user_details['uid']." LIMIT 1";
	    $qry=mysqli_query($link,$sql);
	    $results=mysqli_fetch_assoc($qry);
	    $_SESSION['fb_login']=1;
	    if($results){
		$this->_set_login($results['email'],$results['password']);
	    }else{

		$friends=$facebook->api_client->friends_get('',$data['uid']);
		$pwd=rand(10000,99999);

		$sex=($user_details['sex']=='male')?'M':(($user_details['sex']=='female')?'F':'');

		$in_user['id_user']  = $user_details[''];
		$in_user['uid']  = $user_details['uid'];
		$in_user['name']  = $user_details['name'];
		$in_user['fb_pic_big']  = $user_details['pic_big'];
		$in_user['fb_pic_square']  = $user_details['pic_square'];
		$in_user['fname']  = $user_details['first_name'];
		$in_user['mname']  = $user_details['middle_name'];
		$in_user['lname']  = $user_details['last_name'];
		$in_user['email']  = $user_details['email'];
		$in_user['password']  = $pwd;
		//$in_user['id_admin']  = $user_details[''];
		$in_user['gender']  = $sex;
		$in_user['dob']  = $user_details['birthday_date'];
		//$in_user['avatar']  = $user_details[''];
		$addr=( $user_details['current_location']['city'])? $user_details['current_location']['city'].",":'';
		$addr.=($user_details['current_location']['state'])? $user_details['current_location']['state'].",":'';
		$addr.=($user_details['current_location']['country'])? $user_details['current_location']['country']:'';
		$in_user['address']  = trim($addr,",");
		$in_user['address']  = $user_details[''];
		//$in_user['ques_week_won']  = $user_details[''];
		//$in_user['duels_won']  = $user_details[''];
		//$in_user['exp_point']  = $user_details[''];
		//$in_user['no_badges']  = $user_details[''];
		//$in_user['login_status']  = $user_details[''];
		//$in_user['no_of_logs']  = $user_details[''];
		//$in_user['last_login']  = $user_details[''];
		//$in_user['update_login']  = $user_details[''];
		//$in_user['login_time']  = $user_details[''];
		$in_user['random_num']  = '0';
		$in_user['flag']  = 1;
		$in_user['user_status']  = 1;
		$in_user['id_friends']  = implode(",",$friends);
		//$in_user['toc']  = $user_details[''];
		//$in_user['add_date']  = $user_details[''];
		$in_user['ip']  = $_SERVER['REMOTE_ADDR'];

	    }
	    $this->obj_user->insert_all('user',$in_user,1,$dt_fld='add_date');
	    $this->_set_login($in_user['email'],$pwd);
	}
	function _invited(){
		print "<pre>";
		print_r($_REQUEST);
		$this->obj_user->update_diff_data('invited_to','user','id_user='.$_SESSION['id_user'],$_REQUEST['ids']);
		$url=LBL_SITE_URL.$_REQUEST['p']."/".$_REQUEST['c'];
		if($_REQUEST['cat']!=''){
			$url.="/cat/".$_REQUEST['cat'];
		}
		redirect($url);
	}


############################################
############# MEMEJE FRIEND REQUEST#################
############################################
	function _frnd_request(){
		$sql="select id_admin from ".TABLE_PREFIX."user where id_user = ".$_SESSION['id_user'];
		$res=getsingleindexrow($sql);
		if($res['id_admin']!=1){
		    $sql="SELECT count(*) as total FROM ".TABLE_PREFIX."frnd_request WHERE requested_to = ".$_SESSION['id_user']." AND is_read = 0";
		    $res=getsingleindexrow($sql);
		    if($res['total'] != ''){
			$this->_output['res']=$res['total'];
		    }else{
			 $this->_output['res']=0;
		    }
		}
		$this->_output['tpl']="user/friend_req";
	}
	function _friend_req_list(){
		check_session();
		global $link;
		$cond1 =" requested_to = ".$_SESSION['id_user'];
		$arr1['is_read']=1;
		//$this->obj_user->update_this('frnd_request',$arr1,$cond1);
		$sql_req="SELECT GROUP_CONCAT(requested_by) as req FROM ".TABLE_PREFIX."frnd_request WHERE requested_to = ".$_SESSION['id_user']." and  req_status = 0";
		$query=mysqli_query($link,$sql_req);
		$res_req=mysqli_fetch_assoc($query);
		if($res_req['req']){
			$sql= "SELECT * from ".TABLE_PREFIX."user  WHERE id_user in (".$res_req['req'].")";
			$res=getrows($sql, $err);
		}
		 
		$this->_output['res']=$res;
		$this->_output['tpl']='user/friend_req_list';
	}

	function _get_memeje_frnds(){
		check_session();
		$id_user=$_SESSION['id_user'];
		$sql="SELECT * FROM ".TABLE_PREFIX."user WHERE id_admin !=1 AND id_user !=".$id_user." AND id_user NOT IN (SELECT requested_to FROM ".TABLE_PREFIX."frnd_request WHERE requested_by = ".$id_user." AND req_status !=2 UNION SELECT requested_by FROM ".TABLE_PREFIX."frnd_request WHERE requested_to =".$id_user." AND req_status !=2)";
		$res=getrows($sql,$err);
		$sql = "SELECT memeje_friends FROM ".TABLE_PREFIX."user WHERE id_user=".$id_user;
		$arr= getsingleindexrow($sql);
		$has_frnds_cnt = ($arr['memeje_friends']!='')?count(explode(',',$arr['memeje_friends'])):0;
		$this->_output['frnds_cnt']=$has_frnds_cnt;
		$this->_output['frnds']=$res;
		$this->_output['tpl']="user/memeje_friends";
	}
	function _add_memeje_frnds(){
	    $ids=array();
	    $data = $_REQUEST;
	    $ids=explode(",",$this->_input['ids']);
	    $arr['requested_by'] =$notify['id_user']= $_SESSION['id_user'];
	    $arr['ip'] =$notify['ip']= $_SERVER['REMOTE_ADDR'];
		$notify['notification_type'] = '5';
	    foreach ($ids as $key => $value) {
			$arr['requested_to']=$notify['notified_user'] = $value;
			$id = $this->obj_user->insert_all("frnd_request",$arr,1);
			$notify_id = $this->obj_user->insert_all("notification",$notify,1);
			//print $id;
	    }
	    if($id)
		$this->_mail_notification($this->_input['ids'],"send_frnd_request");
	}
	function _mail_notification($ids,$param){
		global $link;
		if (is_array($ids)){
		    $ids=implode(",", $ids);
		}
		switch($param){
		    case "send_frnd_request":
			$subject=$_SESSION['fname']." send you a friend request";
			$tpl="user/mail_send_frnd_request";
			break;
		    case "conf_frnd_request":
			$subject=$_SESSION['fname']." accept your friend request";
			$tpl="user/mail_conf_frnd_request";
			break;
		}
		$sql="SELECT email,fname FROM memeje__user where id_user IN ($ids)";
		$query=mysqli_query($link,$sql);
		while($rec=mysqli_fetch_assoc($query)){
		    $info['name']=$rec['fname'];
		    $to=$rec['email'];
		   /// $subject=$_SESSION['fname']." send you a friend request";
		    $from = $GLOBALS['conf']['SITE_ADMIN']['email'];
		    //$tpl = "user/$tmpl";
		    $this->smarty->assign('sm',$info);
		    $body = $this->smarty->fetch($this->smarty->add_theme_to_template($tpl));
		    print($body);exit;
		    $msg=sendmail($to,$subject,$body,$from);
		    if ($msg){
			$msg[]="Mail Sent Sucessfully TO ".$value;
		    }
		}
	}
	function _conf_frnd_request(){
	    $arr_fd_req['req_status']='1';
	    $id=$notify['notified_user'] =$this->_input['id'];
	    $id_user=$notify['id_user']=$_SESSION['id_user'];
		$notify['notification_type'] = '1';
		$notify1['is_removed']='1';
	    $frnd_request=$this->obj_user->update_this("frnd_request",$arr_fd_req,"requested_by =$id AND requested_to =$id_user");
	    $update_fd_own=$this->obj_user->update_diff_data('memeje_friends','user','id_user='.$id_user,$id);
			unset($_SESSION['friends']);
			$_SESSION['friends']=$update_fd_own;		

	    $update_fd_ops=$this->obj_user->update_diff_data('memeje_friends','user','id_user='.$id,$id_user);
		$notify_id = $this->obj_user->insert_all("notification",$notify,1);
		$notify_upd=$this->obj_user->update_this("notification",$notify1,"id_user =$id AND  notified_user=$id_user AND notification_type=5");
	    //print  $update_fd_ops;
	    $this->_mail_notification($id,"conf_frnd_request");
	}
	function _rej_frnd_request(){
		$arr['req_status']=2;
		$notify['is_removed'] =1;
		$id=$this->_input['id'];
		$id_user=$_SESSION['id_user'];
		$this->obj_user->update_this("frnd_request",$arr,"requested_by =$id AND requested_to =$id_user");
		$this->obj_user->update_this("notification",$notify,"id_user =$id AND  notified_user=$id_user AND notification_type=5 AND is_removed !=1");
	}
	function _get_sent_users(){
	    if($_SESSION['id_user']){
		//$sql=$this->user_bl->get_search_sql("user","id_user=".$_SESSION['id_user'],"invited_to");
		$sql="SELECT invited_to FROM ".TABLE_PREFIX."user WHERE id_user=".$_SESSION['id_user']." LIMIT 0,1";
		$res=getsingleindexrow($sql);
		ob_clean();
		print $res['invited_to'];
	    }else{
		exit;
	    }
	}
	function _show_profile_info(){
	    $sql=get_search_sql("user","id_user =".$this->_input['id_user'] );
	    $res=getrows($sql, $err);
	    $this->_output['res']=$res;
	    $this->_output['tpl']="user/profile_info";
	}
	###############################################################
	####################  FRIEND LIST #############################
	###############################################################
	function _friend_list(){
	    check_session();
	    $sql=get_search_sql("user","FIND_IN_SET(id_user,(select memeje_friends from memeje__user where id_user='".$_SESSION['id_user']."'))","*");
	    $sql= "SELECT avatar,id_user,gender,concat(fname,' ',lname) as name FROM ".TABLE_PREFIX."user WHERE FIND_IN_SET(id_user,(select memeje_friends from memeje__user where id_user='".$_SESSION['id_user']."'))";
	    $res=getrows($sql, $err);
	    $this->_output['count']=count($res);
	    $this->_output['res']=$res;
	    $this->_output['tpl']="user/friend_list";
	}
	function _all_friends($q='0'){
	    $qstart = $this->_input['qstart'] ? $this->_input['qstart'] : $q;
	    $uri = 'user/all_friends';
	    $sql= "SELECT avatar,id_user,gender,concat(fname,' ',lname) as name,memeje_friends FROM ".TABLE_PREFIX."user WHERE FIND_IN_SET(id_user,(select memeje_friends from memeje__user where id_user='".$_SESSION['id_user']."'))";
	    $this->_output['field'] = array("id_user" => array("id_user", 1));
	    $this->_output['uri'] = $uri;
	    $this->_output['limit'] = $GLOBALS['conf']['PAGINATE']['rec_per_page'];
	    $this->_output['show'] = $GLOBALS['conf']['PAGINATE']['show_page'];
	    $this->_output['sql'] = $sql;
	    $this->_output['type'] = 'box';
	    $this->_output['sort_by'] ="name";
	    $this->_output['sort_order'] = "ASC";
	    $this->_output['ajax'] = '1';
	    $this->_output['qstart'] = $qstart;
	    //$_REQUEST['qstart'] =$qstart;
	     $_REQUEST['choice'] ='all_friends';
	    $this->user_bl->page_listing($this, "user/all_friends");
	}
	function _remove_frnd(){
	    check_session();
	    $id=$this->obj_user->remove_frnd($this->_input['ssnfrnds'],$this->_input['rmvdfrnds'],$this->_input['id_user']);
	    if ($this->_input['qstart']) {
		 $qstart = $this->_input['qstart'];
	    } else {
		$qstart = 0;
	    }
	    if ($this->_input['count'] == 1 && $qstart > 0) {
		$qstart = $qstart - $this->_input['limit'];
	    } else if ($this->_input['count'] > 1 && $qstart > 1) {
		$qstart = $qstart;
	    }else {
		$qstart = 0;
	    }
	    $this->_input['qstart'] = $qstart;
	    $this->_all_friends($this->_input['qstart']);
	}
	function  _getfriends4tag(){
	    global $link;
	    $sql_frnd = get_search_sql("user"," id_user=".$_SESSION['id_user'],"memeje_friends");
	    $res_frnd=getrows($sql_frnd,$err);
	    if($res_frnd[0]['memeje_friends']){
		    $sql =get_search_sql("user","id_user IN(".$res_frnd[0]['memeje_friends'].") ");
	 	    $res = mysqli_query($link,$sql);
		    while($rec= mysqli_fetch_assoc($res)){
				$img_nm = ($rec['avatar'])?$rec['avatar']:(($rec['gender']='M')?"memeje_male.jpg":"memeje_female.jpg");
				$img = "<img src='".LBL_SITE_URL."image/thumb/avatar/".$img_nm."' style='width:40px;height:40px;'/>";
				$arr[] = array("name"=>$rec['fname'],"value"=>$rec['id_user'],"lname"=>$rec['lname'],"pf_img"=>$img);
		    }
		    mysqli_free_result($res);
		    mysqli_next_result($link);
	    }
	    if(!$arr && $this->_input['flg_duel']){
		print "1";exit;
	    }
	    print json_encode($arr);exit;
	}
}