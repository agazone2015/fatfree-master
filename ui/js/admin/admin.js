/* globals $, window, w2ui, setTimeout, w2popup, console */
/* jshint eqeqeq:true */
/*
* HACKS & WORKAROUND USED:
*   01. css: tr[id$='rec_bottom']: hidden unwanted behavior of grid (empty rows at the end)
*   02. css: .w2ui-marker: stronger background text hightlighter color for UX
*   03. js:  (intent) side bar add: manual switching state of grid on and off when clicking items
*   04. js: richmond and malvern grid add buttons: only render form after 0.1s. Rendering immediately doesn't work
*   05. js: using multiple searches + searches together to disable [Seach....] next to search box
*   06. js: manually check changes as event onChange doesn't put changes into getChanges()
*   07. js: FIX 6: set timeout 100 will make things work
*   08. js - Grid: for list/ combo/ select field types in grid, items for editable must follow {id: 0, text: ''}
*   09. js - Grid: Calling grid.save() first will save some work on server
*   10. js: FIX 9: create a dummy object to store changes values and send as POST params
*   11. js - Grid: Saving records of categories in local variables instead of ajax variables to avoid empty category list
*/

/*====================
* [GLOBAL VARIABLES]
====================*/
var categories = [];
var imgExt      = ['image/jpg', 'image/png', 'image/jpeg', 'image/gif'];
window.URL = window.URL || window.webkitURL;

var Grid = {
    // Indicate current cafe displayed in grid
    current: 0,
    currentName: '',
    // Categories order
    categoryOrder: 1,
    // Local store for grid
    records: [],
    fetch: function (callback) {
        var grid = this;
        var url = 'admin/items/'+grid.current;
        $.post(url, {}, function (result){
            grid.records = result.records;
            if (typeof(callback) === 'function') callback();
        });
    },
    template: {
        header: '',
        name: 'Grid',
        show: {
            footer          : true,
            toolbar         : true, lineNumbers     : true,
            expandColumn    : false, emptyRecords   : false
        },
        multiSearch: false,
        searches: [
            { field: 'category.text',   caption: 'Search by Category',      type: 'text' },
            { field: 'itemName',        caption: 'Search by Name',          type: 'text' },
            { field: 'description',      caption: 'Search by Description',   type: 'text' }
        ],
        columns: [
            { field: 'category',  caption: 'Category',    size: '145px',
                    editable: { type: 'list', items: categories, showAll: true, inTag: 'Category name' },
                    render: function (r, i, c_i) {
                        return r.category.text || '???';
                    }},
            { field: 'itemName',    caption: 'Item Name',   size: '20%',  sortable: true, editable: {type: 'text'} },
            { field: 'description', caption: 'Description', size: '40%',  editable: {type: 'text'}},
            { field: 'price',       caption: 'Price',       size: '120px',  sortable: true, editable: {type: 'money'}, render: 'money'},
            { field: 'isActive',    caption: 'Active',      size: '60px', sortable: true, editable: {type: 'checkbox'} },
            { field: 'isVegetarian',caption: 'Vegetarian',  size: '90px', sortable: true, editable: {type: 'checkbox'} },
            { field: 'isPopular',   caption: 'Popular',     size: '80px', sortable: true, editable: {type: 'checkbox'} },
            { field: 'isSpecial',   caption: 'Special',     size: '80px', sortable: true, editable: {type: 'checkbox'} },
        ],
        toolbar: {
            items: [
                { id: 'Item',       type: 'button', caption: 'New Item',    icon: 'w2ui-icon-plus' },
                { id: 'Category',   type: 'button', caption: 'New Category',icon: 'w2ui-icon-plus' },
            ],
            onClick: function (event) {
                if (event.target === 'Item' || event.target === 'Category') {
                    Form.setup(event.target);
                }
            }
        },
        // for record changed event handling
        onChange: function (event) {
            var grid = this;

            // VERY HACKY
            // Set a timeout so JS has time to populate item.changes
            setTimeout(function () {
                // Get item to prepare
                var item = grid.get(event.recid);
                grid.lock('Loading ...', true);

                // create dummy object to store changed values
                var dumItem = JSON.parse(JSON.stringify(item));
                // process and copy changed values
                for (var c in dumItem.changes) {
                    if (dumItem.changes.hasOwnProperty(c)) {
                        dumItem[c] = dumItem.changes[c];
                    }
                }

                // Signal server to update
                $.ajax({
                    type: 'post', url: 'admin/update/'+item.recid,
                    data: $.param(dumItem),
                    success: function (result) {
                        if (result.success) {
                            setTimeout(function () {
                                grid.unlock();
                                grid.save();
                            }, 100);
                        }
                    },
                    error: function (result) {
                        grid.unlock();
                    }
                });
            }, 200);
        },
        onColumnClick: function(event) {
            if (event.field === 'category') {
                Grid.categoryOrder = -1 * Grid.categoryOrder;
                var order = Grid.categoryOrder;
                Grid.records.sort(function (a, b) {
                    return a.category.text > b.category.text ? (-1 * order) : order;
                });
                w2ui.Grid.refresh();
            }
        }
    },
    setup: function () {
        this.fetch(function () {
            w2ui.Grid.header = Grid.currentName;
            w2ui.Grid.records = Grid.records;
            w2ui.Grid.refresh();
            w2ui.layout.content('main', w2ui.Grid);
        });
    }
}

