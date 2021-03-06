<?php echo Yii::t('lang', 'Account_Overview'); ?>
<hr>
<table border="0" cellpadding="0" cellspacing="0" width="100%">
    <tbody><tr>
            <td align="left" nowrap="nowrap">
                <span class="TextMedium">
                    <b><?php echo Yii::t('lang', 'Name'); ?>: </b><?php echo $sender->name; ?><br>
                    <b><?php echo Yii::t('lang', 'Email'); ?>:</b> <a class="LinkMedium" href="mailto:<?php echo $sender->emailaddress; ?>"><?php echo $sender->emailaddress; ?></a><br>
                </span>
            </td>
        </tr>
    </tbody>
</table>
<br>
<?php if(isset($errors)): ?>
    <?php $this->widget('ErrorMessage', array('message' => $errors)); ?>
<?php endif; ?>
<?php if(isset($success)): ?>
    <?php $this->widget('SuccessMessage', array('message' => Yii::t('lang', 'Your_message_has_been_send'))); ?>
<?php endif; ?>
<br>
<?php echo CHtml::beginForm('','post',array('enctype'=>'multipart/form-data')); ?>
<input type="hidden" name="from_user" value="<?php $client->rr_record_id; ?>">
<table style="border-collapse: collapse;" border="0" bordercolor="#111111" cellpadding="0" cellspacing="0" width="70%">
    <tbody><tr>

            <td colspan="2" width="100%"></td>
        </tr>
        <tr>
            <td width="50%"><?php echo Yii::t('lang', 'To_email'); ?>: </td>
            <td width="50%">
                <b><?php echo $receiver_name; ?></b>
            </td>
        </tr>
        <tr>
            <td width="50%"><?php echo Yii::t('lang', 'User_Name'); ?>:</td>
            <td width="50%">
                <b><?php echo $receiver_login; ?></b>
            </td>
        </tr>
        <tr>
            <td width="50%"><?php echo Yii::t('lang', 'From'); ?>:</td>
            <td width="50%">
                <b><?php echo $message->From_name; ?></b>
            </td>
        </tr>
        <tr>
            <td width="50%"><?php echo Yii::t('lang', 'Subject'); ?>:</td>
            <td width="50%"><input class="InputBoxFront" size="40" maxlength="50" name="subject" value="<?php echo $message->Subject; ?>"></td>
        </tr>
        <tr>
            <td width="50%"><?php echo Yii::t('lang', 'Body'); ?>:</td>
            <td width="50%">
                <textarea rows="5" name="text" cols="34">
                    <?php echo $message->Body; ?>
                </textarea>
            </td>
        </tr>
        <tr>
            <td width="50%"></td>
            <td width="50%">
                <br><font color="red" size="2"><?php echo Yii::t('lang', 'Attachment_feature_is_for_uploading'); ?></font><br><br>
                <?php echo CHtml::activeFileField($message, 'attach'); ?>
            </td>
        </tr>
    </tbody></table>
<input value="<?php echo Yii::t('lang', 'Send'); ?>" type="submit" name="send">
<?php echo CHtml::endForm(); ?>