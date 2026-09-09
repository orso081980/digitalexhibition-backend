jQuery(document).ready(function($) {
	const $postListUl = $('#cd-posts-ul');
	const $settingsPanelDefault = $('#cd-settings-panel-default');
	const $settingsPanelContent = $('#cd-settings-panel-content');
	const $selectedPostTitle = $('#cd-selected-post-title');
	const $selectedPostIdInput = $('#cd-selected-post-id');
	const $showPageCheckboxRight = $('#cd-show-page-checkbox'); // Right panel checkbox
	const $highlightCheckboxRight = $('#cd-highlight-checkbox'); // Right panel checkbox
	const $saveButtonRight = $('#cd-save-post-options'); // Right panel save button
	const $feedbackDiv = $('#cd-options-feedback');
	const $spinnerRight = $saveButtonRight.siblings('.spinner');
	const $postSearchInput = $('#cd-post-search');

	let originalPostListItems = [];
	// Store original list items with their HTML structure for search
	$postListUl.find('li').each(function() {
		originalPostListItems.push({
			id: $(this).data('post-id'),
			html: $(this).html(), // Store the full HTML of the <li> content
			text: $(this)
				.find('a.cd-post-title-link')
				.text()
				.toLowerCase(), // For searching
		});
	});

	// Function to filter post list
	function filterPostList() {
		const searchTerm = $postSearchInput.val().toLowerCase();
		$postListUl.empty(); // Clear current list

		if (searchTerm === '') {
			$.each(originalPostListItems, function(index, item) {
				$postListUl.append('<li data-post-id="' + item.id + '">' + item.html + '</li>');
			});
			// Re-apply active class if a post was selected before search
			const currentSelectedId = $selectedPostIdInput.val();
			if (currentSelectedId) {
				$postListUl.find('li[data-post-id="' + currentSelectedId + '"]').addClass('cd-active-post-li');
			}
			return;
		}
		let foundItems = 0;
		$.each(originalPostListItems, function(index, item) {
			if (item.text.includes(searchTerm)) {
				$postListUl.append('<li data-post-id="' + item.id + '">' + item.html + '</li>');
				foundItems++;
			}
		});
		if (foundItems === 0) {
			$postListUl.html('<li>' + cdPostOptions.text_no_posts_found + '</li>');
		}
		// Re-apply active class if a post was selected before search and is in filtered results
		const currentSelectedId = $selectedPostIdInput.val();
		if (currentSelectedId) {
			$postListUl.find('li[data-post-id="' + currentSelectedId + '"]').addClass('cd-active-post-li');
		}
	}
	$postSearchInput.on('keyup', filterPostList);

	// Function to load and display options in the right panel
	function loadOptionsForPost(postId, postTitle) {
		// Remove active class from all li, then add to the current one
		$postListUl.find('li').removeClass('cd-active-post-li');
		$postListUl.find('li[data-post-id="' + postId + '"]').addClass('cd-active-post-li');

		$selectedPostTitle.text(postTitle);
		$selectedPostIdInput.val(postId);

		$settingsPanelDefault.hide();
		$settingsPanelContent.show();
		$feedbackDiv
			.hide()
			.removeClass('notice-success notice-error')
			.empty();
		$showPageCheckboxRight.prop('checked', false).prop('disabled', true);
		$highlightCheckboxRight.prop('checked', false).prop('disabled', true);
		$saveButtonRight.prop('disabled', true);
		$spinnerRight.addClass('is-active').css('visibility', 'visible');

		$.ajax({
			url: cdPostOptions.ajax_url,
			type: 'POST',
			data: {
				action: 'cd_fetch_post_display_options',
				nonce: cdPostOptions.nonce,
				post_id: postId,
			},
			success: function(response) {
				if (response.success) {
					$showPageCheckboxRight.prop('checked', response.data.show_page);
					$highlightCheckboxRight.prop('checked', response.data.highlight);
				} else {
					$feedbackDiv
						.text(response.data.message || cdPostOptions.text_error)
						.addClass('notice-error')
						.show();
				}
			},
			error: function() {
				$feedbackDiv
					.text(cdPostOptions.text_error)
					.addClass('notice-error')
					.show();
			},
			complete: function() {
				$showPageCheckboxRight.prop('disabled', false);
				$highlightCheckboxRight.prop('disabled', false);
				$saveButtonRight.prop('disabled', false);
				$spinnerRight.removeClass('is-active').css('visibility', 'hidden');
			},
		});
	}

	// Handle post selection (click on title link)
	$postListUl.on('click', 'a.cd-post-title-link', function(e) {
		e.preventDefault();
		const $this = $(this);
		const postId = $this.data('post-id');
		const postTitle = $this.text().trim(); // Ensure we get only the text of the link
		loadOptionsForPost(postId, postTitle);
	});

	// Function to save options (used by both list and right panel)
	function savePostOptions(postId, showPage, highlight, $feedbackTarget, $spinnerTarget, $buttonTarget, $listItemTarget) {
		if ($spinnerTarget) $spinnerTarget.addClass('is-active').css('visibility', 'visible');
		if ($buttonTarget) $buttonTarget.prop('disabled', true);
		if ($feedbackTarget)
			$feedbackTarget
				.hide()
				.removeClass('notice-success notice-error')
				.empty();
		if ($listItemTarget) $listItemTarget.removeClass('cd-item-saved cd-item-error').addClass('cd-item-saving');

		$.ajax({
			url: cdPostOptions.ajax_url,
			type: 'POST',
			data: {
				action: 'cd_save_post_display_options',
				nonce: cdPostOptions.nonce,
				post_id: postId,
				show_page: showPage,
				highlight: highlight,
			},
			success: function(response) {
				if (response.success) {
					if ($feedbackTarget) {
						$feedbackTarget
							.text(response.data.message || cdPostOptions.text_saved)
							.addClass('notice-success')
							.show();
					}
					if ($listItemTarget) {
						$listItemTarget.removeClass('cd-item-saving').addClass('cd-item-saved');
						setTimeout(function() {
							$listItemTarget.removeClass('cd-item-saved');
						}, 1500);
					}

					// Update list checkboxes state based on the response from server
					const $currentListItemInList = $postListUl.find('li[data-post-id="' + postId + '"]');
					if ($currentListItemInList.length) {
						$currentListItemInList.find('input.cd-list-show').prop('checked', response.data.new_show_page_state);
						$currentListItemInList.find('input.cd-list-highlight').prop('checked', response.data.new_highlight_state);
					}
					// Update right panel if this is the selected post
					if ($selectedPostIdInput.val() == postId) {
						$showPageCheckboxRight.prop('checked', response.data.new_show_page_state);
						$highlightCheckboxRight.prop('checked', response.data.new_highlight_state);
					}
					// Update originalPostListItems for search consistency
					originalPostListItems = originalPostListItems.map(item => {
						if (item.id == postId) {
							// Create a temporary li to manipulate its checkboxes and get updated HTML
							const tempLi = $('<li>' + item.html + '</li>');
							tempLi.find('input.cd-list-show').prop('checked', response.data.new_show_page_state);
							tempLi.find('input.cd-list-highlight').prop('checked', response.data.new_highlight_state);
							item.html = tempLi.html(); // Store the updated inner HTML
						}
						return item;
					});
				} else {
					if ($feedbackTarget) {
						$feedbackTarget
							.text(response.data.message || cdPostOptions.text_error)
							.addClass('notice-error')
							.show();
					}
					if ($listItemTarget) {
						$listItemTarget.removeClass('cd-item-saving').addClass('cd-item-error'); // Add an error class
						setTimeout(function() {
							$listItemTarget.removeClass('cd-item-error');
						}, 1500);
					}
				}
			},
			error: function() {
				if ($feedbackTarget) {
					$feedbackTarget
						.text(cdPostOptions.text_error)
						.addClass('notice-error')
						.show();
				}
				if ($listItemTarget) {
					$listItemTarget.removeClass('cd-item-saving').addClass('cd-item-error');
					setTimeout(function() {
						$listItemTarget.removeClass('cd-item-error');
					}, 1500);
				}
			},
			complete: function() {
				if ($spinnerTarget) $spinnerTarget.removeClass('is-active').css('visibility', 'hidden');
				if ($buttonTarget) $buttonTarget.prop('disabled', false);
				if ($feedbackTarget) {
					setTimeout(function() {
						$feedbackTarget.fadeOut();
					}, 3000);
				}
			},
		});
	}

	// Handle change on checkboxes in the list
	$postListUl.on('change', 'input.cd-list-indicator', function(e) {
		e.stopPropagation(); // Prevent event bubbling
		const $thisCheckbox = $(this);
		const $listItem = $thisCheckbox.closest('li');
		const postId = $listItem.data('post-id');

		// Get values from the checkboxes within this specific list item
		const showPageVal = $listItem.find('input.cd-list-show').is(':checked');
		const highlightVal = $listItem.find('input.cd-list-highlight').is(':checked');

		// Call savePostOptions, passing the listItem for visual feedback
		savePostOptions(postId, showPageVal, highlightVal, null, null, null, $listItem);
	});

	// Handle save settings from the right panel
	$saveButtonRight.on('click', function() {
		const postId = $selectedPostIdInput.val();
		if (!postId) return;

		const showPage = $showPageCheckboxRight.is(':checked');
		const highlight = $highlightCheckboxRight.is(':checked');
		// Pass the corresponding list item for visual feedback as well
		savePostOptions(postId, showPage, highlight, $feedbackDiv, $spinnerRight, $saveButtonRight, $postListUl.find('li[data-post-id="' + postId + '"]'));
	});
});
