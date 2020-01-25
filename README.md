# Laravel Captcha
Laravel Captcha Generator

## Requirements
- PHP 5.6+
- Laravel Framework 5.2+
- GD Library enabled

## Installation
- Run
<pre><code>composer require zerosdev/laravel-captcha:dev-master</code></pre>

- Add this code to your **config/app.php** in providers array
<pre><code>ZerosDev\LaravelCaptcha\ServiceProvider::class,</code></pre>

- Add this code to your **config/app.php** in aliases array
<pre><code>'Captcha' => ZerosDev\LaravelCaptcha\Facade::class,</code></pre>

- Run
<pre><code>composer dump-autoload</code></pre>

- Run
<pre><code>php artisan vendor:publish --provider="ZerosDev\LaravelCaptcha\ServiceProvider"</code></pre>

## Configuration
Open your **config/captcha.php** and adjust the setting. Optionally, you can adjust some captcha setting on-the-fly with available methods below:

#### chars(String)
Set the characters that will be used as captcha text

#### size(Integer width, Integer height)
Set the captcha image width and height in pixel (px)

#### length(Integer)
Set the captcha character length

## Generation Example
<pre><code>
// Import from root class (Captcha Facade)
use Captcha;

// generate captcha
$captcha = Captcha::chars('123456789ABCDEFGHIJKLMNPQRSTUVWXYZ')->length(4)->size(120, 40)->generate();

// get captcha id
$id = $captcha->id();
// return random generation id

// print html hidden form field, used in blade template
{{ $captcha->form_field() }}
// return: &lt;input type="hidden" name="captcha_id" value="XXXXXXXXXXXXXX" id="captcha_id"&gt;

// get base64 image
$image = $captcha->image();
// return data:image/png; base64,XXXXXXXXXXXXXX

// print html image, used in blade template
{{ $captcha->html_image(['onclick' => 'jsFunction()', 'style' => 'border:1px solid #ddd']) }}
// return: &lt;img src="data:image/png; base64,XXXXXXXXXXXXXX" onclick="jsFunction()" style="border:1px solid #ddd" /&gt;
</code></pre>

## Validation
<pre><code>
// Import from root class (Captcha Facade)
use Captcha;

// validate captcha
// $captchaId = Captcha generation id, $captcha->id()
// $captchaText = Captcha input from client request

if( Captcha::validate($captchaId, $captchaText) )
{
    // Valid
}
else
{
    // Invalid
}
</code></pre>

## Advise
It is recommended to avoid using the "0" (zero) and "O" characters in captcha to avoid being ambiguous
