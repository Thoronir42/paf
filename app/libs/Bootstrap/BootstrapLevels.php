<?php

namespace App\Libs\Bootstrap;


abstract class BootstrapLevels
{
	const
		LEVEL_DEFAULT = 'default',
		LEVEL_PRIMARY = 'primary',
		LEVEL_INFO = 'info',
		LEVEL_WARNING = 'warning',
		LEVEL_DANGER = 'danger';

	public static function getLevels(){
		return [
			self::LEVEL_DEFAULT => 'Default',
			self::LEVEL_PRIMARY => 'Primary',
			self::LEVEL_INFO => 'Info',
			self::LEVEL_WARNING => 'Warning',
			self::LEVEL_DANGER => 'Danger',
		];
	}
}
