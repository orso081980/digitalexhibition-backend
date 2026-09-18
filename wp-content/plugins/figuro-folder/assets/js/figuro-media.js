( function ( $ ) {
	'use strict';

	var state = {
		tree: [],
		counts: { all: 0, uncategorized: 0 },
		currentFolder: '', // '' = All Files, 'uncategorized', or a numeric term id (as string)
		page: 1,
		search: '',
		gridItemIds: [], // ids currently shown in the grid, in display order — powers modal prev/next
		expanded: {}, // term id (string) => true, when its children are visible
		bulkMode: false,
		selected: {} // id (string) => true, only meaningful while bulkMode is on
	};

	var $treePinned    = $( '#figuro-tree-pinned' );
	var $tree          = $( '#figuro-tree' );
	var $grid          = $( '#figuro-grid' );
	var $pagination    = $( '#figuro-pagination' );
	var $heading       = $( '#figuro-current-folder-name' );
	var $folderCount   = $( '#figuro-folder-count' );
	var $search        = $( '#figuro-search' );
	var $dropzone      = $( '#figuro-dropzone' );
	var $uploadLog     = $( '#figuro-upload-log' );
	var $fileInput     = $( '#figuro-file-input' );
	var $addMediaBtn   = $( '#figuro-add-media' );
	var $mediaApp      = $( '#figuro-media-app' );
	var $bulkToggle    = $( '#figuro-bulk-toggle' );
	var $bulkBar       = $( '#figuro-bulk-bar' );
	var $bulkCount     = $( '#figuro-bulk-count' );
	var $bulkDelete    = $( '#figuro-bulk-delete' );
	var $bulkCancel    = $( '#figuro-bulk-cancel' );

	var editFrame; // Reused wp.media "edit attachment details" frame — same UI as the core Media Library.
	var filterProps; // Backbone model behind the "All media items" / "All dates" selects (see initFilters()).

	function ajax( action, data ) {
		return $.post( FiguroMedia.ajaxUrl, $.extend( { action: action, nonce: FiguroMedia.nonce }, data || {} ) );
	}

	function escapeHtml( str ) {
		return $( '<div>' ).text( str == null ? '' : str ).html();
	}

	function flatten( nodes, depth, out ) {
		depth = depth || 0;
		out = out || [];
		nodes.forEach( function ( node ) {
			out.push( { id: node.id, name: node.name, depth: depth } );
			if ( node.children && node.children.length ) {
				flatten( node.children, depth + 1, out );
			}
		} );
		return out;
	}

	function ancestorIds( nodes, targetId, trail ) {
		for ( var i = 0; i < nodes.length; i++ ) {
			var node = nodes[ i ];
			if ( node.id.toString() === targetId.toString() ) {
				return trail;
			}
			if ( node.children && node.children.length ) {
				var found = ancestorIds( node.children, targetId, trail.concat( [ node.id.toString() ] ) );
				if ( found ) return found;
			}
		}
		return null;
	}

	function pinnedNodeHtml( id, name, icon, count ) {
		return (
			'<li>' +
			'<div class="figuro-node figuro-node-pinned" data-id="' + id + '">' +
			'<span class="figuro-node-toggle figuro-node-toggle-spacer"></span>' +
			'<span class="dashicons ' + icon + ' figuro-node-icon"></span>' +
			'<span class="figuro-node-name">' + escapeHtml( name ) + '</span>' +
			'<span class="figuro-node-count">' + count + '</span>' +
			'</div>' +
			'</li>'
		);
	}

	function renderPinned() {
		var html = '';
		html += pinnedNodeHtml( '', FiguroMedia.i18n.allFiles, 'dashicons-admin-media', state.counts.all );
		html += pinnedNodeHtml( 'uncategorized', FiguroMedia.i18n.uncategorized, 'dashicons-media-default', state.counts.uncategorized );
		$treePinned.html( html );
		highlightActive();
	}

	function nodeHtml( node, hasChildren ) {
		var toggle = hasChildren
			? '<button type="button" class="figuro-node-toggle" aria-label="' + escapeHtml( FiguroMedia.i18n.toggle ) + '"><span class="dashicons dashicons-arrow-right-alt2"></span></button>'
			: '<span class="figuro-node-toggle figuro-node-toggle-spacer"></span>';

		var actions =
			'<span class="figuro-node-actions">' +
			'<button type="button" class="figuro-add-sub" title="' + escapeHtml( FiguroMedia.i18n.newFolder ) + '"><span class="dashicons dashicons-plus-alt2"></span></button>' +
			'<button type="button" class="figuro-rename" title="' + escapeHtml( FiguroMedia.i18n.rename ) + '"><span class="dashicons dashicons-edit"></span></button>' +
			'<button type="button" class="figuro-delete" title="' + escapeHtml( FiguroMedia.i18n.delete ) + '"><span class="dashicons dashicons-trash"></span></button>' +
			'</span>';

		var countHtml = '<span class="figuro-node-count">' + node.count + '</span>';

		return (
			'<div class="figuro-node" data-id="' + node.id + '" draggable="true">' +
			toggle +
			'<span class="dashicons dashicons-category figuro-node-icon"></span>' +
			'<span class="figuro-node-name">' + escapeHtml( node.name ) + '</span>' +
			countHtml +
			actions +
			'</div>'
		);
	}

	function renderNodes( nodes ) {
		var html = '';
		nodes.forEach( function ( node ) {
			var hasChildren = !! ( node.children && node.children.length );
			var isExpanded  = !! state.expanded[ node.id ];

			html += '<li class="figuro-branch' + ( hasChildren ? ' has-children' : '' ) + ( isExpanded ? ' is-expanded' : '' ) + '">';
			html += nodeHtml( node, hasChildren );
			if ( hasChildren ) {
				html += '<ul class="figuro-subtree">' + renderNodes( node.children ) + '</ul>';
			}
			html += '</li>';
		} );
		return html;
	}

	function renderTree() {
		$tree.html( renderNodes( state.tree ) );
		highlightActive();
	}

	function highlightActive() {
		$( '.figuro-node' ).removeClass( 'is-active' );
		$( '.figuro-node[data-id="' + state.currentFolder + '"]' ).first().addClass( 'is-active' );
	}

	function expandAncestorsOf( id ) {
		if ( '' === id || 'uncategorized' === id ) return;
		var trail = ancestorIds( state.tree, id, [] );
		if ( ! trail ) return;
		trail.forEach( function ( ancestorId ) {
			state.expanded[ ancestorId ] = true;
		} );
	}

	// Renders tree/counts data returned by figuro_get_tree — or embedded
	// straight in a mutation's own response (create/rename/move/delete folder,
	// move/delete/upload attachments), which saves a whole extra admin-ajax.php
	// round trip (and its full WordPress bootstrap) after every action.
	function applyTreeData( data ) {
		state.tree = data.tree;
		state.counts = data.counts || { all: 0, uncategorized: 0 };
		renderTree();
		renderPinned();
	}

	function loadTree() {
		return ajax( 'figuro_get_tree' ).done( function ( res ) {
			if ( res.success ) {
				applyTreeData( res.data );
			}
		} );
	}

	function folderName( id ) {
		if ( '' === id ) return FiguroMedia.i18n.allFiles;
		if ( 'uncategorized' === id ) return FiguroMedia.i18n.uncategorized;
		var found = null;
		flatten( state.tree ).forEach( function ( n ) {
			if ( n.id.toString() === id.toString() ) found = n.name;
		} );
		return found || '';
	}

	function selectFolder( id ) {
		state.currentFolder = id;
		state.page = 1;
		state.selected = {};
		updateBulkBar();
		$heading.text( folderName( id ) );
		expandAncestorsOf( id );
		renderTree();
		loadGrid();
	}

	// The shared model behind the "All media items" / "All dates" selects
	// (see initFilters()) is treated as unset when a prop is null/false —
	// that's how the core AttachmentFilters views represent "no filter".
	function filterVal( v ) {
		return ( null === v || undefined === v || false === v ) ? '' : v;
	}

	function applyGridData( data ) {
		$folderCount.text(
			data.total
				? data.total + ' ' + ( 1 === data.total ? FiguroMedia.i18n.file : FiguroMedia.i18n.files )
				: ''
		);

		state.gridItemIds = data.items.map( function ( item ) { return item.id; } );

		if ( ! data.items.length ) {
			$grid.html( '<div class="figuro-empty"><span class="dashicons dashicons-portfolio"></span><p>' + escapeHtml( FiguroMedia.i18n.noItems ) + '</p></div>' );
			$pagination.empty();
			return;
		}

		var html = '';
		data.items.forEach( function ( item ) {
			var isSelected = !! state.selected[ item.id.toString() ];
			html +=
				'<div class="figuro-item' + ( isSelected ? ' is-selected' : '' ) + '" data-id="' + item.id + '" draggable="true">' +
				'<span class="figuro-item-check"><span class="dashicons dashicons-yes"></span></span>' +
				'<div class="figuro-item-thumb"><img src="' + escapeHtml( item.thumb ) + '" alt="" loading="lazy" draggable="false" /></div>' +
				'<div class="figuro-item-title" title="' + escapeHtml( item.title ) + '">' + escapeHtml( item.title ) + '</div>' +
				'</div>';
		} );
		$grid.html( html );
		renderPagination( data.page, data.totalPages );
	}

	function loadGrid() {
		$grid.html( '<div class="figuro-loading">' + escapeHtml( FiguroMedia.i18n.loading ) + '</div>' );
		$pagination.empty();

		var filters = filterProps ? filterProps.toJSON() : {};

		return ajax( 'figuro_get_attachments', {
			folder: state.currentFolder,
			paged: state.page,
			search: state.search,
			type: filterVal( filters.type ),
			uploadedTo: filterVal( filters.uploadedTo ),
			author: filterVal( filters.author ),
			year: filterVal( filters.year ),
			monthnum: filterVal( filters.monthnum )
		} ).done( function ( res ) {
			if ( ! res.success ) {
				$grid.html( '<div class="figuro-empty">' + escapeHtml( FiguroMedia.i18n.error ) + '</div>' );
				return;
			}
			applyGridData( res.data );
		} );
	}

	// Initial page load: tree + "All Files" grid in one admin-ajax.php request
	// instead of two, since each admin-ajax.php call re-bootstraps all of
	// WordPress — firing it twice just to paint the first screen is the
	// single biggest thing slowing this page down versus core's Media Library.
	function loadBootstrap() {
		$grid.html( '<div class="figuro-loading">' + escapeHtml( FiguroMedia.i18n.loading ) + '</div>' );

		return ajax( 'figuro_bootstrap' ).done( function ( res ) {
			if ( ! res.success ) {
				loadTree();
				loadGrid();
				return;
			}
			applyTreeData( res.data );
			applyGridData( res.data.attachments );
		} );
	}

	function paginationRange( page, totalPages ) {
		var delta = 2;
		var range = [];

		for ( var i = 1; i <= totalPages; i++ ) {
			if ( 1 === i || totalPages === i || ( i >= page - delta && i <= page + delta ) ) {
				range.push( i );
			}
		}

		var withGaps = [];
		var prev = 0;
		range.forEach( function ( i ) {
			if ( prev && i - prev > 1 ) {
				withGaps.push( '…' );
			}
			withGaps.push( i );
			prev = i;
		} );

		return withGaps;
	}

	function renderPagination( page, totalPages ) {
		if ( totalPages <= 1 ) {
			$pagination.empty();
			return;
		}

		var html = '';

		html += '<button type="button" class="figuro-page-nav" data-page="' + ( page - 1 ) + '"' + ( page <= 1 ? ' disabled' : '' ) + ' aria-label="' + escapeHtml( FiguroMedia.i18n.prevPage ) + '">&larr;</button>';

		paginationRange( page, totalPages ).forEach( function ( i ) {
			if ( '…' === i ) {
				html += '<span class="figuro-page-gap">&hellip;</span>';
			} else {
				html += '<button type="button" class="' + ( i === page ? 'is-current' : '' ) + '" data-page="' + i + '">' + i + '</button>';
			}
		} );

		html += '<button type="button" class="figuro-page-nav" data-page="' + ( page + 1 ) + '"' + ( page >= totalPages ? ' disabled' : '' ) + ' aria-label="' + escapeHtml( FiguroMedia.i18n.nextPage ) + '">&rarr;</button>';

		$pagination.html( html );
	}

	// Renders the same "All media items" / "All dates" selects shown on the
	// core Media Library screen, by reusing WordPress's own Backbone filter
	// views (and their localized option lists) rather than hand-building
	// <select> markup. They share one model; picking a value in either
	// reloads the grid with that filter applied server-side.
	function initFilters() {
		if ( ! window.wp || ! wp.media || ! wp.media.view.AttachmentFilters || ! wp.media.view.AttachmentFilters.All || ! wp.media.view.DateFilter ) {
			return;
		}

		filterProps = new Backbone.Model( {
			status:     null,
			type:       null,
			uploadedTo: null,
			orderby:    'date',
			order:      'DESC',
			author:     null,
			monthnum:   false,
			year:       false
		} );

		// AttachmentFilters.All only reaches into its controller to decide
		// whether to show a "Trashed" filter for grid mode, which our custom
		// query doesn't support — keep it hidden by always reporting false.
		var fakeFilterController = {
			isModeActive: function () {
				return false;
			}
		};

		var typeFilter = new wp.media.view.AttachmentFilters.All( {
			controller: fakeFilterController,
			model: filterProps,
			priority: -80
		} );

		var dateFilter = new wp.media.view.DateFilter( {
			controller: fakeFilterController,
			model: filterProps,
			priority: -75
		} );

		$( '#figuro-filter-type' ).empty().append( typeFilter.el );
		$( '#figuro-filter-date' ).empty().append( dateFilter.el );

		filterProps.on( 'change', function () {
			state.page = 1;
			state.selected = {};
			updateBulkBar();
			loadGrid();
		} );
	}

	// The frame already navigates on Alt+Left/Alt+Right (core's own handler,
	// bound as soon as the modal opens). Plain arrow keys are added here so
	// navigation works without the modifier too; modified presses are left
	// alone so the two handlers don't both fire and skip two items at once.
	function onModalKeydown( e ) {
		var tag = e.target.nodeName;
		if ( ( 'INPUT' === tag || 'TEXTAREA' === tag || 'SELECT' === tag ) && ! e.target.disabled ) {
			return;
		}
		if ( e.altKey || e.metaKey || e.ctrlKey ) {
			return;
		}
		if ( 37 === e.keyCode ) {
			e.preventDefault();
			editFrame.previousMediaItem();
		} else if ( 39 === e.keyCode ) {
			e.preventDefault();
			editFrame.nextMediaItem();
		}
	}

	function bindModalKeyNav() {
		$( document ).off( 'keydown.figuro-media-nav' ).on( 'keydown.figuro-media-nav', onModalKeydown );
	}

	function unbindModalKeyNav() {
		$( document ).off( 'keydown.figuro-media-nav' );
	}

	// Opens the same "Attachment Details" modal used by the core Media Library
	// (title, caption, alt text, description, file URL, edit image, delete
	// permanently, …) via WordPress's own wp.media Backbone views/models —
	// no custom edit UI or endpoints needed.
	function openAttachmentModal( id, $item ) {
		if ( ! window.wp || ! wp.media || ! wp.media.view.MediaFrame.EditAttachments ) {
			return;
		}

		// Fetching the library (below) can take a moment on a slow connection;
		// give the clicked card an immediate "opening" state so the click
		// doesn't feel unresponsive while we wait for it.
		if ( $item ) {
			$item.addClass( 'is-opening' );
		}

		// Build the modal's prev/next set from whatever is currently on
		// screen, in the same order, and fetch it as one batch. A library
		// with a single item (or unfetched attachments) leaves the arrows
		// permanently disabled and, worse, can fire a `change:status` once
		// the fetch lands after the frame is already listening — which the
		// frame reads as "this attachment changed under me" and immediately
		// closes the modal it just opened. Loading everything up front (as
		// core's own grid router does before ever opening the frame) avoids
		// both problems.
		var ids = state.gridItemIds.length ? state.gridItemIds : [ id ];

		var library = wp.media.query( {
			post__in: ids,
			orderby: 'post__in',
			posts_per_page: ids.length
		} );

		library.more().done( function () {
			if ( $item ) {
				$item.removeClass( 'is-opening' );
			}

			var attachment = library.get( id ) || wp.media.attachment( id );

			if ( editFrame ) {
				editFrame.library = library;
				editFrame.open().trigger( 'refresh', attachment );
				bindModalKeyNav();
				return;
			}

			// EditAttachments normally runs inside the core Manage (grid) frame;
			// it only reaches back into that frame's controller on modal close,
			// to read the current search term for the URL. Stub that out since
			// we have no such frame here.
			var fakeController = {
				gridRouter: new wp.media.view.MediaFrame.Manage.Router(),
				browserView: {
					toolbar: {
						get: function () {
							return { $el: { val: function () { return ''; } } };
						}
					}
				}
			};

			editFrame = wp.media( {
				frame: 'edit-attachments',
				controller: fakeController,
				library: library,
				model: attachment
			} );

			// Refresh the grid/tree after the modal closes — covers edits and deletes.
			editFrame.on( 'close', function () {
				unbindModalKeyNav();
				loadTree();
				loadGrid();
			} );

			bindModalKeyNav();
		} ).fail( function () {
			if ( $item ) {
				$item.removeClass( 'is-opening' );
			}
			window.alert( FiguroMedia.i18n.error );
		} );
	}

	function moveAttachments( ids, folderId ) {
		ajax( 'figuro_move_attachments', { ids: ids, folder: folderId } ).done( function ( res ) {
			if ( res.success ) {
				applyTreeData( res.data );
				loadGrid();
			} else {
				window.alert( res.data && res.data.message ? res.data.message : FiguroMedia.i18n.error );
			}
		} );
	}

	// --- Bulk select ---------------------------------------------------------

	function setBulkMode( on ) {
		state.bulkMode = on;
		state.selected = {};
		$mediaApp.toggleClass( 'is-bulk-mode', on );
		$bulkToggle.prop( 'hidden', on );
		$bulkBar.prop( 'hidden', ! on );
		$grid.find( '.figuro-item' ).removeClass( 'is-selected' );
		updateBulkBar();
	}

	function updateBulkBar() {
		var count = 0;
		for ( var id in state.selected ) {
			if ( state.selected.hasOwnProperty( id ) ) count++;
		}
		$bulkCount.text( count ? count + ' ' + ( 1 === count ? FiguroMedia.i18n.itemSelected : FiguroMedia.i18n.itemsSelected ) : '' );
		$bulkDelete.prop( 'disabled', ! count );
	}

	function toggleItemSelected( id, $item ) {
		id = id.toString();
		if ( state.selected[ id ] ) {
			delete state.selected[ id ];
			$item.removeClass( 'is-selected' );
		} else {
			state.selected[ id ] = true;
			$item.addClass( 'is-selected' );
		}
		updateBulkBar();
	}

	function deleteSelectedAttachments() {
		var ids = Object.keys( state.selected );
		if ( ! ids.length ) return;
		if ( ! window.confirm( FiguroMedia.i18n.bulkDeleteConfirm ) ) return;

		$bulkDelete.prop( 'disabled', true );

		ajax( 'figuro_delete_attachments', { ids: ids } ).done( function ( res ) {
			if ( res.success ) {
				setBulkMode( false );
				applyTreeData( res.data );
				loadGrid();
			} else {
				window.alert( res.data && res.data.message ? res.data.message : FiguroMedia.i18n.error );
				updateBulkBar();
			}
		} );
	}

	// --- Upload: "Add Media File" button and drop-anywhere-on-the-page -------
	// Uploads land in whichever folder is currently open, same as dropping a
	// file into a real folder. This is a separate system from the internal
	// drag-and-drop above: that one moves an existing grid item onto a folder
	// (custom "text/figuro-*" drag data); this one reacts only to an actual
	// OS file drag (dataTransfer.types includes "Files"), so the two never
	// interfere with each other even though both ride on the same native
	// drag events.
	function isFileDrag( e ) {
		var dt = e.originalEvent && e.originalEvent.dataTransfer;
		return !! ( dt && $.inArray( 'Files', dt.types || [] ) > -1 );
	}

	function addUploadStatus( name ) {
		var $status = $(
			'<div class="figuro-upload-status">' +
			'<span class="dashicons dashicons-upload"></span>' +
			'<span class="figuro-upload-status-name" title="' + escapeHtml( name ) + '">' + escapeHtml( name ) + '</span>' +
			'</div>'
		).appendTo( $uploadLog );
		return $status;
	}

	function uploadOneFile( file ) {
		var $status = addUploadStatus( file.name );

		if ( FiguroMedia.maxUploadSize && file.size > FiguroMedia.maxUploadSize ) {
			$status.addClass( 'is-error' );
			$status.find( '.figuro-upload-status-name' ).text( file.name + ' — ' + FiguroMedia.i18n.fileTooBig );
			return;
		}

		var formData = new FormData();
		formData.append( 'action', 'figuro_upload_attachment' );
		formData.append( 'nonce', FiguroMedia.nonce );
		formData.append( 'folder', state.currentFolder );
		formData.append( 'file', file );

		$.ajax( {
			url: FiguroMedia.ajaxUrl,
			type: 'POST',
			data: formData,
			processData: false,
			contentType: false
		} ).done( function ( res ) {
			if ( res.success ) {
				$status.remove();
				applyTreeData( res.data );
				loadGrid();
			} else {
				$status.addClass( 'is-error' );
				$status.find( '.figuro-upload-status-name' ).text( ( res.data && res.data.message ) || FiguroMedia.i18n.error );
			}
		} ).fail( function () {
			$status.addClass( 'is-error' );
			$status.find( '.figuro-upload-status-name' ).text( FiguroMedia.i18n.error );
		} );
	}

	function uploadFiles( fileList ) {
		Array.prototype.forEach.call( fileList || [], uploadOneFile );
	}

	// --- Tree interactions -------------------------------------------------

	$( document ).on( 'click', '.figuro-tree .figuro-node', function ( e ) {
		if ( $( e.target ).closest( '.figuro-node-actions, .figuro-node-toggle' ).length ) {
			return;
		}
		selectFolder( $( this ).data( 'id' ).toString() );
	} );

	$tree.on( 'click', '.figuro-node-toggle:not(.figuro-node-toggle-spacer)', function ( e ) {
		e.stopPropagation();
		var $branch = $( this ).closest( '.figuro-branch' );
		var id = $branch.children( '.figuro-node' ).data( 'id' ).toString();
		if ( state.expanded[ id ] ) {
			delete state.expanded[ id ];
		} else {
			state.expanded[ id ] = true;
		}
		renderTree();
	} );

	$( '#figuro-add-root-folder' ).on( 'click', function () {
		var name = window.prompt( FiguroMedia.i18n.newFolderName );
		if ( ! name ) return;
		ajax( 'figuro_create_folder', { name: name, parent: 0 } ).done( function ( res ) {
			if ( res.success ) {
				applyTreeData( res.data );
			} else {
				window.alert( res.data && res.data.message ? res.data.message : FiguroMedia.i18n.error );
			}
		} );
	} );

	$tree.on( 'click', '.figuro-add-sub', function ( e ) {
		e.stopPropagation();
		var $node = $( this ).closest( '.figuro-node' );
		var parentId = $node.data( 'id' );
		var name = window.prompt( FiguroMedia.i18n.newFolderName );
		if ( ! name ) return;
		ajax( 'figuro_create_folder', { name: name, parent: parentId } ).done( function ( res ) {
			if ( res.success ) {
				state.expanded[ parentId.toString() ] = true;
				applyTreeData( res.data );
			} else {
				window.alert( res.data && res.data.message ? res.data.message : FiguroMedia.i18n.error );
			}
		} );
	} );

	$tree.on( 'click', '.figuro-rename', function ( e ) {
		e.stopPropagation();
		var $node   = $( this ).closest( '.figuro-node' );
		var id      = $node.data( 'id' );
		var current = $node.find( '.figuro-node-name' ).first().text();
		var name    = window.prompt( FiguroMedia.i18n.renamePrompt, current );
		if ( ! name || name === current ) return;
		ajax( 'figuro_rename_folder', { id: id, name: name } ).done( function ( res ) {
			if ( res.success ) {
				applyTreeData( res.data );
				if ( state.currentFolder.toString() === id.toString() ) {
					$heading.text( name );
				}
			} else {
				window.alert( res.data && res.data.message ? res.data.message : FiguroMedia.i18n.error );
			}
		} );
	} );

	$tree.on( 'click', '.figuro-delete', function ( e ) {
		e.stopPropagation();
		if ( ! window.confirm( FiguroMedia.i18n.deleteConfirm ) ) return;
		var id = $( this ).closest( '.figuro-node' ).data( 'id' );
		ajax( 'figuro_delete_folder', { id: id } ).done( function ( res ) {
			if ( res.success ) {
				if ( state.currentFolder.toString() === id.toString() ) {
					selectFolder( '' );
				}
				applyTreeData( res.data );
			} else {
				window.alert( res.data && res.data.message ? res.data.message : FiguroMedia.i18n.error );
			}
		} );
	} );

	// Drag & drop: reparent folders, or drop attachments onto a folder to move them.
	$( document ).on( 'dragstart', '.figuro-tree .figuro-node[draggable="true"]', function ( e ) {
		e.originalEvent.dataTransfer.setData( 'text/figuro-folder', $( this ).data( 'id' ).toString() );
	} );

	$( document ).on( 'dragover', '.figuro-node', function ( e ) {
		e.preventDefault();
		$( this ).addClass( 'is-dragover' );
	} );

	$( document ).on( 'dragleave', '.figuro-node', function () {
		$( this ).removeClass( 'is-dragover' );
	} );

	$( document ).on( 'drop', '.figuro-node', function ( e ) {
		e.preventDefault();
		$( this ).removeClass( 'is-dragover' );

		var targetId = $( this ).data( 'id' ).toString();
		var dt = e.originalEvent.dataTransfer;
		var attachmentIds = dt.getData( 'text/figuro-attachments' );
		var folderId = dt.getData( 'text/figuro-folder' );

		if ( attachmentIds ) {
			moveAttachments( JSON.parse( attachmentIds ), targetId );
			return;
		}

		if ( folderId && folderId !== targetId ) {
			var parent = ( '' === targetId || 'uncategorized' === targetId ) ? 0 : targetId;
			ajax( 'figuro_move_folder', { id: folderId, parent: parent } ).done( function ( res ) {
				if ( res.success ) {
					applyTreeData( res.data );
				} else {
					window.alert( res.data && res.data.message ? res.data.message : FiguroMedia.i18n.error );
				}
			} );
		}
	} );

	// --- Grid interactions ---------------------------------------------------

	$grid.on( 'click', '.figuro-item', function () {
		var id = $( this ).data( 'id' );
		if ( state.bulkMode ) {
			toggleItemSelected( id, $( this ) );
			return;
		}
		openAttachmentModal( id, $( this ) );
	} );

	$grid.on( 'dragstart', '.figuro-item', function ( e ) {
		if ( state.bulkMode ) {
			e.preventDefault();
			return;
		}
		var id = $( this ).data( 'id' ).toString();
		e.originalEvent.dataTransfer.setData( 'text/figuro-attachments', JSON.stringify( [ id ] ) );
	} );

	$bulkToggle.on( 'click', function () {
		setBulkMode( ! state.bulkMode );
	} );

	$bulkCancel.on( 'click', function () {
		setBulkMode( false );
	} );

	$bulkDelete.on( 'click', deleteSelectedAttachments );

	$pagination.on( 'click', 'button:not(:disabled)', function () {
		state.page = parseInt( $( this ).data( 'page' ), 10 );
		state.selected = {};
		updateBulkBar();
		loadGrid();
		$( 'html, body' ).animate( { scrollTop: $grid.offset().top - 100 }, 200 );
	} );

	var searchTimer;
	$search.on( 'input', function () {
		clearTimeout( searchTimer );
		var val = $( this ).val();
		searchTimer = setTimeout( function () {
			state.search = val;
			state.page = 1;
			state.selected = {};
			updateBulkBar();
			loadGrid();
		}, 350 );
	} );

	$addMediaBtn.on( 'click', function () {
		$fileInput.trigger( 'click' );
	} );

	$fileInput.on( 'change', function () {
		uploadFiles( this.files );
		this.value = ''; // allow re-selecting the same file(s) later
	} );

	// A running counter, not a boolean, because the browser fires dragenter/
	// dragleave for every element the pointer crosses while hovering — only
	// hitting zero really means the drag left the window.
	var dragDepth = 0;

	$( window ).on( 'dragenter', function ( e ) {
		if ( ! isFileDrag( e ) ) return;
		e.preventDefault();
		dragDepth++;
		$dropzone.addClass( 'is-active' );
	} );

	$( window ).on( 'dragover', function ( e ) {
		if ( ! isFileDrag( e ) ) return;
		e.preventDefault(); // required for 'drop' to fire at all
	} );

	$( window ).on( 'dragleave', function ( e ) {
		if ( ! isFileDrag( e ) ) return;
		dragDepth = Math.max( 0, dragDepth - 1 );
		if ( 0 === dragDepth ) {
			$dropzone.removeClass( 'is-active' );
		}
	} );

	$( window ).on( 'drop', function ( e ) {
		if ( ! isFileDrag( e ) ) return;
		e.preventDefault();
		dragDepth = 0;
		$dropzone.removeClass( 'is-active' );
		uploadFiles( e.originalEvent.dataTransfer.files );
	} );

	$( function () {
		renderPinned();
		initFilters();
		$heading.text( folderName( '' ) );
		loadBootstrap();
	} );
} )( jQuery );
