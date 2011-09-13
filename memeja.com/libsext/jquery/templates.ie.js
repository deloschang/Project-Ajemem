/*
 * Emulate unhandled css selectors for IE
 * @author	Display:inline <contact@display-inline.fr>
 * @url		http://display-inline.fr
 * @version	1.0
 */
$(document).ready(function()
{
	if ($.browser.msie)
	{
		var version = parseInt($.browser.version);
		
		// All current IE versions
		$('.form fieldset > :last-child, .grey-block > :last-child').css('margin-bottom', '0');
		
		// IE 7 & 6
		if (version < 8)
		{
			// Attribute selectors
			$('.form div:last-child, .form p:last-child').addClass('form-last-child');
			$('.form input[type=text]').addClass('form-text');
			$('.form input[type=checkbox], .form input[type=radio]').addClass('form-radio');
			$('.form input[type=checkbox] + label, .form input[type=radio] + label').addClass('form-radio-label');
			
			// Pseudo-content
			$('.form .required label, .form .required .label').after('<span class="form-label-after"> *</span>');
			$('.form .error label, .form .error .label').before('<span class="form-label-before"><img src="images/icons/stop_round.png" /></span>');
			
			// Events selector
			$('.form input[type=text]').focus(function()
			{
				$(this).addClass('form-text-focus');
			}).blur(function()
			{
				$(this).removeClass('form-text-focus');
			});
			
			// Nth-child selectors
			$('.colx4-col:first-child').css('margin-left', '0');
		}
		
		// IE 6 Only
		if (version < 7)
		{
			// IE6 multiple class selectors bug workaround
			if ($(document.body).hasClass('overhang') && $(document.body).hasClass('nolines'))
			{
				$('#content').addClass('overhang-nolines');
			}
			
			// Unhandled :hover
			$('#highlights li, .nav li, #header-links li').hover(function()
			{
				$(this).addClass('hover');
			}, function()
			{
				$(this).removeClass('hover');
			});
			
			// Properties not applied by class form-text-focus
			$('.form input[type=text]').css('padding', '2px 4px 7px 4px');
			
			// PNG fixes
			DD_belatedPNG.fix('#top-line, #top-line .wrapper, #header, #header h1 a, #header-links, #header-links ul, #highlights li');
			DD_belatedPNG.fix('#contact-form, .search-form button, .nav li, #page-top-link, #footer, #footer .smallCol');
			DD_belatedPNG.fix('#content .largeCol, #content .full-content, #content .smallCol p#locate');
			DD_belatedPNG.fix('#logo img, .slideshow li a, .slideshow li a span, .left-border, .right-border, .form fieldset');
			DD_belatedPNG.fix('.messages .error-msg, .messages .warning-msg, .messages .information-msg, .messages .confirmation-msg');
			DD_belatedPNG.fix('.form .required input.form-text, .form .required textarea, .form .error .form-label-before img');
			DD_belatedPNG.fix('.blog-date, .widget ul li a, #header-links li a img, #footer-links li a img, .projects-img-shadow');
		}
		
		// IE 7 & 6
		if (version < 8)
		{
			// Force re-rendering
			$(document.body).append('<div>Test</div>').children(':last').remove();
		}
	}
});