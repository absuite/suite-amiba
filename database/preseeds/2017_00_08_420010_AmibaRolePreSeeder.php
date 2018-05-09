<?php

use Gmf\Sys\Builder;
use Gmf\Sys\Models\Authority\Role;
use Gmf\Sys\Models\Authority\RoleMenu;
use Gmf\Sys\Models\Authority\RoleUser;
use Gmf\Sys\Models\Menu;
use Illuminate\Database\Seeder;

class AmibaRolePreSeeder extends Seeder {
	private $role = 'suite.role.cbo.amiba';
	public $entId = '';
	private $menuTag = 'e1742e00420e11e79d1897dbf903cb38';
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function run() {
		if (empty($this->entId)) {
			$this->entId = config('gmf.ent.id');
		}
		if (empty($this->entId)) {
			return;
		}
		Role::build(function (Builder $b) {
			$b->ent_id($this->entId)->code($this->role)->name('阿米巴经营管理员');
		});
		if ($user = config('gmf.user.model')::findByAccount(config('gmf.admin.account'), 'sys')) {
			RoleUser::build(function (Builder $b) use ($user) {
				$b->ent_id($this->entId)->user_id($user->id)->role($this->role);
			});
		}

		$menus = Menu::where('is_leaf', '1')
			->where('code', 'like', 'amiba%')
			->where('tag', $this->menuTag)
			->get();
		if ($menus && count($menus)) {
			foreach ($menus as $m) {
				RoleMenu::build(function (Builder $b) use ($m) {
					$b->ent_id($this->entId)->menu_id($m->id)->role($this->role)->opinion_enum('permit');
				});
			}
		}
	}
}
