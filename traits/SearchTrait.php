<?php

namespace app\traits;

trait SearchTrait {

    public $fileSearchProcessd = false;
    public $where = null;

    function delimitedComlumns() {
        return true;
    }

    function prependCard() {
        
    }

    final public function beforeValidate() {
// bypass scenarios() implementation in the parent model

        $p = get_parent_class(get_parent_class());
        $dl = $this->delimitedComlumns();
        if ($dl === true) {
            $dl = $this->attributes();
        }
        if ($dl) {
            foreach ($dl as $cl) {
                $eq = '';
                if ($this->$cl && !is_numeric($this->$cl)) {
                    $eq = $this->$cl[0];
                }
                if (isset($this->$cl) && !is_array($this->$cl) && strpos($this->$cl, ',')) {
                    if ($eq && in_array($eq, ['=', '%'])) {
                        $tt = explode(',', $this->$cl);
                        $tr = [];
                        foreach ($tt as $s) {
                            $tr[] = $eq . ltrim($s, $eq);
                        }
                        $this->$cl = $tr;
                    } else {
                        $this->$cl = explode(',', $this->$cl);
                    }
                }
                if ($cl == 'smartcardno' && APPEND_1000_GOSPELL_CARD && !empty($this->$cl)) {
                    $data = [];
                    $cc = (array) $this->$cl;
                    foreach ($cc as $dc) {
                        $data[] = $dc;
                        $_dc = trim($dc, '%=');
                        if (is_numeric($_dc) && strlen($_dc) == 8) {
                            $data[] = $dc[0] . APPEND_1000_GOSPELL_CARD . substr($dc, 1);
                        }
                    }
                    $this->$cl = $data;
                }
            }
        }
        if ($p::beforeValidate()) {
            $this->processFileSearch();
            return true;
        }
        return false;
    }

    public function fileSupportedFields() {
        return [
        ];
    }

    public function processFileSearch($force = false) {
        if ($this->fileSearchProcessd == false || $force == true) {
            $f = $this->fileSupportedFields();
            if (!empty($f)) {
                foreach ($f as $fld) {
                    $v = $this->$fld;
                    if (!empty($v) && is_array($v)) {
                        $v = current($v);
                    }
                    if (!empty($v) && is_string($v)) {
                        if (substr($v, 0, 1) == '@') {
                            $ids = substr($v, 1);
                            $dataObj = \app\models\mongo\MongoFileSearch::findOne($ids);
                            if ($dataObj) {
                                $this->$fld = $dataObj->data_array;
                            }
                        }
                    }
                }
                $this->fileSearchProcessd = true;
            }
        }
    }

}
