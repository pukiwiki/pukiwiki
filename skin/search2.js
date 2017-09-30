// PukiWiki - Yet another WikiWikiWeb clone.
// search2.js
// Copyright
//   2017 PukiWiki Development Team
// License: GPL v2 or (at your option) any later version
//
// PukiWiki search2 pluign - JavaScript client script
window.addEventListener && window.addEventListener('DOMContentLoaded', function() {
  function enableSearch2() {
    var aroundLines = 2;
    var maxResultLines = 20;
    var minBlockLines = 5;
    var minSearchWaitMilliseconds = 100;
    var kanaMap = null;
    function escapeHTML (s) {
      if(typeof s !== 'string') {
        s = '' + s;
      }
      return s.replace(/[&"<>]/g, function(m) {
        return {
          '&': '&amp;',
          '"': '&quot;',
          '<': '&lt;',
          '>': '&gt;'
        }[m];
      });
    }
    function doSearch(searchText, session, startIndex) {
      var url = './?cmd=search2&action=query';
      var props = getSiteProps();
      url += '&encode_hint=' + encodeURIComponent('\u3077');
      if (searchText) {
        url += '&q=' + encodeURIComponent(searchText);
      }
      if (session.base) {
        url += '&base=' + encodeURIComponent(session.base);
      }
      url += '&start=' + startIndex;
      fetch (url
      ).then(function(response){
        if (response.ok) {
          return response.json();
        } else {
          throw new Error(response.status + ': ' +
            + response.statusText + ' on ' + url);
        }
      }).then(function(obj) {
        showResult(obj, session, searchText);
      })['catch'](function(err){
        console.log(err);
        console.log('Error! Please check JavaScript console' + '\n' + JSON.stringify(err) + '|' + err);
      });
    }
    function getMessageTemplate(idText, defaultText) {
      var messageHolder = document.querySelector('#' + idText);
      var messageTemplate = (messageHolder && messageHolder.value) || defaultText;
      return messageTemplate;
    }
    function getAuthorInfo(text) {

    }
    function getPassage(now, dateText) {
      if (! dateText) {
        return '';
      }
      var units = [{u: 'm', max: 60}, {u: 'h', max: 24}, {u: 'd', max: 1}];
      var d = new Date();
      d.setTime(Date.parse(dateText));
      var t = (now.getTime() - d.getTime()) / (1000 * 60); // minutes
      var unit = units[0].u, card = units[0].max;
      for (var i = 0; i < units.length; i++) {
        unit = units[i].u, card = units[i].max;
        if (t < card) break;
        t = t / card;
      }
      return '(' + Math.floor(t) + unit + ')';
    }
    function removeSearchOperators(searchText) {
      var sp = searchText.split(/\s+/);
      if (sp.length <= 1) {
        return searchText;
      }
      var hasOr = false;
      for (var i = sp.length - 1; i >= 0; i--) {
        if (sp[i] === 'OR') {
          hasOr = true;
          sp.splice(i, 1);
        }
      }
      return sp.join(' ');
    }
    function showResult(obj, session, searchText) {
      var searchRegex = textToRegex(removeSearchOperators(searchText));
      var ul = document.querySelector('#result-list');
      if (!ul) return;
      if (obj.start_index === 0) {
        ul.innerHTML = '';
      }
      if (! session.scan_page_count) session.scan_page_count = 0;
      if (! session.read_page_count) session.read_page_count = 0;
      if (! session.hit_page_count) session.hit_page_count = 0;
      var prevHitPageCount = session.hit_page_count;
      session.scan_page_count += obj.scan_page_count;
      session.read_page_count += obj.read_page_count;
      session.hit_page_count += obj.results.length;
      session.page_count = obj.page_count;
      if (prevHitPageCount === 0 && session.hit_page_count > 0) {
        var div = document.querySelector('._plugin_search2_second_form');
        if (div) {
          div.style.display = 'block';
        }
      }
      var msg = obj.message;
      var notFoundMessageTemplate = getMessageTemplate('_plugin_search2_msg_result_notfound',
        'No page which contains $1 has been found.');
      var foundMessageTemplate = getMessageTemplate('_plugin_search2_msg_result_found',
        'In the page <strong>$2</strong>, <strong>$3</strong> pages that contain all the terms $1 were found.');
      var searchTextDecorated = findAndDecorateText(searchText, searchRegex);
      if (searchTextDecorated === null) searchTextDecorated = escapeHTML(searchText);
      var messageTemplate = foundMessageTemplate;
      if (obj.search_done && session.hit_page_count === 0) {
        messageTemplate = notFoundMessageTemplate;
      }
      msg = messageTemplate.replace(/\$1|\$2|\$3/g, function(m){
        return {
          '$1': searchTextDecorated,
          '$2': session.hit_page_count,
          '$3': session.read_page_count
        }[m];
      });
      setSearchMessage(msg);
      var progress = ' (read:' + session.read_page_count + ', scanned:' +
        session.scan_page_count + ', all:' + session.page_count + ')';
      var e = document.querySelector('#_plugin_search2_msg_searching');
      var msg = e && e.value || 'Searching...';
      setSearchStatus(msg + progress);
      if (obj.search_done) {
        setTimeout(function(){
          setSearchStatus('');
        }, 5000);
      }
      var results = obj.results;
      var now = new Date();
      results.forEach(function(val, index) {
        var fragment = document.createDocumentFragment();
        var li = document.createElement('li');
        var hash = '#q=' + encodeSearchTextForHash(searchText);
        var href = val.url + hash;
        var decoratedName = findAndDecorateText(val.name, searchRegex);
        if (! decoratedName) {
          decoratedName = escapeHTML(val.name);
        }
        var author = getAuthorHeader(val.body);
        var updatedAt = '';
        if (author) {
          updatedAt = getUpdateTimeFromAuthorInfo(author);
        } else {
          updatedAt = val.updated_at;
        }
        var liHtml = '<a href="' + escapeHTML(href) + '">' + decoratedName + '</a> ' +
          getPassage(now, updatedAt);
        li.innerHTML = liHtml;
        var a = li.querySelector('a');
        if (a && a.hash) {
          if (a.hash !== hash) {
            // Some browser execute encodeHTML(hash) automatically. Support them.
            a.href = val.url + '#encq=' + encodeSearchTextForHash(searchText);
          }
        }
        fragment.appendChild(li);
        var div = document.createElement('div');
        div.classList.add('search-result-detail');
        var head = document.createElement('div');
        head.classList.add('search-result-page-summary');
        head.innerHTML = escapeHTML(getBodySummary(val.body));
        div.appendChild(head);
        var summary = getSummary(val.body, searchRegex);
        for (var i = 0; i < summary.length; i++) {
          var pre = document.createElement('pre');
          pre.innerHTML = summary[i].lines.join('\n');
          div.appendChild(pre);
        }
        fragment.appendChild(div);
        ul.appendChild(fragment);
      });
      if (!obj.search_done && obj.next_start_index) {
        var waitE = document.querySelector('#_search2_search_wait_milliseconds');
        var interval = minSearchWaitMilliseconds;
        try {
          interval = parseInt(waitE.value);
        } catch (e) {
          interval = minSearchWaitMilliseconds;
        }
        if (interval < minSearchWaitMilliseconds) {
          interval = minSearchWaitMilliseconds;
        }
        setTimeout(function(){
          doSearch(searchText, session, obj.next_start_index);
        }, interval);
      }
    }
    function prepareKanaMap() {
      if (kanaMap !== null) return;
      if (!String.prototype.normalize) {
        kanaMap = {};
        return;
      }
      var dakuten = '\uFF9E';
      var maru = '\uFF9F';
      var map = {};
      for (var c = 0xFF61; c <=0xFF9F; c++) {
        var han = String.fromCharCode(c);
        var zen = han.normalize('NFKC');
        map[zen] = han;
        var hanDaku = han + dakuten;
        var zenDaku = hanDaku.normalize('NFKC');
        if (zenDaku.length === 1) { // +Handaku-ten OK
            map[zenDaku] = hanDaku;
        }
        var hanMaru = han + maru;
        var zenMaru = hanMaru.normalize('NFKC');
        if (zenMaru.length === 1) { // +Maru OK
            map[zenMaru] = hanMaru;
        }
      }
      kanaMap = map;
    }
    function textToRegex(searchText) {
      if (!searchText) return null;
      var regEscape = /[\\^$.*+?()[\]{}|]/g;
      //             1:Symbol             2:Katakana        3:Hiragana
      var regRep = /([\\^$.*+?()[\]{}|])|([\u30a1-\u30f6])|([\u3041-\u3096])/g;
      var s1 = searchText.replace(/^\s+|\s+$/g, '');
      var sp = s1.split(/\s+/);
      var rText = '';
      prepareKanaMap();
      for (var i = 0; i < sp.length; i++) {
        if (rText !== '') {
          rText += '|'
        }
        var s = sp[i];
        if (s.normalize) {
          s = s.normalize('NFKC');
        }
        var s2 = s.replace(regRep, function(m, m1, m2, m3){
          if (m1) {
            // Symbol - escape with prior backslach
            return '\\' + m1;
          } else if (m2) {
            // Katakana
            var r = '(?:' + String.fromCharCode(m2.charCodeAt(0) - 0x60) +
              '|' + m2;
            if (kanaMap[m2]) {
              r += '|' + kanaMap[m2];
            }
            r += ')';
            return r;
          } else if (m3) {
            // Hiragana
            var katakana = String.fromCharCode(m3.charCodeAt(0) + 0x60);
            var r = '(?:' + m3 + '|' + katakana;
            if (kanaMap[katakana]) {
              r += '|' + kanaMap[katakana];
            }
            r += ')';
            return r;
          }
          return m;
        });
        rText += '(' + s2 + ')';
      }
      return new RegExp(rText, 'ig');
    }
    function getAuthorHeader(body) {
      var start = 0;
      var pos;
      while ((pos = body.indexOf('\n', start)) >= 0) {
        var line = body.substring(start, pos);
        if (line.match(/^#author\(/, line)) {
          return line;
        } else if (line.match(/^#freeze(\W|$)/, line)) {
          // Found #freeze still in header
        } else {
          // other line, #author not found
          return null;
        }
        start = pos + 1;
      }
      return null;
    }
    function getUpdateTimeFromAuthorInfo(authorInfo) {
      var m = authorInfo.match(/^#author\("([^;"]+)(;[^;"]+)?/);
      if (m) {
        return m[1];
      }
      return '';
    }
    function getTargetLines(body, searchRegex) {
      var lines = body.split('\n');
      var found = [];
      var foundLines = [];
      var isInAuthorHeader = true;
      var lastFoundLineIndex = -1 - aroundLines;
      var lastAddedLineIndex = lastFoundLineIndex;
      var blocks = [];
      var lineCount = 0;
      for (var index = 0, length = lines.length; index < length; index++) {
        var line = lines[index];
        if (isInAuthorHeader) {
          // '#author line is not search target'
          if (line.match(/^#author\(/)) {
            // Remove this line from search target
            continue;
          } else if (line.match(/^#freeze(\W|$)/)) {
            // Still in header
          } else {
            // Already in body
            isInAuthorHeader = false;
          }
        }
        var decorated = findAndDecorateText(line, searchRegex);
        if (decorated === null) {
          if (index < lastFoundLineIndex + aroundLines + 1) {
            foundLines.push('' + (index + 1) + ':\t' + escapeHTML(lines[index]));
            lineCount++;
            lastAddedLineIndex = index;
          }
        } else {
          var startIndex = Math.max(Math.max(lastAddedLineIndex + 1, index - aroundLines), 0);
          if (lastAddedLineIndex + 1 < startIndex) {
            // Newly found!
            var block = {
              startIndex: startIndex,
              foundLineIndex: index,
              lines: []
            };
            foundLines = block.lines;
            blocks.push(block);
          }
          if (lineCount >= maxResultLines) {
            foundLines.push('...');
            return blocks;
          }
          for (var i = startIndex; i < index; i++) {
            foundLines.push('' + (i + 1) + ':\t' + escapeHTML(lines[i]));
            lineCount++;
          }
          foundLines.push('' + (index + 1) + ':\t' + decorated);
          lineCount++;
          lastFoundLineIndex = lastAddedLineIndex = index;
        }
      }
      return blocks;
    }
    function getSummary(bodyText, searchRegex) {
      return getTargetLines(bodyText, searchRegex);
    }
    function hookSearch2(e) {
      var form = document.querySelector('form');
      if (form && form.q) {
        var q = form.q;
        if (q.value === '') {
          q.focus();
        }
      }
    }
    function getBodySummary(body) {
      var lines = body.split('\n');
      var isInAuthorHeader = true;
      var summary = [];
      var lineCount = 0;
      for (var index = 0, length = lines.length; index < length; index++) {
        var line = lines[index];
        if (isInAuthorHeader) {
          // '#author line is not search target'
          if (line.match(/^#author\(/)) {
            // Remove this line from search target
            continue;
          } else if (line.match(/^#freeze(\W|$)/)) {
            continue;
            // Still in header
          } else {
            // Already in body
            isInAuthorHeader = false;
          }
        }
        line = line.replace(/^\s+|\s+$/g, '');
        if (line.length === 0) continue; // Empty line
        if (line.match(/^#\w+/)) continue; // Block-type plugin
        if (line.match(/^\/\//)) continue; // Comment
        if (line.substr(0, 1) === '*') {
          line = line.replace(/\s*\[\#\w+\]$/, ''); // Remove anchor
        }
        summary.push(line);
        if (summary.length >= 10) {
          continue;
        }
      }
      return summary.join(' ').substring(0, 150);
    }
    function removeEncodeHint() {
      // Remove 'encode_hint' if site charset is UTF-8
      var props = getSiteProps();
      if (!props.is_utf8) return;
      var forms = document.querySelectorAll('form');
      forEach(forms, function(form){
        if (form.cmd && form.cmd.value === 'search2') {
          if (form.encode_hint && (typeof form.encode_hint.removeAttribute === 'function')) {
            form.encode_hint.removeAttribute('name');
          }
        }
      });
    }
    function kickFirstSearch() {
      var form = document.querySelector('._plugin_search2_form');
      var searchText = form && form.q;
      if (!searchText) return;
      if (searchText && searchText.value) {
        var e = document.querySelector('#_plugin_search2_msg_searching');
        var msg = e && e.value || 'Searching...';
        setSearchStatus(msg);
        var base = '';
        forEach(form.querySelectorAll('input[name="base"]'), function(radio){
          if (radio.checked) base = radio.value;
        });
        doSearch(searchText.value, {base: base}, 0);
      }
    }
    function setSearchStatus(statusText) {
      var statusList = document.querySelectorAll('._plugin_search2_search_status');
      forEach(statusList, function(statusObj){
        statusObj.textContent = statusText;
      });
    }
    function setSearchMessage(msgHTML) {
      var objList = document.querySelectorAll('._plugin_search2_message');
      forEach(objList, function(obj){
        obj.innerHTML = msgHTML;
      });
    }
    function forEach(nodeList, func) {
      if (nodeList.forEach) {
        nodeList.forEach(func);
      } else {
        for (var i = 0, n = nodeList.length; i < n; i++) {
          func(nodeList[i], i);
        }
      }
    }
    function replaceSearchWithSearch2() {
      forEach(document.querySelectorAll('form'), function(f){
        if (f.action.match(/cmd=search$/)) {
          f.addEventListener('submit', function(e) {
            var q = e.target.word.value;
            var base = '';
            forEach(f.querySelectorAll('input[name="base"]'), function(radio){
              if (radio.checked) base = radio.value;
            });
            var props = getSiteProps();
            var loc = document.location;
            var baseUri = loc.protocol + '//' + loc.host + loc.pathname;
            if (props.base_uri_pathname) {
              baseUri = props.base_uri_pathname;
            }
            var url = baseUri + '?' +
              (props.is_utf8 ? '' : 'encode_hint=' +
                encodeURIComponent('\u3077') + '&') +
              'cmd=search2' +
              '&q=' + encodeSearchText(q) +
              (base ? '&base=' + encodeURIComponent(base) : '');
            e.preventDefault();
            setTimeout(function() {
              location.href = url;
            }, 1);
            return false;
          });
          var radios = f.querySelectorAll('input[type="radio"][name="type"]');
          forEach(radios, function(radio){
            if (radio.value === 'AND') {
              radio.addEventListener('click', onAndRadioClick);
            } else if (radio.value === 'OR') {
              radio.addEventListener('click', onOrRadioClick);
            }
          });
          function onAndRadioClick(e) {
            var sp = removeSearchOperators(f.word.value).split(/\s+/);
            var newText = sp.join(' ');
            if (f.word.value !== newText) {
              f.word.value = newText;
            }
          }
          function onOrRadioClick(e) {
            var sp = removeSearchOperators(f.word.value).split(/\s+/);
            var newText = sp.join(' OR ');
            if (f.word.value !== newText) {
              f.word.value = newText;
            }
          }
        }
      });
    }
    function encodeSearchText(q) {
      var sp = q.split(/\s+/);
      for (var i = 0; i < sp.length; i++) {
        sp[i] = encodeURIComponent(sp[i]);
      }
      return sp.join('+');
    }
    function encodeSearchTextForHash(q) {
      var sp = q.split(/\s+/);
      return sp.join('+');
    }
    function findAndDecorateText(text, searchRegex) {
      var isReplaced = false;
      var lastIndex = 0;
      var m;
      var decorated = '';
      searchRegex.lastIndex = 0;
      while ((m = searchRegex.exec(text)) !== null) {
        isReplaced = true;
        var pre = text.substring(lastIndex, m.index);
        decorated += escapeHTML(pre);
        for (var i = 1; i < m.length; i++) {
          if (m[i]) {
            decorated += '<strong class="word' + (i - 1) + '">' + escapeHTML(m[i]) + '</strong>'
          }
        }
        lastIndex = searchRegex.lastIndex;
      }
      if (isReplaced) {
        decorated += escapeHTML(text.substr(lastIndex));
        return decorated;
      }
      return null;
    }
    function getSearchTextInLocationHash() {
      // TODO Cross browser
      var hash = location.hash;
      if (!hash) return '';
      var q = '';
      if (hash.substr(0, 3) === '#q=') {
        q = hash.substr(3).replace(/\+/g, ' ');
      } else if (hash.substr(0, 6) === '#encq=') {
        q = decodeURIComponent(hash.substr(6).replace(/\+/g, ' '));
      }
      return q;
    }
    function colorSearchTextInBody() {
      var searchText = getSearchTextInLocationHash();
      if (!searchText) return;
      var searchRegex = textToRegex(removeSearchOperators(searchText));
      var headReText = '([\\s\\b]|^)';
      var tailReText = '\\b';
      var ignoreTags = ['INPUT', 'TEXTAREA', 'BUTTON',
        'SCRIPT', 'FRAME', 'IFRAME'];
      function colorSearchText(element, searchRegex) {
        var decorated = findAndDecorateText(element.nodeValue, searchRegex);
        if (decorated) {
          var span = document.createElement('span');
          span.innerHTML = decorated;
          element.parentNode.replaceChild(span, element);
        }
      }
      function walkElement(element) {
        var e = element.firstChild;
        while (e) {
          if (e.nodeType == 3 && e.nodeValue &&
              e.nodeValue.length >= 2 && /\S/.test(e.nodeValue)) {
            var next = e.nextSibling;
            colorSearchText(e, searchRegex);
            e = next;
          } else {
            if (e.nodeType == 1 && ignoreTags.indexOf(e.tagName) == -1) {
              walkElement(e);
            }
            e = e.nextSibling;
          }
        }
      }
      var target = document.getElementById('body');
      walkElement(target);
    }
    function showNoSupportMessage() {
      var pList = document.getElementsByClassName('_plugin_search2_nosupport_message');
      for (var i = 0; i < pList.length; i++) {
        var p = pList[i];
        p.style.display = 'block';
      }
    }
    function isEnabledFetchFunctions() {
      if (window.fetch && document.querySelector && window.JSON) {
        return true;
      }
      return false;
    }
    function isEnableServerFunctions() {
      var props = getSiteProps();
      if (props.json_enabled) return true;
      return false;
    }
    function getSiteProps() {
      var empty = {};
      var propsDiv = document.getElementById('pukiwiki-site-properties');
      if (!propsDiv) return empty;
      var jsonE = propsDiv.querySelector('div[data-key="site-props"]');
      if (!jsonE) return emptry;
      var props = JSON.parse(jsonE.getAttribute('data-value'));
      return props || empty;
    }
    colorSearchTextInBody();
    if (! isEnabledFetchFunctions()) {
      showNoSupportMessage();
      return;
    }
    if (! isEnableServerFunctions()) return;
    replaceSearchWithSearch2();
    hookSearch2();
    removeEncodeHint();
    kickFirstSearch();
  }
  enableSearch2();
});
