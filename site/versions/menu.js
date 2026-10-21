document.querySelectorAll(".bar, .lb-top").forEach((header) => {
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
});
