<?php

namespace Dg482\Mrd\Builder\Form\Fields;

use Dg482\Mrd\Builder\Form\Structure\BaseStructure;
use Dg482\Mrd\Builder\Form\ValidatorsTrait;
use Carbon\Carbon;
use Dg482\Mrd\Model;

/**
 * Class Field
 * @package Dg482\Mrd\Builder\Form\fields
 */
abstract class Field
{
    use ValidatorsTrait;

    /**
     * Имя поля
     * @var string
     */
    const NAME = 'name';

    /**
     * Код поля
     * @var string
     */
    const CODE = 'code';

    /**
     * Поле с указанием типа поля
     * @var string
     */
    const TYPE = 'type';

    /**
     * Идентификатор пользователя
     * @var string
     */
    const USER_ID = 'user_id';

    /** @var string */
    const DATA = 'data';

    /**
     * Тип поля по умолчанию
     * @var string
     */
    const FIELD_TYPE = '';

    /**
     * Хранилище по умолчанию бд
     * @var string
     */
    const STORAGE_BACKEND = 'backend';

//    /**
//     * Хранилище на клиенте
//     * @var string
//     */
//    const STORAGE_CLIENT = 'client';
//
//    /**
//     * Оформление текста
//     * @var string
//     */
//    const TEXT_DANGER = 'text-danger',
//        TEXT_WARNING = 'text-warning',
//        TEXT_SUCCESS = 'text-success',
//        TEXT_PRIMARY = 'text-primary',
//        TEXT_INFO = 'text-info';

    /**
     * Field label
     * @var string $name
     */
    protected string $name = '';

    /**
     * Model field
     * @var string $field
     */
    protected string $field = '';

    /** @var array $attributes */
    protected array $attributes = [];

    /**
     * Бейдж, выводится внизу поля
     * @var Badge|null $badge
     */
    protected ?Badge $badge = null;

    /**
     * Коллекция бейджей
     * @var array $badges
     */
    protected array $badges = [];

    /**
     * Поле выключено?
     * @var bool $disabled
     */
    protected bool $disabled = false;

    /**
     * Хранилище значения поля
     * @var string
     */
    protected string $storage = self::STORAGE_BACKEND;

    /** @var string */
    protected string $placeholder = '';

    /** @var string */
    protected string $value = '';

    /** @var int */
    protected int $width = 100;

    /** @var bool */
    protected bool $multiple = false;

    /** @var array */
    protected array $data = [];

    /** @var Model|null */
    protected ?Model $relation;

    /**
     * @param  bool  $set
     * @return $this
     */
    public function showTable($set = true): self
    {
        $this->attributes['showTable'] = $set;

        return $this;
    }

    /**
     * @return bool
     */
    public function isShowTable(): bool
    {
        return isset($this->attributes['showTable']) ? true === $this->attributes['showTable'] : true;
    }

    /**
     * @param  bool  $set
     * @return $this
     */
    public function showForm($set = true): self
    {
        $this->attributes['showForm'] = $set;

        return $this;
    }

    /**
     * @return bool
     */
    public function isShowForm(): bool
    {
        return isset($this->attributes['showForm']) ? true === $this->attributes['showForm'] : true;
    }

