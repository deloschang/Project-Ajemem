/*
 * SimpleModal OSX Style Modal Dialog
 * http://www.ericmmartin.com/projects/simplemodal/
 * http://code.google.com/p/simplemodal/
 *
 * Copyright (c) 2010 Eric Martin - http://ericmmartin.com
 *
 * Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Revision: $Id: osx.js 238 2010-03-11 05:56:57Z emartin24 $
 */
jQuery(function ($) {
	osx_modal();
});

function osx_modal(){
	var OSX = {
		container: null,
		init: function () {
			$("input.osx, a.osx").click(function (e) {
				e.preventDefault();	
				$("#osx-modal-content").modal({
					overlayId: 'osx-overlay',
					containerId: 'osx-container',
					closeHTML: null,
					minHeight: 80,
					opacity: 65, 
					position: ['0',],
					overlayClose: true,
					onOpen: OSX.open,
					onClose: OSX.close
				});
			});
		},
		open: function (d) {
			var viewheight;
			if (typeof window.innerWidth != 'undefined') {
				viewheight = window.innerHeight
			} else if (typeof(document.documentElement) != 'undefined' && typeof(document.documentElement.clientWidth)!='undefined' && document.documentElement.clientWidth != 0) {
				viewheight = document.documentElement.clientHeight
			} else {
				viewheight = document.getElementsByTagName('body')[0].clientHeight
			}			
			
			var self = this;
			var ht;
			self.container = d.container[0];
			d.overlay.fadeIn('slow', function () {
				$("#osx-modal-content", self.container).show();
				var title = $("#osx-modal-title", self.container);
				title.show();
				d.container.slideDown('slow', function () {
					setTimeout(function () {
						var h = ht = $("#osx-modal-data", self.container).height()
							+ title.height()
							+ 20; // padding
						if(h<viewheight){
							
						}else{
							h = viewheight-30;
							$("#osx-modal-data").css({height:viewheight-100+'px',overflowY:"auto"});
						}
						d.container.animate(
							{height:h}, 
							200,
							function () {
								$("div.close", self.container).show();
								$("#osx-modal-data", self.container).show();
							}
						);
					}, 1500);
				});
			})
		},
		close: function (d) {
			var self = this; // this = SimpleModal object
			d.container.animate(
				{top:"-" + (d.container.height() + 20)},
				1000,
				function () {
					self.close(); // or $.modal.close();
				}
			);
		}
	};

	OSX.init();
}
