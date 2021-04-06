<?php

namespace app\components;

use yii\base\Component;

class Variable extends Component {

    public $variable;
    private $global = [];

    public function set($key, $value) {
        $this->global[$key] = $value;
    }

    public function get($key) {
        return isset($this->global[$key]) ? $this->global[$key] : null;
    }

}
