<?php declare(strict_types=1);

namespace PAF\Common\Security;

use LeanMapper\Reflection\AnnotationsParser;
use Nette\Application\UI\MethodReflection;
use Nette\Security\IAuthorizator;
use Nette\Security\User;

final class ReflectionAuthorizator
{
    /** @var User */
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function checkMethod(MethodReflection $ref)
    {
        $annotations = (array)AnnotationsParser::parseAnnotationValues('authorize', $ref->getDocComment());

        foreach ($annotations as $annotation) {
            [$resource, $privilege] = explode(' ', $annotation) + [IAuthorizator::ALL, IAuthorizator::ALL];
            $result = $this->check($resource, $privilege);
            if ($result) {
                return $result;
            }

        }

        return new AuthorizationResult(AuthorizationResult::STATUS_OK);
    }

    private function check(string $resource, string $privilege = IAuthorizator::ALL): ?AuthorizationResult
    {
        if (!$this->user->isAllowed($resource, $privilege)) {
            $args = ['resource' => $resource];
            if ($privilege) {
                $args['privilege'] = $privilege;
                $message = 'auth.resource-privilege-unauthorized';
            } else {
                $message = 'auth.resource-unauthorized';
            }

            return new AuthorizationResult(AuthorizationResult::STATUS_FORBIDDEN, $message, $args);
        }

        return null;
    }
}
