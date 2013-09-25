<?php


class VF_Singleton extends VF_AbstractSingleton
{

    /** @return VF_Singleton */
    static function getInstance($new = false) // test only
    {
        if (is_null(self::$instance) || $new) {
            self::$instance = new VF_Singleton;
        }
        return self::$instance;
    }
}