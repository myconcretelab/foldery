(function ($, wp) {
	'use strict';

	var config = window.folderyMediaLibrary || {};
	var selectedFolder = parseInt(config.tree && config.tree.selected, 10) || 0;
	var draggedAttachmentId = null;
	var isSorting = false;
	var isFolderSorting = false;
	var collapsedFolders = loadCollapsedFolders();

	function labels(key) {
		return config.labels && config.labels[key] ? config.labels[key] : key;
	}

	function escapeHtml(value) {
		return $('<div/>').text(value).html();
	}

	function loadCollapsedFolders() {
		try {
			return JSON.parse(window.localStorage.getItem('folderyCollapsedFolders') || '{}');
		} catch (e) {
			return {};
		}
	}

	function saveCollapsedFolders() {
		try {
			window.localStorage.setItem('folderyCollapsedFolders', JSON.stringify(collapsedFolders));
		} catch (e) {}
	}

	function flattenFolders(folders, depth, out) {
		(folders || []).forEach(function (folder) {
			out.push({
				id: parseInt(folder.id, 10),
				name: folder.name,
				count: parseInt(folder.count, 10) || 0,
				depth: depth
			});
			flattenFolders(folder.children || [], depth + 1, out);
		});
		return out;
	}

	function findFolderById(folders, id) {
		var found = null;
		(folders || []).some(function (folder) {
			if (parseInt(folder.id, 10) === id) {
				found = folder;
				return true;
			}
			found = findFolderById(folder.children || [], id);
			return !!found;
		});
		return found;
	}

	function currentFolder() {
		return findFolderById(config.tree ? config.tree.folders : [], selectedFolder);
	}

	function currentLinkedPageId() {
		var folder = currentFolder();
		return folder ? (parseInt(folder.linkedPageId, 10) || 0) : 0;
	}

	function pageOptions(selectedPageId) {
		var html = '<option value="0">' + escapeHtml(labels('noLinkedPage')) + '</option>';
		(config.pages || []).forEach(function (page) {
			var id = parseInt(page.id, 10);
			var title = page.title || ('#' + id);
			var status = page.status && page.status !== 'publish' ? ' [' + page.status + ']' : '';
			html += '<option value="' + id + '"' + (id === selectedPageId ? ' selected' : '') + '>' + escapeHtml(title + status) + '</option>';
		});
		return html;
	}

	function allFilterOptions() {
		var all = [];
		if (config.tree && config.tree.all) {
			all.push({
				id: 0,
				name: config.tree.all.name || labels('allFiles'),
				count: config.tree.all.count || 0,
				depth: 0
			});
		}
		if (config.tree && config.tree.unorganized) {
			all.push({
				id: parseInt(config.tree.unorganized.id, 10),
				name: config.tree.unorganized.name || labels('unorganized'),
				count: config.tree.unorganized.count || 0,
				depth: 0
			});
		}
		return all.concat(flattenFolders(config.tree ? config.tree.folders : [], 0, []));
	}

	function treeNode(folder, depth) {
		var id = parseInt(folder.id, 10);
		var active = id === selectedFolder ? ' is-active' : '';
		var hasChildren = folder.children && folder.children.length;
		var collapsed = hasChildren && collapsedFolders[id] ? ' is-collapsed' : '';
		var children = (folder.children || []).map(function (child) {
			return treeNode(child, depth + 1);
		}).join('');

		return '<li class="foldery-media-folder-item' + collapsed + '" data-folder-id="' + id + '">' +
			'<div class="foldery-media-folder-row" style="--folder-depth:' + depth + '">' +
				'<button type="button" class="foldery-media-toggle" data-folder-id="' + id + '"' + (hasChildren ? '' : ' disabled') + ' aria-expanded="' + (collapsed ? 'false' : 'true') + '"></button>' +
				'<span class="foldery-media-folder-handle" aria-hidden="true"></span>' +
				'<button type="button" class="foldery-media-folder' + active + '" data-folder-id="' + id + '">' +
					'<span class="foldery-media-folder__name">' + escapeHtml(folder.name) + '</span>' +
					'<span class="foldery-media-folder__count">' + (parseInt(folder.count, 10) || 0) + '</span>' +
				'</button>' +
			'</div>' +
			'<ul class="foldery-media-folder-list" data-parent-id="' + id + '">' + children + '</ul>' +
		'</li>';
	}

	function renderPanel() {
		var tree = config.tree || {};
		var allActive = selectedFolder === 0 ? ' is-active' : '';
		var rootActive = selectedFolder === parseInt(config.rootId, 10) ? ' is-active' : '';
		var html = '<div class="foldery-media-panel" aria-label="' + escapeHtml(labels('title')) + '">' +
			'<div class="foldery-media-panel__header">' +
				'<strong>' + escapeHtml(labels('title')) + '</strong>' +
				'<button type="button" class="button button-small foldery-media-create">' + escapeHtml(labels('newFolder')) + '</button>' +
			'</div>' +
			'<div class="foldery-media-tree">' +
				'<ul class="foldery-media-system">' +
					'<li><button type="button" class="foldery-media-folder' + allActive + '" data-folder-id="0" style="--folder-depth:0">' +
					'<span class="foldery-media-folder__name">' + escapeHtml((tree.all && tree.all.name) || labels('allFiles')) + '</span>' +
					'<span class="foldery-media-folder__count">' + ((tree.all && tree.all.count) || 0) + '</span>' +
					'</button></li>' +
					'<li><button type="button" class="foldery-media-folder' + rootActive + '" data-folder-id="' + parseInt(config.rootId, 10) + '" style="--folder-depth:0">' +
					'<span class="foldery-media-folder__name">' + escapeHtml((tree.unorganized && tree.unorganized.name) || labels('unorganized')) + '</span>' +
					'<span class="foldery-media-folder__count">' + ((tree.unorganized && tree.unorganized.count) || 0) + '</span>' +
					'</button></li>' +
				'</ul>' +
				'<ul class="foldery-media-folder-list foldery-media-folder-list-root" data-parent-id="' + parseInt(config.rootId, 10) + '">' +
					((tree.folders || []).map(function (folder) { return treeNode(folder, 0); }).join('')) +
				'</ul>' +
			'</div>' +
			'<div class="foldery-media-actions">' +
				'<button type="button" class="button foldery-media-move">' + escapeHtml(labels('moveSelected')) + '</button>' +
				'<button type="button" class="button foldery-media-rename">' + escapeHtml(labels('rename')) + '</button>' +
				'<button type="button" class="button foldery-media-delete">' + escapeHtml(labels('delete')) + '</button>' +
			'</div>' +
			'<div class="foldery-media-page-link">' +
				'<label for="foldery-media-linked-page">' + escapeHtml(labels('linkedPage')) + '</label>' +
				'<select id="foldery-media-linked-page" class="foldery-media-linked-page">' + pageOptions(currentLinkedPageId()) + '</select>' +
				'<button type="button" class="button foldery-media-save-page-link">' + escapeHtml(labels('savePageLink')) + '</button>' +
			'</div>' +
			'<p class="foldery-media-message" aria-live="polite"></p>' +
		'</div>';

		var $panel = $('.foldery-media-panel');
		if ($panel.length) {
			$panel.replaceWith(html);
		} else {
			var $target = $('.wrap .wp-filter').first();
			if (!$target.length) {
				$target = $('.wrap form#posts-filter').first();
			}
			if (!$target.length) {
				$target = $('.wrap form#file-form, .wrap form#async-upload').first();
			}
			($target.length ? $target : $('.wrap').first()).before(html);
		}
		updateButtons();
		window.setTimeout(initFolderSorting, 0);
	}

	function updateButtons() {
		var canEditFolder = selectedFolder > 0;
		$('.foldery-media-rename, .foldery-media-delete').prop('disabled', !canEditFolder);
		$('.foldery-media-move').prop('disabled', selectedFolder === 0);
		$('.foldery-media-linked-page, .foldery-media-save-page-link').prop('disabled', !canEditFolder);
		$('.foldery-media-linked-page').val(String(currentLinkedPageId()));
		$('.attachments-browser').toggleClass('foldery-media-can-reorder', canReorder());
	}

	function mediaBrowser() {
		if (!wp || !wp.media || !wp.media.frame || !wp.media.frame.content) {
			return null;
		}
		try {
			return wp.media.frame.content.get();
		} catch (e) {
			return null;
		}
	}

	function refreshMedia() {
		var browser = mediaBrowser();
		if (browser && browser.collection && browser.collection.props) {
			browser.collection.props.set({ foldery_media_folder: selectedFolder });
			browser.collection.props.unset('paged');
			browser.collection.reset();
			browser.collection.more();
			syncUploader();
			setTimeout(initAttachmentSorting, 500);
			return;
		}

		if ($('body').hasClass('upload-php')) {
			var url = new URL(window.location.href);
			if (selectedFolder === 0) {
				url.searchParams.delete('foldery_media_folder');
			} else {
				url.searchParams.set('foldery_media_folder', selectedFolder);
			}
			window.location.href = url.toString();
		}
	}

	function setFolder(id, refresh) {
		selectedFolder = parseInt(id, 10);
		$('.foldery-media-folder').removeClass('is-active');
		$('.foldery-media-folder[data-folder-id="' + selectedFolder + '"]').addClass('is-active');
		updateButtons();
		syncUploader();
		initAttachmentSorting();
		if (refresh !== false) {
			refreshMedia();
		}
	}

	function selectedAttachmentIds() {
		var ids = [];
		var browser = mediaBrowser();
		if (browser && browser.controller && browser.controller.state) {
			var selection = browser.controller.state().get('selection');
			if (selection && selection.length) {
				ids = selection.pluck('id');
			}
		}
		if (!ids.length) {
			$('.attachments .attachment.selected, .attachments .attachment[aria-checked="true"]').each(function () {
				var id = parseInt($(this).attr('data-id'), 10);
				if (id) {
					ids.push(id);
				}
			});
		}
		return ids.filter(Boolean);
	}

	function currentFolderName() {
		var $active = $('.foldery-media-folder[data-folder-id="' + selectedFolder + '"] .foldery-media-folder__name');
		return $active.length ? $active.text() : '';
	}

	function request(folderAction, data) {
		return $.post(config.ajaxUrl, $.extend({
			action: 'foldery_media_admin',
			nonce: config.nonce,
			folder_action: folderAction,
			foldery_media_folder: selectedFolder
		}, data || {})).done(function (response) {
			if (response && response.success && response.data) {
				config.tree = response.data.tree || config.tree;
				selectedFolder = parseInt(response.data.selected, 10);
				renderPanel();
				setFolder(selectedFolder, false);
				$('.foldery-media-message').text(response.data.message || '');
			}
		}).fail(function (xhr) {
			var response = xhr.responseJSON;
			var message = response && response.data && response.data.message ? response.data.message : xhr.statusText;
			$('.foldery-media-message').text(message);
		});
	}

	function syncUploader() {
		$('input[name="foldery_media_folder"], input[name="folderyMediaFolder"]').val(selectedFolder);
		if (window.wpUploaderInit && window.wpUploaderInit.multipart_params) {
			window.wpUploaderInit.multipart_params.foldery_media_folder = selectedFolder;
		}
		if (wp && wp.Uploader && wp.Uploader.defaults) {
			wp.Uploader.defaults.multipart_params = wp.Uploader.defaults.multipart_params || {};
			wp.Uploader.defaults.multipart_params.foldery_media_folder = selectedFolder;
		}
	}

	function canReorder() {
		return selectedFolder > 0 && selectedFolder !== parseInt(config.rootId, 10);
	}

	function visibleAttachmentIds() {
		var ids = [];
		$('.attachments .attachment').each(function () {
			var id = parseInt($(this).attr('data-id'), 10);
			if (id) {
				ids.push(id);
			}
		});
		return ids;
	}

	function saveAttachmentOrder() {
		var ids = visibleAttachmentIds();
		if (!canReorder() || ids.length < 2) {
			return;
		}
		request('reorder', { id: selectedFolder, ids: ids }).done(function () {
			$('.foldery-media-message').text(labels('orderSaved'));
		});
	}

	function refreshFolderDepths() {
		function updateList($list, depth) {
			$list.children('.foldery-media-folder-item').each(function () {
				var $item = $(this);
				$item.children('.foldery-media-folder-row').css('--folder-depth', depth);
				updateList($item.children('.foldery-media-folder-list').first(), depth + 1);
			});
		}
		updateList($('.foldery-media-folder-list-root').first(), 0);
	}

	function applyCollapsedFolders() {
		$('.foldery-media-folder-item').each(function () {
			var $item = $(this);
			var id = parseInt($item.attr('data-folder-id'), 10);
			var collapsed = !!collapsedFolders[id];
			var hasChildren = $item.children('.foldery-media-folder-list').children('.foldery-media-folder-item').length > 0;
			$item.toggleClass('is-collapsed', collapsed && hasChildren);
			$item.children('.foldery-media-folder-row').find('.foldery-media-toggle')
				.prop('disabled', !hasChildren)
				.attr('aria-expanded', collapsed && hasChildren ? 'false' : 'true');
		});
	}

	function folderTreePayload() {
		var rows = [];
		$('.foldery-media-folder-list').each(function () {
			var parent = parseInt($(this).attr('data-parent-id'), 10);
			$(this).children('.foldery-media-folder-item').each(function (index) {
				var id = parseInt($(this).attr('data-folder-id'), 10);
				if (id) {
					rows.push({
						id: id,
						parent: parent,
						ord: index + 1
					});
				}
			});
		});
		return rows;
	}

	function saveFolderOrder() {
		var rows = folderTreePayload();
		if (!rows.length) {
			return;
		}
		request('reorder_folders', { tree: JSON.stringify(rows) }).done(function () {
			$('.foldery-media-message').text(labels('folderOrderSaved'));
			refreshFolderDepths();
		});
	}

	function initFolderSorting() {
		var $lists = $('.foldery-media-folder-list');
		if (!$lists.length || !$.fn.sortable) {
			return;
		}

		$lists.each(function () {
			var $list = $(this);
			if ($list.data('ui-sortable')) {
				$list.sortable('refresh');
				return;
			}

			$list.sortable({
				connectWith: '.foldery-media-folder-list',
				handle: '.foldery-media-folder-handle',
				items: '> .foldery-media-folder-item',
				placeholder: 'foldery-media-folder-placeholder',
				tolerance: 'pointer',
				start: function (event, ui) {
					isFolderSorting = true;
					ui.placeholder.height(ui.item.children('.foldery-media-folder-row').outerHeight());
				},
				sort: refreshFolderDepths,
				stop: function () {
					refreshFolderDepths();
					applyCollapsedFolders();
					window.setTimeout(function () {
						isFolderSorting = false;
					}, 0);
				},
				update: function (event, ui) {
					if (this === ui.item.parent()[0]) {
						saveFolderOrder();
					}
				}
			});
		});
	}

	function enableDragOnAttachments() {
		if (canReorder()) {
			$('.attachments .attachment').removeAttr('draggable');
			return;
		}
		$('.attachments .attachment').attr('draggable', 'true');
	}

	function initAttachmentSorting() {
		var $attachments = $('.attachments');
		if (!$attachments.length || !$.fn.sortable) {
			return;
		}

		if (!canReorder()) {
			if ($attachments.data('ui-sortable')) {
				$attachments.sortable('destroy');
			}
			enableDragOnAttachments();
			return;
		}

		$('.attachments .attachment').removeAttr('draggable');
		if ($attachments.data('ui-sortable')) {
			$attachments.sortable('refresh');
			return;
		}

		$attachments.sortable({
			items: '.attachment',
			placeholder: 'foldery-media-sort-placeholder',
			tolerance: 'pointer',
			start: function (event, ui) {
				isSorting = true;
				ui.placeholder.width(ui.item.outerWidth());
				ui.placeholder.height(ui.item.outerHeight());
			},
			stop: function () {
				window.setTimeout(function () {
					isSorting = false;
				}, 0);
			},
			update: saveAttachmentOrder
		});
	}

	function installMediaFilter() {
		if (!wp || !wp.media || !wp.media.view || !wp.media.view.AttachmentFilters || !wp.media.view.AttachmentsBrowser) {
			return;
		}

		var FolderFilter = wp.media.view.AttachmentFilters.extend({
			id: 'media-attachment-foldery-filter',
			createFilters: function () {
				var filters = {};
				allFilterOptions().forEach(function (folder) {
					var prefix = folder.depth > 0 ? new Array(folder.depth + 1).join('- ') : '';
					filters['foldery-' + folder.id] = {
						text: prefix + folder.name,
						props: { foldery_media_folder: folder.id },
						priority: 20 + folder.depth
					};
				});
				this.filters = filters;
			}
		});

		var AttachmentsBrowser = wp.media.view.AttachmentsBrowser;
		wp.media.view.AttachmentsBrowser = AttachmentsBrowser.extend({
			createToolbar: function () {
				AttachmentsBrowser.prototype.createToolbar.apply(this, arguments);
				this.toolbar.set('folderyFolderFilter', new FolderFilter({
					controller: this.controller,
					model: this.collection.props,
					priority: -80
				}).render());
			}
		});
	}

	installMediaFilter();

	$(function () {
		renderPanel();
		syncUploader();
		setTimeout(function () {
			enableDragOnAttachments();
			initAttachmentSorting();
		}, 800);

		$(document).on('click', '.foldery-media-folder', function () {
			if (isFolderSorting) {
				return;
			}
			setFolder(parseInt($(this).attr('data-folder-id'), 10));
		});

		$(document).on('click', '.foldery-media-toggle', function (event) {
			event.preventDefault();
			event.stopPropagation();
			var id = parseInt($(this).attr('data-folder-id'), 10);
			var $item = $('.foldery-media-folder-item[data-folder-id="' + id + '"]').first();
			if (!id || !$item.length || $(this).prop('disabled')) {
				return;
			}
			if ($item.hasClass('is-collapsed')) {
				delete collapsedFolders[id];
			} else {
				collapsedFolders[id] = true;
			}
			saveCollapsedFolders();
			applyCollapsedFolders();
		});

		$(document).on('click', '.foldery-media-create', function () {
			var parent = selectedFolder > 0 ? selectedFolder : config.rootId;
			var name = window.prompt(labels('folderName'));
			if (name) {
				request('create', { parent: parent, name: name }).done(refreshMedia);
			}
		});

		$(document).on('click', '.foldery-media-rename', function () {
			if (selectedFolder <= 0) {
				return;
			}
			var name = window.prompt(labels('folderName'), currentFolderName());
			if (name) {
				request('rename', { id: selectedFolder, name: name }).done(refreshMedia);
			}
		});

		$(document).on('click', '.foldery-media-delete', function () {
			if (selectedFolder <= 0 || !window.confirm(labels('confirmDelete'))) {
				return;
			}
			request('delete', { id: selectedFolder }).done(refreshMedia);
		});

		$(document).on('click', '.foldery-media-move', function () {
			var ids = selectedAttachmentIds();
			if (!ids.length) {
				window.alert(labels('selectFiles'));
				return;
			}
			request('move', { to: selectedFolder, ids: ids }).done(function () {
				$('.foldery-media-message').text(labels('moved'));
				refreshMedia();
			});
		});

		$(document).on('click', '.foldery-media-save-page-link', function () {
			if (selectedFolder <= 0) {
				return;
			}
			request('link_page', {
				id: selectedFolder,
				page_id: parseInt($('.foldery-media-linked-page').val(), 10) || 0
			}).done(function () {
				$('.foldery-media-message').text(labels('pageLinkSaved'));
			});
		});

		$(document).on('mouseenter', '.attachments .attachment', enableDragOnAttachments);

		$(document).on('dragstart', '.attachments .attachment', function (event) {
			if (isSorting || canReorder()) {
				event.preventDefault();
				return;
			}
			draggedAttachmentId = parseInt($(this).attr('data-id'), 10);
			if (event.originalEvent && event.originalEvent.dataTransfer && draggedAttachmentId) {
				event.originalEvent.dataTransfer.setData('text/plain', String(draggedAttachmentId));
				event.originalEvent.dataTransfer.effectAllowed = 'move';
			}
		});

		$(document).on('dragover', '.foldery-media-folder', function (event) {
			if (parseInt($(this).attr('data-folder-id'), 10) !== 0) {
				event.preventDefault();
				$(this).addClass('is-drop-target');
			}
		});

		$(document).on('dragleave drop', '.foldery-media-folder', function () {
			$(this).removeClass('is-drop-target');
		});

		$(document).on('drop', '.foldery-media-folder', function (event) {
			event.preventDefault();
			var to = parseInt($(this).attr('data-folder-id'), 10);
			var id = draggedAttachmentId;
			if (to === 0 || !id) {
				return;
			}
			setFolder(to, false);
			request('move', { to: to, ids: [id] }).done(refreshMedia);
		});

		if (wp && wp.media && wp.media.frame) {
			wp.media.frame.on('content:render:browse', function () {
				setTimeout(function () {
					setFolder(selectedFolder, false);
					enableDragOnAttachments();
					initAttachmentSorting();
				}, 100);
			});
		}
	});
})(jQuery, window.wp);
