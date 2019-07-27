<?php declare(strict_types=1);

namespace PAF;

class FactoryInterfaceAutoloader
{

    public function register()
    {
        spl_autoload_register([$this, 'load']);
    }

    public function load($fullyQualifiedName)
    {
        $parts = explode('\\', $fullyQualifiedName);

        $className = array_pop($parts);
        if ($className && $className[0] === 'I') {
            // strips 'I' from beginning and 'Factory' from end of class, which is
            // a naming convention for interface factories
            $parts[] = substr($className, 1, -7);
            $controlClassName = implode($parts, '\\');

            return class_exists($controlClassName);
        }

        return false;
    }
}
