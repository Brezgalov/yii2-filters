<?php

namespace brezgalov\filters;

interface IFilterHandler
{
    /**
     * Метод применяет фильтр к запросу
     * @param \yii\db\QueryInterface $query
     * @return mixed
     */
    public function apply(\yii\db\QueryInterface $query);
}