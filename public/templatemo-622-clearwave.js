/*
TemplateMo 622 Clearwave
https://templatemo.com/tm-622-clearwave
Free for personal and commercial use
*/

/* Smooth Scroll (JS-driven, overrides CSS) */
document.querySelectorAll('a[href^="#"]').forEach((link) => {
  link.addEventListener('click', (event) => {
    const href = link.getAttribute('href');

    if (href === '#') {
      event.preventDefault();
      window.scrollTo({ top: 0, behavior: 'smooth' });
      return;
    }

    const target = href ? document.querySelector(href) : null;
    if (!target) {
      return;
    }

    event.preventDefault();
    target.scrollIntoView({ behavior: 'smooth' });
  });
});

/* NAV SCROLL */
const nav = document.getElementById('mainNav');
const siteTopbar = document.querySelector('.site-topbar');

if (nav) {
  let lastScrollY = window.scrollY;

  window.addEventListener(
    'scroll',
    () => {
      const currentScrollY = window.scrollY;
      nav.classList.toggle('scrolled', currentScrollY > 40);

      if (siteTopbar) {
        const scrollingDown = currentScrollY > lastScrollY;
        const shouldHideTopbar = scrollingDown && currentScrollY > 120;
        document.body.classList.toggle('topbar-hidden', shouldHideTopbar);
      }

      lastScrollY = currentScrollY;
    },
    { passive: true }
  );
}

/* MOBILE MENU */
const hamburger = document.getElementById('hamburger');
const mobileMenu = document.getElementById('mobileMenu');

if (hamburger && mobileMenu) {
  const openMobileMenu = () => {
    hamburger.classList.add('open');
    mobileMenu.classList.add('open');
    hamburger.setAttribute('aria-expanded', 'true');
    document.body.style.overflow = 'hidden';
  };

  const closeMobileMenu = () => {
    hamburger.classList.remove('open');
    mobileMenu.classList.remove('open');
    hamburger.setAttribute('aria-expanded', 'false');
    document.body.style.overflow = '';
  };

  hamburger.addEventListener('click', () => {
    mobileMenu.classList.contains('open') ? closeMobileMenu() : openMobileMenu();
  });

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
      closeMobileMenu();
    }
  });

  mobileMenu.querySelectorAll('a').forEach((link) => {
    link.addEventListener('click', () => closeMobileMenu());
  });
}

/* SCROLL REVEAL */
const revealEls = document.querySelectorAll('.reveal');
if (revealEls.length > 0 && 'IntersectionObserver' in window) {
  const revealObserver = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
          revealObserver.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.12, rootMargin: '0px 0px -40px 0px' }
  );

  revealEls.forEach((el) => revealObserver.observe(el));
}

/* STAT COUNTERS */
function animateCounter(el) {
  const target = parseFloat(el.dataset.target || '0');
  const decimal = el.dataset.decimal;
  const duration = 1800;
  const start = performance.now();

  function step(now) {
    const elapsed = now - start;
    const progress = Math.min(elapsed / duration, 1);
    const eased = 1 - Math.pow(1 - progress, 4);
    const value = eased * target;

    el.textContent = decimal ? value.toFixed(1) : Math.floor(value);

    if (progress < 1) {
      requestAnimationFrame(step);
    } else {
      el.textContent = decimal ? target.toFixed(1) : String(target);
    }
  }

  requestAnimationFrame(step);
}

const statsGrids = document.querySelectorAll('.stats-grid');
if (statsGrids.length > 0 && 'IntersectionObserver' in window) {
  const statObserver = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.querySelectorAll('.stat-num').forEach(animateCounter);
          statObserver.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.4 }
  );

  statsGrids.forEach((el) => statObserver.observe(el));
}

/* 3D CAROUSEL */
const cards = Array.from(document.querySelectorAll('.phone-card'));
const stage = document.getElementById('carouselStage');
const dotsContainer = document.getElementById('carouselDots');
const nextButton = document.getElementById('carouselNext');
const prevButton = document.getElementById('carouselPrev');
const zoomPipsEl = document.getElementById('zoomPips');
const zoomInBtn = document.getElementById('zoomIn');
const zoomOutBtn = document.getElementById('zoomOut');

