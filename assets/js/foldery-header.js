(function () {
  function numberAttribute(element, name, fallback) {
    var value = Number(element.getAttribute(name));
    return Number.isFinite(value) ? value : fallback;
  }

  function setHeaderReserve(header) {
    var wasCompact = header.classList.contains('is-compact');

    if (wasCompact) {
      header.classList.remove('is-compact');
    }

    var headerHeight = header.offsetHeight;
    var headerTop = Math.max(0, header.getBoundingClientRect().top);
    var headerReserve = headerTop + headerHeight;

    header.style.removeProperty('--foldery-paper-header-height');
    header.style.setProperty('--foldery-paper-header-height', headerHeight + 'px');
    document.documentElement.style.setProperty('--foldery-paper-header-height', headerHeight + 'px');
    document.documentElement.style.setProperty('--foldery-paper-header-reserve', headerReserve + 'px');

    if (wasCompact) {
      header.classList.add('is-compact');
    }
  }

  function updateHeader(header) {
    var compactThreshold = numberAttribute(header, 'data-compact-threshold', 120);
    var expandThreshold = numberAttribute(header, 'data-expand-threshold', 8);
    var isCompact = header.classList.contains('is-compact');
    var shouldCompact = isCompact ? window.scrollY > expandThreshold : window.scrollY > compactThreshold;

    header.classList.toggle('is-compact', shouldCompact);
  }

  function init() {
    var headers = document.querySelectorAll('[data-foldery-paper-header]');
    if (!headers.length) {
      return;
    }

    var ticking = false;
    function requestUpdate() {
      if (ticking) {
        return;
      }

      ticking = true;
      window.requestAnimationFrame(function () {
        headers.forEach(updateHeader);
        ticking = false;
      });
    }

    headers.forEach(function (header) {
      setHeaderReserve(header);
      updateHeader(header);
    });

    window.addEventListener('scroll', requestUpdate, { passive: true });
    window.addEventListener('resize', function () {
      headers.forEach(setHeaderReserve);
      requestUpdate();
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
