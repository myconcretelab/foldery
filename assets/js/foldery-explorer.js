(function() {
  'use strict';

  function normalizeUrl(url) {
    try {
      var parsed = new URL(url, window.location.origin);
      return parsed.origin + parsed.pathname.replace(/\/$/, '');
    } catch (error) {
      return url.replace(/\/$/, '');
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

    function replaceContent() {
      stage.innerHTML = html;
      initMasonry(stage);

      if (animate) {
        stage.classList.add('is-entering');
        window.requestAnimationFrame(function() {
          stage.classList.remove('is-entering');
        });
      }
    }

    if (!animate) {
      replaceContent();
      return;
    }

    stage.classList.add('is-leaving');
    window.setTimeout(function() {
      stage.classList.remove('is-leaving');
      replaceContent();
    }, 180);
  }

  function loadFolder(explorer, folderId, targetUrl, push) {
    var apiUrl = new URL(explorer.dataset.apiUrl);
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
        renderStage(explorer, payload.html || '');
        explorer.classList.remove('is-loading');

        if (push && targetUrl) {
          window.history.pushState({ folderyExplorer: true, folderId: folderId }, payload.title || document.title, targetUrl);
        }
      })
      .catch(function() {
        explorer.classList.remove('is-loading');
      });
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

    initMasonry(explorer);

    explorer.addEventListener('click', function(event) {
      var link = event.target.closest('.foldery-explorer-link[data-folder-id]');
      if (!link) {
        return;
      }

      event.preventDefault();
      loadFolder(explorer, link.dataset.folderId, link.href, true);
    });

    document.addEventListener('click', function(event) {
      var link = event.target.closest('#site-navigation a');
      if (!link || !explorer.isConnected) {
        return;
      }

      var match = menuMap[normalizeUrl(link.href)];
      if (!match) {
        return;
      }

      event.preventDefault();
      loadFolder(explorer, match.folderId, link.href, true);
    });

    window.addEventListener('popstate', function(event) {
      if (event.state && event.state.folderyExplorer && event.state.folderId) {
        loadFolder(explorer, event.state.folderId, window.location.href, false);
      } else {
        window.location.reload();
      }
    });
  }

  document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.foldery-explorer').forEach(initExplorer);
  });
}());
