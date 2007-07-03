<?php
// $Id: domain.ini.php,v 1.1 2007/07/03 14:47:04 henoheno Exp $
// Domain related setting

// Domains who have 2nd and/or 3rd level domains
$domain = array(

	// ccTLD: Australia
	// http://www.auda.org.au/
	// NIC  : http://www.aunic.net/
	// Whois: http://www.ausregistry.com.au/
	'au' => array(
		// .au Second Level Domains
		// http://www.auda.org.au/domains/
		'asn'   => TRUE,
		'com'   => TRUE,
		'conf'  => TRUE,
		'csiro' => TRUE,
		'edu'   => array(	// http://www.domainname.edu.au/
			// Geographic
			'act' => TRUE,
			'nt'  => TRUE,
			'nsw' => TRUE,
			'qld' => TRUE,
			'sa'  => TRUE,
			'tas' => TRUE,
			'vic' => TRUE,
			'wa'  => TRUE,
		),
		'gov'   => array(
			// Geographic
			'act' => TRUE,	// Australian Capital Territory
			'nt'  => TRUE,	// Northern Territory
			'nsw' => TRUE,	// New South Wales
			'qld' => TRUE,	// Queensland
			'sa'  => TRUE,	// South Australia
			'tas' => TRUE,	// Tasmania
			'vic' => TRUE,	// Victoria
			'wa'  => TRUE,	// Western Australia
		),
		'id'    => TRUE,
		'net'   => TRUE,
		'org'   => TRUE,
		'info'  => TRUE,
	),

	// ccTLD: Bahrain
	// NIC  : http://www.inet.com.bh/ (.bh policies not found)
	// Whois: (Not available) http://www.inet.com.bh/
	'bh' => array(
		// Observed
		'com' => TRUE,
		'edu' => TRUE,
		'gov' => TRUE,
		'org' => TRUE,
	),

	// ccTLD: China
	// NIC  : http://www.cnnic.net.cn/en/index/
	// Whois: http://ewhois.cnnic.cn/
	'cn' => array(
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
	),

	// ccTLD: India
	// NIC  : http://www.inregistry.in/
	// Whois: http://www.inregistry.in/whois_search/
	'in' => array(
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
	),

	// ccTLD: South Korea
	// NIC  : http://www.nic.or.kr/english/
	// Whois: http://whois.nida.or.kr/english/
	'kr' => array(
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
	),

	// ccTLD: Japan
	// NIC  : http://jprs.co.jp/en/
	// Whois: http://whois.jprs.jp/en/
	'jp' => array(
		// Guide to JP Domain Name
		// http://jprs.co.jp/en/jpdomain.html

		// Organizational
		'ac' => TRUE,
		'ad' => TRUE,
		'co' => TRUE,
		'ed' => TRUE,
		'go' => TRUE,
		'gr' => TRUE,
		'lg' => TRUE,	// pref.<geographic2nd>.lg.jp etc.
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
	),

	// ccTLD: Mexico
	// NIC  : http://www.nic.mx/
	// Whois: http://www.nic.mx/es/Busqueda.Who_Is
	'mx' => array(
		// Politicas Generales de Nombres de Dominio
		// http://www.nic.mx/es/Politicas?CATEGORY=INDICE
		'com'  => TRUE,
		'edu'  => TRUE,
		'gob'  => TRUE,
		'net'  => TRUE,
		'org'  => TRUE,
	),

	// ccTLD: Russia
	// NIC  : http://www.cctld.ru/en/
	// Whois: http://www.ripn.net:8080/nic/whois/en/
	'ru' => array(
		// List of Reserved second-level Domain Names
		// http://www.cctld.ru/en/doc/detail.php?id21=20&i21=2

		// Organizational
		'ac'   => TRUE,
		'com'  => TRUE,
		'edu'  => TRUE,
		'gov'  => TRUE,
		'int'  => TRUE,
		'mil'  => TRUE,
		'net'  => TRUE,
		'org'  => TRUE,
		'pp'   => TRUE,
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
	),

	// ccTLD: Seychelles
	// NIC  : http://www.nic.sc/
	// Whois: (Not available)
	'sc' => array(
		// http://www.nic.sc/policies.html
		'com' => TRUE,
		'edu' => TRUE,
		'gov' => TRUE,
		'net' => TRUE,
		'org' => TRUE,
	),

	// ccTLD: Taiwan
	// NIC  : http://www.twnic.net.tw/
	// Whois: http://www.twnic.net.tw/
	'tw' => array(
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
	),

	// ccTLD: Tanzania
	// NIC  : http://www.psg.com/dns/tz/
	// Whois: (Not available)
	'tz' => array(
		//  TZ DOMAIN NAMING STRUCTURE
		// http://www.psg.com/dns/tz/tz.txt
		'ac' => TRUE,
		'co' => TRUE,
		'go' => TRUE,
		'ne' => TRUE,
		'or' => TRUE,
	),

	// ccTLD: Ukraine
	// NIC  : http://www.nic.net.ua/
	// Whois: http://whois.com.ua/
	'ua' => array(
		// policy for alternative 2nd level domain names (a2ld)
		// http://www.nic.net.ua/doc/a2ld
		// http://whois.com.ua/
		'cherkassy'  => TRUE,
		'chernigov'  => TRUE,
		'chernovtsy' => TRUE,
		'ck'         => TRUE,
		'cn'         => TRUE,
		'com'        => TRUE,
		'crimea'     => TRUE,
		'cv'         => TRUE,
		'dn'         => TRUE,
		'dnepropetrovsk' => TRUE,
		'donetsk'    => TRUE,
		'dp'         => TRUE,
		'edu'        => TRUE,
		'gov'        => TRUE,
		'if'         => TRUE,
		'ivano-frankivsk' => TRUE,
		'kh'         => TRUE,
		'kharkov'    => TRUE,
		'kherson'    => TRUE,
		'kiev'       => TRUE,
		'kirovograd' => TRUE,
		'km'         => TRUE,
		'kr'         => TRUE,
		'ks'         => TRUE,
		'lg'         => TRUE,
		'lugansk'    => TRUE,
		'lutsk'      => TRUE,
		'lviv'       => TRUE,
		'mk'         => TRUE,
		'net'        => TRUE,
		'nikolaev'   => TRUE,
		'od'         => TRUE,
		'odessa'     => TRUE,
		'org'        => TRUE,
		'pl'         => TRUE,
		'poltava'    => TRUE,
		'rovno'      => TRUE,
		'rv'         => TRUE,
		'sebastopol' => TRUE,
		'sumy'       => TRUE,
		'te'         => TRUE,
		'ternopil'   => TRUE,
		'uz'         => TRUE,
		'uzhgorod'   => TRUE,
		'vinnica'    => TRUE,
		'vn'         => TRUE,
		'zaporizhzhe' => TRUE,
		'zhitomir'   => TRUE,
		'zp'         => TRUE,
		'zt'         => TRUE,
	),

	// ccTLD: United Kingdom
	// NIC  : http://www.nic.uk/
	'uk' => array(
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
	),

	// ccTLD: United States of America
	// NIC  : http://nic.us/
	// Whois: http://whois.us/
	'us' => array(
		// See RFC1480

		// Organizational
		'dni',
		'fed',
		'isa',
		'kids',
		'nsn',

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
	),

	// ccTLD: South Africa
	// NIC  : http://www.zadna.org.za/
	// Whois: 
	//   ac.za  http://www.tenet.ac.za/cgi/cgi_domainquery.exe
	//   co.za  http://co.za/whois.shtml
	//   gov.za http://dnsadmin.gov.za/
	//   org.za http://www.org.za/
	'za' => array(
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
			'ecape' => TRUE,
			'fs.'   => TRUE,
			'gp'    => TRUE,
			'kzn'   => TRUE,
			'lp'    => TRUE,
			'mpm'   => TRUE,
			'ncape' => TRUE,
			'nw'    => TRUE,
			'wcape' => TRUE,
		),
	),

);
?>