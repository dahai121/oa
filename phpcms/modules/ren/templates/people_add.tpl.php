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
            onshow: "<?php echo L('input') . L('����')?>",
            onfocus: "<?php echo L('����������')?>"
        }).inputValidator({
            min: 1,
            max: 20,
            onerror: "<?php echo L('��������')?>"
        });

        $("#nation").formValidator({
            onshow: "<?php echo L('input') . L('����')?>",
            onfocus: "<?php echo L('����')?>"
        }).inputValidator({
            min: 2,
            max: 9,
            onerror: "<?php echo L('����')?>"
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
        <form name="myform" action="?m=ren&c=master&a=add" method="post" id="myform"  enctype="application/x-www-form-urlencoded">
            <fieldset>
                <legend><?php echo L('basic_configuration') ?></legend>
                <table width="100%" class="table_form">

                    <tr>
                        <td width="80"><?php echo L('��Ա����') ?></td>
                        <td>
                            <select name="info[rylx]" class="rylx" id="selectAge">
                                <option value=""></option>
                                <?php foreach ($xiala[76]['child'] as $key => $val ){  ?>
                                    <option value="<?php echo $val['id'] ?>"><?php echo $val['name'] ?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td width="80"><?php echo L('����') ?></td>
                        <td><input type="text" name="info[name]" class="input-text" id="name"></input></td>
                        <td width="80"><?php echo L('�Ա�') ?></td>
                        <td>
                            <label><input name="info[sex]" type="radio" value="��" />�� </label>
                            <label><input name="info[sex]" type="radio" value="Ů" />Ů </label>
                        </td>
                    </tr>

                    <tr>
                        <td width="80"><?php echo L('��������') ?></td>
                        <td><input type="text" name="info[birthday]" class="input-text" id="name"></input></td>
                        <td width="80"><?php echo L('���֤��') ?></td>
                        <td><input type="text" name="info[sfz]" class="input-text" id=""></input></td>
                    </tr>

                    <tr>
                        <td width="80"><?php echo L('����') ?></td>
                        <td><input type="text" name="info[nation]" class="input-text" id="nation"></input></td>
                        <td width="80"><?php echo L('������ò') ?></td>
                        <td>
                            <select name="info[zzmm]" id="selectAge">
                                <?php foreach ($xiala[50]['child'] as $key => $val ){  ?>
                                    <option value="<?php echo $val['id'] ?>"><?php echo $val['name'] ?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td width="80"><?php echo L('����') ?></td>
                        <td><input type="text" name="info[jg]" class="input-text" id=""></input></td>
                        <td width="80"><?php echo L('��ͥסַ') ?></td>
                        <td><input type="text" name="info[address]" class="input-text" id=""></input></td>
                    </tr>

                    <tr>
                        <td width="80"><?php echo L('�������ڵ��ɳ���') ?></td>
                        <td><input type="text" name="info[pcs]" class="input-text" id=""></input></td>
                        <td width="80"><?php echo L('�ֻ�') ?></td>
                        <td><input type="text" name="info[phone]" class="input-text" id=""></input></td>
                    </tr>

                    <tr>
                        <td width="80"><?php echo L('�ʱ�') ?></td>
                        <td><input type="text" name="info[yb]" class="input-text" id=""></input></td>
                        <td width="80"><?php echo L('��ϵ��ַ') ?></td>
                        <td><input type="text" name="info[lxdz]" class="input-text" id=""></input></td>
                    </tr>

                    <tr>
                        <td width="80"><?php echo L('ԭ��λ') ?></td>
                        <td>
                            <select name="info[ydw]" id="selectAge">
                                <?php foreach ($xiala[80]['child'] as $key => $val ){  ?>
                                    <option value="<?php echo $val['id'] ?>"><?php echo $val['name'] ?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td width="80"><?php echo L('�Ļ��̶�') ?></td>
                        <td>
                            <select name="info[education]" id="selectAge">
                                <?php foreach ($xiala[30]['child'] as $key => $val ){  ?>
                                    <option value="<?php echo $val['id'] ?>"><?php echo $val['name'] ?></option>
                                <?php } ?>
                            </select>
                        </td>
                        <td width="80"><?php echo L('�뵳ʱ��') ?></td>
                        <td><input type="text" name="info[rdtime]" class="input-text" id="name"></input></td>
                    </tr>

                    <tr>
                        <td width="80"><?php echo L('ת��ʱ��') ?></td>
                        <td><input type="text" name="info[zztime]" class="input-text" id="name"></input></td>
                        <td width="80"><?php echo L('����ʱ��') ?></td>
                        <td><input type="text" name="info[gztime]" class="input-text" id="name"></input></td>
                    </tr>

                    <tr>

                        <td width="80"><?php echo L('ְ��') ?></td>
                        <td>
                            <select name="info[zj]" id="selectAge">
                                <?php foreach ($xiala[60]['child'] as $key => $val ){  ?>
                                    <option value="<?php echo $val['id'] ?>"><?php echo $val['name'] ?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td width="80"><?php echo L('רҵ����ְλ') ?></td>
                        <td><input type="text" name="info[zyjszw]" class="input-text" id=""></input></td>
                    </tr>

                    <tr>
                        <td width="80"><?php echo L('רҵְ�񼶱�') ?></td>
                        <td>
                            <select name="info[zyzwjb]" id="selectAge">
                                <?php foreach ($xiala[65]['child'] as $key => $val ){  ?>
                                    <option value="<?php echo $val['id'] ?>"><?php echo $val['name'] ?></option>
                                <?php } ?>
                            </select>
                        </td>
                        <td width="80"><?php echo L('�ֹܴ���') ?></td>
                        <td>
                            <select name="info[fgcs]" id="selectAge">
                                <?php foreach ($xiala[68]['child'] as $key => $val ){  ?>
                                    <option value="<?php echo $val['id'] ?>"><?php echo $val['name'] ?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td width="80"><?php echo L('����ְ��') ?></td>
                        <td><input type="text" name="info[dnzw]" class="input-text" id=""></input></td>
                        <td width="80"><?php echo L('���ڵ�֧��') ?></td>
                        <td>
                            <select name="info[szdzb]" id="selectAge">
                                <?php foreach ($xiala[71]['child'] as $key => $val ){  ?>
                                    <option value="<?php echo $val['id'] ?>"><?php echo $val['name'] ?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td width="80"><?php echo L('������') ?></td>
                        <td><input type="text" name="info[dah]" class="input-text" id=""></input></td>
                        <td width="80"><?php echo L('ְ��') ?></td>
                        <td><input type="text" name="info[zw]" class="input-text" id=""></input></td>
                    </tr>

                    <tr>
                        <td width="80"><?php echo L('email') ?></td>
                        <td><input type="text" name="info[email]" class="input-text" id=""></input></td>

                        <td width="80"><?php echo L('ͷ��') ?></td>
                        <td>
                          <?php echo form::images('info[pic]', 'photo', '', 'ren')?>
                        </td>
                    </tr>

                    <tr>
                        <td width="80"><?php echo L('��ע') ?></td>
                        <td>
                            <textarea name="info[bz]" style="width:200px;height:80px;"></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td width="80"><?php echo L('����') ?></td>
                        <td>
                            <textarea name="info[jl]" style="width:200px;height:80px;"></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td width="80"><?php echo L('����') ?></td>
                        <td>
                            <select name="info[lx]" id="selectAge">
                                <?php foreach ($xiala[74]['child'] as $key => $val ){  ?>
                                    <option value="<?php echo $val['id'] ?>"><?php echo $val['name'] ?></option>
                                <?php } ?>
                            </select>

                        </td>
                    </tr>
                    <div >
                    <tr id="ltx">
                        <td width="80" class="ltx"  ><?php echo L('��/���ݵ�λ') ?></td>
                        <td class="ltx"><input type="text" name="info[ltxdw]" class="input-text" id=""></input></td>
                        <td width="80" class="ltx"><?php echo L('��/����ʱ��') ?></td>
                        <td class="ltx"><input type="text" name="info[ltxtime]" class="input-text" id="name"></input></td>
                    </tr>
                    </div>
                </table>
            </fieldset>
            <div class="bk15"></div>
            <input name="dosubmit" type="submit" id="dosubmit" value="<?php echo L('submit') ?>" class="dialog">
        </form>
    </div>
</div>
</body>
</html>


<script>
    $(function () {
        $(".rylx").change(function(){
           var ltx =  $(this).val();
            if(ltx!=77){
                $(".ltx").show();
            }else{
                $(".ltx").hide();
            }
        });
    });
</script>