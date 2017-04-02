// PukiWiki - Yet another WikiWikiWeb clone.
// main.js
// Copyright
//   2017 PukiWiki Development Team
// License: GPL v2 or (at your option) any later version
//
// PukiWiki JavaScript client script
if (window.addEventListener) {
  window.addEventListener('DOMContentLoaded', function() {
    if (!Array.prototype.indexOf || !document.createDocumentFragment) {
      return;
    }
    var headReText = '([\\s\\b]|^)';
    var tailReText = '\\b';
    var _siteList = getSiteListFromBody();
    function ticketToLink(keyText) {
      var siteList = getSiteList();
      for (var i = 0; i < siteList.length; i++) {
        var site = siteList[i];
        var m = keyText.match(site.re);
        if (m) {
          var title = site.title;
          var ticketKey = m[3]
          if (title) {
            title = title.replace(/\$1/g, ticketKey);
          }
          return {
            url: site.base_url + m[3],
            title: title
          };
        }
      }
      return null;
    }
    function regexEscape(key) {
      return key.replace(/[\-\.]/g, function (m) {
        return '\\' + m;
      });
    }
    function setupSites(siteList) {
      for (var i = 0, length = siteList.length; i < length; i++) {
        var site = siteList[i];
        var reText = '';
        switch (site.type) {
          case 'jira':
            reText = '(' + regexEscape(site.key) + '):' + '([A-Z][A-Z0-9_]+-\\d+)';
            break;
          case 'redmine':
            reText = '(' + regexEscape(site.key) + '):' + '(\\d+)';
            break;
          case 'git':
            reText = '(' + regexEscape(site.key) + '):' + '([0-9a-f]{7,40})';
            break;
          default:
            continue;
        }
        site.reText = reText;
        site.re = new RegExp(headReText + reText + tailReText);
      }
    }
    function getSiteList() {
      return _siteList;
    }
    function getSiteListFromBody() {
      var list = [];
      var defRoot = document.querySelector('.pukiwiki-ticketlink-def');
      var siteNodes = defRoot.querySelectorAll('.pukiwiki-ticketlink-site');
      Array.prototype.forEach.call(siteNodes, function (e) {
        var siteInfoText = e.dataset && e.dataset.site;
        if (!siteInfoText) return;
        var info = textToSiteInfo(siteInfoText);
        if (info) {
          list.push(info);
        }
      });
      setupSites(list);
      return list;
    }
    function textToSiteInfo(siteDef) {
      if (!siteDef) return null;
      var info = JSON.parse(siteDef);
      if (info && info.key && info.type && info.base_url) {
        return info;
      }
      return null;
    }
    function startsWith(s, searchString) {
      if (String.prototype.startsWith) {
        return s.startsWith(searchString);
      }
      return s.substr(0, searchString.length) === searchString;
    }
    function getRegex(list) {
      var reText = '';
      for (var i = 0, length = list.length; i < length; i++) {
        if (reText.length > 0) {
          reText += '|'
        }
        reText += list[i].reText;
      }
      return new RegExp(headReText + '(' + reText + ')' + tailReText);
    }
    function makeTicketLink(element) {
      var siteList = getSiteList();
      if (!siteList || siteList.length === 0) {
        return;
      }
      var re = getRegex(siteList);
      var f, m, text = element.nodeValue;
      while (m = text.match(re)) {
        // m[1]: head, m[2]: keyText
        f || (f = document.createDocumentFragment());
        if (m.index > 0 || m[1].length > 0) {
          f.appendChild(document.createTextNode(text.substr(0, m.index) + m[1]));
        }
        var a = document.createElement('a');
        a.textContent = m[2];
        var linkInfo = ticketToLink(a.textContent);
        a.href = linkInfo.url;
        a.title = linkInfo.title;
        f.appendChild(a);
        text = text.substr(m.index + m[0].length);
      }
      if (f) {
        text.length > 0 && f.appendChild(document.createTextNode(text));
        element.parentNode.replaceChild(f, element)
      }
    }
    var ignoreTags = ['A', 'INPUT', 'TEXTAREA', 'BUTTON',
      'SCRIPT', 'FRAME', 'IFRAME'];
    function walkElement(element) {
      var e = element.firstChild;
      while (e) {
        if (e.nodeType == 3 && e.nodeValue &&
            e.nodeValue.length > 5 && /\S/.test(e.nodeValue)) {
          var next = e.nextSibling;
          makeTicketLink(e);
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
  });
}
