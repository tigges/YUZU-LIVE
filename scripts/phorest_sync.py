#!/usr/bin/env python3
"""Refresh the theme's copy of the Phorest menu and check every booking link.

Phorest's booking app reads its menu from an API that only answers the app
itself, so this opens the booking page in a headless browser and keeps the
responses it loads. It writes wordpress/yuzu-round/inc/phorest.json (sorted,
no timestamps, so it only changes when Phorest does) and a plain-English
report of anything the site links to that Phorest no longer has, plus prices
that differ between the site and Phorest.

  python3 scripts/phorest_sync.py                 # refresh + check live site
  python3 scripts/phorest_sync.py --site URL      # check another copy of the site
  python3 scripts/phorest_sync.py --report FILE   # also write the report as markdown

Needs: pip install playwright && python3 -m playwright install chromium
Exit code: 0 when the links all work, 1 when the report lists a problem,
2 when Phorest could not be read (the stored menu is then left untouched).
"""

from __future__ import annotations

import argparse
import html
import json
import re
import sys
import urllib.request
from pathlib import Path

ROOT = Path(__file__).resolve().parent.parent
MENU = ROOT / "wordpress/yuzu-round/inc/phorest.json"
LINKS = ROOT / "wordpress/yuzu-round/inc/phorest-links.json"
SALON = "yuzuhairandbeauty"
BOOK = f"https://www.phorest.com/salon/{SALON}"
PAGES = ["", "prices/", "haircut-ealing/", "hair-colour-ealing/", "balayage-ealing/", "hair-smoothing-ealing/"]
LEVEL_LABEL = {"creative": "Creative Director", "senior": "Senior Stylist", "stylist": "Stylist", "junior": "Junior Stylist"}


def fetch_menu() -> dict:
    from playwright.sync_api import sync_playwright

    wanted = {"service_categories": None, "services": None, "staffs": None, "branch_courses": None}

    def keep(response):
        m = re.search(r"phorest\.me/api/([a-z_]+)(\?[^#]*)?$", response.url)
        if not m or m.group(1) not in wanted or m.group(2):
            return
        try:
            wanted[m.group(1)] = response.json()
        except Exception:
            pass

    with sync_playwright() as p:
        browser = p.chromium.launch()
        page = browser.new_page()
        page.on("response", keep)
        page.goto(f"{BOOK}/book/services", wait_until="networkidle", timeout=90000)
        page.wait_for_timeout(2000)
        page.goto(f"{BOOK}/buy/course", wait_until="networkidle", timeout=90000)
        page.wait_for_timeout(2000)
        browser.close()

    missing = [k for k in ("service_categories", "services", "staffs") if wanted[k] is None]
    if missing:
        raise RuntimeError("Phorest did not send: " + ", ".join(missing))

    categories = {c["id"]: c["name"].strip() for c in wanted["service_categories"]["service_categories"]}
    services = [
        {
            "id": s["id"],
            "name": re.sub(r"\s+", " ", s["name"]).strip(),
            "category": categories.get(s["service_category_id"], ""),
            "price": round(float(s["price"] or 0), 2),
            "duration": int(s["duration"] or 0),
            "patch_test": bool(s.get("patch_test_required")),
        }
        for s in wanted["services"]["services"]
        if s.get("internet_enabled") and not s.get("archived")
    ]
    levels = {c["id"]: c["name"].strip() for c in wanted["staffs"].get("linked", {}).get("staff_categories", [])}
    staff = [
        {"id": s["id"], "name": (s.get("first_name") or "").strip(), "level": levels.get(s.get("staff_category_id"), "")}
        for s in wanted["staffs"]["staffs"]
        if not s.get("archived")
    ]
    courses = []
    for c in (wanted["branch_courses"] or {}).get("branch_courses", []) if isinstance(wanted["branch_courses"], dict) else []:
        courses.append({"id": c.get("id", ""), "name": re.sub(r"\s+", " ", str(c.get("name", ""))).strip(), "price": round(float(c.get("price") or 0), 2)})
    return {
        "salon": SALON,
        "book": BOOK,
        "categories": sorted(({"id": k, "name": v} for k, v in categories.items()), key=lambda c: c["name"].lower()),
        "services": sorted(services, key=lambda s: (s["category"].lower(), s["name"].lower())),
        "staff": sorted(staff, key=lambda s: (s["level"].lower(), s["name"].lower())),
        "courses": sorted(courses, key=lambda c: c["name"].lower()),
    }


def money(n: float) -> str:
    return "£" + (f"{n:.2f}".rstrip("0").rstrip("."))


