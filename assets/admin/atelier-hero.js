(function($) {
  'use strict';

  function clamp(value, min, max) {
    value = parseInt(value, 10);
    if (!value) {
      value = min;
    }

    return Math.min(max, Math.max(min, value));
  }

  function escapeHtml(value) {
    return String(value || '').replace(/[&<>"']/g, function(character) {
      return {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
      }[character];
    });
  }

  function mediaIds(selection) {
    var ids = [];

    selection.each(function(attachment) {
      ids.push(parseInt(attachment.id, 10));
    });

    return ids.filter(Boolean);
  }

  function attachmentData(attachment) {
    var data = attachment && attachment.toJSON ? attachment.toJSON() : {};
    var sizes = data.sizes || {};

    return {
      id: parseInt(data.id || attachment.id, 10),
      title: data.title || data.filename || '',
      thumbnail: sizes.thumbnail ? sizes.thumbnail.url : data.url,
      preview: sizes.large ? sizes.large.url : sizes.medium_large ? sizes.medium_large.url : data.url
    };
  }

  function normalizeArtwork(item) {
    var id;

    if (typeof item === 'number' || typeof item === 'string') {
      id = parseInt(item, 10);
      return id ? { id: id, scale: 100, tapes: 1 } : null;
    }

    if (!item || typeof item !== 'object') {
      return null;
    }

    id = parseInt(item.id, 10);
    if (!id) {
      return null;
    }

    return {
      id: id,
      scale: clamp(item.scale || 100, 55, 145),
      tapes: clamp(item.tapes === 0 ? 0 : item.tapes || 1, 0, 5),
      title: item.title || '',
      thumbnail: item.thumbnail || ''
    };
  }

  function parseArtworks(value) {
    var raw;
    var seen = {};

    value = $.trim(String(value || ''));
    if (!value) {
      return [];
    }

    if (value.charAt(0) === '[') {
      try {
        raw = JSON.parse(value);
      } catch (error) {
        raw = [];
      }
    } else {
      raw = value.split(',');
    }

    return (raw || []).map(normalizeArtwork).filter(function(item) {
      if (!item || seen[item.id]) {
        return false;
      }

      seen[item.id] = true;
      return true;
    });
  }

  function stringifyArtworks(items) {
    return JSON.stringify((items || []).map(normalizeArtwork).filter(Boolean).map(function(item) {
      return {
        id: item.id,
        scale: item.scale,
        tapes: item.tapes
      };
    }));
  }

  function thumbnail(attachment) {
    var data = attachmentData(attachment);

    return data.thumbnail ? '<img src="' + escapeHtml(data.thumbnail) + '" alt="">' : '';
  }

  function renderArtworkItem(item) {
    item = normalizeArtwork(item);
    if (!item) {
      return '';
    }

    return '' +
      '<details class="foldery-atelier-artwork-item" data-artwork-id="' + item.id + '">' +
        '<summary class="foldery-atelier-artwork-summary">' +
          '<span class="foldery-atelier-artwork-tools">' +
            '<span class="foldery-atelier-artwork-handle" aria-label="Reordonner">' +
              '<span class="dashicons dashicons-menu" aria-hidden="true"></span>' +
            '</span>' +
            '<button type="button" class="button-link foldery-atelier-artwork-remove" aria-label="Retirer cette image">' +
              '<span class="dashicons dashicons-trash" aria-hidden="true"></span>' +
            '</button>' +
          '</span>' +
          '<span class="foldery-atelier-artwork-thumb">' +
            (item.thumbnail ? '<img src="' + escapeHtml(item.thumbnail) + '" alt="">' : '') +
          '</span>' +
          '<span class="foldery-atelier-artwork-title">' + escapeHtml(item.title || 'Image #' + item.id) + '</span>' +
        '</summary>' +
        '<div class="foldery-atelier-artwork-main">' +
          '<label class="foldery-atelier-artwork-range">' +
            '<span>Proportion <output>' + item.scale + '%</output></span>' +
            '<input type="range" class="foldery-atelier-artwork-scale" min="55" max="145" step="5" value="' + item.scale + '">' +
          '</label>' +
          '<label class="foldery-atelier-artwork-range">' +
            '<span>Scotch <output>' + item.tapes + '</output></span>' +
            '<input type="range" class="foldery-atelier-artwork-tapes" min="0" max="5" step="1" value="' + item.tapes + '">' +
          '</label>' +
        '</div>' +
      '</details>';
  }

  function artworkFromNode() {
    var $item = $(this);

    return normalizeArtwork({
      id: $item.data('artwork-id'),
      scale: $item.find('.foldery-atelier-artwork-scale').val(),
      tapes: $item.find('.foldery-atelier-artwork-tapes').val(),
      title: $item.find('.foldery-atelier-artwork-title').text(),
      thumbnail: $item.find('.foldery-atelier-artwork-thumb img').attr('src') || ''
    });
  }

  function currentArtworks($field) {
    return $field.find('.foldery-atelier-artwork-item').map(artworkFromNode).get().filter(Boolean);
  }

  function normalizeDecor(item, index) {
    var id;
    var defaults = {
      x: 22 + (((parseInt(index, 10) || 0) * 23) % 56),
      y: 100 + ((parseInt(index, 10) || 0) * 180),
      width: 280,
      scale: 100,
      rotate: 0,
      alpha: 100
    };

    if (typeof item === 'number' || typeof item === 'string') {
      id = parseInt(item, 10);
      return id ? $.extend({ id: id }, defaults) : null;
    }

    if (!item || typeof item !== 'object') {
      return null;
    }

    id = parseInt(item.id, 10);
    if (!id) {
      return null;
    }

    return {
      id: id,
      x: clamp(item.x === 0 ? 0 : item.x || defaults.x, 0, 100),
      y: clamp(item.y === 0 ? 0 : item.y || defaults.y, 0, 4000),
      width: clamp(item.width || defaults.width, 60, 900),
      scale: clamp(item.scale || defaults.scale, 20, 300),
      rotate: Math.min(45, Math.max(-45, parseInt(item.rotate || defaults.rotate, 10) || 0)),
      alpha: clamp(item.alpha === 0 ? 0 : item.alpha || defaults.alpha, 0, 100),
      title: item.title || '',
      thumbnail: item.thumbnail || '',
      preview: item.preview || item.thumbnail || ''
    };
  }

  function parseDecorations(value) {
    var raw;
    var seen = {};

    value = $.trim(String(value || ''));
    if (!value) {
      return [];
    }

    if (value.charAt(0) === '[') {
      try {
        raw = JSON.parse(value);
      } catch (error) {
        raw = [];
      }
    } else {
      raw = value.split(',');
    }

    return (raw || []).map(normalizeDecor).filter(function(item) {
      if (!item || seen[item.id]) {
        return false;
      }

      seen[item.id] = true;
      return true;
    });
  }

  function stringifyDecorations(items) {
    return JSON.stringify((items || []).map(normalizeDecor).filter(Boolean).map(function(item) {
      return {
        id: item.id,
        x: item.x,
        y: item.y,
        width: item.width,
        scale: item.scale,
        rotate: item.rotate,
        alpha: item.alpha
      };
    }));
  }

  function renderDecorItem(item, index) {
    item = normalizeDecor(item, index);
    if (!item) {
      return '';
    }

    return '' +
      '<details class="foldery-atelier-decor-item" data-decor-id="' + item.id + '" data-decor-preview="' + escapeHtml(item.preview || item.thumbnail || '') + '">' +
        '<summary class="foldery-atelier-decor-summary">' +
          '<span class="foldery-atelier-decor-tools">' +
            '<span class="foldery-atelier-decor-handle" aria-label="Reordonner">' +
              '<span class="dashicons dashicons-menu" aria-hidden="true"></span>' +
            '</span>' +
            '<button type="button" class="button-link foldery-atelier-decor-remove" aria-label="Retirer cette image">' +
              '<span class="dashicons dashicons-trash" aria-hidden="true"></span>' +
            '</button>' +
          '</span>' +
          '<span class="foldery-atelier-decor-thumb">' +
            (item.thumbnail ? '<img src="' + escapeHtml(item.thumbnail) + '" alt="">' : '') +
          '</span>' +
          '<span class="foldery-atelier-decor-title">' + escapeHtml(item.title || 'Image #' + item.id) + '</span>' +
        '</summary>' +
        '<div class="foldery-atelier-decor-main">' +
          '<label class="foldery-atelier-decor-range">' +
            '<span>X <output>' + item.x + '%</output></span>' +
            '<input type="range" class="foldery-atelier-decor-x" min="0" max="100" step="1" value="' + item.x + '">' +
          '</label>' +
          '<label class="foldery-atelier-decor-range">' +
            '<span>Y <output>' + item.y + 'px</output></span>' +
            '<input type="range" class="foldery-atelier-decor-y" min="0" max="4000" step="10" value="' + item.y + '">' +
          '</label>' +
          '<label class="foldery-atelier-decor-range">' +
            '<span>Largeur <output>' + item.width + 'px</output></span>' +
            '<input type="range" class="foldery-atelier-decor-width" min="60" max="900" step="10" value="' + item.width + '">' +
          '</label>' +
          '<label class="foldery-atelier-decor-range">' +
            '<span>Echelle <output>' + item.scale + '%</output></span>' +
            '<input type="range" class="foldery-atelier-decor-scale" min="20" max="300" step="5" value="' + item.scale + '">' +
          '</label>' +
          '<label class="foldery-atelier-decor-range">' +
            '<span>Rotation <output>' + item.rotate + 'deg</output></span>' +
            '<input type="range" class="foldery-atelier-decor-rotate" min="-45" max="45" step="1" value="' + item.rotate + '">' +
          '</label>' +
          '<label class="foldery-atelier-decor-range">' +
            '<span>Alpha <output>' + item.alpha + '%</output></span>' +
            '<input type="range" class="foldery-atelier-decor-alpha" min="0" max="100" step="1" value="' + item.alpha + '">' +
          '</label>' +
        '</div>' +
      '</details>';
  }

  function decorFromNode(index) {
    var $item = $(this);

    return normalizeDecor({
      id: $item.data('decor-id'),
      x: $item.find('.foldery-atelier-decor-x').val(),
      y: $item.find('.foldery-atelier-decor-y').val(),
      width: $item.find('.foldery-atelier-decor-width').val(),
      scale: $item.find('.foldery-atelier-decor-scale').val(),
      rotate: $item.find('.foldery-atelier-decor-rotate').val(),
      alpha: $item.find('.foldery-atelier-decor-alpha').val(),
      title: $item.find('.foldery-atelier-decor-title').text(),
      thumbnail: $item.find('.foldery-atelier-decor-thumb img').attr('src') || '',
      preview: $item.data('decor-preview') || ''
    }, index);
  }

  function currentDecorations($field) {
    return $field.find('.foldery-atelier-decor-item').map(decorFromNode).get().filter(Boolean);
  }

  function decorPreviewStyle(item) {
    item = normalizeDecor(item);
    if (!item) {
      return '';
    }

    return [
      '--atelier-content-image-x:' + item.x + '%',
      '--atelier-content-image-y:' + item.y + 'px',
      '--atelier-content-image-width:' + item.width + 'px',
      '--atelier-content-image-scale:' + (item.scale / 100).toFixed(2),
      '--atelier-content-image-rotate:' + item.rotate + 'deg',
      '--atelier-content-image-alpha:' + (item.alpha / 100).toFixed(2)
    ].join(';') + ';';
  }

  function editorDocuments() {
    var docs = [document];

    $('iframe').each(function() {
      var iframeDoc;

      try {
        iframeDoc = this.contentDocument || (this.contentWindow && this.contentWindow.document);
      } catch (error) {
        iframeDoc = null;
      }

      if (iframeDoc && iframeDoc !== document) {
        docs.push(iframeDoc);
      }
    });

    return docs;
  }

  function editorAtelierBoards() {
    var boards = [];

    editorDocuments().forEach(function(doc) {
      var board = doc.querySelector('.editor-styles-wrapper .atelier-board, .atelier-board');

      if (board && boards.indexOf(board) === -1) {
        boards.push(board);
      }
    });

    return boards;
  }

  function renderEditorPreview($field) {
    var items = currentDecorations($field);
    var boards = editorAtelierBoards();

    boards.forEach(function(board) {
      var doc = board.ownerDocument;
      var layer = board.querySelector('.atelier-content-images[data-foldery-editor-preview]');

      if (!items.length) {
        if (layer) {
          layer.remove();
        }
        return;
      }

      if (!layer) {
        layer = doc.createElement('div');
        layer.className = 'atelier-content-images';
        layer.setAttribute('data-foldery-editor-preview', 'true');
        layer.setAttribute('aria-hidden', 'true');
        board.insertBefore(layer, board.firstChild);
      }

      layer.innerHTML = items.map(function(item, index) {
        var src = item.preview || item.thumbnail || '';

        if (!src) {
          return '';
        }

        return '' +
          '<figure class="atelier-content-image atelier-content-image--' + (index + 1) + '" style="' + escapeHtml(decorPreviewStyle(item)) + '">' +
            '<img class="atelier-content-image__asset" src="' + escapeHtml(src) + '" alt="">' +
          '</figure>';
      }).join('');
    });
  }

  function scheduleEditorPreview($field) {
    window.clearTimeout($field.data('foldery-editor-preview-timeout'));
    $field.data('foldery-editor-preview-timeout', window.setTimeout(function() {
      renderEditorPreview($field);
    }, 40));
  }

  function initEditorPreview($field, attempt) {
    attempt = attempt || 0;
    renderEditorPreview($field);

    if (attempt >= 12 || editorAtelierBoards().length) {
      return;
    }

    window.setTimeout(function() {
      initEditorPreview($field, attempt + 1);
    }, 500);
  }

  function syncMetaField($input) {
    var adminData = window.FolderyAtelierAdmin || {};
    var metaKeys = adminData.metaKeys || {};
    var name = $input.attr('name');
    var metaKey = metaKeys[name];
    var meta = {};
    var editorDispatch;

    if (!metaKey || !window.wp || !wp.data || !wp.data.dispatch || !wp.data.select) {
      return;
    }

    try {
      if (!wp.data.select('core/editor') || !wp.data.dispatch('core/editor')) {
        return;
      }

      editorDispatch = wp.data.dispatch('core/editor');
    } catch (error) {
      return;
    }

    if (!editorDispatch.editPost) {
      return;
    }

    meta[metaKey] = $input.val() || '';
    editorDispatch.editPost({ meta: meta });
  }

  function syncArtworkField($field, pushMeta) {
    var $input = $field.find('input[type="hidden"]');
    var $items = $field.find('.foldery-atelier-artwork-item');

    $items.find('.foldery-atelier-artwork-scale').each(function() {
      var value = clamp($(this).val(), 55, 145);
      $(this).val(value).closest('label').find('output').text(value + '%');
    });

    $items.find('.foldery-atelier-artwork-tapes').each(function() {
      var value = clamp($(this).val(), 0, 5);
      $(this).val(value).closest('label').find('output').text(value);
    });

    $field.find('.foldery-atelier-artwork-list').toggleClass('is-empty', !$items.length);
    $input.val($items.length ? stringifyArtworks(currentArtworks($field)) : '');
    if (pushMeta !== false) {
      syncMetaField($input);
    }
  }

  function renderArtworkList($field, items) {
    $field.find('.foldery-atelier-artwork-list').html(items.map(renderArtworkItem).join(''));
    syncArtworkField($field);
  }

  function syncDecorField($field, pushMeta) {
    var $input = $field.find('input[type="hidden"]');
    var $items = $field.find('.foldery-atelier-decor-item');

    $items.find('.foldery-atelier-decor-x').each(function() {
      var value = clamp($(this).val(), 0, 100);
      $(this).val(value).closest('label').find('output').text(value + '%');
    });

    $items.find('.foldery-atelier-decor-y').each(function() {
      var value = clamp($(this).val(), 0, 4000);
      $(this).val(value).closest('label').find('output').text(value + 'px');
    });

    $items.find('.foldery-atelier-decor-width').each(function() {
      var value = clamp($(this).val(), 60, 900);
      $(this).val(value).closest('label').find('output').text(value + 'px');
    });

    $items.find('.foldery-atelier-decor-scale').each(function() {
      var value = clamp($(this).val(), 20, 300);
      $(this).val(value).closest('label').find('output').text(value + '%');
    });

    $items.find('.foldery-atelier-decor-rotate').each(function() {
      var value = Math.min(45, Math.max(-45, parseInt($(this).val(), 10) || 0));
      $(this).val(value).closest('label').find('output').text(value + 'deg');
    });

    $items.find('.foldery-atelier-decor-alpha').each(function() {
      var value = clamp($(this).val(), 0, 100);
      $(this).val(value).closest('label').find('output').text(value + '%');
    });

    $field.find('.foldery-atelier-decor-list').toggleClass('is-empty', !$items.length);
    $input.val($items.length ? stringifyDecorations(currentDecorations($field)) : '');
    scheduleEditorPreview($field);
    if (pushMeta !== false) {
      syncMetaField($input);
    }
  }

  function renderDecorList($field, items) {
    $field.find('.foldery-atelier-decor-list').html(items.map(renderDecorItem).join(''));
    syncDecorField($field);
  }

  function updatePreview($field, attachments) {
    var fieldType = $field.data('foldery-media-field');
    var isMultiple = fieldType === 'multiple';
    var isDecor = fieldType === 'decor';
    var $preview = $field.find('.foldery-atelier-media-preview');
    var html = '';

    if (isDecor) {
      renderDecorList(
        $field,
        mediaIds(attachments).map(function(id, index) {
          var existing = currentDecorations($field).filter(function(item) {
            return item.id === id;
          })[0];
          var attachment = attachments.get(id);
          var data = attachment ? attachmentData(attachment) : {};

          return $.extend(normalizeDecor(id, index), existing || {}, data);
        })
      );
      return;
    }

    if (isMultiple) {
      renderArtworkList(
        $field,
        mediaIds(attachments).map(function(id) {
          var existing = currentArtworks($field).filter(function(item) {
            return item.id === id;
          })[0];
          var attachment = attachments.get(id);
          var data = attachment ? attachmentData(attachment) : {};

          return $.extend({ scale: 100, tapes: 1 }, existing || {}, data);
        })
      );
      return;
    }

    attachments.each(function(attachment) {
      html += thumbnail(attachment);
    });

    $preview.html(html);
  }

  function clearField($field) {
    $field.find('input[type="hidden"]').val('');
    $field.find('.foldery-atelier-media-preview, .foldery-atelier-artwork-list, .foldery-atelier-decor-list').empty();
    if ($field.data('foldery-media-field') === 'multiple') {
      syncArtworkField($field);
    } else if ($field.data('foldery-media-field') === 'decor') {
      syncDecorField($field);
    } else {
      syncMetaField($field.find('input[type="hidden"]'));
    }
  }

  function initSortable() {
    $('.foldery-atelier-artwork-list').each(function() {
      var $list = $(this);

      if ($list.data('foldery-sortable-ready') || !$.fn.sortable) {
        return;
      }

      $list.sortable({
        axis: 'y',
        handle: '.foldery-atelier-artwork-handle',
        items: '.foldery-atelier-artwork-item',
        tolerance: 'pointer',
        update: function() {
          syncArtworkField($list.closest('.foldery-atelier-media-field'));
        }
      });
      $list.data('foldery-sortable-ready', true);
    });

    $('.foldery-atelier-decor-list').each(function() {
      var $list = $(this);

      if ($list.data('foldery-sortable-ready') || !$.fn.sortable) {
        return;
      }

      $list.sortable({
        axis: 'y',
        handle: '.foldery-atelier-decor-handle',
        items: '.foldery-atelier-decor-item',
        tolerance: 'pointer',
        update: function() {
          syncDecorField($list.closest('.foldery-atelier-media-field'));
        }
      });
      $list.data('foldery-sortable-ready', true);
    });
  }

  $(document).on('click', '.foldery-atelier-choose-media', function() {
    var $button = $(this);
    var $field = $button.closest('.foldery-atelier-media-field');
    var fieldType = $field.data('foldery-media-field');
    var isMultiple = fieldType === 'multiple' || fieldType === 'decor';
    var $input = $field.find('input[type="hidden"]');
    var currentIds = fieldType === 'decor'
      ? parseDecorations($input.val()).map(function(item) { return item.id; })
      : fieldType === 'multiple'
      ? parseArtworks($input.val()).map(function(item) { return item.id; })
      : ($input.val() || '').split(',').map(function(id) { return parseInt(id, 10); }).filter(Boolean);
    var frame = wp.media({
      title: $button.data('title') || 'Choisir une image',
      button: {
        text: $button.data('button') || 'Utiliser'
      },
      library: {
        type: 'image'
      },
      multiple: isMultiple ? 'toggle' : false
    });

    frame.on('open', function() {
      var selection = frame.state().get('selection');

      currentIds.forEach(function(id) {
        var attachment = wp.media.attachment(id);
        attachment.fetch();
        selection.add(attachment);
      });
    });

    frame.on('select', function() {
      var selection = frame.state().get('selection');
      var ids = mediaIds(selection);

      if (!isMultiple) {
        ids = ids.slice(0, 1);
        $input.val(ids.join(','));
      }

      updatePreview($field, selection);
      if (!isMultiple) {
        syncMetaField($input);
      }
    });

    frame.open();
  });

  $(document).on('click', '.foldery-atelier-clear-media', function() {
    clearField($(this).closest('.foldery-atelier-media-field'));
  });

  $(document).on('input change', '.foldery-atelier-artwork-scale, .foldery-atelier-artwork-tapes', function() {
    syncArtworkField($(this).closest('.foldery-atelier-media-field'));
  });

  $(document).on('input change', '.foldery-atelier-decor-x, .foldery-atelier-decor-y, .foldery-atelier-decor-width, .foldery-atelier-decor-scale, .foldery-atelier-decor-rotate, .foldery-atelier-decor-alpha', function() {
    syncDecorField($(this).closest('.foldery-atelier-media-field'));
  });

  $(document).on('click', '.foldery-atelier-artwork-remove', function(event) {
    event.preventDefault();
    event.stopPropagation();

    var $field = $(this).closest('.foldery-atelier-media-field');
    $(this).closest('.foldery-atelier-artwork-item').remove();
    syncArtworkField($field);
  });

  $(document).on('click', '.foldery-atelier-decor-remove', function(event) {
    event.preventDefault();
    event.stopPropagation();

    var $field = $(this).closest('.foldery-atelier-media-field');
    $(this).closest('.foldery-atelier-decor-item').remove();
    syncDecorField($field);
  });

  $(document).on('input change', '.foldery-atelier-percent-field', function() {
    var value = clamp($(this).val(), 0, 100);
    $(this).val(value).closest('label').find('output').text(value + '%');
    syncMetaField($(this));
  });

  $(document).on('input change', '#foldery-atelier-title, #foldery-atelier-subtitle, #foldery-atelier-overlay-color', function() {
    syncMetaField($(this));
  });

  $(function() {
    initSortable();
    $('.foldery-atelier-media-field[data-foldery-media-field="multiple"]').each(function() {
      syncArtworkField($(this), false);
    });
    $('.foldery-atelier-media-field[data-foldery-media-field="decor"]').each(function() {
      syncDecorField($(this), false);
    });

    $('.foldery-atelier-media-field[data-foldery-media-field="decor"]').each(function() {
      initEditorPreview($(this));
    });
  });
}(jQuery));
