/**
 * Folder sidebar for the media popup — the "Select File" / "Add Media" modal
 * that ACF file fields, the editor and Elementor open. Same idea as FileBird
 * Pro's: a folder tree in the popup's left column (All Files, Uncategorized,
 * your folders with counts, New Folder / Rename / Delete). Picking a folder
 * narrows the library to it; a file uploaded from the popup lands in the
 * selected folder.
 *
 * Folders themselves are managed by the same admin-ajax endpoints as the
 * Figuro Folders page (class-figuro-ajax.php); the library is narrowed via a
 * `figuro_folder` query prop applied in Figuro_Media_Library::filter_modal_query.
 */
( function ( $ ) {
	'use strict';

	var cfg = window.FiguroPicker;

	if ( ! cfg || ! window.wp || ! wp.media || ! wp.media.view || ! wp.media.view.MediaFrame || ! wp.media.view.MediaFrame.Select ) {
		return;
	}

	var i18n = cfg.i18n;

	// Shared by every popup on the page: one folder selection, one tree.
	var shared = {
		tree: cfg.tree || [],
		totals: cfg.totals || { all: 0, uncategorized: 0 },
		folder: '' // '' = All Files, 'uncategorized', or a folder id (string)
	};
	var panels = [];

	function request( action, data ) {
		var d = $.Deferred();

		$.post( cfg.ajaxUrl, $.extend( { action: action, nonce: cfg.nonce }, data ) )
			.done( function ( res ) {
				if ( res && res.success ) {
					d.resolve( res.data );
				} else {
					d.reject( ( res && res.data && res.data.message ) || i18n.error );
				}
			} )
			.fail( function () {
				d.reject( i18n.error );
			} );

		return d.promise();
	}

	function showError( message ) {
		window.alert( message );
	}

	function acceptTree( data ) {
		if ( data && data.tree ) {
			shared.tree = data.tree;
		}
		if ( data && data.counts ) {
			shared.totals = data.counts;
		}
		renderAll();
	}

	function findFolder( nodes, id ) {
		var i, found;

		for ( i = 0; i < nodes.length; i++ ) {
			if ( String( nodes[ i ].id ) === String( id ) ) {
				return nodes[ i ];
			}
			found = findFolder( nodes[ i ].children || [], id );
			if ( found ) {
				return found;
			}
		}

		return null;
	}

	/** Ids from the top level down to (and including) the given folder. */
	function pathTo( nodes, id ) {
		var i, sub;

		for ( i = 0; i < nodes.length; i++ ) {
			if ( String( nodes[ i ].id ) === String( id ) ) {
				return [ nodes[ i ].id ];
			}
			sub = pathTo( nodes[ i ].children || [], id );
			if ( sub ) {
				return [ nodes[ i ].id ].concat( sub );
			}
		}

		return null;
	}

	function isRealFolder() {
		return shared.folder !== '' && shared.folder !== 'uncategorized';
	}

	function renderAll() {
		panels = panels.filter( function ( panel ) {
			return $.contains( document.documentElement, panel.$el[ 0 ] );
		} );
		panels.forEach( function ( panel ) {
			panel.render();
		} );
	}

	/**
	 * Narrow the popup's active library to the selected folder. Setting the
	 * prop re-queries, exactly like core's type/date filters do.
	 */
	function applyFolder( frame ) {
		var state = frame.state && frame.state();
		var library = state && state.get && state.get( 'library' );

		if ( ! library || ! library.props ) {
			return;
		}
		if ( String( library.props.get( 'figuro_folder' ) || '' ) === shared.folder ) {
			return;
		}

		library.props.set( { figuro_folder: shared.folder } );
	}

	function select( value ) {
		var path = value !== '' && value !== 'uncategorized' ? pathTo( shared.tree, value ) : null;

		shared.folder = value;
		panels.forEach( function ( panel ) {
			( path || [] ).forEach( function ( id ) {
				panel.expanded[ id ] = true;
			} );
			applyFolder( panel.frame );
		} );
		renderAll();
	}

	function Panel( frame ) {
		var self = this;

		this.frame = frame;
		this.search = '';
		this.expanded = {};

		this.$el = $(
			'<div class="figuro-picker">' +
				'<div class="figuro-picker-head">' +
					'<span class="figuro-picker-title"></span>' +
					'<button type="button" class="button button-primary figuro-picker-new"></button>' +
				'</div>' +
				'<div class="figuro-picker-actions">' +
					'<button type="button" class="button figuro-picker-rename"></button>' +
					'<button type="button" class="button figuro-picker-delete"></button>' +
				'</div>' +
				'<ul class="figuro-picker-list figuro-picker-pinned"></ul>' +
				'<input type="search" class="figuro-picker-search" />' +
				'<div class="figuro-picker-scroll"><ul class="figuro-picker-list figuro-picker-tree"></ul></div>' +
			'</div>'
		);

		this.$pinned = this.$el.find( '.figuro-picker-pinned' );
		this.$tree = this.$el.find( '.figuro-picker-tree' );
		this.$rename = this.$el.find( '.figuro-picker-rename' );
		this.$delete = this.$el.find( '.figuro-picker-delete' );

		this.$el.find( '.figuro-picker-title' ).text( i18n.title );
		this.$el.find( '.figuro-picker-new' ).text( i18n.newFolder );
		this.$rename.text( i18n.rename );
		this.$delete.text( i18n.delete );
		this.$el.find( '.figuro-picker-search' ).attr( 'placeholder', i18n.search );

		this.$el.on( 'click', '.figuro-picker-new', function () {
			var name = window.prompt( i18n.newFolderName );
			if ( ! name || ! name.trim() ) {
				return;
			}
			request( 'figuro_create_folder', { name: name, parent: isRealFolder() ? shared.folder : 0 } )
				.done( function ( data ) {
					acceptTree( data );
					select( String( data.id ) );
				} )
				.fail( showError );
		} );

		this.$rename.on( 'click', function () {
			var node = isRealFolder() ? findFolder( shared.tree, shared.folder ) : null;
			var name = node && window.prompt( i18n.renamePrompt, node.name );
			if ( ! name || ! name.trim() ) {
				return;
			}
			request( 'figuro_rename_folder', { id: node.id, name: name } ).done( acceptTree ).fail( showError );
		} );

		this.$delete.on( 'click', function () {
			var node = isRealFolder() ? findFolder( shared.tree, shared.folder ) : null;
			if ( ! node || ! window.confirm( i18n.deleteConfirm ) ) {
				return;
			}
			request( 'figuro_delete_folder', { id: node.id } )
				.done( function ( data ) {
					acceptTree( data );
					select( '' );
				} )
				.fail( showError );
		} );

		this.$el.on( 'click', '.figuro-picker-row', function ( event ) {
			if ( $( event.target ).closest( '.figuro-picker-toggle' ).length ) {
				return;
			}
			select( String( $( this ).attr( 'data-value' ) ) );
		} );

		this.$el.on( 'click', '.figuro-picker-toggle', function ( event ) {
			var id = $( this ).closest( '.figuro-picker-row' ).attr( 'data-value' );
			event.stopPropagation();
			self.expanded[ id ] = ! self.expanded[ id ];
			self.render();
		} );

		this.$el.on( 'input', '.figuro-picker-search', function () {
			self.search = $( this ).val().trim().toLowerCase();
			self.render();
		} );
	}

	Panel.prototype.row = function ( value, label, count, extraClass ) {
		var $row = $( '<div class="figuro-picker-row" role="button" tabindex="0"></div>' ).attr( 'data-value', value );

		if ( shared.folder === String( value ) ) {
			$row.addClass( 'is-selected' );
		}
		if ( extraClass ) {
			$row.addClass( extraClass );
		}

		return $row.append(
			$( '<span class="figuro-picker-toggle" aria-hidden="true"></span>' ),
			$( '<span class="dashicons figuro-picker-icon"></span>' ),
			$( '<span class="figuro-picker-name"></span>' ).text( label ),
			$( '<span class="figuro-picker-count"></span>' ).text( count )
		);
	};

	Panel.prototype.branch = function ( nodes, depth ) {
		var self = this;
		var $ul = $( '<ul class="figuro-picker-list"></ul>' );

		nodes.forEach( function ( node ) {
			var children = node.children || [];
			var $childList = children.length ? self.branch( children, depth + 1 ) : null;
			var matches = ! self.search || node.name.toLowerCase().indexOf( self.search ) !== -1;

			// While searching, keep a folder if it or anything inside it matches.
			if ( self.search && ! matches && ( ! $childList || ! $childList.children().length ) ) {
				return;
			}

			var $li = $( '<li></li>' );
			var $row = self.row( node.id, node.name, node.count, 'is-folder' );
			// Subfolders stay hidden until opened (or while searching), so a
			// library with hundreds of folders doesn't open as one long list.
			var open = !! self.search || !! self.expanded[ node.id ];

			$row.css( 'padding-left', 8 + depth * 14 ).attr( 'title', node.name );
			if ( children.length ) {
				$row.addClass( 'has-children' ).find( '.figuro-picker-toggle' ).attr( 'title', i18n.toggle );
				if ( ! open ) {
					$row.addClass( 'is-collapsed' );
				}
			}

			$li.append( $row );
			if ( $childList && open ) {
				$li.append( $childList );
			}
			$ul.append( $li );
		} );

		return $ul;
	};

	Panel.prototype.render = function () {
		this.$pinned.empty().append(
			$( '<li></li>' ).append( this.row( '', i18n.allFiles, shared.totals.all, 'is-all' ) ),
			$( '<li></li>' ).append( this.row( 'uncategorized', i18n.uncategorized, shared.totals.uncategorized, 'is-uncategorized' ) )
		);

		this.$tree.empty().append( this.branch( shared.tree, 0 ).children() );

		this.$rename.prop( 'disabled', ! isRealFolder() );
		this.$delete.prop( 'disabled', ! isRealFolder() );
	};

	/**
	 * Adds the sidebar to a popup's left column. Frames with a single state
	 * (like ACF's file picker) hide that column via `.hide-menu`; the CSS
	 * shows it again whenever the sidebar is present. Idempotent: called on every
	 * open and menu re-render, and re-attaches the panel if core rebuilt the
	 * menu region and dropped it.
	 */
	function mount( frame ) {
		var $menu = frame.$el.find( '.media-frame-menu' ).first();

		if ( ! $menu.length ) {
			return;
		}

		if ( ! frame.figuroPanel ) {
			frame.figuroPanel = new Panel( frame );
			panels.push( frame.figuroPanel );
		}

		frame.$el.addClass( 'figuro-has-sidebar' );

		if ( ! $.contains( $menu[ 0 ], frame.figuroPanel.$el[ 0 ] ) ) {
			$menu.append( frame.figuroPanel.$el );
		}

		( isRealFolder() ? pathTo( shared.tree, shared.folder ) || [] : [] ).forEach( function ( id ) {
			frame.figuroPanel.expanded[ id ] = true;
		} );

		frame.figuroPanel.render();
		applyFolder( frame );
	}

	/**
	 * Load grid thumbnails lazily. The popup lists up to 80 files at once and
	 * fetches every thumbnail immediately; from R2's public (r2.dev) address,
	 * which only speaks HTTP/1.1 (about 6 parallel connections), that queues
	 * up and a big folder takes seconds to fill in. With `loading="lazy"` the
	 * browser only fetches the thumbnails actually on screen. Done on the
	 * attachment template's source, before core compiles it on first use.
	 */
	$( function () {
		var tpl = document.getElementById( 'tmpl-attachment' );

		if ( tpl && tpl.innerHTML.indexOf( 'loading="lazy"' ) === -1 ) {
			tpl.innerHTML = tpl.innerHTML.replace( /<img /g, '<img loading="lazy" ' );
		}
	} );

	/**
	 * Smaller pages. Core asks for 80 files at a time; on big folders that is
	 * 80 thumbnails fetched at once (see above) and a slow first paint. 30
	 * fills the popup, and scrolling loads the next 30 as usual.
	 */
	if ( wp.media.model && wp.media.model.Query && wp.media.model.Query.defaultArgs ) {
		wp.media.model.Query.defaultArgs.posts_per_page = 30;
	}

	var initialize = wp.media.view.MediaFrame.Select.prototype.initialize;

	wp.media.view.MediaFrame.Select.prototype.initialize = function () {
		var frame = this;

		initialize.apply( this, arguments );

		frame.on( 'open', function () {
			mount( frame );
			// Counts change as files are uploaded/moved elsewhere; refresh quietly.
			request( 'figuro_get_tree' ).done( acceptTree );
		} );
		// Core re-renders regions per mode (`menu:render:default`, …), so match by prefix.
		frame.on( 'all', function ( eventName ) {
			if ( frame.figuroPanel && /^(menu|content):render|^state:activate$/.test( eventName ) ) {
				mount( frame );
			}
		} );
	};

	// Send the selected folder along with uploads made from the popup.
	if ( wp.Uploader ) {
		var uploaderInit = wp.Uploader.prototype.init;

		wp.Uploader.prototype.init = function () {
			if ( uploaderInit ) {
				uploaderInit.apply( this, arguments );
			}

			if ( this.uploader ) {
				this.uploader.bind( 'BeforeUpload', function ( up ) {
					up.settings.multipart_params = up.settings.multipart_params || {};

					if ( isRealFolder() ) {
						up.settings.multipart_params.figuro_folder = shared.folder;
					} else {
						delete up.settings.multipart_params.figuro_folder;
					}
				} );
			}
		};
	}
} )( jQuery );
