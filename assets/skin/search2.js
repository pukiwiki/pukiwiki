// PukiWiki - Yet another WikiWikiWeb clone.
// search2.js
// Copyright
//   2017-2020 PukiWiki Development Team
// License: GPL v2 or (at your option) any later version
//
// PukiWiki search2 pluign - JavaScript client script
/* eslint-env browser */
// eslint-disable-next-line no-unused-expressions
window.addEventListener && window.addEventListener('DOMContentLoaded', function () {
  'use strict'
  function enableSearch2 () {
    var aroundLines = 2
    var maxResultLines = 20
    var defaultSearchWaitMilliseconds = 100
    var defaultMaxResults = 1000
    var kanaMap = null
    var searchProps = {}
    /**
     * Escape HTML special charactors
     *
     * @param {string} s
     */
    function escapeHTML (s) {
      if (typeof s !== 'string') {
        return '' + s
      }
      return s.replace(/[&"<>]/g, function (m) {
        return {
          '&': '&amp;',
          '"': '&quot;',
          '<': '&lt;',
          '>': '&gt;'
        }[m]
      })
    }
    /**
     * @param {string} idText
     * @param {number} defaultValue
     * @type number
     */
    function getIntById (idText, defaultValue) {
      var value = defaultValue
      try {
        var element = document.getElementById(idText)
        if (element) {
          value = parseInt(element.value, 10)
          if (isNaN(value)) { // eslint-disable-line no-restricted-globals
            value = defaultValue
          }
        }
      } catch (e) {
        value = defaultValue
      }
      return value
    }
    /**
     * @param {string} idText
     * @param {string} defaultValue
     * @type string
     */
    function getTextById (idText, defaultValue) {
      var value = defaultValue
      try {
        var element = document.getElementById(idText)
        if (element.value) {
          value = element.value
        }
      } catch (e) {
        value = defaultValue
      }
      return value
    }
    function prepareSearchProps () {
      var p = {}
      p.errorMsg = getTextById('_plugin_search2_msg_error',
        'An error occurred while processing.')
      p.searchingMsg = getTextById('_plugin_search2_msg_searching',
        'Searching...')
      p.showingResultMsg = getTextById('_plugin_search2_msg_showing_result',
        'Showing search results')
      p.prevOffset = getTextById('_plugin_search2_prev_offset', '')
      var baseUrlDefault = document.location.pathname + document.location.search
      baseUrlDefault = baseUrlDefault.replace(/&offset=\d+/, '')
      p.baseUrl = getTextById('_plugin_search2_base_url', baseUrlDefault)
      p.msgPrevResultsTemplate = getTextById('_plugin_search2_msg_prev_results', 'Previous $1 pages')
      p.msgMoreResultsTemplate = getTextById('_plugin_search2_msg_more_results', 'Next $1 pages')
      p.user = getTextById('_plugin_search2_auth_user', '')
      p.showingResultMsg = getTextById('_plugin_search2_msg_showing_result', 'Showing search results')
      p.notFoundMessageTemplate = getTextById('_plugin_search2_msg_result_notfound',
        'No page which contains $1 has been found.')
      p.foundMessageTemplate = getTextById('_plugin_search2_msg_result_found',
        'In the page <strong>$2</strong>, <strong>$3</strong> pages that contain all the terms $1 were found.')
      p.maxResults = getIntById('_plugin_search2_max_results', defaultMaxResults)
      p.searchInterval = getIntById('_plugin_search2_search_wait_milliseconds', defaultSearchWaitMilliseconds)
      p.offset = getIntById('_plugin_search2_offset', 0)
      searchProps = p
    }
    function getSiteProps () {
      var empty = {}
      var propsE = document.querySelector('#pukiwiki-site-properties .site-props')
      if (!propsE) return empty
      var props = JSON.parse(propsE.value)
      return props || empty
    }
    /**
     * @param {NodeList} nodeList
     * @param {function(Node, number): void} func
     */
    function forEach (nodeList, func) {
      if (nodeList.forEach) {
        nodeList.forEach(func)
      } else {
        for (var i = 0, n = nodeList.length; i < n; i++) {
          func(nodeList[i], i)
        }
      }
    }
    /**
     * @param {string} text
     * @param {RegExp} searchRegex
     */
    function findAndDecorateText (text, searchRegex) {
      var isReplaced = false
      var lastIndex = 0
      var m
      var decorated = ''
      if (!searchRegex) return null
      searchRegex.lastIndex = 0
      while ((m = searchRegex.exec(text)) !== null) {
        if (m[0] === '') {
          // Fail-safe
          console.log('Invalid searchRegex ' + searchRegex)
          return null
        }
        isReplaced = true
        var pre = text.substring(lastIndex, m.index)
        decorated += escapeHTML(pre)
        for (var i = 1; i < m.length; i++) {
          if (m[i]) {
            decorated += '<strong class="word' + (i - 1) + '">' + escapeHTML(m[i]) + '</strong>'
          }
        }
        lastIndex = searchRegex.lastIndex
      }
      if (isReplaced) {
        decorated += escapeHTML(text.substr(lastIndex))
        return decorated
      }
      return null
    }
    /**
     * @param {Object} session
     * @param {string} searchText
     * @param {RegExp} searchRegex
     * @param {boolean} nowSearching
     */
    function getSearchResultMessage (session, searchText, searchRegex, nowSearching) {
      var searchTextDecorated = findAndDecorateText(searchText, searchRegex)
      if (searchTextDecorated === null) searchTextDecorated = escapeHTML(searchText)
      var messageTemplate = searchProps.foundMessageTemplate
      if (!nowSearching && session.hitPageCount === 0) {
        messageTemplate = searchProps.notFoundMessageTemplate
      }
      var msg = messageTemplate.replace(/\$1|\$2|\$3/g, function (m) {
        return {
          $1: searchTextDecorated,
          $2: session.hitPageCount,
          $3: session.readPageCount
        }[m]
      })
      return msg
    }
    /**
     * @param {Object} session
     */
    function getSearchProgress (session) {
      var progress = '(read:' + session.readPageCount + ', scan:' +
        session.scanPageCount + ', all:' + session.pageCount
      if (session.offset) {
        progress += ', offset: ' + session.offset
      }
      progress += ')'
      return progress
    }
    /**
     * @param {Object} session
     * @param {number} maxResults
     */
    function getOffsetLinks (session, maxResults) {
      var baseUrl = searchProps.baseUrl
      var links = []
      if ('prevOffset' in session) {
        var prevResultUrl = baseUrl
        if (session.prevOffset > 0) {
          prevResultUrl += '&offset=' + session.prevOffset
        }
        var msgPrev = searchProps.msgPrevResultsTemplate.replace(/\$1/, maxResults)
        var prevResultHtml = '<a href="' + prevResultUrl + '">' + msgPrev + '</a>'
        links.push(prevResultHtml)
      }
      if ('nextOffset' in session) {
        var nextResultUrl = baseUrl + '&offset=' + session.nextOffset +
          '&prev_offset=' + session.offset
        var msgMore = searchProps.msgMoreResultsTemplate.replace(/\$1/, maxResults)
        var moreResultHtml = '<a href="' + nextResultUrl + '">' + msgMore + '</a>'
        links.push(moreResultHtml)
      }
      if (links.length > 0) {
        return links.join(' ')
      }
      return ''
    }
    function prepareKanaMap () {
      if (kanaMap !== null) return
      if (!String.prototype.normalize) {
        kanaMap = {}
        return
      }
      var dakuten = '\uFF9E'
      var maru = '\uFF9F'
      var map = {}
      for (var c = 0xFF61; c <= 0xFF9F; c++) {
        var han = String.fromCharCode(c)
        var zen = han.normalize('NFKC')
        map[zen] = han
        var hanDaku = han + dakuten
        var zenDaku = hanDaku.normalize('NFKC')
        if (zenDaku.length === 1) { // +Handaku-ten OK
          map[zenDaku] = hanDaku
        }
        var hanMaru = han + maru
        var zenMaru = hanMaru.normalize('NFKC')
        if (zenMaru.length === 1) { // +Maru OK
          map[zenMaru] = hanMaru
        }
      }
      kanaMap = map
    }
    /**
     * Hankaku to Zenkaku.
     *
     * @param {String} hankakuChar
     * @returns {String}
     */
    function toZenkaku (hankakuChar) {
      if (hankakuChar.length !== 1) {
        return hankakuChar
      }
      var zenkakuChar = String.fromCharCode(hankakuChar.charCodeAt(0) + 0xfee0)
      if (!String.prototype.normalize) {
        return hankakuChar
      }
      if (zenkakuChar.normalize('NFKC') === hankakuChar) {
        return zenkakuChar
      }
      return hankakuChar
    }
    /**
     * @param {searchText} searchText
     * @type RegExp
     */
    function textToRegex (searchText) {
      if (!searchText) return null
      //            1: Alphabet   2:Katakana        3:Hiragana        4:Wa kigo                                5:Other symbols
      var regRep = /([a-zA-Z0-9])|([\u30a1-\u30f6])|([\u3041-\u3096])|([\u30fb\u30fc\u300c\u300d\u3001\u3002])|([\u0021-\u007e])/ig
      var replacementFunc = function (m, m1, m2, m3, m4, m5) {
        if (m1) {
          // [a-zA-Z0-9]
          return '[' + m1 + toZenkaku(m1) + ']'
        } else if (m2) {
          // Katakana
          var r = '(?:' + String.fromCharCode(m2.charCodeAt(0) - 0x60) +
            '|' + m2
          if (kanaMap[m2]) {
            r += '|' + kanaMap[m2]
          }
          r += ')'
          return r
        } else if (m3) {
          // Hiragana
          var katakana = String.fromCharCode(m3.charCodeAt(0) + 0x60)
          var r2 = '(?:' + m3 + '|' + katakana
          if (kanaMap[katakana]) {
            r2 += '|' + kanaMap[katakana]
          }
          r2 += ')'
          return r2
        } else if (m4) {
          // Wa kigo
          if (kanaMap[m4]) {
            return '[' + m4 + kanaMap[m4] + ']'
          }
          return m4
        } else if (m5) {
          // Other symbols
          return '[' + '\\' + m5 + toZenkaku(m5) + ']'
        }
        return m
      }
      var s1 = searchText.replace(/^\s+|\s+$/g, '')
      if (!s1) return null
      var sp = s1.split(/\s+/)
      var rText = ''
      prepareKanaMap()
      for (var i = 0; i < sp.length; i++) {
        if (rText !== '') {
          rText += '|'
        }
        var s = sp[i]
        if (s.normalize) {
          s = s.normalize('NFKC')
        }
        var s2 = s.replace(regRep, replacementFunc)
        rText += '(' + s2 + ')'
      }
      return new RegExp(rText, 'ig')
    }
    /**
     * @param {string} statusText
     */
    function setSearchStatus (statusText, statusText2) {
      var statusList = document.querySelectorAll('._plugin_search2_search_status')
      forEach(statusList, function (statusObj) {
        var textObj1 = statusObj.querySelector('._plugin_search2_search_status_text1')
        var textObj2 = statusObj.querySelector('._plugin_search2_search_status_text2')
        if (textObj1) {
          var prevText = textObj1.getAttribute('data-text')
          if (prevText !== statusText) {
            textObj1.setAttribute('data-text', statusText)
            if (statusText.substr(statusText.length - 3) === '...') {
              var firstHalf = statusText.substr(0, statusText.length - 3)
              textObj1.textContent = firstHalf
              var span = document.createElement('span')
              span.innerHTML = '<span class="plugin-search2-progress plugin-search2-progress1">.</span>' +
                '<span class="plugin-search2-progress plugin-search2-progress2">.</span>' +
                '<span class="plugin-search2-progress plugin-search2-progress3">.</span>'
              textObj1.appendChild(span)
            } else {
              textObj1.textContent = statusText
            }
          }
        }
        if (textObj2) {
          if (statusText2) {
            textObj2.textContent = ' ' + statusText2
          } else {
            textObj2.textContent = ''
          }
        }
      })
    }
    /**
     * @param {string} msgHTML
     */
    function setSearchMessage (msgHTML) {
      var objList = document.querySelectorAll('._plugin_search2_message')
      forEach(objList, function (obj) {
        obj.innerHTML = msgHTML
      })
    }
    function showSecondSearchForm () {
      // Show second search form
      var div = document.querySelector('._plugin_search2_second_form')
      if (div) {
        div.style.display = 'block'
      }
    }
    /**
     * @param {Element} form
     * @type string
     */
    function getSearchBase (form) {
      var f = form || document.querySelector('._plugin_search2_form')
      var base = ''
      forEach(f.querySelectorAll('input[name="base"]'), function (radio) {
        if (radio.checked) base = radio.value
      })
      return base
    }
    /**
     * Decorate found block (for pre innerHTML)
     *
     * @param {Object} block
     * @param {RegExp} searchRegex
     */
    function decorateFoundBlock (block, searchRegex) {
      var lines = []
      for (var j = 0; j < block.lines.length; j++) {
        var line = block.lines[j]
        var decorated = findAndDecorateText(line, searchRegex)
        if (decorated === null) {
          lines.push('' + (block.startIndex + j + 1) + ':\t' + escapeHTML(line))
        } else {
          lines.push('' + (block.startIndex + j + 1) + ':\t' + decorated)
        }
      }
      if (block.beyondLimit) {
        lines.push('...')
      }
      return lines.join('\n')
    }
    /**
     * @param {string} body
     * @param {RegExp} searchRegex
     */
    function getSummaryInfo (body, searchRegex) {
      var lines = body.split('\n')
      var foundLines = []
      var isInAuthorHeader = true
      var lastFoundLineIndex = -1 - aroundLines
      var lastAddedLineIndex = lastFoundLineIndex
      var blocks = []
      var lineCount = 0
      var currentBlock = null
      for (var index = 0, length = lines.length; index < length; index++) {
        var line = lines[index]
        if (isInAuthorHeader) {
          // '#author line is not search target'
          if (line.match(/^#author\(/)) {
            // Remove this line from search target
            continue
          } else if (line.match(/^#freeze(\W|$)/)) {
            // Still in header
          } else {
            // Already in body
            isInAuthorHeader = false
          }
        }
        var match = line.match(searchRegex)
        if (!match) {
          if (index < lastFoundLineIndex + aroundLines + 1) {
            foundLines.push(lines[index])
            lineCount++
            lastAddedLineIndex = index
          }
        } else {
          var startIndex = Math.max(Math.max(lastAddedLineIndex + 1, index - aroundLines), 0)
          if (lastAddedLineIndex + 1 < startIndex) {
            // Newly found!
            var block = {
              startIndex: startIndex,
              foundLineIndex: index,
              lines: []
            }
            currentBlock = block
            foundLines = block.lines
            blocks.push(block)
          }
          if (lineCount >= maxResultLines) {
            currentBlock.beyondLimit = true
            return blocks
          }
          for (var i = startIndex; i < index; i++) {
            foundLines.push(lines[i])
            lineCount++
          }
          foundLines.push(line)
          lineCount++
          lastFoundLineIndex = lastAddedLineIndex = index
        }
      }
      return blocks
    }
    /**
     * @param {Date} now
     * @param {string} dateText
     */
    function getPassage (now, dateText) {
      if (!dateText) {
        return ''
      }
      var units = [{ u: 'm', max: 60 }, { u: 'h', max: 24 }, { u: 'd', max: 1 }]
      var d = new Date()
      d.setTime(Date.parse(dateText))
      var t = (now.getTime() - d.getTime()) / (1000 * 60) // minutes
      var unit = units[0].u; var card = units[0].max
      for (var i = 0; i < units.length; i++) {
        unit = units[i].u; card = units[i].max
        if (t < card) break
        t = t / card
      }
      return '(' + Math.floor(t) + unit + ')'
    }
    /**
     * @param {string} searchText
     */
    function removeSearchOperators (searchText) {
      var sp = searchText.split(/\s+/)
      if (sp.length <= 1) {
        return searchText
      }
      for (var i = sp.length - 2; i >= 1; i--) {
        if (sp[i] === 'OR') {
          sp.splice(i, 1)
        }
      }
      return sp.join(' ')
    }
    /**
     * @param {string} pathname
     */
    function getSearchCacheKeyBase (pathname) {
      return 'path.' + pathname + '.search2.'
    }
    /**
     * @param {string} pathname
     */
    function getSearchCacheKeyDateBase (pathname) {
      var now = new Date()
      var dateKey = now.getFullYear() + '_0' + (now.getMonth() + 1) + '_0' + now.getDate()
      dateKey = dateKey.replace(/_\d?(\d\d)/g, '$1')
      return getSearchCacheKeyBase(pathname) + dateKey + '.'
    }
    /**
     * @param {string} pathname
     * @param {string} searchText
     * @param {number} offset
     */
    function getSearchCacheKey (pathname, searchText, offset) {
      return getSearchCacheKeyDateBase(pathname) + 'offset=' + offset +
        '.' + searchText
    }
    /**
     * @param {string} pathname
     * @param {string} searchText
     */
    function clearSingleCache (pathname, searchText) {
      if (!window.localStorage) return
      var removeTargets = []
      var keyBase = getSearchCacheKeyDateBase(pathname)
      for (var i = 0, n = localStorage.length; i < n; i++) {
        var key = localStorage.key(i)
        if (key.substr(0, keyBase.length) === keyBase) {
          // Search result Cache
          var subKey = key.substr(keyBase.length)
          var m = subKey.match(/^offset=\d+\.(.+)$/)
          if (m && m[1] === searchText) {
            removeTargets.push(key)
          }
        }
      }
      removeTargets.forEach(function (target) {
        localStorage.removeItem(target)
      })
    }
    /**
     * @param {string} body
     */
    function getBodySummary (body) {
      var lines = body.split('\n')
      var isInAuthorHeader = true
      var summary = []
      for (var index = 0, length = lines.length; index < length; index++) {
        var line = lines[index]
        if (isInAuthorHeader) {
          // '#author line is not search target'
          if (line.match(/^#author\(/)) {
            // Remove this line from search target
            continue
          } else if (line.match(/^#freeze(\W|$)/)) {
            continue
            // Still in header
          } else {
            // Already in body
            isInAuthorHeader = false
          }
        }
        line = line.replace(/^\s+|\s+$/g, '')
        if (line.length === 0) continue // Empty line
        if (line.match(/^#\w+/)) continue // Block-type plugin
        if (line.match(/^\/\//)) continue // Comment
        if (line.substr(0, 1) === '*') {
          line = line.replace(/\s*\[#\w+\]$/, '') // Remove anchor
        }
        summary.push(line)
        if (summary.length >= 10) {
          continue
        }
      }
      return summary.join(' ').substring(0, 150)
    }
    /**
     * @param {string} q searchText
     */
    function encodeSearchText (q) {
      var sp = q.split(/\s+/)
      for (var i = 0; i < sp.length; i++) {
        sp[i] = encodeURIComponent(sp[i])
      }
      return sp.join('+')
    }
    /**
     * @param {string} q searchText
     */
    function encodeSearchTextForHash (q) {
      var sp = q.split(/\s+/)
      return sp.join('+')
    }
    function getSearchTextInLocationHash () {
      var hash = document.location.hash
      if (!hash) return ''
      var q = ''
      if (hash.substr(0, 3) === '#q=') {
        q = hash.substr(3).replace(/\+/g, ' ')
      } else {
        return ''
      }
      var decodedQ = decodeURIComponent(q)
      if (q !== decodedQ) {
        q = decodedQ + ' OR ' + q
      }
      return q
    }
    function colorSearchTextInBody () {
      var searchText = getSearchTextInLocationHash()
      if (!searchText) return
      var searchRegex = textToRegex(removeSearchOperators(searchText))
      if (!searchRegex) return
      var ignoreTags = ['INPUT', 'TEXTAREA', 'BUTTON',
        'SCRIPT', 'FRAME', 'IFRAME']
      /**
       * @param {Element} element
       */
      function colorSearchText (element) {
        var decorated = findAndDecorateText(element.nodeValue, searchRegex)
        if (decorated) {
          var span = document.createElement('span')
          span.innerHTML = decorated
          element.parentNode.replaceChild(span, element)
        }
      }
      /**
       * @param {Element} element
       */
      function walkElement (element) {
        var e = element.firstChild
        while (e) {
          if (e.nodeType === 3 && e.nodeValue &&
              e.nodeValue.length >= 2 && /\S/.test(e.nodeValue)) {
            var next = e.nextSibling
            colorSearchText(e, searchRegex)
            e = next
          } else {
            if (e.nodeType === 1 && ignoreTags.indexOf(e.tagName) === -1) {
              walkElement(e)
            }
            e = e.nextSibling
          }
        }
      }
      var target = document.getElementById('body')
      walkElement(target)
    }
    /**
     * @param {Array<Object>} newResults
     * @param {Element} ul
     */
    function removePastResults (newResults, ul) {
      var removedCount = 0
      var nodes = ul.childNodes
      for (var i = nodes.length - 1; i >= 0; i--) {
        var node = nodes[i]
        if (node.tagName !== 'LI' && node.tagName !== 'DIV') continue
        var nodePagename = node.getAttribute('data-pagename')
        var isRemoveTarget = false
        for (var j = 0, n = newResults.length; j < n; j++) {
          var r = newResults[j]
          if (r.name === nodePagename) {
            isRemoveTarget = true
            break
          }
        }
        if (isRemoveTarget) {
          if (node.tagName === 'LI') {
            removedCount++
          }
          ul.removeChild(node)
        }
      }
      return removedCount
    }
    /**
     * @param {Array<Object>} results
     * @param {string} searchText
     * @param {RegExp} searchRegex
     * @param {Element} parentUlElement
     * @param {boolean} insertTop
     */
    function addSearchResult (results, searchText, searchRegex, parentUlElement, insertTop) {
      var props = getSiteProps()
      var now = new Date()
      var parentFragment = document.createDocumentFragment()
      results.forEach(function (val) {
        var li = document.createElement('li')
        var hash = '#q=' + encodeSearchTextForHash(searchText)
        var href = val.url + hash
        var decoratedName = findAndDecorateText(val.name, searchRegex)
        if (!decoratedName) {
          decoratedName = escapeHTML(val.name)
        }
        var updatedAt = val.updatedAt
        var passageHtml = ''
        if (props.show_passage) {
          passageHtml = ' ' + getPassage(now, updatedAt)
        }
        var liHtml = '<a href="' + escapeHTML(href) + '">' +
          decoratedName + '</a>' + passageHtml
        li.innerHTML = liHtml
        li.setAttribute('data-pagename', val.name)
        // Page detail div
        var div = document.createElement('div')
        div.classList.add('search-result-detail')
        var head = document.createElement('div')
        head.classList.add('search-result-page-summary')
        head.innerHTML = escapeHTML(val.bodySummary)
        div.appendChild(head)
        var summaryInfo = val.hitSummary
        for (var i = 0; i < summaryInfo.length; i++) {
          var pre = document.createElement('pre')
          pre.innerHTML = decorateFoundBlock(summaryInfo[i], searchRegex)
          div.appendChild(pre)
        }
        div.setAttribute('data-pagename', val.name)
        // Add li to ul (parentUlElement)
        li.appendChild(div)
        parentFragment.appendChild(li)
      })
      if (insertTop && parentUlElement.firstChild) {
        parentUlElement.insertBefore(parentFragment, parentUlElement.firstChild)
      } else {
        parentUlElement.appendChild(parentFragment)
      }
    }
    function removeCachedResultsBase (keepTodayCache) {
      var props = getSiteProps()
      if (!props || !props.base_uri_pathname) return
      var keyPrefix = getSearchCacheKeyDateBase(props.base_uri_pathname)
      var keyBase = getSearchCacheKeyBase(props.base_uri_pathname)
      var removeTargets = []
      for (var i = 0, n = localStorage.length; i < n; i++) {
        var key = localStorage.key(i)
        if (key.substr(0, keyBase.length) === keyBase) {
          // Search result Cache
          if (keepTodayCache) {
            if (key.substr(0, keyPrefix.length) !== keyPrefix) {
              removeTargets.push(key)
            }
          } else {
            removeTargets.push(key)
          }
        }
      }
      removeTargets.forEach(function (target) {
        localStorage.removeItem(target)
      })
    }
    function removeCachedResults () {
      removeCachedResultsBase(true)
    }
    function removeAllCachedResults () {
      removeCachedResultsBase(false)
    }
    /**
     * @param {Object} obj
     * @param {Object} session
     * @param {string} searchText
     * @param {number} prevTimestamp
     */
    function showResult (obj, session, searchText, prevTimestamp) {
      var props = getSiteProps()
      var searchRegex = textToRegex(removeSearchOperators(searchText))
      var ul = document.querySelector('#_plugin_search2_result-list')
      if (!ul) return
      if (obj.start_index === 0 && !prevTimestamp) {
        ul.innerHTML = ''
      }
      var searchDone = obj.search_done
      if (!session.scanPageCount) session.scanPageCount = 0
      if (!session.readPageCount) session.readPageCount = 0
      if (!session.hitPageCount) session.hitPageCount = 0
      var prevHitPageCount = session.hitPageCount
      session.hitPageCount += obj.results.length
      if (!prevTimestamp) {
        session.scanPageCount += obj.scan_page_count
        session.readPageCount += obj.read_page_count
        session.pageCount = obj.page_count
      }
      session.searchStartTime = obj.search_start_time
      session.authUser = obj.auth_user
      if (prevHitPageCount === 0 && session.hitPageCount > 0) {
        showSecondSearchForm()
      }
      var results = obj.results
      var cachedResults = []
      results.forEach(function (val) {
        var cache = {}
        cache.name = val.name
        cache.url = val.url
        cache.updatedAt = val.updated_at
        cache.updatedTime = val.updated_time
        cache.bodySummary = getBodySummary(val.body)
        cache.hitSummary = getSummaryInfo(val.body, searchRegex)
        cachedResults.push(cache)
      })
      if (prevTimestamp) {
        var removedCount = removePastResults(cachedResults, ul)
        session.hitPageCount -= removedCount
      }
      var msg = getSearchResultMessage(session, searchText, searchRegex, !searchDone)
      setSearchMessage(msg)
      if (prevTimestamp) {
        setSearchStatus(searchProps.searchingMsg)
      } else {
        setSearchStatus(searchProps.searchingMsg,
          getSearchProgress(session))
      }
      if (searchDone) {
        var singlePageResult = session.offset === 0 && !session.nextOffset
        var progress = getSearchProgress(session)
        setTimeout(function () {
          if (singlePageResult) {
            setSearchStatus('')
          } else {
            setSearchStatus(searchProps.showingResultMsg, progress)
          }
        }, 2000)
      }
      if (session.results) {
        if (prevTimestamp) {
          var newResult = [].concat(cachedResults)
          Array.prototype.push.apply(newResult, session.results)
          session.results = newResult
        } else {
          Array.prototype.push.apply(session.results, cachedResults)
        }
      } else {
        session.results = cachedResults
      }
      addSearchResult(cachedResults, searchText, searchRegex, ul, prevTimestamp)
      var maxResults = searchProps.maxResults
      if (searchDone) {
        session.searchText = searchText
        var prevOffset = searchProps.prevOffset
        if (prevOffset) {
          session.prevOffset = parseInt(prevOffset, 10)
        }
        var json = JSON.stringify(session)
        var cacheKey = getSearchCacheKey(props.base_uri_pathname, searchText, session.offset)
        if (window.localStorage) {
          try {
            localStorage[cacheKey] = json
          } catch (e) {
            // QuotaExceededError "exceeded the quota."
            console.log(e)
            removeAllCachedResults()
          }
        }
        if ('prevOffset' in session || 'nextOffset' in session) {
          setSearchMessage(msg + ' ' + getOffsetLinks(session, maxResults))
        }
      }
      if (!searchDone && obj.next_start_index) {
        if (session.results.length >= maxResults) {
          // Save results
          session.nextOffset = obj.next_start_index
          var prevOffset2 = searchProps.prevOffset
          if (prevOffset2) {
            session.prevOffset = parseInt(prevOffset2, 10)
          }
          var key = getSearchCacheKey(props.base_uri_pathname, searchText, session.offset)
          localStorage[key] = JSON.stringify(session)
          // Stop API calling
          setSearchMessage(msg + ' ' + getOffsetLinks(session, maxResults))
          setSearchStatus(searchProps.showingResultMsg,
            getSearchProgress(session))
        } else {
          setTimeout(function () {
            doSearch(searchText, // eslint-disable-line no-use-before-define
              session, obj.next_start_index,
              obj.search_start_time)
          }, searchProps.searchInterval)
        }
      }
    }
    /**
     * @param {string} searchText
     * @param {string} base
     * @param {number} offset
     */
    function showCachedResult (searchText, base, offset) {
      var props = getSiteProps()
      var searchRegex = textToRegex(removeSearchOperators(searchText))
      var ul = document.querySelector('#_plugin_search2_result-list')
      if (!ul) return null
      var searchCacheKey = getSearchCacheKey(props.base_uri_pathname, searchText, offset)
      var cache1 = localStorage[searchCacheKey]
      if (!cache1) {
        return null
      }
      var session = JSON.parse(cache1)
      if (!session) return null
      if (base !== session.base) {
        return null
      }
      var user = searchProps.user
      if (user !== session.authUser) {
        return null
      }
      if (session.hitPageCount > 0) {
        showSecondSearchForm()
      }
      var msg = getSearchResultMessage(session, searchText, searchRegex, false)
      setSearchMessage(msg)
      addSearchResult(session.results, searchText, searchRegex, ul)
      var maxResults = searchProps.maxResults
      if ('prevOffset' in session || 'nextOffset' in session) {
        var moreResultHtml = getOffsetLinks(session, maxResults)
        setSearchMessage(msg + ' ' + moreResultHtml)
        var progress = getSearchProgress(session)
        setSearchStatus(searchProps.showingResultMsg, progress)
      } else {
        setSearchStatus('')
      }
      return session
    }
    /**
     * @param {string} searchText
     * @param {object} session
     * @param {number} startIndex
     * @param {number} searchStartTime
     * @param {number} prevTimestamp
     */
    function doSearch (searchText, session, startIndex, searchStartTime, prevTimestamp) {
      var props = getSiteProps()
      var baseUrl = './'
      if (props.base_uri_pathname) {
        baseUrl = props.base_uri_pathname
      }
      var url = baseUrl + '?cmd=search2&action=query'
      url += '&encode_hint=' + encodeURIComponent('\u3077')
      if (searchText) {
        url += '&q=' + encodeURIComponent(searchText)
      }
      if (session.base) {
        url += '&base=' + encodeURIComponent(session.base)
      }
      if (prevTimestamp) {
        url += '&modified_since=' + prevTimestamp
      } else {
        url += '&start=' + startIndex
        if (searchStartTime) {
          url += '&search_start_time=' + encodeURIComponent(searchStartTime)
        }
        if (!('offset' in session)) {
          session.offset = startIndex
        }
      }
      fetch(url, { credentials: 'same-origin' }
      ).then(function (response) {
        if (response.ok) {
          return response.json()
        }
        throw new Error(response.status + ': ' +
          response.statusText + ' on ' + url)
      }).then(function (obj) {
        showResult(obj, session, searchText, prevTimestamp)
      })['catch'](function (err) { // eslint-disable-line dot-notation
        if (window.console && console.log) {
          console.log(err)
          console.log('Error! Please check JavaScript console\n' + JSON.stringify(err) + '|' + err)
        }
        setSearchStatus(searchProps.errorMsg)
      })
    }
    function hookSearch2 () {
      var form = document.querySelector('form')
      if (form && form.q) {
        var q = form.q
        if (q.value === '') {
          q.focus()
        }
      }
    }
    function removeEncodeHint () {
      // Remove 'encode_hint' if site charset is UTF-8
      var props = getSiteProps()
      if (!props.is_utf8) return
      var forms = document.querySelectorAll('form')
      forEach(forms, function (form) {
        if (form.cmd && form.cmd.value === 'search2') {
          if (form.encode_hint && (typeof form.encode_hint.removeAttribute === 'function')) {
            form.encode_hint.removeAttribute('name')
          }
        }
      })
    }
    function kickFirstSearch () {
      var form = document.querySelector('._plugin_search2_form')
      var searchText = form && form.q
      if (!searchText) return
      if (searchText && searchText.value) {
        var offset = searchProps.offset
        var base = getSearchBase(form)
        var prevSession = showCachedResult(searchText.value, base, offset)
        if (prevSession) {
          // Display Cache results, then search only modified pages
          if (!('offset' in prevSession) || prevSession.offset === 0) {
            doSearch(searchText.value, prevSession, offset, null,
              prevSession.searchStartTime)
          } else {
            // Show search results
          }
        } else {
          doSearch(searchText.value, { base: base, offset: offset }, offset, null)
        }
        removeCachedResults()
      }
    }
    function replaceSearchWithSearch2 () {
      forEach(document.querySelectorAll('form'), function (f) {
        function onAndRadioClick () {
          var sp = removeSearchOperators(f.word.value).split(/\s+/)
          var newText = sp.join(' ')
          if (f.word.value !== newText) {
            f.word.value = newText
          }
        }
        function onOrRadioClick () {
          var sp = removeSearchOperators(f.word.value).split(/\s+/)
          var newText = sp.join(' OR ')
          if (f.word.value !== newText) {
            f.word.value = newText
          }
        }
        if (f.action.match(/cmd=search$/)) {
          f.addEventListener('submit', function (e) {
            var q = e.target.word.value
            var base = ''
            forEach(f.querySelectorAll('input[name="base"]'), function (radio) {
              if (radio.checked) base = radio.value
            })
            var props = getSiteProps()
            var loc = document.location
            var baseUri = loc.protocol + '//' + loc.host + loc.pathname
            if (props.base_uri_pathname) {
              baseUri = props.base_uri_pathname
            }
            var url = baseUri + '?' +
              (props.is_utf8 ? '' : 'encode_hint=' +
                encodeURIComponent('\u3077') + '&') +
              'cmd=search2' +
              '&q=' + encodeSearchText(q) +
              (base ? '&base=' + encodeURIComponent(base) : '')
            e.preventDefault()
            setTimeout(function () {
              window.location.href = url
            }, 1)
            return false
          })
          var radios = f.querySelectorAll('input[type="radio"][name="type"]')
          forEach(radios, function (radio) {
            if (radio.value === 'AND') {
              radio.addEventListener('click', onAndRadioClick)
            } else if (radio.value === 'OR') {
              radio.addEventListener('click', onOrRadioClick)
            }
          })
        } else if (f.cmd && f.cmd.value === 'search2') {
          f.addEventListener('submit', function () {
            var newSearchText = f.q.value
            var prevSearchText = f.q.getAttribute('data-original-q')
            if (newSearchText === prevSearchText) {
              // Clear resultCache to search same text again
              var props = getSiteProps()
              clearSingleCache(props.base_uri_pathname, prevSearchText)
            }
          })
        }
      })
    }
    function showNoSupportMessage () {
      var pList = document.getElementsByClassName('_plugin_search2_nosupport_message')
      for (var i = 0; i < pList.length; i++) {
        var p = pList[i]
        p.style.display = 'block'
      }
    }
    function isEnabledFetchFunctions () {
      if (window.fetch && document.querySelector && window.JSON) {
        return true
      }
      return false
    }
    function isEnableServerFunctions () {
      var props = getSiteProps()
      if (props.json_enabled) return true
      return false
    }
    prepareSearchProps()
    colorSearchTextInBody()
    if (!isEnabledFetchFunctions()) {
      showNoSupportMessage()
      return
    }
    if (!isEnableServerFunctions()) return
    replaceSearchWithSearch2()
    hookSearch2()
    removeEncodeHint()
    kickFirstSearch()
  }
  enableSearch2()
})
