<script type="text/javascript">
    //var categories = [{id:1,text:"Soup"},{id:2,text:"Box"}] //<?php echo $categories; ?>;
</script>
<div id="topbar">
    <form action="<?php echo $BASE.'/admin/logout'; ?>" method="post" id="logoutForm">
        <button type="submit">Logout</button>
    </form>
</div>
<div id="content">
    <div id="mainContainer">

    </div>
    <div id="extra" style="height: 400px"></div>
    <div id="specialForm" style="width: 750px;">
        <div class="w2ui-page page-0">
            <div class="w2ui-field">
                <label>Price on Menu page:</label>
                <div><input name="isPriceOn" type="checkbox" class="w2ui-input1 w2ui-toggle"/><div><div></div></div></div> <i class="tgIndicator priceTgIndicator"></i>
            </div>
            <div class="w2ui-field">
                <label>Show Promotion:</label>
                <div><input name="isPromotionOn" type="checkbox" class="w2ui-input1 w2ui-toggle"/><div><div></div></div ></div> <i class="tgIndicator promotionTgIndicator"></i>
            </div>
            <div class="w2ui-field">
                <label>Show Special:</label>
                <div><input name="isSpecialOn" type="checkbox" class="w2ui-input1 w2ui-toggle"/><div><div></div></div ></div> <i class="tgIndicator specialTgIndicator"></i>
            </div>
            <div class="w2ui-field">
                <label>About-Us Text:</label>
                <div>
                    <textarea name="about" type="text" style="width: 385px; height: 180px; resize: none"></textarea>
                </div>
            </div>
        </div>

        <div class="w2ui-buttons">
            <button class="btn btn-green" name="save">Save</button>
            <button class="btn btn-blue btn-left" name="preview">Preview</button>
            <button class="btn btn-orange" name="reset">Reset</button>
        </div>
    </div>
    <div id="alertContainer"></div>
</div>
