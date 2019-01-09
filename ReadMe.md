# Подключение
Наследуем IndexAction от **brezgalov\filters\actions\IndexAction**
Теперь query-параметры (кроме conditions, 'expand', 'per-page', 'page') будут использоваться в кач-ве фильтров. Например:
```php
users?id=1 - ищем пользователя с id = 1
users?id[]=23&id[]=42&conditions[id]="in" - ищем пользователя с id 23 или 42
```

# Кастомные фильтры
Создаем модель фильтра
```php
class InnFilter extends BaseObject implements IFilterHandler
{
    public function apply(\yii\db\QueryInterface $query)
    {
        $inn = \Yii::$app->request->getQueryParam('inn');
        $phone = \Yii::$app->request->getQueryParam('phone');
        if (!$inn && $phone) {
            throw new UnprocessableEntityHttpException('Фильтр по инн не может функционировать без параметров inn и phone');
        }
        $query
            ->join('inner join', 'client_requisites as inn_client_reqs', 'drivers.client_id = inn_client_reqs.client_id')
            ->andWhere(['inn_client_reqs.inn' => $inn])
            ->andWhere([
                'or',
                ['drivers.phone1' => $phone],
                ['drivers.phone2' => $phone]
            ])
        ;
    }
}
```
Далее подключаем фильтр в IndexAction:
```php
class IndexAction extends \brezgalov\filters\actions\IndexAction
{
    public function __construct($id, \yii\base\Controller $controller, array $config = [])
    {
        parent::__construct($id, $controller, $config);
        $this->filter->addHandler('inn', new InnFilter());
    }
}
```
# Использование:
`/drivers?filter=inn&inn=12345678&phone=79091234555`
