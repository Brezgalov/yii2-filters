<?php

namespace brezgalov\filters\actions;

use brezgalov\filters\Filter;

/**
 * Базовый экшон. Поддерживает фильтры
 * @package potok\modules\v1\controllers\actions\base
 */
class IndexAction extends \yii\rest\IndexAction
{
    /**
     * filter
     * @var null|Filter
     */
    protected $filter = null;

    /**
     * IndexAction constructor.
     * @param $id
     * @param \yii\base\Controller $controller
     * @param array $config
     */
    public function __construct($id, \yii\base\Controller $controller, array $config = [])
    {
        $this->filter = new Filter();
        parent::__construct($id, $controller, $config);
    }

    /**
     * Подключаем фильтры перед отдачей
     * @return \yii\data\ActiveDataProvider
     */
    protected function prepareDataProvider()
    {
        $provider = parent::prepareDataProvider();
        $this->filter->putFilter(
            $provider->query,
            \Yii::$app->request->getQueryParams()
        );
        return $provider;
    }
}