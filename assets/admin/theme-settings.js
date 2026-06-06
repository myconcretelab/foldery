(function(wp) {
  'use strict';

  var el = wp.element.createElement;
  var useState = wp.element.useState;
  var FolderPicker = window.FolderyFolderPicker && window.FolderyFolderPicker.FolderPicker;
  var config = window.FolderyThemeSettings || {};
  var target = document.getElementById('foldery-header-menu-folder-picker');

  if (!target || !FolderPicker) {
    return;
  }

  function HeaderMenuFolderPicker() {
    var input = document.getElementById(target.dataset.inputId);
    var state = useState(input ? input.value : '');
    var value = state[0];
    var setValue = state[1];

    if (!input) {
      return null;
    }

    input.classList.add('foldery-folder-picker-input--enhanced');

    function onChange(nextValue) {
      setValue(nextValue);
      input.value = nextValue;
      input.dispatchEvent(new Event('change', { bubbles: true }));
    }

    return el(FolderPicker, {
      summaryLabel: wp.i18n.__('Dossiers du menu du header', 'foldery'),
      modalTitle: wp.i18n.__('Selection des dossiers du menu', 'foldery'),
      buttonLabel: wp.i18n.__('Choisir les dossiers du menu', 'foldery'),
      folders: config.folders || [],
      preferMenuTitle: true,
      value: value,
      onChange: onChange
    });
  }

  if (wp.element.createRoot) {
    wp.element.createRoot(target).render(el(HeaderMenuFolderPicker));
    return;
  }

  wp.element.render(el(HeaderMenuFolderPicker), target);
})(wp);
