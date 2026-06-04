(function () {
  function updateHeader(header) {
    var threshold = Number(header.getAttribute('data-compact-threshold') || 52);
    header.classList.toggle('is-compact', window.scrollY > threshold);
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

    headers.forEach(updateHeader);
    window.addEventListener('scroll', requestUpdate, { passive: true });
    window.addEventListener('resize', requestUpdate);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
