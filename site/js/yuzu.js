/* menu.js */
;document.querySelectorAll(".bar, .lb-top, .fx-bar").forEach((header) => {
  const toggle = header.querySelector(".menu-toggle");
  const nav = header.querySelector("nav");
  if (!toggle || !nav) return;

  const setOpen = (open) => {
    header.classList.toggle("open", open);
    toggle.setAttribute("aria-expanded", String(open));
    toggle.setAttribute("aria-label", open ? "Close menu" : "Open menu");
  };

  toggle.addEventListener("click", () => {
    setOpen(!header.classList.contains("open"));
  });

  nav.querySelectorAll("a").forEach((link) => {
    link.addEventListener("click", () => setOpen(false));
  });

  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape" && header.classList.contains("open")) {
      setOpen(false);
    }
  });

  const navLinks = [...nav.querySelectorAll('a[href^="#"]')]
    .map((link) => {
      const id = link.getAttribute("href").slice(1);
      const target = id ? document.getElementById(id) : null;
      return target ? { id, link, target } : null;
    })
    .filter(Boolean);

  if (navLinks.length > 1) {
    const syncNav = () => {
      const vh = window.innerHeight;
      const atBottom = vh + window.scrollY >= document.documentElement.scrollHeight - 80;
      let activeId = "";
      for (const item of navLinks) {
        const threshold = item.id === "visit" ? vh * 0.65 : vh * 0.38;
        if (item.target.getBoundingClientRect().top <= threshold) {
          activeId = item.id;
        }
      }
      if (atBottom) {
        activeId = navLinks[navLinks.length - 1].id;
      }
      for (const item of navLinks) {
        if (item.id === activeId) {
          item.link.setAttribute("aria-current", "page");
        } else {
          item.link.removeAttribute("aria-current");
        }
      }
    };
    window.addEventListener("scroll", syncNav, { passive: true });
    window.addEventListener("resize", syncNav, { passive: true });
    syncNav();
  }
});

