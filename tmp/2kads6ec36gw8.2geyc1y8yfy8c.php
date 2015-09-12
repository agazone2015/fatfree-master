<form action="<?php echo $BASE.'/user/create'; ?>" method="post" class="form-horizontal">

    <div class="input-prepend">
        <span class="addon"><i class="icon-user icon-black"></i> Name</span>
        <input type="text" id="name" name="name" value="" class="input-xlarge" />
    </div>
    <br/><br/>

    <div class="input-prepend">
        <span class="add-on"><i class="icon-envelope icon-black"></i> Supplier</span>
        <input type="text" id="email" name="email" value="" class="input-xlarge" />
    </div>
    <br/><br/>

    <div class="input-prepend">
        <span class="add-on"><i class="icon-headphones icon-black"></i> Price</span>
        <input type="text" id="mobile" name="mobile" value="" class="input-xlarge" />
    </div><br/><br/>

    <div class="input-prepend">
        <span class="add-on add-on-area "><i class="icon-home icon-black"></i> Description</span>
        <textarea row="5" id="address" name="address" class="input-xlarge"></textarea>
    </div><br/><br/>

    <div class="control-group">
        <div class="">
            <input type="hidden" name="create" value="create" />
            <button type="submit" class="btn btn-primary"><i class="icon-ok icon-white"></i> Add Item</button>
        </div>
    </div>

</form>
