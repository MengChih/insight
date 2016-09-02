function doOwlFeatured() {
	if ( jQuery().owlCarousel ) {
		var use_rtl = jQuery( 'body' ).hasClass( 'rtl' );

		var autoplay = jQuery( '#slider' ).hasClass( 'slide' );
		var loop = jQuery( '#slider' ).hasClass( 'continuous' );

		if ( autoplay ) {
			if ( jQuery( '#slider' ).hasClass( 'slow' ) ) {
				var autoplayTimeout = 7000;
			} else if ( jQuery( '#slider').hasClass( 'fast' ) ) {
				var autoplayTimeout = 3000;
			} else {
				autoplayTimeout = 5000;
			}
		} else {
			autoplayTimeout = false;
		}

		jQuery( '.owl-carousel' ).owlCarousel({
			rtl: use_rtl,
			nav: false,
			items: 1,
			loop: loop,
			autoplay: autoplay,
			autoplayTimeout: autoplayTimeout
		});
	}
}

jQuery( document ).ready( function() {
	if ( jQuery( '#slider' ).length ) {
		doOwlFeatured();
	}
});