var config = {
    layout: {
        name: 'layout',
        padding: 0,
        panels: [
            { type: 'left', size: 200, resizable: true, minSize: 120, title: 'Gllow System' },
            { type: 'main', minSize: 550, overflow: 'hidden', title: 'Special' }
        ]
    },
    sidebar: {
        name: 'sidebar',
        nodes: [
            { id: 'gllow', text: 'Gllow', group: true, expanded: true, nodes: [
                { id: 'special', text: 'Special', img: 'icon-folder', selected: true },
                { id: 1, text: 'Richmond', img: 'icon-folder'},
                { id: 2, text: 'Malvern', img: 'icon-folder' },
            ]}
        ],
        onClick: function (event) {
            w2ui.layout.get('main').title = w2ui.sidebar.get(event.target).text;
            var id = parseInt(event.target);
            if (id !== 0 && !isNaN(id)) {
                Grid.current = id;
                Grid.currentName = w2ui.sidebar.get(id).text;
                Grid.setup();
            } else {
                w2ui.layout.content('main', w2ui.mainPanel);
            }
        }
    },
};

var Form = {
    setup: function (formType) {
        // Prepare title for popup
        var title   = 'Add new '+ formType + ' to ' + Grid.currentName;
        // Prepare form name to select
        var form    = 'New' + formType + 'Form';

        // Open popup
        w2popup.open({
            title: title, body: '<div id="NewForm"></div>',
            width: 500, height: 400, speed: 0,
            showClose: true, modal: true, showMax: false,
            onOpen: function (event) {
                // HACKY: set timeout so popup will be ready before rendering form
                setTimeout(function (){
                    w2ui[form].clear();
                    $('#NewForm').w2render(form);
                }, 100);
            },
            style: 'position: relative;'
        });
    }
}


/**
 * [LOCAL]
 * @param {Array} records Category list {id: int, text: string}
 */
function updateCategoriesFromVariable (records) {

    // objects passed by references -> need to store in another variable as records is temporary
    categories = records;
    w2ui.Grid.columns[0].editable.items = categories;
    w2ui.Grid.refresh();


    if (w2ui.NewItemForm !== null) {
        w2ui.NewItemForm.set('category', {options: {items: categories}});
    }
}

