(() => {
  const params = new URLSearchParams(location.search)
  const keys = ['sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat']
  const names = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']
  const hours = { 2: [10, 20], 3: [10, 20], 4: [10, 20], 5: [10, 20], 6: [9, 18] }
  const offers = {
    2: ['tue', 'Colour Tuesdays', '50% off colour with a creative director, with a full-price wash, cut and blow-dry'],
    3: ['wed', 'Smooth Wednesdays', '25% off our three to six month smoothing treatment'],
    4: ['thu', 'Colour Thursdays', '50% off colour with a senior stylist or stylist, with a full-price wash, cut and blow-dry'],
  }

  const parts = Object.fromEntries(
    new Intl.DateTimeFormat('en-GB', { timeZone: 'Europe/London', weekday: 'short', hour: 'numeric', minute: 'numeric', hourCycle: 'h23' })
      .formatToParts(new Date())
      .map((p) => [p.type, p.value]),
  )
  let day = keys.indexOf(parts.weekday.toLowerCase().slice(0, 3))
  let hour = Number(parts.hour) + Number(parts.minute) / 60
  if (keys.includes(params.get('day'))) day = keys.indexOf(params.get('day'))
  if (params.has('hour')) hour = Number(params.get('hour'))

  const clock = (h) => `${h % 12 || 12}${h < 12 ? 'am' : 'pm'}`
  const nextDay = (from, table) => {
    let d = (from + 1) % 7
    while (!table[d]) d = (d + 1) % 7
    return d
  }

  const today = hours[day]
  let open = false
  let status
  if (today && hour >= today[0] && hour < today[1]) {
    open = true
    status = `Open now · until ${clock(today[1])}`
  } else if (today && hour < today[0]) {
    status = `Closed now · opens today at ${clock(today[0])}`
  } else {
    const d = nextDay(day, hours)
    status = `Closed now · opens ${d === (day + 1) % 7 ? 'tomorrow' : names[d]} at ${clock(hours[d][0])}`
  }
  document.querySelectorAll('[data-open-status]').forEach((el) => {
    el.textContent = status
    el.classList.toggle('is-open', open)
  })

  const offerDay = offers[day] ? day : nextDay(day, offers)
  const [key, title, line] = offers[offerDay]
  const flag = offerDay === day ? 'Today' : names[offerDay]
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
  document.querySelectorAll('[data-today-offer]').forEach((el) => {
    const lead = document.createElement('strong')
    lead.textContent = offerDay === day ? 'Today’s offer' : `Next offer · ${names[offerDay]}`
    el.replaceChildren(lead, ` ${title}: ${line}.`)
  })

  const detail = document.querySelector('[data-offer-detail]')
  if (detail) {
    const roll = document.querySelector('.promo-roll-6')
    const pricesHref = detail.getAttribute('data-prices-href') || 'prices.html#cut'
    const promoDetails = {
      tue: {
        kicker: 'Every Tuesday · Creative Director',
        headline: 'Don’t go back to black with this deal',
        text: 'Say goodbye to dull hair with our vibrant colour celebration. Every Tuesday, enjoy 50% off your most expensive colour service when booked with a full-priced wash, cut and blow-dry with our Creative Director.',
        terms: '50% off most expensive colour service · requires full-priced wash, cut & blow-dry · patch test 48h prior',
        bookLabel: 'Book Tuesday colour',
        bookHref: 'https://www.phorest.com/salon/yuzuhairandbeauty',
        external: true,
      },
      wed: {
        kicker: 'Every Wednesday · Aura Smoothing',
        headline: 'Blow everyone away with this deal',
        text: 'Every Wednesday at YUZU hair, enjoy 25% off our Brazilian blow-dry and Aura smoothing treatments—our formaldehyde- and ammonia-free smoothing and straightening treatment lasting 3 to 6 months.',
        terms: '25% off 3–6 month smoothing treatment · formaldehyde & ammonia free',
        bookLabel: 'Book Wednesday smoothing',
        bookHref: 'https://www.phorest.com/salon/yuzuhairandbeauty',
        external: true,
      },
      thu: {
        kicker: 'Every Thursday · Senior Stylist & Stylist',
        headline: 'Thursdays are the new Tuesdays',
        text: 'Every Thursday, enjoy 50% off your most expensive colour service when booked with a full-priced wash, cut and blow-dry with a Senior Stylist or Stylist.',
        terms: '50% off most expensive colour service · requires full-priced wash, cut & blow-dry · patch test 48h prior',
        bookLabel: 'Book Thursday colour',
        bookHref: 'https://www.phorest.com/salon/yuzuhairandbeauty',
        external: true,
      },
      refer: {
        kicker: 'Ongoing reward · Unlimited referrals',
        headline: 'Spread the word, earn £10 credit',
        text: 'Share YUZU hair with friends, family, or colleagues. When your recommended friend books and completes their first appointment with us, you receive £10 credit towards your next visit—with no limit on how many friends you refer.',
        terms: '£10 credit per new client referred · unlimited referrals · redeemable on any service',
        bookLabel: 'Book an appointment',
        bookHref: 'https://www.phorest.com/salon/yuzuhairandbeauty',
        external: true,
      },
      rebook: {
        kicker: 'In-salon loyalty reward',
        headline: 'Hair today, gone tomorrow (but not really)',
        text: 'At YUZU hair, we value loyalty. Book your next appointment at reception on the same day as your visit and receive 10% off that next appointment as a token of our gratitude.',
        terms: '10% off your next appointment when booked in-salon on the day of your visit',
        bookLabel: 'Book your visit',
        bookHref: 'https://www.phorest.com/salon/yuzuhairandbeauty',
        external: true,
      },
      blowdry: {
        kicker: 'Package · Save on regular styling',
        headline: '4 blow-drys for £156',
        text: 'Keep your hair styled for less with our 4-visit blow-dry bundle. Ask at reception on your next visit or call us to purchase.',
        terms: 'Valid 6 months · haircuts not included · not applicable for extensions · no-show deducts 1 blow-dry · non-refundable',
        bookLabel: 'View in price list',
        bookHref: pricesHref,
        external: false,
      },
    }

    const kickerEl = detail.querySelector('[data-detail-kicker]')
    const headlineEl = detail.querySelector('[data-detail-headline]')
    const textEl = detail.querySelector('[data-detail-text]')
    const termsEl = detail.querySelector('[data-detail-terms]')
    const bookEl = detail.querySelector('[data-detail-book]')
    const buttons = [...document.querySelectorAll('[data-promo]')]
    let activePromo = ''

    const placeTray = () => {
      if (!roll || !activePromo || detail.hidden) return
      const idx = buttons.findIndex((b) => b.getAttribute('data-promo') === activePromo)
      if (idx < 0) return
      detail.dataset.col = String(idx % 3)
      if (window.innerWidth > 700) {
        const items = [...roll.querySelectorAll(':scope > li')]
        if (idx < 3 && items[2]) {
          items[2].after(detail)
        } else {
          roll.appendChild(detail)
        }
      } else {
        roll.after(detail)
      }
    }

    const closeTray = () => {
      detail.hidden = true
      activePromo = ''
      buttons.forEach((b) => {
        b.classList.remove('is-selected')
        b.setAttribute('aria-expanded', 'false')
      })
    }

    const openTray = (promoKey) => {
      const p = promoDetails[promoKey]
      if (!p) return
      activePromo = promoKey
      if (kickerEl) kickerEl.textContent = p.kicker
      if (headlineEl) headlineEl.textContent = p.headline
      if (textEl) textEl.textContent = p.text
      if (termsEl) termsEl.textContent = p.terms
      if (bookEl) {
        bookEl.textContent = p.bookLabel
        bookEl.setAttribute('href', p.bookHref)
        if (p.external) {
          bookEl.setAttribute('target', '_blank')
          bookEl.setAttribute('rel', 'noreferrer')
        } else {
          bookEl.removeAttribute('target')
          bookEl.removeAttribute('rel')
        }
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
          if (hashByPromo[k] && history.replaceState) {
            history.replaceState(null, '', hashByPromo[k])
          }
        }
      })
    })

    if (promoByHash[location.hash]) {
      openTray(promoByHash[location.hash])
      document.getElementById('offers')?.scrollIntoView()
    }

    window.addEventListener('resize', placeTray, { passive: true })
    detail.querySelector('[data-detail-close]')?.addEventListener('click', closeTray)
  }
})()
