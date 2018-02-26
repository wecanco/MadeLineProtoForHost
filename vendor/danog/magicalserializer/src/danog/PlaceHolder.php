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

class PlaceHolder
{
    public function __construct($hash, $originalclassnamepony, $elements)
    {
        Serialization::$extracted[$hash] = $this;
        $this->originalclassnamepony = $originalclassnamepony;
        foreach ($elements as $key => $value) {
            $this->{$key} = Serialization::createserializableobject($value);
        }
    }

    public function __wakeup()
    {
        $this->realactualponyobject = new $this->originalclassnamepony(get_object_vars($this));
        if (method_exists($this->realactualponyobject, '__wakeup')) {
            $this->realactualponyobject->__wakeup();
        }
    }
}
