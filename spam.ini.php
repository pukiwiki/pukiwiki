<?php
// $Id: spam.ini.php,v 1.2 2006/12/25 14:00:25 henoheno Exp $
// Spam-related setting

$blocklist['badhost'] = array(

	// Existing URI redirection or masking services,
	// as known as cheap URI obscuring services today,
	// for spammers and affiliate users dazed by money.
	//
	//   Messages from forerunners:
	//     o-rly.net
	//       "A URL REDIRECTION SERVICE GONE BAD"
	//       "SORRY, TRULY"
	//     smcurl.com
	//       "Idiots were using smcURL to shrink URLs and
	//        send them out via spam."
	//     tinyclick.com
	//       "...stop offering it's free services because
	//        too many people were taking advantage of it"
	//
	'*.0kn.com',		// by shim.net
	'0rz.tw',
	'0url.com',
	'*.1sta.com',		// by shorturl.com
	'*.24ex.com',		// by shorturl.com
	'*.2cd.net',		// by shim.net
	'*.2fear.com',		// by shorturl.com
	'*.2fortune.com',	// by shorturl.com
	'*.2freedom.com',	// by shorturl.com
	'*.2hell.com',		// by shorturl.com
	'2hop4.com',
	'*.2savvy.com',		// by shorturl.com
	'*.2truth.com',		// by shorturl.com
	'*.2tunes.com',		// by shorturl.com
	'*.2ya.com',		// by shorturl.com
	'301url.com',
	'*.321.cn',			// by active.ws
	'*.4bb.ru',
	'*.4mg.com',		// by freeservers.com
	'*.4x2.net',		// by active.ws
	'5jp.net',
	'*.6url.com',
	'82m.org',
	'active.ws',
	'*.alturl.com',		// by shorturl.com
	'*.andmuchmore.com',// by webalias.com
	'*.antiblog.com',	// by shorturl.com
	'*.arecool.net',	// by iscool.net
	'ataja.es',
	'*.better.ws',		// by active.ws
	'*.bigbig.com',		// by shorturl.com
	'bingr.com',
	'brokenscript.com',
	'*.browser.to',		// by webalias.com
	'clipurl.com',
	'*.coolhere.com',	// by hotredirect.com
	'*.da.cx',
	'*.dealtap.com',	// by shorturl.com
	'dephine.org',
	'digbig.com',
	'*.digipills.com',
	'doiop.com',
	'*.ebored.com',		// by shorturl.com
	'*.echoz.com',		// by shorturl.com
	'elfurl.com',
	'*.emailme.net',	// by vdirect.com
	'*.escape.to',		// by webalias.com
	'*.f2b.be',			// by f2b.be
	'*.fancyurl.com',
	'ffwd.to',
	'*.filetap.com',	// by shorturl.com
	'flingk.com',
	'*.fornovices.com',	// by webalias.com
	'*.freakz.eu',		// by f2b.be
	'*.freebiefinders.net',	// by shim.net
	'*.freegaming.org',	// by shim.net
	'*.fun.to',			// by webalias.com
	'*.funurl.com',		// by shorturl.com
	'gentleurl.net',
	'*.getto.net',		// by vdirect.com
	'*.got.to',			// by webalias.com
	'*.headplug.com',	// by shorturl.com
	'*.here.ws',		// by active.ws
	'*.hereweb.com',	// by shorturl.com
	'*.hitart.com',		// by shorturl.com
	'*.homepagehere.com',	// by hotredirect.com
	'*.hothere.com',	// by hotredirect.com
	'*.hottestpix.com',	// by webalias.com
	'*.hux.de',
	'*.i89.us',
	'*.iceglow.com',
	'igoto.co.uk',
	'*.imegastores.com',// by webalias.com
	'*.inetgames.com',	// by vdirect.com
	'*.iscool.net',
	'*.isfun.net',		// by iscool.net
	'kat.cc',
	'lame.name',
	'*.latest-info.com',// by webalias.com
	'*.learn.to',		// by webalias.com
	'linkezy.com',
	'lnk.in',
	'makeashorterlink.com',
	'memurl.com',
	'minilien.com',		// by digipills.com
	'*.minilien.com',	// by digipills.com
	'*.mirrorz.com',	// by shorturl.com
	'mo-v.jp',
	'*.moviefever.com',	// by webalias.com
	'*.mp3-archives.com',	// by webalias.com
	'*.mustbehere.com',	// by hotredirect.com
	'*.mypiece.com',	// by active.ws
	'*.myprivateidaho.com',	// by webalias.com
	'mytinylink.com',
	'myurl.in',
	'*.n0.be',			// by f2b.be
	'*.n3t.nl',			// by f2b.be
	'*.ne1.net',
	'*.netbounce.com',	// by vdirect.com
	'*.netbounce.net',	// by vdirect.com
	'*.notlong.com',
	'*.official.ws',	// by active.ws
	'*.oneaddress.net',	// by vdirect.com
	'*.onlyhere.net',	// by hotredirect.com
	'*.op7.net',		// by shim.net
	'*.ouch.ws',		// by active.ws
	'*.pagehere.com',	// by hotredirect.com
	'*.paulding.net',
	'pnope.com',
	'*.premium.ws',		// by active.ws
	'qrl.jp',
	'qurl.net',
	'*.r8.org',			// by ne1.net
	'*.radpages.com',	// by webalias.com
	'*.remember.to',	// by webalias.com
	'*.resourcez.com',	// by webalias.com
	'*.return.to',		// by webalias.com
	'*.rmcinfo.fr',
	'*.runboard.com',
	's-url.net',
	'*.sail.to',		// by webalias.com
	'*.sg5.info',
	'*.shim.net',
	'shorl.com',
	'*.short.be',		// by f2b.be
	'shortenurl.com',
	'shorterlink.com',
	'shortlinks.co.uk',
	'shorttext.com',
	'*.shorturl.com',
	'shrinkurl.us',
	'shrunkurl.com',
	'shurl.org',
	'shurl.net',
	'simurl.com',
	'skocz.pl',
	'*.snapto.net',		// by vdirect.com
	'snipurl.com',
	'*.sports-reports.com',	// by webalias.com
	'*.spyw.com',		// by shorturl.com
	'*.ssr.be',			// by f2b.be
	'*.stop.to',		// by webalias.com
	'*.such.info',		// by active.ws
	'*.surfhere.net',	// by hotredirect.com
	'surl.dk',			// by s-url.dk
	'*.thrill.to',		// by webalias.com
	'tighturl.com',
	'*.tiny.cc',
	'tiny.pl',
	'tiny2go.com',
	'tinylink.com',		// by digipills.com
	'tinypic.com',
	'tinyr.us',
	'tinyurl.com',
	'*.toolbot.com',
	'*.tophonors.com',	// by webalias.com
	'*.torontonian.com',
	'*.true.ws',		// by active.ws
	'*.tux.nu',			// by iscool.net
	'*.tweaker.eu',		// by f2b.be
	'*.tz4.com',
	'*.uncutuncensored.com',	// by webalias.com
	'*.up.to',			// by webalias.com
	'*.uploadr.com',
	'url.vg',
	'url4.net',
	'*.url4.net',
	'urlcut.com',
	'urlcutter.com',
	'urlic.com',
	'urlsnip.com',
	'urlzip.de',
	'urlx.org',
	'*.v9z.com',		// by shim.net
	'*.vdirect.com',	// by vdirect.com
	'*.vdirect.net',	// by vdirect.com
	'*.veryweird.com',	// by webalias.com
	'*.visit.ws',		// by active.ws
	'*.vze.com',		// by shorturl.com
	'w3t.org',
	'*.way.to',			// by webalias.com
	'*.web-freebies.com',	// by webalias.com
	'*.webalias.com',
	'*.webdare.com',	// by webalias.com
	'*.webrally.net',	// by vdirect.com
	'wiz.sc',			// tiny.cc related
	'xrl.us',			// by metamark.net
	'*.xxx-posed.com',	// by webalias.com
	'y11.net',
	'yatuc.com',
	'yep.it',
	'z.la',
	't.z.la',			// by z.la
	'zapurl.com',
	'zippedurl.com',
	'*.zonehere.com',	// by hotredirect.com

);
?>
