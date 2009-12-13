<?php
// $Id: spam.ini.php,v 1.92 2009/12/13 14:32:27 henoheno Exp $
// Spam-related setting

// NOTE FOR ADMINISTRATORS:
//
// Host selection:
//   [1] '.example.org'  prohibits ALL "example.org"-related FQDN
//   [2] '*.example.org' prohibits ONLY subdomains and hosts, EXCEPT "www.example.org"
//   [3] 'example.org'   prohibits BOTH "example.org" and "www.example.org"
//   (Now you know, [1] = [2] + [3])
//
// How to write multiple hosts as an group:
//  'Group Name' => array('a.example.org', 'b.example.com', 'c.example.net'),
//
// How to write regular expression:
//  'Group Name' => '#^(?:.*\.)?what-you-want\.com$#',
//
// Guideline to keep group names unique:
//   - Using capitalized letters, spaces, commas (etc) may suggest you
//     that probably be a group.
//   - Unique word examples:
//     [1] FQDN
//     [2] Mail address of the domain-name owner
//     [3] IP address, if these hosts have the same ones
//     [4] Something unique idea of you
//
// Reference:
//   http://en.wikipedia.org/wiki/Spamdexing
//   http://en.wikipedia.org/wiki/Domainers
//   http://en.wikipedia.org/wiki/Typosquatting


// --------------------------------------------------
// List of the lists

//  FALSE	= ignore them
//  TRUE	= catch them
//  Commented out of the line = do nothing about it

// 'pre': Before the other filters/checkers
$blocklist['pre'] = array(
	'goodhost'	=> FALSE,
//	'official/dev'	=> FALSE,
);

// 'list': Normal list
$blocklist['list'] = array(
	'A-1'		=> TRUE,	// General redirection services
	//'A-2'		=> TRUE,	// Dynamic DNS, Dynamic IP services, ...
	'B-1'		=> TRUE,	// Web N.M spaces
	'B-2'		=> TRUE,	// Jacked contents, something implanted
	'C'			=> TRUE,	// Exclusive spam domains
	//'D'		=> TRUE,	// "Third party in good faith"s
	'E'			=> TRUE,	// Affiliates, Hypes, Catalog retailers, Multi-level marketings, ...
	'Z'			=> TRUE,	// Yours
);


// --------------------------------------------------
// Ignorance list

$blocklist['goodhost'] = array(
	'IANA-examples' => '#^(?:.*\.)?example\.(?:com|net|org)$#',
);

$blocklist['official/dev'] = array(
	// PukiWiki-official/dev specific
	'pukiwiki.sourceforge.jp',
	'.nyaa.tk',	// (Paid *.tk domain, Expire on 2008-05-19)
	'.wanwan.tk',	// (Paid *.tk domain, Expire on 2008-04-21) by nyaa.tk
	'emasaka.blog65.fc2.com',	// Text-to-Impress converter
	'ifastnet.com',				// Server hosting
	'threefortune.ifastnet.com',	// Server hosting
	'sirakaba.s21.xrea.com',		// Ratbeta, known as PukiWiki hacker
	'desperadoes.biz',			// YEAR OF THE CAT, PukiWiki skin designer
);


// --------------------------------------------------
// A: Sample setting of
// Existing URI redirection or masking services

$blocklist['A-1'] = array(

	// A-1: General redirection services -- by HTML meta, HTML frame, JavaScript,
	// web-based proxy, DNS subdomains, etc
	// http://en.wikipedia.org/wiki/URL_redirection
	//
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
	//     symy.jp
	//       "One or more users are using our URL redirect
	//        service for spam/botnet.
	//        So we closed this service."
	//     tinyclick.com
	//       "...stop offering it's free services because
	//        too many people were taking advantage of it"
	//     xjs.org
	//       "We have been forced to close this facility
	//        due to a minority of knuckle draggers who
	//        abused this web site."
	//
	// Please notify us about this list with reason:
	// http://pukiwiki.sourceforge.jp/dev/?BugTrack2/207
	//
	'0nz.org',
	'0rz.tw',
	'0url.com',
	'0zed.info',
	'*.110mb.com',	// by Speed Success, Inc. (110mb.server at gmail.com)
	'123.que.jp',
	'12url.org',
	'*.15h.com',
	'*.1dr.biz',
	'1K.pl' => array(
		'*.1k.pl',
		'*.5g.pl',
		'*.orq.pl',
	),
	'1nk.us',
	'1url.org',
	'1url.in',
	'1webspace.org',
	'2Ch.net' => array(
		'ime.nu',
		'ime.st',
	),
	'2ch2.net',
	'2hop4.com',
	'2s.ca',
	'2site.com',
	'2url.org',
	'301url.com',
	'32url.com',
	'.3dg.de',
	'*.4bb.ru',
	'big5.51job.com',	///gate/big5/
	'5jp.net',
	'.6url.com',
	'*.6x.to',
	'7ref.com',
	'82m.org',
	'*.8rf.com',
	'98.to',
	'abbrv.co.uk',
	'*.abwb.org',
	'acnw.de',
	'Active.ws' => array(
		'*.321.cn',
		'*.4x2.net',
		'active.ws',
		'*.better.ws',
		'*.here.ws',
		'*.mypiece.com',
		'*.official.ws',
		'*.ouch.ws',
		'*.premium.ws',
		'*.such.info',
		'*.true.ws',
		'*.visit.ws',
	),
	'affilitool.com',		// 125.206.117.91(right-way.org) by noboru hamada (info at isosupport.net)
	'aifam.com',
	'All4WebMasters.pl' => array(
		'*.ovp.pl',
		'*.6-6-6.pl',
	),
	'babelfish.altavista.com',	///babelfish/trurl_pagecontent
	'amoo.org',
	'web.archive.org',		///web/2
	'Arzy.net' => array(	// "(c) 2007 www.arzy.net", by urladmin at zvxr.com, DNS arzy.net
		'jmp2.net',
		'2me.tw',
	),
	'ataja.es',
	'ATBHost.com' => array(
		'*.atbhost.com',
		'*.bzhost.net',
	),
	'atk.jp',
	'clearp.ath.cx',
	'athomebiz.com',
	'aukcje1.pl',
	'beam.to',
	'*.bebo.com',
	'beermapping.com',
	'besturl.in',
	'bhomiyo.com',		///en.xliterate/ 64.209.134.9(web137.discountasp.net) by piyush at arborindia.com
	'biglnk.com',
	'bingr.com',
	'bit.ly',
	'bittyurl.com',
	'*.bizz.cc',
	'*.blo.pl',
	'*.bo.pl',
	'briefurl.com',
	'brokenscript.com',
	'BucksoGen.com' => array(
		'*.bucksogen.com',
		'*.bulochka.org',
		'*.korzhik.org',
		'*.kovrizhka.org',
		'*.pirozhok.org',
		'*.plushka.org',
		'*.pryanik.org',
		'*.sushka.org',
	),
	'budgethosts.org',
	'budu.com',				// by peter.eder at imcworld.com
	'*.buzznet.com',
	'*.bydl.com',
	'C-O.IN' => array(
		'*.c-o.cc',
		'*.c-o.in',
		'*.coz.in',
		'*.cq.bz',
	),
	'c64.ch',
	'c711.com',
	'*.cej.pl',
	'checkasite.net',
	'url.chefhost.com',
	'*.chicappa.jp',
	'chilicity.com',
	'big5.china.com',		///gate/big5/
	'chopurl.com',
	'christopherleestreet.com',
	'cintcm.com',
	'*.cjb.net',
	'clipurl.com',
	'*.co.nr',
	'Comtech Enterprises ' => array(	// comteche.com
		'tinyurl.name',
		'tinyurl.us',
	),
	'Cool168.com' => array(
		'*.cool158.com',
		'*.cool168.com',
		'*.ko168.com',
		'*.ko188.com',
	),
	'Coolurl.de' => array(
		'coolurl.de',
		'dornenboy.de',
		'eyeqweb.com',
		'hardcore-porn.de',
		'maschinen-bluten-nicht.de',
	),
	'copyme.org',		// 80.93.82.40(sd14.efedus.com) by marian at mpgsi.com
	'cutalink.com',
	'*.da.cx',
	'*.da.ru',
	'dae2.com',
	'dephine.org',
	'desiurl.com',
	'dhurl.com',
	'digbig.com',
	'Digipills.com' => array(
		'*.digipills.com',
		'minilien.com',
		'tinylink.com',
	),
	'*.discutbb.com',
	'DL.AM' => array(
		'*.cx.la',
		'*.dl.am',
	),
	'*.dl.pl',
	'*.dmdns.com',
	'doiop.com',
	'drlinky.com',
	'durl.us',
	'*.dvdonly.ru',
	'*.dynu.ca',
	'dwarf.name',
	'*.eadf.com',
	'easyURL.net' => array(
		'*.easyurl.net',
		'.goshrink.com',
		'ushrink.com',
	),
	'easyurl.jp',	// 124.38.169.39(*.ap124.ftth.ucom.ne.jp), e-mail:info at value-domain.com,
		// says "by ascentnet.co.jp". http://www.ascentnet.co.jp/press/?type=1&press=45
		// This service seems to be opened at 2007/08/23 with "beta" sign.
		// easyurl.jp clearly point ascentnet.co.jp's 10 local rules:
		//   "Keep continuing to seek originality and contribute it to local,
		//    get/grow niche brands (in local), believe (local) people knows the answer,
		//    observe (local) rule, create nothing to infringe (local) rule, keep 70% of
		//    engeneers, and ..." http://www.ascentnet.co.jp/about/about_01.html
		// I'm so much impressed of the situation around this imported one today.
	'elfurl.com',
	'eny.pl',
	'eTechFocus LLC' => array(	// by eTechFocus LLC (thomask at etechfocus.com)
		'.mywiitime.com',
		'.surfindark.com',		// webmaster at etechfocus.com
		'.surfinshade.com',
		'.surfinshadow.com',
		'.surfinwind.com',
		'.topsecretlive.com',
	),
	'*.eu.org',
	'F2B.be' => array(
		'*.f2b.be',
		'*.freakz.eu',
		'*.n0.be',
		'*.n3t.nl',
		'*.short.be',
		'*.ssr.be',
		'*.tweaker.eu',
	),
	'*.fancyurl.com',
	'Fanznet.jp' => array(	// by takahashi nakaba (nakaba.takahashi at gmail.com)
		'blue11.jp',
		'fanznet.com',
		'katou.in',
		'mymap.in',
		'saitou.in',
		'satou.in',
		'susan.in',
	),
	'.fe.pl',			// Redirection and subdomain
	'ffwd.to',
	'url.fibiger.org',
	'FireMe.to' => array(
		'fireme.to',
		'nextdoor.to',
		'ontheway.to',
	),
	'flingk.com',
	'flog.jp',			// careless redirector and bbs
	'fm7.biz',
	'fnbi.jp',
	'*.fnbi.jp',
	'forgeturl.com',
	'*.free.bg',
	'Freeservers.com' => array(	// United Online Web Services, Inc.
		'*.4mg.com',
		'*.4t.com',
		'*.8m.com',
		'*.8m.net',
		'*.8k.com',
		'*.faithweb.com',
		'*.freehosting.net',
		'*.freeservers.com',
		'*.gq.nu',
		'*.htmlplanet.com',
		'*.itgo.com',
		'*.iwarp.com',
		'*.s5.com',
		'*.scriptmania.com',
		'*.tvheaven.com',
	),
	'*.freewebpages.com',
	'FreeWebServices.net' => array(	// Host Department LLC
		'*.about.gs',	// Dead?
		'*.about.tc',
		'*.about.vg',
		'*.aboutus.gs',
		'*.aboutus.ms',
		'*.aboutus.tc',
		'*.aboutus.vg',
		'*.biografi.biz',
		'*.biografi.info',
		'*.biografi.org',
		'*.biografi.us',
		'*.datadiri.biz',
		'*.datadiri.cc',
		'*.datadiri.com',
		'*.datadiri.info',
		'*.datadiri.net',
		'*.datadiri.org',
		'*.datadiri.tv',
		'*.datadiri.us',
		'*.ecv.gs',
		'*.ecv.ms',
		'*.ecv.tc',
		'*.ecv.vg',
		'*.eprofile.us',
		'*.go2net.ws',
		'*.hits.io',
		'*.hostingweb.us',
		'*.hub.io',
		'*.indo.bz',
		'*.indo.cc',
		'*.indo.gs',
		'*.indo.ms',
		'*.indo.tc',
		'*.indo.vg',
		'*.infinitehosting.net',
		'*.infinites.net',
		'*.lan.io',
		'*.max.io',
		'*.mycv.bz',
		'*.mycv.nu',
		'*.mycv.tv',
		'*.myweb.io',
		'*.ourprofile.biz',
		'*.ourprofile.info',
		'*.ourprofile.net',	// Dead?
		'*.ourprofile.org',
		'*.ourprofile.us',
		'*.profil.bz',
		'*.profil.cc',
		'*.profil.cn',
		'*.profil.gs',
		'*.profil.in',
		'*.profil.ms',
		'*.profil.tc',
		'*.profil.tv',
		'*.profil.vg',	// ?
		'*.site.io',
		'*.wan.io',
		'*.web-cam.ws',
		'*.webs.io',
		'*.zip.io',
	),
	'funkurl.com',		// by Leonard Lyle (len at ballandchain.net)
	'*.fx.to',
	'fyad.org',
	'fype.com',
	'gentleurl.net',
	'Get2.us' => array(
		'*.get2.us',
		'*.hasballs.com',
		'*.ismyidol.com',
		'*.spotted.us',
		'*.went2.us',
		'*.wentto.us',
	),
	'glinki.com',
	'*.globalredirect.com',
	'gnu.vu',
	'*.go.cc',
	//'Google.com' => array(
	//		google.com/translate_c\?u=(?:http://)?
	//),
	'goonlink.com',
	'.gourl.org',
	'.greatitem.com',
	'gzurl.com',
	'url.grillsportverein.de',
	'Harudake.net' => array('*.hyu.jp'),
	'Hattinger Linux User Group' => array('short.hatlug.de'),
	'Hexten.net' => array('lyxus.net'),
	'here.is',
	'HispaVista.com' => array(
		'*.blogdiario.com',
		'*.hispavista.com',
		'.galeon.com',
	),
	'Home.pl' => array(	// by Home.pl Sp. J. (info at home.pl), redirections and forums
		'*.8l.pl',
		'*.blg.pl',
		'*.czytajto.pl',
		'*.ryj.pl',
		'*.xit.pl',
		'*.xlc.pl',
		'*.hk.pl',
		'*.home.pl',
		'*.of.pl',
	),
	'hort.net',
	'free4.hostrocket.com',
	'*.hotindex.ru',
	'HotRedirect.com' => array(
		'*.coolhere.com',
		'*.homepagehere.com',
		'*.hothere.com',
		'*.mustbehere.com',
		'*.onlyhere.net',
		'*.pagehere.com',
		'*.surfhere.net',
		'*.zonehere.com',
	),
	'hotshorturl.com',
	'hotwebcomics.com',	///search_redirect.php
	'hurl.to',
	'*.hux.de',
	'*.i89.us',
	'iat.net',			// 74.208.58.130 by Tony Carter
	'ibm.com',			///links (Correct it)
	'*.iceglow.com',
	'internetadresi.com' => array (
		'rxbuycheap.com',
		'rxcheapwestern.com',
	),
	'go.id-tv.info',	// 77.232.68.138(77-232-68-138.static.servage.net) by Max Million (max at id-tv.info)
	'Ideas para Nuevos Mercados SL' => array(
		// NOTE: 'i4nm.com' by 'Ideas para Nuevos Mercados SL' (i4nm at i4nm.com)
		// NOTE: 'dominiosfree.com' by 'Ideas para nuevos mercados,sl' (dominiosfree at i4nm.com)
		// NOTE: 'red-es.com' by oscar florez (info at i4nm.com)
		// by edgar bortolin (oscar at i4nm.com)
		// by Edgar Bortolin  (oscar at i4nm.com)
		// by oscar florez (oscar at i4nm.com)
		// by Oscar Florez (oscar at red-es.com)
		// by covadonga del valle (oscar at i4nm.com)
		'*.ar.gd',
		'*.ar.gs',	// ns *.nora.net
		'*.ar.kz',	// by oscar
		'*.ar.nu',	// by Edgar
		'*.ar.tc',	// by oscar
		'*.ar.vg',	// by oscar
		'*.bo.kz',	// by oscar
		'*.bo.nu',	// by covadonga
		'*.bo.tc',	// by oscar
		'*.bo.tf',	// by Oscar
		'*.bo.vg',	// by oscar
		'*.br.gd',
		'*.br.gs',	// ns *.nora.net
		'*.br.nu',	// by edgar
		'*.br.vg',	// by oscar
		'*.ca.gs',	// by oscar
		'*.ca.kz',	// by oscar
		'*.cl.gd',	// by oscar
		'*.cl.kz',	// by oscar
		'*.cl.nu',	// by edgar
		'*.cl.tc',	// by oscar
		'*.cl.tf',	// by Oscar
		'*.cl.vg',	// by oscar
		'*.col.nu',	// by Edgar
		'*.cr.gs',	// ns *.nora.net
		'*.cr.kz',	// by oscar
		'*.cr.nu',	// by edgar
		'*.cr.tc',	// by oscar
		'*.cu.tc',	// by oscar
		'*.do.kz',	// by oscar
		'*.do.nu',	// by edgar
		'*.ec.kz',	// by edgar
		'*.ec.nu',	// by Edgar
		'*.ec.tf',	// by Oscar
		'*.es.kz',	// by oscar
		'*.eu.kz',	// by oscar
		'*.gt.gs',	// ns *.nora.net
		'*.gt.tc',	// by oscar
		'*.gt.tf',	// by Oscar
		'*.gt.vg',	// by Oscar
		'*.hn.gs',	// ns *.nora.net
		'*.hn.tc',	// by oscar
		'*.hn.tf',	// by Oscar
		'*.hn.vg',	// by oscar
		'*.mx.gd',
		'*.mx.gs',	// ns *.nora.net
		'*.mx.kz',	// by oscar
		'*.mx.vg',	// by oscar
		'*.ni.kz',	// by oscar
		'*.pa.kz',	// by oscar
		'*.pe.kz',	// by oscar
		'*.pe.nu',	// by Edgar
		'*.pr.kz',	// by oscar
		'*.pr.nu',	// by edgar
		'*.pt.gs',	// ns *.nora.net
		'*.pt.kz',	// by edgar
		'*.pt.nu',	// by edgar
		'*.pt.tc',	// by oscar
		'*.pt.tf',	// by Oscar
		'*.py.gs',	// ns *.nora.net
		'*.py.nu',	// by edgar
		'*.py.tc',	// by oscar
		'*.py.tf',	// by Oscar
		'*.py.vg',	// by oscar
		'*.sv.tc',	// by oscar
		'*.usa.gs',	// ns *.nora.net
		'*.uy.gs',	// ns *.nora.net
		'*.uy.kz',	// by oscar
		'*.uy.nu',	// by edgar
		'*.uy.tc',	// by oscar
		'*.uy.tf',	// by Oscar
		'*.uy.vg',	// by oscar
		'*.ve.gs',	// by oscar
		'*.ve.tc',	// by oscar
		'*.ve.tf',	// by Oscar
		'*.ve.vg',	// by oscar
		'*.ven.nu',	// by edgar
	),
	'ie.to',
	'igoto.co.uk',
	'ilook.tw',
	'indianpad.com',		///view/
	'iNetwork.co.il' => array(
		'inetwork.co.il',	// by NiL HeMo (exe at bezeqint.net)
		'.up2.co.il',		// inetwork.co.il related, not classifiable, by roey blumshtein (roeyb76 at 017.net.il)
		'.dcn.co.il,',		// up2.co.il related, not classifiable, by daniel chechik (ns_daniel0 at bezeqint.net)
	),
	'*.infogami.com',
	'infotop.jp',
	'ipoo.org',
	'IR.pl' => array(
		'*.aj.pl',
		'*.aliasy.org',
		'*.gu.pl',
		'*.hu.pl',
		'*.ir.pl',
		'*.jo.pl',
		'*.su.pl',
		'*.td.pl',
		'*.uk.pl',
		'*.uy.pl',
		'*.xa.pl',
		'*.zj.pl',
	),
	'irotator.com',
	'.iwebtool.com',
	'j6.bz',
	'jeeee.net',
	'Jaze Redirect Services' => array(
		'*.arecool.net',
		'*.iscool.net',
		'*.isfun.net',
		'*.tux.nu',
	),
	'*.jed.pl',
	'JeremyJohnstone.com' => array('url.vg'),
	'jemurl.com',
	'jggj.net',
	'jpan.jp',
	'josh.nu',
	'kat.cc',
	'Kickme.to' => array(
		'.1024bit.at',
		'.128bit.at',
		'.16bit.at',
		'.256bit.at',
		'.32bit.at',
		'.512bit.at',
		'.64bit.at',
		'.8bit.at',
		'.adores.it',
		'.again.at',
		'.allday.at',
		'.alone.at',
		'.altair.at',
		'.american.at',
		'.amiga500.at',
		'.ammo.at',
		'.amplifier.at',
		'.amstrad.at',
		'.anglican.at',
		'.angry.at',
		'.around.at',
		'.arrange.at',
		'.australian.at',
		'.baptist.at',
		'.basque.at',
		'.battle.at',
		'.bazooka.at',
		'.berber.at',
		'.blackhole.at',
		'.booze.at',
		'.bosnian.at',
		'.brainiac.at',
		'.brazilian.at',
		'.bummer.at',
		'.burn.at',
		'.c-64.at',
		'.catalonian.at',
		'.catholic.at',
		'.chapel.at',
		'.chills.it',
		'.christiandemocrats.at',
		'.cname.at',
		'.colors.at',
		'.commodore.at',
		'.commodore64.at',
		'.communists.at',
		'.conservatives.at',
		'.conspiracy.at',
		'.cooldude.at',
		'.craves.it',
		'.croatian.at',
		'.cuteboy.at',
		'.dancemix.at',
		'.danceparty.at',
		'.dances.it',
		'.danish.at',
		'.dealing.at',
		'.deep.at',
		'.democrats.at',
		'.digs.it',
		'.divxlinks.at',
		'.divxmovies.at',
		'.divxstuff.at',
		'.dizzy.at',
		'.does.it',
		'.dork.at',
		'.drives.it',
		'.dutch.at',
		'.dvdlinks.at',
		'.dvdmovies.at',
		'.dvdstuff.at',
		'.emulators.at',
		'.end.at',
		'.english.at',
		'.eniac.at',
		'.error403.at',
		'.error404.at',
		'.evangelism.at',
		'.exhibitionist.at',
		'.faith.at',
		'.fight.at',
		'.finish.at',
		'.finnish.at',
		'.forward.at',
		'.freebie.at',
		'.freemp3.at',
		'.french.at',
		'.graduatejobs.at',
		'.greenparty.at',
		'.grunge.at',
		'.hacked.at',
		'.hang.at',
		'.hangup.at',
		'.has.it',
		'.hide.at',
		'.hindu.at',
		'.htmlpage.at',
		'.hungarian.at',
		'.icelandic.at',
		'.independents.at',
		'.invisible.at',
		'.is-chillin.it',
		'.is-groovin.it',
		'.japanese.at',
		'.jive.at',
		'.kickass.at',
		'.kickme.to',
		'.kindergarden.at',
		'.knows.it',
		'.kurd.at',
		'.labour.at',
		'.leech.at',
		'.liberals.at',
		'.linuxserver.at',
		'.liqour.at',
		'.lovez.it',
		'.makes.it',
		'.maxed.at',
		'.means.it',
		'.meltdown.at',
		'.methodist.at',
		'.microcomputers.at',
		'.mingle.at',
		'.mirror.at',
		'.moan.at',
		'.mormons.at',
		'.musicmix.at',
		'.nationalists.at',
		'.needz.it',
		'.nerds.at',
		'.neuromancer.at',
		'.newbie.at',
		'.nicepage.at',
		'.ninja.at',
		'.norwegian.at',
		'.ntserver.at',
		'.owns.it',
		'.paint.at',
		'.palestinian.at',
		'.phoneme.at',
		'.phreaking.at',
		'.playz.it',
		'.polish.at',
		'.popmusic.at',
		'.portuguese.at',
		'.powermac.at',
		'.processor.at',
		'.prospects.at',
		'.protestant.at',
		'.rapmusic.at',
		'.raveparty.at',
		'.reachme.at',
		'.reads.it',
		'.reboot.at',
		'.relaxed.at',
		'.republicans.at',
		'.researcher.at',
		'.reset.at',
		'.resolve.at',
		'.retrocomputers.at',
		'.rockparty.at',
		'.rocks.it',
		'.rollover.at',
		'.rough.at',
		'.rules.it',
		'.rumble.at',
		'.russian.at',
		'.says.it',
		'.scared.at',
		'.seikh.at',
		'.serbian.at',
		'.short.as',
		'.shows.it',
		'.silence.at',
		'.simpler.at',
		'.sinclair.at',
		'.singz.it',
		'.slowdown.at',
		'.socialists.at',
		'.spanish.at',
		'.split.at',
		'.stand.at',
		'.stoned.at',
		'.stumble.at',
		'.supercomputer.at',
		'.surfs.it',
		'.swedish.at',
		'.swims.it',
		'.synagogue.at',
		'.syntax.at',
		'.syntaxerror.at',
		'.techie.at',
		'.temple.at',
		'.thinkbig.at',
		'.thirsty.at',
		'.throw.at',
		'.toplist.at',
		'.trekkie.at',
		'.trouble.at',
		'.turkish.at',
		'.unexplained.at',
		'.unixserver.at',
		'.vegetarian.at',
		'.venture.at',
		'.verycool.at',
		'.vic-20.at',
		'.viewing.at',
		'.vintagecomputers.at',
		'.virii.at',
		'.vodka.at',
		'.wannabe.at',
		'.webpagedesign.at',
		'.wheels.at',
		'.whisper.at',
		'.whiz.at',
		'.wonderful.at',
		'.zor.org',
		'.zx80.at',
		'.zx81.at',
		'.zxspectrum.at',
	),
	'kisaweb.com',
	'krotki.pl',
	'kuerzer.de',
	'*.kupisz.pl',
	'kuso.cc',
	'*.l8t.com',
	'lame.name',
	'lediga.st',
	'liencourt.com',
	'liteurl.com',
	'linkachi.com',
	'linkezy.com',
	'linkfrog.net',
	'linkook.com',
	'linkyme.com',
	'linkzip.net',
	'lispurl.com',
	'lnk.in',
	'makeashorterlink.com',
	'MAX.ST' => array(	// by Guet Olivier (oliguet at club-internet.fr), frame
		'*.3gp.fr',
		'*.gtx.fr',
		'*.ici.st',
		'*.max.st',
		'*.nn.cx',		// ns *.sivit.org
		'*.site.cx',	// ns *.sivit.org
		'*.user.fr',
		'*.zxr.fr',
	),
	'mcturl.com',
	'memurl.com',
	'Metamark.net' => array('xrl.us'),
	'midgeturl.com',
	'Minilink.org' => array('lnk.nu'),
	'miniurl.org',
	'miniurl.pl',
	'mixi.bz',
	'mo-v.jp',
	'MoldData.md' => array(	// Note: Some parts of '.md' ccTLD
		'.com.md',
		'.co.md',
		'.org.md',
		'.info.md',
		'.host.md',
	),
	'monster-submit.com',
	'mooo.jp',
	'murl.net',
	'myactivesurf.net',
	'mytinylink.com',
	'myurl.in',
	'myurl.com.tw',
	'nanoref.com',
	'Ne1.net' => array(
		'*.ne1.net',
		'*.r8.org',
	),
	'Nashville Linux Users Group' => array('nlug.org'),
	'not2long.net',
	'*.notlong.com',
	'*.nuv.pl',
	'ofzo.be',
	'*.ontheinter.net',
	'ourl.org',
	'ov2.net',				// frame
	'*.ozonez.com',
	'pagebang.com',
	'palurl.com',
	'*.paulding.net',
	'phpfaber.org',
	'pygmyurl.com',
	'pnope.com',
	'prettylink.com',
	'PROXID.net' => array(	// also xRelay.net
		'*.asso.ws',
		'*.corp.st',
		'*.euro.tm',
		'*.perso.tc',
		'*.site.tc',
		'*.societe.st',
	),
	'qqa.jp',
	'qrl.jp',
	'qurl.net',
	'qwer.org',
	'readthisurl.com',		// 67.15.58.36(win2k3.tuserver.com) by Zhe Hong Lim (zhehonglim at gmail.com)
	'radiobase.net',
	'Rakuten.co.jp' => array(
		'pt.afl.rakuten.co.jp',	///c/
	),
	'RedirectFree.com' => array(
		'*.red.tc',
		'*.redirectfree.com',
		'*.sky.tc',
		'*.the.vg',
	),
	'redirme.com',
	'redirectme.to',
	'relic.net',
	'rezma.info',
	'rio.st',
	'rlink.org',
	'*.rmcinfo.fr',
	'roo.to',		// Seems closed, says "bye-bye"
	'rubyurl.com',
	'*.runboard.com',
	'runurl.com',
	's-url.net',
	's1u.net',
	'SG5.co.uk' => array(
		'*.sg5.co.uk',
		'*.sg5.info',
	),
	'Shim.net' => array(
		'*.0kn.com',
		'*.2cd.net',
		'*.freebiefinders.net',
		'*.freegaming.org',
		'*.op7.net',
		'*.shim.net',
		'*.v9z.com',
	),
	'big5.shippingchina.com',
	'shorl.com',
	'shortenurl.com',
	'shorterlink.com',
	'shortlinks.co.uk',
	'shorttext.com',
	'shorturl-accessanalyzer.com',
	'Shortify.com' => array(
		'74678439.com',
		'shortify.com',
	),
	'shortlink.co.uk',
	'ShortURL.com' => array(
		'*.1sta.com',
		'*.24ex.com',
		'*.2fear.com',
		'*.2fortune.com',
		'*.2freedom.com',
		'*.2hell.com',
		'*.2savvy.com',
		'*.2truth.com',
		'*.2tunes.com',
		'*.2ya.com',
		'*.alturl.com',
		'*.antiblog.com',
		'*.bigbig.com',
		'*.dealtap.com',
		'*.ebored.com',
		'*.echoz.com',
		'*.filetap.com',
		'*.funurl.com',
		'*.headplug.com',
		'*.hereweb.com',
		'*.hitart.com',
		'*.mirrorz.com',
		'*.shorturl.com',
		'*.spyw.com',
		'*.vze.com',
	),
	'shrinkalink.com',
	'shrinkthatlink.com',
	'shrinkurl.us',
	'shrt.org',
	'shrunkurl.com',
	'shurl.org',
	'shurl.net',
	'sid.to',
	'simurl.com',
	'sitefwd.com',
	'Sitelutions.com' => array(
		'*.assexy.as',
		'*.athersite.com',
		'*.athissite.com',
		'*.bestdeals.at',
		'*.byinter.net',
		'*.findhere.org',
		'*.fw.nu',
		'*.isgre.at',
		'*.isthebe.st',
		'*.kwik.to',
		'*.lookin.at',
		'*.lowestprices.at',
		'*.onthenet.as',
		'*.ontheweb.nu',
		'*.pass.as',
		'*.passingg.as',
		'*.redirect.hm',
		'*.rr.nu',
		'*.ugly.as',
	),
	'*.skracaj.pl',
	'skiltechurl.com',
	'skocz.pl',
	'slimurl.jp',
	'slink.in',
	'smallurl.eu',
	'smurl.name',
	'snipurl.com',
	'sp-nov.net',
	'splashblog.com',
	'spod.cx',
	'*.spydar.com',
	'Subdomain.gr' => array(
		'*.p2p.gr',
		'*.subdomain.gr',
	),
	'SURL.DK' => array('surl.dk'),	// main page is: s-url.dk
	'surl.se',
	'surl.ws',
	'tdurl.com',
	'tighturl.com',
	'tiniuri.com',
	'tiny.cc',
	'tiny.pl',
	'tiny2go.com',
	'tinylink.eu',
	'tinylinkworld.com',
	'tinypic.com',
	'tinyr.us',
	'TinyURL.com' => array(
		'tinyurl.com',
		'preview.tinyurl.com',
		'tinyurl.co.uk',
	),
	'titlien.com',
	'*.tlg.pl',
	'tlurl.com',
	'link.toolbot.com',
	'tnij.org',
	'Tokelau ccTLD' => array('.tk'),
	'toila.net',
	'*.toolbot.com',
	'*.torontonian.com',
	'trimurl.com',
	//'ttu.cc',		// Seems closed
	'turl.jp',
	'*.tz4.com',
	'U.TO' => array(	// ns *.1004web.com, 1004web.com is owned by Moon Jae Bark (utomaster at gmail.com) = u.to master
		'*.1.to',
		'*.4.to',
		'*.5.to',
		'*.82.to',
		'*.s.to',
		'*.u.to',
		'*.ce.to',
		'*.cz.to',
		'*.if.to',
		'*.it.to',
		'*.kp.to',
		'*.ne.to',
		'*.ok.to',
		'*.pc.to',
		'*.tv.to',
		'*.dd.to',
		'*.ee.to',
		'*.hh.to',
		'*.kk.to',
		'*.mm.to',
		'*.qq.to',
		'*.xx.to',
		'*.zz.to',
		'*.ivy.to',
		'*.joa.to',
		'*.ever.to',
		'*.mini.to',
	),
	'u-go.to',
	'uchinoko.in',
	'Ulimit.com' => array(
		'*.be.tf',
		'*.best.cd',
		'*.bsd-fan.com',
		'*.c0m.st',
		'*.ca.tc',
		'*.clan.st',
		'*.com02.com',
		'*.en.st',
		'*.euro.st',
		'*.fr.fm',
		'*.fr.st',
		'*.fr.vu',
		'*.gr.st',
		'*.ht.st',
		'*.int.ms',
		'*.it.st',
		'*.java-fan.com',
		'*.linux-fan.com',
		'*.mac-fan.com',
		'*.mp3.ms',
		'*.qc.tc',
		'*.sp.st',
		'*.suisse.st',
		'*.t2u.com',
		'*.unixlover.com',
		'*.zik.mu',
	),
	'urltea.com',
	'*.uni.cc',
	'UNONIC.com' => array(
		'*.at.tf',	// AlpenNIC
		'*.bg.tf',
		'*.ca.tf',
		'*.ch.tf',	// AlpenNIC
		'*.cz.tf',
		'*.de.tf',	// AlpenNIC
		'*.edu.tf',
		'*.eu.tf',
		'*.int.tf',
		'*.net.tf',
		'*.pl.tf',
		'*.ru.tf',
		'*.sg.tf',
		'*.us.tf',
	),
	'Up.pl' => array(
		'.69.pl',			// by nsk101869
		'.crack.pl',		// by nsk101869
		'.film.pl',			// by sibr19002
		'.h2o.pl',			// by nsk101869
		'.hostessy.pl',		// by nsk101869
		'.komis.pl',		// by nsk101869
		'.laski.pl',		// by nsk101869
		'.modelki.pl',		// by nsk101869
		'.muzyka.pl',		// by sibr19002
		'.nastolatki.pl',	// by nsk101869
		'.obuwie.pl',		// by nsk101869
		'.prezes.com',		// by Robert e (b2b at interia.pl)
		'.prokuratura.com',	// by Robert Tofil (b2b at interia.pl)
		'.sexchat.pl',		// by nsk101869
		'.sexlive.pl',		// by nsk101869
		'.tv.pl',			// by nsk101869
		'.up.pl',			// by nsk101869
		'.video.pl',		// by nsk101869
		'.xp.pl',			// nsk101869
	),
	'*.uploadr.com',
	'url.ie',
	'url4.net',
	'url-c.com',
	'urlbee.com',
	'urlbounce.com',
	'urlcut.com',
	'urlcutter.com',
	'urlic.com',
	'urlin.it',
	'urlkick.com',
	'URLLogs.com' => array(
		'*.urllogs.com',	// 67.15.219.253 by Javier Keeth (abuzant at gmail.com), ns *.pengs.com, 'Hosted by: Gossimer'
		'.12w.net',			// 67.15.219.253 by Marvin Dreyer (marvin.dreyer at pengs.com), ns *.gossimer.com
	),
	'*.urlproxy.com',
	'urlser.com',
	'urlsnip.com',
	'urlzip.de',
	'urlx.org',
	'useurl.us',		// by Edward Beauchamp (mail at ebvk.com)
	'utun.jp',
	'uxxy.com',
	'*.v27.net',
	'V3.com by FortuneCity.com' => array(	// http://www.v3.com/sub-domain-list.shtml
		'*.all.at',
		'*.back.to',
		'*.beam.at',
		'*.been.at',
		'*.bite.to',
		'*.board.to',
		'*.bounce.to',
		'*.bowl.to',
		'*.break.at',
		'*.browse.to',
		'*.change.to',
		'*.chip.ms',
		'*.connect.to',
		'*.crash.to',
		'*.cut.by',
		'*.direct.at',
		'*.dive.to',
		'*.drink.to',
		'*.drive.to',
		'*.drop.to',
		'*.easy.to',
		'*.everything.at',
		'*.fade.to',
		'*.fanclub.ms',
		'*.firstpage.de',
		'*.fly.to',
		'*.flying.to',
		'*.fortunecity.co.uk',
		'*.fortunecity.com',
		'*.forward.to',
		'*.fullspeed.to',
		'*.fun.ms',
		'*.gameday.de',
		'*.germany.ms',
		'*.get.to',
		'*.getit.at',
		'*.hard-ware.de',
		'*.hello.to',
		'*.hey.to',
		'*.hop.to',
		'*.how.to',
		'*.hp.ms',
		'*.jump.to',
		'*.kiss.to',
		'*.listen.to',
		'*.mediasite.de',
		'*.megapage.de',
		'*.messages.to',
		'*.mine.at',
		'*.more.at',
		'*.more.by',
		'*.move.to',
		'*.musicpage.de',
		'*.mypage.org',
		'*.mysite.de',
		'*.nav.to',
		'*.notrix.at',
		'*.notrix.ch',
		'*.notrix.de',
		'*.notrix.net',
		'*.on.to',
		'*.page.to',
		'*.pagina.de',
		'*.played.by',
		'*.playsite.de',
		'*.privat.ms',
		'*.quickly.to',
		'*.redirect.to',
		'*.rulestheweb.com',
		'*.run.to',
		'*.scroll.to',
		'*.seite.ms',
		'*.shortcut.to',
		'*.skip.to snap.to',
		'*.soft-ware.de',
		'*.start.at',
		'*.stick.by',
		'*.surf.to',
		'*.switch.to',
		'*.talk.to',
		'*.tip.nu',
		'*.top.ms',
		'*.transfer.to',
		'*.travel.to',
		'*.turn.to',
		'*.vacations.to',
		'*.videopage.de',
		'*.virtualpage.de',
		'*.w3.to',
		'*.walk.to',
		'*.warp9.to',
		'*.window.to',
		'*.yours.at',
		'*.zap.to',
		'*.zip.to',
	),
	'VDirect.com' => array(
		'*.emailme.net',
		'*.getto.net',
		'*.inetgames.com',
		'*.netbounce.com',
		'*.netbounce.net',
		'*.oneaddress.net',
		'*.snapto.net',
		'*.vdirect.com',
		'*.vdirect.net',
		'*.webrally.net',
	),
	'venturenetworking.com',	// by Katharine Barbieri (domains at spyforce.com)
	'vgo2.com',
	'Voila.fr' => array('r.voila.fr'),	// Fix it
	'w3t.org',
	'wapurl.co.uk',
	'Wb.st' => array(
		'*.team.st',
		'*.wb.st',
	),
	'wbkt.net',
	'WebAlias.com' => array(
		'*.andmuchmore.com',
		'*.browser.to',
		'*.escape.to',
		'*.fornovices.com',
		'*.fun.to',
		'*.got.to',
		'*.hottestpix.com',
		'*.imegastores.com',
		'*.latest-info.com',
		'*.learn.to',
		'*.moviefever.com',
		'*.mp3-archives.com',
		'*.myprivateidaho.com',
		'*.radpages.com',
		'*.remember.to',
		'*.resourcez.com',
		'*.return.to',
		'*.sail.to',
		'*.sports-reports.com',
		'*.stop.to',
		'*.thrill.to',
		'*.tophonors.com',
		'*.uncutuncensored.com',
		'*.up.to',
		'*.veryweird.com',
		'*.way.to',
		'*.web-freebies.com',
		'.webalias.com',
		'*.webdare.com',
		'*.xxx-posed.com',
	),
	'webmasterwise.com',
	'witherst at hotmail.com' => array(	// by Tim Withers
		'*.associates-program.com',
		'*.casinogopher.com',
		'*.ezpagez.com',
		'*.vgfaqs.com',
	),
	'wittylink.com',
	'wiz.sc',			// tiny.cc related
	'X50.us' => array(
		'*.i50.de',
		'*.x50.us',
	),
	'big5.xinhuanet.com',	///gate/big5/
	'xhref.com',
	'Xn6.net' => array(
		'*.9ax.net',
		'*.xn6.net',
	),
	'*.xshorturl.com',		// by Markus Lee (soul_s at list.ru) 
	'.y11.net',
	'YESNS.com' => array(	// by Jae-Hwan Kwon (kwonjhpd at kornet.net)
		'*.yesns.com',
		'*.srv4u.net',
		//blogne.com
	),
	'yatuc.com',
	'yep.it',
	'yumlum.com',
	'yurel.com',
	'Z.la' => array(
		'z.la',
		't.z.la',
	),
	'zaable.com',
	'zapurl.com',
	'zarr.co.uk',
	'zerourl.com',
	'ZeroWeb.org' => array(
		'*.80t.com',
		'*.firez.org',
		'*.fizz.nu',
		'*.ingame.org',
		'*.irio.net',
		'*.v33.org',
		'*.zeroweb.org',
	),
	'zhukcity.ru',
	'zippedurl.com',
	'zr5.us',
	'*.zs.pl',
	'*.zu5.net',
	'zuso.tw',
	'*.zwap.to',
);

