<!-- Start

// YOU CAN EDIT THESE VARIABLES


var song_name			= "SisterSledgeWeAreFamily"		   // MP3 SONG FILE NAME (no extension)
var file_name			= "colors/BLUE"	  // BUTTON COLORS (no extension)
var backgroundi			= "BLUE"		 	 // BACKGROUND IMAGE - USE COLOR FOLDER NAME
var mxmpbackground_color	= "3366CC"	 	// BACKGROUND COLOR


var autostart                   = "1"		// AUTO START (1 = START / 0 = NO START) 
var mxmptable			= "141"		// TOTAL PLAYER WIDTH
var mxmpwidth			= "59"		// FLASH WIDTH
var mxmpheight			= "17"		// FLASH HIGHT

var mxmpborder_color		= "000000"	// BORDER COLOR
var mxmpborder_width		= "0"		// BORDER WIDTH IN PIXELS




// NOTES: To show player buttons only make "141" width above "59"





// COPYRIGHT 2009 © Allwebco Design Corporation
// Unauthorized use or sale of this script is strictly prohibited by law

// YOU DO NOT NEED TO EDIT BELOW THIS LINE




document.write('<TABLE cellpadding="0" cellspacing="0" border="0" width="'+mxmptable+'" bgcolor="#'+mxmpbackground_color+'" style="border: #'+mxmpborder_color+' '+mxmpborder_width+'px solid; background-image: url(\'template/user/default/files/skins/'+backgroundi+'/background-mxmp.jpg\');"><tr><td>');
document.write('<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" width="'+mxmpwidth+'" height="'+mxmpheight+'" id="xmp3Player1" align="middle">');
document.write('<param name="allowScriptAccess" value="sameDomain" />');
document.write('<param name="movie" value="template/user/default/files/mp3player.swf?file=template/user/default/files/'+file_name+'%2Etxt&amp;song=template/user/default/files/'+song_name+'%2Emp3&amp;astart='+autostart+'" />');
document.write('<param name="quality" value="high" />');
document.write('<param name="bgcolor" value="#'+mxmpbackground_color+'" />');
document.write('<embed src="template/user/default/files/mp3player.swf?file=template/user/default/files/'+file_name+'%2Etxt&amp;song=template/user/default/files/'+song_name+'%2Emp3&amp;astart='+autostart+'" quality="high" bgcolor="#'+mxmpbackground_color+'" width="'+mxmpwidth+'" height="'+mxmpheight+'" name="xmp3Player1" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" /></embed></object><br>');
document.write('</td></tr></table>');

//  End -->