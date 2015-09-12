<?php
    class UserController extends Controller {

        public function index() {
            $item = new Item($this->db);
            $this->f3->set('specials', $item->getSpecial());

            $this->f3->set('page_head','All Items');
            $this->f3->set('view','user/home.html');

            $info = new Info($this->db);
            $information = $info->all()[0];
            $this->f3->set('isPromotionOn', $information->isPromotionOn);
            $this->f3->set('isSpecialOn', $information->isSpecialOn);
        }

        /*
        * Create user function
        * POST request: create user
        * GET request: render form
        */
        public function create() {
            /*
            * check if POST request has create field
            * if yes, add user and return home
            */
            if($this->f3->exists('POST.create')) {
                $user = new User($this->db);
                $user->add();

                $this->f3->reroute('/');
            }
            /*
            * otherwise, render request as a form to input user
            */
            else {
                $this->f3->set('site', 'Gllow - New User');
                $this->f3->set('page_head','Create Item');
                $this->f3->set('view','user/create.html');
            }
        }

        /*
        * Update user function
        * POST request: update user information
        * GET request: render form
        */
        public function update() {
            $user = new User($this->db);
            /*
            * check if POST request has created field
            * if yes, add user and return home
            * !! what happens when edit
            *   1. $user load record into memory according to POST.id
            *   2. copy value from POST request to record
            *   3. update back into database
            *
            */
            if($this->f3->exists('POST.update')) {
                $user->edit($this->f3->get('POST.id'));
                $this->f3->reroute('/');
            }
            /*
            * otherwise, render request as a form to input user
            */
            else {
                $user->getById($this->f3->get('PARAMS.id'));
                $this->f3->set('user',$user);
                $this->f3->set('page_head','Update User');
                $this->f3->set('view','user/update.html');
            }
            /*
            * testing
            */
        }

        /*
        * Delete user function
        * GET only
        */
        public function delete() {

            if($this->f3->exists('PARAMS.id')) {
                $user = new User($this->db);
                $user->delete($this->f3->get('PARAMS.id'));
            }

            // go back to home page after delete
            $this->f3->reroute('/');
        }

        public function menu () {
            if($this->f3->exists('POST.create')) {
                $user = new User($this->db);
                $user->add();

                $this->f3->reroute('/');
            }
            /*
            * otherwise, render request as a form to input user
            */
            else {
                $item = new Item($this->db);
                $this->f3->set('items', $item->getAllByCategoryId(1, 1));
                $this->f3->set('view','user/item.html');
            }
        }

    }
?>
