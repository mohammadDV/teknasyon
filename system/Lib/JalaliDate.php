<?php
namespace System\Lib;

use \DateTime;
use \DateTimeZone;

class JalaliDate extends DateTime  {

	var $org_date;
	var $gregor;
	var $hejri;
	var $ghamari;
	private $timeZoneText = '"+03:00"';
//	private $defLang = 'fa_IR';
	private $defLang = 'en_US';
	private $timezone = NULL;

	public function getTimeZoneText()
	{
		return $this->timeZoneText();
	}

	public function setDefaultLang($lang){
		$this->defLang = $lang;
	}

	public function __construct($timezone = 'Asia/Tehran',$time = "now"){
		$this->timezone = new DateTimeZone($timezone);
		parent::__construct($time, $this->timezone);
		
//		date_default_timezone_set($timezone);

		$this->gregor['year']		        =	parent::format('Y');
		$this->gregor['month']		        =	parent::format('m');
		$this->gregor['day']		        =	parent::format('d');
		$this->gregor['timestamp']	        =	parent::format('U');
		$this -> gregor['days_in_month']    = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);

		$this -> gregor['month_name']       = array("", "ژانویه", "فوریه", "مارس", "آپریل","می", "ژوئن", "جولای", "اگوست",
				"سپتامر","اکتبر", "نوامبر", "دسامبر");
		$this -> gregor['week_name']        = array("", "Monday", "Tuesday", "Wednesday","Thursday", "Friday", "Saturday", "Sunday");
		$this -> gregor['no_name']          = array("", "یکم", "دوم", "سوم","چهارم", "پنجم", "ششم", "هفتم","هشتم", "نهم", "دهم", "یازدهم",
				"دوازدهم", "سیزدهم","چهاردهم", "پانزدهم", "شانزدهم", "هفدهم", "هجدهم", "نوزدهم",
				"بیستم", "بیست و یکم", "بیست و دوم", "بیست و سوم", "بیست و چهارم", "بیست و پنجم",
				"بیست و ششم", "بیست و هفتم", "بیست و هشتم", "بیست و نهم", "سی ام", "سی و یکم");

