<?php
    class ImageManager extends DB\SQL\Mapper {

        public function __construct(DB\SQL $db) {
            parent::__construct($db,'images');
        }

        public function all() {
            $this->load();
            return $this->query;
        }

        public function add() {
            $this->copyFrom('POST');
            $this->save();
        }

        /*
        * [POST]
        * Description
        */
        public function allForMenu () {
            // TODO:
            $this->load(array('isIncluded', 1));
            return $this->query;
        }


        public function getById($id) {
            $this->load(array('imageId=?',$id));
            return $this->query;
        }

        public function edit($id) {
            $this->load(array('imageId=?',$id));
            $this->copyFrom('POST');
            $this->update();
        }

        public function delete($id) {
            $this->load(array('imageId=?',$id));
            $this->erase();
        }
    }
?>
