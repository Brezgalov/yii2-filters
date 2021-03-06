<?php

namespace Brezgalov\Filters;

use yii\base\Model;
use yii\db\ActiveRecord;

class Filter
{
    /**
     * @var string
     */
    protected $modelName;

    /**
     * Массив пользовательских фильтров
     * @var array
     */
    protected $handlers = [];

    /**
     * Список полей по которым можно фильтровать
     * @var array
     */
    protected $allowedFields = [];

    /**
     * Массив полей по которым запрещена фильтрация
     * @var array
     */
    protected $forbiddenFields = [
        'fields' => true,
        'conditions' => true,
        'expand' => true,
        'per-page' => true,
        'page' => true,
    ];

    /**
     * Disable fields filter white list check
     * @var bool
     */
    public $disableWhiteListFields = false;

    /**
     * Добавляем хэндлер в список
     * Пример предопределения фильтров
     * $filter->addHandler('1', [$this, 'func1']->addHandler('2', [$this, 'func2']);
     *
     * @param string $key
     * @param callable $handler
     * @return $this
     */
    public function addHandler($key, IFilterHandler $filter)
    {
        $this->handlers[$key] = $filter;
        return $this;
    }

    /**
     * @param string $model
     */
    public function setModelName($modelName)
    {
        $this->modelName = $modelName;
    }

    /**
     * Добавляем список полей запрещенных к фильтрации
     * @param array $fields
     * @return $this
     */
    public function addForbiddenFields(array $fields)
    {
        foreach ($fields as $field) {
            $this->forbiddenFields[$field] = true;
        }
        return $this;
    }

    /**
     * Добавление полей в белый список для фильтра по полям
     * @param array $fields
     * @return $this
     */
    public function addAllowedFields(array $fields)
    {
        foreach ($fields as $field) {
            $this->allowedFields[$field] = true;
        }
        return $this;
    }

    /**
     * Проверяет является ли элемент фильтра инвалидным
     * @param $field
     * @return bool
     */
    public function isInvalidField($field)
    {
        $forbidden = array_key_exists($field, $this->forbiddenFields);
        $allowed = array_key_exists($field, $this->allowedFields);
        return $forbidden || !$allowed;
    }

    /**
     * Применяем хендлер фильтра к запросу
     * @param \yii\db\QueryInterface $query
     * @param array $params
     */
    public function putFilter(\yii\db\QueryInterface $query, array $params)
    {
        if (array_key_exists('filter', $params)) {
            if (!is_string($params['filter'])) {
                return;
            }
            $filters = explode(',', $params['filter']);
            foreach ($filters as $filter) {
                $this->handlers[$filter]->apply($query);
            }
        } else {
            foreach ($params as $key => $val) {
                if ($this->isInvalidField($key)){
                    continue;
                }
                $key = (!empty($this->modelName))? $this->modelName . '.' . $key : $key;
                $condition = (@$params['conditions'][$key])?: '=';
                $query->andWhere([$condition, $key, $val]);
            }
        }
    }
}