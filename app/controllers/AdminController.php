<?php
    class AdminController extends Controller {

        /*===========================================================
        * Important:
        * Every function handling AJAX request must exit immediately
        * after finished to avoid rendering and returning templates
        ============================================================*/

        /*=================================
        * Additional hacks & workaround used
        * 1. $f3->set('ESCAPE', false) => ability to json_encode string[]
        *       this will set only once, next request will have ESCAPE = true
        * 2. In update function, new values are store in POST.changes[]
        *       => copy all parameter to POST
        ==================================*/


        /*
        * override beforeRoute() to perform check
        */
        public function beforeroute () {

            // Let Controller handler request by corresponding functions when user does AJAX
            if ($this->f3->get('AJAX')) {

                // if user is trying to login, continue to next function
                if ($this->f3->get('PATH') == '/admin/login') return true;

                // If user is not logging in, check session
                // Continue to function if user SESSION is valid
                if ($this->f3->get('SESSION.user')) return true;
                // Return error message 'session expired' if invalid SESSION
                else {
                    header('Content-Type: application/json');
                    echo json_encode(array(
                        'status'    => 'error',
                        'message'   => 'session expired',
                        'code'      =>  399
                    ));
                    exit;
                }
            }

            // If user is trying to get to other pages instead of dashboard
            if ($this->f3->get('PATH') != '/admin') {

                // check if session exist to redirect
                if (!$this->f3->get('SESSION.user')) {

                    // redirect when accessing without login
                    $this->f3->reroute('/admin');
                }
            }
        }

        /*
         * [GET]
         * Handle landing page request
         */
        public function index() {
            if ($this->f3->get('SESSION.user')) {
                $this->f3->reroute('/admin/dashboard');
//                $this->f3->set('page_head', 'Admin Dashboard');
//                $this->f3->set('view', 'admin/dashboard.html');
            } else {
                $this->f3->set('page_head','Admin Login');
                $this->f3->set('view','admin/default.html');
            }
        }

        /**
         * [GET]
         * Landing page after login
         */
        public function dashboard() {
            $this->f3->set('page_head', 'Gllow Administrator Dashboard');

            $tempCategories = array();
            $categories = new Category($this->db);
            $categories = $categories->all();

            foreach ($categories as $category) {
                array_push($tempCategories, $category->categoryName);
            }
            $this->f3->set('ESCAPE', false);
            $this->f3->set('categories', json_encode($tempCategories));
            $this->f3->set('view', 'admin/dashboard.html');
        }

        /**
         * [POST]
         * return categories for corresponding cafe
         */
        public function getItems () {
            $id = $this->f3->get('PARAMS.id');
            $result = array(
                'success' => false,
                'info' => '',
                'total' => 0,
                'records' => array()
            );
            try {
                // Only process to database if cafe id is valid
                if ($id > 0) {
                    $items = new Item($this->db);
                    $items = $items->getAllByCafeId($id);

                    // prepare category object to avoid creating too many objects during loop
                    $category = new Category($this->db);

                    // change result indicatior to true
                    $result[success] = true;
                    $result[status] = "success";

                    // populate records
                    foreach ($items as $item) {
                        $result['records'][] =
                            array(
                                'recid'         => $item->itemId,
                                'itemName'      => $item->itemName,
                                'description'   => $item->description,
                                'price'         => $item->price,
                                'isActive'      => $item->isActive      == 1 ? true : false,
                                'isVegetarian'  => $item->isVegetarian  == 1 ? true: false,
                                'isPopular'     => $item->isPopular     == 1 ? true : false,
                                'isSpecial'     => $item->isSpecial     == 1 ? true : false,
                                'category'      => array(
                                        'id'    => $item->categoryId,
                                        'text'  => $category->getById($item->categoryId)[0]->categoryName)
                                );
                    }
                }
                $result[success] = true;
            } catch (Exception $e) {

            }

            // Echo result and return immediately
            header('Content-Type: application/json');
            echo json_encode($result, JSON_NUMERIC_CHECK );
            exit;
        }

        /**
         * [POST]
         * return [Richmond Categories, Malvern categories]
         */
        public function getCategories () {
            $result = array(
                'success' => false,
                'info' => '',
                'total' => 0,
                'records' => array()
            );

            try {
                $categories = new Category($this->db);
                $categories = $categories->all();
                foreach ($categories as $category) {
                    $result[records][] = array(
                            'id'         => $category->categoryId,
                            'text'  => $category->categoryName);
                }
                $result[success] = true;
                $result[status] = "success";
            } catch (Exception $e) {

            }

            // echo result and return immediately
            header('Content-Type: application/json');
            echo json_encode($result, JSON_NUMERIC_CHECK);
            exit;
        }

        /**
         * [POST]
         * [PARAMS.id]
         * Update item base
         */
        public function updateItem () {
            $id = (int)$this->f3->get('PARAMS.id');
            $result = array(
                'success' => false,
                'message' => '',
                'total' => 0,
                'records' => array()
            );
            try {
                $item = new Item($this->db);

                // MySQL doesn't understand boolean -> need to convert boolean back to 1 / 0
                $this->f3->set('POST.isActive',     $this->toBool($this->f3->get('POST.isActive'))      ? 1 : 0);
                $this->f3->set('POST.isVegetarian', $this->toBool($this->f3->get('POST.isVegetarian'))  ? 1 : 0);
                $this->f3->set('POST.isPopular',    $this->toBool($this->f3->get('POST.isPopular'))     ? 1 : 0);
                $this->f3->set('POST.isSpecial',    $this->toBool($this->f3->get('POST.isSpecial'))     ? 1 : 0);

                // Get categoryId, accepted format: categoryId, coming format: category[id]
                $this->f3->set('POST.categoryId', $this->f3->get('POST.category[id]'));

                // Load data into memory and only update fields that have presence in POST
                $item->edit($id);

                $result[status]  = 'success';
                $result[success] = true;
                $result[message] = 'Successfully updated item';
            } catch (Exception $e) {
                $result[message] = 'Update failed. Please try again later';
            }
            header('Content-Type: application/json');
            echo json_encode($result, JSON_NUMERIC_CHECK);
            exit;
        }

        /*
        * [POST]
        * [PARAMS.name]
        * Add new category
        */
        public function addCategory () {

            // Get category name from PARAMS
            $categoryName = $this->f3->get('PARAMS.name');

            // setup result object
            $result = array (
                    'success'   => false,
                    'status'    => '',
                    'message'   => '',
                    'records'   => array());

            // Check if category name already exists
            $category = new Category($this->db);
            $existingCategory = $category->filterByName($categoryName);

            if ($existingCategory >= 1) {
                $result[status] = 'error';
                $result[message] = 'Category name already exists';
            } else {
                // try insert
                $this->f3->set('POST.categoryName', $categoryName);
                $category->add();

                // Change status and message
                $result[status] = 'success';
                $result[success] = true;
                $result[message] = 'Successfully added new category';

                // return new set of categories for client
                $categories = $category->all();
                foreach ($categories as $category) {
                    $result[records][] = array(
                            'id'    => $category->categoryId,
                            'text'  => $category->categoryName);
                }
            }

            header('Content-Type: application/json');
            echo json_encode($result, JSON_NUMERIC_CHECK);
            exit;
        }

        /*
        * [POST]
        * @Description Add new item
        */
        public function addItem () {
            $result = array (
                    'success'   => false,
                    'status'    => '',
                    'message'   => '',
                    'records'   => array());

            $item = new Item($this->db);
            $fields = $this->f3->get('POST.record');
            $this->f3->clear('POST');

            foreach ($fields as $field=>$value) {
                if ($field == 'category') {
                    $this->f3->set('POST.categoryId', $value[id]);
                } else if ($field == 'cafe') {
                    $this->f3->set('POST.cafeId', $value[id]);
                } else {
                    $postParam = "POST.$field";
                    $this->f3->set($postParam, $value);
                }
            }

            $item->add();
            $result[success]    = true;
            $result[status]     = 'success';
            $result[message]    = 'Added Item successfully';

            header('Content-Type: application/json');
            echo json_encode($result, JSON_NUMERIC_CHECK);
            exit;
        }

        /**
         * [POST]
         * Edit about us information
         */
        public function updateInfo () {
            $result = array(
                'success' => false,
                'message' => '',
                'total' => 0,
                'records' => array()
            );

            $info   = new Info($this->db);
            $fields = $this->f3->get('POST.record');
            $this->f3->clear('POST');

            foreach ($fields as $field => $value) {
                if ($field == 'isPriceOn') {
                    $this->f3->set('POST.isPriceOn', $this->toBool($value) ? 1 : 0);
                } else if ($field == 'isPromotionOn') {
                    $this->f3->set('POST.isPromotionOn', $this->toBool($value) ? 1 : 0);
                } else if ($field == 'isSpecialOn') {
                    $this->f3->set('POST.isSpecialOn', $this->toBool($value) ? 1 : 0);
                }else {
                    $postParam = 'POST.'.$field;
                    $this->f3->set($postParam, $value);
                }
            }
            $info->edit();

            $result[success] = true;
            $result[message] = 'Successfully edited Information';
            $result[status]  = 'success';

            header('Content-Type: application/json');
            echo json_encode($result, JSON_NUMERIC_CHECK);
            exit;
        }

        /*
        * [POST]
        * Retrieve Information for special board
        */
        public function getInfo () {
            // TODO:
            $result = array(
                'success' => false,
                'message' => '',
                'total' => 0,
                'record' => null
            );

            $info = new Info($this->db);
            $info->all();

            foreach ($info as $field => $value) {
                $result[record][$field] = $value;
            }

            $result[success] = true;
            $result[message] = 'Successfully retrieved data';
            $result[status]  = 'success';

            header('Content-Type: application/json');
            echo json_encode($result, JSON_NUMERIC_CHECK);
            exit;
        }

        /*
        * [POST]
        * Receive image data
        */
        public function receiveImage () {
            //$data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $data));
            $result = array(
                'success' => false,
                'message' => 'Upload error',
                'status'  => 'error',
                'total' => 0,
                'records' => array()
            );


            // Get filename and img base64 string from request
            $fileName   = $this->f3->get('PARAMS.fileName');
            $img        = $this->f3->get('POST.img');

            // Return error message if file size is too big. Hint: base64 increase file size ~33%
            if (strlen($img) > 512 * 1024 * 1.4) {
                $result[message] = 'File size too big. <b>Size:'.round(strlen($img)/1024).'KB</b>';
            }
            // Otherwise, proceed
            else {
                $url        = $this->f3->get('UPLOAD').$fileName;

                // Make sure file not already exists
                if (file_exists($url) == false) {

                    // Get file data to perform check if file is image
                    list($type, $data) = explode(';', $img);
                    if (in_array($type, array('data:image/png', 'data:image/jpg', 'data:image/gif', 'data:image/jpeg'))) {
                        list(, $data)      = explode(',', $data);
                        $data = base64_decode($data);
                        file_put_contents($url, $data);


                        // clear POST and copy variable to insert
                        $this->f3->clear('POST');
                        $this->f3->set('POST.imageName', $fileName);
                        $this->f3->set('POST.imageLink', $url);
                        $this->f3->set('POST.size', strlen($data));

                        // Add reference to db
                        $image = new ImageManager($this->db);
                        $image->add();

                        $result[success] = true;
                        $result[status]  = 'success';
                        $result[message] = 'Successfully uploaded. File size: '.(round(strlen($data)/1024)).'KB';
                    }
                } else {
                    $result[message] = 'File already exists';
                }
            }
            header('Content-Type: application/json');
            echo json_encode($result, JSON_NUMERIC_CHECK);
            exit;

        }

        /*
        * [POST]
        * Return images information to client
        */
        public function getImages () {
            $result = array(
                'success' => false,
                'message' => 'Error',
                'status'  => 'error',
                'total' => 0,
                'records' => array()
            );

            $images = new ImageManager($this->db);
            $images = $images->all();
//            var_dump($images);exit;
            foreach ($images as $image) {
                $result[records][] = array(
                    'recid'     => $image->imageId,
                    'imageName' => $image->imageName,
                    'imageLink' => $image->imageLink,
                    'isIncluded'=> $image->isIncluded,
                    'size'      => $image->size
                );
            }

            $result[success] = true;
            $result[status] = 'success';
            $result[message] = 'Successfully retrieved';
            $result[total]      = count($images);
            header('Content-Type: application/json');
            echo json_encode($result, JSON_NUMERIC_CHECK);
            exit;
        }

        /*
        * [POST]
        * Update status Include of images
        */
        public function updateImage () {
            // TODO:
            $result = array(
                'success' => false,
                'message' => '',
                'status'  => 'error',
                'total' => 0,
                'records' => array()
            );

            // Get id from url to pic record to edit
            $imageId = $this->f3->get('PARAMS.id');
            // Prepare parameters
            $this->f3->set('POST.isIncluded', $this->toBool($this->f3->get('POST.isIncluded')) ? 1 : 0 );

            // Edit image information
            $image = new ImageManager($this->db);
            $image->edit($imageId);

            $result[message] = 'Successfully edited.';
            $result[success] = true;
            $result[status]  = 'success';
            header('Content-Type: application/json');
            echo json_encode($result, JSON_NUMERIC_CHECK);
            exit;
        }

        /*
        * [POST]
        * Delete image with imageId
        */
        public function deleteImage () {
            $result = array(
                'success' => false,
                'message' => 'Delete error.',
                'status'  => 'error',
                'total' => 0,
                'records' => array()
            );

            $imageId = $this->f3->get('PARAMS.id');
            $imageRecords   = new ImageManager($this->db);

            $image = $imageRecords->getById($imageId);

            if (is_file($image[0]->imageLink)) {
                $success = unlink($image[0]->imageLink);
                if ($success) {
                    $imageRecords->delete($imageId);

                    $result[success] = true;
                    $result[message] = 'Deleted Successfully';
                    $result[status]  = 'success';
                }
            }

            header('Content-Type: application/json');
            echo json_encode($result, JSON_NUMERIC_CHECK);
            exit;
        }


        /**
         * [POST]
         * PARAMS: [user, password]
         */
        public function login() {
            header('Content-Type: application/json');
            $isSuccess = false;

            if ($this->f3->get('AJAX')) {
                $db = new \DB\SQL('mysql:host=localhost;dbname=gllow;port=3306', 'root', '');
                $user = new \DB\SQL\Mapper($db, 'users');
                $auth = new \Auth($user, array('id'=>'name',
                                               'pw'=>'password'));
                $loginResult = $auth->login($this->f3->get('POST.name'),
                                            $this->f3->get('POST.password')); // returns true on successful login
                if ($loginResult) {
                    $isSuccess = true;
                    $this->f3->set('SESSION.user', $this->f3->get('POST.name'));
                }
            }

            // return result and exit after authentication
            echo json_encode(array(
                'isSuccess'=>$isSuccess,
                'user'=>$this->f3->get('POST.name')));
            exit;
        }

        public function logout() {
            if ($this->f3->get('SESSION.user')) {
                $this->f3->clear('SESSION.user');
            }
            $this->f3->reroute('/admin');
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


        /**
         * Overriding afterroute to render admin layout
         */
        function afterroute() {
            if ($this->audit->isMobile()) {
                $this->f3->set('isMobile', true);
            }
            if ($this->f3->get('SESSION.user')) {
                $this->f3->set('isAdmin', true);
            }
            echo Template::instance()->render('admin/layout.html');
        }

        /**
         * Helper function
         * @param  mixed   $var Need to convert to boolean value
         * @return boolean
         */
        private function toBool($var) {
            if (!is_string($var)) return (bool) $var;
            switch (strtolower($var)) {
                case '1':
                case 'true':
                case 'on':
                case 'yes':
                case 'y':
                    return true;
                default:
                    return false;
            }
        }
    }
?>
