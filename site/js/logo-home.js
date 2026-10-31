;(() => {
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
