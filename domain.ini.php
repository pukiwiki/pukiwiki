<?php
// $Id: domain.ini.php,v 1.3 2007/08/04 13:52:39 henoheno Exp $
// Domain related setting

// Domains who have 2nd and/or 3rd level domains
$domain   = array();
$_pattern = array();

// ------------------------------
// ccTLD: Antigua and Barbuda
// NIC  : http://www.nic.ag/
// Whois: http://ns1.nic.ag/tools/whois.pl
$domain['ag'] = array(
	// AG Blocked or Reserved Domain Names Policy
	// http://www.nic.ag/reserved-names-policy.htm
	// "Available extensions are .AG, .COM.AG, .ORG.AG, .NET.AG, .CO.AG, and .NOM.AG."
	// http://www.nic.ag/
	'co'  => TRUE,
	'com' => TRUE,
	'net' => TRUE,
	'nom' => TRUE,
	'org' => TRUE,
);

// ------------------------------
// ccTLD: Australia
// http://www.auda.org.au/
// NIC  : http://www.aunic.net/
// Whois: http://www.ausregistry.com.au/
$_pattern['au']['geo'] = array(
	// Geographic
	'act' => TRUE, // Australian Capital Territory
	'nt'  => TRUE, // Northern Territory
	'nsw' => TRUE, // New South Wales
	'qld' => TRUE, // Queensland
	'sa'  => TRUE, // South Australia
	'tas' => TRUE, // Tasmania
	'vic' => TRUE, // Victoria
	'wa'  => TRUE, // Western Australia
);
$domain['au'] = array(
	// .au Second Level Domains
	// http://www.auda.org.au/domains/
	'asn'   => TRUE,
	'com'   => TRUE,
	'conf'  => TRUE,
	'csiro' => TRUE,
	'edu'   => & $_pattern['au']['geo'],
	'gov'   => & $_pattern['au']['geo'],
	'id'    => TRUE,
	'net'   => TRUE,
	'org'   => TRUE,
	'info'  => TRUE,
);

// ------------------------------
// ccTLD: Bahrain
// NIC  : http://www.inet.com.bh/ (.bh policies not found)
// Whois: (Not available) http://www.inet.com.bh/
$domain['bh'] = array(
	// Observed
	'com' => TRUE,
	'edu' => TRUE,
	'gov' => TRUE,
	'org' => TRUE,
);

// ------------------------------
// ccTLD: Brazil
// NIC  : http://registro.br/
// Whois: 
$domain['br'] = array(
	// Info: Lista de categorias de dominios
	// http://registro.br/info/dpn.html

	// Categories for institutions
	'agr'  => TRUE, // Agricultural
	'am'   => TRUE, // Broadcasting
	'art'  => TRUE, // Art
	'com'  => TRUE,
	'coop' => TRUE, // Cooperative
	'edu'  => TRUE,
	'esp'  => TRUE, // Sport
	'etc'  => TRUE, // Others
	'far'  => TRUE, // Pharmaceutical
	'fm'   => TRUE, // Broadcasting
	'g12'  => TRUE, // Educational
	'gov'  => TRUE,
	'imb'  => TRUE, // Real estate related
	'ind'  => TRUE, // Industrial
	'inf'  => TRUE, // Informational
	'mil'  => TRUE,
	'net'  => TRUE,
	'org'  => TRUE,
	'psi'  => TRUE, // Internet service providers
	'rec'  => TRUE, // Recreation, entertainment related
	'srv'  => TRUE, // Service-oriented
	'tmp'  => TRUE,
	'tur'  => TRUE, // Tour business
	'tv'   => TRUE,

	// Categories for professionals
	'adm'  => TRUE, // Administrators
	'adv'  => TRUE, // Advocates (Lawers)
	'arq'  => TRUE, // Architects
	'ato'  => TRUE, // Actors
	'bio'  => TRUE, // Biologists
	'bmd'  => TRUE, // Biomedics
	'cim'  => TRUE, // Correctors
	'cng'  => TRUE, // Scenographers
	'cnt'  => TRUE, // Counter (Accountants)
	'ecn'  => TRUE, // Economists
	'eng'  => TRUE, // Engineers
	'eti'  => TRUE, // IT specialists
	'fnd'  => TRUE, // 'Fonoaudiologos', Speech therapists?
	'fot'  => TRUE, // Photographers
	'fst'  => TRUE, // Physiotherapists
	'ggf'  => TRUE, // Geographers
	'jor'  => TRUE, // Journalists
	'lel'  => TRUE, // Auctioneers
	'mat'  => TRUE, // Mathematicians and Statisticians
	'med'  => TRUE, // Doctors
	'mus'  => TRUE, // Musicians
	'not'  => TRUE, // Notaries
	'ntr'  => TRUE, // Nutritionists
	'odo'  => TRUE, // Dentists
	'ppg'  => TRUE, // (Propaganda) Advertising executives and professionals
	'pro'  => TRUE, // Professors
	'psc'  => TRUE, // Psychologists
	'qsl'  => TRUE, // Amateur radio operators
	'slg'  => TRUE, // Sociologists
	'trd'  => TRUE, // Translators
	'vet'  => TRUE, // Veterinarians
	'zlg'  => TRUE, // Zoologists

	// Categories for people
	'blog' => TRUE,
	'flog' => TRUE,
	'nom'  => TRUE,
	'vlog' => TRUE,
	'wiki' => TRUE,
);

