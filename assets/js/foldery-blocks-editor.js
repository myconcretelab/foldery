(function(blocks, element, components, blockEditor, data, i18n) {
  'use strict';

  var el = element.createElement;
  var Fragment = element.Fragment;
  var useMemo = element.useMemo;
  var InspectorControls = blockEditor.InspectorControls;
  var MediaUpload = blockEditor.MediaUpload;
  var MediaUploadCheck = blockEditor.MediaUploadCheck;
  var useBlockProps = blockEditor.useBlockProps;
  var Button = components.Button;
  var PanelBody = components.PanelBody;
  var Placeholder = components.Placeholder;
  var TextControl = components.TextControl;
  var TextareaControl = components.TextareaControl;
  var ToggleControl = components.ToggleControl;
  var useSelect = data.useSelect;
  var useDispatch = data.useDispatch;
  var __ = i18n.__;
  var editorData = window.FolderyBlocksEditor || {};
  var atelierMetaKeys = editorData.atelierMetaKeys || {};
  var siteHeaderData = editorData.siteHeader || {};
  var atelierMetaInputs = {};

  atelierMetaInputs[atelierMetaKeys.heroImage] = 'foldery_atelier_hero_image_id';
  atelierMetaInputs[atelierMetaKeys.title] = 'foldery_atelier_title';
  atelierMetaInputs[atelierMetaKeys.subtitle] = 'foldery_atelier_subtitle';
  atelierMetaInputs[atelierMetaKeys.artworks] = 'foldery_atelier_artwork_ids';

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

  function normalizeArtworkItem(item) {
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
      scale: Math.min(145, Math.max(55, parseInt(item.scale || 100, 10))),
      tapes: Math.min(5, Math.max(0, parseInt(item.tapes === 0 ? 0 : item.tapes || 1, 10)))
    };
  }

  function parseArtworkItems(value) {
    var raw;
    var seen = {};

    value = String(value || '').trim();
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

    return (raw || []).map(normalizeArtworkItem).filter(function(item) {
      if (!item || seen[item.id]) {
        return false;
      }

      seen[item.id] = true;
      return true;
    });
  }

  function stringifyArtworkItems(items) {
    return JSON.stringify((items || []).map(normalizeArtworkItem).filter(Boolean));
  }

  function flattenFolders(folders, output) {
    output = output || [];

    (folders || []).forEach(function(folder) {
      output.push(folder);
      flattenFolders(folder.children, output);
    });

    return output;
  }

  function parseSocialLinks(value) {
    return String(value || '')
      .split(/\r\n|\r|\n/)
      .map(function(line) {
        var parts;

        line = line.trim();
        if (!line) {
          return null;
        }

        parts = line.split('|');
        return {
          label: (parts[0] || '').trim(),
          url: (parts.slice(1).join('|') || '').trim()
        };
      })
      .filter(Boolean);
  }

  function siteHeaderValue(attributes, attributeKey, settingsKey) {
    if (Object.prototype.hasOwnProperty.call(attributes, attributeKey)) {
      return attributes[attributeKey] || '';
    }

    return siteHeaderData.settings && siteHeaderData.settings[settingsKey] ? siteHeaderData.settings[settingsKey] : '';
  }

  function phoneHref(value) {
    var clean = String(value || '').replace(/[^0-9+]/g, '');
    return clean ? 'tel:' + clean : '';
  }

  function linkOrText(label, url, className) {
    label = String(label || '');
    url = String(url || '');

    if (!label) {
      return null;
    }

    if (url) {
      return el('a', { className: className, href: url, onClick: preventDefault }, label);
    }

    return el('span', { className: className }, label);
  }

  function preventDefault(event) {
    event.preventDefault();
  }

  function postTitleText(title) {
    if (!title) {
      return '';
    }

    if (typeof title === 'string') {
      return title;
    }

    return title.raw || title.rendered || '';
  }

  function mediaImageUrl(media, preferredSize) {
    var sizes = media && media.media_details ? media.media_details.sizes || {} : {};

    if (preferredSize && sizes[preferredSize] && sizes[preferredSize].source_url) {
      return sizes[preferredSize].source_url;
    }

    if (sizes.medium_large && sizes.medium_large.source_url) {
      return sizes.medium_large.source_url;
    }

    if (sizes.large && sizes.large.source_url) {
      return sizes.large.source_url;
    }

    if (sizes.medium && sizes.medium.source_url) {
      return sizes.medium.source_url;
    }

    return media && media.source_url ? media.source_url : '';
  }

  function syncLegacyMetaInput(key, value) {
    var inputName = atelierMetaInputs[key];
    var input = inputName ? document.querySelector('[name="' + inputName + '"]') : null;

    if (!input) {
      return;
    }

    input.value = value || '';
  }

  function editorTapeStyle(id, index, tapeIndex, count) {
    var positions = [
      { left: '18%', top: '-14px', rotate: -8, axis: 'x' },
      { left: '50%', top: '-14px', rotate: 3, axis: 'x' },
      { left: '82%', top: '-14px', rotate: 8, axis: 'x' },
      { right: '-34px', top: '22%', rotate: 84, axis: 'y' },
      { right: '-34px', top: '50%', rotate: 92, axis: 'y' },
      { right: '-34px', top: '78%', rotate: 99, axis: 'y' },
      { left: '82%', bottom: '-14px', rotate: -6, axis: 'x' },
      { left: '50%', bottom: '-14px', rotate: 3, axis: 'x' },
      { left: '18%', bottom: '-14px', rotate: 7, axis: 'x' },
      { left: '-34px', top: '78%', rotate: -96, axis: 'y' },
      { left: '-34px', top: '50%', rotate: -88, axis: 'y' },
      { left: '-34px', top: '22%', rotate: -82, axis: 'y' }
    ];
    var seed = Math.abs((id * 1103515245 + index * 12345) | 0);
    var jitterSeed = Math.abs((id * 1103515245 + index * 12345 + tapeIndex * 2654435761) | 0);
    var step = positions.length / Math.max(1, count);
    var position = positions[(seed % positions.length + Math.round(tapeIndex * step)) % positions.length];
    var style = {};
    var jitter = -4 + jitterSeed % 9;
    var axis = position.axis === 'x' ? 'translateX(-50%)' : 'translateY(-50%)';

    Object.keys(position).forEach(function(key) {
      if (key !== 'rotate' && key !== 'axis') {
        style[key] = position[key];
      }
    });

    style.transform = axis + ' rotate(' + (position.rotate + jitter) + 'deg)';
    return style;
  }

  function renderEditorTapes(artwork, index) {
    var tapes = [];
    var count = Math.min(5, Math.max(0, parseInt(artwork.tapes || 0, 10)));
    var tapeIndex;

    for (tapeIndex = 0; tapeIndex < count; tapeIndex++) {
      tapes.push(el('span', {
        key: 'tape-' + tapeIndex,
        className: 'atelier-hero-artwork__tape',
        style: editorTapeStyle(artwork.id, index, tapeIndex, count),
        'aria-hidden': true
      }));
    }

    return tapes;
  }

  function folderMapById(folders) {
    return flattenFolders(folders).reduce(function(map, folder) {
      map[folder.id] = folder;
      return map;
    }, {});
  }

  function renderHeaderMenuItems(folders, showSubmenus, level) {
    level = level || 1;

    return (folders || []).map(function(folder) {
      var children = showSubmenus ? folder.children || [] : [];
      var className = children.length ? 'menu-item menu-item-has-children' : 'menu-item';

      return el(
        'li',
        { key: folder.id, className: className },
        el('a', { href: '#folder-' + folder.id, onClick: preventDefault }, folder.menuTitle || folder.name),
        children.length
          ? el(
              'ul',
              { className: 'sub-menu foldery-explorer-submenu' },
              renderHeaderMenuItems(children, showSubmenus, level + 1)
            )
          : null
      );
    });
  }

  function SiteHeaderPreview(props) {
    var attributes = props.attributes;
    var folders = siteHeaderData.folders || [];
    var folderById = folderMapById(folders);
    var selectedFolders = parseIds(attributes.menuFolderIds).map(function(id) {
      return folderById[id];
    }).filter(Boolean);
    var menuFolders = selectedFolders.length ? selectedFolders : folders;
    var artistName = siteHeaderValue(attributes, 'artistName', 'artist_name');
    var artistBaseline = siteHeaderValue(attributes, 'artistBaseline', 'artist_baseline');
    var phone = siteHeaderValue(attributes, 'phone', 'phone');
    var email = siteHeaderValue(attributes, 'email', 'email');
    var socialLinks = parseSocialLinks(siteHeaderValue(attributes, 'socialLinks', 'social_links'));
    var actionLabel = siteHeaderValue(attributes, 'actionLabel', 'header_link_label');
    var actionUrl = siteHeaderValue(attributes, 'actionUrl', 'header_link_url');
    var classes = ['foldery-paper-header'];

    if (attributes.className) {
      classes = classes.concat(String(attributes.className).split(/\s+/).filter(Boolean));
    }

    return el(
      'header',
      {
        className: classes.join(' '),
        'data-foldery-paper-header': true
      },
      el(
        'div',
        { className: 'foldery-paper-header__paper' },
        el(
          'a',
          {
            className: 'foldery-paper-header__logo',
            href: siteHeaderData.homeUrl || '#',
            'aria-label': siteHeaderData.siteName || '',
            onClick: preventDefault
          },
          siteHeaderData.logoUrl
            ? el('img', { className: 'foldery-paper-header__logo-image', src: siteHeaderData.logoUrl, alt: siteHeaderData.siteName || '' })
            : null
        ),
        el(
          'div',
          { className: 'foldery-paper-header__content' },
          el(
            'section',
            { className: 'foldery-paper-header__column foldery-paper-header__column--artist', 'aria-label': __('Artiste', 'foldery') },
            artistName ? el('h2', null, artistName) : null,
            artistBaseline ? el('p', null, artistBaseline) : null,
            socialLinks.map(function(link, index) {
              return el('p', { key: index }, linkOrText(link.label, link.url, 'foldery-paper-header__social-link'));
            })
          ),
          el(
            'section',
            { className: 'foldery-paper-header__column foldery-paper-header__column--contact', 'aria-label': __('Contact', 'foldery') },
            el('h2', null, __('CONTACT', 'foldery')),
            phone ? el('p', null, el('a', { href: phoneHref(phone), onClick: preventDefault }, phone)) : null,
            email ? el('p', null, el('a', { href: 'mailto:' + email, onClick: preventDefault }, email)) : null
          ),
          el(
            'section',
            { className: 'foldery-paper-header__column foldery-paper-header__column--action', 'aria-label': __('Lien principal', 'foldery') },
            linkOrText(actionLabel, actionUrl, 'foldery-paper-header__action-link')
          )
        )
      ),
      menuFolders.length
        ? el(
            'nav',
            { className: 'foldery-paper-header__menu', 'aria-label': attributes.ariaLabel || __('Menu principal', 'foldery') },
            el('ul', { className: 'foldery-explorer-menu-list' }, renderHeaderMenuItems(menuFolders, attributes.showSubmenus !== false))
          )
        : null
    );
  }

  function useAtelierMeta() {
    var editor = useSelect(function(select) {
      var coreEditor = select('core/editor');
      var core = select('core');
      var meta = coreEditor.getEditedPostAttribute('meta') || {};
      var title = coreEditor.getEditedPostAttribute('title');
      var heroImageId = parseInt(meta[atelierMetaKeys.heroImage] || 0, 10);
      var artworkItems = parseArtworkItems(meta[atelierMetaKeys.artworks]);
      var artworkIds = artworkItems.map(function(item) {
        return item.id;
      });
      var mediaIds = [heroImageId].concat(artworkIds);
      var mediaById = {};

      mediaIds.forEach(function(id) {
        if (id) {
          mediaById[id] = core.getMedia(id);
        }
      });

      return {
        meta: meta,
        postTitle: postTitleText(title),
        heroImageId: heroImageId,
        artworkItems: artworkItems,
        artworkIds: artworkIds,
        mediaById: mediaById
      };
    }, []);
    var dispatch = useDispatch('core/editor');

    function setMetaValue(key, value) {
      var meta = {};
      meta[key] = value;
      dispatch.editPost({ meta: meta });
      syncLegacyMetaInput(key, value);
    }

    return {
      meta: editor.meta,
      postTitle: editor.postTitle,
      heroImageId: editor.heroImageId,
      artworkItems: editor.artworkItems,
      artworkIds: editor.artworkIds,
      mediaById: editor.mediaById,
      setMetaValue: setMetaValue
    };
  }

  function AtelierImageControl(props) {
    return el(
      'div',
      { className: 'foldery-editor-media-control' },
      el('p', { className: 'foldery-editor-media-control__label' }, props.label),
      props.previewUrl
        ? el('img', { className: 'foldery-editor-media-control__preview', src: props.previewUrl, alt: '' })
        : null,
      el(
        MediaUploadCheck,
        null,
        el(MediaUpload, {
          allowedTypes: ['image'],
          multiple: !!props.multiple,
          gallery: !!props.multiple,
          value: props.value,
          onSelect: props.onSelect,
          render: function(renderProps) {
            return el(
              Button,
              { variant: 'secondary', onClick: renderProps.open },
              props.buttonLabel
            );
          }
        })
      ),
      props.hasValue
        ? el(
            Button,
            { variant: 'link', isDestructive: true, onClick: props.onClear },
            __('Retirer', 'foldery')
          )
        : null
    );
  }

  function AtelierHeroControls(props) {
    var meta = props.atelier.meta;
    var heroMedia = props.atelier.mediaById[props.atelier.heroImageId];
    var heroPreviewUrl = mediaImageUrl(heroMedia, 'thumbnail');

    return el(
      InspectorControls,
      null,
      el(
        PanelBody,
        { title: __('Heros atelier', 'foldery'), initialOpen: true },
        el(TextControl, {
          label: __('Titre du heros', 'foldery'),
          value: meta[atelierMetaKeys.title] || '',
          placeholder: props.atelier.postTitle,
          onChange: function(value) {
            props.atelier.setMetaValue(atelierMetaKeys.title, value);
          }
        }),
        el(TextareaControl, {
          label: __('Sous-titre', 'foldery'),
          value: meta[atelierMetaKeys.subtitle] || '',
          rows: 3,
          onChange: function(value) {
            props.atelier.setMetaValue(atelierMetaKeys.subtitle, value);
          }
        }),
        el(AtelierImageControl, {
          label: __('Image du bureau', 'foldery'),
          buttonLabel: heroPreviewUrl ? __('Changer l image', 'foldery') : __('Choisir une image', 'foldery'),
          value: props.atelier.heroImageId,
          previewUrl: heroPreviewUrl,
          hasValue: !!props.atelier.heroImageId,
          onSelect: function(media) {
            props.atelier.setMetaValue(atelierMetaKeys.heroImage, media && media.id ? String(media.id) : '');
          },
          onClear: function() {
            props.atelier.setMetaValue(atelierMetaKeys.heroImage, '');
          }
        }),
        el(AtelierImageControl, {
          label: __('Oeuvres posees sur l image', 'foldery'),
          buttonLabel: props.atelier.artworkIds.length ? __('Changer les oeuvres', 'foldery') : __('Choisir les oeuvres', 'foldery'),
          multiple: true,
          value: props.atelier.artworkIds,
          hasValue: !!props.atelier.artworkIds.length,
          onSelect: function(media) {
            var selection = Array.isArray(media) ? media : [media];
            var existingById = props.atelier.artworkItems.reduce(function(map, item) {
              map[item.id] = item;
              return map;
            }, {});

            props.atelier.setMetaValue(
              atelierMetaKeys.artworks,
              stringifyArtworkItems(selection.map(function(item) {
                var id = item && item.id;
                return existingById[id] || { id: id, scale: 100, tapes: 1 };
              }))
            );
          },
          onClear: function() {
            props.atelier.setMetaValue(atelierMetaKeys.artworks, '');
          }
        })
      )
    );
  }

  function AtelierHeroPreview(props) {
    var meta = props.atelier.meta;
    var heroMedia = props.atelier.mediaById[props.atelier.heroImageId];
    var heroUrl = mediaImageUrl(heroMedia, 'full') || editorData.fallbackHeroImageUrl || '';
    var title = String(meta[atelierMetaKeys.title] || '').trim();
    var subtitle = String(meta[atelierMetaKeys.subtitle] || '').trim();
    var artworks = props.atelier.artworkItems.slice(0, 6);

    return el(
      'section',
      {
        className: 'atelier-hero',
        style: { '--atelier-hero-image': 'url("' + heroUrl + '")' },
        'aria-label': __('Atelier', 'foldery')
      },
      el('div', { className: 'atelier-hero__shade' }),
      el(
        'div',
        { className: 'atelier-hero__inner' },
        el(
          'div',
          { className: 'atelier-hero__copy' },
          title ? el('h1', null, title) : null,
          subtitle ? el('p', null, subtitle) : null
        ),
        artworks.length
          ? el(
              'div',
              { className: 'atelier-hero__artworks' },
              artworks.map(function(artwork, index) {
                var media = props.atelier.mediaById[artwork.id];
                var imageUrl = mediaImageUrl(media, 'medium_large');
                var fullUrl = mediaImageUrl(media, 'full') || imageUrl;
                var style = { '--atelier-artwork-scale': String((artwork.scale || 100) / 100) };

                if (!imageUrl) {
                  return null;
                }

                return el(
                  'figure',
                  { key: artwork.id + '-' + index, className: 'atelier-hero-artwork atelier-hero-artwork--' + (index + 1), style: style },
                  renderEditorTapes(artwork, index),
                  el(
                    'a',
                    {
                      href: fullUrl,
                      onClick: function(event) {
                        event.preventDefault();
                      }
                    },
                    el('img', { className: 'atelier-hero-artwork__image', src: imageUrl, alt: '' })
                  )
                );
              })
            )
          : null
      )
    );
  }

  blocks.registerBlockType('foldery/site-header', {
    apiVersion: 3,
    title: __('Foldery Site Header', 'foldery'),
    icon: 'menu-alt3',
    category: 'theme',
    attributes: {
      menuFolderIds: { type: 'string', default: '' },
      showSubmenus: { type: 'boolean', default: true },
      ariaLabel: { type: 'string', default: 'Menu principal' },
      className: { type: 'string' },
      artistName: { type: 'string' },
      artistBaseline: { type: 'string' },
      phone: { type: 'string' },
      email: { type: 'string' },
      socialLinks: { type: 'string' },
      actionLabel: { type: 'string' },
      actionUrl: { type: 'string' }
    },
    edit: function(props) {
      var blockProps = useBlockProps({ className: 'foldery-site-header-editor' });

      return el(
        Fragment,
        null,
        el(
          InspectorControls,
          null,
          el(
            PanelBody,
            { title: __('Contenu du header', 'foldery'), initialOpen: true },
            el(TextControl, {
              label: __('Nom de l artiste', 'foldery'),
              value: siteHeaderValue(props.attributes, 'artistName', 'artist_name'),
              onChange: function(value) {
                props.setAttributes({ artistName: value });
              }
            }),
            el(TextControl, {
              label: __('Description courte', 'foldery'),
              value: siteHeaderValue(props.attributes, 'artistBaseline', 'artist_baseline'),
              onChange: function(value) {
                props.setAttributes({ artistBaseline: value });
              }
            }),
            el(TextControl, {
              label: __('Telephone', 'foldery'),
              value: siteHeaderValue(props.attributes, 'phone', 'phone'),
              onChange: function(value) {
                props.setAttributes({ phone: value });
              }
            }),
            el(TextControl, {
              label: __('Email', 'foldery'),
              value: siteHeaderValue(props.attributes, 'email', 'email'),
              onChange: function(value) {
                props.setAttributes({ email: value });
              }
            }),
            el(TextareaControl, {
              label: __('Reseaux sociaux', 'foldery'),
              value: siteHeaderValue(props.attributes, 'socialLinks', 'social_links'),
              rows: 3,
              onChange: function(value) {
                props.setAttributes({ socialLinks: value });
              }
            }),
            el(TextControl, {
              label: __('Libelle du lien principal', 'foldery'),
              value: siteHeaderValue(props.attributes, 'actionLabel', 'header_link_label'),
              onChange: function(value) {
                props.setAttributes({ actionLabel: value });
              }
            }),
            el(TextControl, {
              label: __('URL du lien principal', 'foldery'),
              value: siteHeaderValue(props.attributes, 'actionUrl', 'header_link_url'),
              onChange: function(value) {
                props.setAttributes({ actionUrl: value });
              }
            })
          ),
          el(
            PanelBody,
            { title: __('Menu', 'foldery'), initialOpen: false },
            el(TextControl, {
              label: __('Dossiers du menu', 'foldery'),
              value: props.attributes.menuFolderIds || '',
              onChange: function(value) {
                props.setAttributes({ menuFolderIds: value });
              }
            }),
            el(ToggleControl, {
              label: __('Afficher les sous-menus', 'foldery'),
              checked: props.attributes.showSubmenus !== false,
              onChange: function(value) {
                props.setAttributes({ showSubmenus: !!value });
              }
            }),
            el(TextControl, {
              label: __('Libelle ARIA', 'foldery'),
              value: props.attributes.ariaLabel || '',
              onChange: function(value) {
                props.setAttributes({ ariaLabel: value });
              }
            }),
            siteHeaderData.settingsUrl
              ? el(
                  Button,
                  {
                    variant: 'link',
                    href: siteHeaderData.settingsUrl,
                    target: '_blank'
                  },
                  __('Modifier les textes globaux', 'foldery')
                )
              : null
          )
        ),
        el(
          'div',
          blockProps,
          el(SiteHeaderPreview, {
            attributes: props.attributes
          })
        )
      );
    },
    save: function() {
      return null;
    }
  });

  blocks.registerBlockType('foldery/atelier-hero', {
    apiVersion: 3,
    title: __('Foldery Atelier Hero', 'foldery'),
    icon: 'format-image',
    category: 'theme',
    edit: function() {
      var blockProps = useBlockProps({ className: 'foldery-atelier-hero-editor' });
      var atelier = useAtelierMeta();
      var hasMetaKeys = useMemo(function() {
        return atelierMetaKeys.heroImage && atelierMetaKeys.title && atelierMetaKeys.subtitle && atelierMetaKeys.artworks;
      }, []);

      if (!hasMetaKeys) {
        return el(
          'div',
          blockProps,
          el(Placeholder, { icon: 'format-image', label: __('Foldery Atelier Hero', 'foldery') }, __('Configuration du hero indisponible.', 'foldery'))
        );
      }

      return el(
        Fragment,
        null,
        el(AtelierHeroControls, { atelier: atelier }),
        el('div', blockProps, el(AtelierHeroPreview, { atelier: atelier }))
      );
    },
    save: function() {
      return null;
    }
  });
}(wp.blocks, wp.element, wp.components, wp.blockEditor, wp.data, wp.i18n));
