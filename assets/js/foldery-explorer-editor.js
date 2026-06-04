(function(blocks, element, components, blockEditor, i18n) {
  'use strict';

  var el = element.createElement;
  var useState = element.useState;
  var InspectorControls = blockEditor.InspectorControls;
  var useBlockProps = blockEditor.useBlockProps;
  var MediaUpload = blockEditor.MediaUpload;
  var MediaUploadCheck = blockEditor.MediaUploadCheck;
  var PanelBody = components.PanelBody;
  var TextControl = components.TextControl;
  var ToggleControl = components.ToggleControl;
  var Disabled = components.Disabled;
  var Button = components.Button;
  var CheckboxControl = components.CheckboxControl;
  var Modal = components.Modal;
  var SearchControl = components.SearchControl;
  var SelectControl = components.SelectControl;
  var ServerSideRender = wp.serverSideRender;
  var __ = i18n.__;
  var editorData = window.FolderyExplorerEditor || {};

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

  function flattenFolders(folders, output) {
    output = output || [];

    (folders || []).forEach(function(folder) {
      output.push(folder);
      flattenFolders(folder.children, output);
    });

    return output;
  }

  function folderMatches(folder, query) {
    var normalized = query.trim().toLowerCase();

    if (!normalized) {
      return true;
    }

    if (
      String(folder.name || '').toLowerCase().indexOf(normalized) !== -1 ||
      String(folder.path || '').toLowerCase().indexOf(normalized) !== -1 ||
      String(folder.id).indexOf(normalized) !== -1
    ) {
      return true;
    }

    return (folder.children || []).some(function(child) {
      return folderMatches(child, query);
    });
  }

  function selectedSummary(ids, folderById) {
    if (!ids.length) {
      return __('Aucun dossier selectionne', 'foldery');
    }

    return ids.length === 1
      ? __('1 dossier selectionne', 'foldery')
      : ids.length + ' ' + __('dossiers selectionnes', 'foldery');
  }

  function FolderTreeNode(props) {
    var folder = props.folder;
    var selectedIds = props.selectedIds;
    var selectedLookup = props.selectedLookup;
    var expanded = props.expanded;
    var toggleExpanded = props.toggleExpanded;
    var toggleFolder = props.toggleFolder;
    var query = props.query;
    var level = props.level || 0;
    var children = folder.children || [];
    var hasChildren = children.length > 0;
    var isExpanded = !!expanded[folder.id] || !!query.trim();
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
        el(
          Button,
          {
            className: 'foldery-folder-picker-expand',
            icon: hasChildren ? (isExpanded ? 'arrow-down-alt2' : 'arrow-right-alt2') : 'minus',
            label: hasChildren ? __('Afficher les sous-dossiers', 'foldery') : __('Aucun sous-dossier', 'foldery'),
            disabled: !hasChildren,
            onClick: function() {
              toggleExpanded(folder.id);
            }
          }
        ),
        el(CheckboxControl, {
          label: el(
            'span',
            { className: 'foldery-folder-picker-label' },
            el('span', { className: 'foldery-folder-picker-name' }, folder.name),
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
                selectedIds: selectedIds,
                selectedLookup: selectedLookup,
                expanded: expanded,
                toggleExpanded: toggleExpanded,
                toggleFolder: toggleFolder,
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
    var folders = editorData.folders || [];
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

    function setIds(nextIds) {
      props.onChange(nextIds.join(','));
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
        el('strong', null, __('Dossiers de la selection', 'foldery')),
        el('span', null, selectedSummary(ids, folderById))
      ),
      ids.length
        ? el(
            'div',
            { className: 'foldery-folder-picker-chips' },
            ids.map(function(id) {
              var folder = folderById[id];

              return el(
                'button',
                {
                  key: id,
                  type: 'button',
                  className: 'foldery-folder-picker-chip',
                  onClick: function() {
                    toggleFolder(id, false);
                  }
                },
                folder ? folder.name : '#' + id,
                el('span', { 'aria-hidden': true }, 'x')
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
        }, __('Choisir les dossiers', 'foldery')),
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
              title: __('Selection des dossiers', 'foldery'),
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
              el('span', null, selectedSummary(ids, folderById))
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
                          selectedIds: ids,
                          selectedLookup: selectedLookup,
                          expanded: expanded,
                          toggleExpanded: toggleExpanded,
                          toggleFolder: toggleFolder,
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
                value: props.value,
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

  function selectedImageSummary(ids) {
    if (!ids.length) {
      return __('Aucune image selectionnee', 'foldery');
    }

    return ids.length === 1
      ? __('1 image selectionnee', 'foldery')
      : ids.length + ' ' + __('images selectionnees', 'foldery');
  }

  function folderSelectOptions() {
    return [
      { label: __('Racine des medias', 'foldery'), value: '-1' }
    ].concat(
      flattenFolders(editorData.folders || []).map(function(folder) {
        return {
          label: folder.path || folder.name || ('#' + folder.id),
          value: String(folder.id)
        };
      })
    );
  }

  function normalizeMediaIds(media) {
    return (Array.isArray(media) ? media : [media])
      .map(function(item) {
        return item && item.id ? parseInt(item.id, 10) : 0;
      })
      .filter(function(id) {
        return !!id;
      });
  }

  function ImagePicker(props) {
    var ids = parseIds(props.value);

    function setIds(nextIds) {
      props.onChange(nextIds.join(','));
    }

    function removeImage(id) {
      setIds(ids.filter(function(selectedId) {
        return selectedId !== id;
      }));
    }

    function clearImages() {
      setIds([]);
    }

    if (!MediaUpload || !MediaUploadCheck) {
      return el(TextControl, {
        label: __('IDs des images selectionnees', 'foldery'),
        help: __('Liste separee par des virgules.', 'foldery'),
        value: props.value,
        onChange: props.onChange
      });
    }

    return el(
      'div',
      { className: 'foldery-image-picker' },
      el(
        'div',
        { className: 'foldery-image-picker-summary' },
        el('strong', null, __('Images affichees', 'foldery')),
        el('span', null, selectedImageSummary(ids))
      ),
      ids.length
        ? el(
            'div',
            { className: 'foldery-image-picker-chips' },
            ids.map(function(id) {
              return el(
                'button',
                {
                  key: id,
                  type: 'button',
                  className: 'foldery-image-picker-chip',
                  onClick: function() {
                    removeImage(id);
                  }
                },
                '#' + id,
                el('span', { 'aria-hidden': true }, 'x')
              );
            })
          )
        : null,
      el(
        'div',
        { className: 'foldery-image-picker-actions' },
        el(
          MediaUploadCheck,
          null,
          el(MediaUpload, {
            allowedTypes: ['image'],
            gallery: true,
            multiple: true,
            value: ids,
            onSelect: function(media) {
              setIds(normalizeMediaIds(media));
            },
            render: function(renderProps) {
              return el(Button, {
                variant: 'primary',
                onClick: renderProps.open
              }, ids.length ? __('Modifier les images', 'foldery') : __('Choisir les images', 'foldery'));
            }
          })
        ),
        ids.length
          ? el(Button, {
              variant: 'tertiary',
              onClick: clearImages
            }, __('Vider', 'foldery'))
          : null
      )
    );
  }

  blocks.registerBlockType('foldery/explorer', {
    apiVersion: 3,
    title: __('Foldery Explorer', 'foldery'),
    icon: 'images-alt2',
    category: 'widgets',
    attributes: {
      showHomeSelection: { type: 'boolean', default: true },
      homeFolderIds: { type: 'string', default: '45,49,56,38,2,12,1' },
      homeTitle: { type: 'string', default: 'Series en cours...' },
      showRecent: { type: 'boolean', default: true },
      recentTitle: { type: 'string', default: "Recemment cree a l'atelier (ou dehors !)" },
      recentImageIds: { type: 'string', default: '' },
      includePageContent: { type: 'boolean', default: true },
      pageContentLayout: { type: 'string', default: 'stacked' },
      animate: { type: 'boolean', default: true }
    },
    edit: function(props) {
      var attrs = props.attributes;
      var setAttributes = props.setAttributes;
      var blockProps = useBlockProps ? useBlockProps({ className: 'foldery-explorer-editor-preview' }) : { className: 'foldery-explorer-editor-preview' };

      return el(
        element.Fragment,
        null,
        el(
          InspectorControls,
          null,
          el(
            PanelBody,
            { title: __('Accueil', 'foldery'), initialOpen: true },
            el(ToggleControl, {
              label: __('Afficher la selection de la page d’accueil', 'foldery'),
              checked: attrs.showHomeSelection,
              onChange: function(value) { setAttributes({ showHomeSelection: value }); }
            }),
            el(TextControl, {
              label: __('Titre de la selection', 'foldery'),
              value: attrs.homeTitle,
              onChange: function(value) { setAttributes({ homeTitle: value }); }
            }),
            el(FolderPicker, {
              value: attrs.homeFolderIds,
              onChange: function(value) { setAttributes({ homeFolderIds: value }); }
            }),
            el(ToggleControl, {
              label: __('Afficher les creations recentes', 'foldery'),
              checked: attrs.showRecent,
              onChange: function(value) { setAttributes({ showRecent: value }); }
            }),
            el(TextControl, {
              label: __('Titre des creations recentes', 'foldery'),
              value: attrs.recentTitle,
              onChange: function(value) { setAttributes({ recentTitle: value }); }
            }),
            el(ImagePicker, {
              value: attrs.recentImageIds,
              onChange: function(value) { setAttributes({ recentImageIds: value }); }
            })
          ),
          el(
            PanelBody,
            { title: __('Navigation', 'foldery'), initialOpen: false },
            el(ToggleControl, {
              label: __('Afficher le contenu de page associe au dossier', 'foldery'),
              checked: attrs.includePageContent,
              onChange: function(value) { setAttributes({ includePageContent: value }); }
            }),
            attrs.includePageContent && SelectControl
              ? el(SelectControl, {
                  label: __('Presentation du contenu de page', 'foldery'),
                  value: attrs.pageContentLayout || 'stacked',
                  options: [
                    { label: __('Au-dessus des galeries', 'foldery'), value: 'stacked' },
                    { label: __('Deux colonnes papier', 'foldery'), value: 'split' }
                  ],
                  onChange: function(value) { setAttributes({ pageContentLayout: value }); }
                })
              : null,
            el(ToggleControl, {
              label: __('Animations desktop', 'foldery'),
              checked: attrs.animate,
              onChange: function(value) { setAttributes({ animate: value }); }
            })
          )
        ),
        el(
          'div',
          blockProps,
          el(
            Disabled,
            null,
            el(ServerSideRender, {
              block: 'foldery/explorer',
              attributes: attrs
            })
          )
        )
      );
    },
    save: function() {
      return null;
    }
  });

  blocks.registerBlockType('foldery/explorer-menu', {
    apiVersion: 3,
    title: __('Foldery Explorer Menu', 'foldery'),
    icon: 'menu',
    category: 'widgets',
    attributes: {
      rootFolderId: { type: 'number', default: -1 },
      folderIds: { type: 'string', default: '' },
      maxDepth: { type: 'number', default: 0 },
      showSubmenus: { type: 'boolean', default: true },
      includeEmpty: { type: 'boolean', default: true },
      ariaLabel: { type: 'string', default: 'Explorer' },
      scrollToExplorer: { type: 'boolean', default: false }
    },
    edit: function(props) {
      var attrs = props.attributes;
      var setAttributes = props.setAttributes;
      var blockProps = useBlockProps ? useBlockProps({ className: 'foldery-explorer-menu-editor-preview' }) : { className: 'foldery-explorer-menu-editor-preview' };

      return el(
        element.Fragment,
        null,
        el(
          InspectorControls,
          null,
          el(
            PanelBody,
            { title: __('Source du menu', 'foldery'), initialOpen: true },
            SelectControl
              ? el(SelectControl, {
                  label: __('Dossier parent', 'foldery'),
                  help: attrs.folderIds ? __('Ignore si une selection de dossiers est definie.', 'foldery') : null,
                  value: String(attrs.rootFolderId),
                  options: folderSelectOptions(),
                  onChange: function(value) {
                    setAttributes({ rootFolderId: parseInt(value, 10) });
                  }
                })
              : el(TextControl, {
                  label: __('ID du dossier parent', 'foldery'),
                  value: String(attrs.rootFolderId),
                  onChange: function(value) {
                    setAttributes({ rootFolderId: parseInt(value, 10) || -1 });
                  }
                }),
            el(FolderPicker, {
              value: attrs.folderIds || '',
              onChange: function(value) {
                setAttributes({ folderIds: value });
              }
            }),
            el(TextControl, {
              label: __('Profondeur maximale', 'foldery'),
              help: __('0 affiche toute la hierarchie.', 'foldery'),
              disabled: !attrs.showSubmenus,
              value: String(attrs.maxDepth),
              onChange: function(value) {
                setAttributes({ maxDepth: Math.max(0, parseInt(value, 10) || 0) });
              }
            }),
            el(ToggleControl, {
              label: __('Afficher les sous-menus', 'foldery'),
              checked: attrs.showSubmenus,
              onChange: function(value) {
                setAttributes({ showSubmenus: value });
              }
            }),
            el(ToggleControl, {
              label: __('Afficher les dossiers vides', 'foldery'),
              checked: attrs.includeEmpty,
              onChange: function(value) {
                setAttributes({ includeEmpty: value });
              }
            }),
            el(ToggleControl, {
              label: __('Scroller vers Explorer au clic', 'foldery'),
              checked: attrs.scrollToExplorer,
              onChange: function(value) {
                setAttributes({ scrollToExplorer: value });
              }
            }),
            el(TextControl, {
              label: __('Libelle ARIA', 'foldery'),
              value: attrs.ariaLabel,
              onChange: function(value) {
                setAttributes({ ariaLabel: value });
              }
            })
          )
        ),
        el(
          'div',
          blockProps,
          el(
            Disabled,
            null,
            el(ServerSideRender, {
              block: 'foldery/explorer-menu',
              attributes: attrs
            })
          )
        )
      );
    },
    save: function() {
      return null;
    }
  });

  blocks.registerBlockType('foldery/explorer-page-content', {
    apiVersion: 3,
    title: __('Foldery Explorer Page Content', 'foldery'),
    icon: 'media-document',
    category: 'widgets',
    edit: function() {
      var blockProps = useBlockProps ? useBlockProps({ className: 'foldery-explorer-page-content-editor-preview' }) : { className: 'foldery-explorer-page-content-editor-preview' };

      return el(
        'div',
        blockProps,
        el(
          Disabled,
          null,
          el(ServerSideRender, {
            block: 'foldery/explorer-page-content'
          })
        )
      );
    },
    save: function() {
      return null;
    }
  });
}(wp.blocks, wp.element, wp.components, wp.blockEditor, wp.i18n));