// ------------------------------
// ccTLD: China
// NIC  : http://www.cnnic.net.cn/en/index/
// Whois: http://ewhois.cnnic.cn/
$domain['cn'] = array(
	// Provisional Administrative Rules for Registration of Domain Names in China
	// http://www.cnnic.net.cn/html/Dir/2003/11/27/1520.htm

	// Organizational
	'ac'  => TRUE,
	'com' => TRUE,
	'edu' => TRUE,
	'gov' => TRUE,
	'net' => TRUE,
	'org' => TRUE,

	// Geographic
	'ah' => TRUE,
	'bj' => TRUE,
	'cq' => TRUE,
	'fj' => TRUE,
	'gd' => TRUE,
	'gs' => TRUE,
	'gx' => TRUE,
	'gz' => TRUE,
	'ha' => TRUE,
	'hb' => TRUE,
	'he' => TRUE,
	'hi' => TRUE,
	'hk' => TRUE,
	'hl' => TRUE,
	'hn' => TRUE,
	'jl' => TRUE,
	'js' => TRUE,
	'jx' => TRUE,
	'ln' => TRUE,
	'mo' => TRUE,
	'nm' => TRUE,
	'nx' => TRUE,
	'qh' => TRUE,
	'sc' => TRUE,
	'sd' => TRUE,
	'sh' => TRUE,
	'sn' => TRUE,
	'sx' => TRUE,
	'tj' => TRUE,
	'tw' => TRUE,
	'xj' => TRUE,
	'xz' => TRUE,
	'yn' => TRUE,
	'zj' => TRUE,
);

// ------------------------------
// ccTLD: India
// NIC  : http://www.inregistry.in/
// Whois: http://www.inregistry.in/whois_search/
$domain['in'] = array(
	// Policies http://www.inregistry.in/policies/
	'ac'   => TRUE,
	'co'   => TRUE,
	'firm' => TRUE,
	'gen'  => TRUE,
	'gov'  => TRUE,
	'ind'  => TRUE,
	'mil'  => TRUE,
	'net'  => TRUE,
	'org'  => TRUE,
	'res'  => TRUE,
	// Reserved Names by the government (for the 2nd level)
	// http://www.inregistry.in/policies/reserved_names
);