/* logo-home.js */
;;(() => {
  document.querySelectorAll('a.logo-home').forEach((link) => {
    link.addEventListener('click', (event) => {
      if (event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return
      let dest
      try {
        dest = new URL(link.href, window.location.href)
      } catch {
        return
      }
      if (dest.origin !== window.location.origin || dest.pathname !== window.location.pathname) return
      if (dest.hash && dest.hash !== '#') return
      event.preventDefault()
      const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches
      window.scrollTo({ top: 0, left: 0, behavior: reduce ? 'auto' : 'smooth' })
      if (window.location.hash) {
        history.replaceState(null, '', window.location.pathname + window.location.search)
      }
    })
  })
})()

/* visit.js */
;(() => {
  const params = new URLSearchParams(location.search)
  const keys = ['sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat']
  const names = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']

  let schedule = {
    week: [null, null, ['10:00', '20:00'], ['10:00', '20:00'], ['10:00', '20:00'], ['10:00', '20:00'], ['09:00', '18:00']],
    special: [],
  }
  try {
    const raw = document.getElementById('yuzu-hours')?.textContent
    if (raw) {
      const parsed = JSON.parse(raw)
      if (Array.isArray(parsed.week) && parsed.week.length === 7) schedule = { week: parsed.week, special: parsed.special || [] }
    }
  } catch {}

  const toHour = (t) => {
    const [h, m] = String(t).split(':').map(Number)
    return h + (m || 0) / 60
  }
  const clock = (h) => {
    const whole = Math.floor(h)
    const mins = Math.round((h - whole) * 60)
    const base = whole % 12 || 12
    return `${base}${mins ? `:${String(mins).padStart(2, '0')}` : ''}${whole < 12 ? 'am' : 'pm'}`
  }

  const parts = Object.fromEntries(
    new Intl.DateTimeFormat('en-GB', {
      timeZone: 'Europe/London',
      year: 'numeric',
      month: '2-digit',
      day: '2-digit',
      hour: 'numeric',
      minute: 'numeric',
      hourCycle: 'h23',
    })
      .formatToParts(new Date())
      .map((p) => [p.type, p.value]),
  )
  let today = new Date(Date.UTC(Number(parts.year), Number(parts.month) - 1, Number(parts.day)))
  let hour = Number(parts.hour) + Number(parts.minute) / 60
  if (/^\d{4}-\d{2}-\d{2}$/.test(params.get('date') || '')) today = new Date(`${params.get('date')}T00:00:00Z`)
  if (keys.includes(params.get('day'))) {
    const shift = (keys.indexOf(params.get('day')) - today.getUTCDay() + 7) % 7
    today = new Date(today.getTime() + shift * 864e5)
  }
  if (params.has('hour')) hour = Number(params.get('hour'))

  const iso = (d) => d.toISOString().slice(0, 10)
  const addDays = (d, n) => new Date(d.getTime() + n * 864e5)
  const todayIso = iso(today)

  const dayInfo = (d) => {
    const key = iso(d)
    const special = schedule.special.find((s) => s && s.from <= key && key <= (s.to || s.from))
    if (special) {
      return {
        hours: special.open && special.close ? [toHour(special.open), toHour(special.close)] : null,
        label: special.label || '',
      }
    }
    const week = schedule.week[d.getUTCDay()]
    return { hours: week ? [toHour(week[0]), toHour(week[1])] : null, label: '' }
  }

  const nextOpen = (from) => {
    for (let i = 1; i <= 21; i++) {
      const d = addDays(from, i)
      const info = dayInfo(d)
      if (info.hours) return { d, info, i }
    }
    return null
  }

  const todayInfo = dayInfo(today)
  let open = false
  let status
  const tag = todayInfo.label ? ` · ${todayInfo.label}` : ''
  if (todayInfo.hours && hour >= todayInfo.hours[0] && hour < todayInfo.hours[1]) {
    open = true
    status = `Open now · until ${clock(todayInfo.hours[1])}${tag}`
  } else if (todayInfo.hours && hour < todayInfo.hours[0]) {
    status = `Closed now · opens today at ${clock(todayInfo.hours[0])}${tag}`
  } else {
    const next = nextOpen(today)
    const lead = todayInfo.label && !todayInfo.hours ? `Closed today · ${todayInfo.label}` : 'Closed now'
    status = next
      ? `${lead} · opens ${next.i === 1 ? 'tomorrow' : names[next.d.getUTCDay()]} at ${clock(next.info.hours[0])}`
      : lead
  }
  document.querySelectorAll('[data-open-status]').forEach((el) => {
    el.textContent = status
    el.classList.toggle('is-open', open)
  })

  document.querySelectorAll('[data-special-to]').forEach((el) => {
    if ((el.getAttribute('data-special-to') || '') < todayIso) el.hidden = true
  })
  document.querySelectorAll('.visit-special').forEach((list) => {
    if (!list.querySelector('[data-special-to]:not([hidden])')) list.hidden = true
  })

  const offerDays = { 2: 'tue', 3: 'wed', 4: 'thu' }
  let offerDay = today.getUTCDay()
  if (!offerDays[offerDay]) {
    do offerDay = (offerDay + 1) % 7
    while (!offerDays[offerDay])
  }
  const key = offerDays[offerDay]
  const flag = offerDay === today.getUTCDay() ? 'Today' : names[offerDay]
  document.querySelectorAll(`.promo.${key}`).forEach((el) => {
    el.classList.add('is-today')
    el.dataset.flag = flag
    const row = el.closest('ul')
    const item = el.closest('li')
    if (row && item && row.scrollWidth > row.clientWidth) {
      const pad = parseFloat(getComputedStyle(row).paddingLeft) || 0
      row.scrollLeft += item.getBoundingClientRect().left - row.getBoundingClientRect().left - pad
    }
  })

  const detail = document.querySelector('[data-offer-detail]')
  if (detail) {
    const roll = document.querySelector('.promo-roll-6')
    const pricesBase = (detail.getAttribute('data-prices-href') || 'prices.html#cut').split('#')[0]
    const bookHref = detail.querySelector('[data-detail-book]')?.getAttribute('href') || ''
    const read = (promoKey) => {
      const src = document.querySelector(`[data-promo-detail="${promoKey}"]`)
      if (!src) return null
      const pick = (name) => src.querySelector(`[data-part="${name}"]`)?.textContent.trim() || ''
      const pricesHash = src.getAttribute('data-prices-hash') || ''
      const inSalon = src.hasAttribute('data-in-salon')
      return {
        kicker: pick('kicker'),
        headline: pick('headline'),
        text: pick('text'),
        terms: pick('terms'),
        bookLabel: src.getAttribute('data-book-label') || 'Book your appointment',
        bookHref: inSalon ? `${pricesBase}${pricesHash || '#cut'}` : bookHref,
        pricesHref: inSalon || !pricesHash ? '' : `${pricesBase}${pricesHash}`,
        external: !inSalon,
      }
    }

    const kickerEl = detail.querySelector('[data-detail-kicker]')
    const headlineEl = detail.querySelector('[data-detail-headline]')
    const textEl = detail.querySelector('[data-detail-text]')
    const termsEl = detail.querySelector('[data-detail-terms]')
    const bookEl = detail.querySelector('[data-detail-book]')
    const pricesEl = detail.querySelector('[data-detail-prices]')
    const buttons = [...document.querySelectorAll('[data-promo]')]
    let activePromo = ''

    const placeTray = () => {
      if (!roll || !activePromo || detail.hidden) return
      const idx = buttons.findIndex((b) => b.getAttribute('data-promo') === activePromo)
      if (idx < 0) return
      detail.dataset.col = String(idx % 3)
      if (window.innerWidth > 700) {
        const items = [...roll.querySelectorAll(':scope > li')]
        if (idx < 3 && items[2]) items[2].after(detail)
        else roll.appendChild(detail)
      } else {
        roll.after(detail)
      }
    }

    const closeTray = () => {
      detail.hidden = true
      activePromo = ''
      detail.removeAttribute('data-active')
      buttons.forEach((b) => {
        b.classList.remove('is-selected')
        b.setAttribute('aria-expanded', 'false')
      })
    }

    const openTray = (promoKey) => {
      const p = read(promoKey)
      if (!p) return
      activePromo = promoKey
      detail.setAttribute('data-active', promoKey)
      if (kickerEl) kickerEl.textContent = p.kicker
      if (headlineEl) headlineEl.textContent = p.headline
      if (textEl) textEl.textContent = p.text
      if (termsEl) termsEl.textContent = p.terms
      if (bookEl) {
        bookEl.textContent = p.bookLabel
        bookEl.setAttribute('href', p.bookHref)
        if (p.external) {
          bookEl.setAttribute('target', '_blank')
          bookEl.setAttribute('rel', 'noopener')
        } else {
          bookEl.removeAttribute('target')
          bookEl.removeAttribute('rel')
        }
      }
      if (pricesEl) {
        pricesEl.hidden = !p.pricesHref
        if (p.pricesHref) pricesEl.setAttribute('href', p.pricesHref)
      }
      detail.hidden = false
      placeTray()
      buttons.forEach((b) => {
        const match = b.getAttribute('data-promo') === promoKey
        b.classList.toggle('is-selected', match)
        b.setAttribute('aria-expanded', String(match))
      })
    }

    const hashByPromo = {
      tue: '#tuesdays',
      wed: '#wednesdays',
      thu: '#thursdays',
      refer: '#refer',
      rebook: '#rebook',
      blowdry: '#blowdry',
    }
    const promoByHash = Object.fromEntries(Object.entries(hashByPromo).map(([k, v]) => [v, k]))

    buttons.forEach((b) => {
      b.setAttribute('aria-expanded', 'false')
      b.addEventListener('click', () => {
        const k = b.getAttribute('data-promo')
        if (!detail.hidden && activePromo === k) {
          closeTray()
        } else {
          openTray(k)
          if (hashByPromo[k] && history.replaceState) history.replaceState(null, '', hashByPromo[k])
        }
      })
    })

    if (promoByHash[location.hash]) {
      openTray(promoByHash[location.hash])
      document.getElementById('offers')?.scrollIntoView()
    }

    window.addEventListener('resize', placeTray, { passive: true })
    detail.querySelector('[data-detail-close]')?.addEventListener('click', closeTray)
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && !detail.hidden) closeTray()
    })
  }

  const back = document.querySelector('a.blend-back[data-back]')
  if (back && !document.querySelector('.price-board')) {
    try {
      if (document.referrer) {
        const u = new URL(document.referrer)
        if (u.origin === location.origin && u.pathname !== location.pathname) {
          const labels = [
            [/\/prices(\.html|\/)?$/, '← Price list'],
            [/\/questions(\.html|\/)?$/, '← Questions'],
            [/\/patch(\.html|\/)?$/, '← Patch testing'],
            [/\/team(\.html|\/)?$/, '← Careers'],
            [/\/about(\.html|\/)?$/, '← About'],
            [/\/contact(\.html|\/)?$/, '← Contact'],
            [/\/terms(\.html|\/)?$/, '← Terms'],
            [/\/privacy(\.html|\/)?$/, '← Privacy'],
          ]
          for (const [re, text] of labels) {
            if (re.test(u.pathname)) {
              back.textContent = text
              back.setAttribute('href', document.referrer)
              break
            }
          }
        }
      }
    } catch {}
  }

  const sticky = document.querySelector('.blend-sticky')
  const visitCard = document.querySelector('.visit-card')
  if (sticky && visitCard) {
    const syncSticky = () => {
      const r = visitCard.getBoundingClientRect()
      const visible = r.top < window.innerHeight * 0.42 && r.bottom > 100
      sticky.classList.toggle('is-at-visit', visible)
    }
    window.addEventListener('scroll', syncSticky, { passive: true })
    window.addEventListener('resize', syncSticky, { passive: true })
    syncSticky()
  }

  document.querySelectorAll('.blend .looks, .blend .reviews, .blend .promo-roll-6').forEach((row) => {
    const items = [...row.querySelectorAll(':scope > li')]
    if (items.length < 2) return
    const dots = document.createElement('div')
    dots.className = 'scroll-dots'
    dots.setAttribute('aria-label', 'Carousel position')
    const btns = items.map((_, idx) => {
      const b = document.createElement('button')
      b.type = 'button'
      b.setAttribute('aria-label', `Item ${idx + 1} of ${items.length}`)
      if (idx === 0) b.setAttribute('aria-current', 'true')
      b.addEventListener('click', () => {
        const pad = parseFloat(getComputedStyle(row).paddingLeft) || 0
        row.scrollTo({
          left: row.scrollLeft + items[idx].getBoundingClientRect().left - row.getBoundingClientRect().left - pad,
          behavior: 'smooth',
        })
      })
      dots.appendChild(b)
      return b
    })
    row.after(dots)
    const syncDots = () => {
      if (row.scrollWidth <= row.clientWidth) return
      const rowLeft = row.getBoundingClientRect().left
      let best = 0
      let bestDist = Infinity
      items.forEach((li, i) => {
        const d = Math.abs(li.getBoundingClientRect().left - rowLeft)
        if (d < bestDist) {
          bestDist = d
          best = i
        }
      })
      btns.forEach((b, i) => {
        if (i === best) b.setAttribute('aria-current', 'true')
        else b.removeAttribute('aria-current')
      })
    }
    row.addEventListener('scroll', syncDots, { passive: true })
    window.addEventListener('resize', syncDots, { passive: true })
    setTimeout(syncDots, 60)
  })
})()

