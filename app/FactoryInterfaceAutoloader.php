<?php

namespace App;

class FactoryInterfaceAutoloader {

    public function register() {
        spl_autoload_register([$this, 'load']);
    }

    public function load($fullyQualifiedName) {
        $parts = explode('\\', $fullyQualifiedName);

        $className = array_pop($parts);
        if($className[0] === 'I') {
            // strips 'I' from beginning and 'Factory' from end of class, which is
            // a naming convention for interface factories
            $parts[] = substr($className, 1, -7);

            return class_exists(implode($parts, '\\'));
        }

        return false;
    }
}
