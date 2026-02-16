(function() {
  'use strict';

  // ========== HERO INTERACTION REVEAL ==========
  var heroSection = document.getElementById('hero');
  var heroInteractionBg = document.getElementById('hero-interaction-bg');
  var heroRevealTicking = false;
  var heroGearsTriggered = false;
  var previousScrollY = window.scrollY;

  function updateHeroInteractionReveal() {
    if (!heroSection || !heroInteractionBg) return;

    var revealDistance = Math.max(window.innerHeight * 0.9, heroSection.offsetHeight * 0.72);
    var progress = window.scrollY / revealDistance;
    progress = Math.max(0, Math.min(1, progress));

    // Smoothstep easing: starts subtle, reveals more as user scrolls deeper.
    var eased = progress * progress * (3 - 2 * progress);
    heroInteractionBg.style.opacity = (eased * 0.82).toFixed(3);
  }

  function onHeroRevealScroll() {
    var currentScrollY = window.scrollY;

    if (heroSection && currentScrollY <= 8) {
      heroSection.classList.remove('hero-gears-on');
      heroGearsTriggered = false;
    } else if (!heroGearsTriggered && currentScrollY > 10 && currentScrollY >= previousScrollY && heroSection) {
      heroSection.classList.add('hero-gears-on');
      heroGearsTriggered = true;
    }
    previousScrollY = currentScrollY;

    if (heroRevealTicking) return;
    heroRevealTicking = true;
    requestAnimationFrame(function() {
      updateHeroInteractionReveal();
      heroRevealTicking = false;
    });
  }

  window.addEventListener('scroll', onHeroRevealScroll, { passive: true });
  window.addEventListener('resize', updateHeroInteractionReveal);
  updateHeroInteractionReveal();

  // ========== PARALLAX ENGINE ==========
  var isMobile = window.innerWidth < 768;
  var searchParams = new URLSearchParams(window.location.search);
  var parallaxParam = searchParams.get('parallax');
  var disableParallax = parallaxParam === '0';
  // Design preview mode: keep parallax on desktop/tablet even with reduced-motion.
  var parallaxEnabled = !disableParallax && !isMobile;

  if (parallaxEnabled) {
    var parallaxElements = document.querySelectorAll('[data-parallax]');
    var ticking = false;
    function getParallaxMax(el, speed) {
      var maxAttr = parseFloat(el.getAttribute('data-parallax-max'));
      if (!isNaN(maxAttr)) return maxAttr;
      return Math.max(18, Math.abs(speed) * 120);
    }

    function updateParallax() {
      var viewportHeight = window.innerHeight;

      parallaxElements.forEach(function(el) {
        var speed = parseFloat(el.getAttribute('data-parallax')) || 0.1;

        // Skip elements that have reveal class but are not yet revealed
        if (el.classList.contains('reveal') && !el.classList.contains('revealed')) return;

        // Use the element's parent section for position reference (more stable)
        var section = el.closest('section') || el.parentElement;
        var rect = section.getBoundingClientRect();

        // How far through the viewport is this section?
        // -1 = section is fully below viewport, 0 = centered, 1 = fully above
        var sectionCenter = rect.top + rect.height / 2;
        var viewportCenter = viewportHeight / 2;
        var offset = (viewportCenter - sectionCenter) / viewportHeight;

        // Keep movement predictable and visible by clamping section progress.
        var clampedOffset = Math.max(-1, Math.min(1, offset));
        var direction = speed < 0 ? -1 : 1;
        var maxShift = getParallaxMax(el, speed);
        var translateY = clampedOffset * maxShift * direction;
        var translateX = 0;
        if (el.hasAttribute('data-parallax-x')) {
          var xShift = parseFloat(el.getAttribute('data-parallax-x'));
          if (isNaN(xShift)) xShift = maxShift * 0.5;
          translateX = clampedOffset * xShift * direction;
        }
        var transform = 'translate(' + translateX.toFixed(2) + 'px, ' + translateY.toFixed(2) + 'px)';

        if (el.hasAttribute('data-parallax-bgshift')) {
          var bgShift = parseFloat(el.getAttribute('data-parallax-bgshift'));
          if (isNaN(bgShift)) bgShift = maxShift;
          var bgY = clampedOffset * bgShift * direction;
          el.style.backgroundPosition = '0px ' + bgY.toFixed(2) + 'px, 0px ' + (bgY * 0.65).toFixed(2) + 'px';
        }

        // Optional scroll-reactive spin + color for highlighted decorative elements.
        if (el.hasAttribute('data-parallax-spin')) {
          var spinMax = parseFloat(el.getAttribute('data-parallax-spin')) || 360;
          var hueMax = parseFloat(el.getAttribute('data-parallax-hue')) || 90;
          var intensity = Math.abs(clampedOffset);
          var rotation = clampedOffset * spinMax;
          transform += ' rotate(' + rotation.toFixed(2) + 'deg)';
          if (el.hasAttribute('data-parallax-scale')) {
            var scaleMax = parseFloat(el.getAttribute('data-parallax-scale'));
            if (isNaN(scaleMax)) scaleMax = 0.16;
            var scale = 1 + intensity * scaleMax;
            transform += ' scale(' + scale.toFixed(3) + ')';
          }

          el.style.opacity = (0.55 + intensity * 0.35).toFixed(2);
          el.style.filter = 'saturate(' + (1 + intensity * 0.45).toFixed(2) + ') hue-rotate(' + (intensity * hueMax).toFixed(1) + 'deg)';

          if (el.hasAttribute('data-parallax-stroke')) {
            if (!el._parallaxStrokeEls) {
              el._parallaxStrokeEls = el.querySelectorAll('path,circle,rect,ellipse,line,polygon,polyline');
            }
            var from = { r: 184, g: 115, b: 51 };
            var to = { r: 89, g: 208, b: 255 };
            var r = Math.round(from.r + (to.r - from.r) * intensity);
            var g = Math.round(from.g + (to.g - from.g) * intensity);
            var b = Math.round(from.b + (to.b - from.b) * intensity);
            var strokeColor = 'rgb(' + r + ', ' + g + ', ' + b + ')';
            el._parallaxStrokeEls.forEach(function(shape) { shape.style.stroke = strokeColor; });
          }
        }

        el.style.transform = transform;
      });

      ticking = false;
    }

    function onScroll() {
      if (!ticking) {
        requestAnimationFrame(updateParallax);
        ticking = true;
      }
    }

    window.addEventListener('scroll', onScroll, { passive: true });
    updateParallax();

    window.addEventListener('resize', function() {
      isMobile = window.innerWidth < 768;
      if (isMobile) {
        parallaxElements.forEach(function(el) {
          el.style.transform = '';
          if (el.hasAttribute('data-parallax-spin')) {
            el.style.filter = '';
            el.style.opacity = '';
          }
          if (el.hasAttribute('data-parallax-bgshift')) {
            el.style.backgroundPosition = '';
          }
        });
      }
    });
  }

  // ========== SCROLL REVEAL ==========
  var revealObserver = new IntersectionObserver(function(entries) {
    entries.forEach(function(entry) {
      if (entry.isIntersecting) {
        entry.target.classList.add('revealed');
      }
    });
  }, { threshold: 0.15 });

  document.querySelectorAll('.reveal').forEach(function(el) {
    revealObserver.observe(el);
  });

  // ========== FAQ ACCORDION ==========
  document.querySelectorAll('.faq-toggle').forEach(function(btn) {
    btn.addEventListener('click', function() {
      var item = btn.closest('.faq-item');
      if (!item) return;
      var isOpen = item.classList.contains('open');

      // Close all
      document.querySelectorAll('.faq-item').forEach(function(fi) {
        fi.classList.remove('open');
      });

      // Toggle current
      if (!isOpen) {
        item.classList.add('open');
      }
    });
  });

  // ========== MOBILE MENU ==========
  var menuToggle = document.getElementById('menu-toggle');
  var mobileMenu = document.getElementById('mobile-menu');

  if (menuToggle && mobileMenu) {
    menuToggle.addEventListener('click', function() {
      mobileMenu.classList.toggle('open');
    });
  }

  // Close mobile menu on link click
  document.querySelectorAll('.mobile-link').forEach(function(link) {
    link.addEventListener('click', function() {
      if (mobileMenu) mobileMenu.classList.remove('open');
    });
  });

  // ========== NAVBAR SCROLL EFFECT ==========
  var navbar = document.getElementById('navbar');
  window.addEventListener('scroll', function() {
    if (!navbar) return;
    if (window.scrollY > 50) {
      navbar.classList.add('navbar-scrolled');
    } else {
      navbar.classList.remove('navbar-scrolled');
    }
  }, { passive: true });

  // ========== BACK TO TOP ==========
  var backToTop = document.getElementById('back-to-top');

  window.addEventListener('scroll', function() {
    if (!backToTop) return;
    if (window.scrollY > 600) {
      backToTop.classList.add('visible');
    } else {
      backToTop.classList.remove('visible');
    }
  }, { passive: true });

  if (backToTop) {
    backToTop.addEventListener('click', function() {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  // ========== SMOOTH SCROLL FOR ANCHOR LINKS ==========
  document.querySelectorAll('a[href^="#"]').forEach(function(anchor) {
    anchor.addEventListener('click', function(e) {
      var href = this.getAttribute('href');
      if (!href) return;
      var target = document.querySelector(href);
      if (target) {
        e.preventDefault();
        var offset = 64; // navbar height
        var top = target.getBoundingClientRect().top + window.scrollY - offset;
        window.scrollTo({ top: top, behavior: 'smooth' });
      }
    });
  });

})();

