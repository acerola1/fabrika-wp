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

  // ========== QUERY PARAM: PRE-FILL PRODUCT FIELD ==========
  (function() {
    var params = new URLSearchParams(window.location.search);
    var termek = params.get('termek');
    if (!termek) return;

    var nev = params.get('nev');
    var displayValue = decodeURIComponent(termek);
    if (nev) {
      displayValue += ' \u2013 ' + decodeURIComponent(nev);
    }

    var kapcsolat = document.getElementById('kapcsolat');
    var input = null;
    if (kapcsolat) {
      input =
        kapcsolat.querySelector('input#termek') ||
        kapcsolat.querySelector('input[name="termek"]') ||
        kapcsolat.querySelector('input[name="your-termek"]') ||
        kapcsolat.querySelector('input[name="product"]');
    }
    if (input) {
      input.value = displayValue;
      input.dispatchEvent(new Event('input', { bubbles: true }));
      input.dispatchEvent(new Event('change', { bubbles: true }));
    }

    // Auto-scroll to contact section on load when query param is present.
    var hash = window.location.hash;
    if (hash === '#kapcsolat' || hash === '') {
      setTimeout(function() {
        var target = document.getElementById('kapcsolat');
        if (!target) return;
        var offset = 64;
        var top = target.getBoundingClientRect().top + window.scrollY - offset;
        window.scrollTo({ top: top, behavior: 'smooth' });
      }, 150);
    }
  })();

  // ========== CATALOG FILTER BAR (6-3 mock parity) ==========
  (function() {
    // Only run on the /termekek archive, where the filter bar exists.
    var filterBar = document.getElementById('filter-bar');
    if (!filterBar) return;

    var filterButtons = document.querySelectorAll('.filter-btn');
    var productCards = document.querySelectorAll('.product-card');
    var noResults = document.getElementById('no-results');

    if (!filterButtons.length || !productCards.length || !noResults) return;

    function normalizeFilterToken(raw) {
      if (!raw || raw === 'all') return 'all';
      var token = String(raw);
      var tokens = [];
      filterButtons.forEach(function(btn) {
        var val = btn.getAttribute('data-filter');
        if (val) tokens.push(val);
      });
      if (tokens.indexOf(token) !== -1) return token;
      var needle = token.toLowerCase().replace(/[^a-z0-9]+/g, '');
      var best = '';
      var bestScore = -1;
      tokens.forEach(function(val) {
        if (!val || val === 'all') return;
        var cand = val.toLowerCase().replace(/[^a-z0-9]+/g, '');
        if (!cand || !needle) return;
        if (cand === needle) {
          best = val;
          bestScore = 10000;
          return;
        }
        if (needle.indexOf(cand) !== -1 || cand.indexOf(needle) !== -1) {
          if (cand.length > bestScore) {
            bestScore = cand.length;
            best = val;
          }
        }
      });
      return best || token;
    }

    function applyFilter(filter) {
      var visibleCount = 0;

      // Update button states
      filterButtons.forEach(function(btn) {
        if (btn.getAttribute('data-filter') === filter) {
          btn.classList.add('active');
        } else {
          btn.classList.remove('active');
        }
      });

      // Show/hide cards
      productCards.forEach(function(card) {
        var categories = card.getAttribute('data-categories') || '';
        if (filter === 'all' || categories.indexOf(filter) !== -1) {
          card.style.display = '';
          visibleCount++;
        } else {
          card.style.display = 'none';
        }
      });

      if (visibleCount === 0) noResults.classList.remove('hidden');
      else noResults.classList.add('hidden');
    }

    // Click handlers (prevent full reload, update URL like the mock)
    filterButtons.forEach(function(btn) {
      btn.addEventListener('click', function(e) {
        var filter = btn.getAttribute('data-filter') || 'all';
        applyFilter(filter);

        try {
          var url = new URL(window.location.href);
          if (filter === 'all') url.searchParams.delete('kategoria');
          else url.searchParams.set('kategoria', filter);
          history.replaceState(null, '', url);
        } catch (err) {
          // Ignore URL update errors (very old browsers).
        }

        // If it's a link, stop navigation (we already updated URL).
        if (e && typeof e.preventDefault === 'function') e.preventDefault();
      });
    });

    // Apply filter from URL on page load
    try {
      var params = new URLSearchParams(window.location.search);
      var kategoriaParam = params.get('kategoria') || params.get('cimke') || params.get('tag');
      if (kategoriaParam) {
        var normalized = normalizeFilterToken(kategoriaParam);
        applyFilter(normalized);
        if (normalized !== kategoriaParam && normalized !== 'all') {
          var url = new URL(window.location.href);
          url.searchParams.set('kategoria', normalized);
          history.replaceState(null, '', url);
        }
      }
    } catch (err) {
      // Ignore.
    }

    // Global reset (used by the "no results" button markup, like in the mock).
    window.resetFilter = function() {
      applyFilter('all');
      try {
        var url = new URL(window.location.href);
        url.searchParams.delete('kategoria');
        url.searchParams.delete('cimke');
        url.searchParams.delete('tag');
        history.replaceState(null, '', url);
      } catch (err) {
        // Ignore.
      }
    };
  })();

  // ========== CATALOG MODAL / CAROUSEL (2.16b, 2.16c, 2.16d) ==========
  (function() {
    // Only runs on the termékek archive (where the filter bar and modal exist).
    var modal = document.getElementById('product-modal');
    if (!modal) return;

    var modalBackdrop = document.getElementById('modal-backdrop');
    var modalClose    = document.getElementById('modal-close');
    var modalPrev     = document.getElementById('modal-prev');
    var modalNext     = document.getElementById('modal-next');
    var modalImg      = document.getElementById('modal-img');
    var modalProductId = document.getElementById('modal-product-id');
    var modalTags     = document.getElementById('modal-tags');
    var modalTitle    = document.getElementById('modal-title');
    var modalPrice    = document.getElementById('modal-price');
    var modalDesc     = document.getElementById('modal-desc');
    var modalCta      = document.getElementById('modal-cta');
    var modalCounter  = document.getElementById('modal-counter');

    if (!modalBackdrop || !modalClose || !modalImg || !modalTitle) return;

    var productCards = document.querySelectorAll('.product-card');
    var currentModalIndex = 0;
    var visibleProductsList = [];
    var originalUrl = window.location.href;

    function getVisibleProducts() {
      return Array.from(productCards).filter(function(c) {
        return c.style.display !== 'none';
      });
    }

    function pushTermekUrl(id) {
      try {
        var url = new URL(window.location.href);
        if (id) { url.searchParams.set('termek', id); } else { url.searchParams.delete('termek'); }
        history.replaceState(null, '', url);
      } catch (e) { /* ignore */ }
    }

    function renderModal(card) {
      var img    = card.querySelector('img');
      var id     = card.getAttribute('data-id') || '';
      var name   = card.querySelector('h3') ? card.querySelector('h3').textContent.trim() : '';
      var priceEl = card.querySelector('p.text-lg');
      var price  = priceEl ? priceEl.textContent.trim() : '';
      var desc   = card.getAttribute('data-desc') || '';
      var tags   = Array.from(card.querySelectorAll('.tag-badge')).map(function(t) {
        return t.textContent.trim();
      });
      var ctaEl  = card.querySelector('a[href*="#kapcsolat"]');
      var ctaHref = ctaEl ? ctaEl.getAttribute('href') : '#kapcsolat';

      var largeUrl = card.getAttribute('data-img-large') || (img ? img.src : '');
      if (modalImg)       { modalImg.src = largeUrl; modalImg.alt = img ? (img.alt || name) : name; }
      if (modalProductId) { modalProductId.textContent = id; }
      if (modalTitle)     { modalTitle.textContent = name; }
      if (modalPrice)     { modalPrice.textContent = price; }
      if (modalDesc)      { modalDesc.textContent = desc; }
      if (modalCta)       { modalCta.href = ctaHref; }
      if (modalTags)      {
        modalTags.innerHTML = tags.map(function(t) {
          return '<span class="tag-badge">' + t.replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</span>';
        }).join(' ');
      }
      if (modalCounter) {
        modalCounter.textContent = (currentModalIndex + 1) + ' / ' + visibleProductsList.length;
      }
    }

    function openModal(card) {
      visibleProductsList = getVisibleProducts();
      currentModalIndex = visibleProductsList.indexOf(card);
      if (currentModalIndex === -1) { currentModalIndex = 0; }
      renderModal(visibleProductsList[currentModalIndex]);
      modal.style.display = 'flex';
      requestAnimationFrame(function() {
        modal.classList.add('modal-open');
      });
      document.body.classList.add('modal-body-lock');
      if (modalClose) { modalClose.focus(); }
      pushTermekUrl(visibleProductsList[currentModalIndex].getAttribute('data-id') || '');
    }

    function closeModal() {
      modal.classList.remove('modal-open');
      document.body.classList.remove('modal-body-lock');
      setTimeout(function() {
        modal.style.display = 'none';
      }, 260);
      try { history.replaceState(null, '', originalUrl); } catch (e) { /* ignore */ }
    }

    function navigateModal(direction) {
      if (visibleProductsList.length < 2) { return; }
      currentModalIndex = (currentModalIndex + direction + visibleProductsList.length) % visibleProductsList.length;
      renderModal(visibleProductsList[currentModalIndex]);
      pushTermekUrl(visibleProductsList[currentModalIndex].getAttribute('data-id') || '');
    }

    // Kártyára kattintva megnyílik (az "Érdekel" gomb kivételével)
    productCards.forEach(function(card) {
      card.style.cursor = 'pointer';
      card.addEventListener('click', function(e) {
        if (e.target.closest('a[href*="#kapcsolat"]')) { return; }
        openModal(card);
      });
    });

    // Navigációs gombok
    if (modalPrev) { modalPrev.addEventListener('click', function() { navigateModal(-1); }); }
    if (modalNext) { modalNext.addEventListener('click', function() { navigateModal(1); }); }

    // Bezárás: X gomb + backdrop
    modalClose.addEventListener('click', closeModal);
    if (modalBackdrop) { modalBackdrop.addEventListener('click', closeModal); }

    // Billentyűzet: ESC + nyilak (2.16c)
    document.addEventListener('keydown', function(e) {
      if (!modal.classList.contains('modal-open')) { return; }
      if (e.key === 'Escape')     { e.preventDefault(); closeModal(); }
      if (e.key === 'ArrowLeft')  { e.preventDefault(); navigateModal(-1); }
      if (e.key === 'ArrowRight') { e.preventDefault(); navigateModal(1); }
    });

    // Mobil swipe
    var touchStartX = 0;
    var touchStartY = 0;
    modal.addEventListener('touchstart', function(e) {
      touchStartX = e.touches[0].clientX;
      touchStartY = e.touches[0].clientY;
    }, { passive: true });
    modal.addEventListener('touchend', function(e) {
      var dx = e.changedTouches[0].clientX - touchStartX;
      var dy = e.changedTouches[0].clientY - touchStartY;
      if (Math.abs(dx) > Math.abs(dy) && Math.abs(dx) > 50) {
        navigateModal(dx < 0 ? 1 : -1);
      }
    }, { passive: true });

    // Oldalbetöltéskor ?termek= param alapján megnyitja a megfelelő terméket
    try {
      var params = new URLSearchParams(window.location.search);
      var termekParam = params.get('termek');
      if (termekParam) {
        var targetCard = null;
        productCards.forEach(function(c) {
          if ((c.getAttribute('data-id') || '') === termekParam) { targetCard = c; }
        });
        if (targetCard) {
          originalUrl = window.location.href;
          setTimeout(function() { openModal(targetCard); }, 350);
        }
      }
    } catch (e) { /* ignore */ }
  })();

})();
