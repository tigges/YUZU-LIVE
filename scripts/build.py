#!/usr/bin/env python3
"""Build the live site in place.

- Fills every `<!-- @include NAME -->…<!-- /@include -->` region in site/*.html
  from site/partials/NAME.html, so the header, visit card, footer and scripts
  are edited once.
- Bundles the CSS sources into css/yuzu.css (minified) and the JS sources into
  js/yuzu.js, then stamps both URLs with a content hash so caches never serve
  an old file after a deploy.

Run it after editing anything in site/ (scripts/pack-theme.sh runs it first).
"""
from __future__ import annotations

import hashlib
import json
import re
import sys
from pathlib import Path

ROOT = Path(__file__).resolve().parent.parent
SITE = ROOT / "site"
PARTIALS = SITE / "partials"

CSS_SOURCES = ["fonts.css", "brand.css", "fresh.css", "blend.css", "promo.css", "site-extra.css"]
JS_SOURCES = ["menu.js", "logo-home.js", "visit.js", "prices.js", "forms.js", "track.js", "notice.js"]

# Pages that show the full visit card; the rest use the thin footer bar.
SERVICE_PAGES = {"haircut-ealing.html", "hair-colour-ealing.html", "balayage-ealing.html", "hair-smoothing-ealing.html"}

DEFAULT_HOURS = {
    "week": [None, None, ["10:00", "20:00"], ["10:00", "20:00"], ["10:00", "20:00"], ["10:00", "20:00"], ["09:00", "18:00"]],
    "special": [],
}

INCLUDE_RE = re.compile(r"<!-- @include ([a-z-]+) -->.*?<!-- /@include -->", re.S)
STRING_RE = re.compile(r'"(?:\\.|[^"\\])*"|\'(?:\\.|[^\'\\])*\'')


def minify_css(css: str) -> str:
    strings: list[str] = []

    def keep(match: re.Match) -> str:
        strings.append(match.group(0))
        return f"\x00{len(strings) - 1}\x00"

    css = STRING_RE.sub(keep, css)
    css = re.sub(r"/\*.*?\*/", "", css, flags=re.S)
    css = re.sub(r"\s+", " ", css)
    css = re.sub(r"\s*([{};,])\s*", r"\1", css)
    css = re.sub(r":\s+", ":", css)
    css = css.replace(";}", "}")
    css = re.sub(r"\x00(\d+)\x00", lambda m: strings[int(m.group(1))], css)
    return css.strip() + "\n"


def short_hash(data: bytes) -> str:
    return hashlib.sha1(data).hexdigest()[:10]


def build_css() -> str:
    parts = []
    for name in CSS_SOURCES:
        parts.append(f"/* {name} */\n" + (SITE / "css" / name).read_text(encoding="utf-8"))
    out = minify_css("\n".join(parts))
    (SITE / "css" / "yuzu.css").write_text(out, encoding="utf-8")
    return short_hash(out.encode())


def build_js() -> str:
    parts = []
    for name in JS_SOURCES:
        body = (SITE / "js" / name).read_text(encoding="utf-8").strip()
        parts.append(f"/* {name} */\n;{body}\n")
    out = "\n".join(parts)
    (SITE / "js" / "yuzu.js").write_text(out, encoding="utf-8")
    return short_hash(out.encode())


def page_vars(page: str, has_visit: bool) -> dict[str, str]:
    home = page == "index.html"
    return {
        "logo_href": "#" if home else "index.html",
        "looks_href": "#looks" if home else "index.html#looks",
        "services_href": "#services" if home else "prices.html",
        "offers_href": "#offers" if home else "index.html#offers",
        "visit_href": "#visit" if has_visit else "index.html#visit",
        "hours_json": json.dumps(DEFAULT_HOURS, separators=(",", ":")),
    }


def mark_current(html: str, page: str) -> str:
    services = page == "prices.html" or page in SERVICE_PAGES

    def nav(match: re.Match) -> str:
        block = match.group(0).replace(' aria-current="page"', "")
        if services:
            block = re.sub(r'(<a href="(?:prices\.html|#services)")', r'\1 aria-current="page"', block, count=1)
        return block

    html = re.sub(r'<nav aria-label="Primary">.*?</nav>', nav, html, flags=re.S)

    def legal(match: re.Match) -> str:
        block = match.group(0).replace(' aria-current="page"', "")
        return block.replace(f'<a href="{page}">', f'<a href="{page}" aria-current="page">', 1)

    return re.sub(r'<span class="legal-links">.*?</span>', legal, html, flags=re.S)


def fill(page: Path, css_hash: str, js_hash: str) -> bool:
    html = page.read_text(encoding="utf-8")
    if "<!-- @include" not in html:
        return False
    has_visit = "<!-- @include visit -->" in html
    values = page_vars(page.name, has_visit) | {"css_hash": css_hash, "js_hash": js_hash}

    def include(match: re.Match) -> str:
        name = match.group(1)
        body = (PARTIALS / f"{name}.html").read_text(encoding="utf-8").rstrip("\n")
        body = re.sub(r"\{\{(\w+)\}\}", lambda m: values[m.group(1)], body)
        return f"<!-- @include {name} -->\n{body}\n<!-- /@include -->"

    out = mark_current(INCLUDE_RE.sub(include, html), page.name)
    if out != html:
        page.write_text(out, encoding="utf-8")
    return True


def main() -> int:
    css_hash = build_css()
    js_hash = build_js()
    pages = [p.name for p in sorted(SITE.glob("*.html")) if fill(p, css_hash, js_hash)]
    print(f"css/yuzu.css?v={css_hash}  js/yuzu.js?v={js_hash}")
    print("pages: " + ", ".join(pages))
    return 0


if __name__ == "__main__":
    sys.exit(main())