// ------------------------------
// ccTLD: Japan
// NIC  : http://jprs.co.jp/en/
// Whois: http://whois.jprs.jp/en/
$domain['jp'] = array(
	// Guide to JP Domain Name
	// http://jprs.co.jp/en/jpdomain.html

	// Organizational
	'ac' => TRUE,
	'ad' => TRUE,
	'co' => TRUE,
	'ed' => TRUE,
	'go' => TRUE,
	'gr' => TRUE,
	'lg' => TRUE, // pref.<geographic2nd>.lg.jp etc.
	'ne' => TRUE,
	'or' => TRUE,

	// Geographic
	//
	// Examples for 3rd level domains
	//'kumamoto'  => array(
	//	// http://www.pref.kumamoto.jp/link/list.asp#4
	//	'amakusa'   => TRUE,
	//	'hitoyoshi' => TRUE,
	//	'jonan'     => TRUE,
	//	'kumamoto'  => TRUE,
	//	...
	//),
	'aichi'     => TRUE,
	'akita'     => TRUE,
	'aomori'    => TRUE,
	'chiba'     => TRUE,
	'ehime'     => TRUE,
	'fukui'     => TRUE,
	'fukuoka'   => TRUE,
	'fukushima' => TRUE,
	'gifu'      => TRUE,
	'gunma'     => TRUE,
	'hiroshima' => TRUE,
	'hokkaido'  => TRUE,
	'hyogo'     => TRUE,
	'ibaraki'   => TRUE,
	'ishikawa'  => TRUE,
	'iwate'     => TRUE,
	'kagawa'    => TRUE,
	'kagoshima' => TRUE,
	'kanagawa'  => TRUE,
	'kawasaki'  => TRUE,
	'kitakyushu'=> TRUE,
	'kobe'      => TRUE,
	'kochi'     => TRUE,
	'kumamoto'  => TRUE,
	'kyoto'     => TRUE,
	'mie'       => TRUE,
	'miyagi'    => TRUE,
	'miyazaki'  => TRUE,
	'nagano'    => TRUE,
	'nagasaki'  => TRUE,
	'nagoya'    => TRUE,
	'nara'      => TRUE,
	'niigata'   => TRUE,
	'oita'      => TRUE,
	'okayama'   => TRUE,
	'okinawa'   => TRUE,
	'osaka'     => TRUE,
	'saga'      => TRUE,
	'saitama'   => TRUE,
	'sapporo'   => TRUE,
	'sendai'    => TRUE,
	'shiga'     => TRUE,
	'shimane'   => TRUE,
	'shizuoka'  => TRUE,
	'tochigi'   => TRUE,
	'tokushima' => TRUE,
	'tokyo'     => TRUE,
	'tottori'   => TRUE,
	'toyama'    => TRUE,
	'wakayama'  => TRUE,
	'yamagata'  => TRUE,
	'yamaguchi' => TRUE,
	'yamanashi' => TRUE,
	'yokohama'  => TRUE,
);

// ------------------------------
// ccTLD: South Korea
// NIC  : http://www.nic.or.kr/english/
// Whois: http://whois.nida.or.kr/english/
$domain['kr'] = array(
	// .kr domain policy [appendix 1] : Qualifications for Second Level Domains
	// http://domain.nida.or.kr/eng/policy.jsp

	// Organizational
	'co'  => TRUE,
	'ne ' => TRUE,
	'or ' => TRUE,
	're ' => TRUE,
	'pe'  => TRUE,
	'go ' => TRUE,
	'mil' => TRUE,
	'ac'  => TRUE,
	'hs'  => TRUE,
	'ms'  => TRUE,
	'es'  => TRUE,
	'sc'  => TRUE,
	'kg'  => TRUE,

	// Geographic
	'seoul'     => TRUE,
	'busan'     => TRUE,
	'daegu'     => TRUE,
	'incheon'   => TRUE,
	'gwangju'   => TRUE,
	'daejeon'   => TRUE,
	'ulsan'     => TRUE,
	'gyeonggi'  => TRUE,
	'gangwon'   => TRUE,
	'chungbuk'  => TRUE,
	'chungnam'  => TRUE,
	'jeonbuk'   => TRUE,
	'jeonnam'   => TRUE,
	'gyeongbuk' => TRUE,
	'gyeongnam' => TRUE,
	'jeju'      => TRUE,
);

// ------------------------------
// ccTLD: Moldova (No whois server)
// NIC  : http://www.register.md/
// Whois:
//   http://www.max.md/whois/ Second level only
//   http://www.host.md/
$domain['md'] = array(
	// http://www.host.md/ by MoldData (http://www.molddata.md/)
	// "MoldData, a state enterprise" http://www.iana.org/reports/md-report-22oct03.htm
	// http://www.molddata.md/services/domain/molddata.txt
	'com'  => TRUE,
	'co'   => TRUE,
	'info' => TRUE,
	'org'  => TRUE,
	'host' => TRUE,
);

// ------------------------------
// ccTLD: Mexico
// NIC  : http://www.nic.mx/
// Whois: http://www.nic.mx/es/Busqueda.Who_Is
$domain['mx'] = array(
	// Politicas Generales de Nombres de Dominio
	// http://www.nic.mx/es/Politicas?CATEGORY=INDICE
	'com' => TRUE,
	'edu' => TRUE,
	'gob' => TRUE,
	'net' => TRUE,
	'org' => TRUE,
);

