/* Sticky header code */
function StickyHeader() {
	var $ = jQuery;
	if ($('.has_sticky').length) {
		var el = $('.has_sticky .main-head');
		var stickyTop = $('.has_sticky .main-head').offset().top;
		var stickyHeight = $('.has_sticky .main-head').height();
		var AdminBarHeight = $("#wpadminbar").height();
		$('.has_sticky .main-head').css("margin-top", AdminBarHeight);
		$(window).scroll(function () {
			var windowTop = $(window).scrollTop();
			if (stickyTop < windowTop) {
			el.addClass("scroll-to-fixed-fixed");
			$('.wrapper').css("padding-top", stickyHeight);
				el.css({
					position: 'fixed',
					top: 0
				});
			} else {
				el.css({
					position: '',
					top: ''
				});
				el.removeClass("scroll-to-fixed-fixed");
				$('.wrapper').css("padding-top", '');
			}
		});
	}
}

StickyHeader();
$(window).resize(function () {
	StickyHeader();
});

    /* sticky header code end */