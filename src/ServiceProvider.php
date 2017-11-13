<?php

namespace Suite\Amiba;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

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
			$this->registerMigrations();

			$publishes = config('gmf.publishes', 'gmf');
			$this->publishes([
				__DIR__ . '/../resources/assets/js' => base_path('resources/assets/js/vendor/suite-amiba'),
			], $publishes);

			$this->publishes([
				__DIR__ . '/../resources/assets/sass' => base_path('resources/assets/sass/vendor/suite-amiba'),
			], $publishes);

			$this->publishes([
				__DIR__ . '/../resources/assets/img' => base_path('public/img'),
				__DIR__ . '/../resources/assets/files' => base_path('public/files'),
			], $publishes);

			$this->publishes([
				__DIR__ . '/../database/seeds' => base_path('database/seeds'),
				__DIR__ . '/../database/preseeds' => base_path('database/preseeds'),
				__DIR__ . '/../database/postseeds' => base_path('database/postseeds'),
			], $publishes);

			$this->publishes([
				__DIR__ . '/../database/sqls' => base_path('database/sqls'),
				__DIR__ . '/../database/presqls' => base_path('database/presqls'),
				__DIR__ . '/../database/postsqls' => base_path('database/postsqls'),
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
	/**
	 * Register Passport's migration files.
	 *
	 * @return void
	 */
	protected function registerMigrations() {
		$this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

	}
}
