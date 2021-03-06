<?php echo Yii::t('lang', 'Our_clients'); ?>
<hr>

<?php echo Yii::t('lang', 'Available_users'); ?>: <b><?php echo $client->login ?></b>
<?php echo CHtml::beginForm(); ?>
<input type="hidden" name="update" val="1">
<table border="0" width="480">
    <tbody>
        <tr>
            <td><img src="<?php echo Yii::app()->params['http_addr']; ?>images/oneoperatoricon_small.gif" border="0" width="17" height="17">&nbsp;</td>
            <td nowrap="nowrap">
                <span class="TextSmall">
                   <?php echo Yii::t('lang', 'ourclients_txt_3'); ?>
                </span>

            </td>
            <td>
                <input class="InputBoxFront" size="40" maxlength="50" name="firstname" value="<?php echo $client->credit_cards->firstname; ?>">
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td nowrap="nowrap">
                <span class="TextSmall">
                    <?php echo Yii::t('lang', 'ourclients_txt_4'); ?>
                </span>

            </td>
            <td>
                <input class="InputBoxFront" size="40" maxlength="50" name="lastname" value="<?php echo $client->credit_cards->lastname; ?>">
            </td>
        </tr>
        <tr>
            <td></td>
            <td nowrap="nowrap">
                <span class="TextSmall">
                    <?php echo Yii::t('lang', 'Login'); ?>:&nbsp;&nbsp;

                </span>
            </td>
            <td>
                <b><?php echo $client->login ?></b>
            </td>
        </tr>
        <tr>
            <td></td>
            <td nowrap="nowrap">

                <span class="TextSmall">
                   <?php echo Yii::t('lang', 'Sign_up_date'); ?>:&nbsp;&nbsp;
                </span>
            </td>
            <td>
                <b><?php $signup_date   = $client->rr_createdate;
                    $signup_date = strtotime($signup_date);
                    $signup_date = date("M j, Y", $signup_date);
                    echo $signup_date; ?></b> (<?php echo $balance->Client_status ?>)</td>
        </tr>
        <tr>
            <td></td>
            <td nowrap="nowrap">
                <span class="TextSmall">
                    <?php echo Yii::t('lang', 'Password'); ?>:&nbsp;&nbsp;
                </span>
            </td>
            <td>
                <input class="InputBoxFront" size="40" maxlength="50" type="password" name="password" value="<?php echo $client->password; ?>">

            </td>
        </tr>
        <tr>
            <td></td>
            <td nowrap="nowrap">
                <span class="TextSmall">
                    <?php echo Yii::t('lang', 'ourclients_txt_5'); ?>
                </span>
            </td>
            <td valign="top">

                <input class="InputBoxFront" size="40" maxlength="50" name="balance_new" value="<?php echo $client->balance; ?>"><br>
                <?php echo Yii::t('lang', 'including'); ?> <b><?php echo $client->free_mins ?></b> <?php echo Yii::t('lang', 'ourclients_txt_6'); ?>
            </td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td width="100%">
                <table border="0" cellpadding="0" cellspacing="0" width="100%">

                    <tbody><tr height="5"><td><img src="images/transp.gif"></td></tr>

                        <tr height="1"><td bgcolor="#cecece" width="100%" nowrap="nowrap"><img src="images/transp.gif"></td></tr>
                        <tr height="1"><td bgcolor="white" width="100%" nowrap="nowrap"><img src="images/transp.gif"></td></tr>
                        <tr height="5"><td><img src="images/transp.gif"></td></tr>
                    </tbody></table>

            </td>
        </tr>
        <tr>

            <td></td>
            <td nowrap="nowrap">
                <span class="TextSmall">
                    <?php echo Yii::t('lang', 'email_Address'); ?>:&nbsp;&nbsp;
                </span>
            </td>
            <td>
                <span class="TextTiny"><?php echo Yii::t('lang', 'ourclients_txt_7'); ?> <br></span>

                <input class="InputBoxFront" size="40" maxlength="50" name="emailaddress" value="<?php echo $client->emailaddress; ?>">
            </td>
        </tr>
        <tr>
            <td></td>
            <td nowrap="nowrap">
                <span class="TextSmall">
                    <?php echo Yii::t('lang', 'Gender'); ?>:
                </span>
            </td>

            <td valign="top">
                <select size="1" name="gender">
                    <option value="Male" <?php echo ($client->gender == 'Male') ? 'selected' : ''?>><?php echo Yii::t('lang', 'Male'); ?></option>
                    <option value="Female" <?php echo ($client->gender == 'Female') ? 'selected' : ''?>><?php echo Yii::t('lang', 'Female'); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td width="100%">
                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tbody><tr height="5"><td><img src="images/transp.gif"></td></tr>

                        <tr height="1"><td bgcolor="#cecece" width="100%" nowrap="nowrap"><img src="images/transp.gif"></td></tr>
                        <tr height="1"><td bgcolor="white" width="100%" nowrap="nowrap"><img src="images/transp.gif"></td></tr>
                        <tr height="5"><td><img src="images/transp.gif"></td></tr>

                    </tbody></table>

            </td>
        </tr>

        <tr>
            <td></td>
            <td nowrap="nowrap">
                <span class="TextSmall">
                    <?php echo Yii::t('lang', 'How_you_hear_about_us'); ?> :&nbsp;&nbsp;

                </span>
            </td>
            <td>
                <input class="InputBoxFront" size="40" maxlength="50" name="hear" value="<?php echo $client->hear; ?>" readonly="readonly">

            </td>
        </tr>
        <tr>
            <td></td>
            <td nowrap="nowrap">

                <span class="TextSmall">
                    <?php echo Yii::t('lang', 'Date_of_Birth'); ?> :&nbsp;&nbsp;
                </span>
            </td>
            <td nowrap="nowrap">
                <select class="SelectBoxStandard" name="month">
                    <option value="-- Please Select --">-- <?php echo Yii::t('lang', 'Please_Select'); ?> --</option>
                    <?php
                    $month = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
                    foreach($month as $mon): ?>
                    <option value="<?php echo $mon;  ?>" <?php echo ($client->month == $mon) ? 'selected' : '' ?>> <?php echo $mon; ?></option>
                    <?php endforeach; ?>
                </select>
                <select class="SelectBoxStandard" name="day">
                    <option value="-- Please Select --">-- <?php echo Yii::t('lang', 'Please_Select'); ?> --</option>
                    <?php for($i=1;$i<32;$i++): ?>
                    <option value="<?php echo $i; ?>" <?php echo ($client->day == $i) ? 'selected' : '' ?>><?php echo $i;?></option>
                    <?php endfor; ?>
                </select>
                <select class="SelectBoxStandard" name="year">
                    <option value="-- Please Select --">-- <?php echo Yii::t('lang', 'Please_Select'); ?> --</option>
                    <?php for($i=date('Y')-18;$i>date('Y')-73;$i--): ?>
                    <option value="<?php echo $i; ?>" <?php echo ($client->year == $i) ? 'selected' : '' ?>><?php echo $i; ?></option>
                    <?php endfor; ?>
                </select>
            </td>
        </tr>
        <tr>
            <td></td>
            <td valign="top" nowrap="nowrap">
                <span class="TextSmall">
                    <?php echo Yii::t('lang', 'Type'); ?> 
                </span>
            </td>
            <td>
                <select size="1" name="x_Client_status">
                    <?php
                    $cl_stat = array('preferred', 'limited', 'banned', 'unactivated', 'banned_by_reader');
                    foreach($cl_stat as $stat):
                        ?>
                    <option value="<?php echo $stat ?>" <?php echo ($balance->Client_status == $stat) ? 'selected' : '' ?>><?php echo $stat ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
            <td width="25"></td>
            <td nowrap="nowrap"><span class="TextSmall"><?php echo Yii::t('lang', 'Banned_by'); ?> :&nbsp;&nbsp;</span></td>
            <td><input name="who_banned" value="<?php echo isset($ban->who_banned) ? $ban->who_banned : '' ?>" type="text"></td>
            <td></td>
        </tr>
        <tr>
            <td width="25"></td>
            <td nowrap="nowrap"><span class="TextSmall"><?php echo Yii::t('lang', 'Date'); ?>:&nbsp;&nbsp;</span><?php echo isset($ban->date) ? $ban->date : '' ?></td>
            <td></td>
        </tr>
        <tr>
            <td width="25"></td>
            <td nowrap="nowrap"><span class="TextSmall"><?php echo Yii::t('lang', 'Reason'); ?> :&nbsp;&nbsp;</span></td>
            <td><textarea name="reason" rows="5" cols="20"><?php echo isset($ban->reason) ? $ban->reason : '' ?></textarea></td>
            <td></td>
        </tr>
        <tr>
            <td width="25"></td>
            <td nowrap="nowrap"><span class="TextSmall">IP:&nbsp;&nbsp;<br>(<?php echo Yii::t('lang', 'who_banned'); ?>)</span> <?php echo isset($ban->ip_who_banned) ? $ban->ip_who_banned : '' ?></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td nowrap="nowrap">
                <span class="TextSmall">
                    <?php echo Yii::t('lang', 'Personal_Limis'); ?>:&nbsp;&nbsp;
                </span>
            </td>
            <td>
                <input name="personal_limits" value="yes" type="checkbox" <?php echo isset($per_lim) ? 'checked' : '' ?>> <?php echo Yii::t('lang', 'ourclients_txt_8'); ?>
            </td>

        </tr>


        <tr>
            <td></td>
            <td nowrap="nowrap">
                <span class="TextSmall">
                    <?php echo Yii::t('lang', 'Day_Limit'); ?> ($):
                </span>
            </td>
            <td>

                <input class="InputBoxFront" size="20" maxlength="50" name="Day_limit" value="<?php echo isset($per_lim->Day_limit) ? $per_lim->Day_limit : PsyConstants::getName(PsyConstants::DAY_LIMIT) ?>">
            </td>
        </tr>
        <tr>
            <td></td>
            <td nowrap="nowrap">
                <span class="TextSmall">
                    <?php echo Yii::t('lang', 'Month_Limit'); ?> ($):
                </span>
            </td>

            <td>
                <input class="InputBoxFront" size="40" maxlength="50" name="Month_limit" value="<?php echo isset($per_lim->Month_limit) ? $per_lim->Month_limit : PsyConstants::getName(PsyConstants::MONTH_LIMIT) ?>">
            </td>
        </tr>
        <tr>
            <td></td>
            <td nowrap="nowrap">
                <span class="TextSmall">
                    <?php echo Yii::t('lang', 'Affiliate'); ?>
                </span>
            </td>
            <td>
                <input class="InputBoxFront" size="40" maxlength="50" name="x_affiliate" value="<?php echo $client->affiliate ?>">&nbsp;<a href="http://dev.psychic-contact.com/affpro/admin/details.php?id=2"><?php echo Yii::t('lang', 'Show_Affiliates_Profile'); ?></a>
            </td>
        </tr>
        <tr>
            <td><img src="images/addressicon.gif">&nbsp;</td>
            <td nowrap="nowrap">
                <span class="TextSmall">
                    <?php echo Yii::t('lang', 'address'); ?>:&nbsp;&nbsp;
                </span>
            </td>
            <td><?php echo str_replace('\n', ' ', $client->address); ?></td>
        </tr>
        <tr>
            <td><img src="images/phoneicon.gif">&nbsp;</td>
            <td nowrap="nowrap">
                <span class="TextSmall">
                    <?php echo Yii::t('lang', 'Phone'); ?> #:&nbsp;&nbsp;
                </span>
            </td>
            <td><?php echo $client->phone; ?></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td nowrap="nowrap">
                <span class="TextSmall">
                    <?php echo Yii::t('lang', 'Last_digits_of_CC'); ?>:&nbsp;&nbsp;
                </span>
            </td>
            <td><?php echo substr($client->credit_cards->cardnumber, -4) ?></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td nowrap="nowrap">
                <span class="TextSmall">
                    <?php echo Yii::t('lang', 'All_IPs'); ?>:&nbsp;&nbsp;
                </span>
            </td>
            <td>
                <textarea name="ip" rows="5" cols="20" readonly="readonly"><?php echo $balance->IP; ?></textarea>
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>
                <span class="TextSmall">
                    <?php echo Yii::t('lang', 'Chat_type'); ?>:&nbsp;&nbsp;
                </span>
            </td>
            <td>
                <select name="chat_type">
                    <?php
                    $chat_types = array('0' => 'default', '1' => 'debug', '2' => 'jchat', '3' => 'debug2', '4' => 'fchat');
                    foreach($chat_types as $chat_type => $value):
                    ?>
                    <option value="<?php echo $chat_type; ?>" <?php echo ($client->chat_type == $chat_type) ? 'selected' : '' ?>><?php echo $value; ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
            <td colspan="5" width="100%">
                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tbody>
                        <tr>
                            <td width="100%"></td>
                            <td width="10" nowrap="nowrap"><img src="/images/transp.gif"></td>
                            <td></td>
                            <td width="7" nowrap="nowrap"><img src="/images/transp.gif"></td>
                            <td>
                                <input value="<?php echo Yii::t('lang', 'Save'); ?>" type="submit" name="save">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody></table>
