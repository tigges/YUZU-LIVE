(() => {
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
