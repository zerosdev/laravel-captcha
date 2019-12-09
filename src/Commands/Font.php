<?php

namespace ZerosDev\LaravelCaptcha\Commands;

use Illuminate\Support\Facades\Log;
use Illuminate\Console\Command;

class Font extends Command
{
	/**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'captcha:font {--import=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import font from path or url';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
	public function handle()
	{
		$fontPath = dirname(__DIR__, 2).'/storage/fonts';
		$import = $this->option('import');

		$validExt = ['ttf', 'woff', 'woff2', 'eot'];

		if( !file_exists($fontPath) || !is_dir($fontPath) ) {
			return "Directory {$fontPath} is not exists or is not readable!";
		}

		if( empty($import) )
		{
			if( ($od = opendir($fontPath)) === false ) {
				return "Failed to open directory: {$fontPath}";
			}

			$files = [];

			while( false !== ($file = readdir($od)) )
			{
				$originalName = $file;
				$loweredName = strtolower($file);

				if( in_array(substr($loweredName, -3), $validExt) )
				{
					$files[] = $originalName;
				}
			}

			return implode("\n", $files);
		}
		else
		{
			if( !in_array(substr($import, -3), $validExt) ) {
				return "Unsupported font format. Please use one of supported font format: " . implode(", ", $validExt);
			}

			$fileName = basename($import);

			if( false !== ($f = file_get_contents($import)) )
			{
				@file_put_contents($fontPath.'/'.$fileName, $f);

				if( !file_exists($fontPath.'/'.$fileName) || !is_file($fontPath.'/'.$fileName) ) {
					return "Failed to import font";
				}
			}
			else
			{
				return "Failed to import font";
			}

			return "Import font success!";
		}
	}
}