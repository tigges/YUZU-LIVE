# Yuzu Hair & Beauty — live Round

Production site for [Yuzu Hair & Beauty](https://www.yuzuhairandbeauty.london/) at 5 Dickens Yard, Ealing Broadway.

This is a clean live version of the **Round** design from [YUZU_FINAL](https://github.com/tigges/YUZU_FINAL), with the **Join our team** section restored and copy brought back toward the original Wix site.

| Piece | Source |
| --- | --- |
| Layout, type, colour, rounded cards | YUZU_FINAL `?v=round` |
| Welcome, hours, offers, join-the-team, terms | [yuzuhairandbeauty.london](https://www.yuzuhairandbeauty.london/) and the [Wix clone](https://tigges.github.io/YUZU_SEPT_26/?v=wix) |
| Prices | Current creative director, senior stylist, stylist and junior menu |

Intended live hosts: **yuzu.hair** (canonical) and **yuzuhair.com** (301 to yuzu.hair).

## What’s in the box

```
site/                     Cloudways document root (static + send.php)
wordpress/yuzu-round/     WordPress theme that serves those same pages
wordpress/yuzu-round.zip  Ready-to-upload theme (no terminal needed)
scripts/build.py          Fills shared header/visit/footer from site/partials/, bundles css/yuzu.css and js/yuzu.js
scripts/sync-prices.php   Re-bakes the price panels in prices.html and the service pages from the default price list
scripts/pack-theme.sh     Runs build.py, then copies site/ into the theme and zips it
scripts/pack-theme.ps1    Same packer for Windows PowerShell (needs Python 3)
asset/                    Design references for the next version (not used on the live site)
```

Pages: home, prices, exclusive offers, **join our team**, questions, patch testing, terms, privacy.

The home page keeps Round’s overlapping hero card, gallery lightbox, service cards, weekday offer graphics, reviews, visit block and map, and adds the original Join our team band (senior stylists, stylists, models) plus the contact form that thanks you after submit.

## Cloudways — recommended: static app

This is the path that cannot change the layout.

1. Create a **PHP** application on Cloudways (Apache).
2. Deploy this repo (Git, SFTP, or pull).
3. Set the web root to `site/` (or copy `site/` into `public_html`).
4. Point **yuzu.hair** at the app. Add **yuzuhair.com** as an alias; `site/.htaccess` sends it to yuzu.hair.
5. Install the Cloudways SSL certificate for both names.
6. Confirm `send.php` can mail to `info@yuzuhairandbeauty.co.uk` (Cloudways SMTP / Elastic Email). If mail is not set up, the form still opens a mailto.

Local preview:

```bash
python3 -m http.server 4173 --directory site
```

## Cloudways — WordPress theme (blocks-safe)

Do **not** rebuild this in Elementor, Spectra, Kadence, or the site editor. Those tools will change spacing, radius, and the overlapping hero.

Edit the header, visit card and footer once in `site/partials/`, and styles and scripts in the source files under `site/css/` and `site/js/`; `scripts/build.py` writes them into every page and the `yuzu.css` / `yuzu.js` bundles (never edit the bundles by hand).

The **Yuzu Round** theme is a locked WordPress theme: it prints the designed HTML. WordPress hosts the pages, permalinks, and SSL. **Salon details** in wp-admin holds opening hours, holidays and special days, the notice banner above the header, the Google rating, optional cookie-free analytics, and counts of Book, Call, Directions and Email clicks. The salon edits prices in wp-admin under **Price list**. Saving updates the price tables, the homepage From prices and card sentences, and clears the Breeze cache when that plugin is active. The layout stays locked. Restore original prices puts back the list shipped with the theme.

Header: Gallery, Services, Offers, Reviews, Visit, Book. Footer: Questions, Patch testing, Terms, Privacy. There is no price-list PDF and no Join our team item in the menus. The careers band and `/team/` page stay.

**Windows — no terminal.** Do not run the bash commands in PowerShell.

1. Download [wordpress/yuzu-round.zip](https://github.com/tigges/YUZU-LIVE/raw/main/wordpress/yuzu-round.zip).
2. In WordPress: **Appearance → Themes → Add New → Upload Theme** (not the Popular list).
3. Choose that zip → **Install Now → Activate**.
4. Activation creates Home, Prices, Offers, Join our team, Questions, Patch testing, Terms, and Privacy, and sets Home as the front page.
5. **Settings → Permalinks → Post name → Save**.
6. Point yuzu.hair / yuzuhair.com at that WordPress app.

To rebuild the zip later on Windows, from a clone of this repo:

```powershell
.\scripts\pack-theme.ps1
```

A page-builder “Blox / blocks” reconstruction will not keep this layout. If you already have WordPress on Cloudways (the old `/v1-clean/` preview), install this theme over it and keep the designed templates.

## Cloudways staging (YUZU_LIVEWORD)

Live is **yuzu.hair**. Do design and copy checks on a staging clone, then push.

1. On **YUZU_LIVEWORD**, open **Staging Management** (or the app menu → **Clone App / Create Staging**). Tick **Create as Staging**, same server is fine. Wait for the new app and its `cloudwaysapps.com` URL.
2. On that staging app, upload or overwrite the **Yuzu Round** theme (`wordpress/yuzu-round` after `bash scripts/pack-theme.sh`). Do not change the live theme until you have looked at staging.
3. Open the staging URL, not yuzu.hair. Check layout, nav, forms, and mobile.
4. When it looks right, go back to the **live** app → **Staging Management** → **Push**. Choose **Incremental** files if you only changed the theme. Take the live backup. For this site, you usually do **not** need to push the whole database (that would overwrite live comments/settings).
5. **Pull** is the other way: copy live down onto staging when you want a fresh copy of production.

The GitHub repo is the source of the theme. Staging is where you prove a zip before yuzu.hair changes.

## Book, maps, social

- Book: https://www.phorest.com/salon/yuzuhairandbeauty
- Maps: https://maps.app.goo.gl/bhFS5wwkW3xxAdKd8
- Instagram: https://www.instagram.com/yuzuhairandbeauty/
- TikTok: https://www.tiktok.com/@yuzuhairandbeauty.est16
- Facebook: https://www.facebook.com/YUZUHairandBeauty/
