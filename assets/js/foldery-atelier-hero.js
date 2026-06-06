(function () {
  var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)');
  var finePointer = window.matchMedia && window.matchMedia('(hover: hover) and (pointer: fine)');
  var artworkMotion = [
    { scrollX: -10, scrollY: 68, rotate: -1.1, pointerX: 12, pointerY: 8, pointerRotate: 0.45 },
    { scrollX: 8, scrollY: 46, rotate: 0.8, pointerX: 8, pointerY: 5, pointerRotate: -0.35 },
    { scrollX: -6, scrollY: 82, rotate: -0.7, pointerX: 14, pointerY: 10, pointerRotate: 0.35 },
    { scrollX: 10, scrollY: 54, rotate: 0.9, pointerX: 9, pointerY: 6, pointerRotate: -0.3 },
    { scrollX: 5, scrollY: 36, rotate: -0.5, pointerX: 6, pointerY: 4, pointerRotate: 0.25 },
    { scrollX: 13, scrollY: 58, rotate: 1.2, pointerX: 10, pointerY: 7, pointerRotate: -0.4 }
  ];

  function clamp(value, min, max) {
    return Math.min(Math.max(value, min), max);
  }

  function px(value) {
    return value.toFixed(2) + 'px';
  }

  function deg(value) {
    return value.toFixed(3) + 'deg';
  }

  function resetHero(hero, artworks) {
    [
      '--atelier-hero-bg-y',
      '--atelier-hero-shade-y',
      '--atelier-hero-paper-y',
      '--atelier-copy-parallax-y'
    ].forEach(function (property) {
      hero.style.removeProperty(property);
    });

    artworks.forEach(function (artwork) {
      artwork.style.removeProperty('--atelier-artwork-parallax-x');
      artwork.style.removeProperty('--atelier-artwork-parallax-y');
      artwork.style.removeProperty('--atelier-artwork-parallax-rotate');
    });
  }

  function initHero(hero) {
    var artworks = Array.prototype.slice.call(hero.querySelectorAll('.atelier-hero-artwork'));
    var pointer = { x: 0, y: 0 };
    var pointerTarget = { x: 0, y: 0 };
    var pointerAnimating = false;
    var ticking = false;

    function update() {
      var rect = hero.getBoundingClientRect();
      var height = rect.height || hero.offsetHeight || 1;
      var progress = clamp((0 - rect.top) / height, 0, 1);

      ticking = false;

      if (reduceMotion && reduceMotion.matches) {
        resetHero(hero, artworks);
        return;
      }

      hero.style.setProperty('--atelier-hero-bg-y', px(progress * 76));
      hero.style.setProperty('--atelier-hero-shade-y', px(progress * 18));
      hero.style.setProperty('--atelier-hero-paper-y', px(progress * -16));
      hero.style.setProperty('--atelier-copy-parallax-y', px(progress * 18));

      artworks.forEach(function (artwork, index) {
        var motion = artworkMotion[index] || artworkMotion[artworkMotion.length - 1];
        var x = progress * motion.scrollX + pointer.x * motion.pointerX;
        var y = progress * -motion.scrollY + pointer.y * motion.pointerY;
        var rotation = progress * motion.rotate + pointer.x * motion.pointerRotate;

        artwork.style.setProperty('--atelier-artwork-parallax-x', px(x));
        artwork.style.setProperty('--atelier-artwork-parallax-y', px(y));
        artwork.style.setProperty('--atelier-artwork-parallax-rotate', deg(rotation));
      });
    }

    function requestUpdate() {
      if (ticking) {
        return;
      }

      ticking = true;
      window.requestAnimationFrame(update);
    }

    function animatePointer() {
      var deltaX = pointerTarget.x - pointer.x;
      var deltaY = pointerTarget.y - pointer.y;

      pointer.x += deltaX * 0.14;
      pointer.y += deltaY * 0.14;

      if (Math.abs(deltaX) < 0.002 && Math.abs(deltaY) < 0.002) {
        pointer.x = pointerTarget.x;
        pointer.y = pointerTarget.y;
        pointerAnimating = false;
        requestUpdate();
        return;
      }

      requestUpdate();
      window.requestAnimationFrame(animatePointer);
    }

    function requestPointerAnimation() {
      if (pointerAnimating) {
        return;
      }

      pointerAnimating = true;
      window.requestAnimationFrame(animatePointer);
    }

    function updatePointer(event) {
      if (!finePointer || !finePointer.matches || (reduceMotion && reduceMotion.matches)) {
        return;
      }

      var rect = hero.getBoundingClientRect();

      pointerTarget.x = clamp(((event.clientX - rect.left) / rect.width - 0.5) * 2, -1, 1);
      pointerTarget.y = clamp(((event.clientY - rect.top) / rect.height - 0.5) * 2, -1, 1);
      requestPointerAnimation();
    }

    function resetPointer() {
      pointerTarget.x = 0;
      pointerTarget.y = 0;
      requestPointerAnimation();
    }

    window.addEventListener('scroll', requestUpdate, { passive: true });
    window.addEventListener('resize', requestUpdate);
    hero.addEventListener('mousemove', updatePointer, { passive: true });
    hero.addEventListener('mouseleave', resetPointer);

    requestUpdate();
  }

  function init() {
    var heroes = Array.prototype.slice.call(document.querySelectorAll('.atelier-hero'));

    if (!heroes.length || !window.requestAnimationFrame) {
      return;
    }

    heroes.forEach(function (hero) {
      if (hero.dataset.folderyAtelierParallaxReady) {
        return;
      }

      hero.dataset.folderyAtelierParallaxReady = '1';

      if (reduceMotion && reduceMotion.matches) {
        resetHero(hero, Array.prototype.slice.call(hero.querySelectorAll('.atelier-hero-artwork')));
        return;
      }

      initHero(hero);
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