		$this -> hejri['days_in_month']     = array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);
		$this -> hejri['month_name']        = array("", "فروردین", "اردیبهشت", "خرداد", "تیر","مرداد", "شهریور", "مهر", "آبان", "آذر",
				"دی", "بهمن", "اسفند");
		$this -> hejri['week_name']         = array("", "شنبه", "یکشنبه", "دوشنبه","سه شنبه", "چهار شنبه", "پنج شنبه", "جمعه");

		$hejr                               = $this -> gregorian_to_jalali();
		$this -> hejri['year'] = $hejr[0];
		$this -> hejri['month'] = $hejr[1];
		$this -> hejri['day'] = $hejr[2];
		if ($this->hejri['month'] > 6) $this->timeZoneText = '+03:30';
		$this -> hejri['no_name'] = array("", "یکم", "دوم", "سوم","چهارم", "پنجم", "ششم", "هفتم","هشتم", "نهم", "دهم", "یازدهم",
				"دوازدهم", "سیزدهم","چهاردهم", "پانزدهم", "شانزدهم", "هفدهم", "هجدهم", "نوزدهم",
				"بیستم", "بیست و یکم", "بیست و دوم", "بیست و سوم", "بیست و چهارم", "بیست و پنجم",
				"بیست و ششم", "بیست و هفتم", "بیست و هشتم", "بیست و نهم", "سی ام", "سی و یکم");

		$this -> ghamari['days_in_month'] = array(30, 29, 30, 29, 30, 29, 30, 29, 30, 29, 30, 29);
		$this -> ghamari['month_name'] = array("", "محرم", "صفر", "ربیع الاول", "ربیع الثانی","جمادی الاول", "جمادی الثانی","رجب","شعبان", "رمضان","شوال", "ذی القعده", "ذی الحجه");
		$this -> ghamari['week_name'] = array("", "شنبه", "یکشنبه", "دوشنبه","سه شنبه", "چهار شنبه", "پنج شنبه", "جمعه");

		$ghamar = $this -> gregorian_to_ghamari();
		$this -> ghamari['year'] = $ghamar[0];
		$this -> ghamari['month'] = $ghamar[1];
		$this -> ghamari['day'] = $ghamar[2];
		$this -> ghamari['no_name'] = array("", "یکم", "دوم", "سوم","چهارم", "پنجم", "ششم", "هفتم","هشتم", "نهم", "دهم", "یازدهم",
				"دوازدهم", "سیزدهم","چهاردهم", "پانزدهم", "شانزدهم", "هفدهم", "هجدهم", "نوزدهم",
				"بیستم", "بیست و یکم", "بیست و دوم", "بیست و سوم", "بیست و چهارم", "بیست و پنجم",
				"بیست و ششم", "بیست و هفتم", "بیست و هشتم", "بیست و نهم", "سی ام", "سی و یکم");

	}

	public function setTimezone($timezone){
		parent::setTimezone(new DateTimeZone($timezone));
		$this -> set_date_from_ts(parent::format('U'));
	}

	public function getTimezone(){
		return parent::getTimezone();
	}

	public function format($format,$lang = NULL){
		if (is_null($lang)) $lang = $this->defLang;
		return $this -> date($format,$lang,parent::format('U'));
	}

	public function get_month_name($no = '',$lang = NULL){
		if (is_null($lang)) $lang = $this->defLang;
		switch ($lang){
			case 'en':
			case 'en_US':
				if (empty($no)){
					return $this -> date('F','en_US');
				}else {
					return $this -> gregor['month_name'][$no];
				}
				break;
			case 'ar':
				if (empty($no)){
					return $this -> date('F','ar');
				}else {
					return $this -> ghamari['month_name'][$no];
				}
				break;
			default:
				if (empty($no)){
					return $this -> date('F');
				}else {
					return $this -> hejri['month_name'][$no];
				}
				break;
		}
	}

	public function reset(){
		parent::setTimestamp(time());
		$this->gregor['year']		=	parent::format('Y');
		$this->gregor['month']		=	parent::format('m');
		$this->gregor['day']		=	parent::format('d');
		$this->gregor['timestamp']	=	parent::format('U');
		$hejr = $this -> gregorian_to_jalali();
		$this->hejri['year']		=	$hejr[0];
		$this->hejri['month']		=	$hejr[1];
		$this->hejri['day']			=	$hejr[2];
		$ghamar = $this -> gregorian_to_ghamari();
		$this->ghamari['year']		=	$ghamar[0];
		$this->ghamari['month']		=	$ghamar[1];
		$this->ghamari['day']		=	$ghamar[2];
	}

	public function set_date_from_ts($timestamp = ''){
		if (!is_int($timestamp)) return ;
		parent::setTimestamp($timestamp);
		$this->gregor['year']		=	parent::format('Y');
		$this->gregor['month']		=	parent::format('m');
		$this->gregor['day']		=	parent::format('d');
		$this->gregor['timestamp']	=	parent::format('U');
		$hejr = $this -> gregorian_to_jalali();
		$this->hejri['year']		=	$hejr[0];
		$this->hejri['month']		=	$hejr[1];
		$this->hejri['day']			=	$hejr[2];
		$ghamar = $this -> gregorian_to_ghamari();
		$this->ghamari['year']		=	$ghamar[0];
		$this->ghamari['month']		=	$ghamar[1];
		$this->ghamari['day']		=	$ghamar[2];

	}

	public function get_timestamp($year = '',$month = '',$day = '',$hour = '',$min = '',$sec = '',$lang = ''){
		if (empty($year)){	$year	=	$this->date('Y',$lang);		}
		if (empty($month)){	$month	=	$this->date('m',$lang);		}
		if (empty($day)){	$day	=	$this->date('d',$lang);		}
		if (empty($hour)){	$hour	=	$this->date('H',$lang);		}
		if (empty($min)){	$min	=	$this->date('i',$lang);		}
		if (empty($sec)){	$sec	=	$this->date('s',$lang);		}
		return mktime($hour,$min,$sec,$month,$day,$year);
	}

	public function set_gregor_date($year,$month,$day){
		parent::setDate($year, $month, $day);
		$this->gregor['year']		=	parent::format('Y');
		$this->gregor['month']		=	parent::format('m');
		$this->gregor['day']		=	parent::format('d');
		$this->gregor['timestamp']	=	parent::format('U');
	}

	public function set_hejri_date($year,$month,$day){
		$this->hejri['year']		=	intval($year);
		$this->hejri['month']		=	intval($month);
		$this->hejri['day']			=	intval($day);
		$this->jalali_to_gregorian();
		$this->gregorian_to_ghamari();
	}

	public function set_ghamari_date($year,$month,$day){
		$this->ghamari['year']		=	intval($year);
		$this->ghamari['month']		=	intval($month);
		$this->ghamari['day']		=	intval($day);
	}

	public function ghamari2gregorian($y, $m, $d){
		if($y < 1700){

			$jd = $this->intPart((11 * $y + 3) / 30) + 354 * $y + 30 * $m - $this->intPart(($m - 1) / 2) + $d + 1948440 - 385;

			if($jd > 2299160){
				$l = $jd + 68569;
				$n = $this->intPart((4 * $l) / 146097);
				$l = $l - $this->intPart((146097 * $n + 3) / 4);
				$i = $this->intPart((4000 * ($l + 1)) / 1461001);
				$l = $l - $this->intPart((1461 * $i) / 4) + 31;
				$j = $this->intPart((80 * $l) / 2447);
				$d = $l - $this->intPart((2447 * $j) / 80);
				$l = $this->intPart($j / 11);
				$m = $j + 2 - 12 * $l;
				$y = 100 * ($n - 49) + $i + $l;
			}else{
				$j = $jd + 1402;
				$k = $this->intPart(($j - 1) / 1461);
				$l = $j - 1461 * $k;
				$n = $this->intPart(($l - 1) / 365) - $this->intPart($l / 1461);
				$i = $l - 365 * $n + 30;
				$j = $this->intPart((80 * $i) / 2447);
				$d = $i - $this->intPart((2447 * $j) / 80);
				$i = $this->intPart($j / 11);
				$m = $j + 2 - 12 * $i;
				$y = 4 * $k + $n + $i - 4716;
			}

			if($d < 10)
				$d = "0" . $d;
			if($m < 10)
				$m = "0" . $m;
			parent::setDate($y, $m, $d);
			$this->gregor['year']		=	parent::format('Y');
			$this->gregor['month']		=	parent::format('m');
			$this->gregor['day']		=	parent::format('d');
			$this->gregor['timestamp']	=	parent::format('U');
			$this->gregorian_to_jalali();
			return array($y,$m,$d);
		}else
			return "";
	}

	private function intPart($floatNum){
		if($floatNum < -0.0000001){
			return ceil($floatNum - 0.0000001);
		}
		return floor($floatNum + 0.0000001);
	}

	private function div($a,$b){
		return (int) ($a/$b);
	}

	private function is_kabise($year){
		if($year%4==0 && $year%100!=0){
			return true;
		}
		return false;
	}

	private function get_g_dn($year = '',$month = '',$day = ''){
		if (empty($year)){ $gy = $this -> gregor['year']; }else { $gy = $year; }
		if (empty($month)){ $gm = $this -> gregor['month']; }else { $gm = $month; }
		if (empty($day)){ $gd = $this -> gregor['day']; }else { $gd = $day; }
		$gy = $gy -1600; $gm = $gm -1; $gd = $gd -1;
		$g_day_no = 365 * $gy + $this -> div($gy+3,4) - $this -> div($gy+99,100) + $this -> div($gy+399,400);

		for ($i=0; $i < $gm; ++$i){
			$g_day_no += $this -> gregor['days_in_month'][$i];
		}

		if ($gm > 1 && (($gy%4==0 && $gy%100!=0) || ($gy%400==0))){ $g_day_no += 1; }

		$g_day_no += $gd;
		return $g_day_no;
	}

	public function get_j_dn($year = '',$month = '',$day = ''){
		if (empty($year)){ $jy = $this -> hejri['year']; }else { $jy = $year; }
		if (empty($month)){ $jm = $this -> hejri['month']; }else { $jm = $month; }
		if (empty($day)){ $jd = $this -> hejri['day']; }else { $jd = $day; }

		$jy = $jy - 979; $jm = $jm - 1; $jd = $jd - 1;
		$j_day_no = 365 * $jy + $this -> div($jy, 33) * 8 + $this -> div($jy%33+3, 4);

		for ($i=0; $i < $jm; ++$i){
			$j_day_no += $this -> hejri['days_in_month'][$i];
		}

		$j_day_no += $jd;

		return $j_day_no;
	}

	public function get_gh_dn($year = '',$month = '',$day = ''){
		if (empty($year)){ $jy = $this->ghamari['year']; }else { $jy = $year; }
		if (empty($month)){ $jm = $this->ghamari['month']; }else { $jm = $month; }
		if (empty($day)){ $jd = $this->ghamari['day']; }else { $jd = $day; }

		$jy = $jy - 979; $jm = $jm - 1; $jd = $jd - 1;
		$j_day_no = 365 * $jy + $this -> div($jy, 33) * 8 + $this -> div($jy%33+3, 4);

		for ($i=0; $i < $jm; ++$i){
			$j_day_no += $this -> hejri['days_in_month'][$i];
		}

		$j_day_no += $jd;

		return $j_day_no;
	}

	public function gregorian_to_jalali($year = '',$month = '',$day = ''){
		if (empty($year)){ $gy = $this -> gregor['year']; }else { $gy = $year; }
		if (empty($month)){ $gm = $this -> gregor['month']; }else { $gm = $month; }
		if (empty($day)){ $gd = $this -> gregor['day']; }else { $gd = $day; }

		$g_day_no = $this -> get_g_dn($gy,$gm,$gd);
		$j_day_no = $g_day_no-79;

		$j_np = $this -> div($j_day_no, 12053); $j_day_no = $j_day_no % 12053;

		$this -> hejri['year'] = 979 + 33 * $j_np + 4 * $this -> div($j_day_no,1461); $j_day_no %= 1461;

		if ($j_day_no >= 366){ $this -> hejri['year'] += $this -> div($j_day_no-1, 365); $j_day_no = ($j_day_no-1)%365; }

		for ($i = 0; $i < 11 && $j_day_no >= $this -> hejri['days_in_month'][$i]; $i++){
			$j_day_no -= $this -> hejri['days_in_month'][$i];
		}

		$this -> hejri['month'] = $i+1;
		$this -> hejri['day'] = $j_day_no+1;
		if (strlen($this -> hejri['month']) == 1){ $this -> hejri['month'] = '0'.$this -> hejri['month']; }
		if (strlen($this -> hejri['day']) == 1){ $this -> hejri['day'] = '0'.$this -> hejri['day']; }
		return array($this -> hejri['year'], $this -> hejri['month'], $this -> hejri['day']);
	}

	public function gregorian_to_ghamari($year = '',$month = '',$day = ''){
		if (empty($year)){ $gy = $this -> gregor['year']; }else { $gy = $year; }
		if (empty($month)){ $gm = $this -> gregor['month']; }else { $gm = $month; }
		if (empty($day)){ $gd = $this -> gregor['day']; }else { $gd = $day; }
		$ts = mktime(date('H'),date('i'),date('s'),$gm,$gd,$gy);
		$TDays=round($ts/(60*60*24));
		$HYear=round($TDays/354.37419);
		$Remain=$TDays-($HYear*354.37419);
		$HMonths=round($Remain/29.531182);
		$HDays=$Remain-($HMonths*29.531182);
		$HYear=$HYear+1389;
		$HMonths=$HMonths+10;
		$HDays=$HDays+23;

		// If the days is over 29, then update month and reset days
		if ($HDays>29.531188 and round($HDays)!=30){
			$HMonths=$HMonths+1;
			$HDays=Round($HDays-29.531182);
		}

		else{
			$HDays=Round($HDays);
		}

		// If months is over 12, then add a year, and reset months
		if($HMonths>12){
			$HMonths=$HMonths-12;
			$HYear=$HYear+1;
		}
		$this -> ghamari['year'] = $HYear;
		$this -> ghamari['month'] = $HMonths;
		$this -> ghamari['day'] = $HDays-1; // male ghamarii!!!

		return array($this -> ghamari['year'], $this -> ghamari['month'], $this -> ghamari['day']);
	}

	public function jalali_to_gregorian($year = '',$month = '',$day = ''){
		if (empty($year)){		$jy = $this->hejri['year'];		}else { $jy	=	$year;	}
		if (empty($month)){		$jm = $this->hejri['month'];	}else { $jm	=	$month;	}
		if (empty($day)){		$jd = $this->hejri['day'];		}else { $jd	=	$day;	}
		$j_day_no = $this->get_j_dn($jy,$jm,$jd);

		$g_day_no = $j_day_no + 79;

		$gy = 1600 + 400 * $this -> div($g_day_no, 146097);
		$g_day_no = $g_day_no % 146097;

		$leap = true;
		if ($g_day_no >= 36525){
			$g_day_no--;
			$gy += 100 * $this -> div($g_day_no,  36524);
			$g_day_no = $g_day_no % 36524;
			if ($g_day_no >= 365){
				$g_day_no++;
			}else {
				$leap = false;
			}
		}

		$gy += 4 * $this -> div($g_day_no, 1461);
		$g_day_no %= 1461;

		if ($g_day_no >= 366){
			$leap = false;
			$g_day_no -= 1;
			$gy += $this -> div($g_day_no, 365);
			$g_day_no = $g_day_no % 365;
		}

		for ($i = 0; $g_day_no >= $this -> gregor['days_in_month'][$i] + ($i == 1 && $leap); $i++){
			$g_day_no -= $this -> gregor['days_in_month'][$i] + ($i == 1 && $leap);
		}
		$gm = $i+1;
		$gd = $g_day_no+1;
		parent::setDate($gy, $gm, $gd);
		$this->gregor['year']		=	parent::format('Y');
		$this->gregor['month']		=	parent::format('m');
		$this->gregor['day']		=	parent::format('d');
		$this->gregor['timestamp']	=	parent::format('U');
		$this->gregorian_to_jalali();
		return array($this->gregor['year'], $this->gregor['month'], $this->gregor['day']);
	}

	public function get_hejri_week_day($year='',$month='',$day=''){
		return ($this -> get_j_dn($year,$month,$day) + 2) % 7 + 1;
	}

	public function get_gregor_week_day(){
		return ($this -> get_g_dn() + 5) % 7 + 1;
	}

	public function get_hejri_month_name(){
		$mno = intval($this -> hejri['month']);
		return $this -> hejri['month_name'][$mno];
	}

	public function get_ghamari_month_name(){
		$mno = intval($this -> ghamari['month']);
		return $this -> ghamari['month_name'][$mno];
	}

	public function get_gregor_month_name(){
		$mno = intval($this -> gregor['month']);
		return $this -> gregor['month_name'][$mno];
	}

	public function get_hejri_wd_name(){
		return $this -> hejri['week_name'][$this -> get_hejri_week_day()];
	}

	public function get_gregor_wd_name(){
		return $this -> gregor['week_name'][$this -> get_gregor_week_day()];
	}

	public function setDate($year,$month,$day){
		if ($year > 1970){
			parent::setDate($year, $month, $day);
			$this->gregor['year']		=	parent::format('Y');
			$this->gregor['month']		=	parent::format('m');
			$this->gregor['day']		=	parent::format('d');
			$this->gregor['timestamp']	=	parent::format('U');
			$this->gregorian_to_jalali($year, $month, $day);
		}else {
			$this->set_hejri_date($year,$month,$day);
// 			$this->jalali_to_gregorian($year,$month,$day);
		}
	}

	public function setFullDate($year,$month = NULL,$day = NULL,$clnType = NULL,$hour = 0,$minute = 0,$sec = 0){
		if (is_null($month)) $month = parent::format('m');
		if (is_null($day)) $day = parent::format('d');
		parent::setTime($hour, $minute,$sec);
		switch ($clnType){
			case 'ar':
				$this->set_ghamari_date($year, $month, $day);
			break;
			case 'en':
			case 'en_US':
				parent::setDate($year, $month, $day);
				$this->gregor['year']		=	parent::format('Y');
				$this->gregor['month']		=	parent::format('m');
				$this->gregor['day']		=	parent::format('d');
				$this->gregor['timestamp']	=	parent::format('U');
				$this->gregorian_to_jalali();
				$this->gregorian_to_ghamari();
			break;
			default:
				$this->set_hejri_date($year, $month, $day);
			break;
		}
	}

	public function get_hejri_ampm($hour){
		if ($hour < 11){ return 'am'; }
		elseif ($hour < 13){ return 'am'; }
		elseif ($hour < 17){ return 'pm'; }
		elseif ($hour < 20){ return 'pm'; }
		else { return 'pm'; }
	}

	public function get_gregor_ampm($hour){
		if ($hour < 12){ return 'am'; }
		else { return 'pm'; }
	}

	public function date($code = '',$lang = NULL,$ts = ''){
		if (!is_int($ts)) $ts = intval($ts);
		if (is_null($lang)) $lang = $this->defLang;
		if (!empty($ts) && $ts < 0) $ts = hTime2gTime($ts);
		if (!empty($ts)){ $this -> gregor['timestamp'] = $ts; }
		$this -> set_date_from_ts($this -> gregor['timestamp']);
		if (empty($code)){
			return 'Object Method';
		}else {
			$cds = array('d','D','j','l','N','S','w','z','W',
					'F','m','M','n','t',
					'L','o','Y','y',
					'a','A','B','g','G','h','H','i','s','u',
					'e','I','O','P','T','Z',
					'c','r','U');
			switch ($lang){
				case 'en':
				case 'en_US':
				case 'en_GB':
					$rep = array();
					$rep[] = parent::format('d');
					$rep[] = parent::format('D');
					$rep[] = parent::format('j');
					$rep[] = parent::format('l');
					$rep[] = parent::format('N');
					$rep[] = parent::format('S');
					$rep[] = parent::format('w');
					$rep[] = parent::format('z');
					$rep[] = parent::format('W');
					$rep[] = parent::format('F');
					$rep[] = parent::format('m');
					$rep[] = parent::format('M');
					$rep[] = parent::format('n');
					$rep[] = parent::format('t');
					$rep[] = parent::format('L');
					$rep[] = parent::format('o');
					$rep[] = parent::format('Y');
					$rep[] = parent::format('y');
					$rep[] = parent::format('a');
					$rep[] = parent::format('A');
					$rep[] = parent::format('B');
					$rep[] = parent::format('g');
					$rep[] = parent::format('G');
					$rep[] = parent::format('h');
					$rep[] = parent::format('H');
					$rep[] = parent::format('i');
					$rep[] = parent::format('s');
					$rep[] = parent::format('u');
					$rep[] = parent::format('e');
					$rep[] = parent::format('I');
					$rep[] = parent::format('O');
					$rep[] = parent::format('P');
					$rep[] = parent::format('T');
					$rep[] = parent::format('Z');
					$rep[] = parent::format('c');
					$rep[] = parent::format('r');
					$rep[] = parent::format('U');
					break;
				case 'ar':
					if (intval($this->ghamari['day']) < 10) $this->ghamari['day'] = '0'.intval($this->ghamari['day']);
					if (intval($this->ghamari['month']) < 10) $this->ghamari['month'] = '0'.intval($this->ghamari['month']);
					$rep = array();
					$rep[] = $this->ghamari['day'];
					$rep[] = $this -> get_hejri_wd_name();
					$rep[] = intval($this->ghamari['day']);
					$rep[] = $this->get_hejri_wd_name();
					$rep[] = $this->get_hejri_week_day();
					$rep[] = ' ';
					$rep[] = $this->get_hejri_week_day()-1;
					$rep[] = parent::format('z');
					$rep[] = parent::format('W');
					$rep[] = $this->get_ghamari_month_name();
					$rep[] = $this->ghamari['month'];
					$rep[] = $this->get_ghamari_month_name();
					$rep[] = intval($this->ghamari['month']);
					$rep[] = $this->ghamari['days_in_month'][$this->ghamari['month']-1];
					$this -> is_kabise($this->ghamari['year'])?$rep[] = '1': $rep[] = '0';
					$rep[] = $this->ghamari['year'];
					$rep[] = $this->ghamari['year'];
					$rep[] = substr($this->ghamari['year'], 2,2);
					$rep[] = $this->get_hejri_ampm(parent::format('H'));
					$rep[] = $this->get_hejri_ampm(parent::format('H'));
					$rep[] = ' ';
					$rep[] = parent::format('g');
					$rep[] = parent::format('G');
					$rep[] = parent::format('h');
					$rep[] = parent::format('H');
					$rep[] = parent::format('i');
					$rep[] = parent::format('s');
					$rep[] = parent::format('u');
					$rep[] = parent::format('e');
					$rep[] = parent::format('I');
					$rep[] = parent::format('O');
					$rep[] = parent::format('P');
					$rep[] = parent::format('T');
					$rep[] = parent::format('Z');
					$rep[] = "{$this->ghamari['year']}-{$this->ghamari['month']}-{$this->ghamari['day']}T".parent::format('H').":".parent::format('i').":".parent::format('s').$this->timeZoneText; //2016-07-02T18:00:00+04:30
					$rep[] = parent::format('r');
					$rep[] = parent::format('U');
				break;
				default:
					if (intval($this -> hejri['day']) < 10) $this -> hejri['day'] = '0'.intval($this -> hejri['day']);
					if (intval($this -> hejri['month']) < 10) $this -> hejri['month'] = '0'.intval($this -> hejri['month']);
					$rep = array();
					$rep[] = $this -> hejri['day'];
					$rep[] = $this -> get_hejri_wd_name();
					$rep[] = intval($this -> hejri['day']);
					$rep[] = $this -> get_hejri_wd_name();
					$rep[] = $this -> get_hejri_week_day();
					$rep[] = ' ';
					$rep[] = $this -> get_hejri_week_day()-1;
					$rep[] = $this -> get_j_dn();
					$rep[] = ' ';
					$rep[] = $this -> get_hejri_month_name();
					$rep[] = $this -> hejri['month'];
					$rep[] = $this -> get_hejri_month_name();
					$rep[] = intval($this -> hejri['month']);
					$rep[] = $this -> hejri['days_in_month'][$this -> hejri['month']-1];
					$this -> is_kabise($this -> hejri['year'])?$rep[] = '1': $rep[] = '0';
					$rep[] = $this -> hejri['year'];
					$rep[] = $this -> hejri['year'];
					$rep[] = substr($this -> hejri['year'], 2,2);
					$rep[] = $this -> get_hejri_ampm(parent::format('H'));
					$rep[] = $this -> get_hejri_ampm(parent::format('H'));
					$rep[] = ' ';
					$rep[] = parent::format('g');
					$rep[] = parent::format('G');
					$rep[] = parent::format('h');
					$rep[] = parent::format('H');
					$rep[] = parent::format('i');
					$rep[] = parent::format('s');
					$rep[] = parent::format('u');
					$rep[] = parent::format('e');
					$rep[] = parent::format('I');
					$rep[] = parent::format('O');
					$rep[] = parent::format('P');
					$rep[] = parent::format('T');
					$rep[] = parent::format('Z');
					$rep[] = "{$this->hejri['year']}-{$this->hejri['month']}-{$this->hejri['day']}T".parent::format('H').":".parent::format('i').":".parent::format('s').$this->timeZoneText; //2016-07-02T18:00:00+04:30
					$rep[] = parent::format('r');
					$rep[] = parent::format('U');
				break;
			}
			$tmpc = '';
			for ($_i=0;$_i<strlen($code);$_i++){
				$tmpc .= '_\_\_'.$code[$_i];
			}foreach ($cds as &$val) $val = '_\_\_'.$val;
			$return = str_replace($cds,$rep,$tmpc);
			return str_replace('_\_\_','',$return);
		}
	}

	public static function staticDate($code = '',$lang = '',$ts = ''){
		$d = new lib_JalaliDate();
		return $d->date($code,$lang,$ts);
	}

	public function __destruct(){
		unset($this -> gregor);
		unset($this -> hejri);
	}
}

