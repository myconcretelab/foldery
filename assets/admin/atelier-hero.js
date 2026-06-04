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
      thumbnail: sizes.thumbnail ? sizes.thumbnail.url : data.url
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

  function updatePreview($field, attachments) {
    var isMultiple = $field.data('foldery-media-field') === 'multiple';
    var $preview = $field.find('.foldery-atelier-media-preview');
    var html = '';

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
    $field.find('.foldery-atelier-media-preview, .foldery-atelier-artwork-list').empty();
    if ($field.data('foldery-media-field') === 'multiple') {
      syncArtworkField($field);
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
  }

  $(document).on('click', '.foldery-atelier-choose-media', function() {
    var $button = $(this);
    var $field = $button.closest('.foldery-atelier-media-field');
    var isMultiple = $field.data('foldery-media-field') === 'multiple';
    var $input = $field.find('input[type="hidden"]');
    var currentIds = isMultiple
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

  $(document).on('click', '.foldery-atelier-artwork-remove', function(event) {
    event.preventDefault();
    event.stopPropagation();

    var $field = $(this).closest('.foldery-atelier-media-field');
    $(this).closest('.foldery-atelier-artwork-item').remove();
    syncArtworkField($field);
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
  });
}(jQuery));
