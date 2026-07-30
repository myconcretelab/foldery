(function () {
  'use strict';

  function markSafari() {
    var ua = window.navigator.userAgent;
    var isSafari = /Safari/.test(ua) && !/(Chrome|Chromium|CriOS|Edg|OPR|Firefox|FxiOS)/.test(ua);

    document.documentElement.classList.toggle('foldery-is-safari', isSafari);
  }

  function setHeaderReserve(header) {
    header.classList.remove('is-compact');
    var headerHeight = header.offsetHeight;
    var headerTop = Math.max(0, header.getBoundingClientRect().top);
    var headerReserve = headerTop + headerHeight;

    header.style.removeProperty('--foldery-paper-header-height');
    header.style.setProperty('--foldery-paper-header-height', headerHeight + 'px');
    document.documentElement.style.setProperty('--foldery-paper-header-height', headerHeight + 'px');
    document.documentElement.style.setProperty('--foldery-paper-header-reserve', headerReserve + 'px');
  }

  function drawerId(index) {
    return 'foldery-mobile-drawer-' + String(index + 1);
  }

  function createMobileDrawer(header, menu, index) {
    var drawer = document.createElement('aside');
    var mobileMenu = menu.cloneNode(true);
    var headerColumns = header.querySelectorAll(
      '.foldery-paper-header__column--artist, .foldery-paper-header__column--contact'
    );

    drawer.id = drawerId(index);
    drawer.className = 'foldery-mobile-drawer';
    drawer.setAttribute('aria-hidden', 'true');
    drawer.setAttribute('aria-label', 'Navigation');
    drawer.setAttribute('inert', '');

    mobileMenu.classList.add('foldery-paper-header__menu--mobile');
    mobileMenu.removeAttribute('id');
    drawer.appendChild(mobileMenu);

    if (headerColumns.length) {
      var info = document.createElement('div');
      info.className = 'foldery-mobile-drawer__info';

      headerColumns.forEach(function (column) {
        var card = column.cloneNode(true);
        card.classList.add('foldery-mobile-drawer__info-card');
        info.appendChild(card);
      });

      drawer.appendChild(info);
    }

    document.body.appendChild(drawer);

    return drawer;
  }

  function initMobileMenu(header, index, closeAll) {
    var toggle = header.querySelector('.foldery-paper-header__toggle');
    var menu = header.querySelector('.foldery-paper-header__menu');

    if (!toggle || !menu) {
      return null;
    }

    var drawer = createMobileDrawer(header, menu, index);
    var label = toggle.querySelector('.screen-reader-text');
    var isOpen = false;

    toggle.setAttribute('aria-controls', drawer.id);

    function setOpen(open, returnFocus) {
      isOpen = open;
      header.classList.toggle('is-menu-open', open);
      drawer.classList.toggle('is-open', open);
      document.documentElement.classList.toggle('foldery-mobile-menu-open', open);
      document.body.classList.toggle('foldery-mobile-menu-open', open);
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
      drawer.setAttribute('aria-hidden', open ? 'false' : 'true');
      if (open) {
        drawer.removeAttribute('inert');
      } else {
        drawer.setAttribute('inert', '');
      }

      if (label) {
        label.textContent = open ? 'Fermer le menu' : 'Ouvrir le menu';
      }

      if (open) {
        window.requestAnimationFrame(function () {
          var firstLink = drawer.querySelector('a');
          if (firstLink) {
            firstLink.focus({ preventScroll: true });
          }
        });
      } else if (returnFocus) {
        toggle.focus({ preventScroll: true });
      }
    }

    toggle.addEventListener('click', function () {
      if (!isOpen) {
        closeAll(header);
      }
      setOpen(!isOpen, false);
    });

    drawer.addEventListener('click', function (event) {
      if (event.target.closest('a')) {
        setOpen(false, false);
      }
    });

    return {
      header: header,
      drawer: drawer,
      close: function (returnFocus) {
        if (isOpen) {
          setOpen(false, returnFocus);
        }
      },
      isOpen: function () {
        return isOpen;
      }
    };
  }

  function init() {
    var headers = Array.prototype.slice.call(document.querySelectorAll('[data-foldery-paper-header]'));
    if (!headers.length) {
      return;
    }

    var menus = [];

    function closeAll(exceptHeader, returnFocus) {
      menus.forEach(function (menu) {
        if (!exceptHeader || menu.header !== exceptHeader) {
          menu.close(returnFocus);
        }
      });
    }

    headers.forEach(function (header, index) {
      var menu = initMobileMenu(header, index, closeAll);
      if (menu) {
        menus.push(menu);
      }

      setHeaderReserve(header);
    });

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') {
        closeAll(null, true);
      }
    });

    document.addEventListener('click', function (event) {
      var openMenu = menus.find(function (menu) {
        return menu.isOpen();
      });

      if (!openMenu || openMenu.header.contains(event.target) || openMenu.drawer.contains(event.target)) {
        return;
      }

      openMenu.close(false);
    });

    window.addEventListener('resize', function () {
      headers.forEach(setHeaderReserve);
      if (window.matchMedia('(min-width: 721px)').matches) {
        closeAll(null, false);
      }
    });
  }

  markSafari();

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
