<?php
// $Id: spam.ini.php,v 1.9 2007/01/20 15:47:14 henoheno Exp $
// Spam-related setting

$blocklist['goodhost'] = array(
	array(
		'example.com', '*.example.com',
		'example.net', '*.example.net',
		'example.org', '*.example.org'
	),	// by IANA
);

$blocklist['badhost'] = array(

	// Sample setting of:
	// Existing URI redirection or masking services
	// via HTTP redirection, HTML meta, HTML frame, or JavaScript,
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
	// Please notify us about this list with reason:
	// http://pukiwiki.sourceforge.jp/dev/?BugTrack2/207
	//
	'*.0kn.com',		// by shim.net
	'0nz.org',
	'0rz.tw',
	'0url.com',
	'0zed.info',
	'*.1sta.com',		// by shorturl.com
	'1url.org',
	'*.24ex.com',		// by shorturl.com
	'*.2cd.net',		// by shim.net
	'2ch2.net',
	'*.2fear.com',		// by shorturl.com
	'*.2fortune.com',	// by shorturl.com
	'*.2freedom.com',	// by shorturl.com
	'*.2hell.com',		// by shorturl.com
	'2hop4.com',
	'2s.ca',
	'*.2savvy.com',		// by shorturl.com
	'2site.com',
	'*.2truth.com',		// by shorturl.com
	'*.2tunes.com',		// by shorturl.com
	'*.2ya.com',		// by shorturl.com
	'301url.com',
	'*.321.cn',			// by active.ws
	'32url.com',
	'*.4bb.ru',
	'*.4mg.com',		// by freeservers.com
	'*.4x2.net',		// by active.ws
	'*.4t.com',			// by freeservers.com
	'5jp.net',
	'*.6url.com',
	'*.6x.to',
	'74678439.com',		// by shortify.com
	'82m.org',
	'*.8l.pl',			// by Home.pl Sp. J. (info at home.pl)
	'*.8m.com',			// by freeservers.com
	'*.8m.net',			// by freeservers.com
	'*.8k.com',			// by freeservers.com
	'*.abwb.org',
	'acnw.de',
	'active.ws',
	'store.adobe.com',	// Stop it
	'*.alturl.com',		// by shorturl.com
	'*.andmuchmore.com',// by webalias.com
	'*.antiblog.com',	// by shorturl.com
	'athomebiz.com',
	'www.athomebiz.com',
	'*.arecool.net',	// by iscool.net
	'ataja.es',
	'aukcje1.pl',
	'*.better.ws',		// by active.ws
	'*.bigbig.com',		// by shorturl.com
	'bingr.com',
	'bittyurl.com',
	'*.bittyurl.com',
	'*.be.tf',			// by ulimit.com
	'*.best.cd',		// by ulimit.com
	'*.blg.pl',			// by Home.pl Sp. J. (info at home.pl)
	'*.blo.pl',			// HTML frame
	'brokenscript.com',
	'*.browser.to',		// by webalias.com
	'*.bsd-fan.com',	// by ulimit.com
	'*.bucksogen.com',
	'budgethosts.org',
	'*.bulochka.org',	// by bucksogen.com
	'*.buzznet.com',
	'c64.ch',
	'*.c0m.st',			// by ulimit.com
	'*.ca.tc',			// by ulimit.com
	'checkasite.net',
	'www.checkasite.net',
	'*.chicappa.jp',
	'*.clan.st',		// by ulimit.com
	'clipurl.com',
	'*.com02.com',		// by ulimit.com
	'*.coolhere.com',	// by hotredirect.com
	'coolurl.de',
	'*.da.cx',
	'dae2.com',
	'*.dealtap.com',	// by shorturl.com
	'dephine.org',
	'*.discutbb.com',
	'digbig.com',
	'*.digipills.com',
	'doiop.com',
	'dornenboy.de',		// by coolurl.de
	'dtmurl.com',		// by dreamteammoney.com
	'*.dvdonly.ru',
	'*.ebored.com',		// by shorturl.com
	'*.echoz.com',		// by shorturl.com
	'elfurl.com',
	'*.emailme.net',	// by vdirect.com
	'*.en.st',			// by ulimit.com
	'eny.pl',
	'*.escape.to',		// by webalias.com
	'*.euro.st',		// by ulimit.com
	'eyeqweb.com',		// by coolurl.de
	'*.f2b.be',			// by f2b.be
	'*.faithweb.com',	// by freeservers.com
	'*.fancyurl.com',
	'ffwd.to',
	'*.filetap.com',	// by shorturl.com
	'flingk.com',
	'fm7.biz',
	'*.fornovices.com',	// by webalias.com
	'*.fr.fm',			// by ulimit.com
	'*.fr.st',			// by ulimit.com
	'*.fr.vu',			// by ulimit.com
	'flingk.com',
	'*.freakz.eu',		// by f2b.be
	'*.freebiefinders.net',	// by shim.net
	'*.freegaming.org',	// by shim.net
	'*.freehosting.net',// by freeservers.com
	'*.freeservers.com',
	'*.freewebpages.com',
	'fype.com',
	'*.fun.to',			// by webalias.com
	'*.funurl.com',		// by shorturl.com
	'galeon.com',		// by hispavista.com
	'*.galeon.com',		// by hispavista.com
	'gentleurl.net',
	'*.getto.net',		// by vdirect.com
	'goonlink.com',
	'*.got.to',			// by webalias.com
	'*.gq.nu',			// by freeservers.com
	'*.gr.st',			// by ulimit.com
	'greatitem.com',
	'*.greatitem.com',
	'gzurl.com',
	'url.grillsportverein.de',
	'hardcore-porn.de',	// by coolurl.de
	'*.headplug.com',	// by shorturl.com
	'*.here.ws',		// by active.ws
	'*.hereweb.com',	// by shorturl.com
	'*.hispavista.com',
	'*.hitart.com',		// by shorturl.com
	'*.homepagehere.com',	// by hotredirect.com
	'hort.net',
	'*.hothere.com',	// by hotredirect.com
	'*.hottestpix.com',	// by webalias.com
	'*.ht.st',			// by ulimit.com
	'*.htmlplanet.com',	// by freeservers.com
	'*.hux.de',
	'*.hyu.jp',			// by harudake.net
	'*.i89.us',
	'*.iceglow.com',
	'ie.to',
	'igoto.co.uk',
	'*.imegastores.com',// by webalias.com
	'*.inetgames.com',	// by vdirect.com
	'inetwork.co.il',
	'*.infogami.com',
	'*.int.ms',			// by ulimit.com
	'*.iscool.net',
	'*.isfun.net',		// by iscool.net
	'*.it.st',			// by ulimit.com
	'*.itgo.com',		// by freeservers.com
	'*.iwarp.com',		// by freeservers.com
	'iwebtool.com',
	'*.iwebtool.com',
	'jeeee.net',
	'jggj.net',
	'jpan.jp',
	'jemurl.com',
	'*.java-fan.com',	// by ulimit.com
	'kat.cc',
	'*.korzhik.org',	// by bucksogen.com
	'*.kovrizhka.org',	// by bucksogen.com
	'krotki.pl',
	'lame.name',
	'*.latest-info.com',// by webalias.com
	'*.learn.to',		// by webalias.com
	'lediga.st',
	'www.lediga.st',
	'liencourt.com',
	'linkezy.com',
	'linkook.com',
	'*.linux-fan.com',	// by ulimit.com
	'lnk.in',
	'*.mac-fan.com',	// by ulimit.com
	'makeashorterlink.com',
	'maschinen-bluten-nicht.de',	// by coolurl.de
	'mcturl.com',
	'*.mcturl.com',
	'memurl.com',
	'minilien.com',		// by digipills.com
	'*.minilien.com',	// by digipills.com
	'miniurl.pl',
	'*.mirrorz.com',	// by shorturl.com
	'mixi.bz',
	'mo-v.jp',
	'monster-submit.com',
	'www.monster-submit.com',
	'mooo.jp',
	'*.moviefever.com',	// by webalias.com
	'*.mp3.ms',			// by ulimit.com
	'*.mp3-archives.com',	// by webalias.com
	'*.mustbehere.com',	// by hotredirect.com
	'myactivesurf.net',
	'www.myactivesurf.net',
	'*.mypiece.com',	// by active.ws
	'*.myprivateidaho.com',	// by webalias.com
	'mytinylink.com',
	'myurl.in',
	'*.n0.be',			// by f2b.be
	'*.n3t.nl',			// by f2b.be
	'*.ne1.net',
	'*.netbounce.com',	// by vdirect.com
	'*.netbounce.net',	// by vdirect.com
	'nlug.org',			// by Nashville Linux Users Group
	'*.notlong.com',
	'*.official.ws',	// by active.ws
	'ofzo.be',
	'www.ofzo.be',
	'*.oneaddress.net',	// by vdirect.com
	'*.onlyhere.net',	// by hotredirect.com
	'*.op7.net',		// by shim.net
	'*.ouch.ws',		// by active.ws
	'*.pagehere.com',	// by hotredirect.com
	'palurl.com',
	'www.palurl.com',
	'*.paulding.net',
	'phpfaber.org',
	'*.pirozhok.org',	// by bucksogen.com
	'*.plushka.org',	// by bucksogen.com
	'pnope.com',
	'*.premium.ws',		// by active.ws
	'prettylink.com',
	'*.pryanik.org',	// by bucksogen.com
	'*.qc.tc',			// by ulimit.com
	'qrl.jp',
	'qurl.net',
	'*.r8.org',			// by ne1.net
	'*.radpages.com',	// by webalias.com
	'redirectme.to',
	'www.relic.net',
	'rio.st',
	'*.remember.to',	// by webalias.com
	'*.resourcez.com',	// by webalias.com
	'*.return.to',		// by webalias.com
	'*.rmcinfo.fr',
	'*.ryj.pl',			// by Home.pl Sp. J. (info at home.pl)
	'*.runboard.com',
	's-url.net',
	'*.s5.com',			// by freeservers.com
	'*.sail.to',		// by webalias.com
	'*.scriptmania.com',// by freeservers.com
	'*.sg5.info',
	'*.shim.net',
	'shorl.com',
	'*.short.be',		// by f2b.be
	'shortenurl.com',
	'shorterlink.com',
	'shortlinks.co.uk',
	'shorttext.com',
	'*.shorturl.com',
	'shorturl-accessanalyzer.com',
	'shortify.com',
	'shrinkthatlink.com',
	'www.shrinkthatlink.com',
	'shrinkurl.us',
	'shrunkurl.com',
	'*.shrunkurl.com',
	'shurl.org',
	'shurl.net',
	'sid.to',
	'simurl.com',
	'skiltechurl.com',
	'skocz.pl',
	'slimurl.jp',
	'smallurl.eu',
	'*.snapto.net',		// by vdirect.com
	'snipurl.com',
	'*.sp.st',			// by ulimit.com
	'sp-nov.net',
	'www.sp-nov.net',
	'splashblog.com',
	'www.splashblog.com',
	'*.sports-reports.com',	// by webalias.com
	'*.spyw.com',		// by shorturl.com
	'*.ssr.be',			// by f2b.be
	'*.stop.to',		// by webalias.com
	'*.suisse.st',		// by ulimit.com
	'*.such.info',		// by active.ws
	'*.sushka.org',		// by bucksogen.com
	'*.surfhere.net',	// by hotredirect.com
	'surl.dk',			// by s-url.dk
	'symy.jp',
	'*.t2u.com',		// by ulimit.com
	'*.thrill.to',		// by webalias.com
	'tighturl.com',
	'tlurl.com',
	'*.tiny.cc',
	'tiny.pl',
	'tiny2go.com',
	'tinylink.com',		// by digipills.com
	'tinylinkworld.com',
	'www.tinylinkworld.com',
	'tinypic.com',
	'tinyr.us',
	'tinyurl.com',
	'tinyurl.name',		// by comteche.com
	'tinyurl.us',		// by comteche.com
	'*.toolbot.com',
	'*.tophonors.com',	// by webalias.com
	'*.torontonian.com',
	'*.true.ws',		// by active.ws
	'*.tvheaven.com',	// by freeservers.com
	'*.tux.nu',			// by iscool.net
	'*.tweaker.eu',		// by f2b.be
	'*.tz4.com',
	'uchinoko.in',
	'*.uncutuncensored.com',	// by webalias.com
	'*.uni.cc',
	'*.unixlover.com',	// by ulimit.com
	'*.up.to',			// by webalias.com
	'*.uploadr.com',
	'url.vg',			// by jeremyjohnstone.com
	'url4.net',
	'*.url4.net',
	'url-c.com',
	'urlcut.com',
	'urlcutter.com',
	'urlic.com',
	'urlsnip.com',
	'urlzip.de',
	'urlx.org',
	'*.v9z.com',		// by shim.net
	'*.vdirect.com',	// by vdirect.com
	'*.vdirect.net',	// by vdirect.com
	'vgo2.com',
	'www.vgo2.com',
	'*.veryweird.com',	// by webalias.com
	'*.visit.ws',		// by active.ws
	'*.vze.com',		// by shorturl.com
	'w3t.org',
	'wapurl.co.uk',
	'*.way.to',			// by webalias.com
	'www.wbkt.net',
	'*.web-freebies.com',	// by webalias.com
	'*.webalias.com',
	'*.webdare.com',	// by webalias.com
	'webmasterwise.com',
	'*.webmasterwise.com',
	'*.webrally.net',	// by vdirect.com
	'wiz.sc',			// tiny.cc related
	'*.xit.pl',			// by Home.pl Sp. J. (info at home.pl)
	'*.xlc.pl',			// by Home.pl Sp. J. (info at home.pl)
	'xrl.us',			// by metamark.net
	'*.xxx-posed.com',	// by webalias.com
	'y11.net',
	'yatuc.com',
	'yep.it',
	'z.la',
	't.z.la',			// by z.la
	'zapurl.com',
	'*.zik.mu',			// by ulimit.com
	'zippedurl.com',
	'*.zonehere.com',	// by hotredirect.com
	'zuso.tw',


	// Sample setting of: 
	// Dynamic DNS or Dynamic IP or DNS services
	//
	// Please notify us about this list with reason:
	// http://pukiwiki.sourceforge.jp/dev/?BugTrack2/207
	//
	//
	//'*.ath.cx',				// by dydns.com
	//'*.bpa.nu',				// by ddns.ru
	//'*.dnip.net',
	//'*.dyndns.*',
		//'*.dyndns.dk',
		//'*.dyndns.co.za',
		//'*.dyndns.org',	// by dydns.com
		//'*.dyndns.nemox.net',
	//'*.dynu.com',
	//'*.shacknet.nu',		// by dydns.com
	//'*.nerdcamp.net',
	//'*.zenno.info',
	//'*.mine.nu',			// by dydns.com


	// Sample setting of: Jacked (taken advantage of) and cleaning-less sites
	//
	// Please notify us about this list with reason:
	// http://pukiwiki.sourceforge.jp/dev/?BugTrack2%2F208

	// 1. Web-spaces
	'*.0catch.com',		// by bluehost.com
	'*.150m.com',		// by 100 Best, Inc., NS by 0catch.com
	'20six.nl',			// by 20six weblog services (postmaster at 20six.nl)
	'*.20six.nl',
	'20six.co.uk',		// by 20six weblog services
	'*.20six.co.uk',
	'20six.fr',			// by 20six weblog services
	'*.20six.fr',
	'*.50megs.com',
	'*.9999mb.com',
	'*.aimoo.com',
	'*.alice.it',
	'*.alkablog.com'.
	'*.atfreeforum.com',
	'*.asphost4free.com',
	'bloggers.nl',
	'*.bloggers.nl',
	'*.blogspot.com',		// by Google
	'*.bravenet.com',
	'dakrats.net',
	'*.diaryland.com',
	'*.dox.hu',
	'*.eblog.com.au',
	'*.extra.hu',
	'fingerprintmedia.com',
	'*.free-25.de',
	'*.free-bb.com',
	'*.freelinuxhost.com',	// by 100webspace.com
	'www.freeforum.at',
	'www.forumprofi.de',
	'www.forumprofi1.de',	// by forumprofi.de
	'www.forumprofi2.de',	// by forumprofi.de
	'www.forumprofi3.de',	// by forumprofi.de
	'*.goodboard.de',
	'docs.google.com',			// by Google
	'groups-beta.google.com',	// by Google
	'www.healthcaregroup.com',
	'*.hk.pl',				// by info at home.pl
	'*.host-page.com',
	'*.home.pl',			// by info at home.pl
	'hometown.aol.com',
	'*.ifastnet.com',
	'*.ifrance.com',
	'*.journalscape.com',
	'ltss.luton.ac.uk',
	'*.monforum.com',
	'*.monforum.fr',		// by monforum.com
	'myblog.de',			// by 20six weblog services
	'myblog.es',			// by 20six weblog services
	'*.myblogvoice.com',
	'*.netfast.org',
	'neweconomics.info',
	'*.nm.ru',
	'*.phpbbx.de',
	'*.quickfreehost.com',
	'*.sayt.ws',
	'*.sbn.bz',				// by rin.ru
	'*.spazioforum.it',
	'*.squarespace.com',
	'*.stormloader.com',
	'*.t35.com',
	'*.talkthis.com',
	'thestudentunderground.org',
	'www.think.ubc.ca',
	'*.welover.org',
	'*.weblogmaniacs.com',
	'weblogmaniacs.com',
	'*.wmjblogs.ru',
	'*.wol.bz',				 // by sbn.bz (rin.ru)
	'xeboards.com',
	'yourfreebb.de',

	// 2. (Seems to be) Jacked contents, something implanted
	// (e.g. some sort of blog comments, BBSes, forums, wikis)
	'*.aamad.org',
	'anewme.org',
	'www.blepharospasm.org',
	'*.buzznet.com',
	'*.colourware.co.uk',
	'icu.edu.ua',
	'*.iphpbb.com',
	'board-z.de',
	'*.board-z.de',
	'dc503.org',
	'fhmcsa.org.au',
	'*.fhmcsa.org.au',
	'forum.lixium.fr',
	'funkdoc.com',
	'*.goodboard.de',
	'www.homepage-dienste.com',
	'*.inventforum.com',
	'www.funnyclipcentral.com',
	'internetincomeclub.com',
	'kevindmurray.com',
	'www.macfaq.net',
	'www.me4x4.com',
	'morallaw.org',
	'www.morerevealed.com',
	'mountainjusticemedia.org',
	'users.nethit.pl',
	'njbodybuilding.com',
	'nlen.org',
	'omikudzi.ru',
	'www.privatforum.de',
	'*.reallifelog.com',
	'rkphunt.com',
	'www.saskchamber.com',
	'selikoff.net',
	'www.setbb.com',
	'silver-tears.net',
	'theedgeblueisland.com',
	'www.tzaneen.co.za',
	'urgentclick.com',
	'www.wvup.edu',


	// Sample setting of: Exclusive spam domains
	// seems to have flavor of links, pills, gamble, online-games, erotic,
	// affiliates, finance, and/or mixed ones
	//
	// Please notify us about this list with reason:
	// http://pukiwiki.sourceforge.jp/dev/?BugTrack2/208

	// 1. Domain sets (seems to be) born to spam you
	'*.lovestoryx.com',	// by Boris (admin at seekforweb.com, bbmfree at yahoo.com)
	'*.loveaffairx.com',// by Boris (admin at seekforweb.com, bbmfree at yahoo.com)
	'*.onmore.info',	// by Boris (admin at seekforweb.com, bbmfree at yahoo.com)
	'*.scfind.info',	// by Boris (admin at seekforweb.com, bbmfree at yahoo.com)
	'*.scinfo.info',	// by Boris (admin at seekforweb.com, bbmfree at yahoo.com)
	'*.webwork88.info',	// by Boris (admin at seekforweb.com, bbmfree at yahoo.com)
	//
	'htewbop.org',		// by Boris (boss at bse-sofia.bg)
	'*.htewbop.org',
	'*.kimm--d.org',	// by Boris (boss at bse-sofia.bg)
	'*.gtre--h.org',	// by Boris (boss at bse-sofia.bg)
	'*.npou--k.org',	// by Boris (boss at bse-sofia.bg)
	'*.bres--z.org',	// by Boris (boss at bse-sofia.bg)
	'berk--p.org',		// by Boris (boss at bse-sofia.bg)
	'*.bplo--s.org',	// by Boris (boss at bse-sofia.bg)
	'*.basdpo.org',		// by Boris (boss at bse-sofia.bg)
	'jisu--m.org',		// by Boris (boss at bse-sofia.bg)
	'kire--z.org',		// by Boris (boss at bse-sofia.bg)
	'*.mertnop.org',	// by Boris (boss at bse-sofia.bg)
	'mooa--c.org',		// by Boris (boss at bse-sofia.bg)
	'nake--h.org',		// by Boris (boss at bse-sofia.bg)
	'noov--b.org',		// by Boris (boss at bse-sofia.bg)
	'suke--y.org',		// by Boris (boss at bse-sofia.bg)
	'vasdipv.org',		// by Boris (boss at bse-sofia.bg)
	'*.vasdipv.org',
	'vase--l.org',		// by Boris (boss at bse-sofia.bg)
	'vertinds.org',		// by Boris (boss at bse-sofia.bg)
	//
	'*.aqq3.info',		// by Thai Dong Changli (pokurim at gamebox.net)
	'*.axa00.info',		// by Thai Dong Changli (pokurim at gamebox.net)
	'*.okweb11.org',	// by Thai Dong Changli (pokurim at gamebox.net)
	'*.okweb12.org',	// by Thai Dong Changli (pokurim at gamebox.net)
	'*.okweb13.org',	// by Thai Dong Changli (pokurim at gamebox.net)
	'*.okweb14.org',	// by Thai Dong Changli (pokurim at gamebox.net)
	//
	'informazionicentro.info',	// by opezdol at gmail.com
	'*.informazionicentro.info',// by opezdol at gmail.com
	'notiziacentro.info',		// by opezdol at gmail.com
	'*.notiziacentro.info',		// by opezdol at gmail.com
	//
	'*.adult-chat-world.info',	// 'CamsGen' by Lui Xeng Shou (camsgen at model-x.com),
	'*.adult-chat-world.org',	// 'CamsGen' by Lui Xeng Shou (camsgen at model-x.com)
	'*.adult-sex-chat.info',	// 'CamsGen' by Lui Xeng Shou (camsgen at model-x.com)
	'*.adult-sex-chat.org',		// 'CamsGen' by Lui Xeng Shou (camsgen at model-x.com)
	'*.adult-cam-chat.info',	// 'CamsGen' by Lui Xeng Shou (camsgen at model-x.com)
	'*.adult-cam-chat.org',		// 'CamsGen' by Lui Xeng Shou (camsgen at model-x.com)
	'*.dildo-chat.org',			// 'CamsGen' by Lui Xeng Shou (camsgen at model-x.com)
	'*.dildo-chat.info',		// 'CamsGen' by Lui Xeng Shou (camsgen at model-x.com)
	// flirt-online.info is not CamsGen
	'*.flirt-online.org',		// 'CamsGen' by Lui Xeng Shou (camsgen at model-x.com)
	'*.live-adult-chat.info',	// 'CamsGen' by Lui Xeng Shou (camsgen at model-x.com)
	'*.live-adult-chat.org',	// 'CamsGen' by Lui Xeng Shou (camsgen at model-x.com)
	'*.sexy-chat-rooms.info',	// 'CamsGen' by Lui Xeng Shou (camsgen at model-x.com)
	'*.sexy-chat-rooms.org',	// 'CamsGen' by Lui Xeng Shou (camsgen at model-x.com)
	'*.swinger-sex-chat.info',	// 'CamsGen' by Lui Xeng Shou (camsgen at model-x.com)
	'*.swinger-sex-chat.org',	// 'CamsGen' by Lui Xeng Shou (camsgen at model-x.com)
	'*.nasty-sex-chat.info',	// 'CamsGen' by Lui Xeng Shou (camsgen at model-x.com)
	'*.nasty-sex-chat.org',		// 'CamsGen' by Lui Xeng Shou (camsgen at model-x.com)
	'*.camshost.info',			// 'CamsGen' by Sergey (buckster at hotpop.com)
	'*.camdoors.info',			// 'CamsGen' by Sergey (buckster at hotpop.com)
	'*.chatdoors.info',			// 'CamsGen' by Sergey (buckster at hotpop.com)
	'*.lebedi.info',			// 'BucksoGen', by Pronin Sergey (buckster at list.ru), 
	'*.loshad.info',			// 'BucksoGen', by Pronin Sergey (buckster at list.ru)
	'*.porosenok.info',			// 'BucksoGen', by Pronin Sergey (buckster at list.ru)
	'*.indyushonok.info',		// 'BucksoGen', by Pronin Sergey (buckster at list.ru)
	'*.kotyonok.info',			// 'BucksoGen', by Pronin Sergey (buckster at list.ru)
	'*.kozlyonok.info',			// 'BucksoGen', by Pronin Sergey (buckster at list.ru)
	'*.svinka.info',			// 'BucksoGen', by Pronin Sergey (buckster at list.ru)
	'*.svinya.info',			// 'BucksoGen', by Pronin Sergey (buckster at list.ru)
	'*.zherebyonok.info',		// 'BucksoGen', by Pronin Sergey (buckster at list.ru)
	//
	'*.trevisos.org',	// by Marcello Italianore (mital at topo20.org)
	'*.topo20.org',		// by Marcello Italianore (mital at topo20.org)
	//
	'*.wellcams.com',
	'*.j8v9.info',		// by wellcams.com
	'wellcams.biz',		// by wellcams.com
	//
	'*.besturgent.org',		// by Chinu Hua Dzin (graz at rubli.biz)
	'*.googletalknow.org',	// by Chinu Hua Dzin (graz at rubli.biz)
	'*.montypythonltd.org',	// by Chinu Hua Dzin (graz at rubli.biz)
	'*.supersettlemet.org',	// by Chinu Hua Dzin (graz at rubli.biz)
	'*.thepythonfoxy.org',	// by Chinu Hua Dzin (graz at rubli.biz)
	'*.ukgamesyahoo.org',	// by Chinu Hua Dzin (graz at rubli.biz)
	'*.youryahoochat.org',	// by Chinu Hua Dzin (graz at rubli.biz)
	//
	'*.casinoqz.com',		// by Berenice Snow
	'*.dcasinoa.com',		// by August Hawkinson, post with casinoqz.com
	//
	'*.kenogo.com',			// by Adriane Bell
	'*.mycaribbeanpoker.com',	// by Andy Mullis, post with kenogo.com
	'*.crapsok.com',
	'*.onbaccarat.com',		// post with crapsok.com
	//
	'dbsajax.org',		// by Kikimas at mail.net, Redirect to nb717.com etc
	'*.dbsajax.org',
	'acgt2005.org',		// by Kikimas at mail.net, Redirect to nb717.com etc
	'*.acgt2005.org',
	'gopikottoor.com',	// by Kikimas at mail.net, Redirect to nb717.com etc
	'*.gopikottoor.com',
	'koosx.org',		// by Kikimas at mail.net, Redirect to nb717.com etc
	'*.koosx.org',
	'mmgz.org',			// by Kikimas at mail.net, Redirect to nb717.com etc
	'*.mmgz.org',
	'zhiyehua.net',		// by Kikimas at mail.net, Redirect to nb717.com etc
	'*.zhiyehua.net',
	//
	'43sexx.org',		// by Andrey (vdf at lovespb.com)
	'*.43sexx.org',
	'56porn.org',		// by Andrey (vdf at lovespb.com)
	'*.56porn.org',
	'78porn.org',		// by Andrey (vdf at lovespb.com)
	'*.78porn.org',
	'92ssex.org',		// by Andrey (vdf at lovespb.com)
	'*.92ssex.org',
	'93adult.org',		// by Andrey (vdf at lovespb.com)
	'*.93adult.org',
	'buypo.info',		// by Andrey (vdf at lovespb.com), redirect to activefreehost.com
	'*.buypo.info',
	'freexz.info',		// by Andrey (vdf at lovespb.com), redirect to activefreehost.com
	'*.freexz.info',
	'lovespb.info',		// by Andrey (vdf at lovespb.com), redirect to activefreehost.com
	'*.lovespb.info',
	'oursales.info',	// by Andrey (vdf at lovespb.com), redirect to activefreehost.com
	'*.oursales.info',
	'pldk.info',		// by Andrey (vdf at lovespb.com), redirect to activefreehost.com
	'*.pldk.info',
	'poz2.info',		// by Andrey (vdf at lovespb.com), redirect to activefreehost.com
	'*.poz2.info',
	'saleqw.info',		// by Andrey (vdf at lovespb.com), redirect to activefreehost.com
	'*.saleqw.info',
	'usacanadauk.info',	// by Andrey (vdf at lovespb.com), redirect to activefreehost.com
	'*.usacanadauk.info',
	//
	'*.flywebs.com',	// by Andrey Zhurikov (zhu1313 at mail.ru)
	'*.hostrim.com',	// by Andrey Zhurikov (zhu1313 at mail.ru)
	'playbit.com',		// by Andrey Zhurikov (zhu1313 at mail.ru)
	//
	'*.bsb3b.info',		// by Son Dittman (webmaster at dgo3d.info)
	'*.dgo3d.info',		// by Son Dittman (webmaster at dgo3d.info)
	'*.dgo5d.info',		// by Son Dittman (webmaster at dgo3d.info)
	//
	'diabetescarelink.com',	// by cooler.infomedia at gmail.com
	'firstdebthelp.com',	// by cooler.infomedia at gmail.com
	//
	'*.pokah.lv',		// by Nikolajs Karpovs (hostmaster at astrons.com)
	'*.astrons.com',	// by Nikolaj  Karpov  (hostmaster at astrons.com)
	//
	'implex3.com',		// by Skar (seocool at bk.ru)
	'softprof.org',		// by Skar (seocool at bk.ru)
	//
	'tops.gen.in',		// Hiding google:sites. by Kosare (billing at caslim.info)
	'caslim.info',
	//
	'777-poker.biz',	// by Alexandr (foxwar at foxwar.ispvds.com), Hiding google?q=
	'*.777-poker.biz',
	'*.porn-11.com',	// by Alexandr (foxwar at foxwar.ispvds.com)
	//
	'*.conto.pl',		// by biuro at nazwa.pl
	'*.guu.pl',			// by conto.pl (domena at az.pl)
	//
	// Domains by Lin Zhi Qiang (mail at pcinc.cn)
	// NOTE: pcinc.cn -- by Lin Zhi Qiang (lin80 at 21cn.com)
	'bbs-qrcode.com',
	'*.bbs-qrcode.com',
	'conecojp.net',
	'*.conecojp.net',
	'gamaniaech.com',
	'*.gamaniaech.com',
	'game-oekakibbs.com',
	'*.game-oekakibbs.com',
	'games-nifty.com',
	'*.games-nifty.com',
	'gamesragnaroklink.net',
	'*.gamesragnaroklink.net',
	'gemnnammobbs.com',
	'*.gemnnammobbs.com',
	'geocitylinks.com',
	'*.geocitylinks.com',
	'homepage3-nifty.com',
	'*.homepage3-nifty.com',
	'hosetaibei.com',
	'*.hosetaibei.com',
	'jpragnarokonline.com',
	'*.jpragnarokonline.com',
	'jprmthome.com',
	'*.jprmthome.com',
	'lineage1bbs.com',
	'*.lineage1bbs.com',
	'lineage321.com',
	'*.lineage321.com',
	'netgamelivedoor.com',
	'*.netgamelivedoor.com',
	'playsese.com',
	'*.playsese.com',
	'ragnarok-game.com',
	'*.ragnarok-game.com',
	'ragnaroklink.com',
	'*.ragnaroklink.com',
	'rmt-navip.com',
	'*.rmt-navip.com',
	'roprice.com',
	'*.roprice.com',
	'watcheimpress.com',
	'*.watcheimpress.com',
	//
	'*.entirestar.com',		// by Baer (aakin at yandex.ru)
	'*.superbuycheap.com',	// by Baer (aakin at yandex.ru)
	'*.topdircet.com',		// by Baer (aakin at yandex.ru)
	//
	'tianmieccp.com',	// by jiuhatu kou (newblog9 at gmail.com)
	'*.tianmieccp.com',
	'xianqiao.net',		// by jiuhatu kou (newblog9 at gmail.com)
	'*.xianqiao.net',
	//
	'onunicarehealthinsurance.com',	// by  Lawerence Paredes
	'*.onunicarehealthinsurance.com',
	'healthinsuranceem.com',		// by Justin Munson
	'*.healthinsuranceem.com',
	//
	'soft2you.info',	// by Michael (m.frenzy at yahoo.com)
	'*.soft2you.info',
	'top20health.info',	// by Michael (m.frenzy at yahoo.com)
	'*.top20health.info',
	'x09x.info',		// by Michael (m.frenzy at yahoo.com)
	'*.x09x.info',
	//
	'*.isuperdrug.com',	// by Lebedev Sergey (serega555serega555 at yandex.ru)
	'*.vviagra.info',	// by Lebedev Sergey (serega555serega555 at yandex.ru)


	// 2. Lonely domains (buddies not found yet)
	'19cellar.info',	// by Eduardo Guro (boomouse at gmail.com)
	'*.areaseo.com',	// by Antony Carpito (xcentr at lycos.com)
	'*.dlekei.info',	// by Maxima Bucaro (webmaster at tts2f.info)
	'*.discutbb.com',	// by Perez Thomas (thomas.jsp at libertysurf.fr)
	'dreamteammoney.com',	// dtmurl.com related
	'*.ec51.com',		// by zhenfei chen (szczffhh_sso at 21cn.net)
	'fastppc.info',		// by peter conor (fastppc at msn.com)
	'*.fateback.com',	// by LiquidNet Ltd. Redirect to www.japan.jp
	'*.free-rx.net',	// by Neo-x (neo-xxl at yandex.ru), redirect to activefreehost.com
	'hotscriptonline.info',	// by Psy Search (admin at psysearch.com)
	'*.hut1.ru',		// by domains at agava.com
	'investorvillage.com',
	'ismarket.com',		// Google-hiding. intercage.com related IP
	'italy-search.org',	// by Alex Yablin (zaharov-alex at yandex.ru)
	'*.italy-search.org',
	'*.jimka-mmsa.com',	// by Alex Covax (c0vax at mail.ru)
	'myfgj.info',		// by Filus (softscript at gmail.com)
	'*.mujiki.com',		// by Mila Contora (ebumsn at ngs.ru)
	'*.pahuist.info',	// by Yura (yuralg2005 at yandex.ru)
	'*.perevozka777.ru',	// by witalik at gmail.com
	'portaldiscount.com',	// by Mark Tven (bestsaveup at gmail.com)
	'*.portaldiscount.com',
	'*.prama.info',		// by Juan.Kang at mytrashmail.com
	'qoclick.net',		// by DMITRIY SOLDATENKO
	'relurl.com',		// tiny-like. by Grzes Tlalka (grzes1111 at interia.pl)
	'sirlook.com',
	'unctad.net',		// by gfdogfd at lovespb.com
	'*.webnow.biz',		// by Hsien I Fan (admin at servcomputing.com)
	'wellcams.biz',		// by Sergey Sergiyenko (studioboss at gmail.com)
	'zlocorp.com',		// by tonibcrus at hotpop.com, spammed well with "http ://zlocorp.com/"
	'*.zlocorp.com',
	'*.roin.info',		// by Evgenius (roinse at yandex.ru)


	// Hosts shown inside the implanted contents
	// not used via spam, but useful to detect these contents
	//
	// RESERVED


	// Not classifiable (information wanted)
	// Something incoming to pukiwiki related sites
	'mylexus.info',		// by Homer Simpson (simhomer12300 at mail.com), Redirect to Google
	'up2.co.il',		// inetwork.co.il related
);
?>
