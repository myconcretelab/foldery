(function(element, components, i18n) {
  'use strict';

  var el = element.createElement;
  var useState = element.useState;
  var Button = components.Button;
  var CheckboxControl = components.CheckboxControl;
  var Modal = components.Modal;
  var SearchControl = components.SearchControl;
  var TextControl = components.TextControl;
  var __ = i18n.__;

  function parseIds(value) {
    var seen = {};

    return String(value || '')
      .split(',')
      .map(function(id) {
        return parseInt(id, 10);
      })
      .filter(function(id) {
        if (!id || seen[id]) {
          return false;
        }

        seen[id] = true;
        return true;
      });
  }

  function stringifyIds(ids) {
    return (ids || []).map(function(id) {
      return parseInt(id, 10);
    }).filter(Boolean).join(',');
  }

  function flattenFolders(folders, output) {
    output = output || [];

    (folders || []).forEach(function(folder) {
      output.push(folder);
      flattenFolders(folder.children, output);
    });

    return output;
  }

  function folderLabel(folder, preferMenuTitle) {
    if (!folder) {
      return '';
    }

    return preferMenuTitle ? folder.menuTitle || folder.name || ('#' + folder.id) : folder.name || folder.menuTitle || ('#' + folder.id);
  }

  function folderMatches(folder, query) {
    var normalized = String(query || '').trim().toLowerCase();

    if (!normalized) {
      return true;
    }

    if (
      String(folder.name || '').toLowerCase().indexOf(normalized) !== -1 ||
      String(folder.path || '').toLowerCase().indexOf(normalized) !== -1 ||
      String(folder.menuTitle || '').toLowerCase().indexOf(normalized) !== -1 ||
      String(folder.id).indexOf(normalized) !== -1
    ) {
      return true;
    }

    return (folder.children || []).some(function(child) {
      return folderMatches(child, query);
    });
  }

  function selectedSummary(ids) {
    if (!ids.length) {
      return __('Aucun dossier selectionne', 'foldery');
    }

    return ids.length === 1
      ? __('1 dossier selectionne', 'foldery')
      : ids.length + ' ' + __('dossiers selectionnes', 'foldery');
  }

  var currentDragIndex = -1;

  function FolderTreeNode(props) {
    var folder = props.folder;
    var selectedLookup = props.selectedLookup;
    var expanded = props.expanded;
    var toggleExpanded = props.toggleExpanded;
    var toggleFolder = props.toggleFolder;
    var preferMenuTitle = !!props.preferMenuTitle;
    var query = props.query;
    var level = props.level || 0;
    var children = folder.children || [];
    var hasChildren = children.length > 0;
    var isExpanded = !!expanded[folder.id] || !!String(query || '').trim();
    var isSelected = !!selectedLookup[folder.id];
    var visibleChildren = children.filter(function(child) {
      return folderMatches(child, query);
    });

    if (!folderMatches(folder, query)) {
      return null;
    }

    return el(
      'li',
      { className: 'foldery-folder-picker-node' },
      el(
        'div',
        {
          className: 'foldery-folder-picker-row' + (isSelected ? ' is-selected' : ''),
          style: { paddingLeft: (level * 18 + 8) + 'px' }
        },
        el(Button, {
          className: 'foldery-folder-picker-expand',
          icon: hasChildren ? (isExpanded ? 'arrow-down-alt2' : 'arrow-right-alt2') : 'minus',
          label: hasChildren ? __('Afficher les sous-dossiers', 'foldery') : __('Aucun sous-dossier', 'foldery'),
          disabled: !hasChildren,
          onClick: function() {
            toggleExpanded(folder.id);
          }
        }),
        el(CheckboxControl, {
          label: el(
            'span',
            { className: 'foldery-folder-picker-label' },
            el('span', { className: 'foldery-folder-picker-name' }, folderLabel(folder, preferMenuTitle)),
            el('span', { className: 'foldery-folder-picker-meta' }, '#' + folder.id + (folder.count ? ' - ' + folder.count : ''))
          ),
          checked: isSelected,
          onChange: function(checked) {
            toggleFolder(folder.id, checked);
          }
        })
      ),
      hasChildren && isExpanded && visibleChildren.length
        ? el(
            'ul',
            { className: 'foldery-folder-picker-children' },
            visibleChildren.map(function(child) {
              return el(FolderTreeNode, {
                key: child.id,
                folder: child,
                selectedLookup: selectedLookup,
                expanded: expanded,
                toggleExpanded: toggleExpanded,
                toggleFolder: toggleFolder,
                preferMenuTitle: preferMenuTitle,
                query: query,
                level: level + 1
              });
            })
          )
        : null
    );
  }

  function FolderPicker(props) {
    var ids = parseIds(props.value);
    var folders = props.folders || [];
    var flatFolders = flattenFolders(folders);
    var folderById = flatFolders.reduce(function(map, folder) {
      map[folder.id] = folder;
      return map;
    }, {});
    var selectedLookup = ids.reduce(function(map, id) {
      map[id] = true;
      return map;
    }, {});
    var modalState = useState(false);
    var isOpen = modalState[0];
    var setOpen = modalState[1];
    var searchState = useState('');
    var query = searchState[0];
    var setQuery = searchState[1];
    var expandedState = useState(function() {
      return folders.reduce(function(map, folder) {
        map[folder.id] = true;
        return map;
      }, {});
    });
    var expanded = expandedState[0];
    var setExpanded = expandedState[1];
    var dragState = useState({ from: -1, over: -1, after: false });
    var drag = dragState[0];
    var setDrag = dragState[1];
    var summaryLabel = props.summaryLabel || __('Dossiers de la selection', 'foldery');
    var preferMenuTitle = !!props.preferMenuTitle;

    function setIds(nextIds) {
      props.onChange(stringifyIds(nextIds));
    }

    function toggleFolder(id, checked) {
      if (checked) {
        setIds(ids.indexOf(id) === -1 ? ids.concat([id]) : ids);
        return;
      }

      setIds(ids.filter(function(selectedId) {
        return selectedId !== id;
      }));
    }

    function reorderFolder(fromIndex, toIndex) {
      var nextIds = ids.slice();
      var movedId;

      if (fromIndex < 0 || fromIndex >= nextIds.length || toIndex < 0 || toIndex > nextIds.length) {
        return;
      }

      if (fromIndex === toIndex || fromIndex + 1 === toIndex) {
        return;
      }

      movedId = nextIds.splice(fromIndex, 1)[0];
      if (fromIndex < toIndex) {
        toIndex -= 1;
      }
      nextIds.splice(toIndex, 0, movedId);
      setIds(nextIds);
    }

    function dragInsertIndex(event, index) {
      var rect = event.currentTarget.getBoundingClientRect();
      return index + (event.clientX > rect.left + rect.width / 2 ? 1 : 0);
    }

    function toggleExpanded(id) {
      var nextExpanded = {};

      Object.keys(expanded).forEach(function(key) {
        nextExpanded[key] = expanded[key];
      });
      nextExpanded[id] = !nextExpanded[id];
      setExpanded(nextExpanded);
    }

    function clearSelected() {
      setIds([]);
    }

    return el(
      'div',
      { className: 'foldery-folder-picker' },
      el(
        'div',
        { className: 'foldery-folder-picker-summary' },
        el('strong', null, summaryLabel),
        el('span', null, selectedSummary(ids))
      ),
      ids.length
        ? el(
            'div',
            { className: 'foldery-folder-picker-chips' },
            ids.map(function(id, index) {
              var folder = folderById[id];
              var dropAfter = drag.over === index && drag.after;
              var dropBefore = drag.over === index && !drag.after;

              return el(
                'span',
                {
                  key: id,
                  className: 'foldery-folder-picker-chip' +
                    (drag.from === index ? ' is-dragging' : '') +
                    (dropBefore ? ' is-drop-before' : '') +
                    (dropAfter ? ' is-drop-after' : ''),
                  draggable: true,
                  onDragStart: function(event) {
                    currentDragIndex = index;
                    setDrag({ from: index, over: index, after: false });
                    event.dataTransfer.effectAllowed = 'move';
                    event.dataTransfer.setData('text/plain', String(index));
                  },
                  onDragOver: function(event) {
                    var insertIndex = dragInsertIndex(event, index);
                    event.preventDefault();
                    event.dataTransfer.dropEffect = 'move';
                    setDrag({ from: currentDragIndex, over: index, after: insertIndex > index });
                  },
                  onDrop: function(event) {
                    var fromIndex = parseInt(event.dataTransfer.getData('text/plain'), 10);
                    var insertIndex = dragInsertIndex(event, index);
                    event.preventDefault();
                    reorderFolder(isNaN(fromIndex) ? currentDragIndex : fromIndex, insertIndex);
                    currentDragIndex = -1;
                    setDrag({ from: -1, over: -1, after: false });
                  },
                  onDragEnd: function() {
                    currentDragIndex = -1;
                    setDrag({ from: -1, over: -1, after: false });
                  }
                },
                el('span', { className: 'foldery-folder-picker-chip-handle dashicons dashicons-menu', 'aria-hidden': true }),
                el('span', { className: 'foldery-folder-picker-chip-label' }, folder ? folderLabel(folder, preferMenuTitle) : '#' + id),
                el(Button, {
                  className: 'foldery-folder-picker-chip-action foldery-folder-picker-chip-remove',
                  draggable: false,
                  icon: 'no-alt',
                  label: __('Retirer', 'foldery'),
                  onClick: function() {
                    toggleFolder(id, false);
                  }
                })
              );
            })
          )
        : null,
      el(
        'div',
        { className: 'foldery-folder-picker-actions' },
        el(Button, {
          variant: 'primary',
          onClick: function() {
            setOpen(true);
          }
        }, props.buttonLabel || __('Choisir les dossiers', 'foldery')),
        ids.length
          ? el(Button, {
              variant: 'tertiary',
              onClick: clearSelected
            }, __('Vider', 'foldery'))
          : null
      ),
      isOpen
        ? el(
            Modal,
            {
              title: props.modalTitle || __('Selection des dossiers', 'foldery'),
              className: 'foldery-folder-picker-modal',
              onRequestClose: function() {
                setOpen(false);
              }
            },
            el(
              'div',
              { className: 'foldery-folder-picker-modal-header' },
              SearchControl
                ? el(SearchControl, {
                    label: __('Rechercher un dossier', 'foldery'),
                    placeholder: __('Rechercher par nom ou ID', 'foldery'),
                    value: query,
                    onChange: setQuery
                  })
                : el(TextControl, {
                    label: __('Rechercher un dossier', 'foldery'),
                    value: query,
                    onChange: setQuery
                  }),
              el('span', null, selectedSummary(ids))
            ),
            folders.length
              ? el(
                  'div',
                  { className: 'foldery-folder-picker-tree-wrap' },
                  el(
                    'ul',
                    { className: 'foldery-folder-picker-tree' },
                    folders
                      .filter(function(folder) {
                        return folderMatches(folder, query);
                      })
                      .map(function(folder) {
                        return el(FolderTreeNode, {
                          key: folder.id,
                          folder: folder,
                          selectedLookup: selectedLookup,
                          expanded: expanded,
                          toggleExpanded: toggleExpanded,
                          toggleFolder: toggleFolder,
                          preferMenuTitle: preferMenuTitle,
                          query: query
                        });
                      })
                  )
                )
              : el('p', { className: 'foldery-folder-picker-empty' }, __('Aucun dossier disponible.', 'foldery')),
            el(
              'div',
              { className: 'foldery-folder-picker-modal-footer' },
              el(TextControl, {
                label: __('IDs selectionnes', 'foldery'),
                help: __('Option technique: vous pouvez aussi coller une liste separee par des virgules.', 'foldery'),
                value: props.value || '',
                onChange: props.onChange
              }),
              el(Button, {
                variant: 'primary',
                onClick: function() {
                  setOpen(false);
                }
              }, __('Terminer', 'foldery'))
            )
          )
        : null
    );
  }

  window.FolderyFolderPicker = {
    parseIds: parseIds,
    stringifyIds: stringifyIds,
    flattenFolders: flattenFolders,
    FolderPicker: FolderPicker
  };
})(wp.element, wp.components, wp.i18n);
