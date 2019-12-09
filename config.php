<?php

/**
*
*	Captcha configuration file
*
**/

return [

	/**
	*	Captcha character set
	*
	**/
	'chars'		=> '123456789abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ',

	/**
	*	Captcha length
	*
	**/
	'length'	=> 6,

	/**
	*	Captcha image width in pixel (px)
	*
	**/
	'width'		=> 170,

	/**
	*	Captcha image height in pixel (px)
	*
	**/
	'height'	=> 50,

	/**
	*	Captcha text font
	*
	*	Run "php artisan captcha:fonts" to see
	*	all available fonts. This package include
	*	Arimo-Bold.ttf as default font
	*
	**/
	'font'		=> 'Arimo-Bold.ttf',

	/**
	*	Captcha session name
	*
	**/
	'session_name'		=> 'captcha',

];