// Register new item form
$().w2form({
    name: 'NewItemForm',
    url    : 'admin/item/add',
    width: 500, height: 400,
    fields : [
        { field: 'itemName',    type: 'text',   required: true, html: { caption: 'Item Name',   attr: 'style="width: 200px;"' } },
        { field: 'description', type: 'text',                   html: { caption: 'Description', attr: 'style="width: 200px"' } },
        { field: 'price',       type: 'money',  required: true, html: { caption: 'Price',       attr: 'style="width: 200px"' } },
        { field: 'category',    type: 'list',   required: true, html: { caption: 'Category',    attr: 'style="width: 200px"'},
                options: { items: []}},
        { field: 'cafe',        type: 'list',   required: true, html: { caption: 'Cafe Name',   attr: 'style="width: 200px;"'},
                options: {items: [{id: 1, text: 'Richmond'}, {id: 2, text: 'Malvern'}]}},
        { field: 'isVegetarian',type: 'checkbox',               html: { caption: 'Vegetarian'}},
        { field: 'isSpecial',   type: 'checkbox',               html: { caption: 'Special'}},
    ],
    actions: {
        'Add Item': function (event) {
            event.preventDefault();
            var form = this;
            this.submit(function (result){
                if (result.success) {
                    form.clear();
                    w2popup.message({
                        width: 500, height: 400,
                        html: ('<div class="ajaxMsg"><div class="ajaxMsgContent">{0}</div><div class="ajaxMsgControl"><button class="btn btn-green center-y" onclick="w2popup.message(); w2popup.close();">Close</button></div></div>'
                              .format(result.message))
                    });
                }
            });
        },
        Reset: function (event) {
            this.clear();
        },
        Close: function (event) {
            w2popup.close();
        }
    },
    style: 'position: realative; top: 50%; transform: translateY(-50%);'
});

// Register new category form
$().w2form({
    name: 'NewCategoryForm',
    width: 500, height: 400,
    fields: [
        { field: 'categoryName', type: 'text', required: true, html: { caption: 'Category Name', attr: 'style="width: 200px;"' }},
    ],
    actions: {
        'Add Category': function (event) {
            // TODO: add ajax to add category
            var name = this.get('categoryName').$el.val();
            var form = this;
            this.url = 'admin/category/add/'+name;
            this.submit(function (result) {
                if (result.success) {
                    updateCategoriesFromVariable(result.records);
                    form.clear();
                    w2popup.message({
                        width: 500, height: 400,
                        html: ('<div class="ajaxMsg"><div class="ajaxMsgContent">{0}</div><div class="ajaxMsgControl"><button class="btn btn-green center-y" onclick="w2popup.message(); w2popup.close();">Close</button></div></div>'
                              .format(result.message))
                    });
                }
            });
        }
    },
    style: 'position: realative; top: 50%; transform: translateY(-50%);'
});

// Register special form for main Panel
$('#specialForm').w2form({
    name  : 'specialForm',
    url   : 'admin/about',
    fields: [
        { field: 'about',           type: 'text', },
        { field: 'isPriceOn',       type: 'toggle'},
        { field: 'isPromotionOn',   type: 'toggle'},
        { field: 'isSpecialOn',     type: 'toggle'}
    ],
    actions: {
        reset: function () {
            this.clear();
        },
        save: function () {
            this.save();
        },
        preview: function () {
            // display preview;
            window.open('http://localhost/fatfree-master/', '', 'menubar=0, width=500, height=500, location=0,')
        }
    },
    style: 'border-radius: 0;',
    onChange: function (event) {
        if (event.target === 'isPriceOn') {
            $('.priceTgIndicator').text(event.value_new ? 'On' : 'Off');
        } else if (event.target === 'isPromotionOn') {
            $('.promotionTgIndicator').text(event.value_new ? 'On' : 'Off');
        } else if (event.target === 'isSpecialOn') {
            $('.specialTgIndicator').text(event.value_new ? 'On' : 'Off');
        }
    },
    onRender: function () {
        $.get('admin/about', function (result){
            if (result.success) {
                w2ui.specialForm.record = result.record;
                $('.priceTgIndicator').text(result.record.isPriceOn ? 'On' : 'Off');
                $('.promotionTgIndicator').text(result.record.isPromotionOn ? 'On' : 'Off');
                $('.specialTgIndicator').text(result.record.isSpecialOn ? 'On' : 'Off');
                w2ui.specialForm.refresh();
            }
        });
    }
});

