(function(blocks, element, components, blockEditor, i18n) {
  'use strict';

  var el = element.createElement;
  var InspectorControls = blockEditor.InspectorControls;
  var PanelBody = components.PanelBody;
  var TextControl = components.TextControl;
  var ToggleControl = components.ToggleControl;
  var RangeControl = components.RangeControl;
  var Disabled = components.Disabled;
  var ServerSideRender = wp.serverSideRender;
  var __ = i18n.__;

  blocks.registerBlockType('foldery/explorer', {
    title: __('Foldery Explorer', 'foldery'),
    icon: 'images-alt2',
    category: 'widgets',
    attributes: {
      showHomeSelection: { type: 'boolean', default: true },
      homeFolderIds: { type: 'string', default: '45,49,56,38,2,12,1' },
      homeTitle: { type: 'string', default: 'Series en cours...' },
      showRecent: { type: 'boolean', default: true },
      recentTitle: { type: 'string', default: "Recemment cree a l'atelier (ou dehors !)" },
      recentLimit: { type: 'number', default: 3 },
      includePageContent: { type: 'boolean', default: true },
      animate: { type: 'boolean', default: true }
    },
    edit: function(props) {
      var attrs = props.attributes;
      var setAttributes = props.setAttributes;

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
            el(TextControl, {
              label: __('IDs des dossiers selectionnes', 'foldery'),
              help: __('Liste separee par des virgules.', 'foldery'),
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
            el(RangeControl, {
              label: __('Nombre de creations recentes', 'foldery'),
              value: attrs.recentLimit,
              min: 1,
              max: 12,
              onChange: function(value) { setAttributes({ recentLimit: value }); }
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
            el(ToggleControl, {
              label: __('Animations desktop', 'foldery'),
              checked: attrs.animate,
              onChange: function(value) { setAttributes({ animate: value }); }
            })
          )
        ),
        el(
          Disabled,
          null,
          el(ServerSideRender, {
            block: 'foldery/explorer',
            attributes: attrs
          })
        )
      );
    },
    save: function() {
      return null;
    }
  });
}(wp.blocks, wp.element, wp.components, wp.blockEditor, wp.i18n));
