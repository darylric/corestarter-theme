/**
 * Corestarter Navigation Script
 *
 * Handles the mobile menu toggle and keyboard accessibility
 * for dropdown menus.
 *
 * @package Corestarter
 * @since   1.0.0
 */

( function () {
	'use strict';

	const nav    = document.getElementById( 'site-navigation' );
	const toggle = nav ? nav.querySelector( '.menu-toggle' ) : null;

	if ( ! nav || ! toggle ) {
		return;
	}

	/**
	 * Toggle mobile menu.
	 */
	toggle.addEventListener( 'click', function () {
		const expanded = this.getAttribute( 'aria-expanded' ) === 'true';
		this.setAttribute( 'aria-expanded', String( ! expanded ) );
		nav.classList.toggle( 'toggled' );
	} );

	/**
	 * Close mobile menu when clicking outside.
	 */
	document.addEventListener( 'click', function ( event ) {
		if ( ! nav.contains( event.target ) && nav.classList.contains( 'toggled' ) ) {
			toggle.setAttribute( 'aria-expanded', 'false' );
			nav.classList.remove( 'toggled' );
		}
	} );

	/**
	 * Close mobile menu on Escape key.
	 */
	document.addEventListener( 'keydown', function ( event ) {
		if ( event.key === 'Escape' && nav.classList.contains( 'toggled' ) ) {
			toggle.setAttribute( 'aria-expanded', 'false' );
			nav.classList.remove( 'toggled' );
			toggle.focus();
		}
	} );

	/**
	 * Add keyboard support for dropdown submenus.
	 * When a menu item with children receives focus, show the submenu.
	 */
	const menuItems = nav.querySelectorAll( '.menu-item-has-children > a' );
	menuItems.forEach( function ( item ) {
		item.addEventListener( 'focus', function () {
			const parent = this.parentElement;
			// Close siblings.
			const siblings = parent.parentElement.querySelectorAll( '.menu-item-has-children' );
			siblings.forEach( function ( sibling ) {
				if ( sibling !== parent ) {
					sibling.classList.remove( 'focus' );
				}
			} );
			parent.classList.add( 'focus' );
		} );
	} );

	// Remove focus class when tabbing out of submenu.
	nav.addEventListener( 'focusout', function ( event ) {
		setTimeout( function () {
			if ( ! nav.contains( document.activeElement ) ) {
				const focused = nav.querySelectorAll( '.focus' );
				focused.forEach( function ( el ) {
					el.classList.remove( 'focus' );
				} );
			}
		}, 0 );
	} );

} )();