// Register upload form for main panel
$('#uploadForm').w2form({
    name  : 'uploadForm',
    url   : 'admin/upload',
    fields: [
        { field: 'file', type: 'file', require: true,
            html: {caption: 'Files upload:', attr: 'style="min-height: 200px; width: 100%;"' },
            maxFileSize: 2 * 1024 * 1024,
            hint: 'Drag or Click to select'},
    ],
    actions: {
        upload: function () {
            //this.save();
        },
        cancel: function () {
            // display preview;
            //window.open('http://localhost/fatfree-master/', '', 'menubar=0, width=500, height=500, location=0,')
        }
    },
    style: 'height: 300px;'
});

// Register image management panel
var ImageGrid = {
    delete: function (recid) {
        // Prepare url for deleting record
        var url = 'admin/delete/image/'+recid;
        $.post(url, {}, function (result) {
            // If deleted unsuccessfully, alert user
            if (!result.success) {
                w2popup.close();
                w2popup.open({
                    title   : 'Delete Picture',
                    speed   : 0,
                    body    : '<span style="display: inline-block; left: 50%; transform: translateX(-50%);" class="center-y center-x">'+result.message+'</span>',
                    buttons : '<button class="btn btn-blue" onclick="w2popup.close()">Close</button>'
                });
            }
            // Otherwise, remove record
            else {
                w2popup.close();
                ImageGrid.setup();
                //w2ui.ImageGrid.reload();
            }
        });
    },
    prompt: function (recid) {
        w2popup.close();
        w2popup.open({
            title   : 'Delete Picture',
            speed   : 0,
            body    : '<span style="display: inline-block; left: 50%; transform: translateX(-50%);" class="center-y center-x">Are you sure want to delete?</span>',
            buttons : '<button class="btn btn-orange" onclick="ImageGrid.delete('+recid+')">Yes</button> <button class="btn" onclick="w2popup.close()">No</button>',
            onOpen  : function (event) {
            }
        });
    },
    record: [],
    fetch: function (callback) {
        var grid = this;
        var url = 'admin/images';
        $.post(url, {}, function (result){
            grid.records = result.records;
            if (typeof(callback) === 'function') callback();
        });
    },
    template: {
        header: '',
        name: 'ImageGrid',
        show: {
            footer          : true,
            toolbar         : false, lineNumbers    : true,
            expandColumn    : false, emptyRecords   : false
        },
        columns: [
            { field: 'imageLink',  caption: 'Thumbnail (Click to enlarge)',    size: '215px',
                    render: function (r, i, c_i) {
                        return '<img src="'+r.imageLink+'" class="gridThumbnail" alt="imgage"/>';
                    }},
            { field: 'imageName',   caption: 'Image Name',   size: '40%',  sortable: true },
            { field: 'size',        caption: 'Image Size',   size: '10%', sortable: true,
                    render: function (r, i, c_i) {
                        return '' + Math.round(r.size / 1024) + 'KB';
                    }
            },
            { field: 'isIncluded',   caption: 'Show In Menu', size: '120px', sortable: true, editable: {type: 'checkbox'}},
            { field: 'remove',      caption: '',        size: '90px',
                    render: function (r, i, c_i) {
                        return '<button class="btn" onclick="ImageGrid.prompt('+r.recid+')">Delete</button>';
                    }
            }
        ],
        // for record changed event handling
        onChange: function (event) {
            console.log('event:', event);
            console.log('w2ui.ImageGrid.getChanges():', w2ui.ImageGrid.getChanges());
            var grid = this;

            // VERY HACKY
            // Set a timeout so JS has time to populate item.changes
            setTimeout(function () {
                // Get item to prepare
                var item = grid.get(event.recid);
                grid.lock('Loading ...', true);

                // create dummy object to store changed values
                var dumItem = { };// = JSON.parse(JSON.stringify(item));
                // process and copy changed values
                for (var c in item.changes) {
                    if (item.changes.hasOwnProperty(c)) {
                        dumItem[c] = item.changes[c];
                    }
                }

                // Signal server to update
                $.ajax({
                    type: 'post', url: 'admin/images/'+item.recid,
                    data: $.param(dumItem),
                    success: function (result) {
                        if (result.success) {
                            setTimeout(function () {
                                grid.unlock();
                                grid.save();
                            }, 100);
                        }
                    },
                    error: function (result) {
                        grid.unlock();
                    }
                });
            }, 200);
        },
    },
    setup: function () {
        this.fetch(function () {
            w2ui.ImageGrid.records = ImageGrid.records;
            w2ui.ImageGrid.refresh();
            w2ui.mainPanel.content('main', w2ui.ImageGrid);
        })
    }
}


