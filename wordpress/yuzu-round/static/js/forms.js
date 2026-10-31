(() => {
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