if (
  cards.length > 0 &&
  stage &&
  dotsContainer &&
  nextButton &&
  prevButton &&
  zoomPipsEl &&
  zoomInBtn &&
  zoomOutBtn
) {
  const totalCards = cards.length;
  let currentCenter = Math.min(2, totalCards - 1);
  let autoTimer = null;
  let isAnimating = false;

  const zoomSteps = [
    { pw: 160, g1: 178, g2: 316, gh: 450, sh: 420 },
    { pw: 200, g1: 222, g2: 395, gh: 560, sh: 520 },
    { pw: 240, g1: 266, g2: 474, gh: 670, sh: 620 },
    { pw: 280, g1: 310, g2: 553, gh: 780, sh: 720 },
    { pw: 320, g1: 354, g2: 632, gh: 890, sh: 820 },
  ];
  let zoomLevel = 2;

  const posConfig = {
    center: [0, 0, 1, 1],
    left1: [-1, 28, 0.82, 1],
    right1: [1, -28, 0.82, 1],
    left2: [-1, 45, 0.64, 0.55],
    right2: [1, -45, 0.64, 0.55],
    'hidden-left': [-1, 60, 0.48, 0],
    'hidden-right': [1, -60, 0.48, 0],
  };

  const posGap = {
    center: 0,
    left1: 'g1',
    right1: 'g1',
    left2: 'g2',
    right2: 'g2',
    'hidden-left': 'gh',
    'hidden-right': 'gh',
  };

  function applyCardStyles(suppressTransition) {
    const step = zoomSteps[zoomLevel];

    cards.forEach((card) => {
      const pos = card.dataset.pos;
      const config = posConfig[pos];
      if (!config) {
        return;
      }

      const gapKey = posGap[pos];
      const translateX = config[0] * (gapKey ? step[gapKey] : 0);
      const shell = card.querySelector('.phone-shell');

      if (suppressTransition) {
        card.style.transition = 'none';
        if (shell) {
          shell.style.transition = 'none';
        }
      }

      card.style.width = `${step.pw}px`;
      card.style.transform = `translateX(${translateX}px) rotateY(${config[1]}deg) scale(${config[2]})`;
      card.style.opacity = String(config[3]);

      if (shell) {
        shell.style.width = `${step.pw}px`;
        shell.style.boxShadow =
          pos === 'center'
            ? '0 0 0 1px rgba(150,175,170,0.6), 0 40px 80px rgba(13,30,28,0.22), 0 0 48px rgba(26,122,110,0.12), inset 0 1px 0 rgba(255,255,255,0.6)'
            : '';
      }

      if (suppressTransition) {
        requestAnimationFrame(() => {
          card.style.transition = '';
          if (shell) {
            shell.style.transition = '';
          }
        });
      }
    });

    stage.style.height = `${step.sh}px`;
  }

  function getPositionForOffset(cardIndex, centerIndex, total) {
    let offset = cardIndex - centerIndex;

    while (offset > Math.floor(total / 2)) {
      offset -= total;
    }
    while (offset < -Math.floor(total / 2)) {
      offset += total;
    }

    const posMap = { '-2': 'left2', '-1': 'left1', '0': 'center', '1': 'right1', '2': 'right2' };
    return posMap[String(offset)] || (offset < 0 ? 'hidden-left' : 'hidden-right');
  }

  function updatePositions() {
    cards.forEach((card, index) => {
      card.dataset.pos = getPositionForOffset(index, currentCenter, totalCards);
    });

    document.querySelectorAll('.carousel-dot').forEach((dot, index) => {
      dot.classList.toggle('active', index === currentCenter);
    });

    applyCardStyles(false);
  }

  function goTo(index) {
    if (isAnimating) {
      return;
    }

    isAnimating = true;
    currentCenter = ((index % totalCards) + totalCards) % totalCards;
    updatePositions();

    setTimeout(() => {
      isAnimating = false;
    }, 700);
  }

  function next() {
    goTo((currentCenter + 1) % totalCards);
  }

  function prev() {
    goTo((currentCenter - 1 + totalCards) % totalCards);
  }

  cards.forEach((_, index) => {
    const dot = document.createElement('div');
    dot.className = `carousel-dot${index === currentCenter ? ' active' : ''}`;
    dot.addEventListener('click', () => goTo(index));
    dotsContainer.appendChild(dot);
  });

  nextButton.addEventListener('click', () => {
    next();
    resetAuto();
  });

  prevButton.addEventListener('click', () => {
    prev();
    resetAuto();
  });

  cards.forEach((card, index) => {
    card.addEventListener('click', () => {
      if (card.dataset.pos !== 'center') {
        goTo(index);
        resetAuto();
      }
    });
  });

  function startAuto() {
    autoTimer = setInterval(next, 3500);
  }

  function stopAuto() {
    clearInterval(autoTimer);
  }

  function resetAuto() {
    stopAuto();
    startAuto();
  }

  stage.addEventListener('mouseenter', stopAuto);
  stage.addEventListener('mouseleave', startAuto);

  let touchStartX = 0;
  stage.addEventListener(
    'touchstart',
    (event) => {
      touchStartX = event.touches[0].clientX;
    },
    { passive: true }
  );
  stage.addEventListener('touchend', (event) => {
    const diff = touchStartX - event.changedTouches[0].clientX;
    if (Math.abs(diff) > 40) {
      diff > 0 ? next() : prev();
      resetAuto();
    }
  });

  zoomSteps.forEach((_, index) => {
    const pip = document.createElement('div');
    pip.className = `zoom-pip${index === zoomLevel ? ' active' : ''}`;
    pip.addEventListener('click', () => setZoom(index));
    zoomPipsEl.appendChild(pip);
  });

  function setZoom(level) {
    zoomLevel = Math.max(0, Math.min(zoomSteps.length - 1, level));
    applyCardStyles(true);

    zoomPipsEl.querySelectorAll('.zoom-pip').forEach((pip, index) => {
      pip.classList.toggle('active', index === zoomLevel);
    });

    zoomOutBtn.disabled = zoomLevel === 0;
    zoomInBtn.disabled = zoomLevel === zoomSteps.length - 1;
  }

  zoomInBtn.addEventListener('click', () => setZoom(zoomLevel + 1));
  zoomOutBtn.addEventListener('click', () => setZoom(zoomLevel - 1));

  updatePositions();
  setZoom(zoomLevel);
  startAuto();
}