// ------------------------------
// ccTLD: New Zealand
// NIC  : http://www.dnc.org.nz/
// Whois: http://www.dnc.org.nz/
$domain['nz'] = array(
	// Second Level Domains
	// http://www.dnc.org.nz/content/second_level_domains.pdf
	'ac'     => TRUE,
	'co'     => TRUE,
	'gen'    => TRUE,
	'geek'   => TRUE,
	'maori'  => TRUE,
	'net'    => TRUE,
	'org'    => TRUE,
	'school' => TRUE,

	// policies and procedures: Moderated Second Level Domains
	// http://www.dnc.org.nz/story/30043-35-1.html
	'cri'        => TRUE, // Crown Research Institutes
	'govt'       => TRUE,
	'iwi'        => TRUE, // Traditional Maori tribes
	'mil'        => TRUE,
	'parliament' => TRUE,
);

// ------------------------------
// ccTLD: Poland
// NIC  : http://www.dns.pl/english/
// Whois: http://www.dns.pl/cgi-bin/en_whois.pl
$domain['pl'] = array(
	// Functional domain names in NASK
	// http://www.dns.pl/english/dns-funk.html
	'agro'       => TRUE,
	'aid'        => TRUE,
	'atm'        => TRUE,
	'auto'       => TRUE,
	'biz'        => TRUE,
	'com'        => TRUE,
	'edu'        => TRUE,
	'gmina'      => TRUE,
	'gsm'        => TRUE,
	'info'       => TRUE,
	'mail'       => TRUE,
	'media'      => TRUE,
	'miasta'     => TRUE,
	'mil'        => TRUE,
	'net'        => TRUE,
	'nieruchomosci' => TRUE,
	'nom'        => TRUE,
	'org'        => TRUE, 
	'pc'         => TRUE,
	'powiat'     => TRUE,
	'priv'       => TRUE,
	'realestate' => TRUE,
	'rel'        => TRUE,
	'sex'        => TRUE,
	'shop'       => TRUE,
	'sklep'      => TRUE,
	'sos'        => TRUE,
	'szkola'     => TRUE,
	'targi'      => TRUE,
	'tm'         => TRUE,
	'tourism'    => TRUE,
	'travel'     => TRUE,
	'turystyka'  => TRUE,

	// Regional domain names in NASK
	// http://www.dns.pl/english/dns-regiony.html
	'augustow'   => TRUE,
	'babia-gora' => TRUE,
	'bedzin'     => TRUE,
	'beskidy'    => TRUE,
	'bialowieza' => TRUE,
	'bialystok'  => TRUE,
	'bielawa'    => TRUE,
	'bieszczady' => TRUE,
	'boleslawiec'=> TRUE,
	'bydgoszcz'  => TRUE,
	'bytom'      => TRUE,
	'cieszyn'    => TRUE,
	'czeladz'    => TRUE,
	'czest'      => TRUE,
	'dlugoleka'  => TRUE,
	'elblag'     => TRUE,
	'elk'        => TRUE,
	'glogow'     => TRUE,
	'gniezno'    => TRUE,
	'gorlice'    => TRUE,
	'grajewo'    => TRUE,
	'ilawa'      => TRUE,
	'jaworzno'   => TRUE,
	'jelenia-gora' => TRUE,
	'jgora'      => TRUE,
	'kalisz'     => TRUE,
	'karpacz'    => TRUE,
	'kartuzy'    => TRUE,
	'kaszuby'    => TRUE,
	'katowice'   => TRUE,
	'kazimierz-dolny' => TRUE,
	'kepno'      => TRUE,
	'ketrzyn'    => TRUE,
	'klodzko'    => TRUE,
	'kobierzyce' => TRUE,
	'kolobrzeg'  => TRUE,
	'konin'      => TRUE,
	'konskowola' => TRUE,
	'kutno'      => TRUE,
	'lapy'       => TRUE,
	'lebork'     => TRUE,
	'legnica'    => TRUE,
	'lezajsk'    => TRUE,
	'limanowa'   => TRUE,
	'lomza'      => TRUE,
	'lowicz'     => TRUE,
	'lubin'      => TRUE,
	'lukow'      => TRUE,
	'malbork'    => TRUE,
	'malopolska' => TRUE,
	'mazowsze'   => TRUE,
	'mazury'     => TRUE,
	'mielec'     => TRUE,
	'mielno'     => TRUE,
	'mragowo'    => TRUE,
	'naklo'      => TRUE,
	'nowaruda'   => TRUE,
	'nysa'       => TRUE,
	'olawa'      => TRUE,
	'olecko'     => TRUE,
	'olkusz'     => TRUE,
	'olsztyn'    => TRUE,
	'opoczno'    => TRUE,
	'opole'      => TRUE,
	'ostroda'    => TRUE,
	'ostroleka'  => TRUE,
	'ostrowiec'  => TRUE,
	'ostrowwlkp' => TRUE,
	'pila'       => TRUE,
	'pisz'       => TRUE,
	'podhale'    => TRUE,
	'podlasie'   => TRUE,
	'polkowice'  => TRUE,
	'pomorskie'  => TRUE,
	'pomorze'    => TRUE,
	'prochowice' => TRUE,
	'pruszkow'   => TRUE,
	'przeworsk'  => TRUE,
	'pulawy'     => TRUE,
	'radom'      => TRUE,
	'rawa-maz'   => TRUE,
	'rybnik'     => TRUE,
	'rzeszow'    => TRUE,
	'sanok'      => TRUE,
	'sejny'      => TRUE,
	'siedlce'    => TRUE,
	'skoczow'    => TRUE,
	'slask'      => TRUE,
	'slupsk'     => TRUE,
	'sosnowiec'  => TRUE,
	'stalowa-wola' => TRUE,
	'starachowice' => TRUE,
	'stargard'   => TRUE,
	'suwalki'    => TRUE,
	'swidnica'   => TRUE,
	'swiebodzin' => TRUE,
	'swinoujscie'=> TRUE,
	'szczecin'   => TRUE,
	'szczytno'   => TRUE,
	'tarnobrzeg' => TRUE,
	'tgory'      => TRUE,
	'turek'      => TRUE,
	'tychy'      => TRUE,
	'ustka'      => TRUE,
	'walbrzych'  => TRUE,
	'warmia'     => TRUE,
	'warszawa'   => TRUE,
	'waw'        => TRUE,
	'wegrow'     => TRUE,
	'wielun'     => TRUE,
	'wlocl'      => TRUE,
	'wloclawek'  => TRUE,
	'wodzislaw'  => TRUE,
	'wolomin'    => TRUE,
	'wroclaw'    => TRUE,
	'zachpomor'  => TRUE,
	'zagan'      => TRUE,
	'zarow'      => TRUE,
	'zgora'      => TRUE,
	'zgorzelec'  => TRUE, 
);