$(function () {
    // initialization
    $('#mainContainer').w2layout(config.layout);
    w2ui.layout.content('left', $().w2sidebar(config.sidebar));
    // Register Grid in memory
    $().w2grid(Grid.template);
    $().w2grid(ImageGrid.template);
    // Event listener for images click in ImageGrid
    $(document).on('click', '.gridThumbnail', function (e) {
        var $$ = this;
        $.colorbox({
            html: '<div style="position: relative; text-align: center; padding: 0 5px;"><img src="'+$$.src+'" class="imageFullsize" /><div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"></div></div>',
            maxHeight: '90%',
            maxWidth: '90%',
        });
    });

    // Initiate main content with Special panel
    // ========================================
    //w2ui.sidebar.select('special');
    $('#node_special').click();
    // Register main panel
    var pstyle = 'border: 1px solid #dfdfdf; padding: 5px;';
    $().w2layout({
        name: 'mainPanel',
        panels: [
            { type: 'main', style: pstyle + 'border-top: 0px;', content: w2ui.specialForm,
                toolbar: {
                    items: [
                        { type: 'radio',  id: 'specialForm',    group: '1', caption: 'Webpage Control',     img: 'fa fa-gears tb-icon', checked: true },
                        { type: 'radio',  id: 'imagePanel',     group: '1', caption: 'Image Control',       img: 'fa fa-picture-o tb-icon'},
                        { type: 'radio',  id: 'upload',         group: '1', caption: 'Upload Image',        img: 'fa fa-upload tb-icon'},
                        { type: 'break',  id: 'break0' },
                        { type: 'menu',   id: 'item4', caption: 'Drop Down', img: 'icon-folder', items: [
                            { text: 'Item 1', icon: 'icon-page' },
                            { text: 'Item 2', icon: 'icon-page' },
                            { text: 'Item 3', value: 'Item Three', icon: 'icon-page' }
                        ]},
                        { type: 'break', id: 'break1' },
                        { type: 'spacer' },
                    ],
                    onClick: function (event) {
                        if (event.target === 'specialForm') {
                            w2ui.mainPanel.content('main', w2ui.specialForm);
                        } else if (event.target === 'imagePanel') {
                            ImageGrid.setup();
                        } else if (event.target === 'upload') {
                            w2ui.mainPanel.content('main', '<div style="height: 100%; width=100%;" id="uploadManager"></div>');
                            setupImageUploadPanel();
                        }
                        //this.owner.content('main', w2ui[event.target]);
                    },
                    style: 'border-left: 1px solid silver; border-right: 1px solid silver;',
                },
            }
        ],
    });
    w2ui.layout.content('main', w2ui.mainPanel);
    // ========================================

    /*
    * [AJAX POST]
    * Get categories and store in local variable
    */
    function updateCategories () {
        $.ajax({
            type: 'post', url: 'admin/categories',
            success: function (result) {
                if (result.success) {
                    updateCategoriesFromVariable(result.records);
                }
            }
        });
    }
    updateCategories();
});