// --------------------------------------------------

$blocklist['A-2'] = array(

	// A-2: Dynamic DNS, Dynamic IP services, DNS vulnerabilities, or another DNS cases
	//
	//'*.dyndns.*',	// Wildcard for dyndns
	//
	'*.ddo.jp',				// by Kiyoshi Furukawa (furu at furu.jp)
	'ddns.ru' => array('*.bpa.nu'),
	'Dhs.org' => array(
		'*.2y.net',
		'*.dhs.org',
	),
	'*.dnip.net',
	'*.dyndns.co.za',
	'*.dyndns.dk',
	'*.dyndns.nemox.net',
	'DyDNS.com' => array(
		'*.ath.cx',
		'*.dnsalias.org',
		'*.dyndns.org',
		'*.homeip.net',
		'*.homelinux.net',
		'*.mine.nu',
		'*.shacknet.nu',
	),
	'*.dtdns.net',			// by jscott at sceiron.com
	'*.dynu.com',
	'*.dynup.net',
	'*.fdns.net',
	'J-Speed.net' => array(
		'*.bne.jp',
		'*.ii2.cc',
		'*.jdyn.cc',
		'*.jspeed.jp',
	),
	'*.mydyn.de',
	'*.nerdcamp.net',
	'No-IP.com' => array(
			'*.bounceme.net',
			'*.hopto.org',
			'*.myftp.biz',
			'*.myftp.org',
			'*.myvnc.com',
			'*.no-ip.biz',
			'*.no-ip.info',
			'*.no-ip.org',
			'*.redirectme.net',
			'*.servebeer.com',
			'*.serveblog.net',
			'*.servecounterstrike.com',
			'*.serveftp.com',
			'*.servegame.com',
			'*.servehalflife.com',
			'*.servehttp.com',
			'*.servemp3.com',
			'*.servepics.com',
			'*.servequake.com',
			'*.sytes.net',
			'*.zapto.org',
	),
	'*.opendns.be',
	'Yi.org' => array(	// by dns at whyi.org
		'*.yi.org',		// 64.15.155.86(susicivus.crackerjack.net)

		// 72.55.129.46(redirect.yi.org)
		'*.whyi.org',
		'*.weedns.com',
	),
	'*.zenno.info',
	'.cm',	// 'Cameroon' ccTLD, sometimes used as typo of '.com',
			// and all non-recorded domains redirect to 'agoga.com' now
			// http://money.cnn.com/magazines/business2/business2_archive/2007/06/01/100050989/index.htm
			// http://agoga.com/aboutus.html
);

// --------------------------------------------------

// B: Sample setting of:
// Jacked (taken advantage of) and cleaning-less sites
//
// Please notify us about this list with reason:
// http://pukiwiki.sourceforge.jp/dev/?BugTrack2%2F208

