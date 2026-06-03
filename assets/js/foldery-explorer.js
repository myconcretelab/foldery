(function() {
  'use strict';

  function normalizeUrl(url) {
    try {
      var parsed = new URL(url, window.location.origin);
      return parsed.pathname.replace(/\/$/, '') || '/';
    } catch (error) {
      return url.replace(/\/$/, '');
    }
  }

  function currentProtocolUrl(url) {
    try {
      var parsed = new URL(url, window.location.origin);
      return parsed.pathname + parsed.search + parsed.hash;
    } catch (error) {
      return url;
    }
  }

  function animationsEnabled(explorer) {
    if (explorer.dataset.animate !== '1') {
      return false;
    }

    if (window.matchMedia('(max-width: 767px)').matches) {
      return false;
    }

    return !window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  }

  function initMasonry(stage) {
    if (!window.Masonry) {
      return;
    }

    var grids = stage.querySelectorAll('[data-masonry]');
    grids.forEach(function(grid) {
      try {
        var options = JSON.parse(grid.getAttribute('data-masonry') || '{}');
        var masonry = new window.Masonry(grid, options);
        var images = grid.querySelectorAll('img');
        var loaded = 0;

        if (!images.length) {
          masonry.layout();
          return;
        }

        images.forEach(function(image) {
          if (image.complete) {
            loaded += 1;
            if (loaded === images.length) {
              masonry.layout();
            }
            return;
          }

          image.addEventListener('load', function() {
            loaded += 1;
            if (loaded === images.length) {
              masonry.layout();
            }
          }, { once: true });
        });
      } catch (error) {
        return;
      }
    });
  }

  function renderStage(explorer, html) {
    var stage = explorer.querySelector('.foldery-explorer-stage');
    var animate = animationsEnabled(explorer);

    function replaceContent(resolve) {
      stage.innerHTML = html;
      initMasonry(stage);

      if (animate) {
        stage.classList.add('is-entering');
        window.setTimeout(function() {
          stage.classList.remove('is-entering');
        }, 980);
      }

      resolve();
    }

    return new Promise(function(resolve) {
      if (!animate) {
        replaceContent(resolve);
        return;
      }

      stage.classList.add('is-leaving');
      window.setTimeout(function() {
        stage.classList.remove('is-leaving');
        replaceContent(resolve);
      }, 180);
    });
  }

  function linkLabel(link) {
    var label = link.querySelector('.foldery-explorer-back-label, h5');
    return label ? label.textContent.trim() : link.textContent.trim();
  }

  function currentFolderLabel(explorer) {
    var heading = explorer.querySelector('.foldery-explorer-folder .foldery-explorer-heading h3, .foldery-explorer-folder .foldery-explorer-page-content h1, .foldery-explorer-folder .foldery-explorer-page-content h2, .foldery-explorer-folder .foldery-explorer-page-content h3');
    return heading ? heading.textContent.trim() : document.title;
  }

  function readCurrentView(explorer, homeView) {
    var stage = explorer.querySelector('.foldery-explorer-stage');
    var home = stage.querySelector('.foldery-explorer-home');
    var folder = stage.querySelector('.foldery-explorer-folder[data-folder-id]');

    if (home && homeView) {
      return homeView;
    }

    if (folder) {
      return {
        type: 'folder',
        folderId: folder.dataset.folderId,
        url: currentProtocolUrl(window.location.href),
        label: currentFolderLabel(explorer),
        backTarget: null
      };
    }

    return null;
  }

  function serializeView(view) {
    if (!view) {
      return null;
    }

    return {
      type: view.type,
      folderId: view.folderId || null,
      url: view.url || null,
      label: view.label || null,
      backTarget: serializeView(view.backTarget)
    };
  }

  function deserializeView(data, homeView) {
    if (!data) {
      return null;
    }

    if (data.type === 'home') {
      return homeView;
    }

    if (data.type === 'folder' && data.folderId) {
      return {
        type: 'folder',
        folderId: data.folderId,
        url: data.url,
        label: data.label || '',
        backTarget: deserializeView(data.backTarget, homeView)
      };
    }

    return null;
  }

  function writeExplorerState(view, replace) {
    var state = {
      folderyExplorer: true,
      view: serializeView(view)
    };
    var url = view && view.url ? view.url : currentProtocolUrl(window.location.href);

    if (replace) {
      window.history.replaceState(state, document.title, url);
    } else {
      window.history.pushState(state, document.title, url);
    }
  }

  function explorerPagePanels() {
    return document.querySelectorAll('.foldery-explorer-page-panel');
  }

  function initExplorerPagePanels() {
    explorerPagePanels().forEach(function(panel) {
      if (!panel.dataset.homeHtml) {
        panel.dataset.homeHtml = panel.innerHTML;
      }
    });
  }

  function renderExplorerPagePanels(html) {
    explorerPagePanels().forEach(function(panel) {
      var content = panel.querySelector('.foldery-explorer-page-panel-content');
      if (content) {
        content.innerHTML = html || '';
      } else {
        panel.innerHTML = html || '';
      }
    });
  }

  function restoreExplorerPagePanels() {
    explorerPagePanels().forEach(function(panel) {
      panel.innerHTML = panel.dataset.homeHtml || '';
    });
  }

  function updateActiveMenuItem(folderId, ancestorIds) {
    var visibleMatch = false;

    document.querySelectorAll('.foldery-explorer-menu .current-menu-item, .foldery-explorer-menu .current-menu-ancestor').forEach(function(item) {
      item.classList.remove('current-menu-item');
      item.classList.remove('current-menu-ancestor');
    });

    if (!folderId) {
      return;
    }

    document.querySelectorAll('.foldery-explorer-menu a[data-folder-id="' + String(folderId) + '"]').forEach(function(link) {
      var item = link.closest('li');
      if (item) {
        visibleMatch = true;
        item.classList.add('current-menu-item');
        while (item && item.parentElement && item.parentElement.classList.contains('sub-menu')) {
          item = item.parentElement.closest('li');
          if (item) {
            item.classList.add('current-menu-ancestor');
          }
        }
      }
    });

    if (!visibleMatch && Array.isArray(ancestorIds)) {
      ancestorIds.forEach(function(ancestorId) {
        document.querySelectorAll('.foldery-explorer-menu a[data-folder-id="' + String(ancestorId) + '"]').forEach(function(link) {
          var item = link.closest('li');
          if (item) {
            item.classList.add('current-menu-ancestor');
          }
        });
      });
    }
  }

  function ensureBackLink(explorer) {
    var view = explorer.querySelector('.foldery-explorer-folder');
    var link;

    if (!view) {
      return null;
    }

    link = view.querySelector('.foldery-explorer-back');
    if (!link) {
      link = document.createElement('a');
      link.className = 'foldery-explorer-back foldery-explorer-link';
      link.innerHTML = '<span class="foldery-explorer-back-icon" aria-hidden="true"></span><span class="foldery-explorer-back-label"></span>';
      view.insertBefore(link, view.firstChild);
    }

    view.classList.add('has-parent-link');
    return link;
  }

  function configureBackLink(explorer, target) {
    var link;
    var label;

    if (!target) {
      return;
    }

    link = ensureBackLink(explorer);
    if (!link) {
      return;
    }

    label = target.label || 'Accueil';
    link.href = target.url || currentProtocolUrl(window.location.href);
    link.dataset.folderyBack = '1';
    link.setAttribute('aria-label', 'Revenir a ' + label);
    link.setAttribute('title', 'Revenir a ' + label);
    link.querySelector('.foldery-explorer-back-label').textContent = label;

    if (target.type === 'home') {
      delete link.dataset.folderId;
      link.dataset.folderyHome = '1';
    } else {
      link.dataset.folderId = target.folderId;
      delete link.dataset.folderyHome;
    }
  }

  function viewFromBackLink(link, homeView) {
    var label;

    if (link.dataset.folderyHome === '1') {
      return homeView;
    }

    if (!link.dataset.folderId) {
      return null;
    }

    label = linkLabel(link).replace(/^Revenir a\s+/, '');
    return {
      type: 'folder',
      folderId: link.dataset.folderId,
      url: link.href,
      label: label,
      backTarget: null
    };
  }

  function renderHome(explorer, homeView, push) {
    if (!homeView) {
      window.location.reload();
      return Promise.resolve(null);
    }

    return renderStage(explorer, homeView.html).then(function() {
      explorer._folderyCurrentView = homeView;
      restoreExplorerPagePanels();
      updateActiveMenuItem(null);
      if (push) {
        writeExplorerState(homeView, false);
      }
      return homeView;
    });
  }

  function loadFolder(explorer, folderId, targetUrl, push, backTarget, fallbackLabel) {
    var apiUrl = new URL(explorer.dataset.apiUrl, window.location.origin);
    apiUrl.searchParams.set('folder_id', folderId);
    apiUrl.searchParams.set('include_page', explorer.dataset.includePage || '1');

    explorer.classList.add('is-loading');

    return window.fetch(apiUrl.toString(), { credentials: 'same-origin' })
      .then(function(response) {
        if (!response.ok) {
          throw new Error('Foldery explorer request failed.');
        }
        return response.json();
      })
      .then(function(payload) {
        return renderStage(explorer, payload.html || '').then(function() {
          var view = {
            type: 'folder',
            folderId: String(folderId),
            url: currentProtocolUrl(payload.url || targetUrl || window.location.href),
            label: payload.title || fallbackLabel || '',
            backTarget: backTarget || null
          };

          configureBackLink(explorer, view.backTarget);
          explorer._folderyCurrentView = view;
          explorer.classList.remove('is-loading');
          renderExplorerPagePanels(payload.pageContent || '');
          updateActiveMenuItem(folderId, payload.ancestorIds || []);

          if (push && targetUrl) {
            writeExplorerState(view, false);
          }

          return view;
        });
      })
      .catch(function() {
        explorer.classList.remove('is-loading');
      });
  }

  function navigateToView(explorer, view, push, homeView) {
    if (!view) {
      return Promise.resolve(null);
    }

    if (view.type === 'home') {
      return renderHome(explorer, homeView, push);
    }

    return loadFolder(explorer, view.folderId, view.url, push, view.backTarget, view.label);
  }

  function readMenuMap(explorer) {
    try {
      return JSON.parse(explorer.dataset.menuMap || '{}');
    } catch (error) {
      return {};
    }
  }

  function initExplorer(explorer) {
    var menuMap = readMenuMap(explorer);
    var stage = explorer.querySelector('.foldery-explorer-stage');
    var hasHome = !!stage.querySelector('.foldery-explorer-home');
    var homeView = hasHome ? {
      type: 'home',
      url: currentProtocolUrl(window.location.href),
      label: 'Accueil',
      html: stage.innerHTML,
      backTarget: null
    } : null;

    initMasonry(explorer);
    explorer._folderyCurrentView = readCurrentView(explorer, homeView);
    writeExplorerState(explorer._folderyCurrentView, true);

    explorer.addEventListener('click', function(event) {
      var backLink = event.target.closest('.foldery-explorer-back[data-foldery-back]');
      var targetView;
      var link;

      if (backLink) {
        targetView = explorer._folderyCurrentView && explorer._folderyCurrentView.backTarget
          ? explorer._folderyCurrentView.backTarget
          : viewFromBackLink(backLink, homeView);

        if (!targetView) {
          return;
        }

        event.preventDefault();
        navigateToView(explorer, targetView, true, homeView);
        return;
      }

      link = event.target.closest('.foldery-explorer-link[data-folder-id]');
      if (!link) {
        return;
      }

      event.preventDefault();
      loadFolder(explorer, link.dataset.folderId, link.href, true, explorer._folderyCurrentView, linkLabel(link));
    });

    document.addEventListener('click', function(event) {
      var link = event.target.closest('.foldery-explorer-menu a[data-folder-id], #site-navigation a');
      var previousView = explorer._folderyCurrentView;
      var match;
      var folderId;
      var title;

      if (!link || !explorer.isConnected) {
        return;
      }

      match = link.dataset.folderId ? null : menuMap[normalizeUrl(link.href)];
      folderId = link.dataset.folderId || (match && match.folderId);
      title = link.textContent.trim() || (match && match.title);

      if (!folderId) {
        return;
      }

      event.preventDefault();
      loadFolder(explorer, folderId, link.href, true, previousView, title);
    });

    window.addEventListener('popstate', function(event) {
      var view = event.state && event.state.folderyExplorer ? deserializeView(event.state.view, homeView) : null;
      if (view) {
        navigateToView(explorer, view, false, homeView);
      } else if (event.state && event.state.folderyExplorer && event.state.folderId) {
        loadFolder(explorer, event.state.folderId, window.location.href, false, null);
      } else {
        window.location.reload();
      }
    });
  }

  document.addEventListener('DOMContentLoaded', function() {
    initExplorerPagePanels();
    document.querySelectorAll('.foldery-explorer').forEach(initExplorer);
  });
}());