function is_kabise($year){
	if($year%4==0 && $year%100!=0){
		return true;
	}
	return false;
}
function hTime2gTime($time){
	if ($time < 0 or date('Y',$time) < 1970){
		$time = ($time+19601136000);
		if (is_kabise(date('Y',$time)))	$time = $time+86400;
		//	$dobj = new hml_DateTime();
		//	$dobj -> setTimezone($time);
		//	$kiri = $dobj -> gregor['day_in_month'][$dobj -> format('m')]*86400;
		//	$time += mktime(date('H',$time),date('i',$time),date('s',$time),date('m',$time)+1);
	}
	return $time;
}
function hst_str2ts($var1 = '', $var2 = ''){
	if(!$time = @strtotime($var1,$var2)) file_put_contents('mlog.txt',"$var1 |-| $var2");
	$time = @strtotime($var1,$var2);
	if ($time > 0){
		return $time;
	}else {
		$tk = explode(' ', $var1);
		$date = explode('-', $tk[0]);
		$time = explode(':', $tk[1]);
		$dobj = new lib_JalaliDate();
		/*	$y = substr($var1,0,4);
		$m = substr($var1,5,2);
		$d = substr($var1,8,2);
		if (strlen($var1) > 10){
		$h = substr($var1,11,2);
		$i = substr($var1,14,2);
		$s = substr($var1,18,2);
		}*/
		$dobj -> set_hejri_date($date[0], $date[1], $date[2]);
		$time = mktime($time[0],$time[1],$time[2],$dobj -> date('m','en_US'),$dobj -> date('d','en_US'),$dobj -> date('Y','en_US'));
		//return "$y$m$d$h$i$s";
		//	$dobj -> setTime($time[0],$time[1],$time[2]);
		//	echo $time;
		return $time;
	}
}
