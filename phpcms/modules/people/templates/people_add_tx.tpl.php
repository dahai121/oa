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
        <form name="myform" action="?m=people&c=people&a=add" method="post" id="myform">
            <fieldset>
                <legend><?php echo L('basic_configuration') ?></legend>
                <table width="100%" class="table_form">
                    <tr>
                        <td width="80"><?php echo L('����') ?></td>
                        <td><input type="text" name="info[name]" class="input-text" id="name"></input></td>
                    </tr>

                    <tr>
                        <td width="80"><?php echo L('�Ա�') ?></td>
                        <td>
                            <label><input name="info[sex]" type="radio" value="��" />�� </label>
                            <label><input name="info[sex]" type="radio" value="Ů" />Ů </label>
                        </td>
                    </tr>

                    <tr>
                        <td width="80"><?php echo L('��������') ?></td>
                        <td><input type="text" name="info[birthday]" class="input-text" id="name"></input></td>
                    </tr>

                    <tr>
                        <td width="80"><?php echo L('���֤��') ?></td>
                        <td><input type="text" name="info[sfz]" class="input-text" id=""></input></td>
                    </tr>

                    <tr>
                        <td width="80"><?php echo L('����') ?></td>
                        <td><input type="text" name="info[nation]" class="input-text" id="nation"></input></td>
                    </tr>

                    <tr>
                        <td width="80"><?php echo L('������ò') ?></td>
                        <td>
                            <select name="info[zzmm]" id="selectAge">
                                <option value="�й���Ա">�й���Ա</option>
                                <option value="�й�Ԥ����Ա">�й�Ԥ����Ա</option>
                                <option value="��ﵳԱ">��ﵳԱ</option>
                                <option value="������Ա">������Ա</option>
                                <option value="�����ٽ����Ա">�����ٽ����Ա</option>
                                <option value="ũ����������Ա">ũ����������Ա</option>
                                <option value="�¹�����Ա">�¹�����Ա</option>
                                <option value="����ѧ����Ա">����ѧ����Ա</option>
                                <option value="̨����Ա">̨����Ա</option>
                                <option value="�޵���������ʿ">�޵���������ʿ</option>
                                <option value="Ⱥ��">Ⱥ��</option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td width="80"><?php echo L('����') ?></td>
                        <td><input type="text" name="info[jg]" class="input-text" id=""></input></td>
                    </tr>

                    <tr>
                        <td width="80"><?php echo L('��ͥסַ') ?></td>
                        <td><input type="text" name="info[address]" class="input-text" id=""></input></td>
                    </tr>

                    <tr>
                        <td width="80"><?php echo L('�������ڵ��ɳ���') ?></td>
                        <td><input type="text" name="info[pcs]" class="input-text" id=""></input></td>
                    </tr>

                    <tr>
                        <td width="80"><?php echo L('�ֻ�') ?></td>
                        <td><input type="text" name="info[phone]" class="input-text" id=""></input></td>
                    </tr>

                    <tr>
                        <td width="80"><?php echo L('�ʱ�') ?></td>
                        <td><input type="text" name="info[yb]" class="input-text" id=""></input></td>
                    </tr>

                    <tr>
                        <td width="80"><?php echo L('��ϵ��ַ') ?></td>
                        <td><input type="text" name="info[lxdz]" class="input-text" id=""></input></td>
                    </tr>

                    <tr>
                        <td width="80"><?php echo L('�Ļ��̶�') ?></td>
                        <td>
                            <select name="info[education]" id="selectAge">
                                <option value="�о���">�о���</option>
                                <option value="��ѧ">��ѧ</option>
                                <option value="��ר">��ר</option>
                                <option value="��ר">��ר</option>
                                <option value="��У">��У</option>
                                <option value="����">����</option>
                                <option value="���м�����">���м�����</option>
                            </select>
                        </td>
                    </tr>


                    <tr>
                        <td width="80"><?php echo L('�뵳ʱ��') ?></td>
                        <td><input type="text" name="info[rdtime]" class="input-text" id="name"></input></td>
                    </tr>

                    <tr>
                        <td width="80"><?php echo L('ת��ʱ��') ?></td>
                        <td><input type="text" name="info[zztime]" class="input-text" id="name"></input></td>
                    </tr>

                    <tr>
                        <td width="80"><?php echo L('����ʱ��') ?></td>
                        <td><input type="text" name="info[gztime]" class="input-text" id="name"></input></td>
                    </tr>

                    <tr>
                        <td width="80"><?php echo L('�����ݵ�λ') ?></td>
                        <td><input type="text" name="info[ltxdw]" class="input-text" id=""></input></td>
                    </tr>

                    <tr>
                        <td width="80"><?php echo L('ְ��') ?></td>
                        <td>
                            <select name="info[zj]" id="selectAge">
                                <option value="������">������</option>
                                <option value="������">������</option>
                                <option value="���ּ�">���ּ�</option>
                                <option value="���ּ�">���ּ�</option>
                                <option value="������">������</option>
                                <option value="���Ƽ�">���Ƽ�</option>
                                <option value="���Ƽ�">���Ƽ�</option>
                                <option value="����">����</option>
                                <option value="�ɲ�">�ɲ�</option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td width="80"><?php echo L('������ʱ��') ?></td>
                        <td><input type="text" name="info[ltxtime]" class="input-text" id="name"></input></td>
                    </tr>

                    <tr>
                        <td width="80"><?php echo L('רҵ����ְλ') ?></td>
                        <td><input type="text" name="info[zyjszw]" class="input-text" id=""></input></td>
                    </tr>

                    <tr>
                        <td width="80"><?php echo L('רҵְ�񼶱�') ?></td>
                        <td>
                            <select name="info[zyzwjb]" id="selectAge">
                                <option value="���߼�">���߼�</option>
                                <option value="���߼�">���߼�</option>
                                <option value="�м�">�м�</option>
                                <option value="����">����</option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td width="80"><?php echo L('�ֹܴ���') ?></td>
                        <td>
                            <select name="info[fgcs]" id="selectAge">
                                <option value="�ְ칫��">�ְ칫��</option>
                                <option value="��ί�칫��">��ί�칫��</option>
                                <option value="���´�">���´�</option>
                                <option value="�ϲ����칫��">�ϲ����칫��</option>
                                <option value="����">����</option>
                                <option value="������">������</option>
                                <option value="�������">�������</option>
                                <option value="ҽ�Ʊ�����">ҽ�Ʊ�����</option>
                                <option value="�ۺϹ���">�ۺϹ���</option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td width="80"><?php echo L('����ְ��') ?></td>
                        <td><input type="text" name="info[dnzw]" class="input-text" id=""></input></td>
                    </tr>

                    <tr>
                        <td width="80"><?php echo L('���ڵ�֧��') ?></td>
                        <td>
                            <select name="info[szdzb]" id="selectAge">
                                <option value="�ְ칫��">�ְ칫��</option>
                                <option value="��ί�칫��">��ί�칫��</option>
                                <option value="���´�">���´�</option>
                                <option value="�ϲ����칫��">�ϲ����칫��</option>
                                <option value="����">����</option>
                                <option value="������">������</option>
                                <option value="�������">�������</option>
                                <option value="ҽ�Ʊ�����">ҽ�Ʊ�����</option>
                                <option value="�ۺϹ���">�ۺϹ���</option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td width="80"><?php echo L('������') ?></td>
                        <td><input type="text" name="info[dah]" class="input-text" id=""></input></td>
                    </tr>

                    <tr>
                        <td width="80"><?php echo L('ְ��') ?></td>
                        <td><input type="text" name="info[zw]" class="input-text" id=""></input></td>
                    </tr>

                    <tr>
                        <td width="80"><?php echo L('email') ?></td>
                        <td><input type="text" name="info[email]" class="input-text" id=""></input></td>
                    </tr>

                    <tr>
                        <td width="80"><?php echo L('��Ƭ') ?></td>
                        <td><input type="text" name="info[pic]" class="input-text" id=""></input></td>
                    </tr>

                    <tr>
                        <td width="80"><?php echo L('��ע') ?></td>
                        <td><input type="text" name="info[bz]" class="input-text" id=""></input></td>
                    </tr>

                    <tr>
                        <td width="80"><?php echo L('����') ?></td>
                        <td><input type="text" name="info[lx]" class="input-text" id=""></input></td>
                    </tr>

                </table>
            </fieldset>
            <input type="hidden" name="info[rylx]" value="1" >
            <div class="bk15"></div>
            <input name="dosubmit" type="submit" id="dosubmit" value="<?php echo L('submit') ?>" class="dialog">
        </form>
    </div>
</div>
</body>
</html>