$blocklist['B-1'] = array(

	// B-1: Web N.M spaces (N > 0, M >= 0)
	//
	//   Messages from forerunners:
	//     activefreehost.com
	//       "We regret to inform you that ActiveFreeHost
	//        free hosting service has is now closed (as of
	//        September 18). We have been online for over
	//        two and half years, but have recently decided
	//        to take time for software improvement to fight
	//        with server abuse, Spam advertisement and
	//        fraud."
	//
	'*.0000host.com',		// 68.178.200.154, ns *.3-hosting.net
	'*.007ihost.com',		// 195.242.99.199(s199.softwarelibre.nl)
	'*.007webpro.com',		// by richord at ientry.com
	'*.00bp.com',			// 74.86.20.224(layeredpanel.com -> 195.242.99.195) by admin at 1kay.com
	'*.0buckhost.com',		// by tycho at e-lab.nl
	'0Catch.com related' => array(
		'*.0catch.com',		// 209.63.57.4 by Sam Parkinson (sam at 0catch.com), also zerocatch.com

		// 209.63.57.10(www1.0catch.com) by owner at 100megswebhosting.com, ns *.0catch.com
		'*.100megsfree5.com',

		// 209.63.57.10(*snip*) by dan at 0catch.com, ns *.0catch.com
		'*.100freemb.com',		// by Danny Ashworth
		'*.easyfreehosting.com',
		'*.exactpages.com',
		'*.fcpages.com',
		'*.wtcsites.com',

		// 209.63.57.10(*snip*) by domains at netgears.com, ns *.0catch.com
		'*.741.com',
		'*.freecities.com',
		'*.freesite.org',
		'*.freewebpages.org',
		'*.freewebsitehosting.com',
		'*.jvl.com',

		// 209.63.57.10(*snip*) by luke at dcpages.com, ns *.0catch.com
		'*.freespaceusa.com',
		'*.usafreespace.com',

		// 209.63.57.10(*snip*) by rickybrown at usa.com, ns *.0catch.com
		'*.dex1.com',
		'*.questh.com',

		// 209.63.57.10(*snip*), ns *.0catch.com
		'*.00freehost.com',		// by David Mccall (superjeeves at yahoo.com)
		'*.012webpages.com',	// by support at 0catch.com
		'*.150m.com',
		'*.1sweethost.com',		// by whois at bluehost.com
		'*.250m.com',			// by jason at fahlman.net
		'*.9cy.com',			// by paulw0t at gmail.com
		'*.angelcities.com',	// by cliff at eccentrix.com
		'*.arcadepages.com',	// by admin at site-see.com
		'*.e-host.ws',			// by dns at jomax.net
		'*.envy.nu',			// by Dave Ellis (dave at larryblackandassoc.com)
		'*.fw.bz',				// by ben at kuehl.as
		'*.freewebportal.com',	// by mmouneeb at hotmail.com
		'*.g0g.net',			// by domains at seem.co.uk
		'*.galaxy99.net',		// by admin at bagchi.org
		'*.greatnow.com',		// by peo at peakspace.com
		'*.hautlynx.com',		// by hlewis28 at juno.com
		'*.ibnsites.com',		// by cmrojas at mail.com
		'*.just-allen.com',		// by extremehype at msn.com
		'*.kogaryu.com',		// by angelguerra at msn.com
		'*.maddsites.com',		// by big.tone at maddhattentertainment.com
		'*.mindnmagick.com',	// by tim at mind-n-magick.com
		'*.s4u.org',			// by sung_wei_wang at yahoo.com
		'*.s-enterprize.com',
		'*.servetown.com',		// by jonahbliss at earthlink.net
		'*.stinkdot.org',		// by bluedot at ziplip.com
		'*.virtue.nu',			// by dave at larryblackandassoc.com
		'*.zomi.net',			// by sianpu at gmail.com
	),
	'*.1asphost.com',		// 216.55.133.18(216-55-133-18.dedicated.abac.net) by domains at dotster.com
	'100 Best Inc' => array(
		// by 100 Best Inc (info at 100best.com)

		// 66.235.204.7(h204-007.bluefishhosting.com)
		'*.2-hi.com',
		'*.20fr.com',
		'*.20ii.com',
		'*.20is.com',
		'*.20it.com',
		'*.20m.com',	// by jlee at 100bestinc.com
		'*.20to.com',
		'*.2u-2.com',
		'*.3-st.com',
		'*.fws1.com',
		'*.fw-2.com',
		'*.inc5.com',
		'*.on-4.com',
		'*.st-3.com',
		'*.st20.com',
		'*.up-a.com',

		// 216.40.33.252(www-pd.mdnsservice.com)
		'*.0-st.com',
		'*.20me.com',
	),
	'*.100foros.com',
	'*.101freehost.com',
	'*.12gbfree.com',	// 75.126.176.194 by ashphama at yahoo.com
	'20six Weblog Services' => array(
		'.20six.nl',			// by 20six weblog services (postmaster at 20six.nl)
		'.20six.co.uk',
		'.20six.fr',
		'myblog.de',
		'myblog.es',
	),
	'*.250free.com',	// by Brian Salisbury (domains at 250host.com)
	'*.275mb.com',		// 204.15.10.144 by domains at febox.com
	'2IP.com' => array(
		// 205.209.97.53(non-existent) by Host Department LLC (registrar at hostdepartment.com)
		'*.2ip.jp',
		'*.474.jp',
		'*.consensus.jp',
		'*.desyo.jp',
		'*.dif.jp',
		'*.pastels.jp',
		'*.powerblogger.jp',
		'*.vippers.jp',
		'*.webpages.jp',

		// 205.209.97.56(non-existent)
		'*.biografi.biz',
		'*.biografi.info',
		'*.biografi.org',
		'*.biografi.us',
		'*.datadiri.biz',
		'*.datadiri.cc',
		'*.datadiri.com',
		'*.datadiri.info',
		'*.datadiri.net',
		'*.datadiri.org',
		'*.datadiri.tv',
		'*.datadiri.us',
		'*.indo.bz',
		'*.indo.cc',
		'*.profil.bz',
		'*.profil.cc',
		'*.profil.cn',
		'*.profil.in',
		'*.profil.tv',

		// 205.209.101.47(non-existent)
		'*.about.tc',
		'*.about.vg',
		'*.aboutus.gs',
		'*.aboutus.ms',
		'*.aboutus.tc',
		'*.aboutus.vg',
		'*.band.io',
		'*.beijing.am',
		'*.blogs.io',
		'*.boke.am',
		'*.clan.io',
		'*.ecv.gs',
		'*.ecv.ms',
		'*.ecv.vg',
		'*.freekong.cn',
		'*.go2net.ws',
		'*.hello.cn.com',
		'*.hello.io',
		'*.hits.io',
		'*.hostingweb.us',
		'*.hub.io',
		'*.ide.am',
		'*.inc.io',
		'*.incn.in',
		'*.indo.gs',
		'*.indo.ms',
		'*.indo.tc',
		'*.indo.vg',
		'*.infinitehosting.net',
		'*.infinites.net',
		'*.jushige.cn',
		'*.jushige.com',
		'*.kongjian.in',
		'*.lan.io',
		'*.llc.nu',
		'*.maimai.in',
		'*.max.io',
		'*.musician.io',
		'*.mycv.bz',
		'*.mycv.nu',
		'*.mycv.tv',
		'*.myweb.io',
		'*.ourprofile.biz',
		'*.ourprofile.info',
		'*.ourprofile.net',
		'*.ourprofile.org',
		'*.ourprofile.us',
		'*.profil.gs',
		'*.profil.ms',
		'*.profil.tc',
		'*.qiye.in',
		'*.site.io',
		'*.wan.io',
		'*.web-cam.ws',
		'*.webs.io',
		'*.xixu.cc',
		'*.zaici.am',
		'*.zip.io',
		'*.zuzhi.in',

		'*.profil.vg',		// 205.209.101.67(non-existent)
		'*.ecv.tc',			// 208.73.212.12(sp19.information.com)
		//'*.about.gs',		// No record
		//'*.eprofile.us',	// non-existent
	),
	'2Page.de' => array(
		'.dreipage.de',
		'.2page.de',
	),
	'*.3-hosting.net',
	'*.5gbfree.com',	// 75.126.153.58 by rob at roblist.co.uk
	'*.789mb.com',		// 75.126.197.240(545mb.com -> 66.45.238.60, 66.45.238.61) by Nicholas Long (nicolas.g.long at gmail.com)
	'*.8000web.com',	// 75.126.189.45
	'*.9999mb.com',		// 75.126.214.232 by allan Jerman (prodigy-airsoft at cox.net)
	'aeonity.com',		// by Emocium Solutions (creativenospam at gmail.com)
	'Ai.NET' => array(
		'*.100free.com',	// 205.134.188.54(development54.ai.net -> Non-existent) by support at ai.net
		'*.ai.net',			// 205.134.163.4(aries.ai.net) by support at ai.net
	),
	'*.aimoo.com',
	'*.alkablog.com',
	'*.alluwant.de',
	'.amkbb.com',
	'answerbag.com',	// 8.5.0.128
	'anyboard.net',
	'AOL.com' =>	// http://about.aol.com/international_services
		'/^(?:chezmoi|home|homes|hometown|journals|user)\.' .
		'(?:aol|americaonline)\.' .
		'(?:ca|co\.uk|com|com\.au|com.mx|de)$/',
		// Rough but works
	'Apple.com' => array('idisk.mac.com'),
	'*.askfaq.org',
	'.atfreeforum.com',		// 216.224.120.14(kelsey.mykelsey1.com -> 216.224.120.10)
	'*.atwiki.com',			//  by Masakazu Ohno (s071011 at sys.wakayama-u.ac.jp)
	'*.asphost4free.com',
	'basenow.com',
	'BatCave.net' => array(
		'.batcave.net',			// 64.22.112.226
		'.freehostpro.com',		// 64.22.112.226
	),
	'*.bb-fr.com',
	'.bbfast.com',				// 72.52.135.174 by blogmaster2003 at gmail.com
	'*.beeplog.com',
	'beepworld.it',
	'bestfreeforums.com',
	'bestvotexxx.com',
	'Bizcn.com' => '/.*\.w[0-9]+\.bizcn\.com$/', // XiaMen BizCn Computer & Network CO.,LTD
	'blinklist.com',
	'Blog.com' => array(
		// by admin.domains at co.blog.com
		'*.blog.com',
		'*.blogandorra.com',
		'*.blogangola.com',
		'*.blogaruba.com',
		'*.blogaustria.at',
	),
	'*.blog.com.es',
	'*.blog.hr',
	'*.blog4ever.com',
	'*.blog-fx.com',
	'*.blogbeee.com',
	'blogas.lt',
	'blogbud.com',
	'*.blogburkinafaso.com',
	'*.blogcu.com',			// by info at nokta.com
	'blogfreely.com',
	'*.blogdrive.com',
	'*.blogg.de',
	'bloggerblast.com',		// by B. Kadrie (domains at starwhitehosting.com)
	'bloggercrab.com',
	'bloggers.nl',
	'*.bloggingmylife.com',
	'*.bloggles.info',
	'*.blogharbor.com',
	'*.bloguj.eu',
	'bloguitos.com',
	'blogosfer.com',
	'*.blogse.nl',			// 85.17.41.16(srv1.blogse.nl) by ruben at mplay.nl
	'*.blogslive.net',
	'*.blogsome.com',		// by Roger Galligan (roger.galligan at browseireland.com)
	'*.blogstream.com',
	'blogyaz.com',
	'bloodee.com',
	'bluedot.us',			// 209.245.176.23 by mohitsriv at yahoo.com
	'board-4you.de',
	'boboard.com',			// 66.29.54.116 by Excelsoft (shabiba at e4t.net)
	'*.boardhost.com',
	'Bravenet.com' => array(
		'*.bravenet.com',
		'*.bravehost.com',
	),
	'*.by.ru',				// 217.16.29.50 by ag at near.ru, nthost.ru related?
	'C2k.jp' => array(
		// by Makoto Okuse (webmaster at 2style.net)
		'.081.in',
		'.2style.in',
		'.bian.in',	
		'.curl.in',	
		'.ennui.in',
		'.jinx.in',	
		'.loose.in',
		'.mist.in',	
		'.muu.in',	
		'.naive.in',
		'.panic.in',
		'.slum.in',	

		// by 2style, ns *.click2k.net, *.2style.net
		'.2st.jp',
		'.betty.jp',
		'.cabin.jp',
		'.cult.jp',	
		'.mippi.jp',
		'.purety.jp',
		'.rapa.jp',	
		'.side-b.jp',
		'.web-box.jp',
		'.yea.jp',

		// by makoto okuse (webmaster at 2style.net), ns *.click2k.net, *.2style.net
		'.2style.net',
		'.click2k.net',
		'.houka5.com',

		// by click2k, ns *.click2k.net, *.2style.net
		'.psyco.jp',
		'.sweety.jp',

		'.2style.jp',	// by click2k, ns *.2style.jp, *.2style.net
		'.cute.cd',		// by Yuya Fukuda (count at kit.hi-ho.ne.jp), ns *.2style.jp, *.2style.net
	),
	'*.canalblog.com',		// 195.137.184.103 by dns-admin at pinacolaweb.com
	'*.cdnhost.cn',			// 125.65.112.8, 125.65.112.9, 125.91.1.71 "Content Delivery Network" related? by cc at 51web.cn, seems malware hosting. ns *.dnsfamily.com, no official index.html
	'*.chueca.com',
	'city-forum.com',
	'*.clicdev.com',
	'*.craigslist.org',
	'concepts-mall.com',
	'*.conforums.com',		// by Roger Sutton (rogersutton at cox.net)
	'connectedy.com',		// 66.132.45.227(camilla.jtlnet.com) by astrader at insight.rr.com
	'counterhit.de',
	'*.createblog.com',
	'*.createforum.net',
	'*.creatuforo.com',		// by Desafio Internet S.L. (david at soluwol.com)
	'*.createmybb.com',
	'CwCity.de' => array(
		'.cwcity.de',
		'.cwsurf.de',
	),
	'dakrats.net',
	'*.dcswtech.com',
	'del.icio.us',
	'*.devil.it',
	'*.diaryland.com',
	'diigo.com',			///user/
	'digg.com',
	'digstock.com',
	'domains at galaxywave.net' => array(
		'blogstation.net',
		'.phpbb-host.com',
	),
	'dotbb.be',
	'*.dox.hu',				// dns at 1b.hu
	'*.eadf.com',
	'*.eblog.com.au',
	'*.ekiwi.de',
	'*.eamped.com',			// Admin by Joe Hayes (joe_h_31028 at yahoo.com)
	'.easyfreeforum.com',	// by XT Store Sas, Luca Lo Bascio (marketing at atomicshop.it)
	'*.easyjournal.com',
	'*.ebloggy.com',
	'enunblog.com',
	'*.epinoy.com',
	'*.ez-sites.ws',
	'*.ezbbforum.com',		// 72.52.134.135 by blogmaster2003 at gmail.com
	'*.fastsito.com',		// 208.77.96.9 by info at top100italiana.com
	'*.fathippohosting.com',	// 208.101.3.192
 	'FC2.com' => array(
 		'Blogs' => '#^(?:.+\.)?blog[0-9]+\.fc2\.com$#',	// Blogs, 'FOOBAR.blogN.fc2.com' and 'blogN.fc2.com/FOOBAR'
			// Many traps available:
			//	bqdr.blog98.fc2.com,       iframe
			//	csfir.blog87.fc2.com,      iframe
			//	pppgamesk.blog100.fc2.com, iframe, broken Japanese
			//	sippou2006.blog60.fc2.com, iframe
			// NOTE: 'blog.fc2.com' is not included
		'*.h.fc2.com',	// Adult
 	),
 	'*.fizwig.com',
	'forum.ezedia.net',
	'*.extra.hu',			// angelo at jasmin.hu
	'*.fanforum.cc',
	'fingerprintmedia.com',
	'*.filelan.com',
	'*.fora.pl',
	'*.forka.eu',
	'*.foren-city.de',
	'foren-gratis.de',
	'*.foros.tv',
	'*.forospace.com',
	'foroswebgratis.com',
	'*.forum-on.de',
	'*.forum24.se',
	'*.forum5.com',			// by Harry S (hsg944 at gmail.com)
	'.forum66.com',
	'Forumotion.com' => array(
		'*.forumotion.com',	// 75.126.156.15(s00.darkbb.com)
		'*.forumactif.com',	// 91.121.41.79
		'*.editboard.com',	// 75.126.156.15
		'*.sosblog.com',	// 74.86.25.140

		// 75.126.156.15 Used: forumotion.com, forumactif.com, editboard.com
		'*.0forum.biz',
		'*.1forum.biz',
		'*.1fr1.net',
		'*.1talk.net',
		'*.2forum.biz',
		'*.3forum.biz',
		'*.4forum.biz',
		'*.4rumer.com',
		'*.4rumer.net',
		'*.4umer.com',
		'*.4umer.net',
		'*.5forum.biz',
		'*.5forum.info',
		'*.5forum.net',
		'*.6forum.biz',
		'*.6forum.info',
		'*.77forum.com',
		'*.7forum.biz',
		'*.7forum.info',
		'*.7forum.net',
		'*.8forum.biz',
		'*.8forum.info',
		'*.8forum.net',
		'*.9forum.biz',
		'*.9forum.info',
		'*.activebb.net',
		'*.aforumfree.com',
		'*.all-forum.net',
		'*.all-up.com',
		'*.alldiscussion.net',
		'*.allgoo.net',
		'*.allgoo.us',
		'*.american-forum.net',
		'*.americantalk.net',
		'*.annuaire-forums.com',
		'*.asiafreeforum.com',
		'*.asianfreeforum.com',
		'*.azureforum.com',
		'*.azurforum.com',
		'*.bestcheapforum.com',
		'*.bestdiscussion.net',
		'*.bestgoo.com',
		'*.bestofforum.com',
		'*.bestofforum.net',
		'*.bestoforum.net',
		'*.bforum.biz',
		'*.big-forum.net',
		'*.board-poster.com',
		'*.board-realtors.com',
		'*.boardonly.com',
		'*.boardrealtors.net',
		'*.boardsmessage.com',
		'*.briceboard.com',
		'*.bubblelol.com',
		'*.buy-forum.com',
		'*.buy-talk.com',
		'*.buyforum.net',
		'*.buygoo.net',
		'*.buygoo.us',
		'*.camelscrew.com',
		'*.cheap-forum.com',
		'*.cheapforum.net',
		'*.chocoforum.com',
		'*.chocoforum.net',
		'*.clic-topic.com',
		'*.clicboard.com',
		'*.clictopic.com',
		'*.clictopic.net',
		'*.clictopics.com',
		'*.clubdiscussion.net',
		'*.coolbb.net',
		'*.crazy4us.com',
		'*.crazyfruits.net',
		'*.createmyboard.com',
		'*.crewcamel.com',
		'*.cyberfreeforum.com',
		'*.discutforum.com',
		'*.discutfree.com',
		'*.do-forum.com',
		'*.do-goo.com',
		'*.do-goo.net',
		'*.do-talk.com',
		'*.dodiscussion.com',
		'*.dogoo.us',
		'*.easydiscussion.net',
		'*.easyforumlive.com',
		'*.englishboard.net',
		'*.englishboards.com',
		'*.ephpbb.com',
		'*.eraseboard.net',
		'*.eraseboards.net',
		'*.euro-talk.net',
		'*.eurodiscussion.net',
		'*.eurogoo.com',
		'*.exchangesboard.com',
		'*.exprimetoi.net',
		'*.fairtopic.com',
		'*.fforum.biz',
		'*.fforumfree.com',
		'*.first-forum.com',
		'*.firstgoo.com',
		'*.fororama.com',
		'*.forum-2007.com',
		'*.forum-actif.net',
		'*.forum-free.biz',
		'*.forum-free.org',
		'*.forum-motion.com',
		'*.forum0.biz',
		'*.forum0.info',
		'*.forum0.net',
		'*.forum2.biz',
		'*.forum3.biz',
		'*.forum3.info',
		'*.forum5.info',
		'*.forum6.biz',
		'*.forum6.info',
		'*.forum7.biz',
		'*.forum777.com',
		'*.forum8.biz',
		'*.forum9.biz',
		'*.foruma.biz',
		'*.forumactif.biz',
		'*.forumactif.info',
		'*.forumactif.name',
		'*.forumactif.ws',
		'*.forumaction.net',
		'*.forumakers.com',
		'*.forumandco.com',
		'*.forumb.biz',
		'*.forumc.biz',
		'*.forumd.biz',
		'*.forumdediscussions.com',
		'*.forumdediscussions.net',
		'*.forume.biz',
		'*.forumeast.com',
		'*.forumf.biz',
		'*.forumfreek.com',
		'*.forumfreez.com',
		'*.forumg.biz',
		'*.forumh.biz',
		'*.forumh.net',
		'*.forumhope.com',
		'*.forumi.biz',
		'*.forumice.net',
		'*.foruminute.com',
		'*.forumj.biz',
		'*.forumj.net',
		'*.forumjonction.com',
		'*.forumk.biz',
		'*.foruml.biz',
		'*.forumm.biz',
		'*.forummotion.com',
		'*.forummotions.com',
		'*.forumn.biz',
		'*.forumn.net',
		'*.forumo.biz',
		'*.forumonline.biz',
		'*.forumotions.com',
		'*.forump.biz',
		'*.forump.info',
		'*.forumperso.com',
		'*.forumpersos.com',
		'*.forumplatinum.com',
		'*.forumpro.fr',
		'*.forumq.biz',
		'*.forumr.biz',
		'*.forumr.net',
		'*.forumrama.com',
		'*.forums-actifs.com',
		'*.forums-actifs.net',
		'*.forums1.net',
		'*.forumsactifs.com',
		'*.forumsactifs.net',
		'*.forumsfree.org',
		'*.forumsline.com',
		'*.forumsmakers.com',
		'*.forumsmotion.com',
		'*.forumsmotions.com',
		'*.forumt.biz',
		'*.forumv.biz',
		'*.forumvalue.com',
		'*.forumw.biz',
		'*.forumy.biz',
		'*.free-boards.net',
		'*.freediscussion.net',
		'*.freediscussions.net',
		'*.freeforum.me.uk',
		'*.freeforumboard.net',
		'*.freeforumfree.com',
		'*.freevideotalk.com',
		'*.frenchboard.com',
		'*.fruitsandco.com',
		'*.fullboards.com',
		'*.fullsubject.com',
		'*.fullsubject.net',
		'*.get-forum.net',
		'*.getdiscussion.com',
		'*.getgoo.net',
		'*.getgoo.us',
		'*.gettalk.net',
		'*.go-board.com',
		'*.go-board.net',
		'*.go-forum.net',
		'*.go-ogler.com',
		'*.gofreeforum.com',
		'*.gogoo.us',
		'*.goo-board.com',
		'*.goo-boys.com',
		'*.goo-dart.com',
		'*.goo-dole.com',
		'*.goo-done.com',
		'*.gooboards.com',
		'*.goodbb.net',
		'*.goodearths.com',
		'*.goodforum.net',
		'*.goodoles.com',
		'*.goodoolz.com',
		'*.gooforum.com',
		'*.gooforums.com',
		'*.googoolz.com',
		'*.great-forum.com',
		'*.heavenforum.com',
		'*.hforum.biz',
		'*.high-forums.com',
		'*.highbb.com',
		'*.highforum.net',
		'*.hightoxic.com',
		'*.homediscussion.net',
		'*.homegoo.com',
		'*.hooxs.com',
		'*.hot-me.com',
		'*.hot4um.com',
		'*.hotdiscussion.net',
		'*.hotgoo.net',
		'*.in-goo.com',
		'*.in-goo.net',
		'*.infodiscussion.net',
		'*.ingoo.us',
		'*.iowoi.com',
		'*.jforum.biz',
		'*.just-forum.net',
		'*.justboard.net',
		'*.justdiscussion.com',
		'*.justforum.net',
		'*.justgoo.com',
		'*.keuf.net',
		'*.lforum.biz',
		'*.lifediscussion.net',
		'*.lifeme.net',
		'*.lightbb.com',
		'*.logu2.com',
		'*.longdomaine.com',
		'*.lovediscussion.net',
		'*.lowcostforum.com',
		'*.make-free-forum.com',
		'*.makeforumfree.com',
		'*.makefreeforum.com',
		'*.mam9.com',
		'*.manageboard.com',
		'*.max2forum.com',
		'*.meabout.com',
		'*.megabb.com',
		'*.moncontact.com',
		'*.moninter.net',
		'*.motion-forum.net',
		'*.motionforum.net',
		'*.motionsforum.com',
		'*.mrforum.net',
		'*.msnboard.net',
		'*.msnyou.com',
		'*.my-free-board.com',
		'*.my-free-boards.com',
		'*.my-free-forum.com',
		'*.my-free-forums.com',
		'*.my-goo.com',
		'*.my-goo.net',
		'*.mydiscussionboard.com',
		'*.myfreeboard.net',
		'*.mygoo.org',
		'*.myrealboard.com',
		'*.netboarder.com',
		'*.netdiscussion.net',
		'*.netgoo.org',
		'*.new-forum.net',
		'*.newdiscussion.net',
		'*.newgoo.net',
		'*.nforum.biz',
		'*.nice-album.com',
		'*.nice-board.biz',
		'*.nice-board.com',
		'*.nice-board.net',
		'*.nice-boards.com',
		'*.nice-boards.net',
		'*.nice-forum.com',
		'*.nice-forum.net',
		'*.nice-forums.com',
		'*.nice-forums.net',
		'*.nice-gallery.net',
		'*.nice-subject.com',
		'*.nice-subjects.com',
		'*.nice-theme.com',
		'*.nice-topic.com',
		'*.nice-topics.com',
		'*.nicealbums.com',
		'*.niceboard.biz',
		'*.niceboard.net',
		'*.niceboards.net',
		'*.nicegalleries.net',
		'*.nicesubject.com',
		'*.nicesubjects.com',
		'*.nicetopic.net',
		'*.nicetopics.com',
		'*.nightforum.net',
		'*.nocost-forum.com',
		'*.nofeeforum.com',
		'*.one-forum.net',
		'*.onediscussion.net',
		'*.onegoo.us',
		'*.onlinegoo.com',
		'*.onlyquery.com',
		'*.open-board.com',
		'*.openu2.com',
		'*.othersboards.com',
		'*.own0.com',
		'*.pforum.biz',
		'*.phpbb9.com',
		'*.phpbbtest.com',
		'*.positifforum.com',
		'*.postalboard.com',
		'*.prodiscussion.net',
		'*.progoo.us',
		'*.pureforum.net',
		'*.qforum.biz',
		'*.quickbb.net',
		'*.realbb.net',
		'*.realfreeforum.com',
		'*.realmsn.com',
		'*.rforum.biz',
		'*.road2us.com',
		'*.roomforum.com',
		'*.saveboard.com',
		'*.screwcamel.com',
		'*.screwcamels.com',
		'*.sendboard.com',
		'*.sephorum.com',
		'*.sforum.biz',
		'*.shootboard.com',
		'*.shop-forum.net',
		'*.shopsubject.com',
		'*.simpleboard.net',
		'*.site-forums.com',
		'*.smileyforum.net',
		'*.sos-forum.net',
		'*.sos4um.com',
		'*.sos4um.net',
		'*.sosforum.net',
		'*.speedyforum.com',
		'*.stationforum.com',
		'*.subject-expert.com',
		'*.subject-line.com',
		'*.subject-tracer.com',
		'*.subjectchange.com',
		'*.subjectdeals.com',
		'*.subjectdebate.com',
		'*.subjectdone.com',
		'*.subjectonline.com',
		'*.subjectschange.com',
		'*.subjectsecrets.com',
		'*.super-forum.net',
		'*.talk-forums.com',
		'*.team-forum.net',
		'*.team-talk.net',
		'*.teamconvention.com',
		'*.teamgoo.net',
		'*.teensboards.com',
		'*.the-talk.net',
		'*.the-up.com',
		'*.thegoo.us',
		'*.thinksubject.com',
		'*.top-board.com',
		'*.top-forum.net',
		'*.top-me.com',
		'*.top-talk.net',
		'*.topdiscussion.com',
		'*.topgoo.net',
		'*.topic-board.com',
		'*.topic-board.net',
		'*.topic-debate.com',
		'*.topic-ideas.com',
		'*.topic-mail.com',
		'*.topic-zone.com',
		'*.topicboard.net',
		'*.topicboards.com',
		'*.topicmanager.com',
		'*.topicsolutions.net',
		'*.toxicfarm.com',
		'*.unlimitboard.com',
		'*.unlimitedboard.com',
		'*.unlimitedforum.com',
		'*.up-with.com',
		'*.up-your.com',
		'*.urealboard.com',
		'*.user-board.net',
		'*.userboard.net',
		'*.users-board.com',
		'*.users-board.net',
		'*.usersboard.com',
		'*.usersboard.net',
		'*.webgoo.us',
		'*.withboards.com',
		'*.withme.us',
		'*.worldgoo.com',
		'*.yahooboard.net',
		'*.yforum.biz',
		'*.yoo7.com',
		'*.youneed.us',
		'*.your-board.com',
		'*.your-talk.com',
		'*.yourme.net',
		'*.zforum.biz',

		// 75.126.156.15 redirect to forumactif.com
		'*.actifforum.com',
		'*.fr-bb.com',
		'*.superforum.fr',
		
		// 75.126.156.15 redirect to editboard.com
		'*.bbfr.net',
		'*.bbgraf.com',
		'*.cinebb.com',
		'*.darkbb.com',
		'*.dynamicbb.com',
		'*.zikforum.com',

		// 74.86.25.140 Used: sosblog.com
		'*.0-up.com',
		'*.0-yo.com',
		'*.04live.com',
		'*.06fr.com',
		'*.0fra.com',
		'*.0jet.com',
		'*.0yoo.com',
		'*.1toxic.com',
		'*.2k00.com',
		'*.2xik.com',
		'*.blog-2007.com',
		'*.blog-2008.com',
		'*.blog-2009.com',
		'*.blog-2010.com',
		'*.blog-actif.net',
		'*.blog-marley.com',
		'*.blog-talker.com',
		'*.blog2009.com',
		'*.blogactif.net',
		'*.blogalbums.com',
		'*.blogalerie.com',
		'*.blogaleries.com',
		'*.blogallerys.com',
		'*.blogandme.com',
		'*.blogandyou.com',
		'*.blogbubble.net',
		'*.blogdollz.com',
		'*.bloggeuse.net',
		'*.bloggeuses.com',
		'*.bloginstinct.com',
		'*.bloginternet.net',
		'*.blogmakers.net',
		'*.blogmarley.net',
		'*.blogminister.net',
		'*.blogmorane.com',
		'*.blognboard.com',
		'*.blogotek.net',
		'*.blogpas.com',
		'*.blogpublisher.net',
		'*.blogsalbum.com',
		'*.blogsalbums.com',
		'*.blogsfan.net',
		'*.blogsgallerys.com',
		'*.blogsitephp.com',
		'*.blogsland.net',
		'*.blogsmaker.com',
		'*.blogsmakers.com',
		'*.blogspeed.net',
		'*.blogsup.net',
		'*.blogsutil.com',
		'*.blogsutils.com',
		'*.blogsystem.net',
		'*.blogtalker.net',
		'*.blogtheque.net',
		'*.blogutil.com',
		'*.blogvsboard.com',
		'*.briceblog.com',
		'*.cibleblog.com',
		'*.cristalblog.com',
		'*.crystalblog.net',
		'*.dad-blog.com',
		'*.daddy-blog.com',
		'*.defaultblog.com',
		'*.discussionsblog.com',
		'*.dollzblog.com',
		'*.dtoxic.com',
		'*.easiestblog.net',
		'*.espaceblog.net',
		'*.exitblog.net',
		'*.expensiveblog.com',
		'*.extra-blog.net',
		'*.extrablog.net',
		'*.facileblog.com',
		'*.forblogger.com',
		'*.forumsblogs.com',
		'*.freephpblog.com',
		'*.fresh-blog.net',
		'*.galleryblog.net',
		'*.galleryblogs.net',
		'*.gallerysblog.com',
		'*.gallerysblogs.com',
		'*.hyper-blog.net',
		'*.hyper-blogger.com',
		'*.hyper-blogger.net',
		'*.hyperblogger.net',
		'*.instinctblog.com',
		'*.jeunblog.com',
		'*.jeuneblog.com',
		'*.judblog.com',
		'*.makeblog.net',
		'*.man-blog.net',
		'*.man-blogs.com',
		'*.man-blogs.net',
		'*.manblog.net',
		'*.manblog.org',
		'*.manblog.us',
		'*.manblogs.net',
		'*.mansblog.net',
		'*.mansblogs.com',
		'*.mansblogs.net',
		'*.meetingsblogs.com',
		'*.men-blog.net',
		'*.men-blogs.com',
		'*.men-blogs.net',
		'*.menblog.us',
		'*.menblogs.net',
		'*.mensblogs.net',
		'*.messaginblog.com',
		'*.monblogger.com',
		'*.mum-blog.com',
		'*.mummy-blog.com',
		'*.mummyblog.com',
		'*.myareablog.com',
		'*.myblog-online.net',
		'*.mybloginternet.com',
		'*.myblogland.com',
		'*.myblogmag.com',
		'*.myfreshblog.com',
		'*.myinternetblog.net',
		'*.myoverblog.com',
		'*.myoverblog.net',
		'*.mysharedblog.com',
		'*.nice-albums.com',
		'*.nice-galleries.com',
		'*.nicealbum.net',
		'*.nicegallery.net',
		'*.notreblogger.com',
		'*.outilblog.com',
		'*.over-blogger.com',
		'*.overblogger.com',
		'*.pasblog.com',
		'*.pasblog.net',
		'*.perfect-blog.net',
		'*.perfectblog.net',
		'*.phpblogsite.com',
		'*.phpmyblog.net',
		'*.plateformeblog.com',
		'*.playblogger.com',
		'*.publishmyblog.com',
		'*.pulseblog.net',
		'*.pulseblogs.com',
		'*.puretoxic.com',
		'*.reservedblog.com',
		'*.shareblog.net',
		'*.shareblogs.net',
		'*.shared-blogs.com',
		'*.sharedblog.net',
		'*.sharedblogs.net',
		'*.solideblog.com',
		'*.systemeblog.com',
		'*.talkmeblog.com',
		'*.toolsblog.net',
		'*.toxicplace.com',
		'*.ufreeblog.com',
		'*.uliveblog.com',
		'*.ultratoxic.com',
		'*.utilblog.com',
		'*.utilblogs.com',
		'*.utilsblog.com',
		'*.utilsblogs.com',
		'*.various-blog.com',
		'*.various-blog.net',
		'*.various-blogs.com',
		'*.various-blogs.net',
		'*.variusblog.com',
		'*.versusblog.com',
		'*.vieuxblog.com',
		'*.web-day.net',
		'*.whatsblog.net',
		'*.woman-blog.net',
		'*.woman-blogs.com',
		'*.woman-blogs.net',
		'*.womanblog.com',
		'*.womanblog.org',
		'*.womanblog.us',
		'*.womanblogs.net',
		'*.womansblogs.com',
		'*.womansblogs.net',
		'*.women-blog.net',
		'*.women-blogs.com',
		'*.women-blogs.net',
		'*.womenblog.us',
		'*.womenblogs.net',
		'*.womensblog.net',
		'*.womensblogs.net',
		'*.youblog.net',
		'*.yourliveblog.com',
		'*.yourweblog.net',
	),
	'*.forumcommunity.net',
	'*.forumer.com',
	'members.forumfree.com',	// 91.121.4.44(ns37290.ovh.net) by dom at artmajeur.com
	'*.forumfree.net',			// 74.53.57.70(*.static.theplanet.com)
	'forumhosting.org',
	'*.forums.com',
	'forumbolt.com',
	'phpbb.forumgratis.com',
	'forumlari.net',
	'*.forumlivre.com',
	'forumnow.com.br',
	'*.forumppl.com',
	'Forumprofi.de' => '#^(?:.*\.)?forumprofi[0-9]*\.de$#',
	'ForumUp' => '#^^(?:.*\.)?forumup\.' .
		'(?:at|be|ca|ch|co\.nz|co\.uk|co\.za|com|com\.au|com\.mx|cn|' .
		'cz|de|dk|es|eu|fr|gr|hu|in|info|ir|it|jobs|jp|lt|' .
		'lv|org|pl|name|net|nl|ro|ru|se|sk|tv|us|web\.tr)$#',
	'*.fory.pl',
	'fotolog.com',
	'*.fr33webhost.com',
	'*.free20.com',			// 63.246.154.15(unknown.sagonet.net => non-existent) by do168 at 126.com
	'*.free-25.de',
	'*.free-bb.com',
	'Free-Blog-Hosting.com' => array(
		'*.free-blog-hosting.com',		// by Robert Vigil (ridgecrestdomains at yahoo.com), ns *.phpwebhosting.com
		'*.blog-tonite.com',			// ns *.phpwebhosting.com
		'*.blogznow.com',				// ns *.phpwebhosting.com
		'*.myblogstreet.com',			// by Robert Vigil, ns *.phpwebhosting.com
		'*.blogbeam.com',				// by Robert Vigil, ns *.phpwebhosting.com
	),
	'*.free-forums.org',		// 209.62.43.2(ev1s-209-62-43-2.ev1servers.net) by Teodor Turbatu (tteo at zappmobile.ro)
	'free-guestbook.net',
	'*.free-site-host.com',	// by CGM-Electronics (chris at cgm-electronics.com)
	'freebb.nl',
	'*.freeclans.de',
	'*.freehostplace.com',	// by contact at keepclear.co.uk
	'*.freelinuxhost.com',	// by 100webspace.com
	'*.nofeehost.com',
	'*.freehyperspace.com',
	'freeforum.at',			// by Sandro Wilhelmy
	'freeforumshosting.com',	// by Adam Roberts (admin at skaidon.co.uk)
	'*.freeforums.org',		// by 1&1 Internet, Inc. - 1and1.com
	'*.freemyforumadult.com',	// 208.97.191.105(apache2-argon.willie.dreamhost.com) by sick at designsbysick.com
	'*.freewebhostingpro.com',
	'*.freehostingz.com',	// no dns reply => 67.159.33.10 by Marx Lomas (marvellousmarx at hotmail.com)
	'FreeWebHostingArea.com' => array(	// or www.freewha.com
		'*.6te.net',
		'*.ueuo.com',
		'*.orgfree.com',
	),
	'*.freewebpage.org',
	'Freewebs.com' => array(	// by inquiries at freewebs.com
		'freewebs.com',
		'freewebsfarms.com',
	),
	'*.freewebspace.net.au',
	'freewebtown.com',
	'*.freemyforum.com',	// by messahost at gmail.com
	'freepowerboards.com',
	'*.freepowerboards.com',
	'*.fsphost.com',		// by Michael Renz (michael at fsphost.com)
	'Funpic.de' => array(	// by alexander at liemen.net
		'*.funpic.de',
		'*.funpic.org',
	),
	'gb-hoster.de',
	'*.genblogger.com',
	'GetBetterHosting.com' => array(
		'*.30mb.com',	// 207.210.82.74(cpanel.90megs.com) by 30MB Online (63681 at whois.gkg.net), introduced as one alternative of 90megs.com
		'*.90megs.com',	// 207.210.82.75 by Get Better Hosting (admin at getbetterhosting.com)
	),
	'*.gexxe.com',
	'*.goodboard.de',
	'gossiping.net',
	'*.greatnuke.com',
	'*.guestbook.de',
	'gwebspace.de',
	'Google.com' => array(
		'*.blogspot.com',
		'docs.google.com',
		'*.googlegroups.com',		///web/
		'*.googlepages.com',
		'groups-beta.google.com',
		'groups.google.*',	 ///group/ Seems widely distributed
			//'groups.google.ca', 'groups.google.com', 'groups.google.co.uk',
			//'groups.google.de', 'groups.google.es',  'groups.google.fr',
			//'groups.google.it', ...
	),
	'guestbook.at',
	'club.giovani.it',
	'*.gratis-server.de',
	'healthcaregroup.com',
	'*.heliohost.org',
	'Halverston Holdings Limited' => array(	// pochta.ru?lng=en
		// Seems one of affiliates of RBC, RosBusinessConsulting (rbc.ru, rbcnews.com)
		'*.fromru.com',		// by Lapeshkina Tatyana (noc at pochta.ru)
		'*.front.ru',		// (domain at hc.ru)
		'*.hc.ru',			// by (domain at hosting.rbc.ru, domaincredit at hosting.rbc.ru)
		'*.hotbox.ru',		// (domain at hc.ru)
		'*.hotmail.ru',		// (hosting at hc.ru)
		'*.krovatka.su',	// (domain at hc.ru, hosting at hc.ru)
		'*.land.ru',		// (domain at hc.ru)
		'*.mail15.com',		// (hosting at hc.ru)
		'*.mail333.com',	// (hosting at hc.ru)
		'*.newmail.ru',		// (domain at hc.ru, hosting at hc.ru)
		'*.nightmail.ru',	// (domain at hc.ru, hosting at hc.ru)
		'*.pisem.net',		// (hosting at hc.ru)
		'*.pochta.ru',		// (domain at hc.ru)
		'*.pochtamt.ru',	// (domain at hc.ru)
		'*.pop3.ru',		// (domain at hc.ru)
		'*.rbcmail.ru',		// (domain at hc.ru)
		'*.smtp.ru',		// (domain at hc.ru)
	),
	'*.hiblogger.com',		// by chiaokin at gmail.com
	'*.hit.bg',				// by forumup.com ??
	'*.homeblock.com',		// 72.55.141.237(*.static.privatedns.com)
	'*.host-page.com',
	'*.hostingclub.de',
	'forums.hspn.com',
	'*.httpsuites.com',
	'*.hut2.ru',
	'ibfree.org',			// 208.101.45.88
	'IC.cz' => array(
		'*.ezin.cz',		// internetcentrum at gmail.com, ns ignum.com, ignum.cz
		'*.hu.cz',			// internetcentrum at gmail.com
		'*.hustej.net',		// baz at bluedot.cz, dom-reg-joker at ignum.cz
		'*.ic.cz',			// internetcentrum at gmail.com
		'*.kx.cz',			// jan at karabina.cz, info at ignum.cz
		'*.own.cz',			// internetcentrum at gmail.com
		'*.phorum.cz',		// internetcentrum at gmail.com
		'*.tym.cz',			// internetcentrum at gmail.com
		'*.tym.sk',			// jobot at ignum.cz
		'*.wu.cz',			// jan at karabina.cz, info at ignum.cz
		'*.yc.cz',			// ivo at karabina.cz, jan at karabina.cz
		'*.yw.sk',			// jobot at ignum.cz
	),
	'icedesigns at gmail.com' => array(	// by Boling Jiang (icedesigns at gmail.com)
		'*.0moola.com',
		'*.3000mb.com',
		'.501megs.com',
		'*.teracities.com',
		'*.xoompages.com',
	),
	'*.icspace.net',
	'iEUROP.net' => array(
		'*.ibelgique.com',
		'*.iespana.es',
		'*.ifrance.com',
		'*.iitalia.com',
		'*.iquebec.com',
		'*.isuisse.com',
	),
	'*.ihateclowns.net',
	'*.ii55.com',
	'*.ipbfree.com',
	'*.iphorum.com',
	'*.blog.ijijiji.com',
	'*.info.com',
	'*.informe.com',
	'it168.com',
	'.iwannaforum.com',
	'*.jconserv.net',
	'*.jeeran.com',
	'*.jeun.fr',
	'*.joolo.com',
	'*.journalscape.com',
	'*.justfree.com',
	'kataweb.it' => array(		// kata-redir.kataweb.it
		'*.blog.kataweb.it',
		'*.repubblica.it',
	),
	'*.kaixo.com',		// blogs.kaixo.com, blogak.kaixo.com
	'*.kokoom.com',
	'koolpages.com',
	'*.kostenlose-foren.org',
	'*.ksiegagosci.info',
	'LaCoctelera.com' => array(
		'lacoctelera.com',	// by alberto at the-cocktail.com
		'espacioblog.com',	// by dominios at ferca.com
	),
	'Lide.cz' => array(
		'*.lide.cz',
		'*.sblog.cz',
	),
	'*.lioru.com',
	'limmon.net',
	'linkinn.com',
	'lipstick.com',			// 208.96.53.70(customer-reverse-entry.*) by domain_admin at advancemags.com
	'listible.com',			///list/
	'Livedoor.com' => array(
		'blog.livedoor.jp',
		'*.blog.livedoor.com',	// redirection
	),
	'*.livejournal.com',
	'.load4.net',			// 72.232.201.61(61.201.232.72.static.reverse.layeredtech.com), Says free web hosting but anonymous
	'*.logme.nl',
	'lol.to',
	'ltss.luton.ac.uk',
	'Lycos.com' => array(
		'.angelfire.com',	// angelfire.lycos.com

		'*.jubii.dk',	// search., medlem.
		'*.jubiiblog.co.uk',
		'*.jubiiblog.com.es',	// by Lycos Europe GmbH
		'*.jubiiblog.de',
		'*.jubiiblog.dk',
		'*.jubiiblog.fr',
		'*.jubiiblog.it',
		'*.jubiiblog.nl',

		'*.lycos.at',
		'*.lycos.ch',
		'*.lycos.co.uk',
		'angelfire.lycos.com',
		'*.lycos.de',
		'*.lycos.es',
		'*.lycos.fr',
		'*.lycos.it',		// by Lycos Europe GmbH
		'*.lycos.nl',

		'*.spray.se',
		'*.sprayblog.se',

		'*.tripod.com',
	),
	'*.mastertopforum.com',
	'mbga.jp',				// by DeNA Co.,Ltd. (barshige at hq.bidders.co.jp, torigoe at hq.bidders.co.jp)
	'meneame.net',
	'*.memebot.com',
	'*.messageboard.nl',
	'mokono GmbH' => array(
		'*.blog.com.es',
		'*.blog.de',
		'*.blog.fr',
		'*.blog.co.uk',
		'*.blog.ca',
		'*.blogs.se',
		'*.blogs.fi',
		'*.blogs.no',
		'*.blogs.dk',
		'*.blogs.ro',
		'*.weblogs.pl',
		'*.weblogs.cz',
		'*.weblogs.hu',
	),
	'mojklc.com',
	'*.mundoforo.com',
	'*.money-host.com',
	'MonForum.com' => array(
		'*.monforum.com',
		'*.monforum.fr',
	),
	'*.multiforum.nl',		// by Ron Warris (info at phpbbhost.nl)
	'*.my3gb.com',			// 74.86.20.235(layeredpanel.com => 213.239.213.90 => *.clients.your-server.de)
	'*.my10gb.com',			// by craig_gatenby at hotmail.com
	'myblog.is',
	'myblogma.com',
	'*.myblogvoice.com',
	'myblogwiki.com',
	'*.myforum.ro',
	'*.myfreewebhost.org',	// 216.32.73.163(*.static.reverse.ltdomains.com)
	'*.myfreewebs.net',
	'*.mysite.com',
	'*.myxhost.com',
	'*.netfast.org',
	'NetGears.com' => array(
		// by domains at netgears.com, ns *.northsky.com, seems 0Catch.com and northsky.com related
		'*.9k.com',				// 64.39.31.55(netgears.com)
		'*.freewebspace.com',	// 64.49.236.72
	),
	'Netscape.com' => array('*.netscape.com'),
	'users.newblog.com',
	'neweconomics.info',
	'*.newsit.es',				// 75.126.252.108
	'*.nm.ru',
	'*.nmj.pl',
	'*.nocatch.net',			// 74.86.93.190(layeredpanel.com => ...)
	'Northsky.com' => array(
		// by hostmaster at northsky.com
		// northsky.com redirects to communityarchitect.com

		// 64.136.24.162(public-24-162.lax.ws.untd.com) by Mark Bishop
		'*.00author.com',
		'*.00band.com',
		'*.00books.com',
		'*.00cash.com',
		'*.00cd.com',
		'*.00dvd.com',
		'*.00family.com',
		'*.00game.com',
		'*.00home.com',
		'*.00it.com',
		'*.00me.com',
		'*.00movies.com',
		'*.00page.com',
		'*.00politics.com',
		'*.00sf.com',
		'*.00show.com',
		'*.00song.com',
		'*.00trek.com',
		'*.00video.com',
		'*.0me.com',
		'*.0pi.com',
		'*.happy-couple.com',
		'*.warp0.com',

		'*.50megs.com',		// 64.136.25.170

		// 64.136.25.171(mail.50megs.com) by Mark Bishop
		'*.00server.com',
		'*.communityarchitect.com',		// Only mysite.com has a link to communityarchitect.com
		'*.fanspace.com',

		// 64.136.25.168 by Mark Bishop
		'*.00go.com',
		'*.00space.com',
		'*.00sports.com',
	),
	'*.obm.cn',				// by xiaobak at yahoo.com.cn
	'*.ocom.pl',			// 67.15.104.83(*.ev1servers.net)
	'onlyfree.de',
	'*.ooblez.com',			// by John Nolande (ooblez at hotmail.com)
	'*.ohost.de',
	'Osemka.pl' => array(	// by Osemka Internet Media (biuro at nazwa.pl)
		'.friko.pl',
		'.jak.pl',
		'.nazwa.pl',
		'*.prv.pl',			// by NetArt (biuro at nazwa.pl)
		'.w8w.pl',
		'.za.pl',
		'.skysquad.net',	// by Dorota Brzezinska (info at nazwa.pl)
	),
	'oyax.com',				///user_links.php
	'*.p2a.pl',
	'*.parlaris.com',
	'*.pathfinder.gr',
	'*.persianblog.com',
	'*.phorum.pl',
	'Phpbb24.com' => array(	// by Daniel Eriksson
		'*.createforum.us',	// registry at webbland.se
		'*.forumportal.us',	// registry at webbland.se
		'*.freeportal.us',	// registry at network24.se
		'*.phpbb2.us',		// daniel at danielos.com
		'*.phpbb24.com',	// daniel at danielos.com
		'*.myforumportal.com',	// daniel at webbland.se
	),
	'phpbb4you.com',
	'phpbbcommunity.com',
	'*.phpbbx.de',
	'*.pochta.ru',
	'*.portbb.com',
	'*.portcms.com',
	'powerwebmaster.de',
	'pro-board.com',		// by SEM Optimization Services Ltd (2485 at coverage1.com)
	'ProBoards' => '#^.*\.proboards[0-9]*\.com$#',
	'*.probook.de',
	'*.prohosting.com',	// by Nick Wood (admin at dns-solutions.net)
	'*.pun.pl',
	'putfile.com',
	'*.quickfreehost.com',
	'quizilla.com',
	'*.quotaless.com',
	'*.qupis.com',		// by Xisto Corporation (shridhar at xisto.com)
	'razyboard.com',
	'*.rbbloggers.com',	// 88.85.78.82 by manager at vertona.com
	'realhp.de',
	'reddit.com',		///user/
	'rgbdesign at gmail.com' => array(	// by RB2 (rgbdesign at gmail.com)
		'*.juicypornhost.com',
		'*.pornzonehost.com',
		'*.xhostar.com',
	),
	'RIN.ru' => array(
		'*.sbn.bz',
		'*.wol.bz',
	),
	'*.sayt.ws',
	'Seblg.com' => array(
		'*.seblg.com',		// by Dao Lee (st at seblg.com)
		'*.xshorturl.org',	// by Tonny Lee (admin at seblg.com)
	),
	'*.seo-blog.org',
	'*.shoutpost.com',
	'*.siamforum.com',
	'*.siteburg.com',
	'*.sitehome.ru',
	'*.sitesfree.com',		// support at livesearching.com
	'*.sitesled.com',
	'skinnymoose.com',		// by Steven Remington (admin at outdoorwebhosting.com)
	'SmarTrans.com' => array(
		'.3w.to',
		'.aim.to',
		'.djmp.jp',
		'.nihongourl.nu',
		'.url.sh',		// Says SmarTrans
		'.urljp.com',
		'.www1.to',
		'.www2.to',
		'.www3.to',
	),
	'*.spazioblog.it',			// by Ivan Maria Spadacenta
	'*.spazioforum.it',			// by Ivan Maria Spadacenta
	'members.spboards.com',
	'forums.speedguide.net',
	'Sphosting.com' => array(	// by admin at sphosting.com
		'*.hostinplace.com',	// 66.197.204.233(sp3.sphosting.net -> 66.197.204.229)
		'*.sphosting.com',		// 66.197.204.229(sp3.sphosting.net)
		'*.sphosting.net',		// 66.197.204.229(*snip*), redirect to sphosting.com
		'*.spboards.com',		// 66.197.146.104(sp2.sphosting.com -> Non-existent)
		'spweblog.com',			// 66.197.146.101(sp2.sphosting.com -> Non-existent)
	),
	'*.spicyblogger.com',
	'*.spotbb.com',
	'.squadz.net',				// 67.15.50.4(svr66.edns1.com) by info at ehostpros.com
	'*.squarespace.com',
	'stickypond.com',
	'stormloader.com',
	'strikebang.com',
	'*.sultryserver.com',
	'*.t35.com',
	'*.talks.at',
	'tabletpcbuzz.com',
	'*.talkthis.com',
	'tbns.net',
	'telasipforums.com',
	'theforumhub.com',
	'thestudentunderground.org',
	'think.ubc.ca',
	'*.thumblogger.com',
	'Topix.com' => array(
		'topix.com',
		'topix.net',
	),
	'forum.tourism-talk.com.au',
	'tycho at e-lab.nl' => array(
		'*.234mb.com',		// 195.242.99.206(s206.softwarelibre.nl -> 194.109.216.19)
		'*.1234mb.com',		// 74.86.20.227(layeredpanel.com -> 195.242.99.195 -> s195.softwarelibre.nl)
	),
	'*.tumblr.com',
	'UcoZ Web-Services' => array(
		'*.3dn.ru',
		'*.clan.su',
		'*.moy.su',
		'*.my1.ru',
		'*.p0.ru',
		'*.pp.net.ua',
		'*.ucoz.co.uk',
		'*.ucoz.com',
		'*.ucoz.net',
		'*.ucoz.org',
		'*.ucoz.ru',
		'*.ws.co.ua',
	),
	'*.unforo.net',
	'veoh.com',
	'*.vdforum.ru',
	'*.vtost.com',
	'*.vidiac.com',
	'Voila.fr' => array('.site.voila.fr'),
	'volny.cz',
	'voy.com',
	'*.welover.org',
	'Web1000.com' => array(	// http://www.web1000.com/register_new2.php
		'*.fasthost.tv',
		'*.hothost.tv',
		'*.isgreat.tv',
		'*.issexy.tv',
		'*.isterrible.tv',
		'*.somegood.tv',
		'*.adultnations.com',
		'*.alladultfemale.com',
		'*.alladultmale.com',
		'*.allbisexual.com',
		'*.allbreast.com',
		'*.allfeminism.com',
		'*.allmanpages.com',
		'*.allmendirect.com',
		'*.allphotoalbum.com',
		'*.allsexpages.com',
		'*.allwomanweb.com',
		'*.attorney-site.com',
		'*.bedclip.com',
		'*.bestfamilysite.com',
		'*.bestmusicpages.com',
		'*.bigmoron.com',
		'*.bourgeoisonline.com',
		'*.candyfrom.us',
		'*.cartoonhit.com',
		'*.cat-on-line.com',
		'*.chokesondick.com',
		'*.closeupsof.us',
		'*.cpa-site.com',
		'*.dampgirl.com',
		'*.dampgirls.com',
		'*.deepestfetish.com',
		'*.docspages.com',
		'*.dog-on-line.com',
		'*.dogcountries.com',
		'*.dognations.com',
		'*.doingitwith.us',
		'*.drenchedface.com',
		'*.drenchedlips.com',
		'*.drspages.com',
		'*.edogden.com',
		'*.eroticountry.com',
		'*.fasthost.tv',
		'*.fineststars.com',
		'*.foronlinegames.com',
		'*.forplanetearth.com',
		'*.freeadultparty.com',
		'*.freespeechsite.com',
		'*.gayadultxxx.com',
		'*.gaytaboo.com',
		'*.greatcookery.com',
		'*.greatrecipepages.com',
		'*.greatstreamingvideo.com',
		'*.hatesit.com',
		'*.hothost.tv',
		'*.iscrappy.com',
		'*.isgreat.tv',
		'*.issexy.tv',
		'*.isterrible.tv',
		'*.itinto.us',
		'*.japannudes.net',
		'*.jesussave.us',
		'*.jesussaveus.com',
		'*.labialand.com',
		'*.lettersfrom.us',
		'*.lookingat.us',
		'*.lunaticsworld.com',
		'*.microphallus.com',
		'*.mycatshow.com',
		'*.mydogshow.com',
		'*.myhardman.com',
		'*.mylawsite.net',
		'*.mylovething.com',
		'*.onasoapbox.com',
		'*.onlinepulpit.com',
		'*.petitionthegovernment.com',
		'*.photosfor.us',
		'*.picturetrades.com',
		'*.pleasekiss.us',
		'*.politicalemergency.com',
		'*.power-emergency.com',
		'*.prayingfor.us',
		'*.realbadidea.com',
		'*.realisticpolitics.com',
		'*.reallybites.com',
		'*.reallypumping.us',
		'*.reallyrules.com',
		'*.reallysuckass.com',
		'*.reallysucksass.com',
		'*.realsweetheart.com',
		'*.rottenass.com',
		'*.schoolreference.com',
		'*.sexheroes.com',
		'*.sharewith.us',
		'*.smutstars.com',
		'*.soakinglips.com',
		'*.somegood.tv',
		'*.songsfrom.us',
		'*.stinkingfart.com',
		'*.stinkyhands.com',
		'*.storiesfrom.us',
		'*.taboocountry.com',
		'*.television-series.com',
		'*.thisbelongsto.us',
		'*.totallyboning.us',
		'*.vaginasisters.com',
		'*.verydirtylaundry.com',
		'*.videosfor.us',
		'*.videosfrom.us',
		'*.videosof.us',
		'*.virtualdogshit.com',
		'*.web1000.com',
		'*.webpicturebook.com',
		'*.wronger.com',
		'*.xxxnations.com',
		'*.yourwaywith.us',
	),
	'webblog.ru',
	'weblogmaniacs.com',
	'.webng.com',			// www.*, www3.*
	'*.webnow.biz',			// by Hsien I Fan (admin at servcomputing.com), ServComputing Inc. 
	'websitetoolbox.com',
	'*.webtropia.com',
	'Welnet.de' => array(
		'welnet.de',
		'welnet4u.de',
	),
	'wh-gb.de',
	'*.wikidot.com',
	'*.wizhoo.com',			// by Comp U Door (sales at comp-u-door.com)
	'*.wmjblogs.ru',
	'*.wordpress.com',
	//'.wsboards.com',		// Noticed this site had been removed due to spam
	'xeboards.com',			// by Brian Shea (bshea at xeservers.com)
	'*.xforum.se',
	'xfreeforum.com',
	'*.xhost.ro',			// by domain-admin at listserv.rnc.ro
	'*.xoomwebs.com',
	'.xterm.org',
	'.freeblogs.xp.tl',
	'*.xphost.org',			// by alex alex (alrusnac at hotmail.com)
	'*.ya.com',				// 'geo.ya.com', 'blogs.ya.com', 'humano.ya.com', 'audio.ya.com'...
	'Yahoo.com' => array(
		'flickr.com',
		'geocities.com',
	),
	'YANDEX, LLC.' => array(	// noc at yandex.net
		'*.narod.ru',
		'ya.ru',
		'yandex.ru',
	),
	'*.yeahost.com',
	'yourfreebb.de',
	'Your-Websites.com' => array(
		'*.your-websites.net',
		'*.web-space.ws',
	),


	'*.heliohost.org',	// by ashoat at gmail.com
	'X10Hosting.com' => array(
		// by support at clockworkcomputers.com
		'*.x10hosting.com',
		'*.elementfx.com',
		'*.exofire.net',
		'*.pcriot.com',
	),
	'.zendurl.com',		// by ajcar1992 at gmail.com
);

// --------------------------------------------------

$blocklist['B-2'] = array(

	// B-2: Jacked contents, something implanted
	// (e.g. some sort of blog comments, BBSes, forums, wikis)
	'*.3dm3.com',
	'3gmicro.com',			// by Dean Anderson (dean at nobullcomputing.com)
	'*.1fr1.com',
	'a4aid.org',
	'aac.com',
	'*.aamad.org',
	'ad-pecjak.si',
	'agnt.org',
	'alwanforthearts.org',
	'*.anchor.net.au',
	'anewme.org',
	'arcrockett.com',		///golf/excel/
	'arisedesign.net',		//Portfolio/d981/
	'internetyfamilia.asturiastelecentros.com',
	'ballblair.com',		///images/thumbs/
	'Ball State University' => array('web.bsu.edu'),
	'btofaq.net',			///v3/forum
	'blepharospasm.org',
	'brettforrest.com',		// 72.10.43.226 by brettforrest at hotmail.com, ns *.mediatemple.net
	'nyweb.bowlnfun.dk',
	'*.buzznet.com',
	'Carroll College' => array(
		'carroll.edu',		///boards/
	),
	'*.canberra.net.au',
	'castus.com',
	'Case Western Reserve University' => array('case.edu'),
	'ceval.de',
	'chaco.gov.ar',
	'chasingrainbowsaustralia.com',	///geometry/
	'codespeak.net',
	'Colorado School of Mines' => array('ticc.mines.edu'),
	'*.colourware.co.uk',
	'cpuisp.com',
	'International Christian University' => array('icu.edu.ua'),
	'*.iphpbb.com',
	'board-z.de',
	'*.board-z.de',
	'California State University Stanislaus' => array('writing.csustan.edu'),
	'cannon.co.za',
	'columbosgarden.com',
	'*.communityhost.de',
	'connectionone.org',
	'deathwinds.com',
	'deforum.org',
	'delayedreaction.org',
	'deproduction.org',
	'dc503.org',
	'dialadeck.com',		///images/
	'dre-centro.pt',
	'Duke University' => array('devel.linux.duke.edu'),
	'*.esen.edu.sv',
	'forums.drumcore.com',
	'dundeeunited.org',
	'energyglass.com.ua',
	'equitas.com.au',		///images/mim3/, no index.html
	'exclusivehotels.com',
	'info.ems-rfid.com',
	'fairyfunkyard.com',	///images_files/files/
	'farrowhosting.com',	// by Paul Farrow (postmaster at farrowcomputing.com)
	'fbwloc.com',
	'.fhmcsa.org.au',
	'findyourwave.co.uk',
	'frogcafe.net',
	'plone4.fnal.gov',
	'freeforen.com',
	'funkdoc.com',
	'funnyclipcentral.com',
	'gearseds.com',
	'generationrice.com',
	'ghettojava.com',
	'gnacademy.org',
	'*.goodboard.de',
	'GreenDayVideo.net' => array(
		'greendayvideo.net',
		'espanol.greendayvideo.net',
	),
	'Hampton University' => array('calipsovalidation.hamptonu.edu'),
	'Harvard Law School' => array('blogs.law.harvard.edu'),
	'helpiammoving.com',
	'homepage-dienste.com',
	'*.hostistry.com',		// by support at hostistry.com, hostistry at gmail.com
	'Howard University' => array('networks.howard.edu'),
	'hullandhull.com',
	'Huntington University' => array('huntington.edu'),
	'huskerink.com',
	'.hyba.info',
	'ideas4you.com',	///photos/
	'inda.org',
	'*.indymedia.org',	// by abdecom at riseup.net
	'instantbulletin.com',
	'internetincomeclub.com',
	'*.inventforum.com',
	'Iowa State University' => array('boole.cs.iastate.edu'),
	'ipwso.org',
	'irha.info',		// by David Rosenberg (drosen3 at luc.edu),
	'ironmind.com',
	'skkustp.itgozone.com',	// hidden JavaScript
	'jazz2online.com',
	'.jloo.org',
	'.juegohq.com',				// 62.37.117.18(*.static.abi.uni2.es) by admin at juegohq.com, gamble
	'kjon.com',
	'Kazan State University' => array(
		'dir.kzn.ru',
		'sys.kcn.ru',
	),
	'test.kernel.org',
	'kevindmurray.com',
	'kroegjesroutes.nl',
	'.legion.org',
	'Loyola Marymount University' => array('lmu.edu'),
	'forum.lixium.fr',
	'macfaq.net',
	'macvirus.org',		///board/
	'me4x4.com',
	'microbial-ecology.net',
	'minsterscouts.org',
	'morallaw.org',
	'morerevealed.com',
	'macronet.org',
	'mamiya.co.uk',
	'vcd.mmvcd.com',
	'Monmouth College' => array('department.monm.edu'),
	'mountainjusticemedia.org',
	'*.mybbland.com',
	'mydlstore.com',
	'*.netboardz.com',
	'North Carolina A&T State University' => array(
		'ncat.edu',
		'my.ncat.edu',
		'hlc.ncat.edu',
	),
	'placetobe.org',
	'Naropa University' => array(
		'naropa.edu',			///forum/
	),
	'users.nethit.pl',
	'nightclubvip.net',
	'njbodybuilding.com',
	'nlen.org',
	'North Carolina School of Science and Mathematics' => array(
		'neverland.ncssm.edu',	///include/web/forum/
		'grid.ncssm.edu',		///ncssm_grid/
	),
	'Sacred Heart Catholic Primary School' => array('sacredheartpymble.nsw.edu.au'),
	'ofcourseimright.com',	///cgi-bin/roundup/calsify/
	'offtextbooks.com',
	'ofimatika.com',
	'olympiafoto.com',		///images/
	'omakase-net',			// iframe
	'omikudzi.ru',
	'openchemist.net',
	'palungjit.com',
	'pataphysics-lab.com',
	'paypaldev.org',
	'paullima.com',
	'perl.org.br',
	'pfff.co.uk',
	'pimpo.com',			///_old_site/
	'pix4online.co.uk',
	'plone.dk',
	'preform.dk',
	'privatforum.de',
	'publicityhound.net',
	'puppylinux.net',		///news/content/counter/pages/
	'qea.com',
	'rbkdesign.com',
	'rehoboth.com',
	'rodee.org',
	'ryanclark.org',
	'*.reallifelog.com',
	'rkphunt.com',
	'rso-csp.org',			///bulletins/
	'.saasmar.ru',			// Jacked. iframe to banep.info on root, etc
	'sapphireblue.com',
	'saskchamber.com',
	'savevoorhees.org',
	'sdhmalenovice.cz',		//galerie/
	'selikoff.net',
	'serbisyopilipino.org',
	'setbb.com',
	'sharejesusinternational.com',
	'silver-tears.net',
	'Saint Martin\'s University' => array('homepages.stmartin.edu'),
	'.softpress.com',
	'southbound-inc.com',	// There is a <html>.gif (img to it168.com) 
	'fora.taniemilitaria.pl',	///phpbb
	'tehudar.ru',
	'Tennessee Tech University' => array('manila.tntech.edu'),
	'thebluebird.ws',
	'theosis.org',
	'*.thoforum.com',
	'troms-slekt.com',
	'theedgeblueisland.com',
	'toyshop.com.tw',		// /images/sigui/
	'torontoplace.com',
	'chat.travlang.com',
	'trickropingbylassue.com',
	'Truman State University' => array('mathbio.truman.edu'),
	'tuathadedannan.org',
	'txgotg.com',
	'tzaneen.co.za',
	'ugeavisen.com',		///style/
	'forums.ugo.com',
	'whole.cs.unibo.it',
	'The University of North Dakota' => array(
		'learn.aero.und.edu',
		'ez.asn.und.edu',
	),
	'The University of Alabama' => array('bama.ua.edu'),
	'unisonscotlandlaw.co.uk',
	'University of California' => array('classes.design.ucla.edu'),
	'University of Nebraska Lincoln' => array('ftp.ianr.unl.edu'),
	'University of Northern Colorado' => array('unco.edu'),
	'University of North Carolina' => array(
		'elm.cis.uncw.edu'	///testBoinc/
	),
	'University of Toronto' => array(
		'environment.utoronto.ca',
		'grail.oise.utoronto.ca',
		'utsc.utoronto.ca',
	),
	'University of Wisconsin' => array(
		'einstein.phys.uwm.edu'
	),
	'urgentclick.com',
	'vacant.org.uk',
	'Villa Julie College' => array('www4.vjc.edu'),
	'Vail Valley Foundation' => array('.vvf.org'),
	'wabson.org',
	'warping.to',		// Seems (a redirection site, but now) taken advantage of
	'webarch.com',		// by WebArchitects (webarch at insync.net)
	'weehob.com',
	'West Virginia University Parkersburg' => array('wvup.edu'),
	'williamsburgrentals.com',
	'wolvas.org.uk',
	'wookiewiki.org',
	'xsgaming.com',			// Jacked
	'.xthost.info',			// by Michael Renz (dhost at mykuhl.de)
	'Yahoo.com' => array(
		'blog.360.yahoo.com',
		'*.groups.yahoo.com',	///group/ * = es, finance, fr, games, lauch, ...
	),
	'yasushi.site.ne.jp',	// One of mixedmedia.net'
	'youthpeer.org',
	'*.zenburger.com',
	'Zope/Python Users Group of Washington, DC' => array('zpugdc.org'),
);

