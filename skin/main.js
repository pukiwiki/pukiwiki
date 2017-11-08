// PukiWiki - Yet another WikiWikiWeb clone.
// main.js
// Copyright 2017 PukiWiki Development Team
// License: GPL v2 or (at your option) any later version
//
// PukiWiki JavaScript client script
window.addEventListener && window.addEventListener('DOMContentLoaded', function() { // eslint-disable-line no-unused-expressions
  'use strict';
  /**
   * @param {NodeList} nodeList
   * @param {function(Node, number): void} func
   */
  function forEach(nodeList, func) {
    if (nodeList.forEach) {
      nodeList.forEach(func);
    } else {
      for (var i = 0, n = nodeList.length; i < n; i++) {
        func(nodeList[i], i);
      }
    }
  }
  // Name for comment
  function setYourName() {
    var NAME_KEY_ID = 'pukiwiki_comment_plugin_name';
    var actionPathname = null;
    function getPathname(formAction) {
      if (actionPathname) return actionPathname;
      try {
        var u = new URL(formAction, document.location);
        var u2 = new URL('./', u);
        actionPathname = u2.pathname;
        return u2.pathname;
      } catch (e) {
        // Note: Internet Explorer doesn't support URL class
        var m = formAction.match(/^https?:\/\/([^/]+)(\/([^?&]+\/)?)/);
        if (m) {
          actionPathname = m[2]; // pathname
        } else {
          actionPathname = '/';
        }
        return actionPathname;
      }
    }
    function getNameKey(form) {
      var pathname = getPathname(form.action);
      var key = 'path.' + pathname + '.' + NAME_KEY_ID;
      return key;
    }
    function getForm(element) {
      if (element.form && element.form.tagName === 'FORM') {
        return element.form;
      }
      var e = element.parentElement;
      for (var i = 0; i < 5; i++) {
        if (e.tagName === 'FORM') {
          return e;
        }
        e = e.parentElement;
      }
      return null;
    }
    function handleCommentPlugin(form) {
      var namePrevious = '';
      var nameKey = getNameKey(form);
      if (typeof localStorage !== 'undefined') {
        namePrevious = localStorage[nameKey];
      }
      var onFocusForm = function () {
        if (form.name && !form.name.value && namePrevious) {
          form.name.value = namePrevious;
        }
      };
      var addOnForcusForm = function(eNullable) {
        if (!eNullable) return;
        if (eNullable.addEventListener) {
          eNullable.addEventListener('focus', onFocusForm);
        }
      };
      if (namePrevious) {
        var textList = form.querySelectorAll('input[type=text],textarea');
        textList.forEach(function (v) {
          addOnForcusForm(v);
        });
      }
      form.addEventListener('submit', function() {
        if (typeof localStorage !== 'undefined') {
          localStorage[nameKey] = form.name.value;
        }
      }, false);
    }
    function setNameForComment() {
      if (!document.querySelectorAll) return;
      var elements = document.querySelectorAll(
        'input[type=hidden][name=plugin][value=comment],' +
        'input[type=hidden][name=plugin][value=pcomment],' +
        'input[type=hidden][name=plugin][value=article],' +
        'input[type=hidden][name=plugin][value=bugtrack]');
      for (var i = 0; i < elements.length; i++) {
        var form = getForm(elements[i]);
        if (form) {
          handleCommentPlugin(form);
        }
      }
    }
    setNameForComment();
  }
  // AutoTicketLink
  function autoTicketLink() {
    var headReText = '([\\s\\b]|^)';
    var tailReText = '\\b';
    var ignoreTags = ['A', 'INPUT', 'TEXTAREA', 'BUTTON',
      'SCRIPT', 'FRAME', 'IFRAME'];
    var ticketSiteList = [];
    function regexEscape(key) {
      return key.replace(/[-.]/g, function (m) {
        return '\\' + m;
      });
    }
    function setupSites(siteList) {
      for (var i = 0, length = siteList.length; i < length; i++) {
        var site = siteList[i];
        var reText = '';
        switch (site.type) {
          case 'jira':
            reText = '(' + regexEscape(site.key) + '):([A-Z][A-Z0-9_]+-\\d+)';
            break;
          case 'redmine':
            reText = '(' + regexEscape(site.key) + '):(\\d+)';
            break;
          case 'git':
            reText = '(' + regexEscape(site.key) + '):([0-9a-f]{7,40})';
            break;
          default:
            continue;
        }
        site.reText = reText;
        site.re = new RegExp(headReText + reText + tailReText);
      }
    }
    function getSiteListFromBody() {
      var defRoot = document.querySelector('#pukiwiki-site-properties .ticketlink-def');
      if (defRoot && defRoot.value) {
        var list = JSON.parse(defRoot.value);
        setupSites(list);
        return list;
      }
      return [];
    }
    function getSiteList() {
      return ticketSiteList;
    }
    function ticketToLink(keyText) {
      var siteList = getSiteList();
      for (var i = 0; i < siteList.length; i++) {
        var site = siteList[i];
        var m = keyText.match(site.re);
        if (m) {
          var title = site.title;
          var ticketKey = m[3];
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
    function getRegex(list) {
      var reText = '';
      for (var i = 0, length = list.length; i < length; i++) {
        if (reText.length > 0) {
          reText += '|';
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
      var f;
      var m;
      var text = element.nodeValue;
      while (m = text.match(re)) { // eslint-disable-line no-cond-assign
        // m[1]: head, m[2]: keyText
        if (!f) {
          f = document.createDocumentFragment();
        }
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
        if (text.length > 0) {
          f.appendChild(document.createTextNode(text));
        }
        element.parentNode.replaceChild(f, element);
      }
    }
    function walkElement(element) {
      var e = element.firstChild;
      while (e) {
        if (e.nodeType === 3 && e.nodeValue &&
            e.nodeValue.length > 5 && /\S/.test(e.nodeValue)) {
          var next = e.nextSibling;
          makeTicketLink(e);
          e = next;
        } else {
          if (e.nodeType === 1 && ignoreTags.indexOf(e.tagName) === -1) {
            walkElement(e);
          }
          e = e.nextSibling;
        }
      }
    }
    if (!Array.prototype.indexOf || !document.createDocumentFragment) {
      return;
    }
    ticketSiteList = getSiteListFromBody();
    var target = document.getElementById('body');
    walkElement(target);
  }
  function confirmEditFormLeaving() {
    function trim(s) {
      if (typeof s !== 'string') {
        return s;
      }
      return s.replace(/^\s+|\s+$/g, '');
    }
    if (!document.querySelector) return;
    var canceled = false;
    var pluginNameE = document.querySelector('#pukiwiki-site-properties .plugin-name');
    if (!pluginNameE) return;
    var originalText = null;
    if (pluginNameE.value !== 'edit') return;
    var editForm = document.querySelector('.edit_form form._plugin_edit_edit_form');
    if (!editForm) return;
    var cancelMsgE = editForm.querySelector('#_msg_edit_cancel_confirm');
    var unloadBeforeMsgE = editForm.querySelector('#_msg_edit_unloadbefore_message');
    var textArea = editForm.querySelector('textarea[name="msg"]');
    if (!textArea) return;
    originalText = textArea.value;
    var cancelForm = document.querySelector('.edit_form form._plugin_edit_cancel');
    var submited = false;
    editForm.addEventListener('submit', function() {
      canceled = false;
      submited = true;
    });
    cancelForm.addEventListener('submit', function(e) {
      submited = false;
      canceled = false;
      if (trim(textArea.value) === trim(originalText)) {
        canceled = true;
        return false;
      }
      var message = 'The text you have entered will be discarded. Is it OK?';
      if (cancelMsgE && cancelMsgE.value) {
        message = cancelMsgE.value;
      }
      if (window.confirm(message)) { // eslint-disable-line no-alert
        // Execute "Cancel"
        canceled = true;
        return true;
      }
      e.preventDefault();
      return false;
    });
    window.addEventListener('beforeunload', function(e) {
      if (canceled) return;
      if (submited) return;
      if (trim(textArea.value) === trim(originalText)) return;
      var message = 'Data you have entered will not be saved.';
      if (unloadBeforeMsgE && unloadBeforeMsgE.value) {
        message = unloadBeforeMsgE.value;
      }
      e.returnValue = message;
    }, false);
  }
  function showPagePassage() {
    /**
     * @param {Date} now
     * @param {string} dateText
     */
    function getPassage(dateText, now) {
      if (!dateText) {
        return '';
      }
      var units = [{u: 'm', max: 60}, {u: 'h', max: 24}, {u: 'd', max: 1}];
      var d = new Date();
      d.setTime(Date.parse(dateText));
      var t = (now.getTime() - d.getTime()) / (1000 * 60); // minutes
      var unit = units[0].u; var card = units[0].max;
      for (var i = 0; i < units.length; i++) {
        unit = units[i].u; card = units[i].max;
        if (t < card) break;
        t = t / card;
      }
      return '(' + Math.floor(t) + unit + ')';
    }
    var now = new Date();
    var elements = document.getElementsByClassName('page_passage');
    forEach(elements, function(e) {
      var dt = e.getAttribute('data-mtime');
      if (dt) {
        var d = new Date(dt);
        e.textContent = ' ' + getPassage(d, now);
      }
    });
    var links = document.getElementsByClassName('link_page_passage');
    forEach(links, function(e) {
      var dt = e.getAttribute('data-mtime');
      if (dt) {
        var d = new Date(dt);
        if (e.title) {
          e.title = e.title + ' ' + getPassage(d, now);
        } else {
          e.title = e.textContent + ' ' + getPassage(d, now);
        }
      }
    });
  }
  setYourName();
  autoTicketLink();
  confirmEditFormLeaving();
  showPagePassage();
});
