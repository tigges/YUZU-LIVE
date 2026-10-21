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
      }

      if (thanks) {
        form.hidden = true
        thanks.hidden = false
        thanks.focus?.()
      }
    })
  })

  const ids = ['top', 'looks', 'services', 'reviews', 'offers', 'team', 'visit']
  const observed = ids.map((id) => document.getElementById(id)).filter(Boolean)
  if (observed.length && 'IntersectionObserver' in window) {
    const links = new Map(
      [...document.querySelectorAll('.round-nav a[href*="#"]')].map((link) => {
        const id = new URL(link.href, window.location.href).hash.replace('#', '')
        return [id, link]
      }),
    )
    const observer = new IntersectionObserver(
      (entries) => {
        const visible = entries
          .filter((entry) => entry.isIntersecting)
          .sort((a, b) => b.intersectionRatio - a.intersectionRatio)[0]
        const id = visible?.target.id
        if (!id) return
        links.forEach((link, key) => {
          if (key === id) link.setAttribute('aria-current', 'page')
          else if (link.getAttribute('aria-current') === 'page' && !link.dataset.keepCurrent) {
            link.removeAttribute('aria-current')
          }
        })
      },
      { rootMargin: '-20% 0px -55% 0px', threshold: [0.1, 0.25, 0.5] },
    )
    observed.forEach((el) => observer.observe(el))
  }

  const chat = document.createElement('div')
  chat.className = 'salon-chat'
  chat.innerHTML = `
    <section class="salon-chat-panel" hidden aria-label="Chat with Yuzu Hair">
      <header class="salon-chat-head">
        <span class="salon-chat-avatar" aria-hidden="true">Y</span>
        <div>
          <strong>Yuzu Hair</strong>
          <span>Ask us about a visit</span>
        </div>
        <button class="salon-chat-close" type="button" aria-label="Close chat">×</button>
      </header>
      <div class="salon-chat-log">
        <p class="from-salon">Hello. Ask about a colour, a booking, or anything else for your visit.</p>
      </div>
      <form class="salon-chat-form">
        <div class="salon-chat-who">
          <input name="name" autocomplete="name" placeholder="Name" aria-label="Name" />
          <input name="email" type="email" autocomplete="email" placeholder="Email" aria-label="Email" required />
        </div>
        <label class="salon-chat-compose">
          <span class="vh">Message</span>
          <textarea name="message" rows="2" required placeholder="Write a message…"></textarea>
          <button class="salon-chat-send" type="submit" aria-label="Send">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 12h12M12 6l6 6-6 6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </button>
        </label>
        <p class="salon-chat-error" hidden></p>
      </form>
    </section>
    <button class="salon-chat-launcher" type="button" aria-expanded="false" aria-label="Open chat">
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 6.5h14a1.5 1.5 0 0 1 1.5 1.5v7A1.5 1.5 0 0 1 19 16.5H9l-4 3v-3H5A1.5 1.5 0 0 1 3.5 15V8A1.5 1.5 0 0 1 5 6.5Z" fill="currentColor"/></svg>
    </button>
  `
  document.body.appendChild(chat)

  const panel = chat.querySelector('.salon-chat-panel')
  const launcher = chat.querySelector('.salon-chat-launcher')
  const log = chat.querySelector('.salon-chat-log')
  const chatForm = chat.querySelector('.salon-chat-form')
  const chatError = chat.querySelector('.salon-chat-error')
  const setOpen = (open) => {
    panel.hidden = !open
    launcher.setAttribute('aria-expanded', String(open))
    launcher.setAttribute('aria-label', open ? 'Close chat' : 'Open chat')
    chat.classList.toggle('is-open', open)
    if (open) {
      const who = chat.querySelector('.salon-chat-who')
      const focusTarget = who.hidden ? chatForm.querySelector('textarea') : chatForm.querySelector('input')
      focusTarget?.focus()
    }
  }
  launcher.addEventListener('click', () => setOpen(panel.hidden))
  chat.querySelector('.salon-chat-close').addEventListener('click', () => setOpen(false))
  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && !panel.hidden) setOpen(false)
  })
  chatForm.addEventListener('submit', async (event) => {
    event.preventDefault()
    chatError.hidden = true
    const body = new FormData(chatForm)
    body.set('subject', 'Chat from yuzu.hair')
    const message = String(body.get('message') || '').trim()
    if (!message) return
    try {
      const response = await fetch('send.php', {
        method: 'POST',
        body,
        headers: { Accept: 'application/json' },
      })
      const data = await response.json().catch(() => null)
      if (!(response.ok && data && data.ok === true)) {
        throw new Error(data && data.error ? data.error : 'send failed')
      }
    } catch (error) {
      chatError.hidden = false
      chatError.textContent = error.message && error.message !== 'send failed'
        ? error.message
        : 'That message did not send. Please email info@yuzuhairandbeauty.co.uk.'
      return
    }
    const guest = document.createElement('p')
    guest.className = 'from-guest'
    guest.textContent = message
    const reply = document.createElement('p')
    reply.className = 'from-salon'
    reply.textContent = 'Thanks. We’ll reply to the email you gave.'
    log.append(guest, reply)
    log.scrollTop = log.scrollHeight
    chatForm.querySelector('[name=message]').value = ''
    chat.querySelector('.salon-chat-who').hidden = true
  })
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
