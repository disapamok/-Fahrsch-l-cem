<?php 

/**
 *@author     (Schleier IT <email@domain.com>)
 *@time 12:51 , 15.08.19
 *@version     1.0.0 (description)
 * 
 * 
 * THIS file should contain
 * custom codes for translation not found in this project
 * 
 * THE Translation has been created to 
 * work almost like the 
 * laravel translation method
 * trans("string_name", ["first", "second"]);
 * https://laravel.com/docs/5.7/localization
 * :BUT
 * here we use arrays not object 
 * order of strings in "the array" is important.
 * so __1__ is replaced with first word in array,
 * __2__ second word and so on..
 * 
 * 	EXAMPLE
 *	if string_name is written in translationa array as
 *	german:
 *	string_name => "__1__ is the first word and __2__ is the second word"
 *	
 *	english:
 *	string_name => " __2__ is the second word and __1__ is the first word"
 *	
 *	the return translated string is
 *	german:
 *	first is the first word and second is the second word"
 *	
 *	english
 * " seconde is the second word and first is the first word"
 */

//use the function sch_TranslateJsWord($string) to translate in js


//file name for storing words not found in languages
$sch_trans_not_found_file = __DIR__."/translation_not_found.txt";
//try not to change this value from sch_website_language,
//else change in schleier/js/schleier.js file also 
$sch_language_cookie_name = "sch_website_language";
/* gets default language
 * this is the language used for admin notifications*/
$sch_default_language = "locale_de";

/**
 *Merge all default and user defined
 * language strings together
 */
$sch_en_languages_strings = array_merge($sch_default_en_languages_strings, $sch_user_defined_en_languages_strings);
$sch_de_languages_strings = array_merge($sch_default_de_languages_strings, $sch_user_defined_de_languages_strings);

/**
 * gets current language
 *language change is done by js in extensions/assets/extensions.js file
 * @return     string  ( description_of_the_return_value )
 */
function sch_getCurrentLang(){
	global $sch_language_cookie_name;
	$sch_default_language = sch_getDefaultLang();

	$lang = isset($_COOKIE[$sch_language_cookie_name])?$_COOKIE[$sch_language_cookie_name]: $sch_default_language;
	return escape($lang);//de = deutsch | en = englisch
}
/**
 * gets default language
 * this is the language used for admin notifications
 * @return     string  ( description_of_the_return_value )
 */
function sch_getDefaultLang(){
	global $sch_default_language;
	return $sch_default_language;//de = deutsch | en = englisch
}

function sch_getLangHtmlShortCode(){
	return str_replace("locale_", "", sch_getCurrentLang());
}


/**
 * returns all supported languages
 * as shortcode
 * pls use prefix "locale_" before name
 */
function sch_getSupportedLanguages(){
	return ["locale_en", "locale_de"];
}

/**
 * translates a string
 * this function always trims the parametr and returned string
 *
 * @param      <type>  $string  The string
 *
 * @return     string  ( description_of_the_return_value )
 */
function sch_translate($string="", $replacement_arr = []){
	$language_to_use = sch_getTranslationArray();
	return sch_convertWord($language_to_use, $string, $replacement_arr);
}
/**
 * translates a string using default language 
 * this function uses DEFAULT LANGUAGE not USER LANGUAGE
 * this function always trims the parametr and returned string
 *
 * @param      <type>  $string  The string
 *
 * @return     string  ( description_of_the_return_value )
 */
function sch_translate_default($string="", $replacement_arr = []){
	$language_to_use = sch_getTranslationArray(true);
	return sch_convertWord($language_to_use, $string, $replacement_arr);
}
function sch_trans($string="", $replacement_arr = []){
	$language_to_use = sch_getTranslationArray(true);
	return sch_convertWord($language_to_use, $string, $replacement_arr);
}
/**
 * translates a string using default language 
 * this function uses DEFAULT LANGUAGE not USER LANGUAGE
 * this function always trims the parametr and returned string
 *
 * @param      <type>  $string  The string
 *
 * @return     string  ( description_of_the_return_value )
 */
