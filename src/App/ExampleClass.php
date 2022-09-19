<?
declare(strict_types=1);

class ExampleClass
{
    const Foo = 'bar';
    private string $test_variable;

    public function __construct(string $test_variable)
    {
        $this->test_variable = self::Foo;
    }

    public function getTestVariable() : string
    {
        return $this->test_variable;
    }

    function DoSomething(array $foo) {


        return array(0,1, null, '', false, ...$foo);
    }

    public function anotherThing(): array
    {
        return array_values(array_filter($this->DoSomething(array_map(static fn ($foo)=> $foo*2, [2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17])))); // very long line
    }
}