/**
 * Setup image upload panel when user click on upload option in main panel toolbar
 */
function setupImageUploadPanel () {
    try {
    // Todo list
        $('#uploadManager').append(
        '<div id="uploadButtonBar"><button class="btn btn-green" id="uploadBtn">Upload</button><button class="btn" id="clearBtn">Clear</button></div>' +
        '<div id="uploadForm">' +
            '<input id="file" type="file" style="display:none" accept=".jpg,.jpeg,.png,.gif" onchange="handleFiles(this.files)" multiple>' +
            '<div id="fileHandler">'+
                '<span class="uploadHint center-y"><i class="fa fa-cloud-upload"></i> Click To Select Or Drag Files To Upload.<br/>Formats accept: PNG, JPG, JPEG, GIF<br/>File size should be less than 500KB</span>'+
            '</div>' +
            '<div id="fileSelected"><span><i class="fa fa-picture-o"></i> Image(s) selected:<span>' + '<ul id="fileSelectedList"></ul>' +
            '</div>' +
        '</div>'
        );
        $('#fileHandler')[0].addEventListener('dragenter', function (e) {
            e.stopPropagation(); e.preventDefault();
        }, false);
        $('#fileHandler')[0].addEventListener('dragover', function (e) {
            e.stopPropagation(); e.preventDefault();
        })
        $('#fileHandler')[0].addEventListener('drop', function (e) {
            e.stopPropagation(); e.preventDefault();
            var files = Array.prototype.slice.call(e.dataTransfer.files);
            var validated = false;
            var accepted = [];


            for (var i = 0; i < files.length; i++) {
                if (imgExt.indexOf(files[i].type) != -1) {
                    validated = true;
                    accepted.push(files[i]);
                }
            };
            if (!validated) {
                // todo: alert no files selected
                w2popup.open({title: 'No file Selected', body: 'No file selected', showMax: false});
                return;
            }
            handleFiles(files);
        });
        $('#fileHandler').click(function () {
            $('#file').click();
        });
        $('#uploadBtn').click(function () {
            uploadFiles();
        });
        $('#clearBtn').click(function () {
            $('#fileSelectedList').html('');
        });
    } catch (e) {
        console.log('exception:', e);
    }
}

function handleFiles(files) {
    if (!files.length) {
        // todo: display no files selected fileList.innerHTML = "<p>No files selected!</p>";
    } else {
        var list = document.getElementById('fileSelectedList');
        list.innerHTML = '';
        for (var i = 0; i < files.length; i++) {
            // ignore if file size more than 500KB
            if (files[i].size > 500 * 1024) continue;

            // create li element to display image information
            var li = document.createElement("li");
            li.classList.add('thumbnail');
            list.appendChild(li);

            // create a span to display image information
            var info = document.createElement("span");
            info.innerHTML = '<span><i class="fa fa-file-text-o"></i> ' + files[i].name + ": " + Math.round(files[i].size / 1024) + " KB";

            // create img element to display image an store file reference
            var img = document.createElement("img");
            img.src = window.URL.createObjectURL(files[i]);
            img.height = 60;
            img.file = files[i];
            img.onload = function () {
                window.URL.revokeObjectURL(this.src);
            }
            var ael = document.createElement('a');
            var iel = document.createElement('i');
            iel.className = 'fa fa-times';
            ael.setAttribute('onclick', '$(this).parent().remove()');
            ael.setAttribute('class', 'thumb-remove ');
            ael.appendChild(iel);

            li.appendChild(info);
            li.appendChild(img);
            li.appendChild(ael);
        }
    }
}

