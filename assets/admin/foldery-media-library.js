(function ($, wp) {
	'use strict';

	var config = window.folderyMediaLibrary || {};
	var selectedFolder = parseInt(config.tree && config.tree.selected, 10) || 0;
	var draggedAttachmentId = null;

	function labels(key) {
		return config.labels && config.labels[key] ? config.labels[key] : key;
	}

	function escapeHtml(value) {
		return $('<div/>').text(value).html();
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
		var children = (folder.children || []).map(function (child) {
			return treeNode(child, depth + 1);
		}).join('');

		return '<li>' +
			'<button type="button" class="foldery-media-folder' + active + '" data-folder-id="' + id + '" style="--folder-depth:' + depth + '">' +
				'<span class="foldery-media-folder__name">' + escapeHtml(folder.name) + '</span>' +
				'<span class="foldery-media-folder__count">' + (parseInt(folder.count, 10) || 0) + '</span>' +
			'</button>' +
			(children ? '<ul>' + children + '</ul>' : '') +
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
			'<ul class="foldery-media-tree">' +
				'<li><button type="button" class="foldery-media-folder' + allActive + '" data-folder-id="0" style="--folder-depth:0">' +
					'<span class="foldery-media-folder__name">' + escapeHtml((tree.all && tree.all.name) || labels('allFiles')) + '</span>' +
					'<span class="foldery-media-folder__count">' + ((tree.all && tree.all.count) || 0) + '</span>' +
				'</button></li>' +
				'<li><button type="button" class="foldery-media-folder' + rootActive + '" data-folder-id="' + parseInt(config.rootId, 10) + '" style="--folder-depth:0">' +
					'<span class="foldery-media-folder__name">' + escapeHtml((tree.unorganized && tree.unorganized.name) || labels('unorganized')) + '</span>' +
					'<span class="foldery-media-folder__count">' + ((tree.unorganized && tree.unorganized.count) || 0) + '</span>' +
				'</button></li>' +
				((tree.folders || []).map(function (folder) { return treeNode(folder, 0); }).join('')) +
			'</ul>' +
			'<div class="foldery-media-actions">' +
				'<button type="button" class="button foldery-media-move">' + escapeHtml(labels('moveSelected')) + '</button>' +
				'<button type="button" class="button foldery-media-rename">' + escapeHtml(labels('rename')) + '</button>' +
				'<button type="button" class="button foldery-media-delete">' + escapeHtml(labels('delete')) + '</button>' +
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
	}

	function updateButtons() {
		var canEditFolder = selectedFolder > 0;
		$('.foldery-media-rename, .foldery-media-delete').prop('disabled', !canEditFolder);
		$('.foldery-media-move').prop('disabled', selectedFolder === 0);
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
			browser.collection.props.set({ rml_folder: selectedFolder });
			browser.collection.props.unset('paged');
			browser.collection.reset();
			browser.collection.more();
			syncUploader();
			return;
		}

		if ($('body').hasClass('upload-php')) {
			var url = new URL(window.location.href);
			if (selectedFolder === 0) {
				url.searchParams.delete('rml_folder');
			} else {
				url.searchParams.set('rml_folder', selectedFolder);
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
			action: 'foldery_rml_admin',
			nonce: config.nonce,
			folder_action: folderAction,
			rml_folder: selectedFolder
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
		$('input[name="rml_folder"], input[name="rmlFolder"]').val(selectedFolder);
		if (window.wpUploaderInit && window.wpUploaderInit.multipart_params) {
			window.wpUploaderInit.multipart_params.rml_folder = selectedFolder;
		}
		if (wp && wp.Uploader && wp.Uploader.defaults) {
			wp.Uploader.defaults.multipart_params = wp.Uploader.defaults.multipart_params || {};
			wp.Uploader.defaults.multipart_params.rml_folder = selectedFolder;
		}
	}

	function enableDragOnAttachments() {
		$('.attachments .attachment').attr('draggable', 'true');
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
						props: { rml_folder: folder.id },
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
		setTimeout(enableDragOnAttachments, 800);

		$(document).on('click', '.foldery-media-folder', function () {
			setFolder(parseInt($(this).attr('data-folder-id'), 10));
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

		$(document).on('mouseenter', '.attachments .attachment', enableDragOnAttachments);

		$(document).on('dragstart', '.attachments .attachment', function (event) {
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
				}, 100);
			});
		}
	});
})(jQuery, window.wp);