/* prices.js */
;(function () {
  var board = document.querySelector('.price-board');
  if (!board) return;

  var map = { '#cut': 'pm-cut', '#colour': 'pm-colour', '#highlights': 'pm-high', '#treatments': 'pm-treat' };
  var fromHash = map[location.hash];
  if (fromHash) {
    var hashInput = document.getElementById(fromHash);
    if (hashInput) hashInput.checked = true;
  }

  var back = document.querySelector('a.blend-back[data-back]');
  if (back) {
    var homeBase = back.getAttribute('href').split('#')[0];
    var fromServices = /^#(cut|colour|highlights|treatments|services)$/.test(location.hash);
    var ref = '';
    try {
      if (document.referrer) {
        var u = new URL(document.referrer);
        if (u.origin === location.origin) ref = u.pathname;
      }
    } catch (e) {}
    var labels = [
      [/\/patch(\.html|\/)?$/, '← Patch testing'],
      [/\/questions(\.html|\/)?$/, '← Questions'],
      [/\/team(\.html|\/)?$/, '← Join our team'],
      [/\/about(\.html|\/)?$/, '← About'],
      [/\/haircut-ealing(\.html|\/)?$/, '← Haircuts'],
      [/\/hair-colour-ealing(\.html|\/)?$/, '← Hair colour'],
      [/\/balayage-ealing(\.html|\/)?$/, '← Balayage'],
      [/\/hair-smoothing-ealing(\.html|\/)?$/, '← Smoothing'],
    ];
    if (fromServices) {
      back.textContent = '← Services';
      back.setAttribute('href', homeBase + '#services');
    } else {
      for (var i = 0; i < labels.length; i++) {
        if (labels[i][0].test(ref)) {
          back.textContent = labels[i][1];
          back.setAttribute('href', document.referrer);
          break;
        }
      }
    }
  }

  var desc = document.querySelector('[data-level-desc]');
  var rows = board.querySelectorAll('[data-levels]');
  var text = {
    '': 'Price ranges from Creative Director (CD) to Junior Stylist.',
    creative: 'Creative Director (CD): lead stylist and salon visionary.',
    senior: 'Senior Stylist: extensive experience, advanced cutting and colour.',
    stylist: 'Stylist: fully qualified, all cuts and colour.',
    junior: 'Junior Stylist: newly qualified, under senior guidance.',
  };
  var panelCut = board.querySelector('.panel-cut');

  function syncCutOpen() {
    if (board.hasAttribute('data-level')) return;
    if (panelCut) {
      var who = panelCut.getAttribute('data-who') || '';
      panelCut.querySelectorAll('details.price-row').forEach(function (d) {
        d.open = Boolean(who && d.getAttribute('data-who') === who);
      });
    }
    board.querySelectorAll('.panel-colour > details.price-row, .panel-high > details.price-row, .panel-treat > details.price-row').forEach(function (d) {
      d.open = true;
    });
  }

  function apply(level) {
    if (level) board.setAttribute('data-level', level);
    else board.removeAttribute('data-level');
    rows.forEach(function (row) {
      var fig = row.querySelector('.price-fig');
      if (!fig.dataset.all) fig.dataset.all = fig.textContent;
      var price = level ? JSON.parse(row.dataset.levels)[level] : fig.dataset.all;
      fig.innerHTML = price || '—<span class="vh"> not offered at this level</span>';
      row.classList.toggle('is-missing', !price);
      if (level && row.tagName === 'DETAILS') row.open = false;
    });
    if (!level) syncCutOpen();
    if (desc) desc.textContent = text[level];
  }

  var lastWho = (board.querySelector('input[name="who"]:checked') || {}).value || '';
  board.querySelectorAll('input[name="who"]').forEach(function (r) {
    r.addEventListener('click', function () {
      if (r.value && r.value === lastWho) {
        var allWho = board.querySelector('input[name="who"][value=""]');
        if (allWho && panelCut) {
          allWho.checked = true;
          lastWho = '';
          panelCut.removeAttribute('data-who');
          syncCutOpen();
        }
      } else {
        lastWho = r.value;
      }
    });
    r.addEventListener('change', function () {
      lastWho = r.value;
      if (!panelCut) return;
      if (r.value) panelCut.setAttribute('data-who', r.value);
      else panelCut.removeAttribute('data-who');
      syncCutOpen();
    });
  });

  var lastLevel = (document.querySelector('input[name="level"]:checked') || {}).value || '';
  document.querySelectorAll('input[name="level"]').forEach(function (input) {
    input.addEventListener('click', function () {
      if (input.value && input.value === lastLevel) {
        var allLvl = document.querySelector('input[name="level"][value=""]');
        if (allLvl) {
          allLvl.checked = true;
          lastLevel = '';
          apply('');
        }
      } else {
        lastLevel = input.value;
      }
    });
    input.addEventListener('change', function () {
      lastLevel = input.value;
      apply(input.value);
    });
  });

  var qp = new URLSearchParams(location.search);
  var qWho = qp.get('who');
  if (qWho && /^(ladies|gents|children)$/.test(qWho) && panelCut) {
    var whoRadio = board.querySelector('input[name="who"][value="' + qWho + '"]');
    if (whoRadio) {
      whoRadio.checked = true;
      lastWho = qWho;
      panelCut.setAttribute('data-who', qWho);
      syncCutOpen();
    }
  }
  var qLevel = qp.get('level');
  if (qLevel && /^(creative|senior|stylist|junior)$/.test(qLevel)) {
    var lvlRadio = document.querySelector('input[name="level"][value="' + qLevel + '"]');
    if (lvlRadio) {
      lvlRadio.checked = true;
      lastLevel = qLevel;
      apply(qLevel);
    }
  }

  var tabs = board.querySelector('.price-tabs');
  if (tabs) {
    var showTab = function (radio, behavior) {
      var label = tabs.querySelector('label[for="' + radio.id + '"]');
      if (label) tabs.scrollTo({ left: label.offsetLeft - tabs.offsetLeft - 18, behavior: behavior });
    };
    var hashByRadio = { 'pm-cut': '#cut', 'pm-colour': '#colour', 'pm-high': '#highlights', 'pm-treat': '#treatments' };
    board.querySelectorAll('.price-radio').forEach(function (radio) {
      radio.addEventListener('change', function () {
        showTab(radio, 'smooth');
        if (hashByRadio[radio.id] && history.replaceState) history.replaceState(null, '', hashByRadio[radio.id]);
      });
      if (radio.checked) showTab(radio, 'auto');
    });
  }

  board.addEventListener('click', function (e) {
    if (board.hasAttribute('data-level') && e.target.closest('.price-row[data-levels] > summary')) e.preventDefault();
  });
})();

