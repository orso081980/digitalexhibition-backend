jQuery(document).ready(function($) {
	'use strict';

	console.log('MCD Script Loaded. Init mcd_settings:', typeof mcd_settings !== 'undefined' ? mcd_settings : 'NOT FOUND');

	var i18n = typeof mcd_settings !== 'undefined' && typeof mcd_settings.i18n !== 'undefined' ? mcd_settings.i18n : {};
	var $modalOverlay = $('#mcd-post-modal-overlay');
	var $modal = $('#mcd-post-modal');
	var $modalTitle = $('#mcd-modal-title');
	var $modalBody = $modal.find('.mcd-modal-body');
	var $modalSaveButton = $('#mcd-modal-save-button-footer');
	var $modalSpinner = $modal.find('.mcd-modal-footer .spinner');
	var currentEditorId = 'mcd_modal_post_content';

	if ($modalOverlay.length === 0 || $modal.length === 0) {
		console.error('MCD: Modal HTML structure (#mcd-post-modal-overlay or #mcd-post-modal) not found in DOM.');
		return;
	}

	function openModal(title) {
		console.log('MCD: openModal called with title:', title);
		$modalTitle.text(title);
		$modalOverlay.show();
		$modal.show();
		$(document.body).addClass('modal-open');

		setTimeout(function() {
			var editor = typeof tinymce !== 'undefined' ? tinymce.get(currentEditorId) : null;
			if (editor) {
				console.log('MCD: TinyMCE editor found, attempting to show/focus.');
				editor.show();
				try {
					editor.focus();
				} catch (e) {}
			} else {
				console.log('MCD: TinyMCE editor not found after modal open timeout or tinymce undefined. Attempting re-init.');
				initializeEditorInstance($modalBody.find('textarea#' + currentEditorId).val() || '');
			}
		}, 250);
	}

	function closeModal() {
		console.log('MCD: closeModal called.');
		if (typeof tinymce !== 'undefined') {
			var editor = tinymce.get(currentEditorId);
			if (editor) {
				console.log('MCD: Saving and removing TinyMCE editor on close.');
				editor.save();
				editor.remove();
			}
		}
		$modalBody.html('');
		$modal.hide();
		$modalOverlay.hide();
		$(document.body).removeClass('modal-open');
	}

	function showSpinner(show) {
		if (show) {
			$modalSpinner.addClass('is-active');
			$modalSaveButton.prop('disabled', true);
		} else {
			$modalSpinner.removeClass('is-active');
			$modalSaveButton.prop('disabled', false);
		}
	}

	function resetForm() {
		console.log('MCD: Resetting form.');
		var $form = $modalBody.find('#mcd-modal-post-form');
		if ($form.length) {
			$form[0].reset();
			$form.find('#mcd_modal_post_id').val('0');
			if (typeof tinymce !== 'undefined' && tinymce.get(currentEditorId)) {
				tinymce.get(currentEditorId).setContent('');
			} else {
				$('#' + currentEditorId).val('');
			}
			$form.find('#mcd_modal_post-status-display').text(i18n.draft_status || 'Bozza');
			$form.find('#mcd_modal_post_status').val('draft');
			$form.find('#elementor-action-modal-placeholder').hide();
			$form.find('.mcd-image-preview-area').html('');
			$form.find('.mcd-pdf-preview-area').html('');
			$form.find('.mcd-gallery-ids-input, .mcd-pdf-id-input').val('');
			$form.find('.mcd-upload-pdf-button').show();

			if (typeof acf !== 'undefined' && typeof acf.reset === 'function') {
				acf.reset($form);
			} else if (typeof acf !== 'undefined' && typeof acf.do_action === 'function') {
				acf.do_action('reset', $form);
			}
			$modalBody.find('.acf-field').each(function() {
				var $field = $(this);
				var fieldKey = $field.data('key');
				var acfField = typeof acf !== 'undefined' ? acf.getField(fieldKey) : null;
				if (acfField && typeof acfField.val === 'function' && typeof acfField.render === 'function' && acfField.val() !== '') {
					acfField.val('').render();
				} else if (acfField && typeof acfField.clear === 'function') {
					acfField.clear();
				} else {
					$field
						.find('input[type="text"], input[type="hidden"], textarea, select')
						.not('select[name="post_status"]')
						.val('');
					$field.find('input[type="checkbox"], input[type="radio"]').prop('checked', false);
				}
			});
		}
	}

	function populateForm(postData) {
		console.log('MCD: Populating form with data:', postData);
		resetForm();
		var $form = $modalBody.find('#mcd-modal-post-form');
		$form.find('#mcd_modal_post_id').val(postData.id);
		$form.find('#mcd_modal_post_title').val(postData.title);

		if (typeof tinymce !== 'undefined' && tinymce.get(currentEditorId)) {
			tinymce.get(currentEditorId).setContent(postData.content || '');
		} else {
			$('#' + currentEditorId).val(postData.content || '');
		}

		$form.find('#mcd_modal_post_status').val(postData.status || 'draft');
		var status_obj = wp.data
			? wp.data
					.select('core')
					.getPostType('post')
					.statuses.find(s => s.slug === postData.status)
			: null;
		var status_label = status_obj ? status_obj.name : postData.status ? postData.status.charAt(0).toUpperCase() + postData.status.slice(1) : 'Draft';
		$form.find('#mcd_modal_post-status-display').text(status_label);

		if (postData.elementor_edit_url) {
			$('#mcd_modal_elementor_link')
				.attr('href', postData.elementor_edit_url)
				.parent()
				.show();
		} else {
			$('#mcd_modal_elementor_link')
				.parent()
				.hide();
		}

		if (postData.acf_fields && typeof acf !== 'undefined') {
			$.each(postData.acf_fields, function(key, value) {
				var $field = $form.find('.acf-field[data-key="' + key + '"]');
				var acfField = acf.getField(key);
				if (acfField && typeof acfField.val === 'function') {
					acfField.val(value);
					if (typeof acfField.render === 'function') acfField.render();
				} else if ($field.length) {
					var $input = $field.find('input[name="acf[' + key + ']"], textarea[name="acf[' + key + ']"], select[name="acf[' + key + ']"]');
					if ($input.is(':checkbox') || $input.is(':radio')) {
						$input.filter('[value="' + value + '"]').prop('checked', true);
					} else {
						$input.val(value);
					}
				}

				if (key === 'field_mcd_galleria_immagini' && Array.isArray(value)) {
					var $previewArea = $field.find('.mcd-image-preview-area');
					var $idsInput = $field.find('.mcd-gallery-ids-input');
					$previewArea.html('');
					var ids_string = value.join(',');
					$idsInput.val(ids_string);
					value.forEach(function(id) {
						wp.media
							.attachment(id)
							.fetch()
							.done(function() {
								var thumbUrl = this.get('sizes') && this.get('sizes').thumbnail ? this.get('sizes').thumbnail.url : this.get('icon');
								$previewArea.append('<div class="mcd-gallery-item" data-id="' + id + '">' + '<img src="' + thumbUrl + '" alt="">' + '<button type="button" class="mcd-remove-gallery-item button button-link-delete" aria-label="' + (i18n.removeImage || 'Rimuovi') + '"></button>' + '</div>');
							});
					});
				} else if (key === 'field_mcd_upload_pdf' && value) {
					var $previewAreaPdf = $field.find('.mcd-pdf-preview-area');
					var $idInputPdf = $field.find('.mcd-pdf-id-input');
					var $uploadButtonPdf = $field.find('.mcd-upload-pdf-button');
					$previewAreaPdf.html('');
					$idInputPdf.val(value);
					wp.media
						.attachment(value)
						.fetch()
						.done(function() {
							$previewAreaPdf.html('<div class="mcd-pdf-item">' + '<a href="' + this.get('url') + '" target="_blank">' + this.get('filename') + '</a> ' + '<button type="button" class="mcd-remove-pdf-item button button-link-delete" aria-label="' + (i18n.removePDF || 'Rimuovi') + '"></button>' + '</div>');
							$uploadButtonPdf.hide();
						});
				}
			});
		}
	}

	function initializeEditorInstance(content) {
		var $editorTextarea = $modalBody.find('textarea#' + currentEditorId);
		if (!$editorTextarea.length) return;

		if (typeof tinymce !== 'undefined') {
			var editor = tinymce.get(currentEditorId);
			if (editor) editor.destroy();

			var tinySettings = $.extend(true, {}, wp.editor.getDefaultSettings().tinymce);
			tinySettings.selector = '#' + currentEditorId;
			tinySettings.id = currentEditorId;
			tinySettings.init_instance_callback = function(ed) {
				if (content !== null && typeof content !== 'undefined') {
					ed.setContent(content);
				}
				setTimeout(function() {
					ed.focus();
				}, 150);
			};
			tinymce.init(tinySettings);
		}
		if (typeof quicktags !== 'undefined') {
			var qtSettings = $.extend(true, {}, wp.editor.getDefaultSettings().quicktags);
			qtSettings.id = currentEditorId;
			quicktags(qtSettings);
			setTimeout(function() {
				if (typeof QTags !== 'undefined' && QTags._buttonsInit) QTags._buttonsInit();
			}, 100);
		}
	}

	$('#mcd-add-new-post-button').on('click', function() {
		console.log('MCD: Add New Post button clicked.');
		$modalSaveButton.text(i18n.save_post || 'Salva Post');
		resetForm();
		initializeEditorInstance('');
		openModal(i18n.add_new_post || 'Aggiungi Nuovo Post');
		if (typeof acf !== 'undefined') acf.do_action('append', $modalBody.find('#mcd-modal-post-form'));
	});

	$('body').on('click', '.mcd-edit-post-button', function() {
		var postId = $(this).data('post-id');
		console.log('MCD: Edit Post button clicked. Post ID:', postId);
		$modalSaveButton.text(i18n.update_post || 'Aggiorna Post');
		$modalBody.find('#mcd-modal-post-form').hide();
		$modalBody.prepend('<p class="mcd-loading-message">' + (i18n.loading_data || 'Caricamento dati...') + '</p>');
		openModal(i18n.edit_post || 'Modifica Post');
		showSpinner(true);

		$.ajax({
			url: mcd_settings.ajax_url,
			type: 'POST',
			data: {
				action: 'mcd_get_post_data',
				nonce: mcd_settings.get_post_data_nonce,
				post_id: postId,
			},
			dataType: 'json',
			success: function(response) {
				console.log('MCD: AJAX mcd_get_post_data response:', response);
				$modalBody.find('.mcd-loading-message').remove();
				$modalBody.find('#mcd-modal-post-form').show();
				if (response.success) {
					populateForm(response.data.post_data);
					initializeEditorInstance(response.data.post_data.content || '');
					if (typeof acf !== 'undefined') acf.do_action('append', $modalBody.find('#mcd-modal-post-form'));
				} else {
					closeModal();
					alert(response.data.message || i18n.error_unexpected);
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				console.error('MCD: AJAX mcd_get_post_data error:', textStatus, errorThrown, jqXHR);
				$modalBody.find('.mcd-loading-message').remove();
				$modalBody.find('#mcd-modal-post-form').show();
				alert(i18n.error_unexpected);
			},
			complete: function() {
				showSpinner(false);
			},
		});
	});

	$('#mcd-modal-close-button').on('click', function() {
		console.log('MCD: Modal Close button clicked.');
		closeModal();
	});

	$modalSaveButton.on('click', function() {
		console.log('MCD: Modal Save (footer) button clicked.');
		var $form = $modalBody.find('#mcd-modal-post-form');
		if ($form.length === 0) {
			console.error('MCD: Modal form not found on save.');
			return;
		}

		var $titleField = $form.find('#mcd_modal_post_title');
		if ($titleField.val().trim() === '') {
			alert(i18n.title_required || 'Il titolo è obbligatorio.');
			$titleField.focus();
			return;
		}

		if (typeof tinymce !== 'undefined' && tinymce.get(currentEditorId)) {
			console.log('MCD: Saving content from TinyMCE editor for save action', currentEditorId);
			tinymce.get(currentEditorId).save();
		}

		var formData = $form.serializeArray();
		formData.push({ name: 'security', value: mcd_settings.save_post_nonce });

		console.log('MCD: Form data for save (AJAX):', formData);
		showSpinner(true);
		$modalBody.find('#mcd-modal-form-feedback').remove();

		$.ajax({
			url: mcd_settings.ajax_url,
			type: 'POST',
			data: $.param(formData),
			dataType: 'json',
			success: function(response) {
				console.log('MCD: AJAX mcd_ajax_save_post response:', response);
				var $messageArea = $('#mcd-ajax-general-message-area');
				$messageArea.html('');
				if (response.success) {
					closeModal();

					$('#mcd-post-list-table-wrapper').html('<p class="mcd-loading-message">' + (i18n.loading_list || 'Ricaricamento elenco...') + '</p>');
					$('#mcd-post-list-table-wrapper').load(window.location.href + ' #mcd-post-list-table-wrapper > *', function(responseText, textStatus, jqXHR) {
						if (textStatus === 'error') {
							console.error('MCD: Error reloading post list after save:', jqXHR.status, jqXHR.statusText);
							$('#mcd-post-list-table-wrapper').html('<p class="notice notice-error">Errore nel ricaricare l\'elenco.</p>');
						} else {
							console.log('MCD: Post list reloaded after save.');
							if (typeof acf !== 'undefined' && typeof acf.do_action === 'function') {
								acf.do_action('reload', $('body'));
							}
						}
					});

					$messageArea.html('<div class="notice notice-success is-dismissible"><p>' + response.data.message + '</p></div>');
					setTimeout(function() {
						$messageArea.find('.notice').fadeOut(500, function() {
							$(this).remove();
						});
					}, 4000);
				} else {
					$form.prepend('<div id="mcd-modal-form-feedback" class="notice notice-error is-dismissible"><p>' + (response.data.message || i18n.error_unexpected) + '</p></div>');
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				console.error('MCD: AJAX mcd_ajax_save_post error:', textStatus, errorThrown, jqXHR);
				$form.prepend('<div id="mcd-modal-form-feedback" class="notice notice-error is-dismissible"><p>' + i18n.error_unexpected + '</p></div>');
			},
			complete: function() {
				showSpinner(false);
			},
		});
	});

	var galleryFrameModal, pdfFrameModal;

	$('body').on('click', '.mcd-upload-gallery-button', function(e) {
		e.preventDefault();
		if (!$modal.is(':visible') && !$(this).closest($modalBody).length) return;

		var $button = $(this);
		var $fieldContainer = $button.closest('.mcd-custom-gallery-field');
		var $galleryIdsInput = $fieldContainer.find('.mcd-gallery-ids-input');
		var $galleryPreviewArea = $fieldContainer.find('.mcd-image-preview-area');
		var currentTargets = { input: $galleryIdsInput, preview: $galleryPreviewArea };

		if (galleryFrameModal && galleryFrameModal.el) {
			galleryFrameModal.currentTargets = currentTargets;
			galleryFrameModal
				.state()
				.get('selection')
				.reset();
			galleryFrameModal.open();
			return;
		}
		galleryFrameModal = wp.media({
			title: i18n.selectGalleryImages || 'Seleziona Immagini',
			button: { text: i18n.useTheseImages || 'Usa Immagini' },
			library: { type: 'image' },
			multiple: 'add',
		});
		galleryFrameModal.currentTargets = currentTargets;
		galleryFrameModal.on('select open', function() {
			var localTargets = this.currentTargets;
			if (!localTargets) return;
			var selection = this.state().get('selection');
			var currentIds = localTargets.input.val()
				? localTargets.input
						.val()
						.split(',')
						.map(Number)
						.filter(Boolean)
				: [];
			selection.each(function(attachment) {
				attachment = attachment.toJSON();
				if (currentIds.indexOf(attachment.id) === -1) {
					currentIds.push(attachment.id);
					var imageUrl = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.icon;
					localTargets.preview.append('<div class="mcd-gallery-item" data-id="' + attachment.id + '">' + '<img src="' + imageUrl + '" alt="' + attachment.alt + '">' + '<button type="button" class="mcd-remove-gallery-item button button-link-delete" aria-label="' + (i18n.removeImage || 'Rimuovi') + '"></button>' + '</div>');
				}
			});
			localTargets.input.val(currentIds.join(',')).trigger('change');
		});
		galleryFrameModal.open();
	});

	$('body').on('click', '.mcd-remove-gallery-item', function() {
		if (!$modal.is(':visible') && !$(this).closest($modalBody).length) return;
		var $item = $(this).closest('.mcd-gallery-item');
		var itemId = $item.data('id');
		var $fieldContainer = $item.closest('.mcd-custom-gallery-field');
		var $input = $fieldContainer.find('.mcd-gallery-ids-input');
		var currentIds = $input.val()
			? $input
					.val()
					.split(',')
					.map(Number)
					.filter(Boolean)
			: [];
		var newIds = currentIds.filter(function(id) {
			return id !== itemId;
		});
		$input.val(newIds.join(',')).trigger('change');
		$item.remove();
	});

	$('body').on('click', '.mcd-upload-pdf-button', function(e) {
		e.preventDefault();
		if (!$modal.is(':visible') && !$(this).closest($modalBody).length) return;

		var $button = $(this);
		var $fieldContainer = $button.closest('.mcd-custom-pdf-field');
		var $pdfIdInput = $fieldContainer.find('.mcd-pdf-id-input');
		var $pdfPreviewArea = $fieldContainer.find('.mcd-pdf-preview-area');
		var currentTargets = { button: $button, input: $pdfIdInput, preview: $pdfPreviewArea };

		if (pdfFrameModal && pdfFrameModal.el) {
			pdfFrameModal.currentTargets = currentTargets;
			pdfFrameModal
				.state()
				.get('selection')
				.reset();
			pdfFrameModal.open();
			return;
		}
		pdfFrameModal = wp.media({
			title: i18n.selectPDF || 'Seleziona PDF',
			button: { text: i18n.useThisPDF || 'Usa PDF' },
			library: { type: 'application/pdf' },
			multiple: false,
		});
		pdfFrameModal.currentTargets = currentTargets;
		pdfFrameModal.on('select', function() {
			var localTargets = this.currentTargets;
			if (!localTargets) return;
			var attachment = this.state()
				.get('selection')
				.first()
				.toJSON();
			localTargets.input.val(attachment.id).trigger('change');
			localTargets.preview.html('<div class="mcd-pdf-item">' + '<a href="' + attachment.url + '" target="_blank">' + attachment.filename + '</a> ' + '<button type="button" class="mcd-remove-pdf-item button button-link-delete" aria-label="' + (i18n.removePDF || 'Rimuovi') + '"></button>' + '</div>');
			localTargets.button.hide();
		});
		pdfFrameModal.open();
	});

	$('body').on('click', '.mcd-remove-pdf-item', function() {
		if (!$modal.is(':visible') && !$(this).closest($modalBody).length) return;
		var $item = $(this).closest('.mcd-pdf-item');
		var $fieldContainer = $item.closest('.mcd-custom-pdf-field');
		var $idInput = $fieldContainer.find('.mcd-pdf-id-input');
		var $uploadButton = $fieldContainer.find('.mcd-upload-pdf-button');
		$idInput.val('').trigger('change');
		$item.remove();
		$uploadButton.show();
	});

	$('body').on('click', '.mcd-delete-post-button', function() {
		var postId = $(this).data('post-id');
		var nonce = $(this).data('nonce');

		if (!confirm(i18n.confirm_delete || 'Sei sicuro di voler eliminare questo post?')) {
			return;
		}

		$.ajax({
			url: mcd_settings.ajax_url,
			type: 'POST',
			data: {
				action: 'mcd_ajax_delete_post',
				post_id: postId,
				_ajax_nonce: nonce,
			},
			dataType: 'json',
			success: function(response) {
				var $messageArea = $('#mcd-ajax-general-message-area');
				$messageArea.html('');
				var noticeType = response.success ? 'success' : 'error';
				var message = response.data.message || (response.success ? i18n.post_deleted_successfully : i18n.error_deleting_post);

				if (response.success) {
					$('tr[data-post-id="' + postId + '"]').fadeOut(500, function() {
						$(this).remove();
					});
				}
				$messageArea.html('<div class="notice notice-' + noticeType + ' is-dismissible"><p>' + message + '</p></div>');
				setTimeout(function() {
					$messageArea.find('.notice').fadeOut(500, function() {
						$(this).remove();
					});
				}, 4000);
			},
			error: function() {
				var $messageArea = $('#mcd-ajax-general-message-area');
				$messageArea.html('');
				$messageArea.html('<div class="notice notice-error is-dismissible"><p>' + i18n.error_unexpected + '</p></div>');
			},
		});
	});

	$('body').on('click', '.mcd_modal_post-status-select .save-post-status, .mcd_modal_post-status-select .cancel-post-status', function(e) {
		e.preventDefault();
		var $parent = $(this).closest('.misc-pub-post-status');
		if ($(this).hasClass('save-post-status')) {
			var newStatusDisplay = $parent.find('#mcd_modal_post_status option:selected').text();
			$parent.find('#mcd_modal_post-status-display').text(newStatusDisplay);
		}
		$parent.find('.mcd_modal_post-status-select').slideUp();
		$parent.find('.edit-post-status').show();
	});

	$('body').on('click', '#submitdiv_modal .edit-post-status', function(e) {
		e.preventDefault();
		$(this).hide();
		$(this)
			.siblings('.mcd_modal_post-status-select')
			.slideDown();
	});
});
