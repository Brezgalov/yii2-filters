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
     * @var bool if FALSE - pagination is disabled while page or per-page not found in request
     */
    public $pagAlwaysActive = false;

    /**
     * @var int default pagination page size
     */
    public $defaultPageSize = 100;

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
        $queryParams = \Yii::$app->request->getQueryParams();
        $provider = parent::prepareDataProvider();
        //manage pagination
        if (!$this->pagAlwaysActive && !array_key_exists('per-page', $queryParams) && !array_key_exists('page', $queryParams)) {
            $provider->pagination = false;
        } else {
            $provider->pagination->defaultPageSize = $this->defaultPageSize;
        }
        //apply filters
        $this->filter->putFilter(
            $provider->query,
            $queryParams
        );
        return $provider;
    }
}