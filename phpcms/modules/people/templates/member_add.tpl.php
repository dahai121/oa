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

        $("#username").formValidator({
            onshow: "<?php echo L('input') . L('username')?>",
            onfocus: "<?php echo L('username') . L('between_2_to_20')?>"
        }).inputValidator({
            min: 2,
            max: 20,
            onerror: "<?php echo L('username') . L('between_2_to_20')?>"
        }).regexValidator({
            regexp: "ps_username",
            datatype: "enum",
            onerror: "<?php echo L('username') . L('format_incorrect')?>"
        }).ajaxValidator({
            type: "get",
            url: "",
            data: "m=people&c=people&a=public_checkname_ajax",
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
            onerror: "<?php echo L('deny_register') . L('or') . L('user_already_exist')?>",
            onwait: "<?php echo L('connecting_please_wait')?>"
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
        <form name="myform" action="?m=people&c=people&a=add" method="post" id="myform">
            <fieldset>
                <legend><?php echo L('basic_configuration') ?></legend>
                <table width="100%" class="table_form">
                    <tr>
                        <td width="80"><?php echo L('姓名') ?></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td width="80"><?php echo L('性别') ?></td>
                        <td><input type="text" name="info[sex]" class="input-text" id=""></input></td>
                    </tr>

                    <tr>
                        <td width="80"><?php echo L('民族') ?></td>
                        <td><input type="text" name="info[nation]" class="input-text" id="nation"></input></td>
                    </tr>

                    <tr>
                        <td width="80"><?php echo L('身份证') ?></td>
                        <td><input type="text" name="info[sfz]" class="input-text" id=""></input></td>
                    </tr>
                    <tr>
                        <td width="80"><?php echo L('手机') ?></td>
                        <td><input type="text" name="info[phone]" class="input-text" id=""></input></td>
                    </tr>
                    <tr>
                        <td width="80"><?php echo L('籍贯') ?></td>
                        <td><input type="text" name="info[jg]" class="input-text" id=""></input></td>
                    </tr>
                    <tr>
                        <td width="80"><?php echo L('家庭住址') ?></td>
                        <td><input type="text" name="info[address]" class="input-text" id=""></input></td>
                    </tr>
                    <tr>
                        <td width="80"><?php echo L('户口所在地') ?></td>
                        <td><input type="text" name="info[hksz]" class="input-text" id=""></input></td>
                    </tr>
                    <tr>
                        <td width="80"><?php echo L('地派出所') ?></td>
                        <td><input type="text" name="info[dpcs]" class="input-text" id=""></input></td>
                    </tr>
                    <tr>
                        <td width="80"><?php echo L('联系地址') ?></td>
                        <td><input type="text" name="info[nation]" class="input-text" id=""></input></td>
                    </tr>
                    <tr>
                        <td width="80"><?php echo L('入党日期') ?></td>
                        <td><?php echo form::date("info[rdtime]", $end_time)?></td>
                    </tr>
                    <tr>
                        <td width="80"><?php echo L('离退休时间') ?></td>
                        <td><?php echo form::date("info[ltxtime]", $end_time)?></td>
                    </tr>
                    <tr>
                        <td width="80"><?php echo L('离退休单位') ?></td>
                        <td><input type="text" name="info[ltxdw]" class="input-text" id=""></input></td>
                    </tr>
                    <tr>
                        <td width="80"><?php echo L('分管处室') ?></td>
                        <td><input type="text" name="info[fgcs]" class="input-text" id=""></input></td>
                    </tr>
                    <tr>
                        <td width="80"><?php echo L('现享受待遇') ?></td>
                        <td><input type="text" name="info[dy]" class="input-text" id=""></input></td>
                    </tr>
                    <tr>
                        <td width="80"><?php echo L('文化程度') ?></td>
                        <td><input type="text" name="info[education]" class="input-text" id=""></input></td>
                    </tr>
                    <tr>
                        <td width="80"><?php echo L('文革前曾') ?></td>
                        <td><input type="text" name="info[wgqz]" class="input-text" id=""></input></td>
                    </tr>
                    <tr>
                        <td width="80"><?php echo L('认最高职位') ?></td>
                        <td><input type="text" name="info[zgzw]" class="input-text" id=""></input></td>
                    </tr>
                    <tr>
                        <td width="80"><?php echo L('专业技术职位') ?></td>
                        <td><input type="text" name="info[zyjszw]" class="input-text" id=""></input></td>
                    </tr>
                    <tr>
                        <td width="80"><?php echo L('党内职务') ?></td>
                        <td><input type="text" name="info[dnzw]" class="input-text" id=""></input></td>
                    </tr>

                    <tr>
                        <td width="80"><?php echo L('档案号') ?></td>
                        <td><input type="text" name="info[dah]" class="input-text" id=""></input></td>
                    </tr>
                    <tr>
                        <td width="80"><?php echo L('备注') ?></td>
                        <td><input type="text" name="info[bz]" class="input-text" id=""></input></td>
                    </tr>

                    <!--tr>
                        <td><!--?php echo L('email') ?></td>
                        <td>
                            <input type="text" name="info[email]" value="" class="input-text" id="" size="30"></input>
                        </td>
                    </tr-->

                </table>
            </fieldset>

            <div class="bk15"></div>
            <input name="dosubmit" type="submit" id="dosubmit" value="<?php echo L('submit') ?>" class="dialog">
        </form>
    </div>
</div>
</body>
</html>