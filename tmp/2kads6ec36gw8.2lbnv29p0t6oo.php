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
                <label>Show Price On Menu:</label>
                <div><input name="isPriceOn" type="checkbox" class="w2ui-input1 w2ui-toggle"/><div><div></div></div></div> <i class="tgIndicator priceTgIndicator"></i>
            </div>
            <div class="w2ui-field">
                <label>Show Promotion:</label>
                <div><input name="isPromotionOn" type="checkbox" class="w2ui-input1 w2ui-toggle"/><div><div></div></div ></div> <i class="tgIndicator promotionTgIndicator"></i>
            </div>
            <div class="w2ui-field">
                <label>Show Chef Special:</label>
                <div><input name="isSpecialOn" type="checkbox" class="w2ui-input1 w2ui-toggle"/><div><div></div></div ></div> <i class="tgIndicator specialTgIndicator"></i>
            </div>
            <div class="w2ui-field">
                <label>Promotion Content <br/>(Content should not<br/>be more than 150<br /> characters):</label>
                <div>
                    <textarea name="promotion" type="text" style="width: 385px; height: 130px; resize: none"></textarea>
                </div>
            </div>
            <div class="w2ui-field">
                <label>About-Us Text:</label>
                <div>
                    <textarea name="about" type="text" style="width: 385px; height: 180px; resize: none"></textarea>
                </div>
            </div>
        </div>

        <div class="w2ui-buttons" style="text-align: left">
            <button class="btn btn-green" name="save">Save</button>
            <button class="btn btn-preview btn-blue" name="preview" title="View Website as a medium size WIDE screen laptop"><i class="fa fa-laptop"></i> Wide Screen(Med)</button>
            <button class="btn btn-preview btn-blue" name="preview2" title="View Website as a small size wide screen laptop"><i class="fa fa-laptop"></i> Wide Screen(Small)</button>
            <button class="btn btn-preview btn-blue" name="ipadLand" title="View Website as an iPad - Landscape"><i class="fa fa-tablet"></i> iPad (Land)</button>
            <button class="btn btn-preview btn-blue" name="ipadPort" title="View Website as an iPad - Portrait"><i class="fa fa-tablet"></i> iPad (Port)</button>
<!--
            <button class="btn btn-preview btn-blue" name="iphoneLand" title="Preview Website as an iPad - Portrait"><i class="fa fa-mobile-phone"></i> iPhone (Land)</button>
            <button class="btn btn-preview btn-blue" name="iphonePort" title="Preview Website as an iPad - Portrait"><i class="fa fa-mobile-phone"></i> iPhone (Port)</button>
-->
        </div>
    </div>
    <div id="alertContainer"></div>
</div>