/* PRICING TOGGLE */
const pricingToggle = document.getElementById('pricingToggle');
const monthlyLabel = document.getElementById('monthlyLabel');
const annualLabel = document.getElementById('annualLabel');

if (pricingToggle && monthlyLabel && annualLabel) {
  const prices = { starter: [20, 13], pro: [60, 39], ent: [150, 98] };
  const annualTotals = { starter: 156, pro: 468, ent: 1176 };
  let isAnnual = false;

  function updatePricing() {
    const idx = isAnnual ? 1 : 0;
    const starter = document.getElementById('price-starter');
    const pro = document.getElementById('price-pro');
    const ent = document.getElementById('price-ent');
    const starterNote = document.getElementById('annual-note-starter');
    const proNote = document.getElementById('annual-note-pro');
    const entNote = document.getElementById('annual-note-ent');

    if (starter) starter.textContent = String(prices.starter[idx]);
    if (pro) pro.textContent = String(prices.pro[idx]);
    if (ent) ent.textContent = String(prices.ent[idx]);
    if (starterNote) starterNote.textContent = isAnnual ? `$${annualTotals.starter} billed annually` : '\u00a0';
    if (proNote) proNote.textContent = isAnnual ? `$${annualTotals.pro} billed annually` : '\u00a0';
    if (entNote) entNote.textContent = isAnnual ? `$${annualTotals.ent} billed annually` : '\u00a0';

    monthlyLabel.classList.toggle('active', !isAnnual);
    annualLabel.classList.toggle('active', isAnnual);
    pricingToggle.classList.toggle('annual', isAnnual);
    pricingToggle.setAttribute('aria-checked', String(isAnnual));
  }

  pricingToggle.addEventListener('click', () => {
    isAnnual = !isAnnual;
    updatePricing();
  });

  pricingToggle.addEventListener('keydown', (event) => {
    if (event.key === 'Enter' || event.key === ' ') {
      event.preventDefault();
      isAnnual = !isAnnual;
      updatePricing();
    }
  });
}

