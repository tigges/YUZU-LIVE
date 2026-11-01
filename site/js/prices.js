(function () {
  var board = document.querySelector('.price-board');
  if (!board) return;

  var map = { '#cut': 'pm-cut', '#colour': 'pm-colour', '#highlights': 'pm-high', '#treatments': 'pm-treat' };
  var fromHash = map[location.hash];
  if (fromHash) {
    var hashInput = document.getElementById(fromHash);
    if (hashInput) hashInput.checked = true;
  }

  var back = document.querySelector('a.blend-back[data-back]');
  if (back) {
    var homeBase = back.getAttribute('href').split('#')[0];
    var fromServices = /^#(cut|colour|highlights|treatments|services)$/.test(location.hash);
    var ref = '';
    try {
      if (document.referrer) {
        var u = new URL(document.referrer);
        if (u.origin === location.origin) ref = u.pathname;
      }
    } catch (e) {}
    var labels = [
      [/\/patch(\.html|\/)?$/, '← Patch testing'],
      [/\/questions(\.html|\/)?$/, '← Questions'],
      [/\/team(\.html|\/)?$/, '← Join our team'],
      [/\/about(\.html|\/)?$/, '← About'],
      [/\/haircut-ealing(\.html|\/)?$/, '← Haircuts'],
      [/\/hair-colour-ealing(\.html|\/)?$/, '← Hair colour'],
      [/\/balayage-ealing(\.html|\/)?$/, '← Balayage'],
      [/\/hair-smoothing-ealing(\.html|\/)?$/, '← Smoothing'],
    ];
    if (fromServices) {
      back.textContent = '← Services';
      back.setAttribute('href', homeBase + '#services');
    } else {
      for (var i = 0; i < labels.length; i++) {
        if (labels[i][0].test(ref)) {
          back.textContent = labels[i][1];
          back.setAttribute('href', document.referrer);
          break;
        }
      }
    }
  }

  var desc = document.querySelector('[data-level-desc]');
  var rows = board.querySelectorAll('[data-levels]');
  var text = {
    '': 'Price ranges from Creative Director (CD) to Junior Stylist.',
    creative: 'Creative Director (CD): lead stylist and salon visionary.',
    senior: 'Senior Stylist: extensive experience, advanced cutting and colour.',
    stylist: 'Stylist: fully qualified, all cuts and colour.',
    junior: 'Junior Stylist: newly qualified, under senior guidance.',
  };
  var panelCut = board.querySelector('.panel-cut');
  var booked = board.querySelectorAll('[data-book]');
  var levelName = { creative: 'Creative Director', senior: 'Senior Stylist', stylist: 'Stylist', junior: 'Junior Stylist' };

  function syncBook(level) {
    booked.forEach(function (row) {
      var link = row.querySelector('.row-book') || row.querySelector('.price-book');
      if (!link) return;
      if (!link.dataset.href) {
        link.dataset.href = link.getAttribute('href');
        link.dataset.label = link.getAttribute('aria-label') || '';
      }
      var href = level ? JSON.parse(row.dataset.book)[level] : '';
      var fig = row.querySelector('.price-fig');
      var price = !level ? '' : row.dataset.levels ? JSON.parse(row.dataset.levels)[level] : fig ? fig.textContent.trim() : '';
      if (link.classList.contains('row-book')) link.hidden = !(href && price);
      if (href) {
        var name = (row.querySelector('.price-name') || {}).firstChild;
        link.setAttribute('href', href);
        link.setAttribute('aria-label', 'Book ' + (name ? name.textContent.trim() : '') + ' with a ' + levelName[level] + (price ? ', ' + price : '') + ' (opens Phorest in a new tab)');
      } else {
        link.setAttribute('href', link.dataset.href);
        link.setAttribute('aria-label', link.dataset.label);
      }
    });
  }

  function syncCutOpen() {
    if (board.hasAttribute('data-level')) return;
    if (panelCut) {
      var who = panelCut.getAttribute('data-who') || '';
      panelCut.querySelectorAll('details.price-row').forEach(function (d) {
        d.open = Boolean(who && d.getAttribute('data-who') === who);
      });
    }
    board.querySelectorAll('.panel-colour > details.price-row, .panel-high > details.price-row, .panel-treat > details.price-row').forEach(function (d) {
      d.open = true;
    });
  }

  function apply(level) {
    if (level) board.setAttribute('data-level', level);
    else board.removeAttribute('data-level');
    rows.forEach(function (row) {
      var fig = row.querySelector('.price-fig');
      if (!fig.dataset.all) fig.dataset.all = fig.textContent;
      var price = level ? JSON.parse(row.dataset.levels)[level] : fig.dataset.all;
      fig.innerHTML = price || '—<span class="vh"> not offered at this level</span>';
      row.classList.toggle('is-missing', !price);
      if (level && row.tagName === 'DETAILS') row.open = false;
    });
    if (!level) syncCutOpen();
    syncBook(level);
    if (desc) desc.textContent = text[level];
    document.querySelectorAll('[data-level-stylists] > p').forEach(function (p) {
      p.hidden = p.getAttribute('data-for') !== (level || '');
    });
    if (board.hasAttribute('data-search')) runSearch();
  }

  var lastWho = (board.querySelector('input[name="who"]:checked') || {}).value || '';
  board.querySelectorAll('input[name="who"]').forEach(function (r) {
    r.addEventListener('click', function () {
      if (r.value && r.value === lastWho) {
        var allWho = board.querySelector('input[name="who"][value=""]');
        if (allWho && panelCut) {
          allWho.checked = true;
          lastWho = '';
          panelCut.removeAttribute('data-who');
          syncCutOpen();
        }
      } else {
        lastWho = r.value;
      }
    });
    r.addEventListener('change', function () {
      lastWho = r.value;
      if (!panelCut) return;
      if (r.value) panelCut.setAttribute('data-who', r.value);
      else panelCut.removeAttribute('data-who');
      syncCutOpen();
    });
  });

  var lastLevel = (document.querySelector('input[name="level"]:checked') || {}).value || '';
  document.querySelectorAll('input[name="level"]').forEach(function (input) {
    input.addEventListener('click', function () {
      if (input.value && input.value === lastLevel) {
        var allLvl = document.querySelector('input[name="level"][value=""]');
        if (allLvl) {
          allLvl.checked = true;
          lastLevel = '';
          apply('');
        }
      } else {
        lastLevel = input.value;
      }
    });
    input.addEventListener('change', function () {
      lastLevel = input.value;
      apply(input.value);
    });
  });

  var qp = new URLSearchParams(location.search);
  var qWho = qp.get('who');
  if (qWho && /^(ladies|gents|children)$/.test(qWho) && panelCut) {
    var whoRadio = board.querySelector('input[name="who"][value="' + qWho + '"]');
    if (whoRadio) {
      whoRadio.checked = true;
      lastWho = qWho;
      panelCut.setAttribute('data-who', qWho);
      syncCutOpen();
    }
  }
  var qLevel = qp.get('level');
  if (qLevel && /^(creative|senior|stylist|junior)$/.test(qLevel)) {
    var lvlRadio = document.querySelector('input[name="level"][value="' + qLevel + '"]');
    if (lvlRadio) {
      lvlRadio.checked = true;
      lastLevel = qLevel;
      apply(qLevel);
    }
  }

  var tabs = board.querySelector('.price-tabs');
  if (tabs) {
    var showTab = function (radio, behavior) {
      var label = tabs.querySelector('label[for="' + radio.id + '"]');
      if (label) tabs.scrollTo({ left: label.offsetLeft - tabs.offsetLeft - 18, behavior: behavior });
    };
    var hashByRadio = { 'pm-cut': '#cut', 'pm-colour': '#colour', 'pm-high': '#highlights', 'pm-treat': '#treatments' };
    board.querySelectorAll('.price-radio').forEach(function (radio) {
      radio.addEventListener('change', function () {
        showTab(radio, 'smooth');
        if (hashByRadio[radio.id] && history.replaceState) history.replaceState(null, '', hashByRadio[radio.id]);
      });
      if (radio.checked) showTab(radio, 'auto');
    });
  }

  var search = document.querySelector('[data-price-search]');
  var q = search && search.querySelector('input');
  var status = search && search.querySelector('[data-search-status]');
  var none = search && search.querySelector('[data-search-none]');
  var panelWords = {
    'panel-cut': 'cut cuts haircut haircuts trim style styling',
    'panel-colour': 'colour color colours colors tint dye grey gray coverage',
    'panel-high': 'highlights highlight balayage foils foil foiliage lowlights babylights ombre blonde bleach lightening lighten',
    'panel-treat': 'treatment treatments',
  };
  var nameWords = [
    [/aura|smoothing/i, 'keratin straightening straighten smoothing smooth frizz brazilian'],
    [/nashi|filler|k2/i, 'moisture conditioning conditioner repair mask hydration hydrating dry damaged'],
    [/blow-?dry/i, 'blowdry blow dry blowout blow-out'],
    [/kids|boys|girls|teen|children/i, 'kids kid child children boy boys girl girls teen teens teenager'],
    [/gents/i, 'men mens man male gents gentlemen barber'],
    [/ladies/i, 'women womens woman female ladies'],
    [/root/i, 'roots regrowth root'],
    [/toner/i, 'gloss glaze toning'],
    [/illumi/i, 'illumina wella'],
    [/pre-lighten/i, 'bleach lightening lighten'],
    [/remover/i, 'correction remove removal'],
    [/blow-drys|roots for|package/i, 'package packages deal bundle offer'],
  ];
  var norm = function (t) {
    return String(t).toLowerCase().replace(/&/g, ' and ').replace(/[^a-z0-9£]+/g, ' ').trim();
  };
  var items = [];
  board.querySelectorAll('.price-panel').forEach(function (panel) {
    var base = '';
    for (var key in panelWords) if (panel.classList.contains(key)) base = panelWords[key];
    panel.querySelectorAll('.price-row, .packages li').forEach(function (el) {
      var own = panel.classList.contains('panel-high') && el.closest('.price-more') ? 'highlights' : base;
      var nameEl = el.querySelector('.price-name') || el.firstElementChild;
      var name = nameEl ? nameEl.textContent : '';
      var extra = '';
      nameWords.forEach(function (pair) {
        if (pair[0].test(name)) extra += ' ' + pair[1];
      });
      items.push({ el: el, words: norm(name + ' ' + own + extra).split(' ') });
    });
  });
  var saved = null;
  var sent = {};
  var idle = 0;

  function enterSearch() {
    if (saved) return;
    saved = { who: panelCut ? panelCut.getAttribute('data-who') : null, open: [] };
    board.querySelectorAll('details').forEach(function (d) {
      saved.open.push([d, d.open]);
    });
    if (panelCut) panelCut.removeAttribute('data-who');
    board.setAttribute('data-search', '');
  }

  function leaveSearch() {
    if (!saved) return;
    board.removeAttribute('data-search');
    board.querySelectorAll('[data-search-hidden]').forEach(function (el) {
      el.hidden = false;
      el.removeAttribute('data-search-hidden');
    });
    if (panelCut && saved.who) panelCut.setAttribute('data-who', saved.who);
    saved.open.forEach(function (pair) {
      pair[0].open = pair[1];
    });
    if (board.hasAttribute('data-level')) {
      board.querySelectorAll('details.price-row[data-levels]').forEach(function (d) { d.open = false; });
    } else {
      syncCutOpen();
    }
    saved = null;
    status.textContent = '';
    none.hidden = true;
  }

  function hide(el, off) {
    el.hidden = off;
    if (off) el.setAttribute('data-search-hidden', '');
    else el.removeAttribute('data-search-hidden');
  }

  function report(query, found) {
    var slug = norm(query).replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '').slice(0, 40);
    if (slug.length < 3 || sent[slug]) return;
    sent[slug] = true;
    if (typeof window.plausible === 'function') window.plausible('Price search', { props: { q: slug, found: String(found) } });
    var endpoint = (document.querySelector('meta[name="yuzu-track"]') || {}).content;
    if (!found && endpoint && navigator.sendBeacon) navigator.sendBeacon(endpoint, new URLSearchParams({ e: 'search-miss', p: 'prices', s: slug }));
  }

  function runSearch() {
    var terms = norm(q.value).split(' ').filter(Boolean);
    if (!terms.length) {
      leaveSearch();
      return;
    }
    enterSearch();
    var level = board.hasAttribute('data-level');
    var found = 0;
    items.forEach(function (it) {
      var hit = terms.every(function (t) {
        return it.words.some(function (w) { return w.indexOf(t) === 0; });
      });
      hide(it.el, !hit);
      if (!hit) return;
      found++;
      if (it.el.tagName === 'DETAILS' && !(level && it.el.hasAttribute('data-levels'))) it.el.open = true;
    });
    board.querySelectorAll('.price-more, .packages, .price-panel').forEach(function (box) {
      var any = box.querySelector('.price-row:not([hidden]), .packages li:not([hidden])');
      hide(box, !any);
      if (any && box.tagName === 'DETAILS') box.open = true;
    });
    status.textContent = found ? found + (found === 1 ? ' service' : ' services') + ' found' : '';
    none.hidden = found > 0;
    clearTimeout(idle);
    var query = q.value;
    idle = setTimeout(function () { report(query, found); }, 1500);
  }

  if (q) {
    search.hidden = false;
    q.addEventListener('input', runSearch);
    q.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && q.value) {
        q.value = '';
        runSearch();
      }
    });
    var qSearch = qp.get('q');
    if (qSearch) {
      q.value = qSearch.slice(0, 60);
      runSearch();
    }
  }

  board.addEventListener('click', function (e) {
    if (e.target.closest('a')) return;
    if (board.hasAttribute('data-level') && e.target.closest('.price-row[data-levels] > summary')) e.preventDefault();
  });
})();
