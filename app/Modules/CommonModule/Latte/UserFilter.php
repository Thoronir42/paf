<?php declare(strict_types=1);

namespace PAF\Modules\CommonModule\Latte;

use Nette\InvalidArgumentException;
use PAF\Common\Latte\BaseFilter;
use PAF\Modules\CommonModule\Model\User;

class UserFilter extends BaseFilter
{

    public function useFilter(...$args): string
    {
        $user = $args[0] ?? null;

        if ($user === null) {
            return '#!@$';
        }

        if (!$user instanceof User) {
            throw new InvalidArgumentException(
                sprintf("First argument expected to be instance of %s, got %s", User::class, get_class($args[0]))
            );
        }

        // todo: polish resulting string
        return $user->type . ':' . $user->username;
    }
}
