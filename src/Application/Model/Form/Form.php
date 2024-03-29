<?php declare(strict_types=1);

namespace Hanaboso\PipesPhpSdk\Application\Model\Form;

/**
 * Class Form
 *
 * @package Hanaboso\PipesPhpSdk\Application\Model\Form
 */
final class Form
{

    /**
     * @var Field[]
     */
    private array $fields = [];

    /**
     * @param Field $field
     *
     * @return Form
     */
    public function addField(Field $field): Form
    {
        $this->fields[] = $field;

        return $this;
    }

    /**
     * @return Field[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @return mixed[]
     */
    public function toArray(): array
    {
        $fields = [];
        foreach ($this->fields as $field) {
            $fields[] = $field->toArray();
        }

        return $fields;
    }

}
