<?php
    class Item extends DB\SQL\Mapper {

        public function __construct(DB\SQL $db) {
            parent::__construct($db,'items');
        }

        public function all() {
            $this->load();
            return $this->query;
        }

        /**
         * SELECT all for ADMIN SECTION ONLY
         * @param  [[Type]] $cafeId [[Description]]
         * @return [[Type]] [[Description]]
         */
        public function getAllByCafeId ($cafeId) {
            $this->load(array('cafeId=?', $cafeId));
            return $this->query;
        }

        public function add() {
            $this->copyFrom('POST');
            $this->save();
        }

        public function addFromParam() {
            $this->copyFrom('PARAMS');
            $this->save();
        }

        public function getById($id) {
            $this->load(array('itemId=?',$id));
            $this->copyTo('POST');
        }

        public function edit($id) {
            $this->load(array('itemId=?',$id));
            $this->copyFrom('POST');
            $this->update();
        }

        public function delete($id) {
            $this->load(array('itemId=?',$id));
            $this->erase();
        }

        public function getSpecial() {
            $this->load(
                        array('isSpecial=?', 1),
                        array('isActive=?', 1)
                    );
            return $this->query;
        }

        /**
         * ONLY FOR USER => isActive = 1
         * Return all items
         * @param  integer $cafeId     cafe id
         * @param  integer $categoryId category id
         * @return array   list of active items
         */
        public function getAllByCategoryId($cafeId, $categoryId) {
            $this->load(array('cafeId=? and categoryId=? and isActive=?', $cafeId, $categoryId, 1));
            return $this->query;
        }
    }
?>
