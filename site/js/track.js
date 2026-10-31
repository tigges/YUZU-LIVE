(() => {
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
