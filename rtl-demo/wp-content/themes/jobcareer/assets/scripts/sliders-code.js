 /*  *  Document ready function  */var $ = jQuery;jQuery(document).ready(function ($) {	"use strict";		/*		*  Job list		*/		jQuery('.jobs-list').slick({			autoplay: true,			autoplaySpeed: 2000,		});		/*		*  End Job list		*/						/*		*  Blog slider		*/		jQuery('.blog-slider').slick({			slidesToShow: 3,			slidesToScroll: 1,			autoplay: true,			autoplaySpeed: 2000,		});		/*		* End  Blog slider		*/						/*		* Testimonial-slider		*/		jQuery('.testimonial-slider').slick({			autoplay: true,			autoplaySpeed: 2000,		});		/*		*  End Testimonial-slider		*/						/*		*  Save information		*/		jQuery('.save-info').slick({			autoplay: true,			autoplaySpeed: 2000,		});		/*		*  End Save information		*/						/*		*  Hiring slider		*/		jQuery('.hiring-slider').on('init', function(slick) {			console.log('fired!');			$('.hiring-slider').find('>ul').fadeIn(2000);			$('.hiring-slider').addClass('loaded');		});		/*		*  End Hiring slider		*/								/*		*  Hiring slider		*/		jQuery('.hiring-slider').slick({			autoplay: true,			autoplaySpeed: 2000,		});		/*		*  End Hiring slider		*/						/*		*  clients		*/		jQuery('.clients').slick({			dots: false,			speed: 300,			slidesToShow: 6,			slidesToScroll: 1,			autoplay: true,			autoplaySpeed: 2000,		});		/*		*  End clients		*/						/*		*  Slider medium		*/		jQuery('.slider-medium').slick({			slidesToShow: 1,			dots: true,			slidesToScroll: 1,			autoplay: false,			autoplaySpeed: 2000,			arrows: false,		});		/*		*  End Slider medium		*/	}); /* * End Document ready function*/