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
    if (desc) desc.textContent = text[level];
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

  board.addEventListener('click', function (e) {
    if (board.hasAttribute('data-level') && e.target.closest('.price-row[data-levels] > summary')) e.preventDefault();
  });
})();
