<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tbody>
        <tr>
            <td class="ppheading" width="100%"><?php echo Yii::t('lang', 'Our_clients'); ?></td>
        </tr>
        <tr>
            <td><img src="<?php echo Yii::app()->params['http_addr']; ?>images/pixel.gif" width="2" height="2"></td>
        </tr>
    </tbody>
</table>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tbody>
        <tr>
            <td><img src="<?php echo Yii::app()->params['http_addr']; ?>images/pixel.gif" width="6" height="6"></td>
        </tr>
        <tr>
            <td bgcolor="#999999"><img src="<?php echo Yii::app()->params['http_addr']; ?>images/pixel.gif" width="1" height="2"></td>
        </tr>
        <tr>
            <td><img src="<?php echo Yii::app()->params['http_addr']; ?>images/pixel.gif" width="6" height="6"></td>
        </tr>
    </tbody>
</table>
<a href="<?php echo Yii::app()->params['http_addr'].'users/adminSearch'; ?>"><?php echo Yii::t('lang', 'List_of_Clients_Search'); ?></a> &nbsp;
<a href="<?php echo Yii::app()->params['site_domain'].'/'.PsyConstants::getName(PsyConstants::PAGE_CLIENT_ADD); ?>"><?php echo Yii::t('lang', 'Add_new_Client'); ?></a> &nbsp;
<a href="<?php echo Yii::app()->params['site_domain'].'/'.PsyConstants::getName(PsyConstants::PAGE_CLIENT_DOUBLE); ?>"><?php echo Yii::t('lang', 'Double_Accounts'); ?></a> &nbsp;
<a href="<?php echo Yii::app()->params['site_domain'].'/'.PsyConstants::getName(PsyConstants::PAGE_DOUBLE_ACCOUNTS); ?>"><?php echo Yii::t('lang', 'Double_accounts_by'); ?></a> &nbsp;
<a href="<?php echo Yii::app()->params['site_domain'].'/'.PsyConstants::getName(PsyConstants::PAGE_CLIENT_NEW); ?>"><?php echo Yii::t('lang', 'List_of_New_Clients'); ?></a><br>
<a href="<?php echo Yii::app()->params['site_domain'].'/'.PsyConstants::getName(PsyConstants::PAGE_DELETE_OLD); ?>"><?php echo Yii::t('lang', 'Delete_Old_accounts'); ?></a>

<?php echo CHtml::beginForm(); ?>
<input name="search" value="1" type="hidden">
<table border="0" width="480">
    <tbody><tr>
            <td></td>
            <td>
                <span class="TextMedium">
                    <b><?php echo Yii::t('lang', 'ourclients_txt_1'); ?>:</b>&nbsp;&nbsp;
                </span>
            </td>
            <td>
                <input class="InputBoxSmall" size="10" name="client_user" value="">
            </td>
        </tr>
    </tbody></table>
<br>
<input value="<?php echo Yii::t('lang', 'Submit'); ?>" type="submit">
<?php echo CHtml::endForm(); ?>