/**
 * Read file from list in #fileSelectedList and ajax each
 */
function uploadFiles () {
    var files = [];
    var list = $('#fileSelectedList').children();
    for (var i = 0; i < list.length; i++) {
        var img = $(list[i]).find('img')[0];
        files.push({img: img, file: img.file});
    }
    //console.log('files:', files);
    // only start uploading if files are real
    if (files.length > 0) {
        for (var i = 0; i < files.length; i++) {
            /*new FileUploader(files[i].img, files[i].img.file);*/
            (function (filePackage, timeout) {
                setTimeout(function () {
                    UploadBase64(filePackage);
                },timeout);
            })(files[i], i*500)
        }
    }
}

function UploadBase64 (filePackage) {
    var file = filePackage.file;
    var img  = filePackage.img;
    var fileListContainer = $('#fileSelectedList')[0];
    var fileReader = new FileReader();

    fileReader.onload = function(fileLoadedEvent) {
        var srcData = fileLoadedEvent.target.result; // <--- data: base64

        // Prepare upload url
        var url = 'admin/upload/' + file.name;

        // Prepare visual element for tracking
        var progressEl = $('<div class="uploadProgress"><a onclick="$(this).parent().remove()"><i class="fa fa-times"></i></a><span class="uploadName"></span><div class="uploadTrack"><div class="uploadBar"></div></div></div>');

        // Proceed
        $.ajax({
            type: 'post', url: url,
            data: {img: srcData},
            beforeSend: function () {
                progressEl.appendTo('#alertContainer').find('.uploadName').text(file.name);
            },
            xhr: function () {
                var xhr = new XMLHttpRequest();
                xhr.upload.addEventListener('progress', function (e) {
                    if (e.lengthComputable) {
                        var complete = e.loaded / e.total;
                        var width = progressEl.width();
                        progressEl.find('.uploadBar').width(complete * width);
                        progressEl.find('.uploadName').html('<em>'+complete * 100 + '%</em> ' + file.name + ' ');
                    }
                });
                return xhr;
            },
            success: function (result) {
                // remove visual element when upload complete
                if (!result.success) {
                    progressEl.addClass('error').append(result.message).find('.uploadTrack').remove();
                    progressEl.find('.uploadName em').remove();
                } else {
                    progressEl.addClass('done').fadeOut(3350, function (e) {
                        $(this).remove();
                    });
                    $(img.parentNode).fadeOut(2500, function (e) {
                        $(this).remove();
                    });
                }
            }
        });
    }
    fileReader.readAsDataURL(file);
}

function FileUploader(img, file) {
    var reader = new FileReader();
//    this.ctrl = createThrobber(img);
    var xhr = new XMLHttpRequest();
    this.xhr = xhr;

    var self = this;
    this.xhr.upload.addEventListener("progress", function (e) {
        if (e.lengthComputable) {
            var percentage = Math.round((e.loaded * 100) / e.total);
//            self.ctrl.update(percentage);
        }
    }, false);

    xhr.upload.addEventListener("load", function (e) {
//        self.ctrl.update(100);
//        var canvas = self.ctrl.ctx.canvas;
//        canvas.parentNode.removeChild(canvas);
    }, false);
    xhr.open("POST", "admin/upload");
    xhr.overrideMimeType('text/plain; charset=x-user-defined-binary');
    reader.onload = function (evt) {
        xhr.sendAsBinary(evt.target.result);
    };
    reader.readAsBinaryString(file);
}

/**
 * Helper function
 * @returns {String} Format current string with parameters
 */
String.prototype.format = function () {
    var formatted = this;
    for (var i = 0; i < arguments.length; i++) {
        var regexp = new RegExp('\\{' + i + '\\}', 'gi');
        formatted = formatted.replace(regexp, arguments[i]);
    }
    return formatted;
};