/* forms.js */
;(() => {
  document.querySelectorAll('[data-year]').forEach((el) => {
    el.textContent = String(new Date().getFullYear())
  })

  const loadedAt = Date.now()
  document.querySelectorAll('[data-yuzu-form]').forEach((form) => {
    const stamp = form.querySelector('input[name="form_ts"]')
    if (stamp) stamp.value = String(Math.floor(loadedAt / 1000))

    form.addEventListener('submit', async (event) => {
      event.preventDefault()
      const box = form.closest('[data-form-box]')
      const thanks = box?.querySelector('[data-form-thanks]')
      const action = form.getAttribute('action') || 'send.php'
      const body = new FormData(form)
      const error = form.querySelector('[data-form-error]')
      const file = body.get('cv')
      const submitBtn = form.querySelector('button[type="submit"]')
      const origLabel = submitBtn ? submitBtn.textContent : ''
      if (error) error.hidden = true
      if (file instanceof File && file.size > 0) {
        const allowed = /\.(pdf|docx?|txt|rtf)$/i.test(file.name)
        if (!allowed || file.size > 5 * 1024 * 1024) {
          if (error) {
            error.hidden = false
            error.textContent = 'Please attach a PDF, Word or text file under 5 MB.'
          }
          return
        }
      }

      if (submitBtn) {
        submitBtn.disabled = true
        submitBtn.textContent = 'Sending…'
      }

      try {
        const response = await fetch(action, {
          method: 'POST',
          body,
          headers: { Accept: 'application/json' },
        })
        const data = await response.json().catch(() => null)
        if (!(response.ok && data && data.ok === true)) {
          if (error && data && data.error) {
            error.hidden = false
            error.textContent = data.error
            return
          }
          throw new Error('send failed')
        }
      } catch {
        const name = body.get('name') || ''
        const email = body.get('email') || ''
        const phone = body.get('phone') || ''
        const role = body.get('role') || ''
        const message = body.get('message') || ''
        const subject = encodeURIComponent(body.get('subject') || 'Message from yuzu.hair')
        const lines = [
          name && `Name: ${name}`,
          email && `Email: ${email}`,
          phone && `Phone: ${phone}`,
          role && `Role: ${role}`,
          message && `\n${message}`,
          file instanceof File && file.size > 0 && '\nPlease attach the CV to this email. The form could not send the file.',
        ].filter(Boolean)
        if (error) {
          error.hidden = false
          error.textContent = file instanceof File && file.size > 0
            ? 'The form could not send the file. Your email app should open so you can attach the CV.'
            : 'The form could not send. Your email app should open with this message.'
        }
        window.location.href = `mailto:info@yuzuhairandbeauty.co.uk?subject=${subject}&body=${encodeURIComponent(lines.join('\n'))}`
        return
      } finally {
        if (submitBtn) {
          submitBtn.disabled = false
          submitBtn.textContent = origLabel
        }
      }

      if (thanks) {
        form.hidden = true
        thanks.hidden = false
        thanks.focus?.()
      }
    })
  })
})()