/* FAQ ACCORDION */
const faqItems = document.querySelectorAll('.faq-item');
if (faqItems.length > 0) {
  let allOpen = false;

  function toggleFaq(item) {
    const isOpen = item.classList.contains('open');
    item.classList.toggle('open', !isOpen);

    const question = item.querySelector('.faq-question');
    if (question) {
      question.setAttribute('aria-expanded', String(!isOpen));
    }
  }

  faqItems.forEach((item) => {
    const question = item.querySelector('.faq-question');
    if (!question) {
      return;
    }

    question.addEventListener('click', () => toggleFaq(item));
    question.addEventListener('keydown', (event) => {
      if (event.key === 'Enter' || event.key === ' ') {
        event.preventDefault();
        toggleFaq(item);
      }
    });
  });

  const faqToggleAllBtn = document.getElementById('faqToggleAll');
  const faqToggleIcon = document.getElementById('faqToggleIcon');

  if (faqToggleAllBtn && faqToggleIcon) {
    faqToggleAllBtn.addEventListener('click', () => {
      allOpen = !allOpen;

      faqItems.forEach((item) => {
        item.classList.toggle('open', allOpen);
        const question = item.querySelector('.faq-question');
        if (question) {
          question.setAttribute('aria-expanded', String(allOpen));
        }
      });

      faqToggleIcon.textContent = allOpen ? '-' : '+';

      const textNode = Array.from(faqToggleAllBtn.childNodes).find(
        (node) => node.nodeType === Node.TEXT_NODE
      );
      if (textNode) {
        textNode.textContent = allOpen ? ' Collapse all' : ' Expand all';
      }
    });
  }
}

/* HEADER SLIDER */
const headerSlider = document.getElementById('headerSlider');
const headerSlidePrev = document.getElementById('headerSlidePrev');
const headerSlideNext = document.getElementById('headerSlideNext');
const headerSlideDots = document.getElementById('headerSlideDots');

if (headerSlider && headerSlidePrev && headerSlideNext && headerSlideDots) {
  const slides = Array.from(headerSlider.querySelectorAll('.header-slide'));
  const panels = Array.from(document.querySelectorAll('.hero-slide-panel'));
  const heroFrame = headerSlider.closest('.hero-frame');
  const totalSlides = slides.length;

  if (totalSlides > 0) {
    let currentIndex = Math.max(
      0,
      slides.findIndex((slide) => slide.classList.contains('is-active'))
    );
    let autoTimer = null;

    function syncSlides(index) {
      slides.forEach((slide, slideIndex) => {
        const isActive = slideIndex === index;
        slide.classList.toggle('is-active', isActive);
        slide.setAttribute('aria-hidden', String(!isActive));
      });

      panels.forEach((panel, panelIndex) => {
        const isActive = panelIndex === index;
        panel.classList.toggle('is-active', isActive);
        panel.setAttribute('aria-hidden', String(!isActive));
      });

      headerSlideDots.querySelectorAll('.header-slider-dot').forEach((dot, dotIndex) => {
        const isActive = dotIndex === index;
        dot.classList.toggle('active', isActive);
        dot.setAttribute('aria-current', isActive ? 'true' : 'false');
      });
    }

    function goTo(index) {
      currentIndex = (index + totalSlides) % totalSlides;
      syncSlides(currentIndex);
    }

    function nextSlide() {
      goTo(currentIndex + 1);
    }

    function prevSlide() {
      goTo(currentIndex - 1);
    }

    function stopAuto() {
      if (autoTimer) {
        clearInterval(autoTimer);
        autoTimer = null;
      }
    }

    function startAuto() {
      stopAuto();
      autoTimer = window.setInterval(nextSlide, 5200);
    }

    function resetAuto() {
      startAuto();
    }

    slides.forEach((slide, index) => {
      slide.setAttribute('aria-hidden', String(index !== currentIndex));

      const dot = document.createElement('button');
      dot.type = 'button';
      dot.className = `header-slider-dot${index === currentIndex ? ' active' : ''}`;
      dot.setAttribute('aria-label', `Slide ${index + 1}`);
      dot.setAttribute('aria-current', index === currentIndex ? 'true' : 'false');
      dot.addEventListener('click', () => {
        goTo(index);
        resetAuto();
      });
      headerSlideDots.appendChild(dot);
    });

    panels.forEach((panel, index) => {
      panel.setAttribute('aria-hidden', String(index !== currentIndex));
    });

    headerSlidePrev.addEventListener('click', () => {
      prevSlide();
      resetAuto();
    });

    headerSlideNext.addEventListener('click', () => {
      nextSlide();
      resetAuto();
    });

    if (heroFrame) {
      heroFrame.addEventListener('mouseenter', stopAuto);
      heroFrame.addEventListener('mouseleave', startAuto);
      heroFrame.addEventListener('focusin', stopAuto);
      heroFrame.addEventListener('focusout', (event) => {
        if (heroFrame.contains(event.relatedTarget)) {
          return;
        }

        startAuto();
      });
    }

    document.addEventListener('visibilitychange', () => {
      if (document.hidden) {
        stopAuto();
      } else {
        startAuto();
      }
    });

    syncSlides(currentIndex);
    startAuto();
  }
}
