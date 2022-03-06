<?php

namespace App\Provider;

use Pimcore\Model\DataObject\Concrete;

class TestProvider implements \Pimcore\Model\DataObject\ClassDefinition\PreviewGeneratorInterface
{
    protected $productLinkGenerator;

    /**
     * @param \Pimcore\Model\DataObject\Concrete $object
     * @param array $params
     * @return string
     */
    public function generatePreviewUrl(Concrete $object, array $params): string {
        //dd($object);
        $additionalParams = [];
        return 'test/'.$object->getId();
    }

    /**
     * @param \Pimcore\Model\DataObject\Concrete $object
     *
     * @return array
     */
    public function getPreviewConfig(Concrete $object): array {

        return [
            [
                'name' => '_locale',
                'label' => 'Locale',
                'values' => [
                    'English' => 'en',
                    'German' => 'de'
                ],
                'defaultValue' => 'en'
            ],
            [
                'name' => 'otherParam',
                'label' => 'Other',
                'values' => [
                    'Label Text' => 'value',
                    'Option #2' => 2,
                    'Custom Option' => 'custom'
                ],
                'defaultValue' => 'value'
            ]
        ];
    }
}
