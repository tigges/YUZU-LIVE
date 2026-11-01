# Yuzu Hair & Beauty — live Round

Production site for YUZU hair at 5 Dickens Yard, Ealing Broadway: **yuzu.hair** (canonical; yuzuhair.com and yuzuhairandbeauty.london redirect to it).

The site is the **Yuzu Round** WordPress theme on Cloudways. The theme prints the designed HTML from `site/`; WordPress provides the pages, permalinks, SSL and the wp-admin screens (Salon details, Price list, Page copy).

## What’s in the repo

```
site/                     The pages, CSS, JS and images the theme serves
site/partials/            Shared header, visit card, footer and scripts (edit these, not the copies in each page)
site/versions/            Open mockups and the printable review kit (noindex, served at /versions/)
wordpress/yuzu-round/     The theme: rendering, wp-admin screens, SEO, sitemap
functions.php, render.php Shims so the repo root works as the theme folder (Cloudways Git deploy)
scripts/build.py          Fills partials into every page and bundles css/yuzu.css and js/yuzu.js
scripts/sync-prices.php   Re-bakes the price panels from the default price list
scripts/pack-theme.sh     Builds, then writes wordpress/yuzu-round.zip (also .ps1 for Windows)
```

Never edit `css/yuzu.css` or `js/yuzu.js` by hand; run `python3 scripts/build.py` after changing anything in `site/css`, `site/js` or `site/partials`.

Older design versions, the design references (`asset/`) and proposals live on the [`cursor/design-archive-426a`](https://github.com/tigges/YUZU-LIVE/tree/cursor/design-archive-426a) branch.

## Making a change

1. Open a pull request against `main`.
2. Try it on the Cloudways **staging** app: Deployment via Git → branch = the PR branch → **Pull**. Check desktop and phone.
3. Merge the pull request.
4. On the **live** app: Deployment via Git → branch `main` → **Pull**.
   Deployment path: `public_html/wp-content/themes/yuzu-round/` (never `public_html/`).
5. Purge the cache: Breeze → **Purge All Cache** (with *Varnish* ticked), or Cloudways → Application → **Purge Varnish**. The theme also purges itself on the first request after a pull.

Local preview:

```bash
python3 -m http.server 4173 --directory site
```

## Theme zip (fresh WordPress installs only)

The live site does not need the zip. For a new WordPress install, download the latest build from the [theme-latest release](https://github.com/tigges/YUZU-LIVE/releases/download/theme-latest/yuzu-round.zip) (rebuilt by GitHub Actions on every merge), then **Appearance → Themes → Add New → Upload Theme → Activate** and set **Settings → Permalinks → Post name**. Activation creates the pages and sets Home as the front page.

To build it yourself: `bash scripts/pack-theme.sh` (or `.\scripts\pack-theme.ps1` on Windows).

Do not rebuild the pages in Elementor, Spectra, Kadence or the site editor; they change spacing, radius and the overlapping hero.

## What the salon edits in wp-admin

- **Salon details**: opening hours, holidays and special days, the notice banner, the Google rating, analytics, careers listing on Google (hiring on/off, date, pay), the online gift card link, and click counts for Book, Call, Directions and Email.
- **Price list**: every price table, the homepage From prices and card sentences.
- **Page copy**: the words on each page.
- **Salon details → Instagram photos**: the photos on the Instagram landing page (pick from the Media Library; empty slots keep the theme photo).
- **Salon details → Site map**: every page, short link and outside link, with where each page is linked from and whether it is on Google. Built from the theme each time it opens.
- **Reviews & questions**: one list of reviews and one of questions for the whole site; tick the pages each one shows on. Answers can use `{hours}` and `{from:cut}` (or colour, highlights, treatments) so times and prices come from Salon details and Prices.
- **Offers**: the homepage offer cards and the details that open under them, in order; each has a look, an optional day for the “Today” flag, a booking or price-list button and a link name (`yuzu.hair/#tuesdays`). The offers Google reads come from the same list. Each offer’s title and “In one line” also make the offers list on the Instagram page (tick “Instagram page”), the offers answer on Questions and the offers paragraph in Terms; `{offers}` in an answer or page copy shows that list.

## Book, maps, social

- Book: https://www.phorest.com/salon/yuzuhairandbeauty
- Maps: https://maps.app.goo.gl/bhFS5wwkW3xxAdKd8
- Instagram: https://www.instagram.com/yuzuhairandbeauty/
- TikTok: https://www.tiktok.com/@yuzuhairandbeauty.est16
- Facebook: https://www.facebook.com/YUZUHairandBeauty/
