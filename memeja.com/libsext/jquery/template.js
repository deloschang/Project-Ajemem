/*
 * Activate template effects
 * @author	Display:inline <contact@display-inline.fr>
 * @url		http://display-inline.fr
 * @version	1.0
 */

// Define site file extension here: html, php, asp...
template_fileExt = 'html';

// Template setup
$(document).ready(function()
{
	// Keywords
	if ($(document.body).hasClass('reversed'))
	{
		$('#keywords li a').hover(function()
		{
			$(this).css({'padding-left':'0'}).animate({paddingLeft:'8px'}, 100);
		}, function()
		{
			$(this).css({'padding-left':'8px'}).animate({paddingLeft:'0'}, 100);
		});
	}
	else
	{
		$('#keywords li a').hover(function()
		{
			$(this).css({'padding-right':'0'}).animate({paddingRight:'8px'}, 100);
		}, function()
		{
			$(this).css({'padding-right':'8px'}).animate({paddingRight:'0'}, 100);
		});
	}
	
	// Header links
	$('#header-links > li > a').hover(function()
	{
		$(this).css({'padding-bottom':'6px', 'border-bottom-width':'0'}).animate({borderBottomWidth:'6px', paddingBottom:'0'}, 100);
	}, function()
	{
		$(this).css({'padding-bottom':'0', 'border-bottom-width':'6px'}).animate({borderBottomWidth:'0', paddingBottom:'6px'}, 100);
	});
	
	// Opera and IE6 have small problems with this effect
	if (!$.browser.opera && !$.browser.msie)
	{
		$('#header-links li').hover(function()
		{
			var submenu = $(this).children('ul');
			if (submenu.length > 0)
			{
				var final_height = submenu.height();
				submenu.css({height:0, opacity:0}).animate({height:final_height, opacity:1}, 'fast');
			}
		}, function()
		{
			var submenu = $(this).children('ul');
			if (submenu.length > 0)
			{
				submenu.stop().css('display', '').css('height', '');
			}
		});
	}
	
	// Highlight
	$('#highlights li').hover(function()
	{
		$(this).css('background-position', '0 0').animate({backgroundPosition:'0 -300px'}, 500);
	}, function()
	{
		$(this).css('background-position', '0 -300px').animate({backgroundPosition:'0 0'}, 500);
	});
	
	// Nav
	var nav = $('.nav');
	var navElements = nav.children('li');
	navElements.hover(function()
	{
		$(this).children('a').css({'padding-left':'7px', 'padding-right':'17px'}).animate({paddingLeft:'17px', paddingRight:'7px'}, 100);
	}, function()
	{
		$(this).children('a').css({'padding-left':'17px', 'padding-right':'7px'}).animate({paddingLeft:'7px', paddingRight:'7px'}, 100);
	});
	
	// Nav elements with submenu
	var navChildren = navElements.children('ul');
	navChildren.parent().each(function(i)
	{
		this._height = $(this).height();
		var a = $(this).children('a');
		a.get(0)._opened = false;
		this._aHeight = (parseInt(a.height())+17)+'px';
		$(this).css('height', this._aHeight)
	}).children('a').click(function(e)
	{
		var a = $(this);
		var li = a.parent();
		var liNode = li.get(0);
		if (!this._opened)
		{
			e.preventDefault();
			li.animate({height:liNode._height}).parent().find('li ul').parent().each(function()
			{
				if (this != liNode)
				{
					$(this).animate({height:this._aHeight}).children('a').get(0)._opened = false;
				}
			});
			this._opened = true;
		}
	});
	
	// Sub-nav
	navChildren.children('li').children('a').hover(function()
	{
		$(this).css({'padding-left':'0', 'padding-right':'15px', 'border-left-width':'0'}).animate({paddingLeft:'5px', paddingRight:'10px'}, 100).animate({borderLeftWidth:'5px', paddingRight:'5px'}, 100);
	}, function()
	{
		$(this).css({'padding-left':'5px', 'padding-right':'5px', 'border-left-width':'5px'}).animate({paddingLeft:'0', borderLeftWidth:'0', paddingRight:'15px'}, 100);
	});
	
	// Forms input field auto-tip
	$('.search-form input, .form input[type=text], .form textarea').focus(function()
	{
		var input = $(this);
		var title = input.attr('title');
		input.removeClass('input-tip');
		if (title && input.val() == title)
		{
			input.val('');
		}
	}).blur(function()
	{
		var input = $(this);
		var title = input.attr('title');
		if (title && (input.val() == '' || input.val() == title))
		{
			input.val(title);
			input.addClass('input-tip');
		}
	}).blur();
	
	// Prevent tips to be submitted
	$('.search-form, .form').submit(function()
	{
		$(this).find('input[type=text]').each(function(i)
		{
			var input = $(this);
			var title = input.attr('title');
			if (title && input.val() == title)
			{
				input.val('');
			}
		});
	});
	
	/*
	 * Navigation
	 */
	
	// Detection of current page if none specified
	var current = nav.find('.current');
	if (current.length == 0)
	{		

		// Detects from current url, with hash
		var selection = navElements.find('a');
		var href = document.location.href;
		if (href.substr(-1, 1) == '#')
		{
			href = href.substr(0, href.length-1);
		}
		if ($.browser.safari)
		{
			href = href.replace(' ', '%20');			// Webkit leaves spaces unencoded in document.location	
		}
		var found = template_markLinks(selection, href);
		if (!found)
		{
			// If hash, remove it
			if (document.location.hash && document.location.hash.length > 0)
			{
				href = document.location.href.substr(0,  document.location.href.length-document.location.hash.length);
				found = template_markLinks(selection, href);
			}
		}
		if (!found)
		{
			// If folder, search with index.html
			if (document.location.pathname.substr(-1, 1) == '/')
			{
				found = template_markLinks(selection, href+'index.'+template_fileExt);
			}
		}
		
		// Reload selection
		if (found)
		{
			current = nav.find('.current');
		}
	}
	
	// If current
	if (current.length > 0)
	{
		current.each(function(i)
		{
		  	// Check level
			var element = $(this);
			var parent = element.parent().parent();
			if (parent.get(0).nodeName.toLowerCase() == 'li')
			{
				element = parent;
			}
			element.children('a').click();
		});
	}
	
	/*
	 * Contact form
	 */
	var sendMessage = '<p class="loading">Sending...</p>';
	if (!$.browser.msie || $.browser.version > 6)	// IE6 lack of support for position:fixed does not allow animation
	{
		
		$('#contact').addClass('fixed').find('button[type=button]').click(function()
		{
			$('#contact').animate({top:'-385px'});
		});
		
		$('#contact-close').click(function()
		{
			$('#contact').animate({top:'-385px'});
		});
		$('a[href$="#contact"]').click(function(event)
		{
			$('#default_cf').show();
			$('#thank_cf').hide();
			$('#username').val('');
			$('#email').val('');
			$('#phone').val('');
			$('#comments').val('');
			$('#contact').animate({top:0});
			event.preventDefault();
		});
		
		// If a close message link already exists
		$('#contact-message a:last').live('click', function(event)
		{
			$('#contact').animate({top:'-365px'}, 'normal', 'swing', function()
			{
				$('#contact-message').html(sendMessage).hide('normal');
				$('#contact-form').show();
				$('#message').val('');
			});
			event.preventDefault();
		});
		
		// If in url hash, open
		if (document.location.hash == '#contact' || document.location.hash == '#contact-top')
		{
			$('#contact').animate({top:0});
		}
	}
	
	// Ajax sending
	/*$('#contact').submit(function(event)
	{
		// Prevent standard sending
		event.preventDefault();
		
		// Check fields
		var name = $.trim($('#name').val());
		var mail = $.trim($('#mail').val());
		var city = $.trim($('#city').val());
		var phone = $.trim($('#phone').val());
		var time_to_contact = $.trim($('#time_to_contact').val());
		var message = $.trim($('#message').val());
		var mailRegExp = new RegExp('^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$', 'i');
		if (name == '' || mail == '' || message == '')
		{
			alert('Please fill in all fields');
		}
		else if (!mail.match(mailRegExp))
		{
			alert('Invalid mail, please check.');
		}
		else
		{
			//alert(this.action);	
			$('#contact-form').hide();
			$('#contact-message').show();
			$.post(this.action, {'contact-sent':1, name:name, mail:mail, city:city, message:message,phone:phone,time_to_contact:time_to_contact},function(msg) {
																																					  			if(msg) {
				$('#contact-form').hide();
				$('#contact-message').html("Your Message send successfully");
				$('#contact-message').hide();
				$('#contact').animate({top:'-365px'});
			}
 		});
		}
	});*/
	});

/*
 * Add class 'current' to links matching an url
 * @param	Array		selection		jQuery nodes selection
 * @param	string		url				the url to find
 * @return	boolean		true if one or more links found, else false
 */
function template_markLinks(selection, url)
{
	var found = false;
	selection.each(function()
	{
		if (this.href === url)
		{
			$(this).parent().addClass('current');
			found = true;
		}
	});
	return found;
}