// ------------------------------
// ccTLD: Russia
// NIC  : http://www.cctld.ru/en/
// Whois: http://www.ripn.net:8080/nic/whois/en/
$domain['ru'] = array(
	// List of Reserved second-level Domain Names
	// http://www.cctld.ru/en/doc/detail.php?id21=20&i21=2

	// Organizational
	'ac'  => TRUE,
	'com' => TRUE,
	'edu' => TRUE,
	'gov' => TRUE,
	'int' => TRUE,
	'mil' => TRUE,
	'net' => TRUE,
	'org' => TRUE,
	'pp'  => TRUE,
	//'test' => TRUE,

	// Geographic
	'adygeya'     => TRUE,
	'altai'       => TRUE,
	'amur'        => TRUE,
	'amursk'      => TRUE,
	'arkhangelsk' => TRUE,
	'astrakhan'   => TRUE,
	'baikal'      => TRUE,
	'bashkiria'   => TRUE,
	'belgorod'    => TRUE,
	'bir'         => TRUE,
	'bryansk'     => TRUE,
	'buryatia'    => TRUE,
	'cbg'         => TRUE,
	'chel'        => TRUE,
	'chelyabinsk' => TRUE,
	'chita'       => TRUE,
	'chukotka'    => TRUE,
	'chuvashia'   => TRUE,
	'cmw'         => TRUE,
	'dagestan'    => TRUE,
	'dudinka'     => TRUE,
	'e-burg'      => TRUE,
	'fareast'     => TRUE,
	'grozny'      => TRUE,
	'irkutsk'     => TRUE,
	'ivanovo'     => TRUE,
	'izhevsk'     => TRUE,
	'jamal'       => TRUE,
	'jar'         => TRUE,
	'joshkar-ola' => TRUE,
	'k-uralsk'    => TRUE,
	'kalmykia'    => TRUE,
	'kaluga'      => TRUE,
	'kamchatka'   => TRUE,
	'karelia'     => TRUE,
	'kazan'       => TRUE,
	'kchr'        => TRUE,
	'kemerovo'    => TRUE,
	'khabarovsk'  => TRUE,
	'khakassia'   => TRUE,
	'khv'         => TRUE,
	'kirov'       => TRUE,
	'kms'         => TRUE,
	'koenig'      => TRUE,
	'komi'        => TRUE,
	'kostroma'    => TRUE,
	'krasnoyarsk' => TRUE,
	'kuban'       => TRUE,
	'kurgan'      => TRUE,
	'kursk'       => TRUE,
	'kustanai'    => TRUE,
	'kuzbass'     => TRUE,
	'lipetsk'     => TRUE,
	'magadan'     => TRUE,
	'magnitka'    => TRUE,
	'mari-el'     => TRUE,
	'mari'        => TRUE,
	'marine'      => TRUE,
	'mordovia'    => TRUE,
	'mosreg'      => TRUE,
	'msk'         => TRUE,
	'murmansk'    => TRUE,
	'mytis'       => TRUE,
	'nakhodka'    => TRUE,
	'nalchik'     => TRUE,
	'nkz'         => TRUE,
	'nnov'        => TRUE,
	'norilsk'     => TRUE,
	'nov'         => TRUE,
	'novosibirsk' => TRUE,
	'nsk'         => TRUE,
	'omsk'        => TRUE,
	'orenburg'    => TRUE,
	'oryol'       => TRUE,
	'oskol'       => TRUE,
	'palana'      => TRUE,
	'penza'       => TRUE,
	'perm'        => TRUE,
	'pskov'       => TRUE,
	'ptz'         => TRUE,
	'pyatigorsk'  => TRUE,
	'rnd'         => TRUE,
	'rubtsovsk'   => TRUE,
	'ryazan'      => TRUE,
	'sakhalin'    => TRUE,
	'samara'      => TRUE,
	'saratov'     => TRUE,
	'simbirsk'    => TRUE,
	'smolensk'    => TRUE,
	'snz'         => TRUE,
	'spb'         => TRUE,
	'stavropol'   => TRUE,
	'stv'         => TRUE,
	'surgut'      => TRUE,
	'syzran'      => TRUE,
	'tambov'      => TRUE,
	'tatarstan'   => TRUE,
	'tom'         => TRUE,
	'tomsk'       => TRUE,
	'tsaritsyn'   => TRUE,
	'tsk'         => TRUE,
	'tula'        => TRUE,
	'tuva'        => TRUE,
	'tver'        => TRUE,
	'tyumen'      => TRUE,
	'udm'         => TRUE,
	'udmurtia'    => TRUE,
	'ulan-ude'    => TRUE,
	'vdonsk'      => TRUE,
	'vladikavkaz' => TRUE,
	'vladimir'    => TRUE,
	'vladivostok' => TRUE,
	'volgograd'   => TRUE,
	'vologda'     => TRUE,
	'voronezh'    => TRUE,
	'vrn'         => TRUE,
	'vyatka'      => TRUE,
	'yakutia'     => TRUE,
	'yamal'       => TRUE,
	'yaroslavl'   => TRUE,
	'yekaterinburg'     => TRUE,
	'yuzhno-sakhalinsk' => TRUE,
	'zgrad'       => TRUE,
);

