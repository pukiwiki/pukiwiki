<?php
// $Id: spam.ini.php,v 1.4 2007/01/01 14:33:29 henoheno Exp $
// Spam-related setting

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
	'*.4t.com',			// by freeservers.com
	'5jp.net',
	'*.6url.com',
	'*.6x.to',
	'82m.org',
	'*.8m.com',			// by freeservers.com
	'*.8m.net',			// by freeservers.com
	'*.8k.com',			// by freeservers.com
	'*.abwb.org',
	'active.ws',
	'store.adobe.com',	// Stop it
	'*.alturl.com',		// by shorturl.com
	'*.andmuchmore.com',// by webalias.com
	'*.antiblog.com',	// by shorturl.com
	'*.arecool.net',	// by iscool.net
	'ataja.es',
	'*.better.ws',		// by active.ws
	'*.bigbig.com',		// by shorturl.com
	'bingr.com',
	'*.be.tf',			// by ulimit.com
	'*.best.cd',		// by ulimit.com
	'brokenscript.com',
	'*.browser.to',		// by webalias.com
	'*.bsd-fan.com',	// by ulimit.com
	'*.bucksogen.com',
	'*.bulochka.org',	// by bucksogen.com
	'*.buzznet.com',
	'*.c0m.st',			// by ulimit.com
	'*.ca.tc',			// by ulimit.com
	'*.chicappa.jp',
	'*.clan.st',		// by ulimit.com
	'clipurl.com',
	'*.com02.com',		// by ulimit.com
	'*.coolhere.com',	// by hotredirect.com
	'*.da.cx',
	'*.dealtap.com',	// by shorturl.com
	'dephine.org',
	'*.discutbb.com',
	'digbig.com',
	'*.digipills.com',
	'doiop.com',
	'*.ebored.com',		// by shorturl.com
	'*.echoz.com',		// by shorturl.com
	'elfurl.com',
	'*.emailme.net',	// by vdirect.com
	'*.en.st',			// by ulimit.com
	'*.escape.to',		// by webalias.com
	'*.euro.st',		// by ulimit.com
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
	'*.int.ms',			// by ulimit.com
	'*.iscool.net',
	'*.isfun.net',		// by iscool.net
	'*.it.st',			// by ulimit.com
	'*.itgo.com',		// by freeservers.com
	'*.iwarp.com',		// by freeservers.com
	'jemurl.com',
	'*.java-fan.com',	// by ulimit.com
	'kat.cc',
	'*.korzhik.org',	// by bucksogen.com
	'*.kovrizhka.org',	// by bucksogen.com
	'lame.name',
	'*.latest-info.com',// by webalias.com
	'*.learn.to',		// by webalias.com
	'linkezy.com',
	'linkook.com',
	'*.linux-fan.com',	// by ulimit.com
	'lnk.in',
	'*.mac-fan.com',	// by ulimit.com
	'makeashorterlink.com',
	'memurl.com',
	'minilien.com',		// by digipills.com
	'*.minilien.com',	// by digipills.com
	'*.mirrorz.com',	// by shorturl.com
	'mo-v.jp',
	'mooo.jp',
	'*.moviefever.com',	// by webalias.com
	'*.mp3.ms',			// by ulimit.com
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
	'nlug.org',			// by Nashville Linux Users Group
	'*.notlong.com',
	'*.official.ws',	// by active.ws
	'*.oneaddress.net',	// by vdirect.com
	'*.onlyhere.net',	// by hotredirect.com
	'*.op7.net',		// by shim.net
	'*.ouch.ws',		// by active.ws
	'*.pagehere.com',	// by hotredirect.com
	'*.paulding.net',
	'*.pirozhok.org',	// by bucksogen.com
	'*.plushka.org',	// by bucksogen.com
	'pnope.com',
	'*.premium.ws',		// by active.ws
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
	'shrinkurl.us',
	'shrunkurl.com',
	'shurl.org',
	'shurl.net',
	'simurl.com',
	'skiltechurl.com',
	'skocz.pl',
	'*.snapto.net',		// by vdirect.com
	'snipurl.com',
	'*.sp.st',			// by ulimit.com
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
	'*.zik.mu',			// by ulimit.com
	'zippedurl.com',
	'*.zonehere.com',	// by hotredirect.com


	// Sample setting of: Jacked sites (taken advantage of)
	//
	// Please notify us about this list with reason:
	// http://pukiwiki.sourceforge.jp/dev/?BugTrack2%2F208

	// 1. Web-spaces especially taken advantage of
	'*.0catch.com',
	'*.20six.nl',
	'*.9999mb.com',
	'*.alice.it',
	'*.alkablog.com'.
	'*.atfreeforum.com',
	'*.asphost4free.com',
	'bloggers.nl',
	'*.bloggers.nl',
	'*.blogspot.com',
	'*.bravenet.com',
	'*.free-25.de',
	'*.freelinuxhost.com',	// by 100webspace.com
	'groups-beta.google.com',
	'hometown.aol.com',
	'*.journalscape.com',
	'myblog.de',
	'myblog.es',
	'*.myblogvoice.com',
	'*.nm.ru',
	'*.quickfreehost.com',
	'*.sbn.bz',				// by rin.ru
	'*.squarespace.com',
	'*.t35.com',
	'*.welover.org',
	'*.weblogmaniacs.com',
	'weblogmaniacs.com',
	'*.wol.bz', // by sbn.bz (rin.ru)

	// 2. (Seems to be) Jacked contents, something implanted
	// (e.g. some sort of blog comments, BBSes, forums, wikis)
	'*.aamad.org',
	'www.blepharospasm.org',
	'*.colourware.co.uk',
	'*.iphpbb.com',
	'board-z.de',
	'*.board-z.de',
	'*.fhmcsa.org.au',
	'forum.lixium.fr',
	'funkdoc.com',
	'www.homepage-dienste.com',
	'www.macfaq.net',
	'www.me4x4.com',
	'rkphunt.com',
	'www.saskchamber.com',
	'selikoff.net',
	'www.tzaneen.co.za',


	// Sample setting of: Exclusive spam domains
	// seems to have flavor of links, pills, gamble, erotic,
	// affiliates, and/or mixed ones
	//
	// Please notify us about this list with reason:
	// http://pukiwiki.sourceforge.jp/dev/?BugTrack2/208

	// 1. Domain sets (seems to be) born to spam you
	'*.lovestoryx.com',	// by Boris (seekforweb.com, bbmfree at yahoo.com)
	'*.loveaffairx.com',// by Boris (seekforweb.com, bbmfree at yahoo.com)
	'*.onmore.info',	// by Boris (seekforweb.com, bbmfree at yahoo.com)
	'*.scfind.info',	// by Boris (seekforweb.com, bbmfree at yahoo.com)
	'*.webwork88.info',	// by Boris (seekforweb.com, bbmfree at yahoo.com)
	//
	'htewbop.org',		// by Boris (boss at bse-sofia.bg)
	'*.htewbop.org',	// by Boris (boss at bse-sofia.bg)
	'*.kimm--d.org',	// by Boris (boss at bse-sofia.bg)
	'*.gtre--h.org',	// by Boris (boss at bse-sofia.bg)
	'*.npou--k.org',	// by Boris (boss at bse-sofia.bg)
	'*.bres--z.org',	// by Boris (boss at bse-sofia.bg)
	'*.berk--p.org',	// by Boris (boss at bse-sofia.bg)
	'*.bplo--s.org',	// by Boris (boss at bse-sofia.bg)
	'*.basdpo.org',		// by Boris (boss at bse-sofia.bg)
	'*.mertnop.org',	// by Boris (boss at bse-sofia.bg)
	'vasdipv.org',		// by Boris (boss at bse-sofia.bg)
	'*.vasdipv.org',	// by Boris (boss at bse-sofia.bg)
	//
	'*.axa00.info',		// by Thai Dong Changli (pokurim at gamebox.net)
	'*.okweb11.org',	// by Thai Dong Changli (pokurim at gamebox.net)
	//
	'informazionicentro.info',	// by opezdol at gmail.com
	'*.informazionicentro.info',// by opezdol at gmail.com
	'notiziacentro.info',		// by opezdol at gmail.com
	'*.notiziacentro.info',		// by opezdol at gmail.com
	//
	'*.adult-chat-world.info',	// by CamsGen (camsgen at model-x.com)
	'*.adult-sex-chat.info',	// by CamsGen (camsgen at model-x.com)
	'*.camshost.info',			// by CamsGen (buckster at hotpop.com)
	'*.camdoors.info',			// by CamsGen (buckster at hotpop.com)
	'*.chatdoors.info',			// by CamsGen (buckster at hotpop.com)
	'*.dildo-chat.org',			// by CamsGen (camsgen at model-x.com)
	'*.live-adult-chat.org',	// by CamsGen (camsgen at model-x.com)
	'*.sexy-chat-rooms.org',	// by CamsGen (camsgen at model-x.com)
	'*.swinger-sex-chat.org',	// by CamsGen (camsgen at model-x.com)
	'*.nasty-sex-chat.org',		// by CamsGen (camsgen at model-x.com)
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
	'*.casinoqz.com',
	'*.dcasinoa.com',		// would be casinoqz.com
	//
	'*.kenogo.com',
	'*.mycaribbeanpoker.com',	// would be kenogo.com
	//
	'*.koosx.org',		// by Kikimas at mail.net, Redirect to nb717.com etc
	'*.mmgz.org',		// by Kikimas at mail.net, Redirect to nb717.com etc
	//
	'43sexx.org',		// by Andrey (vdf at lovespb.com)
	'56porn.org',		// by Andrey (vdf at lovespb.com)
	//
	'*.flywebs.com',	// by Andrey Zhurikov (zhu1313 at mail.ru)
	'*.hostrim.com',	// by Andrey Zhurikov (zhu1313 at mail.ru)
	'playbit.com',		// by Andrey Zhurikov (zhu1313 at mail.ru)

	// 2. Lonely domains (buddies not found yet)
	'*.aimoo.com',
	'anewme.org',
	'*.areaseo.com',
	'*.bsb3b.info',
	'*.buzznet.com',
	'daintyurl.com',
	'*.dgo5d.info',
	'diabetescarelink.com',
	'*.dlekei.info',
	'*.discutbb.com',
	'fingerprintmedia.com',
	'firstdebthelp.com',
	'hotscriptonline.info',
	'*.hut1.ru',
	'implex3.com',
	'italy-search.org',
	'*.italy-search.org',
	'*.infogami.com',
	'*.mujiki.com',
	'*.okweb12.org',
	'*.pahuist.info',
	'*.perevozka777.ru',
	'*.pokah.lv',
	'portaldiscount.com',
	'*.portaldiscount.com',
	'qoclick.net',
	'relurl.com',		// tiny-like
	'*.scinfo.info',
	'*.selab.org.ua',
	'sirlook.com',
	'*.soft2you.info',
	'softprof.org'
	'*.stormloader.com',
	'tops.gen.in',		// Hiding google:sites
	'vasdipv.org',
	'wapurl.co.uk',
	'*.webnow.biz',
	'wellcams.biz',
	'wwwtahoo.com',		// Typo:yahoo.com

);
?>
