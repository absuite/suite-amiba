<?php

namespace Suite\Amiba;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Packager;

class ServiceProvider extends BaseServiceProvider {
	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot() {
		$this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
		$this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

		if ($this->app->runningInConsole()) {
			Packager::loadDatabasesFrom(__DIR__ . '/../database/');

			$publishes = config('gmf.publishes', 'gmf');
			$this->publishes([
				__DIR__ . '/../resources/assets/js' => base_path('resources/assets/js/vendor/suite-amiba'),
				__DIR__ . '/../resources/assets/sass' => base_path('resources/assets/sass/vendor/suite-amiba'),
			], $publishes);

			$this->publishes([
				__DIR__ . '/../resources/public' => public_path('assets/vendor/suite-amiba'),
			], $publishes);
		}
	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register() {

	}
}