// --------------------------------------------------

$blocklist['C'] = array(

	// C: Sample setting of:
	// Exclusive spam domains
	//
	// Seems to have flavor of links, pills, gamble, online-games, erotic,
	// affiliates, finance, sending viruses, malicious attacks to browsers,
	// and/or mixed ones
	//
	// Please notify us about this list with reason:
	// http://pukiwiki.sourceforge.jp/dev/?BugTrack2/208

	// C-1: Domain sets (seems to be) born to spam you
	//
	// All buziness-related spam
	//'.biz'
	//
	'admin at seekforweb.com' => array(
		// by Boris (admin at seekforweb.com, bbmfree at yahoo.com)
		'.lovestoryx.com',
		'.loveaffairx.com',
		'.onmore.info',
		'.scfind.info',
		'.scinfo.info',
		'.webwork88.info',
	),
	'boss at bse-sofia.bg' => array(	// by Boris
		'.htewbop.org',
		'.kimm--d.org',
		'.gtre--h.org',
		'.npou--k.org',
		'.bres--z.org',
		'.berk--p.org',
		'.bplo--s.org',
		'.basdpo.org',
		'.jisu--m.org',
		'.kire--z.org',
		'.mertnop.org',
		'.mooa--c.org',
		'.nake--h.org',
		'.noov--b.org',
		'.suke--y.org',
		'.vasdipv.org',
		'.vase--l.org',
		'.vertinds.org',
	),
	'pokurim at gamebox.net' => array(	// by Thai Dong Changli
		'.aqq3.info',
		'.axa00.info',
		'.okweb11.org',
		'.okweb12.org',
		'.okweb13.org',
		'.okweb14.org',
	),
	'opezdol at gmail.com' => array(
		'.informazionicentro.info',
		'.notiziacentro.info',
	),
	'SomethingGen' => array(
		// 'CamsGen' by Lui Xeng Shou (camsgen at model-x.com)
		// 'CamsGen' by Sergey (buckster at hotpop.com)
		// 'BucksoGen', by Pronin Sergey (buckster at list.ru)
		// by Lee Chang (nebucha at model-x.com)
		'.adult-chat-world.info',	// by Lui
		'.adult-chat-world.org',	// by Lui
		'.adult-sex-chat.info',		// by Lui
		'.adult-sex-chat.org',		// by Lui
		'.adult-cam-chat.info',		// by Lui
		'.adult-cam-chat.org',		// by Lui
		'.dildo-chat.org',			// by Lui
		'.dildo-chat.info',			// by Lui
		// flirt-online.info is not CamsGen
		'.flirt-online.org',		// by Lui
		'.live-adult-chat.info',	// by Lui
		'.live-adult-chat.org',		// by Lui
		'.sexy-chat-rooms.info',	// by Lui
		'.sexy-chat-rooms.org',		// by Lui
		'.swinger-sex-chat.info',	// by Lui
		'.swinger-sex-chat.org',	// by Lui
		'.nasty-sex-chat.info',		// by Lui
		'.nasty-sex-chat.org',		// by Lui

		'.camshost.info',			// by Sergey
		'.camdoors.info',			// by Sergey
		'.chatdoors.info',			// by Sergey

		// 89.149.206.225 by Pronin
		'.dorozhka.info',
		'.kolonochka.info',
		'.knizhechka.info',
		'.krantik.info',
		'.lebedi.info',
		'.loshad.info',
		'.pechenka.info',
		'.porosenok.info',
		'.indyushonok.info',
		'.kotyonok.info',
		'.kozlyonok.info',
		'.magnoliya.info',
		'.svinka.info',
		'.svinya.info',
		'.televizorchik.info',
		'.tumbochka.info',
		'.zherebyonok.info',

		'.medvezhonok.org',			// 89.149.206.225 "BucksoGen 1.2b"

		'.adult-cam-chat-sex.info',		// by Lee
		'.adult-chat-sex-cam.info',		// 'CamsGen' by Lee
		'.live-chat-cam-sex.info',		// 'CamsGen' by Lee
		'.live-nude-cam-chat.info',		// 'CamsGen' by Lee
		'.live-sex-cam-nude-chat.info',	// 'CamsGen' by Lee
		'.sex-cam-live-chat-web.info',	// 'CamsGen' by Lee
		'.sex-chat-live-cam-nude.info',	// 'CamsGen' by Lee
		'.sex-chat-porn-cam.info',		// by Lee
	),
	'mital at topo20.org' => array(	// by Marcello Italianore
		'.trevisos.org',
		'.topo20.org',
	),
	'WellCams.com' => array(
		'.j8v9.info',		// by Boris Moiseev (borka at 132moiseev.com)
		'.wellcams.com',	// by Sergey Sergiyenko (studioboss at gmail.com)
		'.wellcams.biz',	// by Sergey Sergiyenko (studioboss at gmail.com)
	),
	'graz at rubli.biz' => array(	// by Chinu Hua Dzin
		'.besturgent.org',
		'.googletalknow.org',
		'.montypythonltd.org',
		'.supersettlemet.org',
		'.thepythonfoxy.org',
		'.ukgamesyahoo.org',
		'.youryahoochat.org',
	),
	'kikimas at mail.net' => array(	// Redirect to nb717.com etc
		'.dbsajax.org',
		'.acgt2005.org',
		'.gopikottoor.com',
		'.koosx.org',
		'.mmgz.org',
		'.zhiyehua.net',
	),
	'vdf at lovespb.com' => array(	// by Andrey
		'.02psa.info',
		'.1818u.org',
		'.18ew.info',
		'.43sexx.org',
		'.56porn.org',
		'.6discount.info',
		'.78porn.org',		// "UcoZ WEB-SERVICES"
		'.78rus.info',
		'.92ssex.org',		// "ForumGenerator"
		'.93adult.org',		// "ForumGenerator"
		'.aboutsr.info',
		'.aboutzw.info',
		'.all23.info',
		'.allvdf.info',
		'.buy-dge.info',
		'.buypo.info',
		'.canadausa.info',	// "UcoZ WEB-SERVICES"
		'.cv83.info',
		'.cvwifw.info',
		'.dabouts.info',
		'.eplot.info',		// by Beatrice C. Anderson (Beatrice.C.Anderson at spambob.com)
		'.fuck2z.info',		// "UcoZ WEB-SERVICES"-like design
		'.free01sa.info',
		'.frees1.info',
		'.freexz.info',
		'.ifree-search.org',
		'.lovespb.info',
		'.myso2.info',
		'.nb74u.info',
		'.ohhh2.info',
		'.ol43.info',
		'.olala18.info',
		'.oursales.info',
		'.pasian1.info',
		'.pldk.info',
		'.po473.info',
		'.pornr.info',		// "UcoZ WEB-SERVICES"
		'.poz2.info',
		'.qpf8j4d.info',
		'.saleqw.info',
		'.sexof.info',		// "UcoZ WEB-SERVICES"
		'.sexz18.info',
		'.sexy69a.info',
		'.shedikc.info',
		'.spb78.info',
		'.usacanadauk.info',
		'.v782mks.info',
		'.vny0.info',
		'.wifes1.info',
		'.xranvam.info',
		'.zenitcx.info',
		'.zxolala.info',
	),
	'nike.borzoff at gmail.com' => array(	// by Nike Borzoff, 'vdf at lovespb.com'-related
		'.care01.info',
		'.care02.info',
		'.care03.info',
		'.care04.info',
		'.care05.info',
		'.care06.info',
		'.care07.info',
		'.care08.info',
		'.care09.info',
		'.care10.info',
		'.kra1906.info',	// "UcoZ WEB-SERVICES"
		'.klastar01.info',
		'.klastar02.info',
		'.klastar03.info',
		'.klastar04.info',
		'.klastar05.info',
		'.klastar06.info',
		'.klastar07.info',
		'.klastar08.info',
		'.klastar09.info',
		'.klastar10.info',
		'.um20ax01.info',
		'.um20ax02.info',
		'.um20ax03.info',
		'.um20ax04.info',
		'.um20ax05.info',
		'.um20ax06.info',
		'.um20ax07.info',
		'.um20ax08.info',
		'.um20ax09.info',
		'.um20ax10.info',
	),
	'forrass at gmail.com' => array(	// by Ismail
		'.zmh01.info',
		'.zmh02.info',
		'.zmh03.info',
		'.zmh04.info',
		'.zmh05.info',
		'.zmh06.info',
		'.zmh07.info',
		'.zmh08.info',
		'.zmh09.info',
		'.zmh10.info',
	),
	'Varsylenko Vladimir and family' => array(
		// by Varsylenko Vladimir (vvm_kz at rambler.ru)
		// by David C. Lack (David.C.Lack at dodgeit.com)
		// by Kuzma V Safonov (admin at irtes.ru)
		// by Petrov Vladimir (vvm_kz at rambler.ru)
		// by LAURI FUNK (vvm_kz at rambler.ru)

		// 64.92.162.210(*.static.reverse.ltdomains.com)
		'.abrek.info',				// by Petrov
		'.allsexonline.info',		// by Varsylenko
		'.d1rnow.info',				// by Petrov
		'.doxer.info',				// Petrov
		'.freeforworld.info',		// Varsylenko
		'.goodworksite.info',		// Varsylenko
		'.onall.info',				// by Varsylenko
		'.powersiteonline.info',	// by Varsylenko
		'.rentmysite.info',			// by Varsylenko
		'.sexdrink.info',			// by Petrov
		'.siteszone.info',			// by Varsylenko
		'.sfup.info',				// by Petrov
		'.superfreedownload.info',	// by Varsylenko
		'.superneeded.info',		// by Varsylenko
		'.srup.info',				// by Petrov

		// 66.235.185.143(*.svabuse.info)
		'.accommodationwiltshire.com',	// by Petrov
		'.levines.info',			// by Petrov
		'.sernost.info',			// by Petrov
		'.sexvideosite.info',		// by Petrov
		'.vvsag.info',				// by Petrov

		// 81.0.195.134
		'.michost.info',			// by LAURI
		'.parther.info',			// by LAURI

		// 88.214.202.100
		'.gitsite.info',			// by Petrov
		'.organiq.info',			// by Petrov
		'.yoursitedh.info',			// by Petrov

		// 217.11.233.58 by Petrov
		'.mp3vault.info',

		// DNS time out or failed
		'.bequeous.info',			// by Davi
		'.sopius.info',				// by Kuzma
		'.sovidopad.info',			// by Kuzma
		'.yerap.info',				// by Kuzma
	),
	'zhu1313 at mail.ru' => array(	// by Andrey Zhurikov
		'.flywebs.com',
		'.hostrim.com',
		'.playbit.com',
	),
	'webmaster at dgo3d.info' => array(	// by Son Dittman
		'.bsb3b.info',
		'.dgo3d.info',
		'.dgo5d.info',
	),
	'cooler.infomedia at gmail.com' => array(
		'.diabetescarelink.com',
		'.firstdebthelp.com',
	),
	'hostmaster at astrons.com' => array(	// by Nikolajs Karpovs
		'.pokah.lv',
		'.astrons.com',
	),
	'seocool at bk.ru' => array(	// by Skar
		'.implex3.com',
		'.implex6.info',
		'.softprof.org',
	),
	'Caslim.info' => array(
		'.caslim.info',		// by jonn22 (jonnmarker at yandex.ru)
		'.tops.gen.in',		// by Kosare (billing at caslim.info)
	),
	'foxwar at foxwar.ispvds.com' => array(	// by Alexandr, Hiding google?q=
		'.777-poker.biz',
		'.porn-11.com',
	),
	'Conto.pl' => array(
		'.8x.pl',		// domena at az.pl 
		'.3x3.pl',		// by biuro at nazwa.pl
		'.conto.pl',	// by biuro at nazwa.pl
		'.guu.pl',		// by conto.pl (domena at az.pl)
		'.xcx.pl',		// domena at az.pl
		'.yo24.pl',		// domena at az.pl
	),
	'mail at pcinc.cn' => array(
		// Domains by Lin Zhi Qiang
		// NOTE: pcinc.cn -- 125.65.112.13 by Lin Zhi Qiang (lin80 at 21cn.com)

		// 125.65.112.11
		// The same IP: web001.cdnhost.cn, *.w19.cdnhost.cn
		'shoopivdoor.w19.cdnhost.cn',	// web001.cdnhost.cn
		'.shoopivdoor.com',

		// 125.65.112.12
		// The same IP: web003.cdnhost.cn, *.w16.cdnhost.cn
		'.hosetaibei.com',
		'.playsese.com',

		// 125.65.112.13
		// The same IP: web006.cdnhost.cn, *.w9.cdnhost.cn
		'.ahatena.com',
		'.asdsdgh-jp.com',
		'.conecojp.net',
		'.game-oekakibbs.com',
		'.geocitygame.com',
		'.gsisdokf.net',
		'.korunowish.com',
		'.netgamelivedoor.com',
		'.soultakerbbs.net',
		'.yahoo-gamebbs.com',
		'.ywdgigkb-jp.com',

		// 125.65.112.14
		// The same IP: web007.cdnhost.cn, *.w12.cdnhost.cn
		'.acyberhome.com',
		'.bbs-qrcode.com',
		'.gamesroro.com',
		'.gameyoou.com',
		'.gangnu.com',
		'.goodclup.com',
		'.lineage321.com',
		'.linkcetou.com',
		'.love888888.com',
		'.ragnarok-bbs.com',
		'.ragnarok-game.com',
		'.rmt-navip.com',
		'.watcheimpress.com',

		// 125.65.112.15
		// The same IP: web008.cdnhost.cn, *.w11.cdnhost.cn
		'.18girl-av.com',
		'.aurasoul-visjp.com',
		'.gamaniaech.com',
		'.game-mmobbs.com',
		'.gameslin.net',
		'.gemnnammobbs.com',
		'.gogolineage.net',
		'.grandchasse.com',
		'.jpragnarokonline.com',
		'.jprmthome.com',
		'.maplestorfy.com',
		'.netgamero.net',
		'.nothing-wiki.com',
		'.ourafamily.com',
		'.ragnarok-sara.com',
		'.rmt-lineagecanopus.com',
		'.rmt-ranloki.com',
		'.rogamesline.com',
		'.roprice.com',
		'.tuankong.com',
		'.twreatch.com',

		// 125.65.112.22
		// The same IP: web013.cdnhost.cn
		'.lzy88588.com',
		'.ragnaroklink.com',

		// 125.65.112.24
		'.rmtfane.com',
		'.fc2weday.com',
		'.nlftweb.com',

		// 125.65.112.27
		'.i520i.com',
		'.sunwayto.com',

		// 125.65.112.31
		// The same IP: web016.cdnhost.cn
		'.twyaooplay.com',

		// 125.65.112.32
		// The same IP: web037.cdnhost.cn
		'.emeriss.com',
		'.raginfoy.com',
		'.ragnarokgvg.com',
		'.rentalbbs-livedoor.com',
		'.romaker.com',
		'.sagewikoo.com',
		'.samples112xrea.com',
		'.wiki-house.com',

		// 125.65.112.49
		'.chaosx0.com',

		// 125.65.112.88
		// The same IP: web015.cdnhost.cn
		'.a-hatena.com',
		'.biglobe-ne.com',
		'.blogplaync.com',
		'.din-or.com',
		'.dtg-gamania.com',
		'.fcty-net.com',
		'.game-fc2blog.com',
		'.gameurdr.com',
		'.getamped-garm.com',
		'.interzq.com',
		'.linbbs.com', 			// by zeng xianming (qqvod at qq.com). www.linbbs.com is the same ip of www.game-fc2blog.com(222.77.185.101) at 2007/03/11
		'.luobuogood.com',
		'.ragnarok-search.com',
		'.rinku-livedoor.com',

		// 125.65.112.90
		'.gtvxi.com',

		// 125.65.112.91
		// The same IP: web004.cdnhost.cn
		'.6828teacup.com',
		'.blog-livedoor.net',
		'.cityblog-fc2web.com',
		'.deco030-cscblog.com',
		'.imbbs2t4u.com',
		'.k5dionne.com',
		'.lineagejp-game.com',
		'.mbspro6uic.com',
		'.slower-qth.com',
		'.wikiwiki-game.com',

		// 125.65.112.93
		// The same IP: web022.cdnhost.cn
		'.aaa-livedoor.net',
		'.cityhokkai.com',		// web022.cdnhost.cn
		'.fanavier.net',
		'.geocitylinks.com',	// web022.cdnhost.cn
		'.kuronowish.net',		// web022.cdnhost.cn
		'.ro-bot.net',

		// 125.65.112.95
		// The same IP: web035.cdnhost.cn, web039.cdnhost.cn
		'.23styles.com',
		'.aehatena-jp.com',
		'.ameblog-jp.net',
		'.antuclt.com',
		'.blog-ekndesign.com',
		'.d-jamesinfo.com',
		'.editco-jp.com',
		'.ezbbsy.com',
		'.extd-web.com',
		'.fotblong.com',
		'.game62chjp.net',
		'.gamegohi.com',
		'.gamesmusic-realcgi.net',
		'.gamesragnaroklink.net',
		'.homepage-nifty.com',
		'.ie6xp.com',
		'.irisdti-jp.com',
		'.jklomo-jp.com',
		'.jpxpie6-7net.com',
		'.lian-game.com',
		'.lineage-bbs.com',
		'.lineage1bbs.com',
		'.livedoor-game.com',
		'.litcan.com',
		'.lovejpjp.com',
		'.m-phage.com',
		'.muantang.com',
		'.plusd-itmedia.com',
		'.runbal-fc2web.com',
		'.saussrea.com',
		'.tooalt.com',
		'.toriningena.net',
		'.yahoodoor-blog.com',
		'.yy14-kakiko.com',

		// 125.65.112.137
		'.clublineage.com',

		// 228.14.153.219.broad.cq.cq.dynamic.163data.com.cn
		'.kaukoo.com',			// 219.153.14.228, by zeng xianming (expshell at 163.com)
		'.linrmb.com',			// 219.153.14.228, by zeng xianming (qqvod at qq.com)

		'.ptxk.com',			// 222.73.236.239, by zeng xianming (zxmdiy at gmail.com)
		'.rormb.com',			// 222.73.236.239, by zeng xianming (qqvod at qq.com)

		'.games-nifty.com',		// 255.255.255.255 now
		'.homepage3-nifty.com',	// 255.255.255.255 now
	),
	'caddd at 126.com' => array(
		'.chengzhibing.com',	// by chen gzhibing
		'.jplinux.com',			// by lian liang
		'.lineageink.com',		// by cai zibing, iframe to goodclup.com
		'.lineagekin.com',		// by cai zibing, iframe to goodclup.com
		'.tooplogui.com',		// by zibing cai
		'.twsunkom.com',		// by guo zhi wei
		'.twmsn-ga.com',		// by guo zhi wei, iframe to grandchasse.com
	),
	'nuigiym2 at 163.com' => array(	// by fly bg
		'.linainfo.net',		// Seems IP not allocated now
		'.lineagalink.com',		// 220.247.157.99
		'.lineagecojp.com',		// 61.139.126.10
		'.ragnarokonlina.com',	// 220.247.158.99
	),
	'aakin at yandex.ru' => array(
		// 89.149.206.225(*.internetserviceteam.com) by Baer
		'.entirestar.com',
		'.joppperl.info',
		'.onlinedrugsdirect.com',
		'.pilkazen.info',
		'.superbuycheap.com',
		'.supersmartdrugs.com',
		'.thecheappillspharmacy.com',
		'.topdircet.com',
	),
	'newblog9 at gmail.com' => array(	// by jiuhatu kou
		'.tianmieccp.com',
		'.xianqiao.net',
	),
	'm.frenzy at yahoo.com' => array(	// by Michael
		'.p5v.org',
		'.j111.net',
		'.searchhunter.info',
		'.soft2you.info',
		'.top20health.info',
		'.top20ringtones.info',
		'.top20travels.info',
		'.v09v.info',
		'.x09x.info',
		'.zb-1.com',
	),
	'serega555serega555 at yandex.ru' => array(	// by Lebedev Sergey
		'.bingogoldenpalace.info',
		'.ccarisoprodol.info',
		'.ezxcv.info',
		'.isuperdrug.com',
		'.pharmacif.info',
		'.pornsexteen.biz',
		'.ugfds.info',
		'.vviagra.info',
	),
	'anatolsenator at gmail.com' => array(	// by Anatol
		'.cheapestviagraonline.info',
		'.buyphentermineworld.info'
	),
	'webmaster at mederotica.com' => array(
		'.listsitepro.com',		// by VO Entertainment Inc (webmaster at mederotica.com)
		'.testviagra.org',		// by Chong Li (chongli at mederotica.com)
		'.viagra-best.org',		// by Chong Li (chongli at mederotica.com)
		'.viagra-kaufen.org',	// by Chong Li (chongli at mederotica.com)
	),
	'gray at trafic.name' => array(	// by Billing Name:Gray, Billing Email:gray at trafic.name
		'.auase.info',		// by ilemavyq7461 at techie.com
		'.axeboxew.info',	// by zygeu220 at writeme.com
		'.boluzuhy.info',	// by pikico5419 at post.com
		'.ekafoloz.info',	// by nuzunyly8401 at techie.com
		'.ejixyzeh.info',	// by vubulyma5163 at consultant.com
		'.emyfyr.info',		// by osiqabu9669 at writeme.com
		'.exidiqe.info',	// by kufyca5475 at mail.com
		'.gerucovo.info',	// by apegityk7224 at writeme.com
		'.gubiwu.info',		// by lywunef6532 at iname.com
		'.ijizauax.info',	// by ysauuz2341 at iname.com
		'.ixahagi.info',	// 70.47.89.60 by famevi9827 at email.com
		'.jiuuz.info',		// by meqil6549 at mail.com
		'.nudetar.info',	// by vohepafi3536 at techie.com
		'.nipud.info',		// by bohox9872 at mindless.com
		'.mejymik.info',	// by fiqiji3529 at cheerful.com
		'.mylexus.info',	// Billing Email is simhomer12300 at mail.com, but posted at the same time, and ns *.grayreseller.com
		'.olasep.info',		// by lizon8506 at mail.com
		'.oueuidop.info',	// by arytyb6913 at europe.com
		'.oviravy.info',	// by amyuu3883 at london.com
		'.ovuri.info',		// by exumaxyt1371 at consultant.com
		'.ragibe.info',		// by ehome4458 at myself.com
		'.ucazib.info',		// by gorare7222 at consultant.com
		'.udaxu.info',		// by gubima4007 at usa.com
		'.ulycigop.info',	// by unodyqil6241 at mindless.com
		'.vubiheq.info',	// by uisujih5849 at hotmail.com
		'.xyloq.info',		// 70.47.89.60 by yuunehi8586 at myself.com
		'.yvaxat.info',		// by koqun9660 at mindless.com
		'.yxyzauiq.info',	// by robemuq8455 at cheerful.com
	),
	'Carmodelrank.com etc' => array(
		// by Brianna Dunlord (briasmi at yahoo.com)
		// by Tim Rennei (TimRennei at yahoo.com), redirect to amaena.com (fake-antivirus)
		// by Alice T. Horst (Alice.T.Horst at pookmail.com)
		'.carmodelrank.com',// by Brianna
		'.cutestories.net',	// by Brianna
		'.sturducs.com',
		'.bestother.info',	// by Tim
		'.premiumcasinogames.com',	// by Brianna)
		'.yaahooo.info',	// by Alice
	),
	'aliacsandr at yahoo.com' => array(
		'.cubub.info',				// "Free Web Hosting"
	),
	'aliacsandr85 at yahoo.com' => array(
		// by Dr. Portillo or Eva Sabina Lopez Castell
		'.xoomer.alice.it',			// "Free Web Hosting"
		'.freebloghost.org',		// "Free Web Hosting" by Dr.
		'.freeprohosting.org',		// "Free Web Hosting" by Dr.
		'.googlebot-welcome.org',	// "Free Web Hosting" by Dr.
		'.icesearch.org',			// "Free Web Hosting" by Eva
		'.phpfreehosting.org',		// "Free Web Hosting" by Dr.
		'.sashawww.info',			// "Free Web Hosting" by Dr.
		'.sashawww-vip-vip.org',	// "Free Web Hosting" by Dr.
		'.topadult10.org',			// "Free Web Hosting" by Eva
		'.xer-vam.org',				// "Ongline Catalog" by Dr.
		'.xxxse.info',				// "Free Web Hosting" by Eva
		'.viagra-price.org',		// by Eva
		'.vvsa.org',				// "Free Web Hosting" by Eva
		'.free-webhosts.com',		// "Free Web Hosting" by Free Webspace
	),
	'.onegoodauto.org',				// "Free Web Hosting" by sqrtv2 at gmail.com
	'Something-Gamble' => array(
		// Gamble: Roulette, Casino, Poker, Keno, Craps, Baccarat
		'.atonlineroulette.com',			// by Blaise Johns
		'.atroulette.com',					// by Gino Sand
		'.betting-123.com',					// by Joana Caceres
		'.betting-i.biz',					// by Joaquina Angus
		'.casino-challenge.com',			// by Maren Camara
		'.casino-gambling-i.biz',			// by Giselle Nations
		'.casino-italian.com',				// by Holley Yan
		'.casino123.net',					// by Ta Baines
		'.casinohammamet.com',				// by Inger Barhorst
		'.casinoqz.com',					// by Berenice Snow
		'.casinos-777.net',					// by Iona Ayotte
		'.crapsok.com',						// by Devon Adair,
		'.dcasinoa.com',					// by August Hawkinson
		'.e-poker-4u.com',					// by Issac Leibowitz
		'.free-dvd-player.biz',				// by Rosario Kidd
		'.florida-lottery-01.com',			// by Romeo Dillon
		'.gaming-123.com',					// by Jennifer Byrne
		'.kenogo.com',						// by Adriane Bell
		'.mycaribbeanpoker.com',			// by Andy Mullis
		'.onbaccarat.com',					// by Kassandra Dunn
		'.online-experts.biz',				// by Liberty Helmick
		'.onlinepoker-123.com',				// by Andrea Feaster
		'.playpokeronline-123.com',			// by Don Lenard
		'.poker-123.com',					// by Mallory Patrick (Mallory_Patrick at marketing-support.info)
		'.texasholdem123.com',				// by Savion Lasseter
		'.texasholdem-777.com',				// by Savanna Lederman
		'.the-casino-directory-1715.us',	// by Thora Oldenburg
		'.the-craps-100.us',				// by Lorrine Ripley
		'.the-free-online-game-913.us',		// by Kanesha Clem
		'.the-free-poker-1798.us',			// by Elaina Witte
		'.the-las-vegas-gambling-939.us',	// by Jesusita Hageman
		'.the-online-game-poker-1185.us',	// by Merna Bey
		'.the-playing-black-jack.com',		// by Kristine Brinker
		'.the-poker-1082.us',				// by Kristofer Boldt
		'.the-rule-texas-hold-em-2496.us',	// by Melvina Stamper
		'.the-texas-strategy-holdem-1124.us',	// by Neda Frantz
		'.the-video-black-jack.com',		// by Jagger Godin
	),
	'Something-Insurance' => array(
		// Car / Home / Life / Health / Travel insurance, Loan finance, Mortgage refinance
	
		// 0-9
		'.0q.org',						// by Shamika Curtin, "Online car insurance information"
		'.1-bookmark.com',				// by Sonia Snyder, "Titan auto insurance information"
		'.1day-insurance.com',			// by Kelsie Strouse, "Car insurance costs"
		'.1upinof.com',					// by Diego Johnson, "Car insurance quote online uk resource"
		'.18wkcf.com',					// by Lexy Bohannon
		'.2001werm.org',				// by Raphael Rayburn
		'.2004heeparea1.org',			// by Dinorah Andrews
		'.21nt.net',					// by Jaida Estabrook
		'.3finfo.com',					// by Damian Pearsall
		'.3somes.org',					// by Mauro Tillett
		'.453531.com',					// by Kurt Flannery
		'.4freesay.com',				// by Eloy Jones
		'.5ssurvey.com',				// by Annamarie Kowalski
		'.8-f22.com',					// by Larraine Evers
		'.9q.org',						// by Ami Boynton

		// A
		'.a40infobahn.com',				// by Amit Nguyen
		'.a4h-squad.com',				// by Ross Locklear
		'.aac2000.org',					// by Randi Turner
		'.aaadvertisingjobs.com',		// by Luciano Frisbie
		'.acahost.com',					// by Milton Haberman
		'.aconspiracyofmountains.com',	// by Lovell Gaines
		'.acornwebdesign.co.uk',		// by Uriel Dorian
		'.activel-learning-site.com',	// by Mateo Conn
		'.ad-makers.com',				// by Shemeka Arsenault
		'.ada-information.org',			// by Josef Osullivan
		'.adelawarerefinance.com',		// by Particia Mcmillan, "Delaware refinance advisor"
		'.adult-personal-ads-e-site.info',	// by Nery Ainsworth
		'.aequityrefinance.com',		// by Jadwiga Duckworth
		'.aerovac-hotpress.com',		// by Trey Marlow
		'.agfbiosensors.com',			// by Lionel Dempsey
		'.ahomeloanrefinance.com',		// by Leslie Kinser
		'.affordablerealestate.net',	// by Season Otoole
		'.ahomerefinancingloans.com',	// by Julie Buck, "Home refinancing loans guide"
		'.ahouserefinance.com',			// by Young Alley
		'.akirasworld.com',				// by Piper Sullivan
		'.alderik-production.com',		// by Joan Stiles
		'.alltechdata.com',				// by Dom Laporte
		'.amconllc.com',				// by Syble Benjamin
		'.amobilehomerefinancing.com',	// by Clyfland Buckley, "Mobile home refinancing"
		'.amortgagerefinancepennsylvania.com',	// by Richard Battle, "Mortgage refinance pennsylvania articles"
		'.angelandcrown.net',			// by Claretta Najera
		'.ankoralina.com',				// by Eladia Demers
		'.antiquegoldmine.com',			// by Keena Marlow
		'.aquinosotros.com',			// by Nanci Prentice
		'.architectionale.com',			// by Wilbur Cornett
		'.arcreditcards.com',			// by Ecgbeorht Stokes, "Ameriquest credit cards articles"
		'.arefinancebadcredit.com',		// by Isaac Mejia, "Refinance bad credit"
		'.arefinancehome.com',			// by Duane Doran
		'.arefinancinghome.com',		// by Ike Laney
		'.athletic-shoes-e-shop.info',	// by Romelia Money
		'.auction-emall-site.info',		// by Dayle Denman
		'.auctions-site.info',			// by Cammie Chiu, "Online loan mortgage info"
		'.auto-buy-rite.com',			// by Asuncion Buie
		'.axxinet.net',					// by Roberta Gasper
		'.azimutservizi.com',			// by Ethelene Brook
		'.azstudies.org',				// by Bernardina Walden

		// B
		'.babtrek.com',					// by Simonette Mcbrayer
		'.babycujo.com',				// by Francisco Akers
		'.bakeddelights.com',			// by Dave Evenson
		'.bbcart.com',					// by Lucio Hamlin
		'.berlin-hotel-4u.com',			// by Grisel Tillotson
		'.best-digital-phone.us',		// by Meghann Crockett
		'.bjamusements.com',			// by Lurlene Butz
		'.blursgsu.com',				// by Weston Killian
		'.bookwide.net',				// by Tequila Zacharias
		'.boreholes.org',				// by Flora Reed
		'.breathingassociaiton.org',	// by Alfred Crayton
		'.birdingnh.com',				// by Donald Healy
		'.bisdragons.org',				// by Lupe Cassity
		'.blcschools.net',				// by Alycia Jolly
		'.bronte-foods.com',			// by Kary Pfeiffer
		'.buckscountyneighbors.org',	// by Maile Gaffney
		'.buffalofudge.com',			// by Mable Whisenhunt
		'.burlisonforcongress.com',		// by Luann King
		'.byairlinecreditcard.com',		// by Cali Stevenson, "Airline credit card search"
		'.byplatinumcard.com',			// by Pearl Cross, "Discover platinum card info"

		// C
		'.cabanes-web.com',				// by Vaughn Latham
		'.cardko.com',					// by Terris Cain, "Chase visa card search"
		'.cardpose.com',				// by Deerward Gross, "Gm mastercard articles"
		'.checaloya.com',				// by Susana Coburn
		'.calvarychapelrgvt.org',		// by Karan Kittle
		'.cameras-esite.info',			// by Karlee Frisch
		'.cancerkidsforum.org',			// by Samson Constantino
		'.ccchoices.org',				// by Kenia Cranford
		'.ccupca.org',					// by Evonne Serrano
		'.celebratemehome.com',			// by Soraya Tower
		'.centerfornourishingthefuture.org',	// by Elisa Wilt
		'.chelseaartmmuseum.org',		// by Kayla Vanhorn
		'.choose-shoes.net',			// by Geoffrey Setser
		'.churla.com',					// by Ollie Wolford
		'.circuithorns.co.uk',			// by Nathanial Halle
		'.clanbov.com',					// by Donell Hozier
		'.cnm-ok.org',					// by Thalia Moye
		'.coalitioncoalition.org',		// by Ned Macklin
		'.consoleaddicts.com',			// by Dorla Hoy
		'.counterclockwise.net',		// by Melynda Hartzell
		'.codypub.com',					// by Mercedes Coffman
		'.comedystore.net',				// by Floy Donald
		'.covsys.co.uk',				// by Abby Jacey
		'.cpusa364-northsacramento.com',	// by Dannette Lejeune
		'.craftybidders.com',			// by Dannie Lazo
		'.credit-card-finder.net',		// by Mellie Deherrera
		'.credit-cards-4u.info',		// by Antonina Hil, "Credit cards info"
		'.creditcardstot.com',			// by Bobby Alvarado, "Shell credit cards"
		'.ctwine.org',					// by Hailey Knox

		// D
		'.dazyation.com',				// by Louis Strasser
		'.deepfoam.org',				// by Ethelyn Southard
		'.debt-fixing.com',				// by Dagny Rickman
		'.dgmarketingwebdesign.com',	// by Nubia Lea
		'.domainadoption.com',			// by Breann Pappas
		'.diannbomkamp.com',			// by Russel Croteau
		'.dictionary-spanish.us',		// by Jacki Gilbreath
		'.dictionary-yahoo.us',			// by Lili Mitchem
		'.digital-camera-review-esite.info',	// by Milagros Jowers
		'.digital-cameras-esite.info',	// by Milan Jolin
		'.dnstechnet.net',				// by Tamera Oman
		'.drivenbydata.org',			// by Katherine Noyes
		'.dtmf.net',					// by Micki Slayton
		'.domainsfound.com',			// by Blossom Lively

		// E
		'.ecstacyabuse.net',			// by Alana Knight
		'.e-digital-camera-esite.info',	// by Romaine Cress
		'.eda-aahperd.org',				// by Kaliyah Hammonds
		'.eldorabusecenter.org',		// by Annabella Oneal
		'.emicorporation.com',			// by (Deangelo_Mikayla at marketing-support.info)
		'.encaponline.com',				// by Patrick Keel
		'.ez-shopping-online.com',		// by Gail Bartlett

		// F
		'.faithfulwordcf.com',			// by Bart Weeks
		'.fammedassoc.com',				// by Joshua Nelson
		'.federalministryoffinance.net',	// by Jeffry Mcmillan
		'.f00k.org',					// by Leslie Chapman
		'.foreignrealtions.org',		// by Krystal Hawley
		'.fortwebsite.org',				// by Kristina Motley
		'.fotofirstdigital.com',		// by Tad Whitfield
		'.foundationcommons.org',		// by Caryn Eskew
		'.fraisierest-alexandre.com',	// by Dwayne Douglas
		'.freaky-cheats.com',			// by Al Klein
		'.free--spyware.com',			// by Nikki Contreras
		'.french-home-finance-consultant.info',	// by Santana Melton
		'.fuel-tax-software-advisor.info',	// by Derrick Snyder

		// G
		'.gaintrafficfast.com',			// by Lila Meekins
		'.gaygain.org',					// by Shell Davila
		'.gcaaa.com',					// by Vallie Jaworski
		'.generalsysteme.com',			// by Cale Vogel
		'.generation4games.co.uk',		// by Sonya Graham
		'.german-dictionary.us',		// by Rex Daniel
		'.gilmerrec.com',				// by Leighann Guillory
		'.glenthuntly-athletics.com',	// by Julee Hair
		'.glorybaskets.com',			// by Lynette Lavelle
		'.goconstructionloan.com',		// by Willis Monahan
		'.gohireit.com',				// by Bertha Metzger
		'.godcenteredpeople.com',		// by Jaycee Coble

		// H
		'.healthinsuranceem.com',		// by Justin Munson
		'.hearthorizon.info',			// by Kory Session
		'.hegerindustrial.com',			// by Toni Wesley
		'.herzequip.com',				// by Princess Dunkle
		'.hglcms.org',					// by Gladwin Ng
		'.hipanoempresa.com',			// by Shannon Staub
		'.hitempfurnaces.com',			// by Rebbeca Jaeger
		'.horse-racing-result.com',		// by Rodney Reynolds
		'.hueckerfamily.com',			// by Hershel Sell

		// I
		'.ilove2win.com',				// by Lamont Dickerson
		'.ilruralassistgrp.org',		// by Moises Hauser
		'.imageonsolutions.com',		// by Porsche Dubois
		'.infoanddatacenter.com',		// by Eva Okelley
		'.islamfakta.org',				// by Goldie Boykin
		'.ithomemortgage.com',			// by Adelaide Towers
		'.iyoerg.com',					// by Madyson Gagliano

		// J
		'.jeffaxelsen.com',				// by Daphne William
		'.jeffreyf.net',				// by Vito Platt
		'.johnmartinsreality.com',		// by Pamela Larry
		'.johnsilvers.net',				// by Silver Battaglia

		// K
		'.kcgerbil.org',				// by Marisa Thayer
		'.kdc-phoenix.com',				// by Salma Shoulders
		'.kingscreditcard.com',			// by Sean Parsons, "Credit card info"
		'.kosove.org',					// by Darwin Schneider

		// L
		'.leading-digital-camera-esite.info',	// by Charles Moore, "Online home loan articles"
		'.letsgokayaking.net',			// by Winnie Adair
		'.libertycabs.com',				// by Adela Bonds
		'.liquor-store-cellar.info',	// by Hugh Pearson
		'.locomojo.net',				// by Marco Harmon
		'.lodatissimo.com',				// by Adrian Greeson
		'.lsawc.org',					// by Lara Han
		'.lycos-test.net',				// by Rigoberto Oakley

		// M
		'.macro-society.com',			// by Venessa Hodgson
		'.marthasflavorfest.com',		// by Ahmad Lau
		'.martin-rank.com',				// by Cathleen Crist
		'.maryandfrank.org',			// by Theodore Apodaca
		'.masterkwonhapkido.com',		// by Misty Graham
		'.maxrpm-demo.com',				// by Cristal Cho
		'.mechanomorphic.com',			// by Stanford Crow
		'.mepublishing.net',			// by Karly Fleenor
		'.meyerlanguageservices.co.uk',	// by Breana Kennedy
		'.metwahairports.com',			// by Nan Kitchen
		'.middle-eastnews.com',			// by Tybalt Altmann
		'.mikepelchy.com',				// by Sherly Pearson
		'.milpa.org',					// by Nelly Aguilera
		'.modayun.com',					// by Camilla Velasco
		'.moonstoneerp.com',			// by Garret Salmon
		'.morosozinho.com',				// by Lenore Tovar
		'.morphadox.com',				// by Hung Zielinski
		'.moscasenlared.com',			// by Tera Gant
		'.sdjavasig.com',				// by Gia Swisher
		'.mpeg-radio.com',				// by Sincere Beebe
		'.mrg-now-yes.com',				// by Sparkle Gallegos
		'.mtseniorcenter.org',			// by Frederic Ortega
		'.mysteryclips.com',			// by Edward Ashford

		// N
		'.naavs.org',					// by Yuridia Gandy
		'.naval-aviation.org',			// by Roselle Campo
		'.navigare-ischia.com',			// by Arielle Coons
		'.ncredc.org',					// by Brenda Nye
		'.neonmotorsports.com',			// by Giovanna Vue
		'.nf-ny.com',					// by Yadira Hibbard
		'.ngfdyqva.com',				// by Emiliano Samples
		'.nicozone.com',				// by Blaine Shell
		'.nmbusinessroundtable.org',	// by Chantel Mccourt
		'.npawny.org',					// by Willard Murphy
		'.nysdoed.org',					// by Elric Delgadillo
		'.nyswasteless.org',			// by Shaylee Moskowitz
		'.nytech-ir.com',				// by Adrien Beals

		// O
		'.oadmidwest.com',				// by Gavin Kaplan
		'.oarauto.com',					// by Susann Merriman
		'.onairmilescard.com',			// by Tomoko Hart, "Air miles card information"
		'.onbusinesscard.com',			// by Farris Lane, "Gm business card"
		'.oncashbackcreditcard.com',	// by Ida Willis, "Cash back credit card articles"
		'.onimagegoldcard.com',			// by Roxanna Sims, "Imagine gold mastercard information"
		'.online-pills-24x7.biz',		// by Aide Hallock
		'.online-shopping-site-24x7.info',	// by Stacy Ricketts
		'.onlinehomeloanrefinance.com',	// by Chaz Lynch
		'.onlinehomeloanfinancing.com',	// by Humbert Eldridge
		'.onunicarehealthinsurance.com',	// by  Lawerence Paredes
		'.otterbayweb.com',				// by Maxwell Irizarry

		// P
		'.painting-technique.us',		// by Bryanna Tooley
		'.pakamrcongress.com',			// by Bryce Summerville
		'.patabney.com',				// by Kailyn Slone
		'.parde.org',					// by Ellie Yates
		'.participatingprofiles.com',	// by Jaelynn Meacham
		'.partnershipconference.org',	// by Alla Floyd
		'.pet-stars.com',				// by Carmon Luevano
		'.planning-law.org',			// by Trista Holcombe
		'.ppawa.com',					// by Evonne Scarlett
		'.precisionfilters.net',		// by Faustina Fell

		// Q
		'.qacards.com',					// by Perye Estrada, "Citi visa cards"
		'.quick-debt-consolidation.net',	// by Lala Marte
		'.quicktvr.com',				// by Vernell Crenshaw

		// R
		'.radicalsolutions.org',		// by Reece Medlin
		'.randallburgos.com',			// by Bradly Villa
		'.rcassel.com',					// by Janiah Gallant
		'.rearchitect.org',				// by Marcus Gaudet
		'.rent-an-mba.com',				// by Valentina Mcdermott
		'.reprisenashville.com',		// by Hester Khan
		'.reptilemedia.com',			// by Alicia Patel
		'.resellers2000.com',			// by Dedra Kennedy
		'.reverse-billing.com',			// by Lazaro Gluck
		'.richcapaldi.com',				// by Kya Haggard
		'.richformissouri.com',			// by Alanna Elston
		'.robstraley.com',				// by Leida Bartell
		'.rollingprairie-candlecompany.com',	// by Leigha Aker
		'.rpgbbs.com',					// by Leonel Peart
		'.ruralbusinessonline.org',		// by Lynsey Watters
		'.ruwomenscenter.org',			// by Vince Mclemore
		'.ryanjowens.com',				// by Janine Smythe

		// S
		'.sagarmathatv.org',			// by Liam Funke
		'.sakyathubtenling.org',		// by Liane Falgout
		'.sandiegolawyer.net',			// by Linnie Sommervill
		'.sandishaven.com',				// by Lino Soloman
		'.scienkeen.com',				// by Liza Navarra
		'.seimenswestinghouse.com',		// by Teresa Benedetto
		'.severios.com',				// by Isa Steffen
		'.sexual-hot-girls.com',		// by Viviana Bolton
		'.shakespearelrc.com',			// by Luciana Weaver
		'.shashran.org',				// by Adriel Humphries
		'.shoes-shop.us',				// by Austen Higginbotham
		'.skagitvalleybassanglers.com',	// by Necole Thiele
		'.skinsciencesalon.com',		// by Nena Rook
		'.smartalternative.net',		// by Nicki Lariviere
		'.sml338.org',					// by Nickole Krol
		'.smogfee.com',					// by Sienna Kimble
		'.sneakers-e-shop.info',		// by Nikki Fye
		'.spacewavemedia.com',			// by Thanh Gast
		'.softkernel.com',				// by Nicol Hummer
		'.stjoanmerrillville.com',		// by Hunter Beckham
		'.strelinger.com',				// by Arron Highsmith
		'.striking-viking.com',			// by Kylie Endsley
		'.sunnydeception.org',			// by Amaya Llora
		'.sunzmicro.com',				// by Goddard Arreola
		'.sv-iabc.org',					// by Braden Buck
		'.sykotick.com',				// by Pierce Knecht

		// T
		'.tbody.net',					// by Ormond Roman
		'.the-pizzaman.com',			// by Mario Ramsey
		'.the-shoes.us',				// by Alejandro Gaffney
		'.theborneocompany.com',		// by Bryanna Tooley
		'.theflashchannel.com',			// by Terrilyn Tam, "Loan financing info"
		'.thehomeschool.net',			// by September Concepcio
		'.thenewlywed.com',				// by Allegra Marra
		'.tigerspice.com',				// by Denis Mosser
		'.tnaa.net',					// by Jasmine Andress
		'.top-finance-sites.com',		// by Maryann Doud
		'.tradereport.org',				// by Bettie Sisk
		'.transmodeling.com',			// by Martine Button
		'.travel-01.net',				// by Jay Kim, "Refinance on line"
		'.tsaoc.com',					// by Heriberto Mcfall
		'.tsunamidinner.com',			// by Nannie Richey

		// U
		'.uhsaaa.com',					// by Risa Herbert
		'.ultradeepfield.org',			// by Bobby Ragland
		'.umkclaw.info',				// by Cammy Kern
		'.unitedsafetycontainer.com',	// by Shreya Heckendora
		'.usa-wolf.com',				// by Jacklyn Morrill
		'.usjobfair.com',				// by Lorina Burchette

		// V
		'.vacancesalouer.com',			// by Loris Bergquist
		'.vagents.com',					// by Lorna Beaudette, "Refinancing home loan info"
		'.valleylibertarians.org',		// by Lena Massengale
		'.vanderbiltevents.com',		// by Gannon Krueger
		'.vanwallree.com',				// by Michelina Donahue
		'.vcertificates.com',			// by Hyun Lamp
		'.vonormytexas.us',				// by Suzette Waymire

		// W
		'.washingtondc-areahomes.net',	// by Ailene Broome
		'.web-hosting-forum.net',		// by Deedra Breen, "Mortgage information"
		'.wolsaoh.org',					// by Daniela English
		'.worldpropertycatalog.com',	// by Aray Baxter

		// Y
		'.yankee-merchants.com',		// by Jackson Hinojosa
		'.yourbeachhouse.com',			// by Dedrian Ryals
		'.yourdomainsource.com',		// by Deems Weingarten

		// Z
		'.zkashan.com',					// by Evan Light
		'.zockclock.com',				// by Dorothea Guthrie
	),
	'Something-Drugs' => array(
		// Drugs / Pills / Diet
		'.adult-dvd-rental-top-shop.info',	// by Gregoria Keating
		'.abdelghani-shady.com',		// by Elly Alton
		'.bangbangfilm.com',			// by Davin Chou
		'.centroantequera.com',			// by Keon Kwiatkowski
		'.champagne-cellar.info',		// by Kandis Rizzo
		'.chix0r.org',					// by Christoper Baird
		'.discout-watches-deals.info',	// by Taunya Limon, Insurance -> Drugs?
		'.fantasticbooks-shop.com',		// by Kermit Ashley
		'.fast-cash-01.com',			// by Edgar Oliver
		'.ficeb.info',					// by Vaughn Jacobson, "Phentermine news"
		'.fn-nato.com',					// by Donny Dunlap
		'.gqyinran.com',				// by Alejandro Parks
		'.juris-net.com',				// by Rachelle Bravo
		'.leftpencey.com',				// by Aileen Ashby
		'.miamicaribbeancarnival.com',	// by Herminia Barrios
		'.nike-shoes-e-shop.info',		// by Machelle Groce, "Phentermine"
		'.palaceroyale.net',			// by Brycen Stebbins
		'.pocket-watches-deals.info',	// by Dorinda Stromberg
		'.regresiones.net',				// by Lauralee Smtih, "Online phentermine updates"
		'.yukissushi.com',				// by Donell Hozier
	),
	'Something-Others' => array(
		'.consulting-cu.com',			// by Albina Rauch, 404 not found
		'.dvd-rentals-top-shop.info',	// by Lashunda Pettway, 404 not found
	),
	'Something-NoApp' => array(
		'.auctioncarslisting.com',	// "No application configured at this url." by John Davis
		'.buy-cheap-hardware.com',	// "No application configured at this url." by Tim Morison (domains at sunex.ru)
		'.carsgarage.net',			// "No application configured at this url." by Zonen Herms, and Jimmy Todessky (seomate at gmail.com)
		'.digitshopping.net',		// "No application configured at this url." by Zonen Herms, and Jimmy Todessky (seomate at gmail.com)
		'.your-insurance.biz',		// "No application configured at this url." by Jimmy Todessky (seomate at gmail.com)
	),
	'Cortez and family' => array(
		// by Cortez Shinn (info at goorkkjsaka.info), or Rico Laplant (info at nnjdksfornms.info)
		'.dronadaarsujf.info',	// by Cortez
		'.fromnananaref.info',	// by Cortez
		'.goorkkjsaka.info',	// by Cortez
		'.jkdfjjkkdfe.info',	// by Rico
		'.jkllloldkjsa.info',	// by Cortez
		'.nnjdksfornms.info',	// by Rico
		'.mcmdkkksaoka.info',	// by Cortez
		'.srattaragfon.info',	// by Cortez
		'.yreifnnonoom.info',	// by Rico
		'.zjajjsvgeuds.info',	// by Cortez
	),
	'admin at ematuranza.com' => array(
		'.ancorlontano.com',
		'.dentroallago.com',
		'.digiovinezza.com',
		'.ematuranza.com',
		'.ilfango.com',
		'.nullarimane.com',
		'.questaimmensa.com',
		'.tentailvolo.com',
		'.unatenerezza.com',
		'.volgondilettose.com',
	),
	'admin at edeuj84.info' => array(	// by Cornelius Boyers
		'.bid99df.info',
		'.bj498uf.info',
		'.edeuj84.info',
		'.f4mfid.info',
		'.g4vf03a.info',
		'.j09j4r.info',
		'.jv4r8hv.info',
		'.k43sd3.info',
		'.k4r84d.info',
		'.k4rvda.info',
		'.k4v0df.info',
		'.k903os.info',
		'.k9df93d.info',
		'.kv94fd.info',
		'.ksjs93.info',
		'.l0ks03.info',
		'.l9u3jc.info',
		'.lv043a.info',
		'.nh94h9.info',
		'.m94r9d.info',
		'.s87fvd.info',
		'.v3k0d.info',
		'.v4r8j4.info',
		'.vf044s.info',
		'.vj49rs.info',
		'.vk498j.info',
		'.u03jow.info',
	),
	'Nikhil and Brian' => array(
		// by Brian Dieckman (info at iudndjsdhgas.info)
		// by Nikhil Swafford (info at jhcjdnbkrfo.info)
		// by Gerardo Figueiredo (info at jikpbtjiougje.info)
		'.ihfjeswouigf.info',	// by Brian, / was not found
		'.iudndjsdhgas.info',	// by Brian, / was not found
		'.iufbsehxrtcd.info',	// by Brian, / was not found
		'.jiatdbdisut.info',	// by Brian, / was not found
		'.jkfierwoundhw.info',	// by Brian, / was not found
		'.kfjeoutweh.info',		// by Brian, / was not found
		'.ncjsdhjahsjendl.info',// by Brian, / was not found
		'.oudjskdwibfm.info',	// by Brian, / was not found
		'.cnewuhkqnfke.info',	// by Nikhil, / was not found
		'.itxbsjacun.info',		// by Nikhil, / was not found
		'.jahvjrijvv.info',		// by Nikhil (info at jikpbtjiougje.info), / was not found
		'.jhcjdnbkrfo.info',	// by Nikhil, / was not found
		'.najedncdcounrd.info',	// by Nikhil, / was not found
		'.mcsjjaouvd.info',		// by Nikhil, / was not found
		'.oujvjfdndl.info',		// by Nikhil, / was not found
		'.uodncnewnncds.info',	// by Nikhil, / was not found
		'.jikpbtjiougje.info',	// by Julio Mccaughey (info at jikpbtjiougje.info), / was not found
		'.cijkalvcjirem.info',	// by Gerardo, / was not found
		'.nkcjfkvnvpow.info',	// by Gerardo, / was not found
		'.nmiiamfoujvnme.info',	// by Gerardo, / was not found
		'.nxuwnkajgufvl.info',	// by Gerardo, / was not found
		'.mkjajkfoejvnm.info',	// by Gerardo, / was not found
	),
	'wealth777 at gmail.com' => array(	// by Henry Ford
		'.brutal-forced.com',
		'.library-bdsm.com',
		'.rape-fantasy.us',
	),
	'Croesus International Inc.' => array(	// by Croesus International Inc. (olex at okhei.net)
		'.purerotica.com',
		'.richsex.com',
		'.servik.net',
		'.withsex.com',
	),
	'dreamteammoney.com' => array(
		'.dreamteammoney.com',	// dtmurl.com related
		'.dtmurl.com',			// by dreamteammoney.com, redirection service
	),
	'KLIK VIP Search and family' => array(
		'.cheepmed.org',		// "KLIK VIP Search" by petro (petrotsap1 at gmail.com)
		'.fastearning.net',		// "KlikVIPsearch.com" by Matthew  Parry        (fastearning at mail.ru)
		'.klikvipsearch.com',	// "KLIKVIPSEARCH.COM" by Adrian Monterra (support at searchservices.info)
		'.looked-for.info',		// "MFeed Search" now, by johnson (edu2006alabama at hotmail.com)
		'.mnepoxuy.info',		// "KlikVIPsearch.com" by DEREK MIYAMOTO (grosmeba at ukr.net)
		'.searchservices.info',	// 403 Forbidden now, by Adrian Monterra (support at searchservices.info)
		'.visabiz.net',			// "Visabiz-Katalog-Home" now, by Natalja Estrina (m.estrin at post.skynet.lt)
	),
	'vasyapupkin78 at bk.ru' => array(	// by Andrey Kozlov
		'.antivirs.info',
		'.antivirus1.info',
		'.antivirus2.info',
	),
	'wasam at vangers.net and family' => array(
	
		// 69.31.82.51(colo-69-31-82-51.pilosoft.com) by Kadil Kasekwam (kadilk at vangers.net)
		'.bahatoca.org',
		'.digestlycos.org',
		'.educativaanale.info',
		'.guildstuscan.org',
		'.isaakrobbins.info',
		'.isfelons.org',
		'.lvwelevated.org',
		'.macphersonaca.org',
		'.markyaustrian.org',
		'.michelepug.org',
		'.opalbusy.info',
		'.quijotebachata.info',
		'.salthjc.info',
		'.shogunnerd.info',
		'.solarissean.org',
		'.sparkgsx.info',
		'.tarzanyearly.org',
		'.tulabnsf.org',
		'.vaccarinos.org',

		// 69.31.82.53(colo-69-31-82-53.pilosoft.com) by Bipik Joshu (bipik at vangers.net)
		'.e2007.info',
		'.cmoss.info',

		// 69.31.82.53(colo-69-31-82-53.pilosoft.com) by Kasturba Nagari (kasturba at vangers.net)
		'.finddesk.org',
		'.gsfind.org',	// You mean: sfind.net  by tvaals at vangers.net
		'.my-top.org',	// You mean: my-top.net by tvaals at vangers.net
		'.rcatalog.org',
		'.sbitzone.org',

		// 69.31.82.53(colo-69-31-82-53.pilosoft.com) by Thomas Vaals (tvaals at vangers.net)
		'.cheapns.org',
		'.my-top.net',
		'.sfind.net',
		'.sspot.net',
		'.suvfind.info',

		// 69.31.82.53 by Mariano Ciaramolo (marion at vangers.net)
		'.trumber.com',

		// 69.31.82.53(colo-69-31-82-53.pilosoft.com) by Ashiksh Wasam (wasam at vangers.net)
		'.blogduet.org',
		'.carelf.info',
		'.cmagic.org',	
		'.cspell.org',
		'.dspark.org',
		'.dtonic.org',
		'.mcharm.info',
		'.mslook.info',
		'.phpdinnerware.info',
		'.rnation.org',
		'.uzing.org',

		// 69.31.91.226(colo-69-31-91-226.pilosoft.com) by Kadil Kasekwam (kadilk at vangers.net)
		'.allbar.info',
		'.allersearch.org',
		'.allzoom.org',
		'.dynall.org',
		'.fastopia.org',
		'.offasfast.info',
		'.rblast.org',
		'.rchistes.info',
		'.rette.org',
		'.shufflequince.org',
		'.suvlook.org',

		// 69.31.91.226(colo-69-31-91-226.pilosoft.com) by Ashiksh Wasam (wasam at vangers.net)
		'.290cabeza.org',
		'.bossierpainted.org',
		'.connickkarel.info',	// Admin: tvaals at vangers.net
		'.definekonica.info',	// Admin: tvaals at vangers.net
		'.gradetelemundo.info',
		'.hydraulickin.info',
		'.indicadorestmj.info',
		'.keeleykincaid.org',
		'.kleenbowser.info',
		'.pipnickname.info',
		'.pacolily.org',
		'.redeemtrabalho.info',
		'.scanmakerchua.info',
		'.titanmessina.info',
		'.tragratuit.org',
		'.yeareola.info',
	),
	'SearchHealtAdvCorpGb.com' => array(	// by Jonn Gardens (admin at SearchHealtAdvCorpGb.com -- no such domain)
		'.canadianmedsworld.info',	// 84.252.133.112
		'.tabsdrugstore.info',		// 84.252.133.114
		'.tabsstore.info',			// 84.252.133.114
		'.topcholesterol.info',		// 84.252.133.132
	),
	'be.cx' => array(
		'.be.cx',
		'.ca.cx',
	),
	'john780321 at yahoo.com' => array(	// by John  Brown
		'.bestdiscountpharmacy.biz',	// 2007-01-27, 61.144.122.45
		'.drugs4all.us',				// 2007-03-09, 202.67.150.250
		'.online-pharmacy-no-prescription.org',	// 69.56.135.222(de.87.3845.static.theplanet.com)
	),
	'tremagl.freet at gmail.com' => array(	// by Treman Eagles, redirect to searchadv.com
		'.bertela.info',
		'.forblis.info',
		'.frenallo.info',
		'.goyahoo.info',
		'.herbak.info',
		'.kiokast.info',
		'.nerenok.info',
		'.pestgets.info',
		'.snukker.info',
		'.thegetspons.info',
	),
	'2xxc at 2xxc.net' => array(	// by 2xxc, 404 not found
		'.bobop.info',
		'.kwwwe.info',
		'.piikz.info',
		'.moosm.info',
		'.vvvw.info',
	),
	'support at 51g.net' => array(	// iframe www.lovetw.webnow.biz
		'.ftplin.com',		// 125.65.112.15, by Yongchun Liao
		'.jplin.com',		// 125.65.112.15, by Yongchun Liao
		'.jplineage.com',	// 221.238.195.113, by Yongchun Liao
		'.jplingood.com',	// 125.65.112.15
		'.linenew.com',		// 203.191.148.96
		'.lyftp.com',		// 218.5.77.17,   by Yongchun Liao (weboy at 51g.net)
		'.yzlin.com',		// 220.162.244.36
	),
	'Betty.J.Pelletier at pookmail.com' => array(	// by Betty J. Pelletier
		'.1111mb.com',
		'.2sex18.com',
		'.69porn1.com',
	),
	'ECTechnology' => array(
		'.atmouse.co.kr',		// by EG gisul (kpgak at hanmail.net)
		'.auto-mouse.com',		// "Copyright Ò$ 2007 www.automouse.jp" by ECTechnology (help at atmouse.co.kr)
		'.automouse.jp',
	),
	'lyqz at 21cn.com' => array(
		'.japangame1.com',
		'.lineinfo-jp.com',		// www.lineinfo-jp.com is 61.139.126.10
		'.livedoor1.com',
		'.ragnarokonline1.com',
		'.zhangweijp.com',		// by qiu wang hao (qq.lilac at eyou.com), *.exe, hidden JavaScripts, the same IP of www.lineinfo-jp.com
	),
	'kingrou at hotmail.com' => array(	// by yangjianhe
		'.youshini.com',		// Two iframe to 453787.com's *.exe
		'.453787.com',
	),
	'anpaul541000 at 163.com' => array(	// by su qiuqing
		'.cetname.com',			// 222.77.185.87
		'.jpgamer.net',			// 220.247.157.106
		'.jpplay.net',			// 222.77.185.87, iframe www.lovetw.webnow.biz
		'.lovejptt.com',		// 222.77.185.87
		'.pangzigame.com',		// 220.247.134.136, by qiuqingshan
		'.playncsoft.net',		// 220.247.157.106
	),
	'abc00613 at 163.com' => array(	// by guo yong
		'.avtw1068.com',		// 64.74.223.11
		'.dj5566.org',			// Seems IP not allocated now, by yongchao li
		'.djkkk66990.com',		// 68.178.232.99
		'.lingamesjp.com',		// 219.153.13.23(8.myadmin.cn),  by guo jinlong
	),
	'thomas.jsp at libertysurf.fr' => array(	// by Perez Thomas
		'.cmonfofo.com',
		'.discutbb.com',
	),
	'Dorothy.D.Adams at mailinator.com' => array(	// by Dorothy D. Adams
		'.preca.info',
		'.skiaggi.info',
		'.scrianal.info',
		'.tageom.info',
	),
	'Inpros.net' => array(	// by Hayato Hikari (hikari at t-dm.co.jp)
		'.inpros.biz',			// 38.99.91.137, redirect to inpros.net
		'.inpros.net',			// 202.181.98.79
		'.gametradeonline.jp',	// 210.188.204.233, by Hayato Hikari, RMT
	),
	'szczffhh_sso at 21cn.net' => array(	// by zhenfei chen
		'.ec51.com',
		'.ec51.net',
	),
	'abbevillelaties at yahoo.fr etc' => array(
		// by Mahat Ashat, JavaScript may mocks "ACCOUNT TERMINATE", or "Domain deleted Reason: ABUSE" but ...
		'.ringtones-rate.com',	
		'.ringtones-dir.net',	// by Alex Maklayt (maklayt at ringtones-dir.net), hidden JavaScript
		'.special-ringtones.net',
	),
	'gibson or gibs0n at skysquad.net' => array(	// by Brzezinski Bartosz (gibson at skysquad.net), redirect to find.fm
		'.1sgsc.info',
		'.3h4r89h.info',
		'.3v44dd.info',
		'.6rfuh6.info',
		'.84hd8.info',
		'.94bui89.info',
		'.agysb3.info',
		'.asdjhs.info',
		'.bcvnrth.info',
		'.bheb4r.info',
		'.bhiuno.info',
		'.biug7g.info',
		'.bjb5f4.info',
		'.bob8g7g.info',
		'.br89bdd.info',
		'.bsa3h.info',
		'.bsieb8.info',
		'.basbiubf.info',
		'.bobwwfs2.info',
		'.ciuv9t.info',
		'.dbmdx4.info',
		'.dbrjms.info',
		'.dbtcm.info',
		'.dff9ghu.info',
		'.dfshbb.info',
		'.dgd4ffdh.info',
		'.dh3ge.info',
		'.duc86jh.info',
		'.ergth45.info',
		'.f78bf7ffb.info',
		'.gbdfbo4.info',
		'.ger45.info',
		'.gnvnrrg.info',
		'.h47he7.info',
		'.h488hbd4.info',
		'.hd72b94.info',
		'.he74b7.info',
		'.hfujfnr.info',
		'.husdhd42.info',
		'.hbwje.info',
		'.itg87gji.info',
		'.iugiougiuh.info',
		'.jhd4f4aa.info',
		'.jshd73.info',
		'.krhpgd.info',
		'.lyihjn.info',
		'.nfyjnfj.info',
		'.oihbv.info',
		'.os44fvs.info',
		'.sdfsd3.info',
		'.sdiug4.info',
		'.sdkufhh.info',
		'.sdugb4f.info',
		'.skdbf.info',
		'.sipiv78.info',
		'.sudbfb.info',
		'.tymbbmy.info',
		'.uilhjk.info',
		'.vi87vub.info',
		'.vfuyf87f.info',
		'.viyvvj877.info',
		'.w7fc8eu.info',
		'.wefg43g.info',
		'.xbrch78e.info',
		'.ywsfu.info',
		'.zxcbiv.info',
	),
	'info at infooracle.com' => array(	// by Marek Luto Marek Luto
		'.abofios.info',
		'.amlekfn.info',
		'.amlkdoie.info',
		'.amkslewq.info',
		'.alemfu.info',
		'.aloweks1.info',
		'.alposd3.info',
		'.bamhpb.info',
		'.bhjkb.info',
		'.bjqnj.info',
		'.cvcxcbhpr.info',
		'.czoypaiat.info',
		'.dbpmgc.info',
		'.dgvogrxs.info',
		'.dldksf.info',
		'.dlor6za.com',
		'.dmkoiew.info',
		'.eewrefr.info',
		'.eladne.info',
		'.elksem.info',
		'.elwpod.info',
		'.emlwkdnr.info',
		'.esgmyqk.info',
		'.fauqv.info',
		'.fgxkgy.info',
		'.fhryns.info',
		'.fj38n4g.info',
		'.fjnesal.info',
		'.fmkfoe.info',
		'.fqkcfldtr.info',
		'.fwcigpdwz.info',
		'.fyhik.info',
		'.glrkje.info',
		'.gwkslfq.info',
		'.gwjracvh.info',
		'.hihopepe.info',
		'.hwlyggbkw.info',
		'.hmwbfw.info',
		'.hthyeb.info',
		'.iaofkyaw.info',
		'.uldkxuiw.info',
		'.is7c6w4.info',
		'.ivuddhdk.info',
		'.jgfndjem.info',
		'.jgmdlek.info',
		'.jkrnvmpad.info',
		'.jqujn.info',
		'.jvgmmba.info',
		'.kbaur.info',
		'.kgjindptv.info',
		'.kleo7s9.info',
		'.lezfgam.info',
		'.lfaasy.info',
		'.ljpdjki.info',
		'.lmnpis.info',
		'.lpzcu2f.info',
		'.lrptn.info',
		'.lursqt.info',
		'.mgkabviil.info',
		'.mhtknjyt.info',
		'.mksuuku.info',
		'.mkyky.info',
		'.mloaisn.com',
		'.mlsiknd.info',
		'.mthqz.info',
		'.nnooq.info',
		'.nohhylvc.info',
		'.nuprndsye.info',
		'.nsoelam.info',
		'.nykobczv.info',
		'.nzuhli.info',
		'.odyqzgylr.info',
		'.oidiau.info',
		'.oitzkw.info',
		'.okdmrpz.info',
		'.ooinziti.info',
		'.ortqr.info',
		'.osmkpnekv.info',
		'.ozkzfih.info',
		'.p3ix8wc.com',
		'.piwyt.info',
		'.pfkijrm.info',
		'.pjktcragi.info',
		'.pleoz.info',
		'.plvqm73.info',
		'.pqyrem.info',
		'.qipgqd.info',
		'.qlewixu.com',
		'.qmlskme.info',
		'.qtuff.info',
		'.quoga.info',
		'.quqz.info',
		'.qzxuw.info',
		'.rcaidegp.info',
		'.rlkmdi.info',
		'.rnsoiov.info',
		'.rnwlams.info',
		'.rprgkgqld.info',
		'.rubqvxrn.info',
		'.spqxstl.info',
		'.syckoqjql.info',
		'.tbirb.info',
		'.thalc34.info',
		'.tiabq.info',
		'.tszzpjr.info',
		'.tyjdyn.info',
		'.twgugpns.info',
		'.uaezrqp.info',
		'.udlkasu.info',
		'.uejncyf.info',
		'.ukvflb.info',
		'.ugsuv.info',
		'.ukhgpcp.info',
		'.urprzn.info',
		'.uuhememkw.info',
		'.yalc7en.info',
		'.ybuid.info',
		'.yhdkgfob.info',
		'.ymenq.info',
		'.ynlyb.info',
		'.vieatlubk.info',
		'.vltcaho.info',
		'.wlamsiek.info',
		'.wlerp.info',
		'.wlmtshzi.info',
		'.wmlkams.info',
		'.wprqd.info',
		'.wpyspszi.info',
		'.xdscc.info',
		'.xdvy.info',
		'.xeypku.info',
		'.xsrxh.info',
		'.xwjyrpfe.info',
		'.yxcqw.info',
		'.zhbktrh.info',
		'.zspepn.info',
		'.zsxtz.info',
	),
	'survi at poczta.fm and smiley' => array(
		'.pperd.info',		// "main site :>" by Domagala Andrzej (survi at poczta.fm)
		'.ppert.info',
		'.pperta.info',
		'.pperts.info',
		'.pprtuis.info',
		'.13iuey.info',		// ":>"
		'.13jkhs.info',
		'.13lksa.info',
		'.13rxtx.info',
		'.13slkd.info',
		'.13zaer.info',
	),
	'admin at esemeski.com' => array(	// by Jan Kalka
		'.kxils.info',
		'.kuaph.info',
		'.lncdc.info',
		'.lsqpd.info',
		'.mczed.info',
		'.npous.info',
		'.obgju.info',
	),
	'LiquidNetLimited.com' => array(
		// liquidnetltd.net,	// 216.65.1.131(duoservers.com)

		// FateBack.com related
		// 216.65.1.201(fateback.com) by LiquidNet Ltd. (president at fateback.com), redirect to www.japan.jp
		'.bebto.com',
		'.fateback.com',
		'.undonet.com',
		'.yoll.net',
		'.sinfree.net',		// 216.65.1.201 by LiquidNet Ltd. (support at propersupport.com), redirect to www.japan.jp

		// 50webs.com			// 64.72.112.10
		// dns2.50webs.com		// 64.72.112.11
		'*.freehostia.com',		// 64.72.112.12, many related hosts surrounded, http://freehostia.com/about_us.html says "... partnership with the UK-based LiquidNet Ltd., and ..."
		// dns2.freehostia.com	// 64.72.112.13
		// serv3.freehostia.com	// 64.72.112.14
		// hex12.freehostia.com	// 64.72.112.19, 64.72.112.20
		// mail.50webs.com		// 64.72.112.26
		// supremecenter41.com	// 64.72.112.52
		// 50webs2.50webs.com	// 64.72.112.89
		// supremecenter39.com	// 64.72.112.103

		// by LiquidNet Ltd. (support at propersupport.com)
			'*.50webs.com',			// 64.72.112.10, redirect to mpage.jp, listed in http://www.liquidnetlimited.com/services.html
			// propersupport.com	// 216.65.1.129(dns1.supremecenter.com)
			'duoservers.com',		// 216.65.1.130

		// 100ws.com			// No-ip by LiquidNet Ltd. (ceo at propersupport.com)
	),
	'domains at agava.com' => array(
		'.h18.ru',
		'.hut1.ru',
	),
	'wlmx009 at hotmail.com' => array(
		'.123lineage.com',
		'.ff11-info.com',
		'.lastlineage.com',
		'.lineage2-ol.com',
		'.lineage2006.com',
		'.lineagefirst.com',
	),
	'Zettahost.com' => array(
		'.atspace.biz',		// sales at zettahost.com
		'.atspace.com',		// abuse at zettahost.com
		'.atspace.name',	// NS atspace.com
		'.awardspace.com',	// by abuse at awardspace.com, no DirectoryIndex, 70.86.228.149
		'.awardspace.us',	// by Dimitar Dimitrov (sales at zettahost.com), 70.86.228.149
	),
	'hlq9814 at 163.com' => array(
		'.kotonohax.com',		// by ling bao
		'.ragnarox.mobi',		// by lin bao, *.exe download
		'.rokonline-jp.com',	// by hang long
	),
	'77ch.jp' => array(
		'.77ch.jp',
		'.gamorimori.net',	// by ryo takami (infomation at 77ch.jp)
	),
	'serchportal at mail.ru' => array(	// by Namu Adin
		'.43fert.info',
		'.belis.info',
		'.bonu.info',
		'.chelsite.info',
		'.chparael.info',
		'.cool9f.info',
		'.dada2.info',
		'.dorplanete.info',
		'.dormonde.info',
		'.dorprojet.info',
		'.faciledor.info',
		'.fastsearchgroup.info',
		'.gerta0.info',
		'.getse.info',
		'.gopvl.info',
		'.knopki.info',
		'.propidor.info',
		'.quicksearchnet.info',
		'.ret5.info',
		'.slimfastsearch.info',
		'.virtualpvl.info',
		'.vpvla.info',
		'.xjdor.info',
		'.zhopki.info',
	),
	'SoniqHost.com' => array(	// by Stanley Gutowski (support at soniqhost.com)
		'*.444mb.com',		// Free hosting
		'urlnip.com',		// Redirection
	),
	'WWW.RU' => array(		// by Angela (abuse at www.ru)
		'.1fasttimesatnau.info',
		'.1freecybersex.info',
		'.1freexxxcomics.info',
		'.1fuckingmywife.info',
		'.1pornpreview.info',
		'www.ru',					// by (service at demos.ru), redirection
	),
	'65.90.250.10' => array(	// IP seems the same (65.90.250.10)
		'.adultschat.info',
		'.livecamonline.info',
		'.webcam4u.info',
		'.younghot.info',
	),
	'hostorgadmin at googlemail.com' => array(	// Byethost Internet Ltd.
		'.yoursupportgroup.com',	// 72.36.219.162(*.static.reverse.ltdomains.com)

		// 209.51.196.242
		'.22web.net',
		'.2kool4u.net',
		'.9skul.com',
		'.alojalo.info',
		'.byet.net',
		'.byethost2.com',
		'.byethost3.com',
		'.byethost4.com',
		'.byethost5.com',
		'.byethost6.com',
		'.byethost7.com',
		'.byethost8.com',
		'.byethost9.com',
		'.byethost10.com',
		'.byethost11.com',
		'.byethost12.com',
		'.byethost13.com',
		'.byethost14.com',
		'.byethost15.com',
		'.byethost16.com',
		'.byethost17.com',
		'.byethost18.com',
		'.headshothost.net',
		'.hostwq.net',
		'.mega-file.net',
		'.truefreehost.com',

		'.ifastnet.com',	// 209.51.196.243

		// 209.190.16.82(mx1.byet.org)
		'.1sthost.org',
		'.4sql.net',
		'.byet.org',
		'.hyperphp.com',
		'.kwikphp.com',
		'.my-php.net',
		'.my-place.us',
		'.my-webs.org',
		'.netfast.org',
		'.php0h.com',
		'.php1h.com',
		'.php2h.com',		// by Andrew Millar (asmillar at sir-millar.com), ns also *.byet.org
		'.phpnet.us',
		'.prohosts.org',
		'.pro-php.org', 
		'.prophp.org',
		'.sprinterweb.net',
		'.swiftphp.com',
		'.xlphp.net',

		// 209.190.16.83(mx2.byet.org)
		'.instant-wiki.net',

		// 209.190.16.84(mx3.byet.org)

		// 209.190.16.85(mx4.byet.org)
		'.instant-blog.net',
		'.instant-forum.net',

		'.byethost.com',			// 209.190.18.138
	),
	'webmaster at bestgirlssex.info' => array(	// by lemnaru ionut, ns *.hostgator.com
		'.analmoviesite.info',
		'.bestgirlssex.info',
		'.boxvagina.info',
		'.cyberlivegirls.info',
		'.hotredgirls.info',
		'.forsexlove.info',
		'.hotnudezone.info',
		'.hotredpussy.info',
		'.lesbians-live.info',
		'.lesbians-on-cam.info',
		'.onlinegirlssite.info',
		'.sexloveonline.info',
		'.teensexcard.info',
		'.teensexdirect.info',
		'.topnudesite.info',
		'.vaginafree.info',	
		'.webcam-show.info',
		'.webcamshow.info',
		'.youngsexchat.info',
		'.yourcumshot.info',	
	),
	'stocking.club at gmail.com' => array(
		'.adulthotmodels.com',		// by David Zajwzran
		'.aretheshit.info',			// by David Theissen (zjwzra at mail.ru)
		'.cash-call.info',			// by David Theissen
		'.cialis-compare-levitra-viagra.info',	// by David Theissen
		'.cheap-online-viagra.info',	// by David Theissen
		'.drugcleansing.net',		// by David Zajwzran
		'.men-health-zone.com',		// by David Theissen
		'.purchase-viagra.info',	// by David Theissen
		'.realdrunkengirls.biz',	// by David Theissen
		'.sextoyslife.com',			// by David Zajwzran
		'.sexysubjects.info',		// by David Zajwzran
		'.shithotsex.info',			// by David Theissen (zjwzra at mail.ru)
		'.stocks-trader.info',		// by David Theissen (zjwzra at mail.ru)
		'.travelcardsite.info',		// by David Theissen
	),
	'lustiq at p5com.com' => array(
		'.wonkalook.com',		// ns *.willywonka.co.in, 85.255.117.226
		'.willywonka.co.in',	// by Nick Priest (lustiq at p5com.com), 85.255.117.226
	),
	'web at 6jy.com' => array(
		'.micro36.com',			// by Teng Zhang, content from lineage.jp, post with 'lineage1bbs.com'
		'.movie1945.com',		// by Zhang Teng, content from lineage.jp, hidden JavaScript
	),
	'mk_slowman at yahoo.com' => array(	// by Mike Slowman (mk_slowman at yahoo.com)
		'.auto-fgen.info',
		'.fast-marketing.info',
		'.from-usa.info',
		'.generic-pharm.info',
		'.pharm-directory.info',
		'.popular-people.info',
		'.safe-health.info',
		'.star-celebrities.info',
		'.super-home-biz.info',
		'.top5-auto.info',
		'.top5-cars.info',
		'.vip-furniture.info',
		'.vip-pc.info',
		'.vip-pets.info',
	),
	'abuse at search-store.org' => array(
		'.travel-gen.info',		// by Mike Slowman (abuse at search-store.org)
	),
	'Leading Edge Marketing Inc.' => array(
		// by Leading Edge Marketing Inc. (domains at leminternet.com), seems an advertiser
		'.abemedical.com',
		'.attractwomennow.com',
		'.bettersexmall.com',
		'.buymaxoderm.com',
		'.buyvprx.com',
		'.genf20.com',
		'.infinityhealthnews.com',
		'.istnewsletter.com',
		'.leadingedgecash.com',
		'.leadingedgeherbals.com',
		'.leadingedgevipsonly.com',
		'.lecash.com',
		'.leminfo.com',
		'.proextendersystem.com',
		'.provestra.com',
		'.semenax.com',
		'.shavenomore.com',
		'.theedgenewsletter.com',
		'.vigorelle.com',
		'.vigrx.com',
		'.vigrxplus.com',
		'.wbstnewsletter.com',
	),
	'clickx at bk.ru' => array(	// by Alexey Enrertov
		'.coolget*.info' =>
			'#^(?:.*\.)?' . 'coolget' .
			'(?:bus|find|news|php|place|post|srch)' .
			'\.info$#',
		'.coolgirl*.info' =>
			'#^(?:.*\.)?' . 'coolgirl' .
			'(?:apple|fish|search)' .
			'\.info$#',
		'.coolmeet*.info' =>
			'#^(?:.*\.)?' . 'coolmeet' .
			'(?:apple|click|find|fish|news|php|place|post|srch|search)' .
			'\.info$#',
		'.cool**.info' =>
			'#^(?:.*\.)?' . 'cool' . '(?:strong|the)' .
			'(?:apple|bus|click|find|fish|news|php|place|post|srch|search)' .
			'\.info$#',
		'.freseasy*.info' =>
			'#^(?:.*\.)?' . 'freseasy' .
			'(?:apple|click|find|fish|post|search)' .
			'\.info$#',
		'.fres**.info' =>
			'#^(?:.*\.)?' .
			'fres' . '(?:adult|boy|get|girl|meet|new|real|strong|the)' .
			'(?:apple|bus|click|find|fish|news|php|place|post|srch|search)' .
			'\.info$#',
			// These are not found yet:
			// fresgirlsrch.info
			// fresadultapple.info
			// fresadultclick.info
			// frestheplace.info

		// 66.232.113.44
		'.nuhost.info',
		'.susearch.info',

		// 66.232.126.74(hv94.steephost.com)
		'.dilej.com',
		'.fyvij.com',
		'.howus.com',
		'.jisyn.com',
		'.kaxem.com',
		'.mihug.com',
		'.mobyb.com',
		'.qidat.com',
		'.qihek.com',
		'.ryzic.com',
		'.sasuv.com',
		'.tuquh.com',
		'.vehyq.com',
		'.wezid.com',
		'.wifuj.com',
		'.xijyt.com',
		'.zuqyn.com',

		'.mosugy.com',
	),
	'jakaj ay hotmail.com' => array(	// 66.232.113.46, the same approach and timing of clickx at bk.ru
		'.hitsearching.info',
		'.hugeamountdata.info',
		'.megafasthost.info',
		'.real-big-host.info',
		'.search4freez.info',
		'.yasech.info',

		// 66.232.97.246(host4.blazegalaxy.com => 71.6.196.202)
		'.bymire.com',

		// 66.232.120.98(host.radiantdomain.com => 71.6.196.202 => fc6196202.aspadmin.net)
		'.ligiwa.com',

		'.mubeho.com',
	),
	'ice--man at mail.ru related' => array(
		// 74.50.97.198 by andrey, the same approach and timing of clickx at bk.ru
		'.bestcreola.com',
		'.crekatierra.com',
		'.creolafire.com',
		'.crolik.com',
		'.croller.cn',
		'.ecrmx.com',
		'.eflashpoint.com',
		'.exoticmed.com',
		'.feelview.com',
		'.greatexotic.com',
		'.icrtx.com',
		'.icyhip.com',
		'.icyiceman.com',
		'.icypopular.com',
		'.iflashpoint.com',
		'.justmdx.com',
		'.kiliusgroup.com',
		'.klickerr.com',
		'.klickerrworld.com',
		'.kreolic.com',
		'.margansitio.com',
		'.margantierra.com',
		'.mimargan.com',
		'.oilkeys.com',
		'.planetmdx.com',
		'.thekeyse.com',
		'.viewgreat.com',
		'.yourcreola.com',
		
		// 69.46.23.48
		'.bestkilius.com',
		'.crekadirecto.com',
		'.getflashsite.com',
		'.sucreka.com',
		'.superkilius.com',

		// 69.46.23.48 by bing-16 at ftunez.org
		'.bovorup.cn',
		'.litotar.cn',
		'.nihydec.cn',

		// 66.232.112.175 by bing-32 at ftunez.org
		'.lasyxy.cn',

		// by bing-65 at ftunez.org
		'.coxyvuk.cn',		// 66.232.120.111(non-existent)
		'.comygyx.cn',		// 66.232.120.112(non-existent)
		'.gyqalec.cn',		// 66.232.120.114(non-existent)
		'.paluwir.cn',		// 66.232.120.114(non-existent)
		'.qunonid.cn',		// 66.232.120.114(*snip*)
		'.qupyvin.cn',		// 66.232.120.115(non-existent)
		'.ririjyz.cn',		// 66.232.120.111(*snip*)
		'.saralar.cn',		// 66.232.120.113(non-existent)
		'.vawomyl.cn',		// 66.232.120.115(non-existent)

		// 69.46.23.48 by clarkson-34 at ftunez.org
		'.bumora.cn',
		'.byxite.cn',
		'.byxuqu.cn',
		'.jadama.cn',
		'.kybope.cn',
		'.mefeki.cn',
		'.mokiso.cn',
		'.niqeme.cn',
		'.pukafo.cn',
		'.qesaxa.cn',
		'.toxezi.cn',
		'.tujudy.cn',
		'.tutike.cn',

		// NEUTRAL but dark: with clarkson-34 at ftunez.org, non-existent domain
		'.cabino.cn',
		'.cuwace.cn',
		'.ferazi.cn',
		'.gigywy.cn',
		'.lapype.cn',
		'.mewabe.cn',
		'.pezegy.cn',
		'.pyzuza.cn',
		'.qypony.cn',
		'.ropesy.cn',
		'.wadyfe.cn',

		// 69.46.23.48 clarkson-58 at ftunez.org
		'.becabe.cn',
		'.biciqy.cn',
		'.bezymu.cn',
		'.cifori.cn',
		'.ciwyxi.cn',
		'.dobari.cn',
		'.dusofe.cn',
		'.dutyda.cn',
		'.dykanu.cn',
		'.fakexu.cn',
		'.fylema.cn',
		'.godymo.cn',
		'.hyhaxy.cn',
		'.ganosi.cn',
		'.kuxyju.cn',
		'.lacezy.cn',
		'.lonyru.cn',
		'.lycato.cn',
		'.nykyby.cn',
		'.qyhuko.cn',
		'.redere.cn',
		'.riwimu.cn',
		'.wopary.cn',
		'.xizity.cn',
		'.xuxusa.cn',

		// by entretov-28 at ftunez.org
		'.hotejen.cn',		// 66.232.120.111(*snip*)
		'.kyhadat.cn',		// 66.232.120.115(*snip*)

		// 69.46.23.48 by entretov-32 at ftunez.org
		'.cihuji.cn',
		'.deqyve.cn',
		'.genefa.cn',
		'.gujyju.cn',
		'.hasadu.cn',
		'.hedomi.cn',
		'.kecicy.cn',
		'.kipabo.cn',
		'.manunu.cn',
		'.musoru.cn',
		'.myvuna.cn',
		'.pikybe.cn',
		'.riqawo.cn',
		'.rufuxy.cn',
		'.vyfilu.cn',
		'.xadule.cn',
		'.zolaxe.cn',
		'.zerixu.cn',

		// 66.232.112.242(hv78.steephost.com) by entretov-43 at ftunez.org
		'.dozoda.cn',
		'.nemipu.cn',

		// entretov-84 at ftunez.org
		'.muruvun.cn',		// 66.232.120.114(*snip*)
		'.favulol.cn',		// 66.232.120.115(*snip*)
		'.tixuqyx.cn',		// 66.232.120.111(*snip*)

		// 69.46.23.48 by jeremy-57 at ftunez.org
		'.duzele.cn',
		'.figede.cn',
		'.fiwany.cn',
		'.gyfalu.cn',
		'.jepylo.cn',
		'.nuqumy.cn',

		// NEUTRAL but dark: with jeremy-57 at ftunez.org, non-existent domain
		'.burahu.cn',
		'.jydijy.cn',
		'.komyby.cn',
		'.najepa.cn',
		'.pylobu.cn',
		'.qofoly.cn',
		'.sybuvi.cn',
		'.vycexu.cn',
		'.wotyqo.cn',
		'.xudoli.cn',

		// by entretov-28 at ftunez.org
		'.xevavuv.cn',		// 66.232.120.113(*snip*)

		// by entretov-37 at ftunez.org
		'.sadyroz.cn',		// 66.232.120.114(*snip*)
		'.zedutox.cn',		//  66.232.120.112(non-existent)

		// 66.232.112.175 by entretov-86 at ftunez.org
		'.faweji.cn',
		'.jypaci.cn',
		'.xozuso.cn',

		// 69.46.23.48 by miles-37 at ftunez.org
		'.beqymo.cn',
		'.ceqibi.cn',
		'.dyraqo.cn',
		'.qenedo.cn',
		'.qurypa.cn',
		'.siluvo.cn',
		'.tujala.cn',
		'.wukuwi.cn',
		'.xenibo.cn',
		'.xiculo.cn',
		'.zabemu.cn',

		// NEUTRAL but dark: with miles-37 at ftunez.org, non-existent domain
		'.bevaka.cn',
		'.fysyte.cn',
		'.guxixu.cn',
		'.hatoli.cn',
		'.jitobu.cn',
		'.juxeca.cn',
		'.kifuhy.cn',
		'.licila.cn',
		'.mecomy.cn',
		'.niryko.cn',
		'.noxoco.cn',
		'.qiwysu.cn',
		'.tutysy.cn',

		// by oker-74 at ftunez.org
		'.navufol.cn',

		// by oker-97 at ftunez.org
		'.dohakot.cn',		// 66.232.127.126(yourbusiness.ME-127.com)
		'.mesyvuc.cn',		// 66.232.120.111(*snip*)
		'.nyjyzup.cn',		// 66.232.127.126(*snip*)
		'.qagibit.cn',		// 66.232.120.114(*snip*)
		'.rovazaw.cn',		// 66.232.120.112(*snip*)
		'.tozojug.cn',		// 66.232.120.111(*snip*)
		'.xywataw.cn',		// 66.232.120.112(*snip*)

		// by polet-20 at ftunez.org
		'.mopoxon.cn',		// 66.232.127.126(*snip*)

		// 66.232.112.175 by sabrosky-49 at ftunez.org
		'.gywiqe.cn',
		'.jotapo.cn',
		'.jywixa.cn',

		// 69.46.23.48 by sabrosky-60 at ftunez.org
		'.bawegap.cn',
		'.buremyl.cn',
		'.cilybut.cn',
		'.cutufek.cn',
		'.defypyr.cn',
		'.femaxij.cn',
		'.gelejo.cn',
		'.gocylyv.cn',
		'.kehiqaq.cn',
		'.ninyjit.cn',
		'.ruroruw.cn',
		'.vetehow.cn',

		// 69.46.23.48 sabrosky-85 at ftunez.org
		'.banyla.cn',
		'.bubaqu.cn',
		'.bygage.cn',
		'.dafozy.cn',
		'.kaxyjo.cn',
		'.makyle.cn',
		'.naleto.cn',
		'.pidele.cn',
		'.poqexa.cn',
		'.pymaqo.cn',
		'.qupiqy.cn',
		'.reqefy.cn',
		'.sopocy.cn',
		'.vuhexo.cn',
		'.weryso.cn',
		'.wubula.cn',
		'.xufuxy.cn',
		'.zuhyxu.cn',

		// 69.46.23.48 by tribiani-97 at ftunez.org
		'.dyxorod.cn',
		'.firywoz.cn',
		'.jixezyx.cn',
		'.joveraw.cn',
		'.jowaxup.cn',
		'.nodarej.cn',
		'.pimijom.cn',
		'.tugupeg.cn',

		// 66.232.112.175 by abuse-here at inbox.ru
		'.catybe.cn',
		'.jytame.cn',
		'.wygete.cn',
	),
	'74.50.97.*' => array(
		'.kaqeje.com',			// 74.50.97.51(non-existent) by abuse-here at inbox.ru
		'.cumimo.com',			// 74.50.97.51(non-existent) by olga at ike.com
		'.kaxavo.com',			// 74.50.97.52(non-existent) by gunter at ftunez.org
		'.hoheru.com',			// 74.50.97.52(non-existent) by gunter at ftunez.org
		'.tyqoti.com',			// 74.50.97.52(non-existent) by anna at hotmail.com
		'.fupopu.com',			// 74.50.97.53(non-existent) by abuse-here at inbox.ru
		'.poxupo.com',			// 74.50.97.53(non-existent) by inna at gmail.com
		'.kuluvo.com',			// 74.50.97.53(non-existent) by gunter at ftunez.org
		'.wugoba.com',			// 74.50.97.53(non-existent) by abuse-here at inbox.ru
		'.civuhe.com',			// 74.50.97.54(non-existent) by olga at ike.com
		'.zasuly.com',			// 74.50.97.54(non-existent) by olga at ike.com
		'.liwowu.com',			// 74.50.97.54(non-existent) by abuse-here at inbox.ru
		'.vobime.com',			// 74.50.97.55(non-existent) by inna at gmail.com
		'.nyrive.com',			// 74.50.97.55(non-existent) by anna at hotmail.com
		'.hehepu.com',			// 74.50.99.245(non-existent) by abuse-here at inbox.ru
		'.bynute.com',			// 74.50.99.245(non-existent) by abuse-here at inbox.ru

		'.sevimy.com',			// 66.232.124.12(time-out) by inna at gmail.com
		'.gapubo.com',			// 66.232.124.12(time-out) by gunter at ftunez.org
		'.vejoku.com',			// 66.232.124.12(time-out) by olga at ike.com
		'.qysahu.com',			// 66.232.124.12(time-out) by abuse-here at inbox.ru
		'.hidolu.com',			// 66.232.124.13(time-out) by inna at gmail.com
		'.tetace.com',			// 66.232.124.13(time-out) by gunter at ftunez.org
		'.vuxilu.com',			// 66.232.124.13(time-out) by olga at ike.com
		'.teboca.com',			// 66.232.124.13(time-out) by olga at ike.com
		'.dizive.com',			// 66.232.124.14(time-out) by olga at ike.com
		'.peduxe.com',			// 66.232.124.14(time-out) by gunter at ftunez.org
		'.sybyna.com',			// 66.232.124.14(time-out) by gunter at ftunez.org
		'.bepofe.com',			// 66.232.124.15(time-out) by anna at hotmail.com
		'.kuloja.com',			// 66.232.124.15(time-out) by gunter at ftunez.org
		'.tetadu.com',			// 66.232.124.15(time-out) by inna at gmail.com
		'.qilato.com',			// 66.232.124.15(time-out) by inna at gmail.com
		'.lobimi.com',			// 66.232.124.16(time-out) by olga at ike.com
		'.tazuwe.com',			// 66.232.124.16(time-out) by olga at ike.com
		'.pihufo.com',			// 66.232.124.16(time-out) by olga at ike.com
		'.decewa.com',			// 66.232.124.16(time-out) by gunter at ftunez.org

		'.lynymu.com',			// 206.51.226.194(time-out) by abuse-here at inbox.ru
		'.saciqo.com',			// 206.51.226.194(time-out) by olga at ike.com
		'.zalajy.com',			// 206.51.226.194(time-out) by inna at gmail.com
		'.hisimy.com',			// 206.51.226.194(time-out) by inna at gmail.com
		'.qysowe.com',			// 206.51.226.194(time-out) by olga at ike.com
	),
	'nijeoi at hotmai.com' => array(
		// 66.232.126.74 by Nicol Makerson, the same approach and timing _and IP_ of clickx at bk.ru
		'.bowij.com',
		'.bozib.com',
		'.cavux.com',
		'.dipov.com',
		'.gumoz.com',
		'.hakyb.com',
		'.hehyv.com',
		'.hepyt.com',
		'.howoj.com',
		'.jywaz.com',
		'.ka4search.info',	// Found at faweji.cn/ and jytame.cn/, / forbidden
		'.kyheq.com',
		'.kyzad.com',
		'.qicad.com',
		'.qubyd.com',
		'.mocyq.com',
		'.muloq.com',
		'.myxim.com',
		'.nufyp.com',
		'.waqog.com',
		'.wyduc.com',
		'.xefyv.com',
		'.xomej.com',
		'.xomip.com',
		'.xykyl.com',
		'.zakuw.com',
		'.zeliw.com',
		'.zimev.com',
		'.zipif.com',

		// 66.232.97.244(host2.blazegalaxy.com => 71.6.196.202)
		'.tycoco.com',

		// 66.232.97.246(host4.blazegalaxy.com => 71.6.196.202)
		'.pufeqa.com',

		'.xehymi.com',
	),
	'niichka at hotmail.com' => array(
		// 66.232.113.44, the same approach and IP of clickx at bk.ru
		'.aerosearch.info',
		'.freader.info',
		'.info4searchz.info',
		'.nice-host.info',
		'.realyfast.info',
		'.resuts.info',

		// 66.232.126.74, the same approach and IP of clickx at bk.ru
		'.bepoh.com',
		'.gufiq.com',
		'.kedyh.com',
		'.pofyb.com',
		'.rurid.com',
		'.vucaj.com',
		'.vuwir.com',

		// 66.232.97.244(host2.blazegalaxy.com => 71.6.196.202 => fc6196202.aspadmin.net)
		'.bejefe.com',

		// 66.232.97.245(host3.blazegalaxy.com => 71.6.196.202)
		'.tidawu.com',

		// 66.232.97.247(host5.blazegalaxy.com => 71.6.196.202)
		'.rofuqa.com',
	),
	'porychik at hot.ee' => array(	// by Igor
		'.tedstate.info',	// "Free Web Hosting"
		'.giftsee.com',
	),
	'admin at pertol.info' => array(
		// 81.0.195.189 "Free Web Hosting"
		'.laparka.cn',
		'.mirlos.cn',
		'.oldmoms.cn',

		// 217.11.233.64(timed-out)
		'.ejmpep.cn',	// by admin at pertol.info
		'.ytere.cn',	// by admin at x2t.com

		'.pertol.info',				// 72.232.246.202(*.static.reverse.ltdomains.com) by partner at pornography-world.com
		'.pornography-world.com',	// 72.232.246.205(*.static.reverse.ltdomains.com) by admin at pornography-world.com, ns *.pertol.info

		// 209.67.218.106(*.static.reverse.ltdomains.com) by partner at pornography-world.com, ns *.peraonline.info
		'.new-greece-travel.com',
		'.peraonline.info',

		'.barkman.cn',
	),

	'aofa at vip.163.com' => array(
		'.bdjyw.net',		// by gaoyun, infected images, iframe to 5944.net's VBScript
		'.5944.net',
	),
	'zerberster at gmail.com' => array(	// by Curtis D. Pick, / not found
		'.maxrentcar.info',
		'.newsonyericsson.info',
		'.pornositeworld.biz',
		'.rentcarweb.info',
		'.xxxdomainsex.biz',
	),
	'kopper1970 at gmail.com' => array(
		'.cardealerall.info',		// by Green
		'.donatecarsales.info',		// by Sipil
		'.ringtonewilly.info',		// by Sipil
		'.travelstraveling.info',	// by Chinik
		'.viagrabuyonline.org',		// by Sipil
		'.viagraorderbuy.com',		// by Anatol
		'.worldcuptourism.info',	// by Sipil
	),
	'lisaedwards at ledw.th' => array(	// by Lisa Edwards
		'.globalinfoland.info',
		'.goodlifesearch.info',
		'.hotnetinfo.info',
		'.hotpornmovies.org',
		'.infopilot.info',
	),
	'iisuse at gmail.com' => array(	// by vladislav morozov (iisuse at gmail.com). / is spam
		'.bang-bro.org',
		'.datinghost.info',
		'.hello-craulers.info',
		'.free-blog-host.info',
		'.sucking-boobs.info',
	),
	'chub at seznam.cz' => array(	// "CamsGen 1.0" by Lee Chen Ho
		'.allcamsguide.info',
		'.camerascams.info',
		'.camerasera.info',
		'.girlcamsworld.info',
		'.hiddenlimocams.info',
		'.redlivecams.info',
		'.spycamsgear.info',
		'.spycamssite.info',
		'.supercamsusa.info',
		'.thecamsnow.info',
	),
	'87.242.116.81' => array(
		'.axit.ru',			// by Sergej L Ivanov (deeeport at yandex.ru)
		'.bilbidon.ru',		// by Ilya S Vorobiyov (reginamedom at yandex.ru)
		'.flating.ru',		// by Sergej L Ivanov (deeeport at yandex.ru)
		'.kalisto.ru',		// by Vladimir I Sokolov (azimut at gmail.ru)
		'.sanartuk.ru',		// by Vladimir I Noskov (hoskv2003 at gmail.ru)
	),
	'208.70.75.153' => array(
		'.cerc-fi.info',	// by Kon Bi (cerca-two at ya.ru)
		'.cerc-fo.info',	// by Kon Bi (cerca-two at ya.ru)
		'.cerc-no.info',	// by Ru Lee (cerca-tree at ya.ru)
		'.cerc-on.info',
		'.cerc-sv.info',	// by Ru Lee (cerca-tree at ya.ru)
		'.cerc-sx.org',		// by Kon Bi (cerca-two at ya.ru)
		'.cerc-te.info',	// by Ru Lee (cerca-tree at ya.ru)
		'.cerc-tr.info',
		'.cerc-tw.info',
		'.cerc-fi.org',		// by Kon Bi (cerca-two at ya.ru)
		'.cerc-fo.org',		// by Kon Bi (cerca-two at ya.ru)
		'.cerc-no.org',		// by Ru Lee (cerca-tree at ya.ru)
		'.cerc-on.org',		// by cerca-one at ya.ru
		'.cerc-sv.org',		// by Ru Lee (cerca-tree at ya.ru)
		'.cerc-sx.org',		// by Kon Bi (cerca-two at ya.ru)
		'.cerc-te.org',		// by Ru Lee (cerca-tree at ya.ru)
		'.cerc-tr.org',		// by cerca-one at ya.ru
		'.cerc-tw.org',		// by cerca-one at ya.ru
		'.cerca-fi.org',	// by orgitaly1 at ya.ru
		'.cerca-fo.info',
		'.cerca-no.info',
		'.cerca-on.info',
		'.cerca-sv.info',
		'.cerca-sx.org',	// by orgitaly2 at ya.ru
		'.cerca-te.info',
		'.cerca-tr.info',
		'.cerca-sx.org',
		'.cerca-tr.org',	// orgitaly1 at ya.ru
		'.ricerca-fiv.org',	// orgitaly1 at ya.ru
		'.ricerca-fo.info',
		'.ricerca-one.org',
		'.ricerca-sv.org',
		'.ricerca-sx.org',
		'.ricerca-te.org',
		'.ricerca-tw.org',	// orgitaly1 at ya.ru
		'.subit01.org',
		'.subit02.org',
		'.subit03.org',
		'.subit04.org',
		'.subit05.org',
		'.subit06.org',
		'.subit01.info',
		'.subit02.info',
		'.subit03.info',
		'.subit04.info',
		'.subit05.info',
		'.subit06.info',
	),
	'ernestppc at yahoo.com' => array(	// by Anrey Markov (ernestppc at yahoo.com)
		'.5-base.com',
		'.pharmacy-style.com',
	),
	'snmaster at yandex.ru' => array(	// by Andrey M Somov (snmaster at yandex.ru)
		'.ista-2006.ru',
		'.wefas.ru',
	),
	'sidor2 at gmail.com' => array(	// by Sipiki (sidor2 at gmail.com)
		'.tourismworldsite.info',
		'.yourtourismtravel.info',
	),
	'x-mail007 at mail.ru' => array(	// by Boris britva (x-mail007 at mail.ru)
		'.easyfindcar.info',
		'.siteinfosystems.info',
	),
	'smesh1155 at gmail.com' => array(
		'.hospitalforyou.info',			// by Gimmi
		'.thephentermineonline.info',	// by Kipola
	),
	'supermaster at pisem.net' => array(	// by Aleksandr Krasnik (supermaster at pisem.net), ns *.msn-dns.com
		'.kiski.net.in',
		'.pipki.org.in',
		'.popki.ind.in',
		'.siski.co.in',
	),
	'tiptronikmike at mail.com' => array(
		'tiptronikmike at mail.com' => '#^(?:.*\.)?[irvyz][0-5]sex\.info$#',
		// by Michael Tronik (tiptronikmike at mail.com), e.g. 
		// by Martin Brest (brestmartinjan at yahoo.com), e.g. 74.52.150.242
		// by Adulterra Inkognita (inkognitaadulterra at yahoo.com), e.g. 74.52.150.244
		//'.i0sex.info',		// Michael
		//'.i1sex.info',		// Michael
		//'.i2sex.info',		// Martin
		//'.i3sex.info',		// Martin
		//'.i4sex.info',		// Adulterra
		//'.i5sex.info',		// Adulterra
		//[irvyz]6sex.info not found
		'.i8sex.info',			// by Martin
	),
	'skuarlytronald at mail.com' => array(
		'.girlsfreewild.info',		// by Ronald Skuarlyt (skuarlytronald at mail.com), the same / with i4sex.info, post with z2sex.info, 64.27.13.120
		'.girlsgoingmad.info',		// 64.27.13.120
		'.girlsgonewildside.info',	// 64.27.13.120
	),
	'66.232.109.250' => array(
		'.1626pornporno.info',
		'.1851pornporno.info',
		'.1876pornporno.info',
		'.476pornporno.info',
	),
	'LiveAdultHost.com' => array(	// by Daniel Simeonov (dsim at mbox.contact.bg)
		'.compactxxx.com',
		'.eadulthost.com',
		'.eadultview.com',
		'.eroticpool.net',
		'.ipornservice.com',
		'.liveadulthost.com',
		'.nudepal.com',
		'.sweetservers.com',
	),
	'support at orgija.org' => array(
		'.assfuckporn.org',
		'.dosugmos.org',
		'.fuckporn.org',
		'.girlsdosug.org',
		'.girlsporno.org',
		'.moscowintim.org',
		'.pornass.org',
		'.pornopussy.org',
		'.progirlsporn.org',
		'.pussypornogirls.org',
	),
	'125.65.112.93' => array(
		'.gamanir.com',		// by yangjianhe (upload888 at 126.com), malicious file
		'.twurbbs.com',		// by mingzhong ni (ggyydiy at 163.com)
	),
	'm_koz at mail.ru' => array(	// 217.11.233.76 by Kozlov Maxim
		'.beta-google.com',
		'.tv-reklama.info',
		'.ebooktradingpost.com',		// Anonymous but 217.11.233.76, ns *.ruswm.com
		'.constitutionpartyofwa.org',	// Anonymous but 217.11.233.76, ns *.ruswm.com, "UcoZ WEB-SERVICES"
	),
	'81.0.195.148' => array(	// Says: "GOOGLE LOVES ME!!!", I don't think so. the same post with m_koz found
		'.abobrinha.org',
		'.aneurysmic.com',		// / not found
		'.physcomp.org',		// / not found
		'.seriedelcaribe2006.org',
		'.refugeeyouthinamerica.com',
	),
	'skip_20022 at yahoo.com' => array(
		// 203.174.83.55
		'.a28hosting.info',		// by Bill Jones
		'.besthealth06.org',	// by yakon, "Free Web Hosting Services" but "BestHealth"
		'.besthentai06.org',	// by yakon
	),
	'USFINE.com' => array(
		'.usfine.com',			// 74.52.201.108 by Tang zaiping (tzpsky at gmail.com)
		'.usfine.net',			// 74.52.201.109 by zaiping tang (zppsky at gmail.com)
	),
	'68.178.211.57' => array(
		'.igsstar.com',				// 68.178.211.57 by igsstar at hotmail.com, PARK31.SECURESERVER.NET, pl
		'.powerleveling-wow.com',	// 68.178.211.57 by zhang jun (zpq689 at 163.com)
	),
	'rambap at yandex.ru' => array(	// by Equipe Tecnica Ajato (rambap at yandex.ru)
		'.google-yahoo-msn.org',
		'.expedia-travel.org',
	),
	'admin at newestsearch.com' => array(	// by Gibrel Sitce
		'.emr5ce.org',
		'.wfe7nv.org',
		'.xyr99yx.org',
	),
	'203.171.230.39' => array(
		// by 51shell at 163.com
		'.amatou-fc2.com',

		// registrar bizcn.com, iframe + cursor
		'.playonlinenc.com',
		'.playboss-jp.com',
	),
	'Digi-Rock.com' => array(
		'.rom776.com',
		// owner-organization: DIGIROCK, INC.
		// owner-email: domain-contact at digi-rock.com
		// with an external ad-and-JavaScript,
		// says "This site introduces rom776."(Note: Actual rom776 is the another site, http://776.netgamers.jp/ro/ , says s/he don't own rom776.com)
		// "Actually, this site has been motivated by a desire to researching search-engine-rank of this site, and researching how the people place this site.".
	),
	'snap990 at yahoo.com' => array(	// by John Glade (snap990 at yahoo.com)
		'.date-x.info',				// 208.73.34.48(support-office.hostican.com -> 208.79.200.16)
		'.getrun.cn',				// 208.73.34.48
		'.ipod-application.info',	// NO IP
		'.love-total.net',			// 208.73.34.48, was 74.50.97.136(server.serveshare.com)
		'.stonesex.info',			// NO IP, was 74.50.97.136
	),
	'germerts at yandex.ru' => array(
		// 89.188.112.64(allworldteam.com by aakin at yandex.ru) by Sergey Marchenko (germerts at yandex.ru)
		'.andatra.info',
		'.banchitos.info',
		'.batareya.info',
		'.blevota.info',
		'.broneslon.info',
		'.gamadril.info',
		'.gipotenuza.info',
		'.govnosaklo.info',
		'.muflon.info',
		'.termitnik.info',
		'.tugosos.info',		// 206.53.51.77(www.bmezine.com -> ..)
	),
	'84.252.148.80' => array(	//  84.252.148.80(heimdall.mchost.ru)
		'.acronis-true-image.info',
		'.calcio-xp.info',
		'.cosanova.info',
		'.cose-rx.info',
		'.dictip.info',
		'.findig.info',
		'.fotonow.info',
		'.lavoro-tip.info',
		'.loan-homes.info',
		'.mionovita.info',
		'.mustv.info',
		'.newsnaked.info',
		'.online-tod.info',
		'.opakit.info',
		'.opanow.info',
		'.opriton.info',
		'.porta-bl.info',
		'.refdif.info',
		'.xzmovie.info',
	),
	'84.252.148.120 etc' => array(
		'.isurfind.ru',			// 84.252.148.120 by Egor S Naumov (prpramer at narod.ru)
		'.planetavilton.info',	// 84.252.148.120
		'.softfind.info',		// 84.252.148.80 by Dmitriy (dimamcd at yandex.ru)
	),
	'cxh at 99jk.com' => array(	// by xinghao chen (cxh at 99jk.com), ns *.hichina.com, health care
		'.99jk.com',
		'.99jk.com.cn',
		'.99jk.cn',
	),
	'kiler81 at yandex.ru' => array(	// by Vasiliy (kiler81 at yandex.ru)
		'.kliktop.biz',
		'.kliktop.org',
		'.pharmatop.us',
		'.supertop.us',
		'.supervaizer.info',
	),
	'infomed2004 at mail.ru' => array(	// by Andrey Ushakov (infomed2004 at mail.ru)
		'.freeamateursexx.info',	// 81.0.195.228
		'.freeanalsexx.info',		// 217.11.233.97
		'.freegaysexx.info',		// 81.0.195.228
	),
	'support at dns4me.biz' => array(	// 89.149.228.237 by John Black (support at dns4me.biz)
		'.abbhi.info',
		'.gayblogguide.biz',
		'.huope.info',
		'.thebdsmday.info',
		'.zioprt.info',			// 89.149.228.237
	),
	'dzheker at yandex.ru' => array(	// by dzheker at yandex.ru
		'.boblisk.info',
		'.factyri.info',
		'.jorge1.info',
	),
	'lichincool at gmail.com' => array(	// 72.232.229.115 by lichincool at gmail.com, / meanless
		'.bestmindstorm.org',
		'.redstoreonline.org',
	),
	'59.106.24.2' => array(	// 59.106.24.2, sakagutiryouta at yahoo.co.jp
		'.8e8ae.net',
		'.c-cock.com',
		'.fa59eaf.com',
		'.set-place.net',
		'.sex-beauty.net',
	),
	'84.252.148.140' => array(	// 84.252.148.140(kratos.mchost.ru)
		'.tomdir.info',
		'.tomdirdirect.info',
		'.tomdirworld.info',
		'.treton.info',
		'.trefas.info',
		'.tretonmondo.info',
		'.unefout.info',
		'.unefoutprojet.info',
		'.unitfree.info',
		'.vilret.info',
		'.vilttown.info',
		'.votrefout.info',
		'.warmfind.info',
		'.warptop.info',
		'.wildtram.info',
		'.xofind.info',
		'.xopdiscover.info',
		'.xopfind.info',
		'.xoplocate.info',
		'.xopseek.info',
		'.xpfirst.info',
		'.xphighest.info',
		'.xptop.info',
	),
	'info at thecanadianmeds.com' => array(	// by Andrey Smirnov (info at thecanadianmeds.com)
		'.myviagrasite.com',	// 80.74.153.2
		'.thecanadianmeds.com',	// 80.74.153.17
	),
	'sania at zmail.ru' => array(	// by Mark Williams (sania at zmail.ru)
		'.bigemot.com',				// 217.11.233.34, / not found
		'.espharmacy.com',			// 217.11.233.34
		'.pharmacyonlinenet.com',	// 216.195.51.59, hidden JavaScript
		'.ringtonecooler.com',		// 217.11.233.34
	),
	'dfym at dfym.cn' => array(
		// ANI, JavaScript, iframe

		// 220.166.64.44 by chen jinian (dfym at dfym.cn)
		'.okwit.com',
		'.sakerver.com',
		'.motewiki.net',

		'.caremoon.net',	// 221.10.254.63 by abc00623 at 163.com, Admin by dfym, ns *.myhostadmin.net

		'.8568985.com',		// 61.139.126.10 by liaojiaying88 at sina.com, Admin by dfym, ns *.myhostadmin.net

		// 61.139.126.47, by guoyongdan at 126.com, Tech by dfym, ns *.myhostadmin.net
		'.bbtv-chat.com',
		'.ketubatle.com',
		'.playonlanei.com',
		'.rmtfcne.com',

	),
	'mkiyle at gmail.com' => array(	// by Mihelich (mkiyle at gmail.com)
		'.findcraft.info',			// 209.8.28.11(209-8-28-11.pccwglobal.net)
		'.lookmedicine.info',		// 206.161.205.22
		'.lookshop.info',			// 209.8.40.52(goes.to.high.school.in.beverly-hills.ca.us)
		'.searchhealth.info',		// 206.161.205.30(seg.fau.lt)
		'.worldsitesearch.info',	// 209.8.40.59
	),
	'lee.seery at gmail.com' => array(
		'.klikgoogle.com',	// 64.21.34.55(klikgoogle.com), by KLIK Media GmbH (max at awmteam.com)
		'.lingvol.com',		// 64.21.34.55
		'.micevol.com',		// 64.21.34.55
		'.heyhey.info',		// 64.21.34.55	by anonymous
	),
	'69-64-64-71.dedicated.abac.net etc' => array(	// ns *.trklink.com
		// 69-64-64-71.dedicated.abac.net
		'.520-ard.info',
		'.550bcards.info',
		'.559-cads.info',
		'.559caard.info',
		'.565-caaard.info',
		'.575cadr.info',
		'.577cadrs.info',
		'.590-acrds.info',
		'.596-cadrs.info',
		'.596caards.info',
		'.596caaard.info',
		'.asstablishingcads.info',
		'.astablish-ard.info',
		'.astablishcacrds.info',
		'.begginers-acards.info',
		'.begginersacrds.info',
		'.beggingcaaard.info',
		'.beginercacrds.info',
		'.cacrdscreating.info',
		'.caditbegging.info',
		'.cadr-buildup.info',
		'.cadr-establilsh.info',
		'.cadrs-buildercredit.info',
		'.cadrs570.info',
		'.cads-565.info',

		// 69-64-64-113.dedicated.abac.net
		'.interistacards.info',
		'.intrust-ards.info',
		'.intrustacrds.info',
		'.lfixed-ard.info',
		'.lowerate-ard.info',
		'.lowpercentage-ard.info',
		'.lowpercentageacrd.info',
	),
	'acua at mail.ru' => array(

		'.allmyns.info',	// 84.16.226.29(www.billago.de -> 80.244.243.173 ->  billago.de) by acua at mail.ru, / forbidden

			// by adventurer at allmyns.info
			'.zgyfubsvi.cn',	// 84.16.251.218(*.internetserviceteam.com)

			// by webmaster at allmyns.info
			'.degvc.cn',	// 84.16.226.216(s3an.ath.cx -- DyDNS)
			'.ihpvy.cn',	// 84.16.226.28(www.fs-tools.de -> 80.244.243.172 -> fs-tools.de)
			'.lbtuo.cn',	// 84.16.255.254(*.internetserviceteam.com)
			'.liunc.cn',	// 84.16.249.241(ip2.frankfurt.mabako.net -> 84.16.234.167 ->  frankfurt.mabako.net)
			'.rcyqr.cn',	// 84.16.226.217(mand.zapto.org -- Non-existent)
			'.rekth.cn',	// 89.149.196.19(www.kosmetik-eshop.de ->  80.244.243.181 -> ip1.rumsoft-webhosting.de)
			'.riumh.cn',	// 84.16.226.28(*snip*)
			'.zbtym.cn',	// 84.16.251.219(*.internetserviceteam.com)
			'.zjcgx.cn',	// 217.20.112.102(*.internetserviceteam.com)
			'.zxvlr.cn',	// 84.16.255.253(*.internetserviceteam.com)

		// 84.16.249.240(euro.lotgd.pl -> 88.198.6.42), / says 'noy found'
		'.dns4babka.info',	// by acua at mail.ru

			// by golubcov at dns4babka.info
			'.toiulw.cn',	// 89.149.196.72(mendoi.fansubs.omni-forums.net -> 72.9.144.200)

			// by webmaster at dns4babka.info
			'.credh.cn',
			'.fucfv.cn',
			'.gdxnk.cn',
			'.sqrrt.cn',
			'.ywtmd.cn',
			'.kncqy.cn',	// by webmaster at allmyns.info

		// 84.16.251.222(alinoe.org -> 212.85.96.95 -> v00095.home.net.pl), / says 'noy found'
		'.dedka2ns.info',	// by acua at mail.ru

			// by chasik at dedka2ns.info
			'.vlqnoj.cn',	// 84.16.251.220(*.internetserviceteam.com)

			// by rochel at dedka2ns.info
			'.fudhxjjqh.cn',

			// by webmaster at dedka2ns.info
			'.ascpo.cn',
			'.jgycr.cn',
			'.nqdtt.cn',
			'.oswde.cn',
			'.qeyig.cn',
			'.soqsx.cn',
			'.ukncd.cn',
			'.zijgb.cn',

		// 84.16.255.253(*snip*), / says 'noy found'
		'.dns4dedka.info',	// by acua at mail.ru

			// by aleon at dns4dedka.info
			'.afjjvumqa.cn',	// 84.16.249.240(*snip*)

			// by dig at dns4dedka.info
			'.bdnge.cn',	// 84.16.226.28(*snip*)
			'.dcdsu.cn',	// 217.20.112.102(*snip*)
			'.fhgdp.cn',	// 84.16.249.239(euro.lotgd.pl -> 88.198.6.42)
			'.frvdv.cn',	// 84.16.226.28(*snip*)
			'.heulw.cn',	// 84.16.226.217(*snip*)
			'.hissw.cn',	// 84.16.249.240(*snip*)
			'.lwqjr.cn',	// 84.16.255.253(*snip*)
			'.obwew.cn',	// 84.16.251.218(*snip*)
			'.otkiu.cn',	// 84.16.255.254(*snip*)
			'.pztkq.cn',	// 89.149.228.163(*.internetserviceteam.com)
			'.rgjcs.cn',	// 84.16.251.219(*snip*)
			'.rjskp.cn',	// 84.16.249.241(*snip*)
			'.sokrp.cn',	// 84.16.226.217(*snip*)
			'.ubtnp.cn',	// 84.16.226.29(*snip*)
			'.vdecc.cn',	// 84.16.226.29(*snip*)
			'.vgkkc.cn',	// 89.149.196.72(*snip*)
			'.vqsmy.cn',	// 84.16.249.239(*snip*)
			'.xcmsp.cn',	// 84.16.251.223(freebsd .. what)
			'.xiuky.cn',	// 84.16.251.222(*snip*)
			'.xrqcd.cn',	// 89.149.196.19(*snip*)

			// by la at dns4dedka.info
			'.aeyzf.cn',	// 84.16.251.218(*snip*)
			'.blvqo.cn',	// 84.16.249.241(*snip*), Expiration Date: 2008-08-16
			'.bgslu.cn',	// 89.149.228.163(*snip*)
			'.dxouw.cn',	// 84.16.255.253(*snip*)
			'.ecsbe.cn',	// 84.16.251.218(*snip*)
			'.eothy.cn',	// 84.16.249.241(*snip*)
			'.epocy.cn',	// 84.16.251.220(*snip*)
			'.ewvjw.cn',	// 89.149.196.72(*snip*)
			'.faacz.cn',	// 84.16.251.222(*snip*)
			'.filun.cn',	// 89.149.196.72(*snip*)
			'.fzdpk.cn',	// 84.16.249.239(*snip*)
			'.hatyg.cn',	// 84.16.251.223(*snip*)
			'.hmtqn.cn',	// 84.16.249.240(*snip*)
			'.ibfte.cn',	// 89.149.196.19(*snip*)
			'.jcaym.cn',	// 84.16.249.240(*snip*)
			'.iqzaw.cn',	// 84.16.255.254(*snip*)
			'.jclsf.cn',	// 84.16.249.240(*snip*)
			'.jefdh.cn',	// 84.16.249.240(*snip*)
			'.kchjh.cn',	// 84.16.251.219(*snip*)
			'.krumo.cn',	// 84.16.226.217(*snip*)
			'.lbava.cn',	// 217.20.112.102(*snip*)
			'.mqrtw.cn',	// 84.16.226.29(*snip*)
			'.njpgv.cn',	// 84.16.251.219(*snip*)
			'.npovm.cn',	// 84.16.226.28(*snip*)
			'.nyobt.cn',	// 89.149.196.19(*snip*)
			'.ovxxt.cn',	// 84.16.251.223(*snip*)
			'.owhwz.cn',	// 89.149.228.163(*snip*)
			'.ozjyi.cn',	// 84.16.249.241(*snip*)
			'.pfnzj.cn',	// 84.16.226.217(*snip*)
			'.pixvf.cn',	// 84.16.255.254(*snip*)
			'.qydph.cn',	// 89.149.228.163(*snip*)
			'.rxens.cn',	// 89.149.196.72(*snip*)
			'.sojbp.cn',	// 84.16.249.239(*snip*)
			'.srths.cn',	// 84.16.251.222(*snip*)
			'.tdytc.cn',	// 84.16.255.254(*snip*)
			'.unquz.cn',	// 84.16.251.223(*snip*)
			'.uwcns.cn',	// 89.149.196.19(*snip*)
			'.vcbdm.cn',	// 84.16.251.220(*snip*)
			'.wnmat.cn',	// 84.16.255.253(*snip*)
			'.wttmr.cn',	// 84.16.226.29(*snip*)
			'.xpwib.cn',	// 84.16.251.220(*snip*)
			'.yrogt.cn',	// 84.16.249.239(*snip*)

			// by le at dns4dedka.info
			'.goslw.cn',	// 84.16.251.220(*snip*)
			'.hqbmh.cn',	// 84.16.251.223(*snip*)
			'.iewik.cn',	// 84.16.255.254(*snip*)
			'.jnkeh.cn',	// 89.149.228.163(*snip*)
			'.pifyp.cn',	// 89.149.228.163(*snip*)
			'.nohyl.cn',	// 89.149.196.72(*snip*)
			'.nvzvx.cn',	// 84.16.255.254(*snip*)
			'.uchoe.cn',	// 84.16.249.239(*snip*)
			'.ujoyf.cn',	// 84.16.251.218(*snip*)
			'.ulfqh.cn',	// 89.149.196.19(*snip*)
			'.vxugv.cn',	// 84.16.251.223(*snip*)
			'.dbgti.cn',	// 84.16.249.240(*snip*)
			'.oelmv.cn',	// 84.16.226.28(*snip*)
			'.qniww.cn',	// 84.16.251.218(*snip*)
			'.vtvyq.cn',	// 84.16.251.219(*snip*)
			'.zqonm.cn',	// 84.16.249.241(*snip*)

			// by skspb at dns4dedka.info
			'.hxkxkjy.cn',	// 84.16.226.28(*snip*)

			// 84.16.255.253(*snip*) by webmaster at dns4dedka.info
			'.bcpnb.cn',
			'.cfbpr.cn',
			'.dnndb.cn',
			'.ekwme.cn',
			'.iutps.cn',
			'.ryftj.cn',
			'.vxqcb.cn',

		'.ns2best.info',	// 89.149.196.19(*snip*) by acua at mail.ru
			// by apple at ns2best.info
			'.ptiey.cn',	// 84.16.252.80(*.internetserviceteam.com)
			// by paulwolf at ns2best.info
			'.ifytzq.cn',
		
		'.sedns.info',		// 84.16.226.216(*snip*) by acua at mail.ru
			// by pigato at sedns.info
			'.xjvbunksa.cn',	// 217.20.112.102(*snip*)
			// by roschem at sedns.info
			'.ilzelqvmoa.cn',	// 84.16.243.170(*.fixshell.com -> Non-existent)
			// by op at sedns.info
			'.bumfn.cn',
			'.csmlu.cn',
			'.epcwi.cn',
			'.krwzn.cn',
			'.lkvcp.cn',
			'.mvmkh.cn',
			'.nwpeg.cn',
			'.rnqol.cn',
			'.tyuaf.cn',
			'.ucysa.cn',
			'.vhyom.cn',
			'.wtstu.cn',
			'.zslxr.cn',
	),
	'gilvcta sy jilbertsbram.com' => array(
		// 206.53.51.126
		'.dsfljkeilm1.cn',
		'.dsfljkeilm2.cn',
		'.dsfljkeilm3.cn',
		'.dsfljkeilm4.cn',
		'.dsfljkeilm5.cn',
		'.dsfljkeilm6.cn',
		'.dsfljkeilm7.cn',
		'.dsfljkeilm8.cn',
		'.dsfljkeilm9.cn',
		'.dsfljkeilm10.cn',
	),
	'ganzer3 at gmail.com' => array(	// by Roman Shteynshlyuger (ganzer3 at gmail.com)

		// 69.64.82.76(*.dedicated.abac.net)
		'.bruised-criedit.info',
		'.bruised-crtedit.info',
		'.bruised-czrd.info',
		'.bruisedcreditcars.info',
		'.bruisedcreitcard.info',
		'.bruisedcreitd.info',
		'.buliderscreadet.info',
		'.cleaningup-cdit.info',
		'.cleaningup-cedict.info',
		'.cleaningup-cerdic.info',
		'.cleanup-crrd.info',

		// 69.64.82.77(*.dedicated.abac.net)
		'.bruised-crediot.info',
		'.bruised-credtid.info',
		'.bruisedcriet.info',
		'.bruisedredit.info',
		'.buliders-crdt.info',
		'.buliders-cre4dit.info',
		'.buliders-creadt.info',
		'.buliders-credcards.info',
		'.buliders-credictcard.info',
		'.cleaningupccreditcards.info',
		'.cleaningupcdedit.info',
		'.cleaningupcedirt.info',
		'.cleaningupceidt.info',
		'.cleaningupcrasd.info',
		'.cleaningupcreait.info',

		// 69.64.82.78(*.dedicated.abac.net)
		'.bruised-cridet.info',
		'.bruised-drecit.info',
		'.bruised-reditcards.info',
		'.bruisedcredikt.info',
		'.bruisedcredith.info',
		'.bruisedcredtid.info',
		'.cleanup-criet.info',
		'.cleanup-csrds.info',
		'.cleanup-dards.info',

		// 69.64.82.79(*.dedicated.abac.net)
		'.bruised-crediotcards.info',
		'.bruised-creditcar.info',
		'.bruised-creid.info',
		'.bruised-creidtcard.info',
		'.buliders-crdit.info',
		'.buliders-creadet.info',
		'.cleaningup-ceridt.info',
		'.cleaningupcrecit.info',
		'.cleaningupccritcard.info',
		'.cleanupdredit.info',
		'.cleanupredit.info',
	),
	'.malwarealarm.com',
		// (206.161.201.216 -> 206-161-201-216.pccwglobal.net)
		// by Eddie Sachs (hostmaster at isoftpay.com), scaring virus, spyware or something
		// NOTE: scanner.malwarealarm.com(206.161.201.212 -> 206-161-201-212.pccwglobal.net)
	'.viagraorder.org',		// IP not allocated, ns *.heyhey.info(IP not allocated) => 89.248.99.110
	'Inet-Traffic.com' => array(
		// "The Inet-Traffic network offers over 6 million unique visitors a month."
		//'.dcomm.com',		// by D Communications Inc. S.A.

		'.freehomepages.com',	// 205.237.204.51 by domains at inet-traffic.com, ns *.dcomm.com
		'.inet-traffic.com',	// 205.237.204.106(reverse.dcomm.com) by domains at inet-traffic.com, ns *.dcomm.com
		// ...
		// 205.237.204.114(www.searchit.com -> 205.237.204.151)
		// ...
		'.homepagez.com',		// 205.237.204.118 by domainadmin at navigationcatalyst.com, ns *.dnsnameserver.org
		// ...
		'.pagerealm.com',	// 205.237.204.121 by domains at inet-traffic.com, ns *.dcomm.com
		'.koolpages.com',	// 205.237.204.122 by domains at inet-traffic.com, ns *.dcomm.com
		'.oddworldz.com',	// 205.237.204.123 by domains at inet-traffic.com, ns *.dcomm.com
		'.cybcity.com',		// 205.237.204.124 by domains at inet-traffic.com, ns *.dcomm.com
		'.cybamall.com',	// 205.237.204.125 by domains at inet-traffic.com, ns *.dcomm.com
		'.haywired.com',	// 205.237.204.126 by domains at inet-traffic.com, ns *.dcomm.com
		'.cyberturf.com',	// 205.237.204.127 by domains at inet-traffic.com, ns *.dcomm.com
		'.dazzled.com',		// 205.237.204.128 by domains at inet-traffic.com, ns *.dcomm.com
		'.megaone.com',		// 205.237.204.129 by domains at inet-traffic.com, ns *.dcomm.com
		// ...
		// 205.237.204.131(www.powow.com -> 205.237.204.136)
		// 205.237.204.132(www.pcpages.com ->  205.237.204.135)
		// ...
		'.pcpages.com',		// 205.237.204.135(reverse.dcomm.com) by domains at inet-traffic.com, ns *.addplace.com
		'.powow.com',		// 205.237.204.136 by domains at inet-traffic.com, ns *.dcomm.com
		// ...
		// 205.237.204.143(gameroom.com -> 72.32.22.210)
		// ...
		'.searchit.com',	// 205.237.204.151(reverse.dcomm.com) by domains at inet-traffic.com, ns *.dcomm.com
					// http://www.trendmicro.com/vinfo/grayware/ve_GraywareDetails.asp?GNAME=ADW_SOFTOMATE.A
		// ...
		'.gameroom.com',	// 72.32.22.210 by julieisbusy at yahoo.com, listed at inet-traffic.com and freehomepages.com
	),
	'andreyletov at yahoo.com related' => array(
		'.180haifa.com',		// 82.103.128.177(e82-103-128-177s.easyspeedy.com) by Andrey Letov
		'.mens-medication.com',	// 89.248.99.118 by Boris Rabinovich
		'.pills-supplier.com',	// 89.248.99.118 by Boris Rabinovich

		// 89.248.99.118 by anonymous
		'.lpgpharmacy.com',
		'.onlybestgalleries.com',
		'.viagrabest.info',
		'.viagrabestprice.info',
		'.viagratop.info',

		'.canadians-medication.com',	// 89.248.99.118 by beseo at bk.ru
		'.absulutepills.com',			// 216.40.236.58(*.ev1servers.net) by bulka at skyhaifa.com, redirect to canadians-medication.com
		'.eyzempills.com',				// 216.40.236.59(*.ev1servers.net) by bulka at skyhaifa.com, redirect to canadians-medication.com
	),
	'alrusnac at hotmail.com' => array(
		'.122mb.com',			// 209.67.214.122 by Alexandru Rusnac (alrusnac at hotmail.com)
		//'.cigarettesportal.com',	// 72.36.211.194(*.static.reverse.ltdomains.com)
	),
	'203.116.63.123' => array(
		'.fast4me.info',		// by Hakan Durov (poddubok at inbox.ru), / is blank
		'.fastmoms.info',		// by Pavel Golyshev (pogol at walla.com), / is blank
	),
	'goodwin77 at bk.ru' => array(	// ns *.petterhill.ru
		'.autotowncar.cn',		// 69.73.146.184
		'.badgirlhome.cn',		// 69.73.146.186
	),
	'Wmp.co.jp' => array(
		// 219.94.134.208, ns *.dns.ne.jp, adult
		'.celebe.net',			// by nic-staff at sakura.ad.jp(using sakura.ne.jp), said: by "wmp.co.jp"
		'.kousyunyu.com',		// Admin/Billing/Tech Email: * at wmp.co.jp
		// online-support.jp	// 202.222.19.81(sv70.lolipop.jp), / blank
	),
	'Macherie.tv' => array(
		'.macherie.tv',			// 124.32.230.31, pr to himehime.com
		'.himehime.com',		// 124.32.230.94(recruit.macherie.tv)
		'.livechatladyjob.com',	// 124.32.230.94 by Hajime Kawagoe (sawada at innetwork.jp), recruiting site for macherie.tv
	),
	'admin at fr4f3ds.info' => array(	// 217.11.233.54, / forbidden
		'.yemine.info',
		'.fr4f3ds.info',
	),
	'75.126.129.222' => array(
		// 75.126.129.222(greatpool.biz -> 72.232.198.234 -> brasilrok.com.br -> ...)
		'.viagrabuycheap.info',	// ns *.advernsserver.com
		'.viagrageneric.org',	// IP not allocated, ns *.heyhey.info(IP not allocated) => 75.126.129.222
	),
	'sqr at bk.ru' => array(
		// 69.46.18.2(hv113.steephost.com -> 72.232.191.50 -> 72.232.191.50.steephost.com)
		'.masserch.info',		// "Free Web Hosting", spam
		'.miserch.info',
	),
	'gangstadra at hotmail.com' => array(
		'.kenrucky.com',		// 82.98.86.167(*.sedoparking.com)
		'.michigab.com',		// 82.98.86.175(*.sedoparking.com)
		'.pimperos.com',		// 82.98.86.163(*.sedoparking.com)
		'.pimpcupid.com',		// 89.149.226.111(*.internetserviceteam.com)
	),
	'hosan by front.ru' => array(
		'.salihome.info',				// 206.53.51.155(non-existent)
		'.online-freesearch.com',		// 206.53.51.155
		'.choosefinest.com',			// 206.53.51.157
		'.carsprojects.com',			// 206.53.51.159
		'.sport-brands.com',			// 206.53.51.167
		'.online-pharmaceutics.com',	// 206.53.51.168
	),
	'89.248.107.118' => array(	// "Canadian Pharmacy" 89.248.107.118(non-existent)
		'.canadians-health.com',	// by andreyletov at yahoo.com
		'.cialischeap.info',
		'.superrv.info',
	),
	'zinerit4 at gmail.com' => array(	// 69.46.29.149(hv37.steephost.com => 72.232.191.50 ...)
		'.hatbi.com',
		'.justrty.com',
		'.kol2you.com',
		'.kolplanet.com',
		'.myrty.com',
		'.officialrty.com',
		'.pbaol.com',
		'.planetkol.com',
	),
	'Abra1.com' => array(
		// 66.45.254.244(non-existent), 66.45.254.245(non-existent)
		'.abra1.com', 		// by info at maisontropicale.com
		'.abra4.com',		// by websites at caribbeanonlineinternational.com

		// 209.85.51.238(*.opticaljungle.com -> non-existent)
		'.abra2.com',		// by domain at anondns.org
		'.abra3.com',		// by domain at anondns.org
	),
	'topdomainz at gmail.com' => array(
		// 217.73.201.237 , ns *.parked.ru
		'.aboutgoogleearth.info',	// IP timed-out, ns *.aboutgoogleearth.info
		'.aboutgooglegroup.info',
		'.aboutgoogleimage.info',
		'.aboutgooglemail.info',
		'.aboutgooglemap.info',
		'.aboutgooglenews.info',
		'.aboutgooglevideo.info',
		'.bestgoogleearth.info',
		'.magicgoogleearth.info',
		'.magicgooglemap.info',
		'.magicgooglevideo.info',

		// 84.16.251.248(*.internetserviceteam.com), ns *.aboutgoogleearth.info
		'.slajzer.info',
		'.tonoscelular.info',
		'.tonosmotorola.info',
	),
	'Anderson at extra.by' => array(
		// Encoded JavaScript

		// 84.16.235.84(ns2.rrbone-dns.net -> non-existent) ns *.5upp.info
		'.4cat.info',	// by Anderson at extra.by, ns *.9upp.info
		'.6dir.info',
		'.8dir.info',
		'.9dir.info',
		'.2pro.info',
		'.5pro.info',	// by Anderson at extra.by, ns *.9upp.info
		'.6pro.info',
		'.7pro.info',
		'.8pro.info',
		'.9pro.info',
		'.5sms.info',
		'.6sms.info',
		'.8sms.info',
		'.9sms.info',
		'.1upp.info',
		'.2upp.info',
		'.3upp.info',
		'.4upp.info',
		'.5upp.info',
		'.6upp.info',
		'.7upp.info',
		'.8upp.info',
		'.9upp.info',	// 89.149.208.23(*.internetserviceteam.com) by Anderson at extra.by, ns *.9upp.info
		'.2vip.info',	// by Anderson at extra.by
		'.4vip.info',
		'.5vip.info',
		'.6vip.info',
		'.7vip.info',
		'.8vip.info',	// ns *.9upp.info
		'.9vip.info',

		// 89.149.208.23(*.internetserviceteam.com) by evdesign at gmail.com, ns *.9upp.info
		'.w3rar.info',
		'.w3sex.info',
		'.w3zip.info',
		'.w3in.info',
		'.w3out.info',
	),
	'zorkin87 at mail.ru' => array(
		// 66.232.112.242(hv78.steephost.com -> 72.232.191.50 -> *.steephost.com)
		'.ruqore.com',
		'.goxama.com',
		'.cazebi.com',
		'.fukeqa.com',
		'.fydoge.com',
		'.jukaly.com',
		'.jumoga.com',
		'.jumuxo.com',
		'.tyriva.com',
		'.vudyho.com'
	),
	'jessicaeagloff at yahoo.co.uk' => array(
		'.pilltrade.com',		// 91.196.219.81(non-existent) by jessicaeagloff at yahoo.co.uk, ns *.rxrxrxrx.com
		'.rxrxrxrx.com',		// IP not allocated by postmaster at cialischoice.com
		'.cialischoice.com',	// 81.29.249.88(non-existent) by postmaster at cialischoice.com, ns *.rxp-hosting.com
		'.rxp-hosting.com',		// IP not allocated by jessicaeagloff at yahoo.co.uk
	),
	'qbbs at xinoffice.com' => array(
		// ns *.name-services.com
		'.lingage.com',		// 8.15.231.118
		'.puksins.com',		// IP not allocated now
	),
	'advertolog at gmail.com' => 
		// 81.0.250.86(network.upl.cz => non-existent) or
		// 217.11.233.27
		'#^(?:.*\.)?download-mp3-music-0(?:[0-2][0-9]|30)\.cn$#',	// 001-030
	'contact at aboutdomain.com' => array(
		'.lopieur0.com',	// Not match but used
		'.lopieur1.com',	// Not match but used
		'.lopieur2.com',	// 209.62.21.228(*.ev1servers.net => non-existent) by contact at aboutdomain.com
	),
	'info at cash4wm.biz related' => array(
		// 84.16.233.12(*.internetserviceteam.com)
		'.newsblogforu.com',

		// 84.16.235.141()
		'.informator4you.com',

		// 84.16.240.68(*.internetserviceteam.com)
		'.absolute-best-site.com',
		'.new-blog-for-you.com',

		// 84.16.240.69(*.internetserviceteam.com)
		'.best-sport-blog.com',
		'.big-internet-site.com',
		'.brendsite.com',
		'.my-news-blog.com',

		// 84.16.242.123(*.internetserviceteam.com)
		'.best-news-blog.com',
		'.site-with-content.com',

		// The same method with info at cash4wm.biz
		'.coolnews-4u.com',		// 217.20.123.122(*.internetserviceteam.com),
		'.bestnews-4u.com',		// 84.16.227.135(*.internetserviceteam.com)
		'.big-site-news.com',	// 84.16.243.168(*.fixshell.com -> non-existent)
		'.good-news-4u.info',	// 89.149.196.72(mendoi.fansubs.omni-forums.net -> ..)
		'.super-blog-here.com',	// 89.149.247.25, the same IP with iblcqms.cn
		'.bloginfo4u.info',		// 89.149.247.25
	),
	'kerjy at yahoo.com' => array(
		'.wibek.com',	// 66.232.126.212(hv71.steephost.com -> ..) by boldalex at yahoo.com
		'.hehik.com',	// 66.232.126.213(hv71.steephost.com -> ..) by kerovska at mail.nu
		'.mynyh.com',	// 66.232.126.214(hv71.steephost.com -> ..) by kerjy at yahoo.com
		'.mabej.com',	// 66.232.126.215(hv71.steephost.com -> ..) by jeffy at hotmail.com
		'.qanom.com',	// 66.232.126.216(hv71.steephost.com -> ..) by kerjy at yahoo.com
	),
	'vme at hotmail.ru' => array(
		'.seoblack.net',	// 216.195.33.112(non-exsistent)
		'.seoblack.biz',	// 216.195.33.113(non-exsistent)
	),
	'john at hellomyns.info' => array(
		'.cuhdw.cn',		// 217.20.113.27, / nothing
		'.byouz.cn',		// 89.149.247.24, / nothing
		'.idghc.cn',
		'.inxrj.cn',
		'.jqzdf.cn',
		'.vsxej.cn',
		'.wkgnr.cn',
	),
	'.CN spam payload' => array(
		'.i3sa.cn',			// by varra6yu4lt8607 at yahoo.com
		'.bjlzhh.cn',		// 210.51.162.236(non-existent) by bjlzhh at 163.com
		'.iactive.com.cn',	// 60.28.204.205(non-existent) by buggd at 263.net
		'.njss.com.cn',		// 219.142.175.25(mail.codeprof.com) by admin at yywt.com
		'.qimo.com.cn',		// 219.235.228.55(non-existent) by baojieshuma at 163.com
		'.wzhj.com.cn',		// 218.244.136.78 by nemesisxue at yahoo.com.cn
		'.ytbaixin.com.cn',	// 210.22.13.42(sym.gdsz.cncnet.net) by ytwqg at 163.com
		'.tj-008.cn',		// 222.35.3.90(non-existent) by dinmo.net at gmail.com
		'.xglzl.cn',		// 60.215.129.74(non-existent) by 148044648 at 163.com
		'.xixii.cn',		// 218.206.72.210(non-existent) by chenzhen8168 at yahoo.com.cn
	),
	'Time2ns.info' => array(
		// time2ns.info(84.16.226.58 -> *.internetserviceteam.com), "Fedora Core Test Page" says this is private
		'.klmnei.cn',		// 84.16.243.123(*.internetserviceteam.com) by arsen at time2ns.info(84.16.226.58)
		'.lumyjugmn.cn',	// 89.149.243.225(*.internetserviceteam.com) by kashin at time2ns.info
		'.uxmrscgdi.cn',	// 89.149.247.23(*.internetserviceteam.com) by Bmurphy at itsmyns.info
		'.iblcqms.cn',		// 89.149.247.25(*.internetserviceteam.com) by Gershun at time2ns.info, / not found
	),
	'Ilovemyns.info' => array(
		// ilovemyns.info(89.149.247.26 -> *.internetserviceteam.com), "Fedora Core Test Page" says this is private
		'.aasghwf.cn',		// 84.16.243.121(*.internetserviceteam.com) by Shooll at ilovemyns.info
	),
	'Freehostdns.info' => array(
		// freehostdns.info(217.20.112.24 -> neviem.kto.sk), "Fedora Core Test Page" says this is private
		'.qyxswynd.cn',		// 89.149.247.26(*.internetserviceteam.com) by margarita at freehostdns.info
		'.ugivorm.cn',		// 217.20.127.219(*.internetserviceteam.com) by Chos at freehostdns.info
	),
	'Newns4me.info' => array(
		// newns4me.info(217.20.127.231 -> pls.dont.eat.shit.la), "Fedora Core Test Page" says not public
		'.lxwxjzpiy.cn',	// 217.20.113.27(*.internetserviceteam.com) by Alexan at newns4me.info
	),

	// C-2: Lonely domains (buddies not found yet)
	'.0721-4404.com',
	'.0nline-porno.info',	// by Timyr (timyr at narod.ru)
	'.101010.ru',			// 72.232.246.178(spirit.intellovations.com -> 207.218.230.66) by gkrg94g at mail.ru, / forbidden
	'.1-click-clipart.com',	// by Big Resources, Inc. (hostmaster at bigresources.com)
	'.19cellar.info',		// by Eduardo Guro (boomouse at gmail.com)
	'.1gangmu.com',			// by gangmutangyaoju (wlmx009 at hotmail.com), Seems physing site for ff11-jp.com
	'.1gb.cc',				// by Hakan us (hakanus at mail.com)
	'.1gb.in',				// by Sergius Mixman (lancelot.denis at gmail.com)
	'.0annie.info',
	'.6i6.de',
	'.77toperrwuter1.info',	// by Roman
	'.99-idea.com',			// 202.44.54.48(*.worldinternetworkcorporation.com) by mistercolor at gmail.com, encoded JavaScript
	'.angioco.com',			// 62.37.112.100, gamble
	'.advancediet.com',		// by Shonta Mojica (hostadmin at advancediet.com)
	'.adult-master-club.com',	// by Alehander (mazyrkevich at cosmostv.by)
	'.adultpersonalsclubs.com',	// by Peter (vaspet34 at yahoo.com)
	'.akalukseree.com',			// 202.44.52.54(*.worldinternetworkcorporation.com) by nantachit at yahoo.com, encoded JavaScript
	'.akgame.com',			// 72.32.79.100 by Howard Ke (gmtbank at gmail.com), rmt & pl
	'.alasex.info',			// 'UcoZ web-services' 216.32.81.234(server.isndns.net) by yx0 at yx0.be
	'.alfanetwork.info',	// by dante (dantequick at gmail.com)
	'.allworlddirect.info',	// Forbidden
	'.amoreitsex.com',
	'.angel-live.com',		// 61.211.231.181, ns *.netassist.ne.jp, pr to himehime.com
	'.angelkiss.jp',		// 59.106.45.50, pr to himehime.com and chatwalker.com
	'.approved-medication.com',	// 208.109.181.53(p3slh079.shr.phx3.secureserver.net)
	'.areahomeinfo.info',	// by Andrus (ffastenergy at yahoo.com), republishing articlealley.com
	'.areaseo.com',			// by Antony Carpito (xcentr at lycos.com)
	'.ascotstationers.com',	// 89.149.228.164(*.internetserviceteam.com) by hoopeer at gmail.com
	'.auto-car-cheap.org',
	'.banep.info',			// by Mihailov Dmitriy (marokogadro at yahoo.com), iframe to this site
	'.baurish.info',
	'.bernardtomic21.info',	// by filboj at hotmail.com
	'.bestop.name',
	'.betmmo.com',			// 63.223.98.182 by Huang Qiang (liuxing-wushi at hotmail.com), pl
	'.bestrademark.info',	// by victoria (niko16d at yahoo.com), redirect to majordomo.ru
	'.bestshopfinder.info',
	'.blogest.org',			// 203.116.63.68 by Bobby.R.Kightlinger at pookmail.com, / seems blank
	'.bookblogsite.org',	// 217.11.233.58 by Eugene.E.Mather at mailinator.com
	'.brisbanedecking.com',	// 61.14.187.244(orion.websiteactive.com) by jgstubb at bigpond.net.au, / nothing
	'.businessplace.biz',	// by Grenchenko Ivan Petrovich (eurogogi at yandex.ru)
	'.buyshaliflute.com',	// 70.84.133.34(epsilon.websiteactive.com) by bigwave3000 at ozemail.com.au
	'.capital2u.info',		// by Delbert.A.Henry at dodgeit.com
	'.casa-olympus.com',	// "UcoZ WEB-SERVICES"
	'.cashing-view.com',	// 210.188.201.22(sv69.xserver.jp) by kikuchi at money-agent.jp
	'.catkittenmagazines.org',		// 87.118.97.117
	'.cdmsolutionsinc.com',	// 206.123.100.160 by rpeterson at ricochet.com
	'.chatwalker.com',		// 124.32.230.65
	'.cosamoza.com',		// 216.195.51.62 by travelwins at yahoo.com
	'.covertarena.co.uk',	// by Wayne Huxtable
	'.d999.info',			// by Peter Vayner (peter.vayner at inbox.ru)
	'.dance-ithaca.com',	// 203.191.238.22(non-existent) by iichiba at hotmail.com, finance
	'.dinmo.cn',			// 218.30.96.149 by dinso at 163.com, seo etc.	//'.wow-gold.dinmo.cn',	// 125.65.76.59, pl
	'.dinmoseo.com',		// 210.51.168.102(winp2-web-g02.xinnetdns.com) by jianmin911 at 126.com, NS *.xinnetdns.com, seo
	'.discoverproducts.info',// 216.195.58.37(non-existent) by snarku at gmail.com
	'.dlekei.info',			// by Maxima Bucaro (webmaster at tts2f.info)
	'.dollar4u.info',		// by Carla (Carla.J.Merritt at mytrashmail.com), / is blank
	'.drug-shop.us',			// by Alexandr (matrixpro at mail.ru)
	'.drugs-usa.info',		// by Edward SanFilippo (Edward.SanFilippo at gmail.com), redirect to activefreehost.com
	'.dssoaps.com',			// 67.15.104.24(pluto.websiteactive.com) by donnabl at iprimus.com.au, encoded JavaScript
	'.easypharmsite.com',	// 75.126.129.229(greatpool.biz => 72.232.198.234 => ) by andrew at pharmacy-inet.com
	'.easyshopusa.com',		// by riter (riter at nm.ru)
	'.edjuego.com',			// 85.92.70.253(non-existent), gamble
	'.edu.ph',				// "philippine network foundation inc"
	'.eec.mn',					// 208.109.203.142(*.ip.secureserver.net), encoded JavaScript
	'.ex-web.net',			// RMT by ex co,ltd (rmt at ex-web.net)
	'.extracheapmeds.com',	// "freexxxmovies" by John Smith (89 at bite.to)
	'.fantasy-handjob-ra.com',	// by Hose Pedro (hosepedro at gmail.com)
	'.fastppc.info',		// by peter conor (fastppc at msn.com)
	'.ffxiforums.net',		// 204.16.199.105 by Zhang xiaolong (mail at 33986.com), hidden VBScript
	'.filthserver.com',		// sales at onlinemarketingservices.biz
	'.find-stuff.org',		// by Alice Freedman (admin at ip-labs.ru), / 404 Not Found
	'.firstdrugstorezone.info',	// by Goose (boris208 at yandex.ru)
	'.free-finding.com',	// by Ny hom (nyhom at yahoo.com)
	'.free-rx.net',			// by Neo-x (neo-xxl at yandex.ru), redirect to activefreehost.com
	'.free-sex-movie-net.info',	// by vitas61 at yahoo.com
	'.freeblog.ru',			// by Kondrashov Evgeniy Aleksandrovich (evkon at rol.ru), login form only, ns *.nthost.ru
	'.freehost5.com',		// 75.126.32.184(kosmohost.net), words only
	'.freeliveringtones.com',	// by Silan (lippe1988 at gmail.com)
	'.freemobilephonesworld.info',	// by andresid (andresid1 at yandex.ru)
	'.fwjjjtmrlr.cn',		// 217.20.127.232(really.cut3.info -> non-existent) by analog at gamehostingns.info(217.20.113.79)
	'.game4enjoy.net',		// by huang jinglong (fenlin231 at sina.com)
	'.game4egold.com',		// by Filus Saifullin (ebay at soft-script.com)
	'.goldcoastonlinetutoring.com',	// by Robert Tanenbaum (buildbt at lycos.com)
	'.golden-keys.net',		// 89.149.205.146(unknown.vectoral.info) by aktitol at list.ru
	'.gomeodc.com',			// 125.65.112.49 by wang meili (gannipo at yahoo.com.cn), iframe to vviccd520.com
	'.ganecity.com',		// by shao tian (huangjinqiang at sina.com)
	'.gm-exchange.jp',		// 210.188.216.49 RMT
	'.goamoto.ru',			// by Dmitry E Kotchnev (z2archive at gmail.com)
	'.good1688.com',		// by Wen Chien Lunz (wzk1219 at yahoo.com.tw), one of them frame to , and whoop.to
	'.google-pharmacy.com',	// by alex (mdisign1997 at yahoo.com), hiding with urlx.org etc
	'.greatbestwestern.org',// by gao.wungao at gmail.com
	'.greatsexdate.com',	// by Andreas Crablo (crablo at hotmail.com)
	'.guesttext.info',		// 81.0.195.134 by Grace.D.Kibby pookmail.com, / seems null
	'.guild-wars-online.com',	// by Fuzhou Tianmeng Touzi Zixun Co.,Ltd (welkin at skyunion.com)
	'.happyhost.org',		// by Paul Zamnov (paul at zamnov.be)
	'.hloris.com',			// by Wilshi Jamil (ixisus at front.ru)
	'.honda168.net',		// by tan tianfu (xueyihua at gmail.com), seems not used now
	'.hostuju.cz',			// ns banan.cz, banan.it
	'.hot4buy.org',			// by Hot Maker (jot at hot4buy.org)
	'.hotscriptonline.info',// by Psy Search (admin at psysearch.com)
	'.hostzerocost.com',	// server failed, by Gokhan Yildirim (gokhany at gmail.com)
	'.iinaa.net',			// domain at ml.ninja.co.jp, ns *.shinobi.jp
	'.incbuy.info',			// by Diego T. Murphy (Diego.T.Murphy at incbuy.info)
	'.informator4you.com',	// 84.16.235.141 by info at cash4wm.biz
	'.infradoc.com',
	'.investorvillage.com',	// by natalija puchkova (internet at internet.lv)
	'.ismarket.com',		// Google-hiding. intercage.com related IP
	'.italialiveonline.info',	// by Silvio Cataloni (segooglemsn at yahoo.com), redirect to activefreehost.com
	'.italy-search.org',	// by Alex Yablin (zaharov-alex at yandex.ru)
	'.itsexosit.net',
	'.itxxxit.net',
	'.hostsy.us',			// 217.11.233.21 by Julian.L.Harrington at dodgit.com, / empty, redirection
	'.jimmys21.com',		// by Klen Kudryavii (telvid at shaw.ca)
	'.jimka-mmsa.com',		// by Alex Covax (c0vax at mail.ru), seems not used yet
	'.joynu.com',			// by lei wang (93065 at qq.com), hidden JavaScript
	'.jsyuanyang.com',		// 58.211.0.23(non-existing)
	'.karasikov.net',			// 217.107.217.7(server3.jino.ru -> 217.107.217.17) by pashkanet at list.ru, encoded JavaScript
	'.kingtools.de',
	'.kymon.org',			// by Albert Poire (isupport at yahoo.com), / Forbidden, 70.87.62.252
	'.leucainfo.com',
	'.library-blogs.net',	// by Peter Scott (pscontent at gmail.com)
	'.lightyearmedia.com',	// 216.104.33.66(esc91.midphase.com) by techsupport at midphase.com, encoded JavaScript
	'.link-keeper.net',		// 210.172.108.236 (257.xrea.com)
	'.ls.la',				// by Milton McLellan (McLellanMilton at yahoo.com)
	'.m-sr.net',			// 210.175.62.125(non-existent) by luckynets2003 at yahoo.co.jp, ns *.ARK-NET.NE.JP
	'.mamaha.info',			// by Alex Klimovsky (paganec at gmail.com), seems now constructiong
	'.manseekingwomanx.com',// by Bill Peterson (coccooc at fastmail.fm)
	'.mdjadozone.org',		// 64.34.124.103(non-existent) by info at infoconceptlc.com, / blank
	'.medicineonlinestore.com',	// Alexander Korovin (domains at molddata.md)
	'.medpharmaworldguide.com',	// by Nick Ivchenkov (signmark at gmail.com), / not found
	'.megvideochatlive.info',	// Bad seo
	'.milfxxxpass.com',		// by Morozov Pavlik (rulets at gmail.com)
	'.mncxvsm.info',		// 217.11.233.105, / blank
	'.moremu.com',			// 205.134.190.12(amateurlog.com) by Magaly Plumley (domains ay moremu.com)
	'.morfolojna.cn',		// 206.53.51.126, by gilserta at jilbertsabram.com, Using web.archive.org
	'.myfgj.info',			// by Filus (softscript at gmail.com)
	'.mujiki.com',			// by Mila Contora (ebumsn at ngs.ru)
	'.mxsupportmailer.com',
	'.next-moneylife.com',	// RMT
	'.newalandirect.com',	// by Alnoor Hirji, ns *.sablehost.com
	'.ngfu2.info',			// by Tara Lagrant (webmaster at ngfu2.info)
	'.northwestradiator.com',	// 66.206.12.166(caudill.cc -> 63.115.2.1 -> ...) by nwradiator at qwest.net, encoded JavaScript
	'.nucked-sex.com',		// 203.223.150.222 by lis (noidlis2 at yahoo.com)
	'.ok10000.com',			// by zipeng hu (ldcs350003 at hotmail.com)
	'.olimpmebel.info',		// by pol (pauk_life at mail.ru), frame to bettersexmall.com
	'.onlinetert.info',		// by Jarod Hyde (grigorysch at gmail.com)
	'.onlin-casino.com',	// by Lomis Konstantinos (businessline3000 at gmx.de)
	'.onlineviagra.de',
	'.oppe.biz',			// 81.0.195.241(non-existent) by Nellie.J.Gonzalez at mailinator.com, / blank, redirect to thesuperxxx.com
	'.ornit.info',			// by Victoria C. Frey (Victoria.C.Frey at pookmail.com)
	'.ozomw.info',
	'.pahuist.info',		// by Yura (yuralg2005 at yandex.ru)
	'.pelican-bulletin.info',	// by Elizabeth K. Perry (redmonk at mail.ru)
	'.perevozka777.ru',		// by witalik at gmail.com
	'.pharmacy2online.com',	// by Mike Hiliok (bbong80 at yahoo.com)
	'.pills-storage.com',	// by 
	'.plusintedia.com',		// by g yk (abc00623 at 163.com), seems not used now
	'.porkyhost.com',		// 79965 at whois.gkg.net
	'.porno-babe.info',		// by Peter (asdas at mail.ru), redirect to Google
	'.pornesc.com',			// by Xpeople (suppij atmail.ru)
	'.portaldiscount.com',	// by Mark Tven (bestsaveup at gmail.com)
	'.powerlevelingweb.com',	// 68.178.211.9 by jun zhang (huanbing at 126.com), pl
	'.prama.info',			// by Juan.Kang at mytrashmail.com
	'.privatedns.com',		// 209.172.41.50(sebulba.privatedns.com), encoded JavaScript, root of various posts
	',pulsar.net',			// by TheBuzz Int. (theboss at tfcclion.com)
	'.qoclick.net',			// by DMITRIY SOLDATENKO
	'.quality-teen-porn-photo.com',	// by info at densa.info
	'.relurl.com',			// tiny-like. by Grzes Tlalka (grzes1111 at interia.pl)
	'.replicaswatch.org',	// by Replin (admin at furnitureblog.org)
	'.rigame.info',			// by debra_jordan07 at yahoo.com
	'.rmt-trade.com',		// by wang chun (dlxykj at 126.com), rmt
	'.roin.info',			// by Evgenius (roinse at yandex.ru)
	'.rpz3zmr75a.com',		// 216.188.26.235(park-www.trellian.com, redirects to domainparkltd.com) by hostmaster at domainparkltd.com(216.188.26.235)
	'.rutewqsfrt10.cn',		// 72.36.237.146(*.static.reverse.ltdomains.com) by ferrari by list.ru
	'.save-darina.org',		// 85.14.36.36 by Plamen Petrov (plamen5rov at yahoo.com)
	'.search99top.info',	// 85.255.118.236 / not found, ns *.f01137d.com, redirect to abosearch.com
	'.searchadv.com',		// by Jaan Randolph (searchadv at gmail.com)
	'.seek-www.com',		// by Adam Smit (pingpong at mail.md)
	'.sessocities.net',		// 66.98.162.20(*.ev1servers.net: Non-existent domain) by info at secureserver3.com
	'.seven-pharmacy.com',	// 83.138.176.247 by Justin Timberlake (preved at gmail.com)
	'.sexamoreit.com',
	'.sexforit.com',
	'.sexmaniacs.org',		// by Yang Chong (chong at x-india.com)
	'.sexsmovies.info',		// 203.174.83.22 by dima (vitas at vitas-k.com)
	'.sirlook.com',
	'.so-net.ws',			// by Todaynic.com Inc, seems a physing site for so-net.jp
	'.sepcn.info',			// / not found
	'.sslcp.com',			// by shufang zhou (info at 6come.com), dns *.hichina.com
	'.sticy.info',			// by Richard D. Mccall (richardmccall at yahoo.com)
	'.stordproduksjonslag.no',	// 81.27.32.152(wh42.webhuset.no) by helgatun at mail.com, encoded JavaScript
	'.super-discount.sakura.ne.jp',	// 59.106.19.206(www756.sakura.ne.jp), sales
	'.superrwm.info',		// by Dark Dux (duxdark at yahoo.com)
	'.superverizonringtones.com',	// by joshua at list.ru
	'.teriak.cn',			// 83.69.224.82(non-existent) by sto_xyev at yahoo.com, / blank
	'.thebest-penis-enlargement-pills.com',	// 209.59.142.226(host.gudzonserver.com) by sergey.xoxlov at gmail.com
	'.thehostcity.com',		// Domains by Proxy
	'.thesuperxxx.com',		// 81.29.249.27(non-existent)
	'.thetinyurl.com',		// by Beth J. Carter (Beth.J.Carter at thetinyurl.com), / is blank
	'.thetrendy.info',		// by Harold (Harold.J.Craft at pookmail.com), / is blank
	'.theusapills.com',		// by Dr. Zarman (contactus at theusapills.com)
	'.tingstock.info',		// 209.160.73.65(delta.xocmep.info) "nice day, commander ;)" by Andrey Konkin (konkinnews55 at yahoo.com)
	'.topmeds10.com',
	'.tschofenig.com',			// 213.239.234.98(*.clients.your-server.de), port 8080
	'.truststorepills.com',	// 89.188.113.64(allworldteam.com) by Alexey (admin at myweblogs.net)
	'.twabout.com',			// by qiu wenbing (qiuwenbing at 126.com), content from l2mpt.net
	'.uaro.info',			// by Neru Pioner (neru at smtp.ru)
	'.unctad.net',			// by gfdogfd at lovespb.com
	'.vacant.org.uk',
	'.viagrausaonline.com',	// 85.17.52.139(non-existent)
	'.vip-get.info',		// 203.223.150.222 by Jhon Craig (bartes1992 at mail.ru), / forbidden
	'.virtualsystem.de',
	'.vdxhost.com',
	'.vodkaporn.com',		// 67.19.116.83(non-existent) by green at gmx.co.uk
	'.vviccd520.com',		// 202.75.219.217 by kuang zhang (oulingfeng66 at 163.com), encoded JavaScript
	'.homes.com.au',		// 139.134.5.124 by wongcr at bigpond.net.au, / meanless,
	'.wbtechs.us',			// 68.178.232.100(parkwebwin-v01.prod.mesa1.secureserver.net) by westbabylon at aol.com
	'.webnow.biz',			// by Hsien I Fan (admin at servcomputing.com)
	'.webtools24.net',		// by Michael Helminger (info at ishelminger.de)
	'.wer3.info',			// by Martin Gundel (Martin at mail.com), 404 not found
	'.withsex.net',			// by C.W.Jang (jangcw1204 at naver.com)
	'.whoop.to',			// RMT
	'.womasia.info',		// by Mark Fidele (markfidele at yahoo.com)
	'.worldinsurance.info',	// by Alexander M. Brown (Alex_Brown at yahoo.com), fake-antivirus
	'.wow-powerleveling-wow.com',	// 63.223.77.112 by dingmengxl at 126.com, pl
	'.wowgoldweb.com',		// 64.202.189.111(winhostecn28.prod.mesa1.secureserver.net) by lei chen (dreamice at yeah.net), rmt & pl
	'.wwwna.info',			// / 404 Not Found
	'.xpacificpoker.com',	// by Hubert Hoffman (support at xpacificpoker.com)
	'.xamorexxx.net',
	'.xn--gmqt9gewhdnlyq9c.net',	// 122.249.16.133(x016133.ppp.asahi-net.or.jp) by daizinazikanwo yahoo.co.jp
	'.xsessox.com',
	'.xxxmpegs.biz',		// 217.11.233.65, redirect to *.malwarealarm.com, / null
	'.yoi4.net',			// by Ryouhei Nakamura (888 at sympathys.com), tell me why so many blogs with popular issues and _diverted design from blog.livedoor.jp_ around here.
	'.zlocorp.com',			// by tonibcrus at hotpop.com, spammed well with "http ://zlocorp.com/"
	'.zyguo.info',			// ns globoxhost.net
	'.zhuyiw.com',			// by zhou yuntao (whzyt0122 at sohu.com)


	// C-3: Not classifiable (information wanted)
	//
	// Something incoming to pukiwiki related sites
	'nana.co.il related' => array(
		'.planetnana.co.il',
		'.nana.co.il',
	),
);

