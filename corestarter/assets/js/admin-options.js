/**
 * Corestarter Admin Options Script
 *
 * Handles tab switching, color pickers, media uploaders,
 * social icon repeater, and conditional field visibility.
 *
 * @package Corestarter
 * @since   1.1.0
 */

( function ( $ ) {
	'use strict';

	/* ==================================================
	   Tab Switching
	   ================================================== */

	function initTabs() {
		var $tabs   = $( '.cs-nav-tabs .nav-tab' );
		var $panels = $( '.cs-tab-content' );

		function activateTab( tabId ) {
			$tabs.removeClass( 'nav-tab-active' );
			$panels.removeClass( 'active' );
			$( 'a[data-tab="' + tabId + '"]' ).addClass( 'nav-tab-active' );
			$( '#' + tabId ).addClass( 'active' );
		}

		function setTabUrl( tabId ) {
			// Use a query-param so the value survives the options.php redirect.
			var url = new URL( window.location.href );
			url.searchParams.set( 'tab', tabId );
			url.hash = ''; // clear any stale hash
			var newUrl = url.pathname + url.search;
			window.history.replaceState( null, null, newUrl );

			// Keep _wp_http_referer in sync — WordPress redirects back to
			// whatever this field says after saving, so the tab is preserved.
			$( 'input[name="_wp_http_referer"]' ).val( newUrl );
		}

		// Restore active tab from URL query-param (survives the save redirect).
		var urlParams = new URLSearchParams( window.location.search );
		var activeTab = urlParams.get( 'tab' );
		if ( activeTab && $( '#' + activeTab ).length ) {
			activateTab( activeTab );
		}

		$tabs.on( 'click', function ( e ) {
			e.preventDefault();
			var target = $( this ).data( 'tab' );
			activateTab( target );
			setTabUrl( target );
		} );
	}

	/* ==================================================
	   Color Pickers
	   ================================================== */

	function initColorPickers() {
		$( '.cs-color-picker' ).wpColorPicker();
	}

	/* ==================================================
	   Media Uploader (Logo)
	   ================================================== */

	function initMediaUploaders() {
		var strings = window.corestarterAdmin || {};

		// Logo upload.
		$( document ).on( 'click', '.cs-upload-btn', function ( e ) {
			e.preventDefault();
			var target  = $( this ).data( 'target' );
			var $id     = $( '#' + target + '-id' );
			var $preview = $( '#' + target + '-preview' );
			var $remove = $( '.cs-remove-btn[data-target="' + target + '"]' );

			var frame = wp.media( {
				title: strings.mediaTitle || 'Choose Image',
				button: { text: strings.mediaButton || 'Use This Image' },
				multiple: false,
				library: { type: 'image' },
			} );

			frame.on( 'select', function () {
				var attachment = frame.state().get( 'selection' ).first().toJSON();
				$id.val( attachment.id );
				$preview.html( '<img src="' + attachment.url + '" alt="">' );
				$remove.show();
			} );

			frame.open();
		} );

		// Logo remove.
		$( document ).on( 'click', '.cs-remove-btn', function ( e ) {
			e.preventDefault();
			var target = $( this ).data( 'target' );
			$( '#' + target + '-id' ).val( '' );
			$( '#' + target + '-preview' ).empty();
			$( this ).hide();
		} );
	}

	/* ==================================================
	   Social Icons Repeater
	   ================================================== */

	function initSocialRepeater() {
		var $repeater = $( '#cs-social-repeater' );
		var template  = $( '#tmpl-cs-social-row' ).html() || '';
		var strings   = window.corestarterAdmin || {};

		// Get next available index.
		function getNextIndex() {
			var max = -1;
			$repeater.find( '.cs-social-row' ).each( function () {
				var idx = parseInt( $( this ).data( 'index' ), 10 );
				if ( idx > max ) {
					max = idx;
				}
			} );
			return max + 1;
		}

		// Add row.
		$( '#cs-add-social' ).on( 'click', function () {
			var index = getNextIndex();
			var html  = template.replace( /__INDEX__/g, index );
			$repeater.append( html );
		} );

		// Remove row.
		$repeater.on( 'click', '.cs-social-remove-row', function () {
			$( this ).closest( '.cs-social-row' ).remove();
			reindexRows();
		} );

		// Reindex rows after removal.
		function reindexRows() {
			$repeater.find( '.cs-social-row' ).each( function ( i ) {
				var $row = $( this );
				$row.attr( 'data-index', i );
				$row.find( '[name]' ).each( function () {
					var name = $( this ).attr( 'name' );
					name = name.replace( /\[social_icons\]\[\d+\]/, '[social_icons][' + i + ']' );
					$( this ).attr( 'name', name );
				} );
			} );
		}

		// Social icon upload.
		$repeater.on( 'click', '.cs-social-upload-btn', function ( e ) {
			e.preventDefault();
			var $row     = $( this ).closest( '.cs-social-row' );
			var $id      = $row.find( '.cs-social-icon-id' );
			var $preview = $row.find( '.cs-social-icon-preview' );
			var $remove  = $row.find( '.cs-social-remove-icon-btn' );

			var frame = wp.media( {
				title: strings.mediaTitle || 'Choose Image',
				button: { text: strings.mediaButton || 'Use This Image' },
				multiple: false,
				library: { type: 'image' },
			} );

			frame.on( 'select', function () {
				var attachment = frame.state().get( 'selection' ).first().toJSON();
				$id.val( attachment.id );
				$preview.html( '<img src="' + attachment.url + '" alt="">' );
				$remove.show();
			} );

			frame.open();
		} );

		// Social icon remove.
		$repeater.on( 'click', '.cs-social-remove-icon-btn', function ( e ) {
			e.preventDefault();
			var $row = $( this ).closest( '.cs-social-row' );
			$row.find( '.cs-social-icon-id' ).val( '' );
			$row.find( '.cs-social-icon-preview' ).empty();
			$( this ).hide();
		} );
	}

	/* ==================================================
	   Container Type Toggle
	   ================================================== */

	function initContainerToggle() {
		var $radios   = $( 'input[name="corestarter_options[container_type]"]' );
		var $widthRow = $( '.cs-container-width-row' );

		$radios.on( 'change', function () {
			if ( 'full' === $( this ).val() ) {
				$widthRow.hide();
			} else {
				$widthRow.show();
			}
		} );
	}

	/* ==================================================
	   Initialize All
	   ================================================== */

	$( document ).ready( function () {
		initTabs();
		initColorPickers();
		initMediaUploaders();
		initSocialRepeater();
		initContainerToggle();
	} );

} )( jQuery );
