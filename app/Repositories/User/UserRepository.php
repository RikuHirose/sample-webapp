<?php

namespace App\Repositories\User;

Use App\Models\User;
use App\Repositories\Base\BaseRepository;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
		public function getBlankModel()
		{
			return new User();
		}
}
