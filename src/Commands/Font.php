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
			return $this->error("Directory {$fontPath} is not exists or is not readable!");
		}

		if( empty($import) )
		{
			if( ($od = opendir($fontPath)) === false ) {
				return $this->error("Failed to open directory: {$fontPath}");
			}

			$files = [];

			while( false !== ($file = readdir($od)) )
			{
				$originalName = $file;
				$loweredName = strtolower($file);
				$ext = pathinfo($loweredName, PATHINFO_EXTENSION);

				if( in_array($ext, $validExt) )
				{
					$files[] = $originalName;
				}
			}

			$i = 1;
			foreach( $files as $file ) {
				$this->line("{$i}. ".$file);
				$i++;
			}
		}
		else
		{
			if( !filter_var($import, FILTER_VALIDATE_URL) )
			{
				if( !file_exists($import) ) {
					return $this->error("{$import} is not exists or is not readable!");
				}

				if( !is_file($import) ) {
					return $this->error("{$import} is not valid file or is not readable!");
				}
			}

			$ext = pathinfo(strtolower($import), PATHINFO_EXTENSION);

			if( !in_array($ext, $validExt) ) {
				return $this->error("Unsupported font format. Please use one of supported font format: " . implode(", ", $validExt));
			}

			$fileName = basename($import);

			if( false !== ($f = file_get_contents($import)) )
			{
				@file_put_contents($fontPath.'/'.$fileName, $f);

				if( !file_exists($fontPath.'/'.$fileName) || !is_file($fontPath.'/'.$fileName) ) {
					return $this->error("Failed to import font");
				}
			}
			else
			{
				return $this->error("Failed to import font");
			}

			return $this->info("Import font success!");
		}
	}
}