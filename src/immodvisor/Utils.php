<?php
/**
 * Classe regroupant les fonctions utiles
 * Afin de profiter pleinement des mises à jours de cette librarie, il est fortement recommandé de ne pas la modifier
 * @author Jeremy Humbert <jeremy@immodvisor.com>
 * @copyright 2019 immodvisor
 */
namespace immodvisor;

final class Utils extends immodvisor {
	
	public static function getReferer() {
		$protocol = 'http';
		if((array_key_exists('HTTPS', $_SERVER) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1)) || (array_key_exists('HTTP_X_FORWARDED_PROTO', $_SERVER) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) {
			$protocol = 'https';
		}
		$host = (array_key_exists('HTTP_HOST', $_SERVER)) ? $_SERVER['HTTP_HOST'] : null;
		return $protocol.'://'.$host;
	}
	
	public static function isEmail($var, $check_trash=true) {
		if(is_array($var) || is_object($var) || empty($var)) {
			return false;
		}
		if(!preg_match("/^\w(?:\w|-|\.(?!\.|@))*@\w(?:\w|-|\.(?!\.))*\.\w{2,3}$/", $var)) {
			return false;
		}
		if($check_trash && self::isEmailTrash($var)) {
			return false;
		}
		return true;
	}
	
	public static function isEmailTrash($var) {
		if(is_array($var) || is_object($var) || empty($var)) {
			return false;
		}
		$trash = array("@0815.ru0clickemail.com", "@0-mail.com", "@0wnd.net", "@0wnd.org", "@10minutemail.com", "@20minutemail.com", "@2prong.com", "@3d-painting.com", "@4warding.com", "@4warding.net", "@4warding.org", "@9ox.net", "@a-bc.net", "@ag.us.to", "@amilegit.com", "@anonbox.net", "@anonymbox.com", "@antichef.com", "@antichef.net", "@antispam.de", "@baxomale.ht.cx", "@beefmilk.com", "@binkmail.com", "@bio-muesli.net", "@bobmail.info", "@bodhi.lawlita.com", "@bofthew.com", "@brefmail.com", "@bsnow.net", "@bugmenot.com", "@bumpymail.com", "@casualdx.com", "@chogmail.com", "@cool.fr.nf", "@correo.blogos.net", "@cosmorph.com", "@courriel.fr.nf", "@courrieltemporaire.com", "@curryworld.de", "@cust.in", "@dacoolest.com", 
					   "@dandikmail.com", "@deadaddress.com", "@despam.it", "@despam.it", "@devnullmail.com", "@dfgh.net", "@digitalsanctuary.com", "@discardmail.com", "@discardmail.de", "@disposableaddress.com", "@disposeamail.com", "@disposemail.com", "@dispostable.com", "@dm.w3internet.co.ukexample.com", "@dodgeit.com", "@dodgit.com", "@dodgit.org", "@dontreg.com", "@dontsendmespam.de", "@dump-email.info", "@dumpyemail.com", "@e4ward.com", "@email60.com", "@emailias.com", "@emailias.com", "@emailinfive.com", "@emailmiser.com", "@emailtemporario.com.br", "@emailwarden.com", "@enterto.com", "@ephemail.net", "@explodemail.com", "@fakeinbox.com", "@fakeinformation.com", "@fansworldwide.de", "@fastacura.com", "@filzmail.com", 
					   "@fixmail.tk", "@fizmail.com", "@frapmail.com", "@garliclife.com", "@gelitik.in", "@get1mail.com", "@getonemail.com", "@getonemail.net", "@girlsundertheinfluence.com", "@gishpuppy.com", "@goemailgo.com", "@great-host.in", "@greensloth.com", "@greensloth.com", "@gsrv.co.uk", "@guerillamail.biz", "@guerillamail.com", "@guerillamail.net", "@guerillamail.org", "@guerrillamail.biz", "@guerrillamail.com", "@guerrillamail.de", "@guerrillamail.net", "@guerrillamail.org", "@guerrillamailblock.com", "@haltospam.com", "@hidzz.com", "@hotpop.com", "@ieatspam.eu", "@ieatspam.info", "@ihateyoualot.info", "@imails.info", "@inboxclean.com", "@inboxclean.org", "@incognitomail.com", "@incognitomail.net", "@ipoo.org", 
					   "@irish2me.com", "@jetable.com", "@jetable.fr.nf", "@jetable.net", "@jetable.org", "@jnxjn.com", "@junk1e.com", "@kasmail.com", "@kaspop.com", "@klzlk.com", "@kulturbetrieb.info", "@kurzepost.de", "@kurzepost.de", "@lifebyfood.com", "@link2mail.net", "@litedrop.com", "@lookugly.com", "@lopl.co.cc", "@lr78.com", "@maboard.com", "@mail.by", "@mail.mezimages.net", "@mail4trash.com", "@mailbidon.com", "@mailcatch.com", "@maileater.com", "@mailexpire.com", "@mailin8r.com", "@mailinator.com", "@mailinator.net", "@mailinator2.com", "@mailincubator.com", "@mailme.lv", "@mailmetrash.com", "@mailmoat.com", "@mailnator.com", "@mailnull.com", "@mailzilla.org", "@mbx.cc", "@mega.zik.dj", "@meltmail.com", "@mierdamail.com", 
					   "@mintemail.com", "@mjukglass.nu", "@mobi.web.id", "@moburl.com", "@moncourrier.fr.nf", "@monemail.fr.nf", "@monmail.fr.nf", "@mt2009.com", "@mx0.wwwnew.eu", "@mycleaninbox.net", "@myspamless.com", "@mytempemail.com", "@mytrashmail.com", "@netmails.net", "@neverbox.com", "@no-spam.ws", "@nobulk.com", "@noclickemail.com", "@nogmailspam.info", "@nomail.xl.cx", "@nomail2me.com", "@nospam.ze.tc", "@nospam4.us", "@nospamfor.us", "@nowmymail.com", "@objectmail.com", "@obobbo.com", "@odaymail.com", "@onewaymail.com", "@ordinaryamerican.net", "@owlpic.com", "@pookmail.com", "@privymail.de", "@proxymail.eu", "@punkass.com", "@putthisinyourspamdatabase.com", "@quickinbox.com", "@rcpt.at", "@recode.me", "@recursor.net", 
					   "@regbypass.comsafe-mail.net", "@safetymail.info", "@sandelf.de", "@saynotospams.com", "@selfdestructingmail.com", "@sendspamhere.com", "@sharklasers.com", "@shieldedmail.com", "@shiftmail.com", "@skeefmail.com", "@slopsbox.com", "@slushmail.com", "@smaakt.naar.gravel", "@smellfear.com", "@snakemail.com", "@sneakemail.com", "@sofort-mail.de", "@sogetthis.com", "@soodonims.com", "@spam.la", "@spamavert.com", "@spambob.net", "@spambob.org", "@spambog.com", "@spambog.de", "@spambog.ru", "@spambox.info", "@spambox.us", "@spamcannon.com", "@spamcannon.net", "@spamcero.com", "@spamcorptastic.com", "@spamcowboy.com", "@spamcowboy.net", "@spamcowboy.org", "@spamday.com", "@spamex.com", "@spamfree.eu", "@spamfree24.com", 
					   "@spamfree24.de", "@spamfree24.eu", "@spamfree24.info", "@spamfree24.net", "@spamfree24.org", "@spamgourmet.com", "@spamgourmet.net", "@spamgourmet.org", "@spamherelots.com", "@spamhereplease.com", "@spamhole.com", "@spamify.com", "@spaminator.de", "@spamkill.info", "@spaml.com", "@spaml.de", "@spammotel.com", "@spamobox.com", "@spamspot.com", "@spamthis.co.uk", "@spamthisplease.com", "@speed.1s.fr", "@suremail.info", "@tempalias.com", "@tempe-mail.com", "@tempemail.biz", "@tempemail.com", "@tempemail.net", "@tempinbox.co.uk", "@tempinbox.com", "@tempomail.fr", "@temporaryemail.net", "@temporaryinbox.com", "@tempymail.com", "@thankyou2010.com", "@thisisnotmyrealemail.com", "@throwawayemailaddress.com", 
					   "@tilien.com", "@tmailinator.com", "@tradermail.info", "@trash-amil.com", "@trash-mail.at", "@trash-mail.com", "@trash-mail.de", "@trash2009.com", "@trashmail.at", "@trashmail.com", "@trashmail.me", "@trashmail.net", "@trashmailer.com", "@trashymail.com", "@trashymail.net", "@trillianpro.com", "@tyldd.com", "@tyldd.com", "@uggsrock.com", "@wegwerfmail.de", "@wegwerfmail.net", "@wegwerfmail.org", "@wh4f.org", "@whyspam.me", "@willselfdestruct.com", "@winemaven.info", "@wronghead.com", "@wuzupmail.net", "@xoxy.net", "@yogamaven.com", "@yopmail.com", "@yopmail.fr", "@yopmail.net", "@yuurok.com", "@zippymail.info", "@zoemail.com");
		for($i=0; $i<  count($trash); $i++) {
			if(substr($var, 0-strlen($trash[$i])) == $trash[$i]) {
				return true;
			}
		}
		return false;
	}
	
