<?php

namespace ZerosDev\LaravelCaptcha;

use Exception;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\HtmlString;

class Captcha
{
	/**
	*	captcha id
	**/
	protected $id = null;

	/**
	*	image buffer data
	**/
	protected $bufferData = null;

	/**
	*	latest captcha session key
	**/
	protected $lastSessionKey = '';

	/**
	*	default captcha image width (px)
	**/
	protected $width;

	/**
	*	default captcha image height (px)
	**/
	protected $height;

	/**
	*	captcha characters
	**/
	protected $chars;

	/**
	*	default captcha length
	*/
	protected $captchaLength;

	/**
	*	captcha session name
	*/
	protected $sessionName;

	/**
	*	font name
	**/
	protected $font;

	/**
	*	
	* Initializing captcha
	*	
	* @return void
	*
	**/

	public function __construct()
	{
		$this->width = intval(config('captcha.width', 170));
		$this->height = intval(config('captcha.height', 50));
		$this->chars = config('captcha.chars', '123456789abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ');
		$this->captchaLength = intval(config('captcha.length', 6));
		$this->font = config('captcha.font', 'Arimo-Bold.ttf');
		$this->sessionName = config('captcha.session_name', 'captcha');

		$this->fontPath = dirname(__DIR__).'/storage/fonts/'.$this->font;

		# Check if font is exists
		if( !file_exists($this->fontPath) || !is_file($this->fontPath) ) {
			throw new Exception("Font {$this->fontPath} is not exists or can not be accessed. Kindly check the font file or directory/file permission");
		}

		# check if captcha length > 0
		if( $this->captchaLength < 1 ) {
			throw new Exception("Captcha length must be greater than 0");
		}

		# Start session if not started
		if( session_status() !== PHP_SESSION_ACTIVE )
		{
			if( !headers_sent() )
			{
				session_start();
			}
			else
			{
				throw new Exception("PHP session can not be started. Header already sent!");
			}
		}
	}

	/**
	*	
	* Set the character list
	*
	* @param string of character $chars
	* @return \ZerosDev\LaravelCaptcha\Captcha
	*
	**/

	public function chars($chars)
	{
		$this->chars = $chars;
		return $this;
	}

	/**
	*	
	* Set the length of captcha code
	*
	* @param Int $length
	* @return \ZerosDev\LaravelCaptcha\Captcha
	*
	**/

	public function length($length)
	{
		$this->captchaLength = intval($length);
		if( $this->captchaLength <= 0 ) {
			throw new Exception("Captcha length must be greater than 0");
		}
		return $this;
	}

	/**
	*	
	* Set the size of captcha image
	*
	* @param integer $width
	* @param integer $height
	* @return \ZerosDev\LaravelCaptcha\Captcha
	*
	**/

	public function size($width, $height)
	{
		$this->width = intval($width);
		$this->height = intval($height);
		return $this;
	}

	/**
	*	
	* Generating captcha
	*	
	* @return \ZerosDev\LaravelCaptcha\Captcha
	*
	**/

	public function generate()
	{
		$image = imagecreatetruecolor($this->width, $this->height);
		$background_color = imagecolorallocate($image, 255, 255, 255);
		imagefilledrectangle($image, 0, 0, $this->width, $this->height, $background_color);
		$line_color = imagecolorallocate($image, 64, 64, 64);

		for($i=0; $i<10; $i++)
		{
			imageline($image, 0, rand()%$this->height, $this->width, rand()%$this->height, $line_color);
		}

		$pixel_color = imagecolorallocate($image, 0, 0, 255);

		for($i=0; $i<1000; $i++)
		{
			imagesetpixel($image, rand()%$this->width, rand()%$this->height, $pixel_color);
		}

		$len = strlen($this->chars);
		$text_color = imagecolorallocate($image, 0,0,0);
		$shadow_color = $grey = imagecolorallocate($image, 128, 128, 128);
		$word = '';
		$padding = 10;
		$spacing = (($this->width-$padding)/$this->captchaLength);

		for($i=0; $i<$this->captchaLength; $i++)
		{
			$angle = mt_rand(-4, 4) * mt_rand(1,4);
			$sizeStart = (($this->height/2)-3);
			$sizeEnd = (($this->height/2)+3);
			$font_size = mt_rand($sizeStart, $sizeEnd);
			$letter = $this->chars[mt_rand(0, $len-1)];
			$xCoordinate = $i == 0 ? $padding : ($i*$spacing)+$padding;
			$lineHeight = ($this->height/2) + ($font_size/2);

			imagettftext($image, $font_size, $angle, $xCoordinate, $lineHeight, $shadow_color, $this->font, $letter);
			imagettftext($image, $font_size, $angle, $xCoordinate, $lineHeight, $text_color, $this->font, $letter);
			$word .= $letter;
		}

		$word = str_replace(' ', '', $word);

		ob_start();
		imagepng($image);
		imagedestroy($image);

		$this->bufferData = ob_get_clean();
		$this->id = uniqid().time();
		$this->lastSessionKey = '_'.$this->id;
		$cd = Session::has($this->sessionName) ? json_decode(Session::get($this->sessionName), true) : [];

		if( count($cd) >= 20 )
		{
			Session::forget($this->sessionName);
			$cd = [];
		}

		$cd[$this->lastSessionKey] = $word;
		$sessionValue = json_encode($cd);
		Session::put($this->sessionName, $sessionValue);

		return $this;
	}

	/**
	*
	* Get captcha image
	*	
	* @return string of generated base64 image
	*
	**/

	public function image()
	{
		return !empty($this->bufferData) ? 'data:image/png;base64, ' . base64_encode($this->bufferData) : null;
	}

	/**
	*	
	* Get captcha id
	*	
	* @return string of captcha generation id
	*
	**/

	public function id()
	{
		return !empty($this->id) ? $this->id : null;
	}
	
	/**
	*	
	* Generate html hidden input
	*	
	* @param Captcha ID $id
	* 
	* @return html
	*
	**/
	
	public function form_field($captchaId = null, $elementId = 'captcha_id')
	{
		return HtmlString('<input type="hidden" id="'.$elementId.'" name="captcha_id" value="'.($captchaId ? $captchaId : $this->id).'">');
	}
	
	/**
	*	
	* Creating html tag of captcha image
	*	
	* @param HTML Attributes (array) $attributes
	* 
	* @return html
	*
	**/
	
	public function html_image($attributes = [])
	{
		$html = '<img src="'.$this->image().'" ';
		
		foreach($attributes as $name => $value)
		{
		    $html .= $name.'="'.$value.'" ';
		}
		
		$html .= '/>';

		return HtmlString($html);
	}

	/**
	*	
	* Validating captcha
	*	
	* @param Captcha ID $id
	* @param Captcha Code $captcha
	* @return boolean
	*
	**/

	public function validate($id, $captcha)
	{
		$cd = Session::has($this->sessionName) ? Session::get($this->sessionName) : null;
		if( !empty($cd) )
		{
			$list = json_decode($cd, true);
			$key = '_'.$id;
			if( isset($list[$key]) )
			{
				$result = hash_equals($list[$key], $captcha) ? true : false;
				
				if( $result )
				{
				    unset($list[$key]);
				    $sessionValue = json_encode($list);
			        Session::put($this->sessionName, $sessionValue);
				}
				
				return $result;
			}
		}

		return false;
	}
}