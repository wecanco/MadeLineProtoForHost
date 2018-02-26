# MagicalSerializer

Licensed under AGLPV3, created by Daniil Gentili (https://daniil.it).

This library allows you to serialize classes that extend pthreads's Volatile or Threaded classes, or any other internal class with a custom serializer.  

It also provides reverse and forward compatibility between old serializations, where your objects do not yet extend Threaded/Volatile, and new serializations, where they do extend them.

Install it using composer, the package name is `danog/magicalserializer`.


Usage of this library is extremely simple, here are a few examples (see a.php and b.php for more):


```
<?php

class a
{
    public function __construct() {
        var_dump("Constructed!");
        $this->a = 'pony';
    }
    public function __wakeup() {
        var_dump($this->a);
    }
}
$a = new a;
file_put_contents('test', serialize($a));
```

As you can see, here `a` does not extend any class, and we are serializing an instance of it to the file `test`.
This example also prints `Constructed!`, since the constructor function, __construct, is called.

```
<?php

class a extends \Volatile
{
    use \danog\Serializable;
    public function __magic_construct() {
        var_dump("Constructed!");
        $this->a = 'pony';
    }
    public function __wakeup() {
        var_dump($this->a);
    }
}
$a = \danog\Serialization::unserialize(file_get_contents('test'));
var_dump($a);
$othera = new a;

```

Here we must simply include the trait `\danog\Serializable` to make the class serializable.  
This will deserialize correctly the contents of `test`, recreating the instance of `a`, with the only difference that now it will be thread safe.  
Of course, the wakeup function will be called, so `pony` will be printed.  
You may have noticed that the construct function is now called `__magic_construct` instead of `__construct`: this is a required change, or else this library will not work.  
Of course, when `a` will be instantiated, the constructor function will be called anyway (and you'll see the message `Constructed!` pop up).  

If you try to serialize (using `\danog\Serialization::serialize`) either `$a` or `$othera` and deserialize it from the first script (where `a` does not extend any other class), you will find that it will be deserialized correctly.


