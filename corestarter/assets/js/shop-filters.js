/**
 * Corestarter Shop Filters
 *
 * Handles the mobile filter drawer toggle for the shop sidebar.
 *
 * @package Corestarter
 * @since   1.0.0
 */

( function () {
	'use strict';

	const toggle  = document.querySelector( '.shop-filter-toggle' );
	const sidebar = document.getElementById( 'shop-sidebar' );
	const close   = document.querySelector( '.shop-sidebar-close' );
	const overlay = document.querySelector( '.shop-sidebar-overlay' );

	if ( ! toggle || ! sidebar ) {
		return;
	}

	function openFilters() {
		sidebar.classList.add( 'is-open' );
		if ( overlay ) {
			overlay.classList.add( 'is-visible' );
		}
		document.body.style.overflow = 'hidden';
		if ( close ) {
			close.focus();
		}
	}

	function closeFilters() {
		sidebar.classList.remove( 'is-open' );
		if ( overlay ) {
			overlay.classList.remove( 'is-visible' );
		}
		document.body.style.overflow = '';
		toggle.focus();
	}

	toggle.addEventListener( 'click', openFilters );

	if ( close ) {
		close.addEventListener( 'click', closeFilters );
	}

	if ( overlay ) {
		overlay.addEventListener( 'click', closeFilters );
	}

	document.addEventListener( 'keydown', function ( event ) {
		if ( event.key === 'Escape' && sidebar.classList.contains( 'is-open' ) ) {
			closeFilters();
		}
	} );
} )();
