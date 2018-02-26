<?php
/*
Copyright 2016-2018 Daniil Gentili
(https://daniil.it)
This file is part of MagicalSerializer.
MagicalSerializer is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
MagicalSerializer is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
See the GNU Affero General Public License for more details.
You should have received a copy of the GNU General Public License along with the MagicalSerializer.
If not, see <http://www.gnu.org/licenses/>.
*/

namespace danog;

class Serialization
{
    public static $extracted = [];

    public static function unserialize($data)
    {
        foreach (get_declared_classes() as $class) {
            if (isset(class_uses($class)['danog\Serializable'])) {
                $namelength = strlen($class);
                if (strpos($data, 'O:'.$namelength.':"'.$class.'":') === false) {
                    continue;
                }
                $data = explode('O:'.$namelength.':"'.$class.'":', $data);
                $stringdata = array_shift($data);
                foreach ($data as $chunk) {
                    list($attributecount, $value) = explode(':{', $chunk, 2);
                    $attributecount++;
                    $stringdata .= 'O:17:"danog\PlaceHolder":'.$attributecount.':{s:21:"originalclassnamepony";s:'.$namelength.':"'.$class.'";'.$value;
                }
                $data = $stringdata;
            }
        }
        self::$extracted = [];
        $data = self::extractponyobject(unserialize($data));
        self::$extracted = [];

        return $data;
    }

    public static function extractponyobject($orig)
    {
        if (isset($orig->realactualponyobject)) {
            return self::extractponyobject($orig->realactualponyobject);
        }
        if (is_array($orig) || $orig instanceof \Volatile) {
            foreach ($orig as $key => $value) {
                $orig[$key] = self::extractponyobject($value);
            }

            return $orig;
        }
        if (is_object($orig) && !isset(self::$extracted[$hash = spl_object_hash($orig)])) {
            self::$extracted[$hash] = true;
            foreach ($orig as $key => $value) {
                $orig->{$key} = self::extractponyobject($value);
            }
        }

        return $orig;
    }

    public static function serialize($object, $not_compatible = false)
    {
        self::$extracted = [];
        $object = serialize(self::createserializableobject($object));
        self::$extracted = [];
        if ($not_compatible === true) {
            return $object;
        }
        $object = explode('O:17:"danog\PlaceHolder":', $object);
        $newobject = array_shift($object);
        foreach ($object as $chunk) {
            list($attributecount, $value) = explode(':{', $chunk, 2);
            $attributecount--;
            list($pre, $value) = explode('s:21:"originalclassnamepony";s:', $value, 2);
            list($length, $value) = explode(':', $value, 2);
            $classname = substr($value, 1, $length);
            $value = $pre.substr($value, $length + 3);
            $newobject .= 'O:'.strlen($classname).':"'.$classname.'":'.$attributecount.':{'.$value;
        }

        return $newobject;
    }

    public static function createserializableobject($orig)
    {
        if (is_object($orig) && $orig instanceof \danog\MadelineProto\VoIP) {
            $orig = false;
        }
        if (is_object($orig)) {
            if (isset(self::$extracted[$hash = spl_object_hash($orig)])) {
                return self::$extracted[$hash];
            }
            if (method_exists($orig, 'fetchserializableobject')) {
                return $orig->fetchserializableobject($hash);
            }
            self::$extracted[$hash] = $orig;
        }
        if (is_array($orig) || $orig instanceof \Volatile) {
            foreach ($orig as $key => $value) {
                $orig[$key] = self::createserializableobject($value);
            }
        }

        return $orig;
    }
}
