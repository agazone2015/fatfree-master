<?php
    class UserController extends Controller {

        public function index() {
            $item = new Item($this->db);
            $this->f3->set('specials', $item->getSpecial());

            $info = new Info($this->db);
            $information = $info->all()[0];
            $this->f3->set('isPromotionOn', $information->isPromotionOn);
            $this->f3->set('isSpecialOn', $information->isSpecialOn);
            $this->f3->set('promotionText', $information->promotion);

            $this->f3->set('isHome', 'active');
            $this->f3->set('view','user/home.html');
        }

        /*
        * [GET]
        * Render menu page based on param
        */
        public function menu () {
            // TODO:
            $cafeName = $this->f3->get('PARAMS.cafeName');
            if (strtolower($cafeName) == 'richmond') {
                $this->f3->set('isRichmond', 'active');
                $cafeId = 1;
            }
            else if (strtolower($cafeName) == 'malvern') {
                $this->f3->set('isMalvern', 'active');
                $cafeId = 2;
            }
            $this->f3->set('cafeId', $cafeId);

            // create blur effect for background
            $this->f3->set('isBlurred', 'blur');

            // retrieve all pictures and return links to client
            $images = new ImageManager($this->db);
            $menuImages = $images->allForMenu();

            // retrieve all categories and setup for template
            $cats       = new Category($this->db);
            $categories = $cats->all();
            $this->f3->set('categories', $categories);
            $firstCategory = $categories[0]->categoryId;

            $info       = new Info($this->db);
            $this->f3->set('isPriceOn', $info->all()[0]->isPriceOn);

            // setup for client
            $clientArray = array();
            foreach ($menuImages as $menuImage) {
                $clientArray[] = $menuImage->imageLink;
            }

            // retreive all items based on $cafeId
            $items      = new Item($this->db);
            $this->f3->set('items', $items->getAllByCategoryId($cafeId, $firstCategory));


            $this->f3->set('pictureArray', $clientArray);
            $this->f3->set('ESCAPE', false);


            $this->f3->set('view', 'user/menu.html');
        }

        /*
        * [POST]
        * Get all items match with categories and cafeId and return
        */
        public function getCategoryItems () {
            $result = array(
                'success' => false,
                'message' => 'error.',
                'status'  => 'error',
                'total' => 0,
                'records' => array()
            );

            $cafeId     = $this->f3->get('POST.cafeId');
            $categoryId = $this->f3->get('POST.categoryId');

            $items      = new Item($this->db);
            $items      = $items->getAllByCategoryId($cafeId, $categoryId);
            foreach ($items as $item) {
                $result[records][] = array(
                                'itemId'        => $item->itemId,
                                'itemName'      => $item->itemName,
                                'description'   => $item->description,
                                'price'         => $item->price,
                                'isVegetarian'  => $item->isVegetarian  == 1 ? true: false,
                                'isPopular'     => $item->isPopular     == 1 ? true : false
                    );
            }

            $result[total] = count($items);
            $result[success] = true;
            $result[status]  = 'success';
            $result[message] = 'Successfully retrieved data';

            header('Content-Type: application/json');
            echo json_encode($result, JSON_NUMERIC_CHECK);
            exit;
        }


        /*
        * [GET]
        * Route user to location page
        */
        public function cafes () {
            // TODO:
            $info = new Info($this->db);
            $information = $info->all()[0];
            $this->f3->set('aboutUs', $information->about);
            $this->f3->set('isCafes', 'active');
            $this->f3->set('isBlurred', 'blur');
            $this->f3->set('view', 'user/cafes.html');
        }

        /*
        * [POST]
        * Return user distance and duration to Gllows
        */
        public function findLocation () {

            $address = $this->f3->get('PARAMS.address');
            // TODO:
            $address = $address.' Melbourne VIC Australia';

            $baseUrl ='https://maps.googleapis.com/maps/api/distancematrix/json?';
            $key = '&key=AIzaSyCpQ3Qicw8ZdQm6BSP0sOBFNCR25hSMpjk';
            $mode = '&mode=car';

            $cafeRichmond = 'Gllow Taiwanese cafe 448 Bridge Road Richmond Melbourne VIC';
            $cafeMalvern  = 'Gllow Taiwanese cafe 29 Station Street Malvern Melbourne VIC';

            $compareRichmond = 'origins=' . str_replace(' ', '+', $address) . '&destinations=' . str_replace(' ', '+', $cafeRichmond);
            $compareMalvern  = 'origins=' . str_replace(' ', '+', $address) . '&destinations=' . str_replace(' ', '+', $cafeMalvern);

            $richmondRequest = $baseUrl . $compareRichmond . $mode . $key;
            $malvernRequest  = $baseUrl . $compareMalvern  . $mode . $key;


            $richmondResult = file_get_contents($richmondRequest);
            $malvernResult  = file_get_contents($malvernRequest);

            header('Content-Type: application/json');
            echo json_encode(array(json_decode($richmondResult), json_decode($malvernResult)), JSON_NUMERIC_CHECK);
            exit;
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

        public function menuOld () {
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
