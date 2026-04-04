/**
 * Corestarter Main Script
 *
 * Handles sticky header scroll effect, back-to-top button,
 * and smooth scroll behavior.
 *
 * @package Corestarter
 * @since   1.0.0
 */

( function () {
	'use strict';

	const header    = document.getElementById( 'masthead' );
	const backToTop = document.getElementById( 'back-to-top' );
	const settings  = window.corestarterSettings || {};

	/**
	 * Throttle function calls for scroll performance.
	 *
	 * @param {Function} fn       Callback function.
	 * @param {number}   delay    Throttle delay in ms.
	 * @return {Function} Throttled function.
	 */
	function throttle( fn, delay ) {
		let lastCall = 0;
		return function ( ...args ) {
			const now = Date.now();
			if ( now - lastCall >= delay ) {
				lastCall = now;
				fn.apply( this, args );
			}
		};
	}

	/**
	 * Handle scroll events for sticky header and back-to-top.
	 */
	function onScroll() {
		const scrollY = window.scrollY;

		// Sticky header scroll class.
		if ( header && settings.stickyHeader ) {
			if ( scrollY > 10 ) {
				header.classList.add( 'is-scrolled' );
			} else {
				header.classList.remove( 'is-scrolled' );
			}
		}

		// Back to top visibility.
		if ( backToTop ) {
			if ( scrollY > 300 ) {
				backToTop.classList.add( 'is-visible' );
			} else {
				backToTop.classList.remove( 'is-visible' );
			}
		}
	}

	// Listen to scroll with throttle.
	window.addEventListener( 'scroll', throttle( onScroll, 100 ), { passive: true } );

	// Back to top click.
	if ( backToTop ) {
		backToTop.addEventListener( 'click', function () {
			window.scrollTo( {
				top: 0,
				behavior: 'smooth',
			} );
		} );
	}

	// Run once on load.
	onScroll();

} )();
