<?php

declare(strict_types=1);

namespace Overblog\GraphQLBundle\Tests\Resolver;

use GraphQL\Type\Definition\ResolveInfo;
use Overblog\GraphQLBundle\Resolver\Resolver;
use PHPUnit\Framework\TestCase;

class ResolverTest extends TestCase
{
    /**
     * @param $fieldName
     * @param $source
     * @param $expected
     *
     * @dataProvider resolverProvider
     */
    public function testDefaultResolveFn($fieldName, $source, $expected): void
    {
        $info = new ResolveInfo(['fieldName' => $fieldName]);

        $this->assertEquals($expected, Resolver::defaultResolveFn($source, [], [], $info));
    }

    public function testSetObjectOrArrayValue(): void
    {
        $object = new \stdClass();
        $object->foo = null;
        Resolver::setObjectOrArrayValue($object, 'foo', 'bar');
        $this->assertSame($object->foo, 'bar');

        $data = ['foo' => null];
        Resolver::setObjectOrArrayValue($data, 'foo', 'bar');
        $this->assertSame($data['foo'], 'bar');
    }

    public function resolverProvider()
    {
        $object = new Toto();

        return [
            ['key', ['key' => 'toto'], 'toto'],
            ['fake', ['coco'], null],
            ['privatePropertyWithoutGetter', $object, null],
            ['privatePropertyWithoutGetterUsingCallBack', $object, Toto::PRIVATE_PROPERTY_WITHOUT_GETTER],
            ['privatePropertyWithGetter', $object, Toto::PRIVATE_PROPERTY_WITH_GETTER_VALUE],
            ['private_property_with_getter2', $object, Toto::PRIVATE_PROPERTY_WITH_GETTER2_VALUE],
            ['not_object_or_array', 'String', null],
            ['name', $object, $object->name],
        ];
    }
}
