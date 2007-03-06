<?php
// $Id: spam.ini.php,v 1.31 2007/03/06 12:09:10 henoheno Exp $
// Spam-related setting
//
// Reference:
//   Spamdexing http://en.wikipedia.org/wiki/Spamdexing


$blocklist['goodhost'] = array(
	'IANA-examples' => '#^(?:.*\.)?example\.(?:com|net|org)$#',

	// PukiWiki-official/dev specific
	//'logue.tk',		// Well-known PukiWiki heavy user, Logue (Paid *.tk domain, Expire on 2008-12-01)
	//'*.logue.tk',
	// nyaa.tk
		//'nyaa.tk',	// (Paid *.tk domain, Expire on 2008-05-19)
		//'*.nyaa.tk',
		//'wanwan.tk',	// (Paid *.tk domain, Expire on 2008-04-21)
		//'*.wanwan.tk',
	//'ifastnet.com'	// www.isfastnet.com

	// Yours
	//''
	//''
	//''

);

$blocklist['badhost'] = array(

	// A: Sample setting of
	// Existing URI redirection or masking services

	// A-1: By HTTP redirection, HTML meta, HTML frame, JavaScript,
	// or DNS subdomains
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
	'*.110mb.com',
	'123.que.jp',
	'12url.org',
	'*.15h.com',
	'*.1dr.biz',
	'1nk.us',
	'1url.org',
	'1url.in',
	'1webspace.org',
	'2ch2.net',
	'2hop4.com',
	'2s.ca',
	'2site.com',
	'2url.org',
	'301url.com',
	'32url.com',
	'3dg.de',
	'*.3dg.de',
	'*.4bb.ru',
	'5jp.net',
	'6url.com',
	'*.6url.com',
	'*.6x.to',
	'7ref.com',
	'82m.org',
	'*.8rf.com',
	'98.to',
	'*.abwb.org',
	'acnw.de',
	'active.ws' => array(
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
	'store.adobe.com',	// Stop it
	'aifam.com',
	'amoo.org',
	'arzy.net' => array(
		'jmp2.net',			// "(c) 2007 www.arzy.net", by URLadmin at ZVXR.Com, DNS arzy.net
		'2me.tw',			// "(c) 2007 www.arzy.net", by urladmin at zvxr.com, DNS arzy.net
	),
	'ataja.es',
	'atk.jp',
	'athomebiz.com',
	'athomebiz.com',
	'aukcje1.pl',
	'beermapping.com',
	'besturl.in',
	'biglnk.com',
	'bingr.com',
	'bittyurl.com',
	'*.bizz.cc',
	'*.blo.pl',			// HTML frame
	'fanznet.jp' => array('blue11.jp'),
	'briefurl.com',
	'brokenscript.com',
	'bucksogen.com' => array(
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
	'*.buzznet.com',
	'*.bydl.com',
	'c-o.in' => array(
		'*.c-o.cc',
		'*.c-o.in',
		'*.coz.in',
		'*.cq.bz',
	),
	'c64.ch',
	'c711.com',
	'checkasite.net',
	'*.chicappa.jp',
	'chopurl.com',
	'christopherleestreet.com',
	'*.cjb.net',
	'clipurl.com',
	'*.co.nr',
	'comteche.com' => array(
		'tinyurl.name',
		'tinyurl.us',
	),
	'cool168.com' => array(
		'*.cool158.com',
		'*.cool168.com',
		'*.ko168.com',
		'*.ko188.com',
	),
	'coolurl.de' => array(
		'coolurl.de',
		'dornenboy.de',
		'eyeqweb.com',
		'hardcore-porn.de',
		'maschinen-bluten-nicht.de',
	),
	'cutalink.com',
	'*.da.cx',
	'*.da.ru',
	'dae2.com',
	'dephine.org',
	'desiurl.com',
	'digbig.com',
	'digipills.com' => array(
		'*.digipills.com',
		'minilien.com',
		'tinylink.com',
	),
	'*.discutbb.com',
	'dl.am' => array(
		'*.cx.la',
		'*.dl.am',
	),
	'*.dmdns.com',
	'doiop.com',
	'drlinky.com',
	'durl.us',
	'*.dvdonly.ru',
	'*.dynu.ca',
	'elfurl.com',
	'eny.pl',
	'*.eu.org',
	'f2b.be' => array(
		'*.f2b.be',
		'*.freakz.eu',
		'*.n0.be',
		'*.n3t.nl',
		'*.short.be',
		'*.ssr.be',
		'*.tweaker.eu',
	),
	'*.fancyurl.com',
	'fanznet.com' => array(
		'fanznet.com',
		'katou.in',
		'mymap.in',
		'saitou.in',
		'satou.in',
		'susan.in',
	),
	'ffwd.to',
	'url.fibiger.org',
	'fireme.to' => array(
		'fireme.to',
		'nextdoor.to',
		'ontheway.to',
	),
	'flingk.com',
	'fm7.biz',
	'fnbi.jp',
	'*.fnbi.jp',
	'*.freecities.com',
	'freeservers.com' => array(
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
	'fyad.org',
	'fype.com',
	'*.fx.to',
	'gentleurl.net',
	'get2.us' => array(
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
	'goonlink.com',
	'gourl.org',
	'*.gourl.org',
	'greatitem.com',
	'*.greatitem.com',
	'gzurl.com',
	'url.grillsportverein.de',
	'harudake.net' => array('*.hyu.jp'),
	'here.is',
	'hispavista.com' => array(
		'*.hispavista.com',
		'galeon.com',
		'*.galeon.com',
	),
	// by Home.pl Sp. J. (info at home.pl), redirections and forums
	'home.pl' => array(
		'*.8l.pl',
		'*.blg.pl',
		'*.ryj.pl',
		'*.xit.pl',
		'*.xlc.pl',
		'*.hk.pl',
		'*.home.pl',
	),
	'hort.net',
	'*.hotindex.ru',
	'hotredirect.com' => array(
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
	'hurl.to',
	'*.hux.de',
	'*.i89.us',
	'*.iceglow.com',
	'ie.to',
	'igoto.co.uk',
	'ilook.tw',
	'inetwork.co.il' => array(
		'inetwork.co.il',
		'up2.co.il',		// inetwork.co.il related, not classifiable
		'*.up2.co.il',
	),
	'*.infogami.com',
	'ipoo.org',
	'irotator.com',
	'iscool.net' => array(
		'*.arecool.net',
		'*.iscool.net',
		'*.isfun.net',
		'*.tux.nu',
	),
	'iwebtool.com',
	'*.iwebtool.com',
	'jeeee.net',
	'jemurl.com',
	'jggj.net',
	'jpan.jp',
	'kat.cc',
	'kickme.to' => array(
		'1024bit.at',
		'*.1024bit.at',
		'128bit.at',
		'*.128bit.at',
		'16bit.at',	
		'*.16bit.at',
		'256bit.at',
		'*.256bit.at',
		'32bit.at',	
		'*.32bit.at',
		'512bit.at',
		'*.512bit.at',
		'64bit.at',	
		'*.64bit.at',
		'8bit.at',	
		'*.8bit.at',
		'adores.it',
		'*.adores.it',
		'again.at',	
		'*.again.at',
		'allday.at',
		'*.allday.at',
		'alone.at',	
		'*.alone.at',
		'altair.at',
		'*.altair.at',
		'american.at',
		'*.american.at',
		'amiga500.at',
		'*.amiga500.at',
		'ammo.at',	
		'*.ammo.at',
		'amplifier.at',
		'*.amplifier.at',
		'amstrad.at',
		'*.amstrad.at',
		'anglican.at',
		'*.anglican.at',
		'angry.at',	
		'*.angry.at',
		'around.at',
		'*.around.at',
		'arrange.at',
		'*.arrange.at',
		'australian.at',
		'*.australian.at',
		'baptist.at',
		'*.baptist.at',
		'basque.at',
		'*.basque.at',
		'battle.at',
		'*.battle.at',
		'bazooka.at',
		'*.bazooka.at',
		'berber.at',
		'*.berber.at',
		'blackhole.at',
		'*.blackhole.at',
		'booze.at',	
		'*.booze.at',
		'bosnian.at',
		'*.bosnian.at',
		'brainiac.at',
		'*.brainiac.at',
		'brazilian.at',
		'*.brazilian.at',
		'bummer.at',
		'*.bummer.at',
		'burn.at',	
		'*.burn.at',
		'c-64.at',	
		'*.c-64.at',
		'catalonian.at',
		'*.catalonian.at',
		'catholic.at',
		'*.catholic.at',
		'chapel.at',
		'*.chapel.at',
		'chills.it',
		'*.chills.it',
		'christiandemocrats.at',
		'*.christiandemocrats.at',
		'cname.at',	
		'*.cname.at',
		'colors.at',
		'*.colors.at',
		'commodore.at',
		'*.commodore.at',
		'commodore64.at',
		'*.commodore64.at',
		'communists.at',
		'*.communists.at',
		'conservatives.at',
		'*.conservatives.at',
		'conspiracy.at',
		'*.conspiracy.at',
		'cooldude.at',
		'*.cooldude.at',
		'craves.it',
		'*.craves.it',
		'croatian.at',
		'*.croatian.at',
		'cuteboy.at',
		'*.cuteboy.at',
		'dancemix.at',
		'*.dancemix.at',
		'danceparty.at',
		'*.danceparty.at',
		'dances.it',
		'*.dances.it',
		'danish.at',
		'*.danish.at',
		'dealing.at',
		'*.dealing.at',
		'deep.at',	
		'*.deep.at',
		'democrats.at',
		'*.democrats.at',
		'digs.it',	
		'*.digs.it',
		'divxlinks.at',
		'*.divxlinks.at',
		'divxmovies.at',
		'*.divxmovies.at',
		'divxstuff.at',
		'*.divxstuff.at',
		'dizzy.at',	
		'*.dizzy.at',
		'does.it',	
		'*.does.it',
		'dork.at',	
		'*.dork.at',
		'drives.it',
		'*.drives.it',
		'dutch.at',	
		'*.dutch.at',
		'dvdlinks.at',
		'*.dvdlinks.at',
		'dvdmovies.at',
		'*.dvdmovies.at',
		'dvdstuff.at',
		'*.dvdstuff.at',
		'emulators.at',
		'*.emulators.at',
		'end.at',	
		'*.end.at',
		'english.at',
		'*.english.at',
		'eniac.at',	
		'*.eniac.at',
		'error403.at',
		'*.error403.at',
		'error404.at',
		'*.error404.at',
		'evangelism.at',
		'*.evangelism.at',
		'exhibitionist.at',
		'*.exhibitionist.at',
		'faith.at',	
		'*.faith.at',
		'fight.at',	
		'*.fight.at',
		'finish.at',
		'*.finish.at',
		'finnish.at',
		'*.finnish.at',
		'forward.at',
		'*.forward.at',
		'freebie.at',
		'*.freebie.at',
		'freemp3.at',
		'*.freemp3.at',
		'french.at',
		'*.french.at',
		'graduatejobs.at',
		'*.graduatejobs.at',
		'greenparty.at',
		'*.greenparty.at',
		'grunge.at',
		'*.grunge.at',
		'hacked.at',
		'*.hacked.at',
		'hang.at',	
		'*.hang.at',
		'hangup.at',
		'*.hangup.at',
		'has.it',	
		'*.has.it',
		'hide.at',	
		'*.hide.at',
		'hindu.at',	
		'*.hindu.at',
		'htmlpage.at',
		'*.htmlpage.at',
		'hungarian.at',
		'*.hungarian.at',
		'icelandic.at',
		'*.icelandic.at',
		'independents.at',
		'*.independents.at',
		'invisible.at',
		'*.invisible.at',
		'is-chillin.it',
		'*.is-chillin.it',
		'is-groovin.it',
		'*.is-groovin.it',
		'japanese.at',
		'*.japanese.at',
		'jive.at',	
		'*.jive.at',
		'kickass.at',
		'*.kickass.at',
		'kickme.to',
		'*.kickme.to',
		'kindergarden.at',
		'*.kindergarden.at',
		'knows.it',	
		'*.knows.it',
		'kurd.at',	
		'*.kurd.at',
		'labour.at',
		'*.labour.at',
		'leech.at',	
		'*.leech.at',
		'liberals.at',
		'*.liberals.at',
		'linuxserver.at',
		'*.linuxserver.at',
		'liqour.at',
		'*.liqour.at',
		'lovez.it',	
		'*.lovez.it',
		'makes.it',	
		'*.makes.it',
		'maxed.at',	
		'*.maxed.at',
		'means.it',	
		'*.means.it',
		'meltdown.at',
		'*.meltdown.at',
		'methodist.at',
		'*.methodist.at',
		'microcomputers.at',
		'*.microcomputers.at',
		'mingle.at',
		'*.mingle.at',
		'mirror.at',
		'*.mirror.at',
		'moan.at',	
		'*.moan.at',
		'mormons.at',
		'*.mormons.at',
		'musicmix.at',
		'*.musicmix.at',
		'nationalists.at',
		'*.nationalists.at',
		'needz.it',	
		'*.needz.it',
		'nerds.at',	
		'*.nerds.at',
		'neuromancer.at',
		'*.neuromancer.at',
		'newbie.at',
		'*.newbie.at',
		'nicepage.at',
		'*.nicepage.at',
		'ninja.at',	
		'*.ninja.at',
		'norwegian.at',
		'*.norwegian.at',
		'ntserver.at',
		'*.ntserver.at',
		'owns.it',	
		'*.owns.it',
		'paint.at',	
		'*.paint.at',
		'palestinian.at',
		'*.palestinian.at',
		'phoneme.at',
		'*.phoneme.at',
		'phreaking.at',
		'*.phreaking.at',
		'playz.it',	
		'*.playz.it',
		'polish.at',
		'*.polish.at',
		'popmusic.at',
		'*.popmusic.at',
		'portuguese.at',
		'*.portuguese.at',
		'powermac.at',
		'*.powermac.at',
		'processor.at',
		'*.processor.at',
		'prospects.at',
		'*.prospects.at',
		'protestant.at',
		'*.protestant.at',
		'rapmusic.at',
		'*.rapmusic.at',
		'raveparty.at',
		'*.raveparty.at',
		'reachme.at',
		'*.reachme.at',
		'reads.it',	
		'*.reads.it',
		'reboot.at',
		'*.reboot.at',
		'relaxed.at',
		'*.relaxed.at',
		'republicans.at',
		'*.republicans.at',
		'researcher.at',
		'*.researcher.at',
		'reset.at',	
		'*.reset.at',
		'resolve.at',
		'*.resolve.at',
		'retrocomputers.at',
		'*.retrocomputers.at',
		'rockparty.at',
		'*.rockparty.at',
		'rocks.it',	
		'*.rocks.it',
		'rollover.at',
		'*.rollover.at',
		'rough.at',	
		'*.rough.at',
		'rules.it',	
		'*.rules.it',
		'rumble.at',
		'*.rumble.at',
		'russian.at',
		'*.russian.at',
		'says.it',	
		'*.says.it',
		'scared.at',
		'*.scared.at',
		'seikh.at',	
		'*.seikh.at',
		'serbian.at',
		'*.serbian.at',
		'short.as',	
		'*.short.as',
		'shows.it',	
		'*.shows.it',
		'silence.at',
		'*.silence.at',
		'simpler.at',
		'*.simpler.at',
		'sinclair.at',
		'*.sinclair.at',
		'singz.it',	
		'*.singz.it',
		'slowdown.at',
		'*.slowdown.at',
		'socialists.at',
		'*.socialists.at',
		'spanish.at',
		'*.spanish.at',
		'split.at',	
		'*.split.at',
		'stand.at',	
		'*.stand.at',
		'stoned.at',
		'*.stoned.at',
		'stumble.at',
		'*.stumble.at',
		'supercomputer.at',
		'*.supercomputer.at',
		'surfs.it',	
		'*.surfs.it',
		'swedish.at',
		'*.swedish.at',
		'swims.it',	
		'*.swims.it',
		'synagogue.at',
		'*.synagogue.at',
		'syntax.at',
		'*.syntax.at',
		'syntaxerror.at',
		'*.syntaxerror.at',
		'techie.at',
		'*.techie.at',
		'temple.at',
		'*.temple.at',
		'thinkbig.at',
		'*.thinkbig.at',
		'thirsty.at',
		'*.thirsty.at',
		'throw.at',	
		'*.throw.at',
		'toplist.at',
		'*.toplist.at',
		'trekkie.at',
		'*.trekkie.at',
		'trouble.at',
		'*.trouble.at',
		'turkish.at',
		'*.turkish.at',
		'unexplained.at',
		'*.unexplained.at',
		'unixserver.at',
		'*.unixserver.at',
		'vegetarian.at',
		'*.vegetarian.at',
		'venture.at',
		'*.venture.at',
		'verycool.at',
		'*.verycool.at',
		'vic-20.at',
		'*.vic-20.at',
		'viewing.at',
		'*.viewing.at',
		'vintagecomputers.at',
		'*.vintagecomputers.at',
		'virii.at',	
		'*.virii.at',
		'vodka.at',	
		'*.vodka.at',
		'wannabe.at',
		'*.wannabe.at',
		'webpagedesign.at',
		'*.webpagedesign.at',
		'wheels.at',
		'*.wheels.at',
		'whisper.at',
		'*.whisper.at',
		'whiz.at',	
		'*.whiz.at',
		'wonderful.at',
		'*.wonderful.at',
		'zor.org',	
		'*.zor.org',
		'zx80.at',	
		'*.zx80.at',
		'zx81.at',	
		'*.zx81.at',
		'zxspectrum.at',
		'*.zxspectrum.at',
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
	'linkzip.net',
	'lispurl.com',
	'lnk.in',
	'makeashorterlink.com',
	'mcturl.com',
	'memurl.com',
	'metamark.net' => array('xrl.us'),
	'midgeturl.com',
	'minilink.org' => array('lnk.nu'),
	'miniurl.org',
	'miniurl.pl',
	'mixi.bz',
	'mo-v.jp',
	'monster-submit.com',
	'mooo.jp',
	'murl.net',
	'myactivesurf.net',
	'mytinylink.com',
	'myurl.in',
	'myurl.com.tw',
	'nanoref.com',
	'ne1.net' => array(
		'*.ne1.net',
		'*.r8.org',
	),
	'Nashville Linux Users Group' => array('nlug.org'),
	'not2long.net',
	'*.notlong.com',
	'*.nuv.pl',
	'ofzo.be',
	'*.ontheinter.net',
	'*.ozonez.com',
	'palurl.com',
	'*.paulding.net',
	'phpfaber.org',
	'pnope.com',
	'prettylink.com',
	'proxid.net' => array(	// also xrelay.net
		'*.asso.ws',
		'*.corp.st',
		'*.euro.tm',
		'*.perso.tc',
		'*.site.tc',
		'*.societe.st',
	),
		'qrl.jp',
	'qurl.net',
	'qwer.org',
	'redirectfree.com' => array(
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
	'*.rmcinfo.fr',
	'rubyurl.com',
	'*.runboard.com',
	'runurl.com',
	's-url.net',
	'sg5.co.uk' => array(
		'*.sg5.co.uk',
		'*.sg5.info',
	),
	'shim.net' => array(
		'*.0kn.com',
		'*.2cd.net',
		'*.freebiefinders.net',
		'*.freegaming.org',
		'*.op7.net',
		'*.shim.net',
		'*.v9z.com',
	),
	'shorl.com',
	'shortenurl.com',
	'shorterlink.com',
	'shortlinks.co.uk',
	'shorttext.com',
	'shorturl-accessanalyzer.com',
	'shortify.com' => array(
		'74678439.com',
		'shortify.com',
	),
	'shortlink.co.uk',
	'shorturl.com' => array(
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
	'sitelutions.com' => array(
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
	'smallurl.eu',
	'smurl.name',
	'snipurl.com',
	'sp-nov.net',
	'splashblog.com',
	'*.spydar.com',
	'subdomain.gr' => array(
		'*.p2p.gr',
		'*.subdomain.gr',
	),
	's-url.dk' => array('surl.dk'),
	'surl.ws',
	'symy.jp',
	'tdurl.com',
	'tighturl.com',
	'tiniuri.com',
	'*.tiny.cc',
	'tiny.pl',
	'tiny2go.com',
	'tinylink.eu',
	'tinylinkworld.com',
	'tinypic.com',
	'tinyr.us',
	'tinyurl.com',
	'titlien.com',
	'Tokelau ccTLD' => array('*.tk'),
	'tlurl.com',	
	'link.toolbot.com',
	'tnij.org',
	'*.toolbot.com',
	'*.torontonian.com',
	'trimurl.com',
	'ttu.cc',
	'turl.jp',
	'*.tz4.com',
	'uchinoko.in',
	'ulimit.com' => array(
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
	'*.uni.cc',
	'*.uploadr.com',
	'jeremyjohnstone.com' => array('url.vg'),
	'unonic.com' => array(
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
	'url4.net',
	'url-c.com',
	'urlbee.com',
	'urlbounce.com',
	'urlcut.com',
	'urlcutter.com',
	'urlic.com',
	'urlin.it',
	'*.urlproxy.com',
	'urlser.com',
	'urlsnip.com',
	'urlzip.de',
	'urlx.org',
	'utun.jp',
	'*.v27.net',
	'v3.com by fortunecity.com' => array(
		// http://www.v3.com/sub-domain-list.shtml
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
	'vdirect.com' => array(
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
	'vgo2.com',
	'w3t.org',
	'wapurl.co.uk',
	'wb.st' => array(
		'*.team.st',
		'*.wb.st',
	),
	'wbkt.net',
	'webalias.com' => array(
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
		'webalias.com',
		'*.webalias.com',
		'*.webdare.com',
		'*.xxx-posed.com',
	),
	'webmasterwise.com',
	'wittylink.com',
	'wiz.sc',			// tiny.cc related
	'x50.us' => array(
		'*.i50.de',
		'*.x50.us',
	),
	'xhref.com',
	'xn6.net' => array(
		'*.9ax.net',
		'*.xn6.net',
	),
	'y11.net',
	'*.y11.net',
	'yatuc.com',
	'yep.it',
	'yurel.com',
	'z.la' => array(
		'z.la',
		't.z.la',
	),
	'zapurl.com',
	'zarr.co.uk',
	'zeroweb.org' => array(
		'*.80t.com',
		'*.firez.org',
		'*.fizz.nu',
		'*.ingame.org',
		'*.irio.net',
		'*.v33.org',
		'*.zeroweb.org',
	),
	'zippedurl.com',
	'*.zu5.net',
	'zuso.tw',
	'*.zwap.to',
	// A-2: Dynamic DNS, Dynamic IP services, DNS vulnerabilities, or another DNS cases
	//
	//'*.ath.cx',				// by dydns.com
	//'*.bpa.nu',				// by ddns.ru
	//'*.dnip.net',
	//'*.dnsalias.org',			// by dydns.com
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
	//'*.cm',	// 'Cameroon' ccTLD, sometimes used as typo of '*.com'
			// and all non-recorded domains redirect to 'agoga.com' now


	// B: Sample setting of:
	// Jacked (taken advantage of) and cleaning-less sites
	//
	// Please notify us about this list with reason:
	// http://pukiwiki.sourceforge.jp/dev/?BugTrack2%2F208

	// B-1: Web spaces
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

	// by 100 Best Inc (info at 100best.com)
	'100 Best Inc' => array(
		'*.0-st.com',
		'*.150m.com',	// by 100 Best, Inc., NS by 0catch.com
		'*.2-hi.com',
		'*.20fr.com',
		'*.20ii.com',
		'*.20is.com',
		'*.20it.com',
		'*.20m.com',	// by jlee at 100bestinc.com
		'*.20me.com',
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
	),
	'*.0catch.com',		// by bluehost.com
	'*.0moola.com',
	'20six weblog services' => array(
		'20six.nl',			// by 20six weblog services (postmaster at 20six.nl)
		'*.20six.nl',
		'20six.co.uk',
		'*.20six.co.uk',
		'20six.fr',
		'*.20six.fr',
		'myblog.de',
		'myblog.es',
	),
	'*.2page.de',
	'*.30mb.com',		// by 30MB Online (63681 at whois.gkg.net)
	'*.3000mb.com',		// by Boling Jiang (icedesigns at gmail.com)
	'*.501megs.com',
	'*.50megs.com',
	'501megs.com',
	'*.9999mb.com',
	'*.9k.com',
	'*.aimoo.com',
	'*.alkablog.com',
	'home.aol.com',
	'hometown.aol.com',
	'hometown.aol.de',
	'angelfire.com',	// angelfire.lycos.com
	'm.askfaq.org',
	'*.atfreeforum.com',
	'*.asphost4free.com',
	'*.beeplog.com',
	'bestfreeforums.com',
	'*.blog.hr',
	'blogas.lt',
	'*.blogg.de',
	'*.bloggingmylife.com',
	'bloggers.nl',
	'*.blogharbor.com',
	'blogosfer.com',
	'*.blogspot.com',		// by Google
	'blogyaz.com',
	'*.bravenet.com',
	'*.by.ru',
	'*.chueca.com',
	'concepts-mall.com',
	'*.createmybb.com',
	'cwcity.de' => array(
		'cwcity.de',
		'*.cwcity.de',
		'cwsurf.de',
		'*.cwsurf.de',
	),
	'dakrats.net',
	'*.dcswtech.com',
	'*.devil.it',
	'*.diaryland.com',
	'*.dox.hu',
	'dreipage.de',		// by 2page.de
	'*.eblog.com.au',
	'*.ekiwi.de',
	'*.epinoy.com',
	'forum.ezedia.net',
	'*.extra.hu',
	'fingerprintmedia.com',
	'*.filelan.com',
	'*.free-25.de',
	'*.free-bb.com',
	'freebb.nl',
	'*.freeclans.de',
	'*.freelinuxhost.com',	// by 100webspace.com
	'freeforum.at',
	'freewebs.com',
	'foroswebgratis.com',
	'*.fory.pl',
	'*.forum-on.de',
	'forumnow.com.br',
	'*.forumppl.com',
	'forumprofi' => '#^(?:.*\.)?forumprofi[0-9]*\.de$#',
	'forumup' => '#^^(?:.*\.)?forumup\.' .
		'(?:at|be|ca|ch|co\.nz|co\.uk|co\.za|com|com.au|cn|' .
		'cz|de|dk|es|eu|fr|gr|hu|in|info|ir|it|jobs|jp|lt|' .
		'lv|org|pl|name|net|nl|ro|ru|se|sk|tv|us|web\.tr)$#',
	'freepowerboards.com',
	'*.freepowerboards.com',
	'gossiping.net',
	'*.hit.bg',				// by forumup.com ??
	'gb-hoster.de',
	'*.goodboard.de',
	'docs.google.com',			// by Google
	'enunblog.com',
	'club.giovani.it',
	'groups-beta.google.com',	// by Google
	'healthcaregroup.com',
	'*.host-page.com',
	'*.hut2.ru',
	'ieurop.net' => array(
		'*.ibelgique.com',
		'*.iespana.es',	
		'*.ifrance.com',
		'*.iitalia.com',
		'*.iquebec.com',
		'*.isuisse.com',
	),
	'*.ifastnet.com',
	'*.ihateclowns.net',
	'iwannaforum.com',
	'*.iwannaforum.com',
	'*.journalscape.com',
	'*.kokoom.com',
	'*.ksiegagosci.info',
	'*.land.ru',			// pochta.ru related
	'lide.cz' => array(
		'*.lide.cz',
		'*.sblog.cz',
	),
	'limmon.net',
	'*.logme.nl',
	'ltss.luton.ac.uk',
	'*.lycos.it',
	'angelfire.lycos.com',
	'*.messageboard.nl',
	'*.monforum.com',
	'*.monforum.fr',		// by monforum.com
	'myblog.is',
	'myblogma.com',
	'*.myblogvoice.com',
	'*.myforum.ro',
	'*.myfreewebs.net',
	'*.myxhost.com',
	'*.narod.ru',
	'*.netfast.org',
	'neweconomics.info',
	'*.nm.ru',
	'*.parlaris.com',
	'*.pathfinder.gr',
	'*.phorum.pl',
	'*.phpbbx.de',
	'*.pochta.ru',
	'proboards' => '#^(?:.*\.)proboards[0-9]*\.com$#',
	'*.prophp.org',		// pro-php.org
	'*.quickfreehost.com',
	'quizilla.com',
	'razyboard.com',
	'realhp.de',
	'rin.ru' => array(
		'*.sbn.bz',
		'*.wol.bz',
	),
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
	'*.sayt.ws',
	'*.siamforum.com',
	'*.siteburg.com',
	'*.sitesfree.com',
	'*.spazioforum.it',
	'*.spicyblogger.com',
	'*.spotbb.com',
	'*.squarespace.com',
	'stickypond.com',
	'*.stormloader.com',
	'*.sultryserver.com',
	'*.t35.com',
	'*.talkthis.com',
	'tbns.net',
	'telasipforums.com',
	'thestudentunderground.org',
	'think.ubc.ca',
	'*.tripod.com',
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
	'*.vidiac.com',
	'volny.cz',
	'*.welover.org',
	'webblog.ru',
	'weblogmaniacs.com',
	'websitetoolbox.com',
	'wh-gb.de',
	'*.wikidot.com',
	'*.wmjblogs.ru',
	'*.wordpress.com',
	'wsboards.com',
	'*.wsboards.com',
	'xeboards.com',
	'*.xhostar.com',
	'*.xoompages.com',
	'blogs.ya.com',
	'yourfreebb.de',
	'your-websites.com' => array(
		'*.your-websites.net',
		'*.web-space.ws',
	),

	// B-2: Jacked contents, something implanted
	// (e.g. some sort of blog comments, BBSes, forums, wikis)
	'*.aamad.org',
	'alwanforthearts.org',
	'anewme.org',
	'blepharospasm.org',
	'*.buzznet.com',
	'*.colourware.co.uk',
	'icu.edu.ua',
	'*.iphpbb.com',
	'board-z.de',
	'*.board-z.de',
	'California State University Stanislaus' => array('writing.csustan.edu'),
	'delayedreaction.org',
	'deproduction.org',
	'dc503.org',
	'fhmcsa.org.au',
	'*.fhmcsa.org.au',
	'forum.lixium.fr',
	'hullandhull.com',
	'funkdoc.com',
	'*.goodboard.de',
	'homepage-dienste.com',
	'*.inventforum.com',
	'plone4.fnal.gov',
	'funnyclipcentral.com',
	'ghettojava.com',
	'huskerink.com',
	'hyba.info',
	'*.hyba.info',
	'ipwso.org',
	'internetincomeclub.com',
	'jloo.org',
	'*.jloo.org',
	'test.kernel.org',
	'kevindmurray.com',
	'macfaq.net',
	'me4x4.com',
	'morallaw.org',
	'morerevealed.com',
	'mamiya.co.uk',
	'mountainjusticemedia.org',
	'*.mybbland.com',
	'users.nethit.pl',
	'njbodybuilding.com',
	'nlen.org',
	'offtextbooks.com',
	'omikudzi.ru',
	'pix4online.co.uk',
	'preform.dk',
	'privatforum.de',
	'ryanclark.org',
	'*.reallifelog.com',
	'rkphunt.com',
	'saskchamber.com',
	'selikoff.net',
	'setbb.com',
	'silver-tears.net',
	'Tennessee Tech University' => array('manila.tntech.edu'),
	'troms-slekt.com',
	'Kazan State University' => array(
		'dir.kzn.ru',
		'sys.kcn.ru',
	),
	'theedgeblueisland.com',
	'Truman State University' => array('mathbio.truman.edu'),
	'tzaneen.co.za',
	'The University of North Dakota' => array(
		'learn.aero.und.edu',
		'ez.asn.und.edu',
	),
	'urgentclick.com',
	'vacant.org.uk',
	'Villa Julie College' => array('www4.vjc.edu'),
	'West Virginia University Parkersburg' => array('wvup.edu'),
	'wolvas.org.uk',
	'wookiewiki.org',
	'youthpeer.org',
	'*.zenburger.com',


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
	//'*.biz'
	//
	// by Boris (admin at seekforweb.com, bbmfree at yahoo.com)
	'admin at seekforweb.com' => array(
		'*.lovestoryx.com',	
		'*.loveaffairx.com',
		'*.onmore.info',
		'*.scfind.info',
		'*.scinfo.info',
		'*.webwork88.info',
	),
	//
	// by Boris (boss at bse-sofia.bg)
	'boss at bse-sofia.bg' => array(
		'htewbop.org',
		'*.htewbop.org',
		'*.kimm--d.org',
		'*.gtre--h.org',
		'*.npou--k.org',
		'*.bres--z.org',
		'berk--p.org',
		'*.bplo--s.org',
		'*.basdpo.org',
		'jisu--m.org',
		'kire--z.org',
		'*.mertnop.org',
		'mooa--c.org',
		'nake--h.org',
		'noov--b.org',
		'suke--y.org',
		'vasdipv.org',
		'*.vasdipv.org',
		'vase--l.org',
		'vertinds.org',
	),
	//
	// by Thai Dong Changli (pokurim at gamebox.net)
	'Thai Dong Changli' => array(
		'*.aqq3.info',
		'*.axa00.info',
		'*.okweb11.org',
		'*.okweb12.org',
		'*.okweb13.org',
		'*.okweb14.org',
	),
	//
	// by opezdol at gmail.com
	'opezdol' => array(
		'informazionicentro.info',
		'*.informazionicentro.info',
		'notiziacentro.info',
		'*.notiziacentro.info',
	),
	//
	'something_gen' => array(
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

		'*.adult-cam-chat-sex.info',	// by Lee Chang (nebucha at model-x.com)
		'*.adult-chat-sex-cam.info',	// 'CamsGen' by Lee Chang (nebucha at model-x.com)
		'*.live-chat-cam-sex.info',		// 'CamsGen' by Lee Chang (nebucha at model-x.com)
		'*.live-nude-cam-chat.info',	// 'CamsGen' by Lee Chang (nebucha at model-x.com)
		'*.live-sex-cam-nude-chat.info',// 'CamsGen' by Lee Chang (nebucha at model-x.com)
		'*.sex-cam-live-chat-web.info',	// 'CamsGen' by Lee Chang (nebucha at model-x.com)
		'*.sex-chat-live-cam-nude.info',// 'CamsGen' by Lee Chang (nebucha at model-x.com)
		'*.sex-chat-porn-cam.info',		// by Lee Chang (nebucha at model-x.com)
	),
	//
	// by Marcello Italianore (mital at topo20.org)
	'Marcello Italianore' => array(
		'*.trevisos.org',
		'*.topo20.org',
	),
	//
	'wellcams.com' => array(
		'*.j8v9.info',
		'*.wellcams.com',
		'wellcams.biz',
	),
	//
	// by Chinu Hua Dzin (graz at rubli.biz)
	'Chinu Hua Dzin' => array(
		'*.besturgent.org',
		'*.googletalknow.org',
		'*.montypythonltd.org',
		'*.supersettlemet.org',
		'*.thepythonfoxy.org',
		'*.ukgamesyahoo.org',
		'*.youryahoochat.org',
	),
	//
	// by Kikimas at mail.net, Redirect to nb717.com etc
	'Kikimas at mail.net' => array(
		'dbsajax.org',
		'*.dbsajax.org',
		'acgt2005.org',
		'*.acgt2005.org',
		'gopikottoor.com',
		'*.gopikottoor.com',
		'koosx.org',
		'*.koosx.org',
		'mmgz.org',
		'*.mmgz.org',
		'zhiyehua.net',
		'*.zhiyehua.net',
	),
	//
	// by Andrey (vdf at lovespb.com)
	'vdf at lovespb.com' => array(
		'1818u.org',
		'*.1818u.org',
		'18ew.info',
		'*.18ew.info',
		'43sexx.org',
		'*.43sexx.org',
		'56porn.org',
		'*.56porn.org',
		'6discount.info',
		'*.6discount.info',
		'78porn.org',		// "UcoZ WEB-SERVICES"
		'*.78porn.org',
		'78rus.info',
		'*.78rus.info',
		'92ssex.org',		// "ForumGenerator"
		'*.92ssex.org',
		'93adult.org',		// "ForumGenerator"
		'*.93adult.org',
		'buypo.info',
		'*.buypo.info',
		'canadausa.info',	// "UcoZ WEB-SERVICES"
		'*.canadausa.info',
		'cvwifw.info',
		'*.cvwifw.info',
		'eplot.info',		// by Beatrice C. Anderson (Beatrice.C.Anderson at spambob.com)
		'*.eplot.info',
		'fuck2z.info',		// "UcoZ WEB-SERVICES"-like design
		'*.fuck2z.info',
		'frees1.info',
		'*.frees1.info',
		'freexz.info',
		'*.freexz.info',
		'ifree-search.org',
		'*.ifree-search.org',
		'kra1906.info',		// by Nike Borzoff (nike.borzoff at gmail.com), "UcoZ WEB-SERVICES"
		'*.kra1906.info',
		'lovespb.info',
		'*.lovespb.info',
		'oursales.info',
		'*.oursales.info',
		'olala18.info',
		'*.olala18.info',
		'pldk.info',
		'*.pldk.info',
		'pornr.info',		// "UcoZ WEB-SERVICES"
		'*.pornr.info',
		'poz2.info',
		'*.poz2.info',
		'saleqw.info',
		'*.saleqw.info',
		'sexof.info',		// "UcoZ WEB-SERVICES"
		'*.sexof.info',
		'sexz18.info',
		'*.sexz18.info',
		'sexy69a.info',
		'*.sexy69a.info',
		'spb78.info',
		'*.spb78.info',
		'um20ax09.info',	// by Nike Borzoff (nike.borzoff at gmail.com)
		'*.um20ax09.info',
		'usacanadauk.info',
		'*.usacanadauk.info',
		'v782mks.info',	
		'*.v782mks.info',
		'vny0.info',
		'*.vny0.info',
		'wifes1.info',
		'*.wifes1.info',
		'xranvam.info',
		'*.xranvam.info',
		'zxolala.info',
		'*.zxolala.info',
	),
	//
	'Varsylenko Vladimir and family' => array(
		'allsexonline.info',	// by Varsylenko Vladimir (vvm_kz at rambler.ru), redirect to activefreehost.com
		'*.allsexonline.info',
		'bequeous.info',	// by David C. Lack (David.C.Lack at dodgeit.com), redirect to activefreehost.com
		'*.bequeous.info',
		'goodworksite.info',	// by Varsylenko Vladimir (vvm_kz at rambler.ru), redirect to activefreehost.com
		'*.goodworksite.info',
		'rentmysite.info',	// by Varsylenko Vladimir (vvm_kz at rambler.ru), redirect to activefreehost.com
		'*.rentmysite.info',
		'siteszone.info',	// by Varsylenko Vladimir (vvm_kz at rambler.ru), redirect to activefreehost.com
		'*.siteszone.info',
		'sopius.info',		// by kuzmas (admin at irtes.ru), redirect to activefreehost.com
		'*.sopius.info',
		'sovidopad.info',	// by kuzmas (admin at irtes.ru), redirect to activefreehost.com
		'*.sovidopad.info',
		'superfreedownload.info',	// by Varsylenko Vladimir (vvm_kz at rambler.ru), redirect to activefreehost.com
		'*.superfreedownload.info',
		'yerap.info',		// by Kuzma V Safonov (admin at irtes.ru), redirect to activefreehost.com
		'*.yerap.info',
	),
	//
	// by Andrey Zhurikov (zhu1313 at mail.ru)
	'Andrey Zhurikov' => array(
		'*.flywebs.com',
		'*.hostrim.com',
		'playbit.com',
	),
	//
	// by Son Dittman (webmaster at dgo3d.info)
	'Son Dittman' => array(
		'*.bsb3b.info',
		'*.dgo3d.info',
		'*.dgo5d.info',
	),
	//
	// by cooler.infomedia at gmail.com
	'cooler.infomedia' => array(
		'diabetescarelink.com',
		'firstdebthelp.com',
	),
	//
	// by Nikolajs Karpovs (hostmaster at astrons.com)
	'Nikolajs Karpovs' => array(
		'*.pokah.lv',
		'*.astrons.com',
	),
	//
	// by Skar (seocool at bk.ru)
	'Skar' => array(
		'implex3.com',
		'softprof.org',
	),
	//
	'tops.gen.in',		// Hiding google:sites. by Kosare (billing at caslim.info)
	'caslim.info',
	//
	// by Alexandr (foxwar at foxwar.ispvds.com), Hiding google?q=
	'foxwar at foxwar.ispvds.com' => array(
		'777-poker.biz',
		'*.777-poker.biz',
		'*.porn-11.com',
	),
	//
	'conto.pl' => array(
		'*.conto.pl',	// by biuro at nazwa.pl
		'*.guu.pl',		// by conto.pl (domena at az.pl)
	),
	//
	// Domains by Lin Zhi Qiang (mail at pcinc.cn)
	// NOTE: pcinc.cn -- by Lin Zhi Qiang (lin80 at 21cn.com)
	'Lin Zhi Qiang' => array(
		'bbs-qrcode.com',
		'*.bbs-qrcode.com',
		'conecojp.net',
		'*.conecojp.net',
		'gamaniaech.com',
		'*.gamaniaech.com',
		'game-fc2blog.com',
		'*.game-fc2blog.com',
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
	),
	//
	// by Baer (aakin at yandex.ru)
	'aakin at yandex.ru' => array(
		'*.entirestar.com',
		'*.superbuycheap.com',
		'*.topdircet.com',
	),
	//
	// by jiuhatu kou (newblog9 at gmail.com)
	'newblog9 at gmail.com' => array(
		'tianmieccp.com',
		'*.tianmieccp.com',
		'xianqiao.net',	
		'*.xianqiao.net',
	),
	//
	// by Michael (m.frenzy at yahoo.com)
	'm.frenzy at yahoo.com' => array(
		'soft2you.info',
		'*.soft2you.info',
		'top20health.info',
		'*.top20health.info',
		'top20ringtones.info',
		'*.top20ringtones.info',
		'v09v.info',
		'*.v09v.info',
		'x09x.info',
		'*.x09x.info',
		'zb-1.com',
		'*.zb-1.com',
	),
	//
	// by Lebedev Sergey (serega555serega555 at yandex.ru)
	'Lebedev Sergey' => array(
		'bingogoldenpalace.info',
		'*.bingogoldenpalace.info',
		'ccarisoprodol.info',
		'*.ccarisoprodol.info',
		'ezxcv.info',
		'*.ezxcv.info',
		'isuperdrug.com',
		'*.isuperdrug.com',
		'pharmacif.info',
		'*.pharmacif.info',
		'pornsexteen.biz',
		'*.pornsexteen.biz',
		'ugfds.info',
		'*.ugfds.info',
		'vviagra.info',
		'*.vviagra.info',
	),
	//
	// by Anatol (anatolsenator at gmail.com)
	'anatolsenator at gmail.com' => array(
		'*.cheapestviagraonline.info',
		'*.buyphentermineworld.info'
	),
	//
	'webmaster at mederotica.com' => array(
		'listsitepro.com',	// by VO Entertainment Inc (webmaster at mederotica.com)
		'*.listsitepro.com',
		'testviagra.org',	// by Chong Li (chongli at mederotica.com)
		'*.testviagra.org',
		'viagra-best.org',	// by Chong Li (chongli at mederotica.com)
		'*/viagra-best.org',
	),
	//
	// by Billing Name:Gray (gray at trafic.name)
	'gray at trafic.name' => array(
		'axeboxew.info',
		'*.axeboxew.info',
		'boluzuhy.info',
		'*.boluzuhy.info',
		'ekafoloz.info',
		'*.ekafoloz.info',
		'exidiqe.info',
		'*.exidiqe.info',
		'gubiwu.info',
		'*.gubiwu.info',
		'jiuuz.info',
		'*.jiuuz.info',
		'olasep.info',
		'*.olasep.info',
		'oueuidop.info',
		'*.oueuidop.info',
		'oviravy.info',
		'*.oviravy.info',
		'ragibe.info',
		'*.ragibe.info',
		'udaxu.info',
		'*.udaxu.info',
		'vubiheq.info',
		'*.vubiheq.info',
		'yvaxat.info',
		'*.yvaxat.info',
	),
	//
	'carmodelrank.com etc' => array(
		'carmodelrank.com',	// by Brianna Dunlord (briasmi at yahoo.com)
		'*.carmodelrank.com',
		'cutestories.net',	// by Brianna Dunlord (briasmi at yahoo.com)
		'*.cutestories.net',
		'sturducs.com',
		'*.sturducs.com',
		'bestother.info',	// by Tim Rennei (TimRennei at yahoo.com), redirect to amaena.com (fake-antivirus)
		'*.bestother.info',
		'yaahooo.info',		// by Alice T. Horst (Alice.T.Horst at pookmail.com), redirect to activefreehost.com
		'*.yaahooo.info',
	),
	//
	// by Dr. Portillo or Eva Sabina Lopez Castell (aliacsandr85 at yahoo.com)
	'aliacsandr85 at yahoo.com' => array(
		'xoomer.alice.it',		// "Free Web Hosting"
		'freebloghost.org',		// "Free Web Hosting" by Dr.
		'*.freebloghost.org',
		'freeprohosting.org',	// "Free Web Hosting" by Dr.
		'*.freeprohosting.org',
		'googlebot-welcome.org',// "Free Web Hosting" by Dr.
		'*.googlebot-welcome.org',
		'icesearch.org',		// "Free Web Hosting" by Eva
		'*.icesearch.org',
		'phpfreehosting.org',	// "Free Web Hosting" by Dr.
		'*.phpfreehosting.org',
		'sashawww.info',		// "Free Web Hosting" by Dr.
		'*.sashawww.info',
		'sashawww-vip-vip.org',		// "Free Web Hosting" by Dr.
		'*.sashawww-vip-vip.org',
		'topadult10.org',		// "Free Web Hosting" by Eva
		'*.topadult10.org',
		'xer-vam.org',			// "Ongline Catalog" by Dr.
		'*.xer-vam.org',
		'xxxse.info',			// "Free Web Hosting" by Eva
		'*.xxxse.info',
		'vvsa.org',				 // "Free Web Hosting" by Eva
		'*.vvsa.org',
	),
	//
	// Gamble: Roulette, Casino, Poker, Keno, Craps, Baccarat
	'something_gamble' => array(
		'atonlineroulette.com',		// by Blaise Johns
		'*.atonlineroulette.com',
		'atroulette.com',			// by Gino Sand
		'*.atroulette.com',
		'betting-123.com',			// by Joana Caceres
		'*.betting-123.com',
		'betting-i.biz',			// by Joaquina Angus
		'*.betting-i.biz',
		'casino-challenge.com',		// by Maren Camara
		'*.casino-challenge.com',
		'casino123.net',			// by Ta Baines
		'*.casino123.net',
		'casinoqz.com',				// by Berenice Snow
		'*.casinoqz.com',
		'crapsok.com',				// by Devon Adair,
		'*.crapsok.com',
		'dcasinoa.com',				// by August Hawkinson
		'*.dcasinoa.com',
		'e-poker-4u.com',			// by Issac Leibowitz
		'*.e-poker-4u.com',
		'free-dvd-player.biz',		// by Rosario Kidd
		'*.free-dvd-player.biz',
		'gaming-123.com',			// by Jennifer Byrne
		'*.gaming-123.com',
		'kenogo.com',				// by Adriane Bell
		'*.kenogo.com',
		'mycaribbeanpoker.com',		// by Andy Mullis
		'*.mycaribbeanpoker.com',
		'onbaccarat.com',			// by Kassandra Dunn
		'*.onbaccarat.com',
		'poker-123.com',			// by Mallory Patrick (Mallory_Patrick at marketing-support.info)
		'*.poker-123.com',
		'texasholdem-777.com',		// by Savanna Lederman
		'*.texasholdem-777.com',
		'the-craps-100.us',			// by Lorrine Ripley
		'*.the-craps-100.us',
		'the-free-online-game-913.us',	// by Kanesha Clem
		'*.the-free-online-game-913.us',
		'the-online-game-poker-1185.us',	// by Merna Bey
		'*.the-online-game-poker-1185.us',
		'the-rule-texas-hold-em-2496.us',	// by Melvina Stamper
		'*.the-rule-texas-hold-em-2496.us',
		'the-texas-strategy-holdem-1124.us',	// by Neda Frantz
		'*.the-texas-strategy-holdem-1124.us',
		'the-las-vegas-gambling-939.us',	// by Jesusita Hageman
		'*.the-las-vegas-gambling-939.us',
	),
	//
	// Car / Home / Life / Health / Travel insurance, Loan finance, Mortgage refinance
	'something_insurance' => array(
		'0q.org',					// by Shamika Curtin
		'*.0q.org',
		'1-bookmark.com',			// by Sonia Snyder
		'*.1-bookmark.com',
		'1day-insurance.com',		// by Kelsie Strouse
		'*.1day-insurance.com',
		'1upinof.com',				// by Diego Johnson
		'*.1upinof.com',
		'18wkcf.com',				// by Lexy Bohannon
		'*.18wkcf.com',
		'2001werm.org',				// by Raphael Rayburn
		'*.2001werm.org',
		'2004heeparea1.org',		// by Dinorah Andrews
		'*.2004heeparea1.org',
		'21nt.net',					// by Jaida Estabrook
		'*.21nt.net',
		'3finfo.com',				// by Damian Pearsall
		'*.3finfo.com',
		'3somes.org',				// by Mauro Tillett
		'*.3somes.org',
		'453531.com',				// by Kurt Flannery
		'*.453531.com',
		'4freesay.com',				// by Eloy Jones
		'*.4freesay.com',
		'8-f22.com',				// by Larraine Evers
		'*.8-f22.com',
		'a40infobahn.com',			// by Amit Nguyen
		'*.a40infobahn.com',
		'a4h-squad.com',			// by Ross Locklear
		'*.a4h-squad.com',
		'aac2000.org',				// by Randi Turner
		'*.aac2000.org',
		'aaadvertisingjobs.com',	// by Luciano Frisbie
		'*.aaadvertisingjobs.com',
		'aconspiracyofmountains.com',	// by Lovell Gaines
		'*.aconspiracyofmountains.com',
		'acornwebdesign.co.uk',		// by Uriel Dorian
		'*.acornwebdesign.co.uk',
		'activel-learning-site.com',	// by Mateo Conn
		'*.activel-learning-site.com',
		'ad-makers.com',			// by Shemeka Arsenault
		'*.ad-makers.com',
		'ada-information.org',		// by Josef Osullivan
		'*.ada-information.org',
		'aequityrefinance.com',		// by Jadwiga Duckworth
		'*.aequityrefinance.com',
		'ahomeloanrefinance.com',	// by Leslie Kinser
		'*.ahomeloanrefinance.com',
		'affordablerealestate.net',	// by Season Otoole
		'*.affordablerealestate.net',
		'ahouserefinance.com',	// by Young Alley
		'*.ahouserefinance.com',
		'alderik-production.com',	// by Joan Stiles
		'*.alderik-production.com',
		'alltechdata.com',			// by Dom Laporte
		'*.alltechdata.com',
		'angelandcrown.net',		// by Claretta Najera
		'*.angelandcrown.net',
		'ankoralina.com',			// by Eladia Demers
		'*.ankoralina.com',
		'architectionale.com',		// by Wilbur Cornett
		'*.architectionale.com',
		'arefinancehome.com',		// by Duane Doran
		'*.arefinancehome.com',
		'arefinancinghome.com',		// by Ike Laney
		'*.arefinancinghome.com',
		'athletic-shoes-e-shop.info',	// by Romelia Money
		'*.athletic-shoes-e-shop.info',
		'auto-buy-rite.com',		// by Asuncion Buie
		'*.auto-buy-rite.com',
		'azstudies.org',			// by Bernardina Walden
		'*.azstudies.org',
		'babtrek.com',				// by Simonette Mcbrayer
		'*.babtrek.com',
		'babycujo.com',				// by Francisco Akers
		'*.babycujo.com',
		'bakeddelights.com',		// by Dave Evenson
		'*.bakeddelights.com',
		'best-digital-phone.us',	// by Meghann Crockett
		'*.best-digital-phone.us',
		'blursgsu.com',				// by Weston Killian
		'*.blursgsu.com',
		'boreholes.org',			// by Flora Reed
		'*.boreholes.org',
		'breathingassociaiton.org',	// by Alfred Crayton
		'*.breathingassociaiton.org',
		'birdingnh.com',			// by Donald Healy
		'*.birdingnh.com',
		'bisdragons.org',			// by Lupe Cassity
		'*.bisdragons.org',
		'bronte-foods.com',			// by Kary Pfeiffer
		'*.bronte-foods.com',
		'buckscountyneighbors.org',	// by Maile Gaffney
		'*.buckscountyneighbors.org',
		'buffalofudge.com',			// by Mable Whisenhunt
		'*.buffalofudge.com',
		'burlisonforcongress.com',	// by Luann King
		'*.burlisonforcongress.com',
		'cabanes-web.com',			// by Vaughn Latham
		'*.cabanes-web.com',
		'calvarychapelrgvt.org',	// by Karan Kittle
		'*.calvarychapelrgvt.org',
		'cameras-esite.info',		// by Karlee Frisch
		'*.cameras-esite.info',
		'cancerkidsforum.org',		// by Samson Constantino
		'*.cancerkidsforum.org',
		'ccchoices.org',			// by Kenia Cranford
		'*.ccchoices.org',
		'centerfornourishingthefuture.org',	// by Elisa Wilt
		'*.centerfornourishingthefuture.org',
		'churla.com',				// by Ollie Wolford
		'*.churla.com',
		'cnm-ok.org',				// by Thalia Moye
		'*.cnm-ok.org',
		'coalitioncoalition.org',	// by Ned Macklin
		'*.coalitioncoalition.org',
		'counterclockwise.net',		// by Melynda Hartzell
		'*.counterclockwise.net',	
		'codypub.com',				// by Mercedes Coffman
		'*.codypub.com',
		'comedystore.net',			// by Floy Donald
		'*.comedystore.net',
		'covsys.co.uk',				// by Abby Jacey
		'*.covsys.co.uk',
		'cpusa364-northsacramento.com',	// by Dannette Lejeune
		'*.cpusa364-northsacramento.com',
		'ctwine.org',				// by Hailey Knox
		'*.ctwine.org',
		'deepfoam.org',				// by Ethelyn Southard
		'*.deepfoam.org',
		'diannbomkamp.com',			// by Russel Croteau
		'*.diannbomkamp.com',
		'dictionary-spanish.us',	// by Jacki Gilbreath
		'*.dictionary-spanish.us',
		'dictionary-yahoo.us',		// by Lili Mitchem
		'*.dictionary-yahoo.us',
		'dtmf.net',					// by Micki Slayton
		'*.dtmf.net',
		'domainsfound.com',			// by Blossom Lively
		'*.domainsfound.com',
		'ecstacyabuse.net',			// by Alana Knight
		'*.ecstacyabuse.net',
		'e-digital-camera-esite.info',	// by Romaine Cress
		'*.e-digital-camera-esite.info',
		'eda-aahperd.org',			// by Kaliyah Hammonds
		'*.eda-aahperd.org',
		'eldorabusecenter.org',		// by Annabella Oneal
		'*.eldorabusecenter.org',
		'encaponline.com',			// by Patrick Keel
		'*.encaponline.com',
		'ez-shopping-online.com',	// by Gail Bartlett
		'*.ez-shopping-online.com',
		'foreignrealtions.org',		// by Krystal Hawley
		'*.foreignrealtions.org',
		'fortwebsite.org',			// by Kristina Motley
		'*.fortwebsite.org',
		'foundationcommons.org',	// by Caryn Eskew
		'*.foundationcommons.org',
		'fraisierest-alexandre.com',	// by Dwayne Douglas
		'*.aisierest-alexandre.com',
		'freaky-cheats.com',		// by Al Klein
		'*.freaky-cheats.com',
		'free--spyware.com',		// by Nikki Contreras
		'*.free--spyware.com',
		'gcaaa.com',				// by Vallie Jaworski
		'*.gcaaa.com',
		'generation4games.co.uk',	// by Sonya Graham
		'*.generation4games.co.uk',
		'gilmerrec.com',			// by Leighann Guillory
		'*.gilmerrec.com',
		'gohireit.com',				// by Bertha Metzger
		'*.gohireit.com',
		'godcenteredpeople.com',	// by Jaycee Coble
		'*.godcenteredpeople.com',
		'healthinsuranceem.com',	// by Justin Munson
		'*.healthinsuranceem.com',
		'hegerindustrial.com',		// by Toni Wesley
		'*.hegerindustrial.com',
		'hipanoempresa.com',		// by Shannon Staub
		'*.hipanoempresa.com',
		'hitempfurnaces.com',		// by Rebbeca Jaeger
		'*.hitempfurnaces.com',
		'ilruralassistgrp.org',		// by Moises Hauser
		'*.ilruralassistgrp.org',
		'islamfakta.org',			// by Goldie Boykin
		'*.islamfakta.org',
		'ithomemortgage.com',		// by Adelaide Towers
		'*.ithomemortgage.com',
		'iyoerg.com',				// by Madyson Gagliano
		'*.iyoerg.com',
		'kcgerbil.org',				// by Marisa Thayer
		'*.kcgerbil.org',
		'kdc-phoenix.com',			// by Salma Shoulders
		'*.kdc-phoenix.com',
		'kosove.org',				// by Darwin Schneider
		'*.kosove.org',
		'locomojo.net',				// by Marco Harmon
		'*.locomojo.net',
		'lycos-test.net',			// by Rigoberto Oakley
		'*.lycos-test.net',
		'macro-society.com',		// by Venessa Hodgson
		'*.macro-society.com'.
		'martin-rank.com',			// by Cathleen Crist
		'*.martin-rank.com',
		'maryandfrank.org',			// by Theodore Apodaca
		'*.maryandfrank.org',
		'meyerlanguageservices.co.uk',	// by Breana Kennedy
		'*.meyerlanguageservices.co.uk',
		'modayun.com',				// by Camilla Velasco
		'*.modayun.com',
		'morosozinho.com',			// by Lenore Tovar
		'*.morosozinho.com',
		'morphadox.com',			// by Hung Zielinski
		'*.morphadox.com',
		'mpeg-radio.com',			// by Sincere Beebe
		'*.mpeg-radio.com',
		'mrg-now-yes.com',			// by Sparkle Gallegos
		'*.mrg-now-yes.com',
		'mtseniorcenter.org',		// by Frederic Ortega
		'*.mtseniorcenter.org',
		'mysteryclips.com',			// by Edward Ashford
		'*.mysteryclips.com',
		'navigare-ischia.com',		// by Arielle Coons
		'*.navigare-ischia.com',
		'nmbusinessroundtable.org',	// by Chantel Mccourt
		'*.nmbusinessroundtable.org',
		'npawny.org',				// by Willard Murphy
		'*.npawny.org',
		'nysdoed.org',				// by Elric Delgadillo
		'*nysdoed.org',
		'nytech-ir.com',			// by Adrien Beals
		'*.nytech-ir.com',
		'oarauto.com',				// by Susann Merriman
		'*.oarauto.com',
		'online-pills-24x7.biz',	// by Aide Hallock
		'*.online-pills-24x7.biz',
		'onlinehomeloanrefinance.com',	// by Chaz Lynch
		'*.onlinehomeloanrefinance.com',
		'onlinehomeloanfinancing.com',	// by Humbert Eldridge
		'*.onlinehomeloanfinancing.com',
		'onunicarehealthinsurance.com',	// by  Lawerence Paredes
		'*.onunicarehealthinsurance.com',
		'parde.org',				// by Ellie Yates
		'*.parde.org',
		'participatingprofiles.com',	// by Jaelynn Meacham
		'*.participatingprofiles.com',
		'partnershipconference.org',	// by Alla Floyd
		'*.partnershipconference.org',
		'pet-stars.com',			// by Carmon Luevano
		'*.pet-stars.com',
		'planning-law.org',			// by Trista Holcombe
		'*.planning-law.org',
		'ppawa.com',				// by Evonne Scarlett
		'*.ppawa.com',
		'precisionfilters.net',		// by Faustina Fell
		'*.precisionfilters.net',
		'quick-debt-consolidation.net',	// by Lala Marte
		'*.quick-debt-consolidation.net',
		'quicktvr.com',				// by Vernell Crenshaw
		'*.quicktvr.com',
		'radicalsolutions.org',		// by Reece Medlin
		'*.radicalsolutions.org',
		'rcassel.com',				// by Janiah Gallant
		'*.rcassel.com',
		'rearchitect.org',			// by Marcus Gaudet
		'*.rearchitect.org',
		'rent-an-mba.com',			// by Valentina Mcdermott
		'*.rent-an-mba.com',
		'reprisenashville.com',		// by Hester Khan
		'*.reprisenashville.com',
		'richcapaldi.com',			// by Kya Haggard
		'*.richcapaldi.com',
		'rollingprairie-candlecompany.com',	// by Leigha Aker
		'*.rollingprairie-candlecompany.com',
		'ruralbusinessonline.org',	// by Lynsey Watters
		'*.ruralbusinessonline.org',
		'ruwomenscenter.org',		// by Vince Mclemore
		'*.ruwomenscenter.org',
		'ryanjowens.com',			// by Janine Smythe
		'*.ryanjowens.com',
		'sandiegolawyer.net',		// by Linnie Sommerville
		'*.sandiegolawyer.net',
		'shoes-shop.us',			// by Austen Higginbotham
		'*.shoes-shop.us',
		'skinsciencesalon.com',		// by Nena Rook
		'*.skinsciencesalon.com',
		'sneakers-e-shop.info',		// by Nikki Fye
		'*.sneakers-e-shop.info',
		'spacewavemedia.com',		// by Thanh Gast
		'*.spacewavemedia.com',
		'softkernel.com',			// by Nicol Hummer
		'*.softkernel.com',
		'strelinger.com',			// by Arron Highsmith
		'*.strelinger.com',
		'sunnydeception.org',		// by Amaya Llora
		'*.sunnydeception.org',
		'sunzmicro.com',			// by Goddard Arreola
		'*.sunzmicro.com',
		'sv-iabc.org',				// by Braden Buck
		'*.sv-iabc.org',
		'sykotick.com',				// by Pierce Knecht
		'*.sykotick.com',
		'tbody.net',				// by Ormond Roman
		'*.tbody.net',
		'the-shoes.us',				// by Alejandro Gaffney
		'*.the-shoes.us',
		'top-finance-sites.com',	// by Maryann Doud
		'*.top-finance-sites.com',
		'tradereport.org',			// by Bettie Sisk
		'*.tradereport.org',
		'tsunamidinner.com',		// by Nannie Richey
		'*.tsunamidinner.com',
		'usjobfair.com',			// by Lorina Burchette
		'*.usjobfair.com',
		'vacancesalouer.com',		// by Loris Bergquist
		'*.vacancesalouer.com',
		'vonormytexas.us',			// by Suzette Waymire
		'*.vonormytexas.us',
		'worldpropertycatalog.com',	// by Aray Baxter
		'*.worldpropertycatalog.com',


		//
		'faithfulwordcf.com',		// by Bart Weeks
		'*.faithfulwordcf.com',
		'gaintrafficfast.com',		// by Lila Meekins
		'*.gaintrafficfast.com',
		'gaygain.org',				// by Shell Davila
		'*.gaygain.org',
		'hearthorizon.info',		// by Kory Session
		'*.hearthorizon.info',
		'hglcms.org',				// by 
		'*.hglcms.org',
		'horse-racing-result.com',	// by 
		'*.horse-racing-result.com',
		'hueckerfamily.com',		// by 
		'*.hueckerfamily.com',
		'ilove2win.com',			// by 
		'*.ilove2win.com',
		'imageonsolutions.com',		// by 
		'*.imageonsolutions.com',
		'infoanddatacenter.com',	// by 
		'*.infoanddatacenter.com',
		'johnmartinsreality.com',	// by 
		'*.johnmartinsreality.com',
		'johnsilvers.net',			// by 
		'*.johnsilvers.net',
		'libertycabs.com',			// by 
		'*.libertycabs.com',
		'masterkwonhapkido.com',	// by 
		'*.masterkwonhapkido.com',
		'maxrpm-demo.com',			// by 
		'*.maxrpm-demo.com',
		'mechanomorphic.com',		// by 
		'*.mechanomorphic.com',
		'metwahairports.com',		// by 
		'*.metwahairports.com',
		'milpa.org',				// by 
		'*.milpa.org',
		'moonstoneerp.com',			// by 
		'*.moonstoneerp.com',
		'naavs.org',				// by 
		'*.naavs.org',
		'naval-aviation.org',		// by 
		'*.naval-aviation.org',
		'neonmotorsports.com',		// by 
		'*.neonmotorsports.com',
		'nicozone.com',				// by 
		'*.nicozone.com',
		'online-shopping-site-24x7.info',	// by 
		'*.online-shopping-site-24x7.info',
		'otterbayweb.com',			// by 
		'*.otterbayweb.com',
		'reptilemedia.com',			// by 
		'*.reptilemedia.com',
		'resellers2000.com',		// by 
		'*.resellers2000.com',
		'reverse-billing.com',		// by 
		'*.reverse-billing.com',
		'richformissouri.com',		// by 
		'*.richformissouri.com',
		'rpgbbs.com',				// by 
		'*.rpgbbs.com',
		'scienkeen.com',			// by 
		'*.scienkeen.com',
		'sexual-hot-girls.com',		// by 
		'*.sexual-hot-girls.com',
		'shakespearelrc.com',		// by 
		'*.shakespearelrc.com',
		'smartalternative.net',		// by 
		'*.smartalternative.net',
		'smogfee.com',				// by 
		'*.smogfee.com',
		'tigerspice.com',			// by 
		'*.tigerspice.com',
		'tnaa.net',					// by 
		'*.tnaa.net',
		'transmodeling.com',		// by 
		'*.transmodeling.com',
		'tsaoc.com',				// by 
		'*.tsaoc.com',
		'uhsaaa.com',				// by 
		'*.uhsaaa.com',
		'vcertificates.com',		// by 
		'*.vcertificates.com',
		'yankee-merchants.com',		// by 
		'*.yankee-merchants.com',
		'yourbeachhouse.com',		// by 
		'*.yourbeachhouse.com',
		'zkashan.com',				// by 
		'*.zkashan.com',
		'zockclock.com',			// by 
		'*.zockclock.com',
	),
	//
	// Drugs / Pills
	'something_drugs' => array(
		'fn-nato.com',				// by Donny Dunlap
		'*.fn-nato.com',
		'fantasticbooks-shop.com',	// by Kermit Ashley
		'*.fantasticbooks-shop.com',
	),
	//
	// by Cortez Shinn (info at goorkkjsaka.info), or Rico Laplant (info at nnjdksfornms.info)
	'Cortez and family' => array(
		'dronadaarsujf.info',	// by Cortez
		'*.dronadaarsujf.info',
		'fromnananaref.info',	// by Cortez
		'*.fromnananaref.info',
		'goorkkjsaka.info',		// by Cortez
		'*.goorkkjsaka.info',
		'jkdfjjkkdfe.info',		// by Rico
		'*.jkdfjjkkdfe.info',
		'jkllloldkjsa.info',	// by Cortez
		'*.jkllloldkjsa.info',
		'nnjdksfornms.info',	// by Rico
		'*.nnjdksfornms.info',
		'mcmdkkksaoka.info',	// by Cortez
		'*.mcmdkkksaoka.info',
		'srattaragfon.info',	// by Cortez
		'*.srattaragfon.info',
		'yreifnnonoom.info',	// by Rico
		'*.yreifnnonoom.info',
		'zjajjsvgeuds.info',	// by Cortez
		'*.zjajjsvgeuds.info',
	),
	//
	// by Harvey Pry (admin at ematuranza.com)
	'Harvey Pry' => array(
		'ancorlontano.com',		
		'*.ancorlontano.com',
		'dentroallago.com',
		'*.dentroallago.com',
		'digiovinezza.com',
		'*.digiovinezza.com',
		'ematuranza.com',
		'*.ematuranza.com',
		'ilfango.com',
		'*.ilfango.com',
		'nullarimane.com',
		'*.nullarimane.com',
		'questaimmensa.com',
		'*.questaimmensa.com',
		'tentailvolo.com',
		'*.tentailvolo.com',
		'unatenerezza.com',
		'*.unatenerezza.com',
		'volgondilettose.com',
		'*.volgondilettose.com',
	),
	//
	// by Cornelius Boyers (admin at edeuj84.info)
	'Cornelius Boyers' => array(
		'bid99df.info',
		'*.bid99df.info',
		'bj498uf.info',
		'*.bj498uf.info',
		'edeuj84.info',
		'*.edeuj84.info',
		'f4mfid.info',
		'*.f4mfid.info',
		'g4vf03a.info',
		'*.g4vf03a.info',
		'j09j4r.info',
		'*.j09j4r.info',
		'jv4r8hv.info',
		'*.jv4r8hv.info',
		'k43sd3.info',
		'*.k43sd3.info',
		'k4r84d.info',
		'*.k4r84d.info',
		'k4rvda.info',
		'*.k4rvda.info',
		'k4v0df.info',
		'*.k4v0df.info',
		'k903os.info',
		'*.k903os.info',
		'k9df93d.info',
		'*.k9df93d.info',
		'kv94fd.info',
		'*.kv94fd.info',
		'ksjs93.info',
		'*.ksjs93.info',
		'l0ks03.info',
		'*.l0ks03.info',
		'l9u3jc.info',
		'*.l9u3jc.info',
		'lv043a.info',
		'*.lv043a.info',
		'nh94h9.info',
		'*.nh94h9.info',
		'm94r9d.info',
		'*.m94r9d.info',
		's87fvd.info',
		'*.s87fvd.info',
		'v3k0d.info',
		'*.v3k0d.info',
		'v4r8j4.info',
		'*.v4r8j4.info',
		'vf044s.info',
		'*.vf044s.info',
		'vj49rs.info',
		'*.vj49rs.info',
		'vk498j.info',
		'*.vk498j.info',
		'u03jow.info',
		'*.u03jow.info',
	),
	//
	'Nikhil and Brian' => array(
		'ihfjeswouigf.info',	// by Brian Dieckman (info at iudndjsdhgas.info), / was not found
		'*.ihfjeswouigf.info',
		'iudndjsdhgas.info',	// by Brian Dieckman (info at iudndjsdhgas.info), / was not found
		'*.iudndjsdhgas.info',
		'iufbsehxrtcd.info',	// by Brian Dieckman (info at iudndjsdhgas.info), / was not found
		'*.iufbsehxrtcd.info',
		'jiatdbdisut.info',		// by Brian Dieckman (info at iudndjsdhgas.info), / was not found
		'*.jiatdbdisut.info',
		'jkfierwoundhw.info',	// by Brian Dieckman (info at iudndjsdhgas.info), / was not found
		'*.jkfierwoundhw.info',
		'kfjeoutweh.info',		// by Brian Dieckman (info at iudndjsdhgas.info), / was not found
		'*.kfjeoutweh.info',
		'ncjsdhjahsjendl.info',	// by Brian Dieckman (info at iudndjsdhgas.info), / was not found
		'*.ncjsdhjahsjendl.info',
		'oudjskdwibfm.info',	// by Brian Dieckman (info at iudndjsdhgas.info), / was not found
		'*.oudjskdwibfm.info',
		'cnewuhkqnfke.info',	// by Nikhil Swafford (info at jhcjdnbkrfo.info), / was not found
		'*.cnewuhkqnfke.info',
		'itxbsjacun.info',		// by Nikhil Swafford (info at jhcjdnbkrfo.info), / was not found
		'*.itxbsjacun.info',
		'jahvjrijvv.info',		// by Nikhil Swafford (info at jikpbtjiougje.info), / was not found
		'*.jahvjrijvv.info',
		'jhcjdnbkrfo.info',		// by Nikhil Swafford (info at jhcjdnbkrfo.info), / was not found
		'*.jhcjdnbkrfo.info',
		'najedncdcounrd.info',	// by Nikhil Swafford (info at jhcjdnbkrfo.info), / was not found
		'*.najedncdcounrd.info',
		'mcsjjaouvd.info',		// by Nikhil Swafford (info at jhcjdnbkrfo.info), / was not found
		'*.mcsjjaouvd.info',
		'oujvjfdndl.info',		// by Nikhil Swafford (info at jhcjdnbkrfo.info), / was not found
		'*.oujvjfdndl.info',
		'uodncnewnncds.info',	// by Nikhil Swafford (info at jhcjdnbkrfo.info), / was not found
		'*.uodncnewnncds.info',
		'jikpbtjiougje.info',	// by Julio Mccaughey (info at jikpbtjiougje.info), / was not found
		'*.jikpbtjiougje.info',
		'cijkalvcjirem.info',	// by Gerardo Figueiredo (info at jikpbtjiougje.info), / was not found
		'*.cijkalvcjirem.info',
		'nkcjfkvnvpow.info',	// by Gerardo Figueiredo (info at jikpbtjiougje.info), / was not found
		'*.nkcjfkvnvpow.info',
		'nmiiamfoujvnme.info',	// by Gerardo Figueiredo (info at jikpbtjiougje.info), / was not found
		'*.nmiiamfoujvnme.info',
		'nxuwnkajgufvl.info',	// by Gerardo Figueiredo (info at jikpbtjiougje.info), / was not found
		'*.nxuwnkajgufvl.info',
		'mkjajkfoejvnm.info',	// by Gerardo Figueiredo (info at jikpbtjiougje.info), / was not found
		'*.mkjajkfoejvnm.info',
	),
	//
	'something_noapp' => array(
		'auctioncarslisting.com',	// "No application configured at this url." by John Davis
		'*.auctioncarslisting.com',
		'buy-cheap-hardware.com',	// "No application configured at this url." by Tim Morison (domains at sunex.ru)
		'*.buy-cheap-hardware.com',
		'carsgarage.net',			// "No application configured at this url." by Zonen Herms, and Jimmy Todessky (seomate at gmail.com)
		'*.carsgarage.net',
		'digitshopping.net',		// "No application configured at this url." by Zonen Herms, and Jimmy Todessky (seomate at gmail.com)
		'*.digitshopping.net',
		'your-insurance.biz',		// "No application configured at this url." by Jimmy Todessky (seomate at gmail.com)
		'*.your-insurance.biz',
	),
	//
	// by Henry Ford (wealth777 at gmail.com)
	'Henry Ford' => array(
		'brutal-forced.com',
		'*.brutal-forced.com',
		'library-bdsm.com',
		'*.library-bdsm.com',
	),
	//
	// by Croesus International Inc. (olex at okhei.net)
	'Croesus International Inc.' => array(
		'purerotica.com',
		'*.purerotica.com',
		'richsex.com',
		'*.richsex.com',
		'servik.net',
		'*.servik.net',
		'withsex.com',
		'*.withsex.com',
	),
	//
	'dreamteammoney.com' => array(
		'dreamteammoney.com',	// dtmurl.com related
		'dtmurl.com',			// by dreamteammoney.com
	),
	// KLIK VIP Search and familiy
	'KLIK VIP Search' => array(
		'cheepmed.org',			// "KLIK VIP Search" by petro (petrotsap1 at gmail.com)
		'*.cheepmed.org',
		'fastearning.net',		// "KlikVIPsearch.com" by Matthew  Parry        (fastearning at mail.ru)
		'*.fastearning.net',
		'klikvipsearch.com',	// "KLIKVIPSEARCH.COM" by Adrian Monterra (support at searchservices.info)
		'*.klikvipsearch.com',
		'looked-for.info',		// "MFeed Search" now, by johnson (edu2006alabama at hotmail.com)
		'*.looked-for.info',
		'mnepoxuy.info',		// "KlikVIPsearch.com" by DEREK MIYAMOTO (grosmeba at ukr.net)
		'*.mnepoxuy.info',
		'searchservices.info',	// 403 Forbidden now, by Adrian Monterra (support at searchservices.info)
		'*.searchservices.info',
		'visabiz.net',			// "Visabiz-Katalog-Home" now, by Natalja Estrina (m.estrin at post.skynet.lt)
		'*.visabiz.net',
	),
	//
	// by Andrey Kozlov (vasyapupkin78 at bk.ru)
	'vasyapupkin78 at bk.ru' => array(
		'antivirus1.info',
		'*.antivirus1.info',
		'antivirus2.info',
		'*.antivirus2.info',
	),


	// C-2: Lonely domains (buddies not found yet)
	'0nline-porno.info',	// by Timyr (timyr at narod.ru)
	'*.0nline-porno.info',
	'1111mb.com',
	'*.1111mb.com',
	'19cellar.info',		// by Eduardo Guro (boomouse at gmail.com)
	'6i6.de',
	'*.6i6.de',
	'*.advancediet.com',	// by Shonta Mojica (hostadmin at advancediet.com)
	'adultpersonalsclubs.com',	// by Peter (vaspet34 at yahoo.com)
	'*.adultpersonalsclubs.com',
	'alfanetwork.info',		// by dante (dantequick at gmail.com)
	'*.alfanetwork.info',
	'*.areaseo.com',		// by Antony Carpito (xcentr at lycos.com)
	'awardspace.com',		// by abuse at awardspace.com, no DirectoryIndex
	'*.awardspace.com',
	'*.baurish.info',
	'bestdiscountpharmacy.biz',	// by John  Brown (john780321 at yahoo.com), 2007-01-27, 61.144.122.45
	'bloggerblast.com',		// by B. Kadrie (domains at starwhitehosting.com)
	'*.businessplace.biz',	// by Grenchenko Ivan Petrovich (eurogogi at yandex.ru)
	'covertarena.co.uk',	// by Wayne Huxtable
	'd999.info',			// by Peter Vayner (peter.vayner at inbox.ru)
	'*.d999.info',
	'*.dlekei.info',		// by Maxima Bucaro (webmaster at tts2f.info)
	'*.discutbb.com',		// by Perez Thomas (thomas.jsp at libertysurf.fr)
	'drug-shop.us',			// by Alexandr (matrixpro at mail.ru)
	'*.drug-shop.us',
	'drugs-usa.info',		// by Edward SanFilippo (Edward.SanFilippo at gmail.com), redirect to activefreehost.com
	'*.drugs-usa.info',
	'easyshopusa.com',		// by riter (riter at nm.ru)
	'*.easyshopusa.com',
	'*.ec51.com',			// by zhenfei chen (szczffhh_sso at 21cn.net)
	'ex-web.net',			// RMT by ex co,ltd (rmt at ex-web.net)
	'*.ex-web.net',
	'fastppc.info',			// by peter conor (fastppc at msn.com)
	'*.fateback.com',		// by LiquidNet Ltd. Redirect to www.japan.jp
	'*.free-finding.com',	// by Ny hom (nyhom at yahoo.com)
	'*.free-rx.net',		// by Neo-x (neo-xxl at yandex.ru), redirect to activefreehost.com
	'*.google-yahoo-msn.org',	// by Equipe Tecnica Ajato (rambap at yandex.ru)
	'greatsexdate.com',		// by Andreas Crablo (crablo at hotmail.com)
	'*.greatsexdate.com',
	'*.hot4buy.org',		// by Hot Maker (jot at hot4buy.org)
	'hotnetinfo.info',		// by Lisa Edwards (lisaedwards at ledw.th)
	'hotscriptonline.info',	// by Psy Search (admin at psysearch.com)
	'*.hut1.ru',			// by domains at agava.com
	'*.incbuy.info',		// by Diego T. Murphy (Diego.T.Murphy at incbuy.info)
	'investorvillage.com',
	'ismarket.com',			// Google-hiding. intercage.com related IP
	'italialiveonline.info',	// by Silvio Cataloni (segooglemsn at yahoo.com), redirect to activefreehost.com
	'italy-search.org',		// by Alex Yablin (zaharov-alex at yandex.ru)
	'*.italy-search.org',
	'*.jimka-mmsa.com',		// by Alex Covax (c0vax at mail.ru)
	'*.ls.la',				// by Milton McLellan (McLellanMilton at yahoo.com)
	'milfxxxpass.com',		// by Morozov Pavlik (rulets at gmail.com)
	'*.milfxxxpass.com',
	'myfgj.info',			// by Filus (softscript at gmail.com)
	'*.mujiki.com',			// by Mila Contora (ebumsn at ngs.ru)
	'ngfu2.info',			// by Tara Lagrant (webmaster at ngfu2.info)
	'*.ngfu2.info',
	'onlin-casino.com',		// by Lomis Konstantinos (businessline3000 at gmx.de)
	'*.onlin-casino.com',
	'ornit.info',			// by Victoria C. Frey (Victoria.C.Frey at pookmail.com)
	'*.ornit.info',
	'*.pahuist.info',		// by Yura (yuralg2005 at yandex.ru)
	'*.perevozka777.ru',	// by witalik at gmail.com
	'php0h.com',			// by Byethost Internet Ltd. (hostorgadmin at googlemail.com)
	'*.php0h.com',
	'portaldiscount.com',	// by Mark Tven (bestsaveup at gmail.com)
	'*.portaldiscount.com',
	'*.prama.info',			// by Juan.Kang at mytrashmail.com
	'pulsar.net',			// by TheBuzz Int. (theboss at tfcclion.com)
	'*.pulsar.net',
	'qoclick.net',			// by DMITRIY SOLDATENKO
	'relurl.com',			// tiny-like. by Grzes Tlalka (grzes1111 at interia.pl)
	'*.replicaswatch.org', // by Replin (admin at furnitureblog.org)
	'*.roin.info',			// by Evgenius (roinse at yandex.ru)
	'*.seek-www.com',		// by Adam Smit (pingpong at mail.md)
	'sexmaniacs.org',		// by Yang Chong (chong at x-india.com)
	'*.sexmaniacs.org',
	'sirlook.com',
	'tabsdrugstore.info',	// by Jonn Gardens (admin at SearchHealtAdvCorpGb.com -- no such domain)
	'*.tabsdrugstore.info',
	'*.thetinyurl.com',		// by Beth J. Carter (Beth.J.Carter at thetinyurl.com)
	'topmeds10.com',
	'*.topmeds10.com',
	'unctad.net',			// by gfdogfd at lovespb.com
	'uzing.org',			// by Ashiksh Wasam (wasam at vangers.net)
	'*.vacant.org.uk',
	'*.webnow.biz',			// by Hsien I Fan (admin at servcomputing.com)
	'wellcams.biz',			// by Sergey Sergiyenko (studioboss at gmail.com)
	'*.xpacificpoker.com',	// by Hubert Hoffman (support at xpacificpoker.com)
	'zlocorp.com',			// by tonibcrus at hotpop.com, spammed well with "http ://zlocorp.com/"
	'*.zlocorp.com',

	// C-3: Not classifiable (information wanted)
	//
	// Something incoming to pukiwiki related sites
	'nana.co.il related' => array(
		'planetnana.co.il',
		'*.nana.co.il',
	),
	'mylexus.info',		// by Homer Simpson (simhomer12300 at mail.com), Redirect to Google

	// D: Sample setting of
	// "third party in good faith"s
	//
	// Hosts shown inside of the implanted contents,
	// not used via spam, but maybe useful to detect these contents
	//
	// 'RESERVED',


	// Z: Yours
	//
	//'',
	//'',
	//'',

);
?>