function sch_translate_notification($string="", $replacement_arr = []){
	$language_to_use = sch_getTranslationArray(true);
	return sch_convertWord($language_to_use, $string, $replacement_arr);
}

/**
 * convert shortcode to word or sentences
 * do not call this function directly\
 * use sch_translate, sch_translate_notification, or sch_translate_default
 *
 * @param      <type>          $language_to_use  The language to use
 * @param      string          $string           The string
 * @param      array           $replacement_arr  The replacement arr
 *
 * @return     boolean|string  ( description_of_the_return_value )
 */
function sch_convertWord($language_to_use, $string="", $replacement_arr = []){
	if(empty($string))return $string;
	global $sch_trans_not_found_file;
	// $language_to_use = sch_getTranslationArray();

	$string = trim($string);
	$string_ = sch_gettranslationShortcode($string);

	//insert words that are not found into file
	if(!isset($language_to_use["$string"]) && !isset($language_to_use["$string_"])){
		//check if already saved
		$strings_saved = file_get_contents($sch_trans_not_found_file);
		if(!strstr($strings_saved, '"'.$string_.'" => "')){
			$string_tow = '"'.$string_.'" => "'.$string.'",'." \n";
			fwrite(fopen($sch_trans_not_found_file, "a+"), $string_tow);			
		}
		return $string;
	}
	//check if word is available, else check, formatted word availability
	$translated_key = isset($language_to_use["$string_"])?$language_to_use["$string_"]:$string;
	$translated_key = isset($language_to_use["$string"])?$language_to_use["$string"]:$translated_key;
	for ($i=0; $i < count($replacement_arr); $i++) { 
		$translated_key = str_replace("__".($i+1)."__", $replacement_arr[$i], $translated_key);
	}

	//constants
	$translated_key = str_replace("__APP_NAME__", env("APP_NAME"), $translated_key);

	return $translated_key;
}

function sch_gettranslationShortcode($string){
	$string_ = trim($string);
	// $string_ = str_replace("ü", "u", $string_);
	// $string_ = str_replace("ä", "a", $string_);
	// $string_ = str_replace("ö", "o", $string_);
	// $string_ = str_replace("Ü", "u", $string);
	// $string_ = str_replace("Ä", "a", $string_);
	// $string_ = str_replace("Ö", "o", $string_);
	// $string_ = str_replace("ß", "ss", $string_);
	$string_ = str_replace(" ", "_", strtolower($string_));
	$string_ = str_replace("-", "_", $string_);
	$string_ = str_replace("'", "", $string_);
	$string_ = str_replace(" / ", "_slash_", $string_);
	$string_ = str_replace("/", "_slash_", $string_);
	$string_ = str_replace(",", "", $string_);
	$string_ = str_replace("!", "", $string_);
	$string_ = str_replace("&", "8nd", $string_);
	$string_ = str_replace(".", "", $string_);
	$string_ = str_replace("(", "", $string_);
	$string_ = str_replace(")", "", $string_);
	$string_ = str_replace("?", "", $string_);
	return $string_;
}

function trans($string){
	fwrite(fopen($sch_trans_not_found_file.".txt","a+"), "trans fn called");
	return sch_translate($string);
}


//returns the current array of strings for 
//the translation
function sch_getTranslationArray($use_default = false){
	global $sch_de_languages_strings;
	global $sch_en_languages_strings;
	global $sch_trans_not_found_file;

	$language_to_use = [];
	$current_lang = $use_default?sch_getDefaultLang():sch_getCurrentLang();

	//website language
	switch ($current_lang) {
		case "locale_en":	
			$language_to_use = $sch_en_languages_strings;
			break;	
			//fallback language == german
		case "locale_de":
		default:
			$language_to_use = $sch_de_languages_strings;
			break;
	}

	return $language_to_use;
}

 ?>