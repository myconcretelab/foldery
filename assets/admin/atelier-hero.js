(function($) {
  'use strict';

  function mediaIds(selection) {
    var ids = [];

    selection.each(function(attachment) {
      ids.push(parseInt(attachment.id, 10));
    });

    return ids.filter(Boolean);
  }

  function thumbnail(attachment) {
    var data = attachment.toJSON();
    var sizes = data.sizes || {};
    var url = sizes.thumbnail ? sizes.thumbnail.url : data.url;

    return url ? '<img src="' + url + '" alt="">' : '';
  }

  function updatePreview($field, attachments) {
    var $preview = $field.find('.foldery-atelier-media-preview');
    var html = '';

    attachments.each(function(attachment) {
      html += thumbnail(attachment);
    });

    $preview.html(html);
  }

  function clearField($field) {
    $field.find('input[type="hidden"]').val('');
    $field.find('.foldery-atelier-media-preview').empty();
  }

  $(document).on('click', '.foldery-atelier-choose-media', function() {
    var $button = $(this);
    var $field = $button.closest('.foldery-atelier-media-field');
    var isMultiple = $field.data('foldery-media-field') === 'multiple';
    var $input = $field.find('input[type="hidden"]');
    var currentIds = ($input.val() || '').split(',').map(function(id) {
      return parseInt(id, 10);
    }).filter(Boolean);
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
      }

      $input.val(ids.join(','));
      updatePreview($field, selection);
    });

    frame.open();
  });

  $(document).on('click', '.foldery-atelier-clear-media', function() {
    clearField($(this).closest('.foldery-atelier-media-field'));
  });
}(jQuery));
