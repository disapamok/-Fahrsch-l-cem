
 constants = 
 __APP_NAME__ = website name in env(APP_NAME)

 * THE Translation has been created to 
 * work almost like the 
 * laravel translation method
 * trans("string_name", ["first", "second"]);
 * https://laravel.com/docs/5.7/localization
 * :BUT
 * here we use arrays not object 
 (array starts from 1 here :) )_
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
 
 * 3. do not edit any code found in the folder "defaults" please
 * 	-to edit lang in "default" use array in strings, with the same 
 * shortcode

 *e.g if you add
	"app_author" => "APP TEST",
	in array in strings/en will change 
	the value for app_name for english language 
	to APP TEST everywhere
 *e.g 2 if you add
	"app_author" => "APP PROBE",
	in array in strings/de will change 
	the value for app_name for german language 
	to APP PROBE everywhere
 * 



 WARNING:: if you use google translate it adds space between the __ and the digit,
 you can replace all using this regex
__ ([0-9]) ==> __$1
([0-9])__ ==> $1__