def check_defaults(menu: dict, links: dict) -> list[str]:
    names = {(s["category"], s["name"]) for s in menu["services"]}
    cats = {c["name"] for c in menu["categories"]}
    out = []
    for level, per_tab in links["levels"].items():
        for tab, cat in per_tab.items():
            if cat not in cats:
                out.append(f"The {LEVEL_LABEL.get(level, level)} {tab} link points at the Phorest category “{cat}”, which Phorest no longer has.")
    for group, rows in links["rows"].items():
        for row, per_level in rows.items():
            for level, ref in per_level.items():
                cat, _, name = ref.partition(" / ")
                if (cat, name) not in names:
                    out.append(f"Price list “{row}” ({LEVEL_LABEL.get(level, level)}) is set to book “{name}” in “{cat}”, which Phorest no longer has. Visitors get the {LEVEL_LABEL.get(level, level)} list instead.")
    return out


def check_site(menu: dict, site: str) -> tuple[list[str], list[str]]:
    services = {s["id"]: s for s in menu["services"]}
    cats = {c["id"] for c in menu["categories"]}
    broken, prices = [], []
    for path in PAGES:
        url = site.rstrip("/") + "/" + path
        try:
            req = urllib.request.Request(url, headers={"User-Agent": "yuzu-phorest-check"})
            page = urllib.request.urlopen(req, timeout=30).read().decode("utf-8", "replace")
        except Exception as e:
            broken.append(f"Could not open {url} ({e}).")
            continue
        found = re.finditer(r'<a [^>]*href="(https://www\.phorest\.com/salon/[^"]+)"[^>]*>|data-book-href="(https://www\.phorest\.com/salon/[^"]+)"', page)
        for m in found:
            href = html.unescape(m.group(1) or m.group(2))
            tag = m.group(0)
            svc = re.search(r"/book/services/([A-Za-z0-9_-]+)", href)
            cat = re.search(r"/book/categories/([A-Za-z0-9_-]+)", href)
            if svc and svc.group(1) not in services:
                broken.append(f"{url} links to a Phorest service that no longer exists ({svc.group(1)}).")
            elif cat and cat.group(1) not in cats:
                broken.append(f"{url} links to a Phorest category that no longer exists ({cat.group(1)}).")
            elif svc:
                shown = re.search(r'data-price="([^"]*)"', tag)
                fig = re.search(r"£(\d+(?:\.\d+)?)", html.unescape(shown.group(1))) if shown else None
                s = services[svc.group(1)]
                if fig and abs(float(fig.group(1)) - s["price"]) > 0.001:
                    label = re.search(r'aria-label="([^"]*)"', tag)
                    what = html.unescape(label.group(1)) if label else s["name"]
                    what = re.sub(r"^Book | \(opens Phorest in a new tab\)$", "", what)
                    what = re.sub(r", £[^,]*$", "", what)
                    prices.append(f"{what}: the site says £{fig.group(1)}, Phorest says {money(s['price'])} (“{s['name']}”, {s['category']}).")
    return sorted(set(broken)), sorted(set(prices))


def main() -> int:
    ap = argparse.ArgumentParser()
    ap.add_argument("--site", default="https://yuzu.hair")
    ap.add_argument("--report", default="")
    ap.add_argument("--no-site", action="store_true", help="skip checking the live pages")
    args = ap.parse_args()

    try:
        menu = fetch_menu()
    except Exception as e:
        print(f"Phorest could not be read: {e}", file=sys.stderr)
        return 2
    text = json.dumps(menu, indent=1, ensure_ascii=False) + "\n"
    changed = not MENU.exists() or MENU.read_text(encoding="utf-8") != text
    MENU.write_text(text, encoding="utf-8")

    links = json.loads(LINKS.read_text(encoding="utf-8"))
    defaults = check_defaults(menu, links)
    broken, prices = ([], []) if args.no_site else check_site(menu, args.site)

    lines = [f"Phorest menu: {len(menu['services'])} services, {len(menu['categories'])} categories, {len(menu['staff'])} staff." + (" Changed since the last check." if changed else " No change.")]
    for title, items in (("Links that no longer work", broken), ("Price list links to fix in the theme", defaults), ("Prices that differ between the site and Phorest", prices)):
        if items:
            lines += ["", f"### {title}", *[f"- {i}" for i in items]]
    if not (broken or defaults or prices):
        lines += ["", "Every booking link on the site opens a service or list Phorest still has, and the prices match."]
    report = "\n".join(lines) + "\n"
    print(report)
    if args.report:
        Path(args.report).write_text(report, encoding="utf-8")
    return 1 if (broken or defaults or prices) else 0


if __name__ == "__main__":
    sys.exit(main())