// ------------------------------
// ccTLD: Seychelles
// NIC  : http://www.nic.sc/
// Whois: (Not available)
$domain['sc'] = array(
	// http://www.nic.sc/policies.html
	'com' => TRUE,
	'edu' => TRUE,
	'gov' => TRUE,
	'net' => TRUE,
	'org' => TRUE,
);

// ------------------------------
// ccTLD: Taiwan
// NIC  : http://www.twnic.net.tw/
// Whois: http://www.twnic.net.tw/
$domain['tw'] = array(
	// Guidelines for Administration of Domain Name Registration
	// http://www.twnic.net.tw/english/dn/dn_02.htm
	// II. Types of TWNIC Domain Names and Application Requirements
	// http://www.twnic.net.tw/english/dn/dn_02_b.htm
	'club' => TRUE,
	'com'  => TRUE,
	'ebiz' => TRUE,
	'edu'  => TRUE,
	'game' => TRUE,
	'gov'  => TRUE,
	'idv'  => TRUE,
	'mil'  => TRUE,
	'net'  => TRUE,
	'org'  => TRUE,
	// Reserved words for the 2nd level
	// http://mydn.twnic.net.tw/en/dn02/INDEX.htm
);

// ------------------------------
// ccTLD: Tanzania
// NIC  : http://www.psg.com/dns/tz/
// Whois: (Not available)
$domain['tz'] = array(
	//  TZ DOMAIN NAMING STRUCTURE
	// http://www.psg.com/dns/tz/tz.txt
	'ac' => TRUE,
	'co' => TRUE,
	'go' => TRUE,
	'ne' => TRUE,
	'or' => TRUE,
);

