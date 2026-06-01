(function () {
  'use strict';

  var options = window.FolderyLightboxOptions || {};
  var labels = options.labels || {};
  var linkSelector = 'a[data-foldery-lightbox="image"], a[rel^="prettyPhoto"], a[data-rel^="prettyPhoto"], a[data-gal^="prettyPhoto"]';
  var state = {
    root: null,
    overlay: null,
    container: null,
    image: null,
    loading: null,
    title: null,
    status: null,
    prev: null,
    next: null,
    slideshow: null,
    items: [],
    index: 0,
    timer: null,
    touchX: null
  };

  function text(key, fallback) {
    return labels[key] || fallback;
  }

  function isImageLink(link) {
    return link && /\.(jpe?g|png|gif|webp|avif|bmp)(?:$|[?#])/i.test(link.href);
  }

  function groupValue(link) {
    return link.getAttribute('data-foldery-lightbox-group') ||
      link.getAttribute('data-rel') ||
      link.getAttribute('data-gal') ||
      link.getAttribute('rel') ||
      'page';
  }

  function sameGroup(link, group) {
    return groupValue(link) === group;
  }

  function galleryRoot(link) {
    if (!options.groupGallery || !link.closest) {
      return null;
    }

    return link.closest('.gallery, .wp-block-gallery, .blocks-gallery-grid, .wpb_gallery, .entry-gallery, .cms-grid, [data-foldery-lightbox-gallery]');
  }

  function getItems(link) {
    var root = galleryRoot(link);
    var group = groupValue(link);
    var links = Array.prototype.slice.call((root || document).querySelectorAll(linkSelector));

    if (!root && !options.groupLinks) {
      return [link];
    }

    links = links.filter(function (item) {
      return isImageLink(item) && (root || sameGroup(item, group));
    });

    return links.length ? links : [link];
  }

  function getTitle(link) {
    var title = link.getAttribute('data-foldery-lightbox-title') || link.getAttribute('title') || '';
    var figure = link.closest ? link.closest('figure') : null;
    var caption;
    var image;

    if (!title && figure) {
      caption = figure.querySelector('figcaption, .wp-caption-text');
      title = caption ? caption.innerHTML : '';
    }

    if (!title) {
      image = link.querySelector('img');
      title = image ? image.getAttribute('alt') || '' : '';
    }

    if (!title && options.titleDefault) {
      title = link.textContent || '';
    }

    return String(title || '').trim();
  }

  function renderStatus() {
    var template = text('groupStatus', 'Image %current% of %total%');
    return template
      .replace('%current%', String(state.index + 1))
      .replace('%total%', String(state.items.length));
  }

  function build() {
    if (state.root) {
      return;
    }

    state.root = document.createElement('div');
    state.root.id = 'foldery-lightbox';
    state.root.className = 'foldery-lightbox';
    state.root.innerHTML =
      '<div class="foldery-lightbox__overlay"></div>' +
      '<div class="foldery-lightbox__layout" role="dialog" aria-modal="true">' +
        '<div class="foldery-lightbox__container">' +
          '<div class="foldery-lightbox__controls">' +
            '<button type="button" class="foldery-lightbox__control foldery-lightbox__slideshow"></button>' +
            '<button type="button" class="foldery-lightbox__control foldery-lightbox__close"></button>' +
          '</div>' +
          '<div class="foldery-lightbox__content">' +
            '<button type="button" class="foldery-lightbox__nav foldery-lightbox__prev"></button>' +
            '<img class="foldery-lightbox__image" alt="">' +
            '<button type="button" class="foldery-lightbox__nav foldery-lightbox__next"></button>' +
            '<div class="foldery-lightbox__loading"></div>' +
          '</div>' +
          '<div class="foldery-lightbox__details">' +
            '<span class="foldery-lightbox__title"></span>' +
            '<span class="foldery-lightbox__status"></span>' +
          '</div>' +
        '</div>' +
      '</div>';

    document.body.appendChild(state.root);

    state.overlay = state.root.querySelector('.foldery-lightbox__overlay');
    state.container = state.root.querySelector('.foldery-lightbox__container');
    state.image = state.root.querySelector('.foldery-lightbox__image');
    state.loading = state.root.querySelector('.foldery-lightbox__loading');
    state.title = state.root.querySelector('.foldery-lightbox__title');
    state.status = state.root.querySelector('.foldery-lightbox__status');
    state.prev = state.root.querySelector('.foldery-lightbox__prev');
    state.next = state.root.querySelector('.foldery-lightbox__next');
    state.slideshow = state.root.querySelector('.foldery-lightbox__slideshow');

    state.root.querySelector('.foldery-lightbox__close').setAttribute('aria-label', text('close', 'Close'));
    state.root.querySelector('.foldery-lightbox__close').title = text('close', 'Close');
    state.prev.setAttribute('aria-label', text('prev', 'Previous'));
    state.next.setAttribute('aria-label', text('next', 'Next'));
    state.loading.textContent = text('loading', 'Loading');
    state.overlay.style.opacity = String(options.overlayOpacity || 0.8);

    state.overlay.addEventListener('click', close);
    state.root.querySelector('.foldery-lightbox__close').addEventListener('click', close);
    state.prev.addEventListener('click', function () { move(-1); });
    state.next.addEventListener('click', function () { move(1); });
    state.slideshow.addEventListener('click', toggleSlideshow);
    state.root.addEventListener('touchstart', touchStart, { passive: true });
    state.root.addEventListener('touchend', touchEnd, { passive: true });
  }

  function open(link) {
    build();
    state.items = getItems(link);
    state.index = Math.max(0, state.items.indexOf(link));
    document.documentElement.classList.add('foldery-lightbox-open');
    state.root.classList.toggle('foldery-lightbox--autofit', options.autofit !== false);
    state.root.classList.toggle('foldery-lightbox--animate', !!options.animate);
    state.root.classList.add('is-open');
    show(state.index);

    if (options.slideshowAutostart && state.items.length > 1) {
      startSlideshow();
    } else {
      stopSlideshow();
    }
  }

  function show(index) {
    var link = state.items[index];
    var title = getTitle(link);
    var single = state.items.length <= 1;

    state.index = index;
    state.root.classList.add('is-loading');
    state.root.classList.toggle('item-single', single);
    state.prev.disabled = single || (!options.loop && index === 0);
    state.next.disabled = single || (!options.loop && index === state.items.length - 1);
    state.title.textContent = title;
    state.status.textContent = single ? '' : renderStatus();
    state.image.alt = title.replace(/<[^>]+>/g, '');
    state.image.removeAttribute('src');

    var image = new Image();
    image.onload = function () {
      state.image.src = link.href;
      state.root.classList.remove('is-loading');
    };
    image.onerror = function () {
      state.root.classList.remove('is-loading');
    };
    image.src = link.href;
  }

  function move(step) {
    var next = state.index + step;

    if (next < 0) {
      next = options.loop ? state.items.length - 1 : 0;
    } else if (next >= state.items.length) {
      next = options.loop ? 0 : state.items.length - 1;
    }

    if (next !== state.index) {
      show(next);
    }
  }

  function close() {
    if (!state.root) {
      return;
    }

    stopSlideshow();
    state.root.classList.remove('is-open');
    document.documentElement.classList.remove('foldery-lightbox-open');
  }

  function startSlideshow() {
    stopSlideshow();
    state.root.classList.add('slideshow-active');
    state.slideshow.setAttribute('aria-label', text('slideshowStop', 'Stop slideshow'));
    state.slideshow.title = text('slideshowStop', 'Stop slideshow');
    state.timer = window.setInterval(function () {
      move(1);
    }, Math.max(1, Number(options.slideshowDuration || 6)) * 1000);
  }

  function stopSlideshow() {
    if (state.timer) {
      window.clearInterval(state.timer);
      state.timer = null;
    }
    if (state.root) {
      state.root.classList.remove('slideshow-active');
      state.slideshow.setAttribute('aria-label', text('slideshowStart', 'Start slideshow'));
      state.slideshow.title = text('slideshowStart', 'Start slideshow');
    }
  }

  function toggleSlideshow() {
    if (state.timer) {
      stopSlideshow();
    } else if (state.items.length > 1) {
      startSlideshow();
    }
  }

  function touchStart(event) {
    state.touchX = event.changedTouches && event.changedTouches.length ? event.changedTouches[0].clientX : null;
  }

  function touchEnd(event) {
    var endX = event.changedTouches && event.changedTouches.length ? event.changedTouches[0].clientX : null;
    var delta = state.touchX === null || endX === null ? 0 : endX - state.touchX;

    if (Math.abs(delta) > 50) {
      move(delta > 0 ? -1 : 1);
    }
    state.touchX = null;
  }

  document.addEventListener('click', function (event) {
    var link = event.target.closest ? event.target.closest(linkSelector) : null;
    if (!link || !isImageLink(link)) {
      return;
    }

    event.preventDefault();
    event.stopPropagation();
    if (event.stopImmediatePropagation) {
      event.stopImmediatePropagation();
    }
    open(link);
  }, true);

  document.addEventListener('keydown', function (event) {
    if (!state.root || !state.root.classList.contains('is-open')) {
      return;
    }

    if (event.key === 'Escape') {
      close();
    } else if (event.key === 'ArrowLeft') {
      move(-1);
    } else if (event.key === 'ArrowRight') {
      move(1);
    } else if (event.key === ' ') {
      event.preventDefault();
      toggleSlideshow();
    }
  });

  window.vc_prettyPhoto = window.vc_prettyPhoto || function () {};
}());
