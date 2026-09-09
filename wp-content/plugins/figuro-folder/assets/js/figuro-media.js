( function ( $ ) {
	'use strict';

	var state = {
		tree: [],
		currentFolder: '', // '' = All Files, 'uncategorized', or a numeric term id (as string)
		page: 1,
		search: '',
		selected: {},
		expanded: {} // term id (string) => true, when its children are visible
	};

	var $treePinned    = $( '#figuro-tree-pinned' );
	var $tree          = $( '#figuro-tree' );
	var $grid          = $( '#figuro-grid' );
	var $pagination    = $( '#figuro-pagination' );
	var $heading       = $( '#figuro-current-folder-name' );
	var $folderCount   = $( '#figuro-folder-count' );
	var $selectionInfo = $( '#figuro-selection-info' );
	var $moveTarget    = $( '#figuro-move-target' );
	var $search        = $( '#figuro-search' );

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

	function renderMoveTarget() {
		var flat = flatten( state.tree );
		var html = '<option value="">' + escapeHtml( FiguroMedia.i18n.moveSelected ) + '</option>';
		html += '<option value="uncategorized">' + escapeHtml( FiguroMedia.i18n.uncategorized ) + '</option>';
		flat.forEach( function ( node ) {
			html += '<option value="' + node.id + '">' + new Array( node.depth + 1 ).join( ' ' ) + escapeHtml( node.name ) + '</option>';
		} );
		$moveTarget.html( html );
	}

	function pinnedNodeHtml( id, name, icon ) {
		return (
			'<li>' +
			'<div class="figuro-node figuro-node-pinned" data-id="' + id + '">' +
			'<span class="figuro-node-toggle figuro-node-toggle-spacer"></span>' +
			'<span class="dashicons ' + icon + ' figuro-node-icon"></span>' +
			'<span class="figuro-node-name">' + escapeHtml( name ) + '</span>' +
			'</div>' +
			'</li>'
		);
	}

	function renderPinned() {
		var html = '';
		html += pinnedNodeHtml( '', FiguroMedia.i18n.allFiles, 'dashicons-admin-media' );
		html += pinnedNodeHtml( 'uncategorized', FiguroMedia.i18n.uncategorized, 'dashicons-media-default' );
		$treePinned.html( html );
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

	function loadTree() {
		return ajax( 'figuro_get_tree' ).done( function ( res ) {
			if ( res.success ) {
				state.tree = res.data.tree;
				renderTree();
				renderMoveTarget();
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
		$heading.text( folderName( id ) );
		expandAncestorsOf( id );
		renderTree();
		loadGrid();
	}

	function loadGrid() {
		$grid.html( '<div class="figuro-loading">' + escapeHtml( FiguroMedia.i18n.loading ) + '</div>' );
		$pagination.empty();

		ajax( 'figuro_get_attachments', {
			folder: state.currentFolder,
			paged: state.page,
			search: state.search
		} ).done( function ( res ) {
			if ( ! res.success ) {
				$grid.html( '<div class="figuro-empty">' + escapeHtml( FiguroMedia.i18n.error ) + '</div>' );
				return;
			}

			$folderCount.text(
				res.data.total
					? res.data.total + ' ' + ( 1 === res.data.total ? FiguroMedia.i18n.file : FiguroMedia.i18n.files )
					: ''
			);

			if ( ! res.data.items.length ) {
				$grid.html( '<div class="figuro-empty"><span class="dashicons dashicons-portfolio"></span><p>' + escapeHtml( FiguroMedia.i18n.noItems ) + '</p></div>' );
				return;
			}

			var html = '';
			res.data.items.forEach( function ( item ) {
				html +=
					'<div class="figuro-item" data-id="' + item.id + '" draggable="true">' +
					'<label class="figuro-item-check-wrap"><input type="checkbox" class="figuro-item-check" /></label>' +
					'<div class="figuro-item-thumb"><img src="' + escapeHtml( item.thumb ) + '" alt="" loading="lazy" /></div>' +
					'<div class="figuro-item-title" title="' + escapeHtml( item.title ) + '">' + escapeHtml( item.title ) + '</div>' +
					'</div>';
			} );
			$grid.html( html );
			renderPagination( res.data.page, res.data.totalPages );
			updateSelectionInfo();
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

	function updateSelectionInfo() {
		var count = Object.keys( state.selected ).length;
		$selectionInfo.text( count ? count + ' ' + FiguroMedia.i18n.selected : '' );
		$selectionInfo.toggleClass( 'has-selection', !! count );
	}

	function moveAttachments( ids, folderId ) {
		ajax( 'figuro_move_attachments', { ids: ids, folder: folderId } ).done( function ( res ) {
			if ( res.success ) {
				loadTree();
				loadGrid();
			} else {
				window.alert( res.data && res.data.message ? res.data.message : FiguroMedia.i18n.error );
			}
		} );
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
				loadTree();
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
				loadTree();
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
				loadTree().done( function () {
					if ( state.currentFolder.toString() === id.toString() ) {
						$heading.text( name );
					}
				} );
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
				loadTree();
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
					loadTree();
				} else {
					window.alert( res.data && res.data.message ? res.data.message : FiguroMedia.i18n.error );
				}
			} );
		}
	} );

	// --- Grid interactions ---------------------------------------------------

	$grid.on( 'click', '.figuro-item', function ( e ) {
		if ( ! $( e.target ).is( '.figuro-item-check' ) ) {
			var $check = $( this ).find( '.figuro-item-check' );
			$check.prop( 'checked', ! $check.prop( 'checked' ) );
		}

		var id = $( this ).data( 'id' );
		var checked = $( this ).find( '.figuro-item-check' ).prop( 'checked' );
		$( this ).toggleClass( 'is-selected', checked );

		if ( checked ) {
			state.selected[ id ] = true;
		} else {
			delete state.selected[ id ];
		}
		updateSelectionInfo();
	} );

	$grid.on( 'dragstart', '.figuro-item', function ( e ) {
		var id = $( this ).data( 'id' ).toString();
		var ids = state.selected[ id ] ? Object.keys( state.selected ) : [ id ];
		e.originalEvent.dataTransfer.setData( 'text/figuro-attachments', JSON.stringify( ids ) );
	} );

	$pagination.on( 'click', 'button:not(:disabled)', function () {
		state.page = parseInt( $( this ).data( 'page' ), 10 );
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
			loadGrid();
		}, 350 );
	} );

	$moveTarget.on( 'change', function () {
		var folderId = $( this ).val();
		var ids = Object.keys( state.selected );
		if ( ! folderId || ! ids.length ) {
			$( this ).val( '' );
			return;
		}
		moveAttachments( ids, folderId );
		$( this ).val( '' );
	} );

	$( function () {
		renderPinned();
		loadTree();
		selectFolder( '' );
	} );
} )( jQuery );