// ------------------------------
// ccTLD: Ukraine
// NIC  : http://www.nic.net.ua/
// Whois: http://whois.com.ua/
$domain['ua'] = array(
	// policy for alternative 2nd level domain names (a2ld)
	// http://www.nic.net.ua/doc/a2ld
	// http://whois.com.ua/

	// Organizational
	'com' => TRUE,
	'edu' => TRUE,
	'gov' => TRUE,
	'net' => TRUE,
	'org' => TRUE,

	// Regional (long and short)
	'cherkassy'       => TRUE,	'ck' => TRUE,
	'chernigov'       => TRUE,	'cn' => TRUE,
	'chernovtsy'      => TRUE,	'cv' => TRUE,
	'crimea'          => TRUE,	'cr' => TRUE,
	'dnepropetrovsk'  => TRUE,	'dp' => TRUE,
	'donetsk'         => TRUE,	'dn' => TRUE,
	'ivano-frankivsk' => TRUE,	'if' => TRUE,
	'kharkov'         => TRUE,	'kh' => TRUE,
	'kherson'         => TRUE,	'ks' => TRUE,
	'khmelnitskiy'    => TRUE,	'km' => TRUE,
	'kiev'            => TRUE,	'kv' => TRUE,
	'kirovograd'      => TRUE,	'kr' => TRUE,
	'lugansk'         => TRUE,	'lg' => TRUE,
	'lutsk'           => TRUE,	'lt' => TRUE,
	'lviv'            => TRUE,	'lv' => TRUE,
	'nikolaev'        => TRUE,	'mk' => TRUE,
	'odessa'          => TRUE,	'od' => TRUE,
	'poltava'         => TRUE,	'pl' => TRUE,
	'rovno'           => TRUE,	'rv' => TRUE,
	'sebastopol'      => TRUE,	'sb' => TRUE,
	'sumy'            => TRUE,	'sm' => TRUE,
	'ternopil'        => TRUE,	'te' => TRUE, // Seems not 'tr'
	'uzhgorod'        => TRUE,	'uz' => TRUE,
	'vinnica'         => TRUE,	'vn' => TRUE,
	'zaporizhzhe'     => TRUE,	'zp' => TRUE,
	'zhitomir'        => TRUE,	'zt' => TRUE,
);

// ------------------------------
// ccTLD: United Kingdom
// NIC  : http://www.nic.uk/
$domain['uk'] = array(
	// Second Level Domains
	// http://www.nic.uk/registrants/aboutdomainnames/sld/
	'co'     => TRUE,
	'ltd'    => TRUE,
	'me'     => TRUE,
	'net'    => TRUE,
	'nic'    => TRUE,
	'org'    => TRUE,
	'plc'    => TRUE,
	'sch'    => TRUE,

	// Delegated Second Level Domains
	// http://www.nic.uk/registrants/aboutdomainnames/sld/delegated/
	'ac'     => TRUE,
	'gov'    => TRUE,
	'mil'    => TRUE,
	'mod'    => TRUE,
	'nhs'    => TRUE,
	'police' => TRUE,
);