<input value="<?php echo Yii::t('lang', 'Remove_account'); ?>" type="submit" name="remove_acc">
<?php echo CHtml::endForm(); ?>
<a href="send_emails.php?toid=<?php echo $client->rr_record_id ?>"><?php echo Yii::t('lang', 'Send_email_to'); ?> <?php echo $client->login ?></a><br><br>
<a href="<?php echo Yii::app()->params['http_addr'] ?>admin/clientTranscripts?id=<?php echo $client->rr_record_id ?>" target="_blank"><?php echo Yii::t('lang', 'Transcripts'); ?></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="client_archive.php?client_id=<?php echo $client->rr_record_id ?>" target="_blank"><?php echo Yii::t('lang', 'Archive'); ?></a><br>

<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'dataProvider'=>$user_deps,
    'columns' => array(
        array(
            'name' =>  Yii::t('lang', 'Date'),
            'value' => '$data->Date'
        ),
        array(
            'name' =>  Yii::t('lang', 'Amount'),
            'value' => '$data->Amount'
        ),
        array(
            'name' =>  Yii::t('lang', 'Currency'),
            'value' => '$data->Currency'
        ),
        array(
            'name' =>  Yii::t('lang', 'BMT'),
            'value' => '$data->Bmt',
        ),
        array(
            'name' =>  Yii::t('lang', 'Payment_Method'),
            'value' => '$data->Order_numb'
        )
    ),
));
?>

