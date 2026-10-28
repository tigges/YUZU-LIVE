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
    if (row && item && row.scrollWidth > row.clientWidth) row.scrollLeft += item.getBoundingClientRect().left - row.getBoundingClientRect().left
  })
  document.querySelectorAll('[data-today-offer]').forEach((el) => {
    const lead = document.createElement('strong')
    lead.textContent = offerDay === day ? 'Today’s offer' : `Next offer · ${names[offerDay]}`
    el.replaceChildren(lead, ` ${title}: ${line}.`)
  })

  const sticky = document.querySelector('[data-sticky-book]')
  const end = document.querySelector('[data-book-end]')
  if (sticky && end && 'IntersectionObserver' in window) {
    new IntersectionObserver(([entry]) => sticky.classList.toggle('is-hidden', entry.isIntersecting && scrollY > 0)).observe(end)
  }
})()
