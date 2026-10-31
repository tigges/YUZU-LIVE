(() => {
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