/* track.js */
;(() => {
  const endpoint = document.querySelector('meta[name="yuzu-track"]')?.content || ''

  const pageKey = () => {
    const path = location.pathname.replace(/\/index\.html$/, '/').replace(/\.html$/, '').replace(/\/+$/, '')
    return path.split('/').pop() || 'home'
  }

  const placeOf = (link) => {
    const own = link.closest('[data-place]')
    if (own) return own.getAttribute('data-place')
    const tray = link.closest('.offer-tray')
    if (tray) return `offer-${tray.getAttribute('data-active') || 'tray'}`
    if (link.closest('.fx-bar')) return 'nav'
    if (link.closest('.blend-sticky')) return 'sticky'
    if (link.closest('.site-notice')) return 'notice'
    if (link.closest('.visit-card')) return 'visit'
    if (link.closest('.hero')) return 'hero'
    const section = link.closest('section[id], main[id]')
    return section ? section.id : 'page'
  }

  const kindOf = (href) => {
    if (/phorest\.com/i.test(href)) return 'book'
    if (/^tel:/i.test(href)) return 'call'
    if (/^mailto:/i.test(href)) return 'email'
    if (/maps\.app\.goo\.gl|google\.[a-z.]+\/maps/i.test(href)) return 'directions'
    if (/^https:\/\/wa\.me\//i.test(href)) return 'whatsapp'
    if (/\/review(\/|$)/.test(href)) return 'review'
    return ''
  }

  const tagBooking = (link) => {
    let url
    try {
      url = new URL(link.href)
    } catch {
      return
    }
    if (!/phorest\.com$/i.test(url.hostname)) return
    url.searchParams.set('utm_source', 'yuzu.hair')
    url.searchParams.set('utm_medium', 'website')
    url.searchParams.set('utm_campaign', 'book')
    url.searchParams.set('utm_content', `${pageKey()}-${placeOf(link)}`)
    link.href = url.toString()
  }

  document.querySelectorAll('a[href*="phorest.com"]').forEach(tagBooking)

  const record = (link) => {
    const kind = kindOf(link.getAttribute('href') || '')
    if (!kind) return
    const page = pageKey()
    const place = placeOf(link)
    if (endpoint && navigator.sendBeacon) {
      navigator.sendBeacon(endpoint, new URLSearchParams({ e: kind, p: page, s: place }))
    }
    if (typeof window.plausible === 'function') {
      window.plausible(`Click: ${kind}`, { props: { page, place } })
    }
  }

  const onActivate = (event) => {
    const link = event.target.closest?.('a[href]')
    if (!link) return
    if (/phorest\.com/i.test(link.href)) tagBooking(link)
    if (event.type === 'click' || event.button === 1) record(link)
  }
  document.addEventListener('click', onActivate, true)
  document.addEventListener('auxclick', onActivate, true)
})()

/* notice.js */
;(() => {
  document.querySelectorAll('.site-notice[data-notice-id]').forEach((notice) => {
    const close = notice.querySelector('[data-notice-close]')
    if (!close) return
    close.addEventListener('click', () => {
      notice.hidden = true
      try {
        localStorage.setItem('yuzu-notice-dismissed', notice.getAttribute('data-notice-id') || '')
      } catch {}
    })
  })
})()
