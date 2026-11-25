(() => {
  const $ = (sel, root = document) => root.querySelector(sel);
  const $$ = (sel, root = document) => [...root.querySelectorAll(sel)];
  const token = $('meta[name="csrf-token"]')?.content || '';
  const LABELS = {
    brand: 'Merk', model: 'Model', year: 'Bouwjaar', first_registration: 'Eerste toelating', apk_until: 'APK',
    color: 'Kleur', body_type: 'Carrosserie', fuel: 'Brandstof', power_kw: 'Vermogen', engine_cc: 'Cilinderinhoud',
    doors: 'Deuren', seats: 'Zitplaatsen',
  };

  let toastTimer;
  function toast(message) {
    const el = $('[data-toast]');
    if (!el) return;
    el.textContent = message;
    el.classList.add('is-visible');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => el.classList.remove('is-visible'), 2800);
  }

  async function post(url, body) {
    const res = await fetch(url, {
      method: 'POST',
      headers: { 'X-CSRF-Token': token, 'X-Requested-With': 'fetch', Accept: 'application/json' },
      body,
      credentials: 'same-origin',
    });
    let data = {};
    try {
      data = await res.json();
    } catch {
      data = { ok: false, error: 'De server gaf een onverwacht antwoord.' };
    }
    if (res.status === 401) {
      toast('Je bent uitgelogd. Log opnieuw in.');
    }
    return { status: res.status, data };
  }

  function initMeasures() {
    requestAnimationFrame(() => {
      $$('[data-height]').forEach((el) => (el.style.height = `${Number(el.dataset.height)}%`));
      $$('[data-width]').forEach((el) => (el.style.width = `${Number(el.dataset.width)}%`));
    });
  }

  function initAutosubmit() {
    $$('[data-autosubmit]').forEach((el) => el.addEventListener('change', () => el.form?.requestSubmit()));
    $('[data-focus-me]')?.focus();
    const flash = $('[data-flash]');
    if (flash) setTimeout(() => flash.remove(), 6000);
  }

  function initQuickStatus() {
    $$('[data-quick-status]').forEach((form) => {
      const select = $('select', form);
      select.addEventListener('change', async () => {
        const fd = new FormData(form);
        select.disabled = true;
        const { data } = await post(form.action, fd);
        select.disabled = false;
        if (data.ok) {
          select.className = `select select--status status-select--${data.status}`;
          toast(`Status gewijzigd naar ${data.label}`);
        } else {
          toast(data.error || 'Opslaan lukte niet.');
        }
      });
    });
  }

  function confirmDialog(text) {
    const dialog = $('[data-confirm-dialog]');
    if (!dialog?.showModal) return Promise.resolve(window.confirm(text));
    $('[data-confirm-text]', dialog).textContent = text;
    dialog.showModal();
    return new Promise((resolve) => {
      dialog.addEventListener('close', () => resolve(dialog.returnValue === 'ok'), { once: true });
    });
  }

  function initConfirm() {
    $$('form[data-confirm]').forEach((form) => {
      form.addEventListener('submit', async (e) => {
        if (form.dataset.confirmed) return;
        e.preventDefault();
        if (await confirmDialog(form.dataset.confirm)) {
          form.dataset.confirmed = '1';
          form.requestSubmit();
        }
      });
    });
  }

  function initCarForm() {
    const form = $('#car-form');
    if (!form) return;
    const saveBar = $('.save-bar', form);
    let dirty = false;
    form.addEventListener('input', () => {
      if (dirty) return;
      dirty = true;
      saveBar?.classList.add('is-dirty');
      const note = $('[data-dirty-note]', form);
      if (note) note.textContent = 'Niet opgeslagen wijzigingen';
    });
    form.addEventListener('submit', () => (dirty = false));
    window.addEventListener('beforeunload', (e) => {
      if (dirty) e.preventDefault();
    });

    const brand = $('#brand', form);
    const modelList = $('#model-list', form);
    const models = JSON.parse($('[data-models-json]', form)?.textContent || '{}');
    const fillModels = () => {
      modelList.innerHTML = '';
      const key = Object.keys(models).find((b) => b.toLowerCase() === brand.value.trim().toLowerCase());
      (models[key] || []).forEach((m) => modelList.append(new Option(m)));
    };
    brand.addEventListener('input', fillModels);
    fillModels();

    const desc = $('#description', form);
    const counter = $('[data-char-count="description"]');
    const updateCount = () => (counter.textContent = `${desc.value.length} / 5000`);
    desc.addEventListener('input', updateCount);
    updateCount();

    const featureCount = $('[data-feature-count]');
    const updateFeatures = () => {
      const n = $$('input[name="features[]"]:checked', form).length;
      featureCount.textContent = n === 1 ? '1 optie gekozen' : `${n} opties gekozen`;
    };
    form.addEventListener('change', (e) => e.target.name === 'features[]' && updateFeatures());
    updateFeatures();
  }

  function initPlateLookup() {
    const box = $('[data-plate-lookup]');
    if (!box) return;
    const input = $('[data-plate-input]', box);
    const button = $('[data-plate-button]', box);
    const result = $('[data-plate-result]', box);
    const form = box.closest('form');

    const formatPlate = (v) => v.toUpperCase().replace(/[^A-Z0-9]/g, '').slice(0, 6);
    input.addEventListener('blur', () => {
      const raw = formatPlate(input.value);
      if (raw.length !== 6) return;
      const groups = raw.match(/[A-Z]+|\d+/g) || [raw];
      input.value = groups.length === 3 ? groups.join('-') : groups.length === 2 ? splitTwo(groups) : raw;
    });
    function splitTwo([a, b]) {
      if (a.length === 4) return `${a.slice(0, 2)}-${a.slice(2)}-${b}`;
      if (b.length === 4) return `${a}-${b.slice(0, 2)}-${b.slice(2)}`;
      return `${a}-${b}`;
    }
    input.addEventListener('keydown', (e) => {
      if (e.key === 'Enter') {
        e.preventDefault();
        lookup();
      }
    });
    button.addEventListener('click', lookup);

    function showError(message) {
      result.className = 'lookup-result is-error';
      result.textContent = message;
    }

    async function lookup() {
      const plate = formatPlate(input.value);
      if (plate.length !== 6) {
        showError('Een Nederlands kenteken heeft 6 letters en cijfers, bijvoorbeeld K-482-TB.');
        input.focus();
        return;
      }
      button.disabled = true;
      $('span', button).textContent = 'Bezig met ophalen…';
      result.className = 'lookup-result';
      result.textContent = 'De RDW wordt geraadpleegd…';
      const fd = new FormData();
      fd.append('plate', plate);
      try {
        const { data } = await post(box.dataset.endpoint, fd);
        if (!data.ok) {
          showError(data.error || 'Ophalen lukte niet.');
          return;
        }
        input.value = data.formatted;
        apply(data.data);
      } catch {
        showError('Geen verbinding met de server. Controleer je internet en probeer opnieuw.');
      } finally {
        button.disabled = false;
        $('span', button).textContent = 'Gegevens ophalen';
      }
    }

    function apply(data) {
      const filled = [];
      Object.entries(data).forEach(([key, value]) => {
        if (value === null || value === '' || !(key in LABELS)) return;
        const field = $(`[data-rdw="${key}"]`, form);
        if (!field) return;
        if (field.tagName === 'SELECT' && ![...field.options].some((o) => o.value === String(value))) return;
        field.value = String(value);
        field.classList.remove('is-filled');
        void field.offsetWidth;
        field.classList.add('is-filled');
        filled.push(LABELS[key]);
      });
      form.dispatchEvent(new Event('input', { bubbles: true }));
      $('#brand', form)?.dispatchEvent(new Event('input', { bubbles: true }));

      result.className = 'lookup-result';
      result.textContent = '';
      const title = document.createElement('strong');
      title.textContent = filled.length
        ? `${filled.length} velden ingevuld${data.cached ? ' (uit cache van vandaag)' : ''}`
        : 'De RDW kende dit kenteken, maar er waren geen bruikbare gegevens.';
      result.append(title);
      if (filled.length) {
        const ul = document.createElement('ul');
        filled.forEach((name) => {
          const li = document.createElement('li');
          li.textContent = name;
          ul.append(li);
        });
        result.append(ul);
      }
      const notes = [];
      if (data.fuel === 'hybrid') notes.push('Hybride gevonden. Is het een plug-in hybride? Pas de brandstof dan aan.');
      if (!data.body_type && data.body_raw) notes.push(`Carrosserie “${data.body_raw}” is niet automatisch gekoppeld. Kies hem zelf.`);
      notes.push('Prijs, kilometerstand en transmissie vul je zelf in.');
      notes.forEach((text) => {
        const p = document.createElement('p');
        p.className = 'note';
        p.textContent = text;
        result.append(p);
      });
    }
  }

  function initPhotos() {
    const section = $('[data-photos]');
    if (!section) return;
    const grid = $('[data-photo-grid]', section);
    const input = $('[data-file-input]', section);
    const dropzone = $('[data-dropzone]', section);
    const progress = $('[data-progress]', section);
    const errorsBox = $('[data-upload-errors]', section);
    const empty = $('[data-photo-empty]', section);
    const countEl = $('[data-photo-count]', section);
    const max = Number(section.dataset.max);
    const ALLOWED = ['image/jpeg', 'image/png', 'image/webp'];
    const MAX_BYTES = 8 * 1024 * 1024;
    let saveTimer;

    const items = () => $$('.photo-item', grid);
    const refresh = () => {
      const n = items().length;
      countEl.textContent = `${n} / ${max}`;
      empty.hidden = n > 0;
      items().forEach((li, i) => ($('img', li).alt = `Foto ${i + 1}`));
    };

    function showErrors(list) {
      errorsBox.textContent = '';
      if (!list.length) return;
      const strong = document.createElement('strong');
      strong.textContent = list.length === 1 ? 'Eén foto is niet toegevoegd:' : `${list.length} foto’s zijn niet toegevoegd:`;
      const ul = document.createElement('ul');
      list.forEach((t) => {
        const li = document.createElement('li');
        li.textContent = t;
        ul.append(li);
      });
      errorsBox.append(strong, ul);
    }

    function upload(files) {
      const local = [];
      const valid = [];
      [...files].forEach((f) => {
        if (!ALLOWED.includes(f.type)) local.push(`${f.name} is geen JPG, PNG of WebP`);
        else if (f.size > MAX_BYTES) local.push(`${f.name} is groter dan 8 MB`);
        else valid.push(f);
      });
      const room = max - items().length;
      if (valid.length > room) {
        valid.splice(room).forEach((f) => local.push(`${f.name}: maximum van ${max} foto’s bereikt`));
      }
      showErrors(local);
      if (!valid.length) return;

      const batches = [];
      for (let i = 0; i < valid.length; i += 10) batches.push(valid.slice(i, i + 10));
      progress.hidden = false;
      const bar = $('span', progress);
      let done = 0;

      const sendBatch = (batch) => new Promise((resolve) => {
        const fd = new FormData();
        batch.forEach((f) => fd.append('photos[]', f));
        const xhr = new XMLHttpRequest();
        xhr.open('POST', section.dataset.upload);
        xhr.setRequestHeader('X-CSRF-Token', token);
        xhr.setRequestHeader('X-Requested-With', 'fetch');
        xhr.setRequestHeader('Accept', 'application/json');
        xhr.upload.addEventListener('progress', (e) => {
          if (e.lengthComputable) bar.style.width = `${((done + e.loaded / e.total) / batches.length) * 100}%`;
        });
        xhr.addEventListener('load', () => {
          let data = {};
          try { data = JSON.parse(xhr.responseText); } catch { data = { errors: ['De server gaf een onverwacht antwoord.'] }; }
          resolve(data);
        });
        xhr.addEventListener('error', () => resolve({ errors: ['De upload is mislukt door een verbindingsfout.'] }));
        xhr.send(fd);
      });

      (async () => {
        const allErrors = [...local];
        let added = 0;
        for (const batch of batches) {
          const data = await sendBatch(batch);
          done++;
          (data.added || []).forEach((photo) => {
            grid.append(photoItem(photo));
            added++;
          });
          if (data.error) allErrors.push(data.error);
          (data.errors || []).forEach((e) => allErrors.push(e));
        }
        progress.hidden = true;
        bar.style.width = '0';
        input.value = '';
        showErrors(allErrors);
        refresh();
        if (added) toast(added === 1 ? 'Foto toegevoegd' : `${added} foto’s toegevoegd`);
      })();
    }

    function photoItem(photo) {
      const li = $('[data-photo-template]', section).content.firstElementChild.cloneNode(true);
      li.dataset.photoId = String(photo.id);
      li.classList.add('is-new');
      $('img', li).src = photo.thumb;
      return li;
    }

    function scheduleSave() {
      refresh();
      clearTimeout(saveTimer);
      saveTimer = setTimeout(async () => {
        const fd = new FormData();
        fd.append('order', JSON.stringify(items().map((li) => Number(li.dataset.photoId))));
        const { data } = await post(section.dataset.reorder, fd);
        toast(data.ok ? 'Volgorde opgeslagen' : data.error || 'Volgorde opslaan lukte niet');
      }, 500);
    }

    input.addEventListener('change', () => upload(input.files));
    ['dragenter', 'dragover'].forEach((type) => dropzone.addEventListener(type, (e) => {
      if (!e.dataTransfer?.types.includes('Files')) return;
      e.preventDefault();
      dropzone.classList.add('is-over');
    }));
    ['dragleave', 'drop'].forEach((type) => dropzone.addEventListener(type, () => dropzone.classList.remove('is-over')));
    dropzone.addEventListener('drop', (e) => {
      if (!e.dataTransfer?.files.length) return;
      e.preventDefault();
      upload(e.dataTransfer.files);
    });

    let dragged = null;
    grid.addEventListener('dragstart', (e) => {
      dragged = e.target.closest('.photo-item');
      if (!dragged) return;
      dragged.classList.add('is-dragging');
      e.dataTransfer.effectAllowed = 'move';
      e.dataTransfer.setData('text/plain', dragged.dataset.photoId);
    });
    grid.addEventListener('dragover', (e) => {
      if (!dragged) return;
      e.preventDefault();
      const target = e.target.closest('.photo-item');
      $$('.is-target', grid).forEach((el) => el.classList.remove('is-target'));
      if (!target || target === dragged) return;
      target.classList.add('is-target');
      const rect = target.getBoundingClientRect();
      const after = e.clientX > rect.left + rect.width / 2;
      target[after ? 'after' : 'before'](dragged);
    });
    grid.addEventListener('drop', (e) => {
      if (dragged) e.preventDefault();
    });
    grid.addEventListener('dragend', () => {
      if (!dragged) return;
      dragged.classList.remove('is-dragging');
      $$('.is-target', grid).forEach((el) => el.classList.remove('is-target'));
      dragged = null;
      scheduleSave();
    });

    grid.addEventListener('click', async (e) => {
      const li = e.target.closest('.photo-item');
      if (!li) return;
      const move = e.target.closest('[data-move]');
      if (move) {
        const dir = Number(move.dataset.move);
        const sibling = dir < 0 ? li.previousElementSibling : li.nextElementSibling;
        if (sibling) {
          sibling[dir < 0 ? 'before' : 'after'](li);
          $(`[data-move="${dir}"]`, li)?.focus();
          scheduleSave();
        }
        return;
      }
      if (e.target.closest('[data-make-main]')) {
        grid.prepend(li);
        $('[data-move="1"]', li)?.focus();
        scheduleSave();
        return;
      }
      if (e.target.closest('[data-delete-photo]')) {
        if (!(await confirmDialog('Deze foto definitief verwijderen?'))) return;
        const { data } = await post(`${section.dataset.deleteBase}/${li.dataset.photoId}/verwijderen`, new FormData());
        if (data.ok) {
          li.remove();
          refresh();
          toast('Foto verwijderd');
        } else {
          toast(data.error || 'Verwijderen lukte niet');
        }
      }
    });
    refresh();
  }

  initMeasures();
  initAutosubmit();
  initQuickStatus();
  initConfirm();
  initCarForm();
  initPlateLookup();
  initPhotos();
})();