// ------------------------------
// ccTLD: United States of America
// NIC  : http://nic.us/
// Whois: http://whois.us/
$domain['us'] = array(
	// See RFC1480

	// Organizational
	'dni'  => TRUE, // Distributed National Institutes
	'fed'  => TRUE, // FEDeral government, <org-name>.<city>.FED.US
	'isa'  => TRUE,
	'kids' => TRUE,
	'nsn'  => TRUE,

	// Geographical
	// United States Postal Service: State abbreviations (for postal codes)
	// http://www.usps.com/ncsc/lookups/abbreviations.html
	'ak' => TRUE, // Alaska
	'al' => TRUE, // Alabama
	'ar' => TRUE, // Arkansas
	'as' => TRUE, // American samoa
	'az' => TRUE, // Arizona
	'ca' => TRUE, // California
	'co' => TRUE, // Colorado
	'ct' => TRUE, // Connecticut
	'dc' => TRUE, // District of Columbia
	'de' => TRUE, // Delaware
	'fl' => TRUE, // Florida
	'fm' => TRUE, // Federated states of Micronesia
	'ga' => TRUE, // Georgia
	'gu' => TRUE, // Guam
	'hi' => TRUE, // Hawaii
	'ia' => TRUE, // Iowa
	'id' => TRUE, // Idaho
	'il' => TRUE, // Illinois
	'in' => TRUE, // Indiana
	'ks' => TRUE, // Kansas
	'ky' => TRUE, // Kentucky
	'la' => TRUE, // Louisiana
	'ma' => TRUE, // Massachusetts
	'md' => TRUE, // Maryland
	'me' => TRUE, // Maine
	'mh' => TRUE, // Marshall Islands
	'mi' => TRUE, // Michigan
	'mn' => TRUE, // Minnesota
	'mo' => TRUE, // Missouri
	'mp' => TRUE, // Northern mariana islands
	'ms' => TRUE, // Mississippi
	'mt' => TRUE, // Montana
	'nc' => TRUE, // North Carolina
	'nd' => TRUE, // North Dakota
	'ne' => TRUE, // Nebraska
	'nh' => TRUE, // New Hampshire
	'nj' => TRUE, // New Jersey
	'nm' => TRUE, // New Mexico
	'nv' => TRUE, // Nevada
	'ny' => TRUE, // New York
	'oh' => TRUE, // Ohio
	'ok' => TRUE, // Oklahoma
	'or' => TRUE, // Oregon
	'pa' => TRUE, // Pennsylvania
	'pr' => TRUE, // Puerto Rico
	'pw' => TRUE, // Palau
	'ri' => TRUE, // Rhode Island
	'sc' => TRUE, // South Carolina
	'sd' => TRUE, // South Dakota
	'tn' => TRUE, // Tennessee
	'tx' => TRUE, // Texas
	'ut' => TRUE, // Utah
	'va' => TRUE, // Virginia
	'vi' => TRUE, // Virgin Islands
	'vt' => TRUE, // Vermont
	'wa' => TRUE, // Washington
	'wi' => TRUE, // Wisconsin
	'wv' => TRUE, // West Virginia
	'wy' => TRUE, // Wyoming
);

// ------------------------------
// ccTLD: South Africa
// NIC  : http://www.zadna.org.za/
// Whois: 
//   ac.za  http://www.tenet.ac.za/cgi/cgi_domainquery.exe
//   co.za  http://co.za/whois.shtml
//   gov.za http://dnsadmin.gov.za/
//   org.za http://www.org.za/
$domain['za'] = array(
	// Second-level subdomains of .ZA
	// http://www.zadna.org.za/slds.html
	'ac'   => TRUE,
	'city' => TRUE,
	'co'   => TRUE,
	'edu'  => TRUE,
	'gov'  => TRUE,
	'law'  => TRUE,
	'mil'  => TRUE,
	'nom'  => TRUE,
	'org'  => TRUE,
	'school' => array(
		// Provincial Domains
		// http://www.esn.org.za/dns/
		'ecape' => TRUE, // Eastern Cape
		'fs.'   => TRUE, // Free State
		'gp'    => TRUE, // Gauteng Province
		'kzn'   => TRUE, // Kwazulu-Natal
		'lp'    => TRUE, // Limpopo Province
		'mpm'   => TRUE, // Mpumalanga
		'ncape' => TRUE, // Northern Cape
		'nw'    => TRUE, // North-West Province
		'wcape' => TRUE, // Western Cape
	),
);

?>