<?php /* Smarty version 2.6.7, created on 2011-09-12 23:22:16
         compiled from common/header.tpl.html */ ?>

<!-- Template: common/header.tpl.html Start 12/09/2011 23:22:16 --> 
 <div id="header">
	<h1><a href="http://memeja.com/">Memeje</a></h1>
	<center>
	    <?php if ($_SESSION['id_user']): ?>
		 Welcome <?php echo $_SESSION['email']; ?>

	    <?php endif; ?>
	</center>
	<div class="fltrht" style="margin-top:0px;">
		<?php if ($_SESSION['id_admin'] || $_SESSION['id_user']): ?>
			<a href ="javascript:void(0);" onclick="fb_logout();">Logout</a>
	    <?php endif; ?>
		<?php if ($_SESSION['id_admin']): ?>
			<br/><a href="http://memeja.com/flexyadmin/">Return to Admin Site</a>
		<?php endif; ?>
		<?php if (! $_SESSION['id_user']): ?>
		    <br/><a href="http://memeja.com/">Login</a>
		<?php endif; ?>
		
		<br/><a href="http://memeja.com/cms/show/code/aboutus">About Us</a>
		<!--<br/><a href="http://memeja.com/cms/show/code/memeja">What is Memeja</a>-->
		<br/><a href="http://memeja.com/achievements/whatisMemeja">What is Memeja</a>
		<br/><a href="http://memeja.com/leaderboard/leaderboard">Leaderboard</a>
	</div>
</div>

<!-- Template: common/header.tpl.html End --> 