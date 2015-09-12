<?php
    class Info extends DB\SQL\Mapper {

        public function __construct(DB\SQL $db) {
            parent::__construct($db,'information');
        }

        public function all() {
            $this->load();
            return $this->query;
        }

//        public function add() {
//            $this->copyFrom('POST');
//            $this->save();
//        }

//        public function getById($id) {
//            $this->load(array('itemId=?',$id));
//            $this->copyTo('POST');
//        }

        public function edit() {
            $this->load(array('infoId=?',1));
            $this->copyFrom('POST');
            $this->update();
        }

//        public function delete($id) {
//            $this->load(array('itemId=?',$id));
//            $this->erase();
//        }
    }
?>
