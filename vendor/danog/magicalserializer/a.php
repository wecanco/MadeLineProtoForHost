<?php

class a
{
}
class b
{
}
$a = new a();
$a->a = ['lel', ['lel', [new a()]]];
$a->b = new a();
$a->b->c = [new a()];
$a->b->c[0]->d = 'cos';
$a->b->c[0]->e = new b();
$a->b->c[0]->e->f = new a();
$a = [$a];
file_put_contents('test', serialize($a));
var_dump(serialize($a), $a);

var_dump(unserialize(file_get_contents('testb')));
