<?php /* Smarty version 2.6.7, created on 2011-09-12 23:04:22
         compiled from meme/random_meme.tpl.html */ ?>
<?php $this->assign('category', $this->_tpl_vars['util']->get_values_from_config('CATEGORY'));  echo '
<script type="text/javascript">
    var x=0;
    function rand_set_tot_adaggr(id,con){
	var url = "http://memeja.com/meme/set_adaggr";
	$.post(url,{id_meme:id,ce:0,con:con },function(res){
	    if(res[0]!=0){
		    if(res[1]==1){
			$("#aggr"+id).html(res[0]);
			$("#is_agreed"+id).val(\'1\');
		     }else{
			$("#disaggr"+id).html(res[0]);
			$("#is_disagreed"+id).val(\'1\');
		     }
	     }else
		   alert("You have already voted.");
	 },"json");
     }
    function get_all_rand_replies(id){
	var url = "http://memeja.com/meme/get_all_replies";
	$.post(url,{id:id,ce:0,flag:1 }, function(res){
	    $("#randsend_reply"+id).html(res);
	    if(!$("#randadd_caption"+id).is(":hidden"))
		$(\'#randadd_caption\'+id).slideToggle(\'slow\');
	    $(\'#randsend_reply\'+id).slideToggle(\'slow\');
	 });
     }
    function rand_chk_forclear(id){
	if($(\'#rand_rpl_con\'+id).val() == "Reply with answer.")
	    $(\'#rand_rpl_con\'+id).val(\'\');
     }

    function rand_post_reply(id){
	if($("#rand_rpl_con"+id).val()=="" || $("#rand_rpl_con"+id).val()=="Reply with answer."){
	     $("#rand_rpl_con"+id).val("Reply with answer.");
	     return false;
	 }
	$("#randsend_reply"+id).hide("slow",function(){ });
	var url = "http://memeja.com/meme/answer_to_meme";
	$.post(url,{answer:$("#rand_rpl_con"+id).val(),id:id,ce:0 },function(res){
	    $("#randrepl"+id).html(res);
	 });
     }
    function get_rand_cats(){
	var cats = \'\';
	$(".rand_category").each(function(){
		   if($(this).is(":checked")){
		       cats+=$(this).val()+\',\';
		    }
	 });
	return cats;
     }
    function show_my_details(id_meme){
	var url = "http://memeja.com/meme/meme_list";
	     $(\'#comc_img\').hide();
	     $("#lding_rand").show();
	     var rnd_cat = get_rand_cats();
	     var rnd_ids=$("#rand_ids").val();
	$.post(url, {last_id:id_meme,ce:0,cat:\'rand\',rand_fg:\'1\',rnd_ids:rnd_ids,rnd_cat:rnd_cat }, function(res){
		    if(res){
			$("#rand_meme").html(res);
		     }else{
			$.post(url, {last_id:id_meme,ce:0,cat:\'rand\',rand_fg:\'2\',rnd_cat:rnd_cat }, function(data){
				$("#rand_meme").html(data);
				$("#rand_ids").val("");
			 });
		     }
	 });
     }
    function load_rand_meme(){
	var url = "http://memeja.com/meme/meme_list";
	$(\'#comc_img\').hide();
	$("#lding_rand").show();
	var rnd_cat = get_rand_cats();
	$("#rand_ids").val(\'\');
	$.post(url, {ce:0,cat:\'rand\',rnd_cat:rnd_cat,last_id:1 }, function(res){
	    $("#rand_meme").html(res);
	 });
     }
    function get_rand_captions(id){
	var url = "http://memeja.com/caption/add_caption";
	$.post(url,{id:id,ce:0,flag:\'rand\' }, function(res){
	    if(!$("#randsend_reply"+id).is(":hidden"))
		$(\'#randsend_reply\'+id).slideToggle(\'slow\');
	    $("#randadd_caption"+id).html(res);
	    $(\'#randadd_caption\'+id).slideToggle(\'slow\');
		
	 });
     }
    function rand_flagging(id_meme){
	var flaged_bfr=0;
	var url = "http://memeja.com/meme/flagging_meme";
	$.ajax({
	    type: "POST",
	    url: url,
	    async:false,
	    data: {ce:0,chk:\'1\',id:id_meme } ,
	    dataType: "json",
	    success: function(msg) {
		flaged_bfr = (parseInt(msg))?1:0;
	     }
	 });
	if(flaged_bfr){
		alert("You have already flagged the meme.");
		return false;
	     }else{
		var fg = confirm("Are you sure ?\\nIf you flag , it may lead your account to be frozened or deleted");
		if(fg)
		    $.post(url, {ce:0,id:id_meme },function(data){
			alert("You have successfully flagged the meme.");
		     }); 
	     }
     }
    
</script>
'; ?>

<center>
    <label style="font-size: 16px;font-weight:bold;">Check off the categories.</label>
<div>
	    <?php if (count($_from = (array)$this->_tpl_vars['category'])):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
		<input type="checkbox" value="<?php echo $this->_tpl_vars['key']; ?>
" class="rand_category" onClick="load_rand_meme();"/><span class="cat_text"><?php echo $this->_tpl_vars['item']; ?>
</span>
	    <?php endforeach; endif; unset($_from); ?>
</div><br/>
</center>
<div>
    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "meme/loadmore_rand_meme.tpl.html", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</div>
<input type="hidden" id="rand_ids" />