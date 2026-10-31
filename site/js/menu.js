document.querySelectorAll(".bar, .lb-top, .fx-bar").forEach((header) => {
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
