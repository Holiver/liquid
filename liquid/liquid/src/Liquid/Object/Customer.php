<?php
namespace Liquid\Object;

use common\proxy\CustomerService;
use Liquid\Drop;
use Liquid\Tools;
use Yii;

class Customer extends Drop {
    public $info = null;
    public $id = null;

    public function __construct($userId = '', $customer = null){
        $this->id = $userId;
        $this->info = $customer;
    }

    public function info()  {
        if ($this->info === null)   {
            if ($this->id)    {
                // 如果是当前登陆用户，统一通过 identity 获取详情
                if (Yii::$app->user->identity && ($this->id == Yii::$app->user->identity->getId()))  {
                    $this->info = Yii::$app->user->identity->getInfo();
                } else  {
                    $this->info = CustomerService::getCustomerInfo($this->id);
                }
            } else {
                if (Yii::$app->user->identity)  {
                    $this->info = Yii::$app->user->identity->getInfo();
                }
            }

            if ($this->info === null)   {
                $this->info = [];
            }
        }

        return $this->info;
    }

    public function id() {
        return $this->info() ? $this->id : null;
    }

    public function email() {
        return $this->info() ? $this->info['email'] : '';
    }

    public function name()  {
        return $this->info() ? Tools::getName($this->info['first_name'], $this->info['last_name']) : '';
    }
}
