(() => {
  const $ = (sel, root = document) => root.querySelector(sel);
  const $$ = (sel, root = document) => [...root.querySelectorAll(sel)];
  const base = document.body.dataset.base || '';
  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const euro = new Intl.NumberFormat('nl-NL', { style: 'currency', currency: 'EUR', maximumFractionDigits: 0 });

  const store = {
    get(key) {
      try {
        const value = JSON.parse(localStorage.getItem(key) || '[]');
        return Array.isArray(value) ? value.filter((id) => Number.isInteger(id) && id > 0) : [];
      } catch {
        return [];
      }
    },
    set(key, list) {
      try {
        localStorage.setItem(key, JSON.stringify(list));
      } catch {}
    },
  };
  const KEYS = { fav: 'rw:favorites', compare: 'rw:compare', recent: 'rw:recent' };
  const MAX_COMPARE = 3;

  let toastTimer;
  function toast(message) {
    const el = $('[data-toast]');
    if (!el) return;
    el.textContent = message;
    el.classList.add('is-visible');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => el.classList.remove('is-visible'), 2600);
  }

  function initHeader() {
    const header = $('[data-header]');
    const toggle = $('[data-menu-toggle]');
    const nav = $('[data-nav]');
    if (header) {
      const onScroll = () => header.classList.toggle('is-scrolled', window.scrollY > 8);
      onScroll();
      window.addEventListener('scroll', onScroll, { passive: true });
    }
    if (toggle && nav) {
      const close = () => {
        nav.classList.remove('is-open');
        toggle.setAttribute('aria-expanded', 'false');
      };
      toggle.addEventListener('click', () => {
        const open = !nav.classList.contains('is-open');
        nav.classList.toggle('is-open', open);
        toggle.setAttribute('aria-expanded', String(open));
      });
      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && nav.classList.contains('is-open')) {
          close();
          toggle.focus();
        }
      });
    }
  }

  function updateBadge() {
    const badge = $('[data-count="favorites"]');
    if (!badge) return;
    const count = store.get(KEYS.fav).length;
    const changed = badge.textContent !== String(count);
    badge.textContent = String(count);
    badge.hidden = count === 0;
    if (changed && count > 0) {
      badge.classList.remove('is-bumped');
      void badge.offsetWidth;
      badge.classList.add('is-bumped');
    }
  }

  function syncToggles() {
    const favs = store.get(KEYS.fav);
    const compare = store.get(KEYS.compare);
    $$('[data-fav]').forEach((btn) => btn.setAttribute('aria-pressed', String(favs.includes(Number(btn.dataset.fav)))));
    $$('[data-compare]').forEach((btn) => btn.setAttribute('aria-pressed', String(compare.includes(Number(btn.dataset.compare)))));
    updateTray();
  }

  function updateTray() {
    const tray = $('[data-compare-tray]');
    if (!tray) return;
    const list = store.get(KEYS.compare);
    const onComparePage = !!$('[data-compare-page]');
    tray.classList.toggle('is-visible', list.length > 0 && !onComparePage);
    $('[data-compare-label]', tray).textContent = list.length === 1 ? '1 auto geselecteerd' : `${list.length} auto’s geselecteerd`;
    const link = $('[data-compare-link]', tray);
    link.href = `${base}/vergelijken?ids=${list.join(',')}`;
    link.setAttribute('aria-disabled', String(list.length < 2));
    link.textContent = list.length < 2 ? 'Kies nog een auto' : 'Vergelijk';
  }

  function initToggles() {
    document.addEventListener('click', (e) => {
      const fav = e.target.closest('[data-fav]');
      if (fav) {
        e.preventDefault();
        const id = Number(fav.dataset.fav);
        let list = store.get(KEYS.fav);
        const on = !list.includes(id);
        list = on ? [id, ...list].slice(0, 50) : list.filter((x) => x !== id);
        store.set(KEYS.fav, list);
        syncToggles();
        updateBadge();
        toast(on ? 'Bewaard in je favorieten' : 'Verwijderd uit je favorieten');
        if (!on) removeFromFavoritesPage(id);
        return;
      }
      const cmp = e.target.closest('[data-compare]');
      if (cmp) {
        e.preventDefault();
        const id = Number(cmp.dataset.compare);
        let list = store.get(KEYS.compare);
        if (list.includes(id)) {
          list = list.filter((x) => x !== id);
          toast('Uit de vergelijking gehaald');
        } else if (list.length >= MAX_COMPARE) {
          toast(`Je kunt maximaal ${MAX_COMPARE} auto’s vergelijken`);
          return;
        } else {
          list = [...list, id];
          toast(list.length === 1 ? 'Kies nog een auto om te vergelijken' : 'Toegevoegd aan de vergelijking');
        }
        store.set(KEYS.compare, list);
        syncToggles();
        return;
      }
      if (e.target.closest('[data-compare-clear]')) {
        store.set(KEYS.compare, []);
        syncToggles();
      }
      const link = e.target.closest('[data-compare-link][aria-disabled="true"]');
      if (link) {
        e.preventDefault();
        toast('Kies minimaal twee auto’s');
      }
    });
    window.addEventListener('storage', () => {
      syncToggles();
      updateBadge();
    });
  }

  async function fetchCards(ids) {
    if (!ids.length) return '';
    const res = await fetch(`${base}/api/kaarten?ids=${ids.join(',')}`, { headers: { 'X-Requested-With': 'fetch' } });
    if (!res.ok) throw new Error('Request failed');
    return res.text();
  }

  async function initFavoritesPage() {
    const page = $('[data-favorites-page]');
    if (!page) return;
    const list = $('[data-favorites-list]', page);
    const empty = $('[data-favorites-empty]', page);
    const ids = store.get(KEYS.fav);
    if (!ids.length) {
      list.hidden = true;
      empty.hidden = false;
      return;
    }
    try {
      list.innerHTML = await fetchCards(ids);
      syncToggles();
      if (!list.children.length) {
        list.hidden = true;
        empty.hidden = false;
      }
    } catch {
      list.innerHTML = '<p class="muted">Je favorieten konden niet worden geladen. Herlaad de pagina.</p>';
    }
  }

  function removeFromFavoritesPage(id) {
    const page = $('[data-favorites-page]');
    if (!page) return;
    const card = $(`.car-card[data-car-id="${id}"]`, page);
    card?.remove();
    if (!$('.car-card', page)) {
      $('[data-favorites-list]', page).hidden = true;
      $('[data-favorites-empty]', page).hidden = false;
    }
  }

  function initComparePage() {
    const page = $('[data-compare-page]');
    if (!page) return;
    const stored = store.get(KEYS.compare);
    if (page.dataset.hasIds === '0' && stored.length) {
      window.location.replace(`${base}/vergelijken?ids=${stored.join(',')}`);
      return;
    }
    const current = $$('[data-compare-remove]', page).map((b) => Number(b.dataset.compareRemove));
    if (current.length) store.set(KEYS.compare, current);
    syncToggles();
    page.addEventListener('click', (e) => {
      const btn = e.target.closest('[data-compare-remove]');
      if (!btn) return;
      const list = store.get(KEYS.compare).filter((x) => x !== Number(btn.dataset.compareRemove));
      store.set(KEYS.compare, list);
      window.location.href = list.length ? `${base}/vergelijken?ids=${list.join(',')}` : `${base}/vergelijken?ids=`;
    });
  }

  async function initRecent() {
    const view = $('[data-car-view]');
    if (view) {
      const id = Number(view.dataset.carView);
      store.set(KEYS.recent, [id, ...store.get(KEYS.recent).filter((x) => x !== id)].slice(0, 8));
    }
    const section = $('[data-recent]');
    if (!section) return;
    const ids = store.get(KEYS.recent);
    if (!ids.length) return;
    try {
      const html = await fetchCards(ids);
      if (!html.trim()) return;
      $('[data-recent-list]', section).innerHTML = html;
      section.hidden = false;
      syncToggles();
    } catch {}
  }

  function setOdometer(odo, value) {
    const digits = String(Math.max(0, Math.min(999, value))).padStart(2, '0').split('');
    let cells = $$('.odo__digit', odo);
    while (cells.length < digits.length) {
      const cell = cells[0].cloneNode(true);
      odo.prepend(cell);
      cells = $$('.odo__digit', odo);
    }
    while (cells.length > digits.length) {
      cells[0].remove();
      cells = $$('.odo__digit', odo);
    }
    cells.forEach((cell, i) => {
      const strip = $('.odo__strip', cell);
      strip.style.transform = `translateY(-${Number(digits[i]) * 1.35}em)`;
    });
  }

  function initFinder() {
    const form = $('[data-finder]');
    if (!form) return;
    const odo = $('[data-odo]', form);
    const label = $('[data-odo-label]', form);
    $$('[data-digit]', odo).forEach((strip) => (strip.style.transform = `translateY(-${Number(strip.dataset.digit) * 1.35}em)`));
    let controller;
    form.addEventListener('change', async () => {
      const params = new URLSearchParams();
      new FormData(form).forEach((v, k) => v && params.set(k, v));
      params.set('aantal', '1');
      controller?.abort();
      controller = new AbortController();
      try {
        const res = await fetch(`${base}/occasions?${params}`, { headers: { 'X-Requested-With': 'fetch' }, signal: controller.signal });
        const data = await res.json();
        setOdometer(odo, data.total);
        label.textContent = `Toon ${data.total} auto’s`;
      } catch {}
    });
  }

  function catalogUrl(form) {
    const data = new FormData(form);
    const params = new URLSearchParams();
    const lists = {};
    for (const [key, value] of data) {
      if (!value) continue;
      if (key.endsWith('[]')) {
        const k = key.slice(0, -2);
        (lists[k] ||= []).push(value);
      } else {
        params.set(key, value);
      }
    }
    Object.entries(lists).forEach(([k, v]) => params.set(k, v.join(',')));
    if (params.get('sorteer') === 'nieuwste') params.delete('sorteer');
    const sort = $('#sort');
    if (sort && sort.value !== 'nieuwste') params.set('sorteer', sort.value);
    const order = ['zoek', 'merk', 'model', 'prijs_min', 'prijs_max', 'bouwjaar_min', 'bouwjaar_max', 'km_max', 'brandstof', 'transmissie', 'carrosserie', 'sorteer', 'pagina'];
    const sorted = new URLSearchParams();
    order.forEach((k) => params.has(k) && sorted.set(k, params.get(k)));
    const qs = sorted.toString();
    return `${base}/occasions${qs ? `?${qs}` : ''}`;
  }

  function initCatalog() {
    const form = $('[data-filters]');
    const results = $('[data-results]');
    if (!form || !results) return;
    const brand = $('[data-brand-select]', form);
    const model = $('[data-model-select]', form);
    const models = JSON.parse($('[data-models-json]', form)?.textContent || '{}');
    const scrim = $('[data-scrim]');
    const applyCount = $('[data-apply-count]', form);
    let controller;
    let debounce;

    const setLoading = (on) => {
      results.classList.toggle('is-loading', on);
      results.setAttribute('aria-busy', String(on));
    };

    async function load(url, { push = false, scroll = false } = {}) {
      controller?.abort();
      controller = new AbortController();
      setLoading(true);
      try {
        const res = await fetch(url, { headers: { 'X-Requested-With': 'fetch' }, signal: controller.signal });
        if (!res.ok) throw new Error(String(res.status));
        const html = await res.text();
        const swap = () => {
          results.innerHTML = html;
          syncToggles();
        };
        if (document.startViewTransition && !reduceMotion && scroll) {
          document.startViewTransition(swap);
        } else {
          swap();
        }
        const count = $('[data-result-count]', results)?.dataset.resultCount;
        if (applyCount && count !== undefined) applyCount.textContent = count;
        history[push ? 'pushState' : 'replaceState']({ catalog: true }, '', url);
        if (scroll) {
          const top = results.getBoundingClientRect().top + window.scrollY - 90;
          if (window.scrollY > top) window.scrollTo({ top, behavior: reduceMotion ? 'auto' : 'smooth' });
        }
      } catch (err) {
        if (err.name !== 'AbortError') window.location.href = url;
      } finally {
        setLoading(false);
      }
    }

    const fillModels = () => {
      const list = models[brand.value] || [];
      model.innerHTML = '';
      const all = new Option('Alle modellen', '');
      model.add(all);
      list.forEach((m) => model.add(new Option(`${m.name} (${m.total})`, m.slug)));
      model.disabled = !brand.value;
    };

    form.addEventListener('change', (e) => {
      if (e.target === brand) fillModels();
      if (e.target.name === 'zoek') return;
      load(catalogUrl(form));
    });
    form.addEventListener('input', (e) => {
      if (e.target.name !== 'zoek') return;
      clearTimeout(debounce);
      debounce = setTimeout(() => load(catalogUrl(form)), 350);
    });
    form.addEventListener('submit', (e) => {
      e.preventDefault();
      closeSheet();
      load(catalogUrl(form), { scroll: true });
    });

    results.addEventListener('change', (e) => {
      if (e.target.id === 'sort') load(catalogUrl(form));
    });
    results.addEventListener('click', (e) => {
      const link = e.target.closest('[data-page-link]');
      if (!link) return;
      e.preventDefault();
      const url = new URL(link.href);
      syncFormFromUrl(url);
      load(link.href, { push: true, scroll: true });
    });
    results.addEventListener('click', (e) => {
      if (e.target.closest('[data-filters-open]')) openSheet();
    });

    function syncFormFromUrl(url) {
      const p = url.searchParams;
      form.reset();
      $$('input[type="checkbox"]', form).forEach((c) => (c.checked = false));
      ['zoek', 'merk', 'prijs_min', 'prijs_max', 'bouwjaar_min', 'bouwjaar_max', 'km_max'].forEach((k) => {
        const el = form.elements[k];
        if (el) el.value = p.get(k) || '';
      });
      fillModels();
      model.value = p.get('model') || '';
      const trans = p.get('transmissie') || '';
      $$('input[name="transmissie"]', form).forEach((r) => (r.checked = r.value === trans));
      ['brandstof', 'carrosserie'].forEach((k) => {
        const values = (p.get(k) || '').split(',');
        $$(`input[name="${k}[]"]`, form).forEach((c) => (c.checked = values.includes(c.value)));
      });
    }

    window.addEventListener('popstate', () => {
      const url = new URL(window.location.href);
      syncFormFromUrl(url);
      load(url.href);
    });

    let lastFocus;
    function openSheet() {
      lastFocus = document.activeElement;
      form.classList.add('is-open');
      scrim?.classList.add('is-open');
      document.body.style.overflow = 'hidden';
      $$('[data-filters-open]').forEach((b) => b.setAttribute('aria-expanded', 'true'));
      $('[data-filters-close]', form)?.focus();
    }
    function closeSheet() {
      if (!form.classList.contains('is-open')) return;
      form.classList.remove('is-open');
      scrim?.classList.remove('is-open');
      document.body.style.overflow = '';
      $$('[data-filters-open]').forEach((b) => b.setAttribute('aria-expanded', 'false'));
      lastFocus?.focus?.();
    }
    $('[data-filters-close]', form)?.addEventListener('click', closeSheet);
    scrim?.addEventListener('click', closeSheet);
    document.addEventListener('keydown', (e) => e.key === 'Escape' && closeSheet());
  }

  function initGallery() {
    const gallery = $('[data-gallery]');
    if (!gallery) return;
    const track = $('[data-gallery-track]', gallery);
    const slides = $$('.gallery__slide', track);
    const prev = $('[data-gallery-prev]', gallery);
    const next = $('[data-gallery-next]', gallery);
    const counter = $('[data-gallery-counter]', gallery);
    const thumbs = $$('[data-gallery-thumb]', gallery);
    const dots = $$('.gallery__dots span', gallery);
    const lightbox = $('[data-lightbox]', gallery);
    const lbTrack = $('[data-lightbox-track]', gallery);
    const lbCounter = $('[data-lightbox-counter]', gallery);
    let index = 0;

    const goTo = (i, smooth = true) => {
      index = Math.max(0, Math.min(slides.length - 1, i));
      track.scrollTo({ left: index * track.clientWidth, behavior: smooth && !reduceMotion ? 'smooth' : 'auto' });
    };
    const update = () => {
      counter.textContent = `${index + 1} / ${slides.length}`;
      if (prev) prev.disabled = index === 0;
      if (next) next.disabled = index === slides.length - 1;
      thumbs.forEach((t, i) => {
        if (i === index) {
          t.setAttribute('aria-current', 'true');
          t.scrollIntoView({ block: 'nearest', inline: 'nearest', behavior: reduceMotion ? 'auto' : 'smooth' });
        } else {
          t.removeAttribute('aria-current');
        }
      });
      dots.forEach((d, i) => d.classList.toggle('is-active', i === index));
    };
    let ticking = false;
    track.addEventListener('scroll', () => {
      if (ticking) return;
      ticking = true;
      requestAnimationFrame(() => {
        const i = Math.round(track.scrollLeft / track.clientWidth);
        if (i !== index) {
          index = i;
          update();
        }
        ticking = false;
      });
    }, { passive: true });
    prev?.addEventListener('click', () => goTo(index - 1));
    next?.addEventListener('click', () => goTo(index + 1));
    thumbs.forEach((t) => t.addEventListener('click', () => goTo(Number(t.dataset.galleryThumb))));
    track.addEventListener('keydown', (e) => {
      if (e.key === 'ArrowRight') { e.preventDefault(); goTo(index + 1); }
      if (e.key === 'ArrowLeft') { e.preventDefault(); goTo(index - 1); }
      if (e.key === 'Enter') openLightbox(index);
    });

    const lbItems = lbTrack ? $$('li', lbTrack) : [];
    const lbUpdate = () => {
      const i = Math.round(lbTrack.scrollLeft / lbTrack.clientWidth);
      lbCounter.textContent = `${i + 1} / ${lbItems.length}`;
    };
    function openLightbox(i) {
      if (!lightbox?.showModal) return;
      lightbox.showModal();
      document.body.style.overflow = 'hidden';
      requestAnimationFrame(() => {
        lbTrack.scrollTo({ left: i * lbTrack.clientWidth, behavior: 'auto' });
        lbUpdate();
      });
    }
    lbTrack?.addEventListener('scroll', () => requestAnimationFrame(lbUpdate), { passive: true });
    lightbox?.addEventListener('keydown', (e) => {
      const i = Math.round(lbTrack.scrollLeft / lbTrack.clientWidth);
      if (e.key === 'ArrowRight') lbTrack.scrollTo({ left: (i + 1) * lbTrack.clientWidth, behavior: 'smooth' });
      if (e.key === 'ArrowLeft') lbTrack.scrollTo({ left: (i - 1) * lbTrack.clientWidth, behavior: 'smooth' });
    });
    lightbox?.addEventListener('close', () => {
      document.body.style.overflow = '';
      const i = Math.round(lbTrack.scrollLeft / lbTrack.clientWidth);
      goTo(i, false);
    });
    $('[data-lightbox-close]', gallery)?.addEventListener('click', () => lightbox.close());
    $('[data-gallery-zoom]', gallery)?.addEventListener('click', () => openLightbox(index));
    track.addEventListener('click', (e) => {
      const img = e.target.closest('img[data-index]');
      if (img) openLightbox(Number(img.dataset.index));
    });
    window.addEventListener('resize', () => goTo(index, false));
    update();
  }

  function initCalculator() {
    const calc = $('[data-calc]');
    if (!calc) return;
    const price = Number(calc.dataset.price);
    const rate = Number(calc.dataset.rate);
    const minLoan = Number(calc.dataset.minLoan);
    const down = $('[data-calc-down]', calc);
    const downOut = $('[data-calc-down-out]', calc);
    const monthlyEl = $('[data-calc-monthly]', calc);
    const loanEl = $('[data-calc-loan]', calc);
    const totalEl = $('[data-calc-total]', calc);
    let shown = 0;
    let frame;

    const monthly = (loan, months) => {
      if (loan <= 0) return 0;
      const r = rate / 100 / 12;
      return r === 0 ? loan / months : (loan * r) / (1 - (1 + r) ** -months);
    };
    const animateTo = (value) => {
      cancelAnimationFrame(frame);
      if (reduceMotion) {
        shown = value;
        monthlyEl.textContent = euro.format(value);
        return;
      }
      const start = shown;
      const t0 = performance.now();
      const step = (t) => {
        const p = Math.min(1, (t - t0) / 380);
        shown = start + (value - start) * (1 - (1 - p) ** 3);
        monthlyEl.textContent = euro.format(shown);
        if (p < 1) frame = requestAnimationFrame(step);
      };
      frame = requestAnimationFrame(step);
    };
    const update = () => {
      const d = Number(down.value);
      const term = Number($('[data-calc-term]:checked', calc)?.value || 60);
      const loan = Math.max(0, price - d);
      const m = loan >= minLoan ? monthly(loan, term) : 0;
      downOut.textContent = euro.format(d);
      loanEl.textContent = euro.format(loan);
      totalEl.textContent = loan >= minLoan ? euro.format(m * term) : 'Leenbedrag te laag';
      animateTo(m);
    };
    shown = monthly(price - Number(down.value), Number($('[data-calc-term]:checked', calc)?.value || 60));
    calc.addEventListener('input', update);
    calc.addEventListener('submit', (e) => e.preventDefault());
  }

  function initTabs() {
    const wrap = $('[data-tabs]');
    if (!wrap) return;
    const tabs = $$('[role="tab"]', wrap);
    const select = (name, focus = false) => {
      tabs.forEach((tab) => {
        const on = tab.dataset.tab === name;
        tab.setAttribute('aria-selected', String(on));
        tab.tabIndex = on ? 0 : -1;
        $(`#${tab.getAttribute('aria-controls')}`).hidden = !on;
        if (on && focus) tab.focus();
      });
    };
    tabs.forEach((tab) => {
      tab.tabIndex = tab.getAttribute('aria-selected') === 'true' ? 0 : -1;
      tab.addEventListener('click', () => select(tab.dataset.tab));
      tab.addEventListener('keydown', (e) => {
        if (!['ArrowLeft', 'ArrowRight'].includes(e.key)) return;
        const i = tabs.indexOf(tab);
        const nextTab = tabs[(i + (e.key === 'ArrowRight' ? 1 : -1) + tabs.length) % tabs.length];
        select(nextTab.dataset.tab, true);
      });
    });
    document.addEventListener('click', (e) => {
      const link = e.target.closest('[data-open-form]');
      if (!link) return;
      if (tabs.length) select(link.dataset.openForm);
      setTimeout(() => {
        const panel = tabs.length ? $(`#${$(`[data-tab="${link.dataset.openForm}"]`, wrap).getAttribute('aria-controls')}`) : wrap;
        $('input:not([type="hidden"]), textarea', panel)?.focus({ preventScroll: true });
      }, reduceMotion ? 0 : 450);
    });
  }

  function initSlots() {
    const date = $('[data-date-input]');
    if (!date) return;
    const weekday = $('[data-slots="weekday"]');
    const saturday = $('[data-slots="saturday"]');
    const hint = date.parentElement.querySelector('.field__hint');
    const defaultHint = hint?.textContent;
    const apply = () => {
      if (!date.value) {
        saturday.hidden = true;
        weekday.hidden = false;
        $('legend', weekday).textContent = 'Tijd';
        return;
      }
      const day = new Date(`${date.value}T12:00:00`).getDay();
      const isSat = day === 6;
      const isSun = day === 0;
      weekday.hidden = isSat;
      saturday.hidden = !isSat;
      $('legend', weekday).textContent = 'Tijd';
      $('legend', saturday).textContent = 'Tijd';
      $$('input', isSat ? weekday : saturday).forEach((i) => (i.checked = false));
      $$('input', weekday).concat($$('input', saturday)).forEach((i) => (i.disabled = isSun));
      if (hint) hint.textContent = isSun ? 'Op zondag zijn we gesloten. Kies een andere dag.' : defaultHint;
      date.setAttribute('aria-invalid', String(isSun));
    };
    date.addEventListener('change', apply);
    apply();
  }

  function initForms() {
    $$('[data-request-form]').forEach((form) => {
      form.addEventListener('submit', () => {
        const btn = $('button[type="submit"]', form);
        if (!btn) return;
        btn.disabled = true;
        btn.textContent = btn.dataset.submitLabel || 'Bezig…';
      });
    });
    const errors = $('[data-form-errors]');
    if (errors) {
      const section = errors.closest('section') || errors;
      section.scrollIntoView({ block: 'start' });
      $('[aria-invalid="true"]', errors.closest('form'))?.focus({ preventScroll: true });
    }
    window.addEventListener('pageshow', (e) => {
      if (!e.persisted) return;
      $$('[data-request-form] button[type="submit"]').forEach((b) => (b.disabled = false));
    });
  }

  function initMobileBar() {
    const bar = $('[data-mobile-bar]');
    const target = $('.summary__actions') || $('.summary');
    if (!bar || !target) return;
    const contact = $('#contact-auto');
    let pastSummary = false;
    let inContact = false;
    const render = () => bar.classList.toggle('is-visible', pastSummary && !inContact);
    new IntersectionObserver(([entry]) => {
      pastSummary = !entry.isIntersecting && entry.boundingClientRect.top < 0;
      render();
    }).observe(target);
    if (contact) {
      new IntersectionObserver(([entry]) => {
        inContact = entry.isIntersecting;
        render();
      }, { threshold: 0.15 }).observe(contact);
    }
  }

  function initShare() {
    document.addEventListener('click', async (e) => {
      const btn = e.target.closest('[data-share]');
      if (!btn) return;
      const data = { title: btn.dataset.shareTitle, url: window.location.href.split('#')[0] };
      if (navigator.share) {
        try {
          await navigator.share(data);
        } catch {}
        return;
      }
      try {
        await navigator.clipboard.writeText(data.url);
        toast('Link gekopieerd');
      } catch {
        toast('Kopiëren lukte niet. Kopieer de link uit de adresbalk.');
      }
    });
  }

  function initViewTransitions() {
    window.addEventListener('pageswap', (e) => {
      if (!e.viewTransition) return;
      const to = e.activation?.entry?.url;
      if (!to) return;
      const card = $$('.car-card').find((c) => to.endsWith($('a', c)?.getAttribute('href') || '#none'));
      const img = card && $('[data-vt-photo]', card);
      if (img) img.style.viewTransitionName = 'car-photo';
    });
    window.addEventListener('pagereveal', (e) => {
      if (!e.viewTransition) return;
      const img = $('.gallery [data-vt-photo]');
      if (!img) return;
      img.style.viewTransitionName = 'car-photo';
      e.viewTransition.finished.finally(() => (img.style.viewTransitionName = ''));
    });
  }

  initHeader();
  initToggles();
  syncToggles();
  updateBadge();
  initFinder();
  initCatalog();
  initGallery();
  initCalculator();
  initTabs();
  initSlots();
  initForms();
  initMobileBar();
  initShare();
  initFavoritesPage();
  initComparePage();
  initRecent();
  initViewTransitions();
})();