    /**
     * Массив параметров поля для отрисовки в UI
     * @return array
     */
    public function getFormField(): array
    {
        return [
            'id' => empty($this->id) ? md5($this->getName().mktime(true)) : $this->id,
            self::NAME => $this->getName(),
            self::TYPE => $this->getFieldType(),
            'field' => $this->getField(),
            'variant' => ($this instanceof FieldEnum) ? $this->getVariant() : [],
            'items' => ($this instanceof BaseStructure) ? $this->getItems() : [],
            'badge' => $this->badge ? $this->badge->getData() : null,
            'badges' => ($this->badges !== []) ? array_map(function (Badge $badge) {
                return $badge->getData();
            }, $this->badges) : [],
            'disabled' => $this->isDisabled(),
            'storage' => $this->getStorage(),
            'attributes' => $this->getAttributes(),
            'placeholder' => $this->getPlaceholder(),
            'validators' => $this->getValidators(),
        ];
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param  string  $name
     * @return $this
     */
    public function setName(string $name): Field
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getFieldType(): string
    {
        return $this::FIELD_TYPE;
    }

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @param  string  $field
     * @return $this
     */
    public function setField(string $field): Field
    {
        $this->field = $field;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    /**
     * @param  bool  $disabled
     * @return $this|self
     */
    public function setDisabled(bool $disabled): self
    {
        $this->disabled = $disabled;

        return $this;
    }

    /**
     * @return string
     */
    public function getStorage(): string
    {
        return $this->storage;
    }

    /**
     * @param  string  $storage
     * @return Field
     */
    public function setStorage(string $storage): Field
    {
        $this->storage = $storage;

        return $this;
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param  array  $attributes
     * @return $this
     */
    public function setAttributes(array $attributes): Field
    {
        if (false === isset($attributes['showTable'])) {
            $attributes['showTable'] = true;
        }

        if (false === isset($attributes['showForm'])) {
            $attributes['showForm'] = true;
        }

        $this->attributes = $attributes;

        return $this;
    }

    /**
     * @return string
     */
    public function getPlaceholder(): string
    {
        return $this->placeholder;
    }

    /**
     * @param  string  $placeholder
     * @return Field
     */
    public function setPlaceholder(string $placeholder): Field
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    /**
     * Скрыть поле в форме
     * @return $this
     */
    public function hideForm(): Field
    {
        $this->attributes['showForm'] = false;

        return $this;
    }

    /**
     * Скрыть поле в таблице
     * @return $this
     */
    public function hideTable(): Field
    {
        $this->attributes['showTable'] = false;

        return $this;
    }

    /**
     * @return null
     */
    public function getBadge()
    {
        return $this->badge;
    }

    /**
     * @param $badge
     * @return $this
     */
    public function setBadge(Badge $badge): self
    {
        $this->badge = $badge;

        return $this;
    }

    /**
     * @param  Badge  $badge
     * @return $this
     */
    public function pushBadge(Badge $badge): self
    {
        array_push($this->badges, $badge);

        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setFieldValue($value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * значение поля модели/сборное значение
     *
     * @param  bool  $original
     * @return string
     */
    public function getFieldValue(bool $original = false)
    {
        if ($this->value instanceof Carbon && $original === false) {
            return $this->value->format(Carbon::DEFAULT_TO_STRING_FORMAT);
        }

        return $this->value;
    }

    /**
     * Обновление значения поля
     *
     * @param  null  $value
     * @param  Field|null  $dateField
     * @return null
     */
    public function updateValue($value = null, ?Field $dateField = null)
    {
        if ($dateField !== null) {
            return $dateField->getFieldValue(false);
        }

        return $value;
    }

    /**
     * @return bool
     */
    public function isMultiple(): bool
    {
        return $this->multiple;
    }

    /**
     * @param  bool  $multiple
     * @return Field
     */
    public function setMultiple(bool $multiple): Field
    {
        $this->multiple = $multiple;

        return $this;
    }

    /**
     * @param  Model  $model
     * @param  Model|null  $relation
     * @return $this
     */
    public function setFieldRelation(Model $model, ?Model $relation): Field
    {
        if (null === $this->relation && $relation) {
            $this->setData([
                'owner' => $model->{'id'},
                'form' => $_REQUEST['form'],
                'field' => $this->getField(),
                'relation' => $relation,
            ])->setFieldValue([]);

            if ($this instanceof FieldEnum) {
                $this->setVariant((new EnumVariant)
                    ->make($relation->{'id'}, $relation->{'name'}))
                    ->setFieldValue([$relation->{'id'}]);
            }

            $this->relation = $relation;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param  array  $data
     * @return Field
     */
    public function setData(array $data): Field
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return Model|null
     */
    public function getFieldRelation()
    {
        return $this->relation;
    }
}
