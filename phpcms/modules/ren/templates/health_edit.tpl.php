<?php defined('IN_ADMIN') or exit('No permission resources.'); ?>
<?php include $this->admin_tpl('header', 'admin'); ?>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH ?>formvalidator.js" charset="UTF-8"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH ?>formvalidatorregex.js" charset="UTF-8"></script>
<script type="text/javascript">
    <!--
    $(function () {
        $.formValidator.initConfig({
            autotip: true, formid: "myform", onerror: function (msg) {
            }
        });

        $("#name").formValidator({
            onshow: "<?php echo L('input') . L('姓名')?>",
            onfocus: "<?php echo L('请输入姓名')?>"
        }).inputValidator({
            min: 1,
            max: 20,
            onerror: "<?php echo L('姓名必填')?>"
        });

        $("#nation").formValidator({
            onshow: "<?php echo L('input') . L('民族')?>",
            onfocus: "<?php echo L('民族')?>"
        }).inputValidator({
            min: 2,
            max: 9,
            onerror: "<?php echo L('民族')?>"
        });


        $("#password").formValidator({
            onshow: "<?php echo L('input') . L('password')?>",
            onfocus: "<?php echo L('password') . L('between_6_to_20')?>"
        }).inputValidator({min: 6, max: 20, onerror: "<?php echo L('password') . L('between_6_to_20')?>"});

        $("#pwdconfirm").formValidator({
            onshow: "<?php echo L('input') . L('cofirmpwd')?>",
            onfocus: "<?php echo L('input') . L('passwords_not_match')?>",
            oncorrect: "<?php echo L('passwords_match')?>"
        }).compareValidator({
            desid: "password",
            operateor: "=",
            onerror: "<?php echo L('input') . L('passwords_not_match')?>"
        });


        $("#point").formValidator({
            tipid: "pointtip",
            onshow: "<?php echo L('input') . L('point') . L('point_notice')?>",
            onfocus: "<?php echo L('point') . L('between_1_to_8_num')?>"
        }).regexValidator({regexp: "^\\d{1,8}$", onerror: "<?php echo L('point') . L('between_1_to_8_num')?>"});
        $("#email").formValidator({
            onshow: "<?php echo L('input') . L('email')?>",
            onfocus: "<?php echo L('email') . L('format_incorrect')?>",
            oncorrect: "<?php echo L('email') . L('format_right')?>"
        }).inputValidator({
            min: 2,
            max: 32,
            onerror: "<?php echo L('email') . L('between_2_to_32')?>"
        }).regexValidator({
            regexp: "email",
            datatype: "enum",
            onerror: "<?php echo L('email') . L('format_incorrect')?>"
        }).ajaxValidator({
            type: "get",
            url: "",
            data: "m=people&c=people&a=public_checkemail_ajax",
            datatype: "html",
            async: 'false',
            success: function (data) {
                if (data == "1") {
                    return true;
                } else {
                    return false;
                }
            },
            buttons: $("#dosubmit"),
            onerror: "<?php echo L('deny_register') . L('or') . L('email_already_exist')?>",
            onwait: "<?php echo L('connecting_please_wait')?>"
        });
        $("#nickname").formValidator({
            onshow: "<?php echo L('input') . L('nickname')?>",
            onfocus: "<?php echo L('nickname') . L('between_2_to_20')?>"
        }).inputValidator({
            min: 2,
            max: 20,
            onerror: "<?php echo L('nickname') . L('between_2_to_20')?>"
        }).regexValidator({
            regexp: "ps_username",
            datatype: "enum",
            onerror: "<?php echo L('nickname') . L('format_incorrect')?>"
        }).ajaxValidator({
            type: "get",
            url: "",
            data: "m=people&c=index&a=public_checknickname_ajax",
            datatype: "html",
            async: 'false',
            success: function (data) {
                if (data == "1") {
                    return true;
                } else {
                    return false;
                }
            },
            buttons: $("#dosubmit"),
            onerror: "<?php echo L('already_exist') . L('already_exist')?>",
            onwait: "<?php echo L('connecting_please_wait')?>"
        }).defaultPassed();
    });


    //-->
</script>
<div class="pad-10">
    <div class="common-form">
        <form name="myform" action="?m=ren&c=health&a=edit" method="post" id="myform"  enctype="application/x-www-form-urlencoded">
            <fieldset>
                <legend><?php echo L('basic_configuration') ?></legend>
                <table width="100%" class="table_form">

                    <tr>
                        <td width="80"><?php echo L('姓名') ?></td>
                        <td id="name" ><?php echo $uinfo['title'] ?></td>
                    </tr>
                    <tr>
                        <td width="80"><?php echo L('医保序号') ?></td>
                        <td><?php echo $data['ybxh'] ?></td>
                    </tr>

                    <tr>
                        <td width="80"><?php echo L('医疗待遇') ?></td>
                        <td>
                            <select name="info[yldy]" id="selectAge">
                                <?php foreach ($xiala[40]['child'] as $key => $val ){  ?>
                                    <option value="<?php echo $val['id'] ?>"><?php echo $val['name'] ?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td width="80"><?php echo L('就诊医院') ?></td>
                        <td><textarea name="info[jzyy]" ><?php echo $data['jzyy'] ?></textarea></td>
                    </tr>
                    <tr>
                        <td width="80"><?php echo L('主要病症') ?></td>
                        <td><textarea name="info[zybz]" ><?php echo $data['zybz'] ?></textarea></td>
                    </tr>


                </table>
            </fieldset>
            <div class="bk15"></div>
            <input type="hidden" name="id" value="<?php echo $data['id'] ?>" >
            <input name="dosubmit" type="submit" id="dosubmit" value="<?php echo L('submit') ?>">
        </form>
    </div>
</div>
</body>
</html>


<script>
    $(function () {
        $(".rylx").change(function(){
           var id =  $(this).val();
            $.post("?m=ren&c=health&a=ajax_get_people&pc_hash=<?php echo $_SESSION['pc_hash'];?>",{id:id},function(data){
                $("#name").html(data);
            });
        });
    });
</script>