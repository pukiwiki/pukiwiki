<?php
// $Id: spam.ini.php,v 1.8 2007/01/07 08:01:16 henoheno Exp $
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
	'*.2savvy.com',		// by shorturl.com
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
	'82m.org',
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
	'*.be.tf',			// by ulimit.com
	'*.best.cd',		// by ulimit.com
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
	'*.dealtap.com',	// by shorturl.com
	'dephine.org',
	'*.discutbb.com',
	'digbig.com',
	'*.digipills.com',
	'doiop.com',
	'dornenboy.de',		// by coolurl.de
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
	'url.grillsportverein.de',
	'hardcore-porn.de',	// by coolurl.de
	'*.headplug.com',	// by shorturl.com
	'*.here.ws',		// by active.ws
	'*.hereweb.com',	// by shorturl.com
	'*.hispavista.com',
	'*.hitart.com',		// by shorturl.com
	'*.homepagehere.com',	// by hotredirect.com
	'*.hothere.com',	// by hotredirect.com
	'*.hottestpix.com',	// by webalias.com
	'*.ht.st',			// by ulimit.com
	'*.htmlplanet.com',	// by freeservers.com
	'*.hux.de',
	'*.i89.us',
	'*.iceglow.com',
	'igoto.co.uk',
	'*.imegastores.com',// by webalias.com
	'*.inetgames.com',	// by vdirect.com
	'*.infogami.com',
	'*.int.ms',			// by ulimit.com
	'*.iscool.net',
	'*.isfun.net',		// by iscool.net
	'*.it.st',			// by ulimit.com
	'*.itgo.com',		// by freeservers.com
	'*.iwarp.com',		// by freeservers.com
	'jpan.jp',
	'jemurl.com',
	'*.java-fan.com',	// by ulimit.com
	'kat.cc',
	'*.korzhik.org',	// by bucksogen.com
	'*.kovrizhka.org',	// by bucksogen.com
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
	'memurl.com',
	'minilien.com',		// by digipills.com
	'*.minilien.com',	// by digipills.com
	'*.mirrorz.com',	// by shorturl.com
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
	'*.remember.to',	// by webalias.com
	'*.resourcez.com',	// by webalias.com
	'*.return.to',		// by webalias.com
	'*.rmcinfo.fr',
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
	'*.uncutuncensored.com',	// by webalias.com
	'*.uni.cc',
	'*.unixlover.com',	// by ulimit.com
	'*.up.to',			// by webalias.com
	'*.uploadr.com',
	'url.vg',			// by jeremyjohnstone.com
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
	'*.9999mb.com',
	'*.aimoo.com',
	'*.alice.it',
	'*.alkablog.com'.
	'*.atfreeforum.com',
	'*.asphost4free.com',
	'bloggers.nl',
	'*.bloggers.nl',
	'*.blogspot.com',
	'*.bravenet.com',
	'*.diaryland.com',
	'*.dox.hu',
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
	'groups-beta.google.com',
	'www.healthcaregroup.com',
	'*.hk.pl',				// by info at home.pl
	'*.home.pl',			// by info at home.pl
	'hometown.aol.com',
	'*.ifastnet.com',
	'*.ifrance.com',
	'*.journalscape.com',
	'*.monforum.com',
	'*.monforum.fr',		// by monforum.com
	'myblog.de',			// by 20six weblog services
	'myblog.es',			// by 20six weblog services
	'*.myblogvoice.com',
	'neweconomics.info',
	'*.nm.ru',
	'*.quickfreehost.com',
	'*.sayt.ws',
	'*.sbn.bz',				// by rin.ru
	'*.squarespace.com',
	'*.stormloader.com',
	'*.t35.com',
	'*.talkthis.com',
	'thestudentunderground.org',
	'www.think.ubc.ca',
	'*.welover.org',
	'*.weblogmaniacs.com',
	'weblogmaniacs.com',
	'*.wol.bz', // by sbn.bz (rin.ru)

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
	'*.inventforum.com',
	'www.funnyclipcentral.com',
	'internetincomeclub.com',
	'www.homepage-dienste.com',
	'www.macfaq.net',
	'www.me4x4.com',
	'www.morerevealed.com',
	'mountainjusticemedia.org',
	'*.reallifelog.com',
	'rkphunt.com',
	'www.saskchamber.com',
	'selikoff.net',
	'www.setbb.com',
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
	'*.mertnop.org',	// by Boris (boss at bse-sofia.bg)
	'noov--b.org',		// by Boris (boss at bse-sofia.bg)
	'suke--y.org',		// by Boris (boss at bse-sofia.bg)
	'vasdipv.org',		// by Boris (boss at bse-sofia.bg)
	'*.vasdipv.org',
	'vase--l.org',		// by Boris (boss at bse-sofia.bg)
	'vertinds.org',		// by Boris (boss at bse-sofia.bg)
	//
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
	'*.adult-chat-world.info',	// by CamsGen (camsgen at model-x.com)
	'*.adult-chat-world.org',	// by CamsGen (camsgen at model-x.com)
	'*.adult-sex-chat.info',	// by CamsGen (camsgen at model-x.com)
	'*.adult-sex-chat.org',		// by CamsGen (camsgen at model-x.com)
	'*.adult-cam-chat.info',	// by CamsGen (camsgen at model-x.com)
	'*.adult-cam-chat.org',		// by CamsGen (camsgen at model-x.com)
	'*.camshost.info',			// by CamsGen (buckster at hotpop.com)
	'*.camdoors.info',			// by CamsGen (buckster at hotpop.com)
	'*.chatdoors.info',			// by CamsGen (buckster at hotpop.com)
	'*.dildo-chat.org',			// by CamsGen (camsgen at model-x.com)
	'*.dildo-chat.info',		// by CamsGen (camsgen at model-x.com)
	'*.live-adult-chat.info',	// by CamsGen (camsgen at model-x.com)
	'*.live-adult-chat.org',	// by CamsGen (camsgen at model-x.com)
	'*.sexy-chat-rooms.info',	// by CamsGen (camsgen at model-x.com)
	'*.sexy-chat-rooms.org',	// by CamsGen (camsgen at model-x.com)
	'*.swinger-sex-chat.info',	// by CamsGen (camsgen at model-x.com)
	'*.swinger-sex-chat.org',	// by CamsGen (camsgen at model-x.com)
	'*.nasty-sex-chat.info',	// by CamsGen (camsgen at model-x.com)
	'*.nasty-sex-chat.org',		// by CamsGen (camsgen at model-x.com)
	// flirt-online.info is not CamsGen
	'*.flirt-online.org',		// by CamsGen (camsgen at model-x.com)
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
	'*.koosx.org',		// by Kikimas at mail.net, Redirect to nb717.com etc
	'*.mmgz.org',		// by Kikimas at mail.net, Redirect to nb717.com etc
	'*.zhiyehua.net',	// by Kikimas at mail.net, Redirect to nb717.com etc
	//
	'43sexx.org',		// by Andrey (vdf at lovespb.com)
	'*.43sexx.org',
	'56porn.org',		// by Andrey (vdf at lovespb.com)
	'*.56porn.org',
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
	// pcinc.cn -- by Lin Zhi Qiang (lin80 at 21cn.com)
	'gamesragnaroklink.net',// by Lin Zhi Qiang (mail at pcinc.cn)
	'*.gamesragnaroklink.net',
	'gemnnammobbs.com',		// by Lin Zhi Qiang (mail at pcinc.cn)
	'*.gemnnammobbs.com',
	'homepage3-nifty.com',	// by Lin Zhi Qiang (mail at pcinc.cn)
	'*.homepage3-nifty.com',
	'jpragnarokonline.com',// by Lin Zhi Qiang (mail at pcinc.cn)
	'*.jpragnarokonline.com',
	'ragnaroklink.com',		// by Lin Zhi Qiang (mail at pcinc.cn)
	'*.ragnaroklink.com',

	// 2. Lonely domains (buddies not found yet)
	'*.areaseo.com',	// by Antony Carpito (xcentr at lycos.com)
	'*.dlekei.info',	// by Maxima Bucaro (webmaster at tts2f.info)
	'*.discutbb.com',	// by Perez Thomas (thomas.jsp at libertysurf.fr)
	'*.ec51.com',		// by zhenfei chen (szczffhh_sso at 21cn.net)
	'hotscriptonline.info',	// by Psy Search (admin at psysearch.com)
	'*.hut1.ru',		// by domains at agava.com
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
	'*.soft2you.info',	// by Michael (m.frenzy at yahoo.com)
	'unctad.net',		// by gfdogfd at lovespb.com
	'*.webnow.biz',		// by Hsien I Fan (admin at servcomputing.com)
	'wellcams.biz',		// by Sergey Sergiyenko (studioboss at gmail.com)
	'zlocorp.com',		// by tonibcrus at hotpop.com, spammed well with "http ://zlocorp.com/"
	'*.zlocorp.com',


	// Hosts shown inside the implanted contents
	// not used via spam, but useful to detect these contents
	//
	// RESERVED

);
?>