// --------------------------------------------------

$blocklist['D'] = array(
	// D: Sample setting of
	// "Third party in good faith"s
	//
	// Hosts shown inside of the implanted contents,
	// not used via spam, but maybe useful to detect these contents
	//
	// 'RESERVED',
);

// --------------------------------------------------

$blocklist['E'] = array(
	// E: Sample setting of
	// Promoters
	// (Affiliates, Hypes, Catalog retailers, Multi-level marketings, Resellers,
	//  Ads, Business promotions, SEO, etc)
	//
	// They often promotes near you using blog article, mail-magazines, tools(search engines, blogs, etc), etc.
	// Sometimes they may promote each other

	'15-Mail.com related' => array(
		'.15-mail.com',				// 202.218.109.45(*.netassist.jp) by yukiyo yamamoto (sunkusu5268 at m4.ktplan.ne.jp)
		'.1bloglog.com',			// 210.253.115.159 by Yukiyo Yamamoto (info at 15-mail.com)
		'.investment-school.com',	// 210.253.115.159 by Yukiyo Yamamoto (info at 15-mail.com)
		'.breakjuku.com',			// 210.253.115.159 (service provider bet.co.jp = xserver.jp)
		'.nambara.biz',				// by Yukiyo Yamamoto (info at 15-mail.com)
	),
	'.all-affiliater.com',			// 202.222.30.18(sv125.lolipop.jp), ns *.lolipop.jp
	'.chachai.com',					// 210.188.205.161(sv339.lolipop.jp) by tetsuo ihira (chachai at hida-kawai.jp)
	'E-brainers.com related' => array(
		// 202.212.14.101
		'.cyoto-morketing-club.com',	// by Fujio Iwasaki (domain at sppd.co.jp)
		'.e-brainers.com',				// by Fujio Iwasaki (domain at sppd.co.jp)
		'.my-tune.jp',					// by brainers Inc.
		'.technical-support-center.com',// by Fujio Iwasaki (domain at sppd.co.jp)
		'.weekle.jp',					// by brainers Inc.

		// 210.136.111.56 by Masatoshi Kobayashi (domain at e-brainers.com)
		// 210.136.111.56 by Fujio Iwasaki (domain at sppd.co.jp)
		'.3minutes-marketing-club.com',	// by Fujio
		'.affiliate-vampire.com',		// by Masatoshi
		'.article-site-power-package.com',	// by Masatoshi
		'.audio-marketing-club.com',	// by Fujio
		'.brainers-task-manager.com',	// by Masatoshi
		'.brainers-troubleshooter-generator.com',	// by Masatoshi
		'.brainersbuzz.com',			// by Masatoshi
		'.den4renz-marketing-club.com',	// by Fujio
		'.english-contents-club.com',	// by Masatoshi
		'.fly-in-ads-japan.com',		// by Fujio
		'.free-resalerights-giveaway.com',	// by Fujio
		'.freegiveawaysecret.com',		// by Masatoshi
		'.guaranteedvisitorpro.com',	// by Masatoshi
		'.havads-japan.com',			// by Masatoshi
		'.info-business123.com',		// by Fujio
		'.instant-marketing-club.com',	// by Fujio
		'.internetmarketinggorinjyu.com',	// by Masatoshi
		'.marketing-force-japan.com',	// by Fujio
		'.masatoshikobayashi.com',		// by Fujio
		'.profitsinstigator.com',		// by Masatoshi Kobayashi (akada at e-brainers.com)
		'.replytomatt.com',				// by Fujio
		'.santa-deal.com',				// by Fujio
		'.santa-deal-summer.com',		// by Fujio
		'.scratch-card-factory.com',	// by Masatoshi
		'.script4you-japan.com',		// by Fujio
		'.sell1000000dollarinjapan.com',// by Fujio
		'.squeeze-page-secret.com',		// by Masatoshi
		'.viral-blog-square.com',		// by Fujio
		'.viralarticle.com',			// by Fujio
		'.wowhoken.com',				// by Fujio

		// 202.212.14.104 by Fujio Iwasaki  (domain at sppd.co.jp)
		'.brainerstelevision.com',
		'.demosite4you.com',
		'.keywordcatcherpro.com',
		'.script-marketing-club.com',

		// 202.228.204.140(server.ultimate-marketing-weapon.com) by Masatoshi Kobayashi (akada at e-brainers.com)
		// 202.228.204.140 by Masatoshi Kobayashi (domain at e-brainers.com)
		// 202.228.204.140 by Naoki Kobayashi (info at bet.co.jp)
		'.1sap.com',			// by Naoki, ns *.ultimate-marketing-weapon.com
		'.brainers.ws',			// by info at key-systems.net, ns *.ultimate-marketing-weapon.com
		'.brainerscode.com',	// by akada
		'.brainerslive.com',	// by domain
		'.brainersreview.com',	// by domain
		'.brainerstest.com',	// by akada
		'.otosecret.com',		// by domain
		'.ultimate-marketing-weapon.com',	// by akada
		'.planet-club.net',		// 202.228.204.141(server.ultimate-marketing-weapon.com)
		'.terk.jp',				// by Tsuyoshi Tsukada, QHM

		'.samuraiautoresponder.com',	// 211.125.179.75(bq1.mm22.jp) by Masatoshi Kobayashi (kobayashi at wowhoken.com)
		'.sppd.co.jp',		// 210.136.106.122 by Studio Map Ltd., ns *.sppd.ne.jp, spam
	),
	'.e2996.com',			// 202.181.105.241(sv261.lolipop.jp)
	'ezinearticles.com',	// 216.235.79.13 by C Knight (opensrs at sparknet.net)
	'.fx4rich.com',			// 219.94.128.161(www921.sakura.ne.jp) by Yuji Nakano (info at will76.com)
	'gonz-style.com',		// 210.251.253.242(s187.xrea.com) by arai at p-cafe.net, reseller
	'Hokuken.com' => array(		// Bisuness promotion, affiliate about QHM
		'.hokuken.com',		// 210.188.216.191(sv399.lolipop.jp) by Takahiro Kameda (registrant email admin at muumuu-domain.com)
		'.1st-easy-hp.com',	// 210.188.201.45(sv84.xserver.jp) by takahiro kameda (customer at hokuken.com)
		'.open-qhm.net',	// 125.53.25.8(s297.xrea.com), was 202.222.31.223(sv183.lolipop.jp) "Open QHM by hokuken"

		// Redirects and affiliates:
		// (They all use "paperboy and co." related services, muumuu-domain.com and lolipop.jp)
		//   yousense.info/fwd/mama1 redirects to www.infocart.jp/af.php?af=520517&url=www.1st-easy-hp.com/index.php?iInfoCart&item=XXXX    redirects to www.1st-easy-hp.com/index.php?iInfoCart
		//   info.mizo3.com/fwd/qhm  redirects to (*snip*)af=moukaru88y(*snip*)
		//   sanpei.vc/fwd/startkit  redirects to (*snip*)af=katosanpe(*snip*)
		//   ysa-techno.com/fwd/homepagesakusei redirects to (*snip*)af=yukko777(*snip*)
		//   qhm.kikikan.com        points several links to (*snip*)af=kikikan(*snip*)
		//   www.writeonjp.com/puki points several links to (*snip*)af=notes(*snip*)
		//   ...

		// Other suggestions:
		//   info.mizo3.com says: "Owners who bought QHM from MY site..."

		// Blog posting at the same time
		//   2007/06/13 hidenonikki.seesaa.net/archives/20070613-1.html
		//   2007/06/13 e123.info/archives/76
		//   2007/06/14 bobbin1950.seesaa.net/archives/20070614-1.html
		//   2007/06/25 ichibankantan.seesaa.net/article/52650651.html
		//   2007/06/26 www.neko01.com/pc/blog/2007/06/open_quick_homepage_maker_1.php
	),
	'info at kobeweb.jp' => array(
		'.soholife.jp',		// 211.125.65.203 by Takashige Tabuchi (info at kobeweb.jp)
		'.kobeweb.jp',		// 59.106.13.51(www421.sakura.ne.jp)
		'.sloters.tv',		// 211.125.65.203 by Takashige Tabuchi (t-2 at white.interq.or.jp)
	),
	'.info-affiliate.net',	// 219.94.148.8(sv41.chicappa.jp)
	'Infocart.jp' => array(		// by wai at infocart.jp
		// Trying to earn money easily by selling 'earn-money-easiliy' tips
		//descr:        INFOMAG (Shimooka,Yasuyoshi)
		'.infocart.jp',			// 60.32.154.171, by Hawaiikigyo, affiliate

		//inetnum:      60.32.154.176 - 60.32.154.183
		// descr:        HAWAII KIGYO.COM (Shimooka,Yasuyoshi)
		'.infomag.jp',			// 60.32.154.179, by Infocart,  business promotion
	),
	'Info-sniper.com' => array(
		'.1koibana.com',		// 59.106.12.162(sv265.lolipop.jp), says "Info-sniper presents"
		'.info-sniper.com',		// 202.222.18.25(sv37.lolipop.jp)
	 	'.mailjoho.com',		// 202.222.19.81(sv70.lolipop.jp), says "Info-sniper", link to info-sniper.com and 2muryoureport.com
	),
	'.infostore.jp',		// 216.255.235.45, ns *.estore.co.jp
	'.junquito55.com',		// 59.106.12.213(sv281.lolipop.jp)
	'JunSuzuki.com' => array(	// e-brainers.com related
		'.junsuzuki.com',		// 218.216.67.43(s92.xrea.com) by Jun Suzuki (jun_suzuki at compus.net)
		'.globalswing.biz',		// 210.188.217.109(sv27.xserverzero.net)
	),
	'Natsukih.net' => array(
		'.natsukih.net',		// 210.188.245.5(sv04.futurismworks.jp) by natsuki hayashi(ii at leo.bekkoame.ne.jp)
		'.1seikou.biz',			// 221.186.251.73(s68.xrea.com), says "by natsuki hayashi", "Heisei natsuki project"
		'.oniblog.net',			// 210.251.253.242(s187.xrea.com), says "Heisei natsuki project", see also blog.1seikou.biz
		'.muryoureport.com',	// 210.188.205.58(sv311.lolipop.jp), redirect to 2muryoureport.com

		// by info at natsukih.net
		'.1muryoureport.com',	// 222.227.75.40(s162.xrea.com), says  "Heisei natsuki project"
		'.2muryoureport.com',	// 59.139.29.227(s233.xrea.com), says  "Heisei natsuki project"
		'.3muryoureport.com',	// 222.227.75.45(s167.xrea.com)
		'.4muryoureport.com',	// 202.222.31.77(sakura1.digi-rock.com)
		'.5muryoureport.com',	// 210.251.253.230
		'.6muryoureport.com',	// 202.222.31.77(*snip*)
		'.7muryoureport.com',	// 202.222.31.77(*snip*)
		'.muryouaff.com',		// 210.251.253.238(s183.xrea.com)
	),
	'Point-park.com' => array(	// Tadahiro Ogawa (domain at wide.ne.jp)
		'.11kanji.com',		// 211.10.131.88
		'.mlmsupport.jp',	// 211.10.131.108 by info at point-park.com
		'.point-park.com',	// 211.10.131.88
		'.point-park.jp',	// 43.244.140.160(160.140.244.43.ap.yournet.ne.jp)
	),
	'.potitto.info',		// 219.94.132.89(sv450.lolipop.jp)
	'PRJAPAN.co.jp' => array(
		//'.prjapan.co.jp',		// 211.10.20.143(sv.prjapan.co.jp)
		'go.prjapan.co.jp',		// 210.189.72.220(sv.webstars.jp)
		'.bestsale.jp',			// 210.189.77.143 "Public Relations Inc." by info at prjapan.co.jp, kamita at prjapan.co.jp, nakade at prjapan.co.jp
		'.hyouka-navi.jp',		// 202.218.52.63(sv.numberzoo.jp) by kamita at i-say.net
		'.sugowaza.jp',			// 210.189.77.143(sv.bestsale.jp) by kamita at i-say.net, 
		//'.webstars.jp',		// 210.189.72.220  by info at prjapan.co.jp
	),
	'Rakuten.co.jp' => array(
		'hb.afl.rakuten.co.jp',		///hsc/ 203.190.60.104 redirect to rakuten.co.jp
		'hbb.afl.rakuten.co.jp',	///hsb/ 203.190.60.105 image server?
	),
	'.sedori-data.com',		// 210.188.205.7(sv03.lolipop.jp)
	'.seozone.jp',			// 211.133.134.77(sv27.wadax.ne.jp) by blue_whale20002004 at yahoo.com.cn
	'.tool4success.com',	// 210.188.201.31(sv70.xserver.jp) by Yukihiro Akada (ml at original-ehon.com)
	'tera at kirinn.com' => array(	// 59.139.29.234(s240.xrea.com) by Naohsi Terada (tera at kirinn.com)
		'.e123.info',
		'.ialchemist.net',
		'.j012.net',
		'.xn--yckc2auxd4b6564dogvcf7g.biz',
	),
	'Viscose.jp' => array(
		'.web-navi21.com',	// 202.222.30.12 by kazuhiro shikano(shikano at guitar.ocn.ne.jp)
		'.viscose.jp',		///link/ 210.188.205.205(sv370.lolipop.jp) by  kazuhiro shikano
	),
	'.zakkuzaku.com',		// 210.188.201.44(sv83.xserver.jp)
);

// --------------------------------------------------

$blocklist['Z'] = array(
	// Z: Yours
	//
	//'',
	//'',
	//'',
);

?>