	public static function isInt($var, $unsigned=true) {
		if(is_array($var) || is_object($var)) {
			return false;
		}
		if($unsigned) {
			return (bool)preg_match('`^[0-9]+$`', $var);
		}
		return (bool)preg_match('`^-?[0-9]+$`', $var);
	}
	
	public static function isFloat($var, $unsigned=true) {
		if(is_array($var) || is_object($var)) {
			return false;
		}
		if($unsigned) {
			return (bool)preg_match('`^[0-9]*\.?[0-9]+$`', $var);
		}
		return (bool)preg_match('`^-?[0-9]*\.?[0-9]+$`', $var);
	}
	
	public static function isUrl($var, $protocols=array('http','https')) {
		if(is_array($var) || is_object($var)) {
			return false;
		}
		if(is_array($protocols)) {
			$ok = false;
			foreach($protocols as $k => $v) {
				if(strpos($var, $v."://") !== false) {
					$ok = true;
					break;
				}
			}
			if(!$ok) {
				return false;
			}
		}
		$var = self::removeAccents($var);
		return (filter_var($var, FILTER_VALIDATE_URL) === false) ? false : true;
	}
	
	public static function removeAccents($string) {
		if(!preg_match('/[\x80-\xff]/', $string)) {
			return $string;
		}
		$chars = array(
			chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
			chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
			chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
			chr(195).chr(135) => 'C', chr(195).chr(136) => 'E',
			chr(195).chr(137) => 'E', chr(195).chr(138) => 'E',
			chr(195).chr(139) => 'E', chr(195).chr(140) => 'I',
			chr(195).chr(141) => 'I', chr(195).chr(142) => 'I',
			chr(195).chr(143) => 'I', chr(195).chr(145) => 'N',
			chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
			chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
			chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
			chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
			chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
			chr(195).chr(159) => 's', chr(195).chr(160) => 'a',
			chr(195).chr(161) => 'a', chr(195).chr(162) => 'a',
			chr(195).chr(163) => 'a', chr(195).chr(164) => 'a',
			chr(195).chr(165) => 'a', chr(195).chr(167) => 'c',
			chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
			chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
			chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
			chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
			chr(195).chr(177) => 'n', chr(195).chr(178) => 'o',
			chr(195).chr(179) => 'o', chr(195).chr(180) => 'o',
			chr(195).chr(181) => 'o', chr(195).chr(182) => 'o',
			chr(195).chr(182) => 'o', chr(195).chr(185) => 'u',
			chr(195).chr(186) => 'u', chr(195).chr(187) => 'u',
			chr(195).chr(188) => 'u', chr(195).chr(189) => 'y',
			chr(195).chr(191) => 'y',
			chr(196).chr(128) => 'A', chr(196).chr(129) => 'a',
			chr(196).chr(130) => 'A', chr(196).chr(131) => 'a',
			chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
			chr(196).chr(134) => 'C', chr(196).chr(135) => 'c',
			chr(196).chr(136) => 'C', chr(196).chr(137) => 'c',
			chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
			chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
			chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
			chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
			chr(196).chr(146) => 'E', chr(196).chr(147) => 'e',
			chr(196).chr(148) => 'E', chr(196).chr(149) => 'e',
			chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
			chr(196).chr(152) => 'E', chr(196).chr(153) => 'e',
			chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
			chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
			chr(196).chr(158) => 'G', chr(196).chr(159) => 'g',
			chr(196).chr(160) => 'G', chr(196).chr(161) => 'g',
			chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
			chr(196).chr(164) => 'H', chr(196).chr(165) => 'h',
			chr(196).chr(166) => 'H', chr(196).chr(167) => 'h',
			chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
			chr(196).chr(170) => 'I', chr(196).chr(171) => 'i',
			chr(196).chr(172) => 'I', chr(196).chr(173) => 'i',
			chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
			chr(196).chr(176) => 'I', chr(196).chr(177) => 'i',
			chr(196).chr(178) => 'IJ',chr(196).chr(179) => 'ij',
			chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
			chr(196).chr(182) => 'K', chr(196).chr(183) => 'k',
			chr(196).chr(184) => 'k', chr(196).chr(185) => 'L',
			chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
			chr(196).chr(188) => 'l', chr(196).chr(189) => 'L',
			chr(196).chr(190) => 'l', chr(196).chr(191) => 'L',
			chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
			chr(197).chr(130) => 'l', chr(197).chr(131) => 'N',
			chr(197).chr(132) => 'n', chr(197).chr(133) => 'N',
			chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
			chr(197).chr(136) => 'n', chr(197).chr(137) => 'N',
			chr(197).chr(138) => 'n', chr(197).chr(139) => 'N',
			chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
			chr(197).chr(142) => 'O', chr(197).chr(143) => 'o',
			chr(197).chr(144) => 'O', chr(197).chr(145) => 'o',
			chr(197).chr(146) => 'OE',chr(197).chr(147) => 'oe',
			chr(197).chr(148) => 'R',chr(197).chr(149) => 'r',
			chr(197).chr(150) => 'R',chr(197).chr(151) => 'r',
			chr(197).chr(152) => 'R',chr(197).chr(153) => 'r',
			chr(197).chr(154) => 'S',chr(197).chr(155) => 's',
			chr(197).chr(156) => 'S',chr(197).chr(157) => 's',
			chr(197).chr(158) => 'S',chr(197).chr(159) => 's',
			chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
			chr(197).chr(162) => 'T', chr(197).chr(163) => 't',
			chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
			chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
			chr(197).chr(168) => 'U', chr(197).chr(169) => 'u',
			chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
			chr(197).chr(172) => 'U', chr(197).chr(173) => 'u',
			chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
			chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
			chr(197).chr(178) => 'U', chr(197).chr(179) => 'u',
			chr(197).chr(180) => 'W', chr(197).chr(181) => 'w',
			chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
			chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z',
			chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z',
			chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
			chr(197).chr(190) => 'z', chr(197).chr(191) => 's'
		);
		$string = strtr($string, $chars);
		return $string;
	}
	
}
