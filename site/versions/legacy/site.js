(() => {
  const header = document.querySelector('.round-header')
  const toggle = document.querySelector('.menu-toggle')
  const nav = document.querySelector('.round-nav')

  if (toggle && header) {
    toggle.addEventListener('click', () => {
      const open = header.classList.toggle('open')
      toggle.setAttribute('aria-expanded', String(open))
      toggle.setAttribute('aria-label', open ? 'Close menu' : 'Open menu')
      document.body.classList.toggle('is-locked', open)
    })

    nav?.querySelectorAll('a').forEach((link) => {
      link.addEventListener('click', () => {
        header.classList.remove('open')
        toggle.setAttribute('aria-expanded', 'false')
        toggle.setAttribute('aria-label', 'Open menu')
        document.body.classList.remove('is-locked')
      })
    })
  }

  const shots = [...document.querySelectorAll('[data-lightbox]')]
  const lightbox = document.querySelector('.lightbox')
  if (shots.length && lightbox) {
    const image = lightbox.querySelector('img')
    const caption = lightbox.querySelector('figcaption')
    const closeBtn = lightbox.querySelector('.lightbox-close')
    let index = 0

    const show = (next) => {
      index = (next + shots.length) % shots.length
      const shot = shots[index]
      image.src = shot.dataset.full || shot.querySelector('img').src
      image.alt = shot.dataset.alt || ''
      caption.textContent = shot.dataset.title || ''
      lightbox.classList.add('is-open')
      lightbox.setAttribute('aria-hidden', 'false')
      document.body.classList.add('is-locked')
      document.querySelector('.round')?.classList.add('lightbox-open')
      closeBtn.focus()
    }

    const hide = () => {
      lightbox.classList.remove('is-open')
      lightbox.setAttribute('aria-hidden', 'true')
      document.body.classList.remove('is-locked')
      document.querySelector('.round')?.classList.remove('lightbox-open')
    }

    shots.forEach((shot, shotIndex) => {
      shot.addEventListener('click', () => show(shotIndex))
    })
    closeBtn?.addEventListener('click', hide)
    lightbox.querySelector('.prev')?.addEventListener('click', () => show(index - 1))
    lightbox.querySelector('.next')?.addEventListener('click', () => show(index + 1))
    document.addEventListener('keydown', (event) => {
      if (!lightbox.classList.contains('is-open')) return
      if (event.key === 'Escape') hide()
      if (event.key === 'ArrowRight') show(index + 1)
      if (event.key === 'ArrowLeft') show(index - 1)
    })
  }

  document.querySelectorAll('[data-yuzu-form]').forEach((form) => {
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

  const navLinks = [...document.querySelectorAll('.round-nav a[href*="#"]')]
    .map((link) => {
      const id = new URL(link.href, window.location.href).hash.replace('#', '')
      const target = id ? document.getElementById(id) : null
      return target ? { id, link, target } : null
    })
    .filter(Boolean)

  if (navLinks.length) {
    const syncNav = () => {
      const vh = window.innerHeight
      const atBottom = vh + window.scrollY >= document.documentElement.scrollHeight - 80
      let activeId = ''
      for (const item of navLinks) {
        const threshold = item.id === 'visit' ? vh * 0.65 : vh * 0.38
        if (item.target.getBoundingClientRect().top <= threshold) {
          activeId = item.id
        }
      }
      if (atBottom) {
        activeId = navLinks[navLinks.length - 1].id
      }
      for (const item of navLinks) {
        if (item.id === activeId) {
          item.link.setAttribute('aria-current', 'page')
        } else if (item.link.getAttribute('aria-current') === 'page' && !item.link.dataset.keepCurrent) {
          item.link.removeAttribute('aria-current')
        }
      }
    }
    window.addEventListener('scroll', syncNav, { passive: true })
    window.addEventListener('resize', syncNav, { passive: true })
    syncNav()
  }
})()

;(() => {
  const homePath = (url) => {
    let path = url.pathname.replace(/\/index\.html$/i, '/')
    if (path.length > 1 && path.endsWith('/')) path = path.slice(0, -1)
    return path || '/'
  }

  document.querySelectorAll('a.round-logo').forEach((link) => {
    link.addEventListener('click', (event) => {
      if (event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return
      let dest
      try {
        dest = new URL(link.href, window.location.href)
      } catch {
        return
      }
      if (dest.origin !== window.location.origin) return
      if (homePath(dest) !== homePath(window.location)) return
      event.preventDefault()
      const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches
      window.scrollTo({ top: 0, left: 0, behavior: reduce ? 'auto' : 'smooth' })
      if (window.location.hash) {
        history.replaceState(null, '', window.location.pathname + window.location.search)
      }
    })
  })
})()
