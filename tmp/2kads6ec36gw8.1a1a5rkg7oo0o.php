<form action="<?php echo $BASE.'/user/update'; ?>" method="post" class="form-horizontal">

    <div class="input-prepend">
        <span class="add-on"><i class="icon-user icon-black"></i> Name</span>
        <input type="text" id="name" name="name" value="<?php echo $POST['name']; ?>" class="input-xlarge" />
    </div><br/><br/>

    <div class="input-prepend">
        <span class="add-on"><i class="icon-envelope icon-black"></i> Supplier</span>
        <input type="text" id="email" name="email" value="<?php echo $POST['email']; ?>" class="input-xlarge" />
    </div>
    <br/><br/>

    <div class="input-prepend">
        <span class="add-on"><i class="icon-headphones icon-black"></i> Price</span>
        <input type="text" id="mobile" name="mobile" value="<?php echo $POST['mobile']; ?>" class="input-xlarge" />
    </div><br/><br/>

    <div class="input-prepend">
        <span class="add-on add-on-area "><i class="icon-home icon-black"></i> Description</span>
        <textarea row="5" id="address" name="address" class="input-xlarge"><?php echo $POST['address']; ?></textarea>
    </div><br/><br/>

    <div class="control-group">
        <div class="">
            <input type="hidden" name="id" value="<?php echo $POST['id']; ?>" />
            <input type="hidden" name="update" value="update" />
            <button type="submit" class="btn btn-primary"><i class="icon-edit icon-white"></i> Update</button>
        </div>
    </div>

</form>
