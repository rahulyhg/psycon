<?php $this->widget('UserInfo'); ?>
<?php if(isset($favorite_msg)): ?>
<center>
    <font color="red"><b><?php echo $favorite_msg; ?></b></font>
</center>
<?php endif; ?>
<table width="90%" align="center">
<?php $this->widget('UserMenu'); ?>
<tr>
<td width="100%" valign="top" colspan="5"><br/>
<?php echo Yii::t('lang', 'NOTE_TO_OUR_CLIENTS'); ?>
<!--
You may also cancel any charges yourself if you change your mind,
provided you have not used any of the new time purchased and you cancel
it within 5 days of the purchase.
-->
<?php echo Yii::t('lang', 'You_may_also_ask_us_to_cancel'); ?>
<a href="<?php echo Yii::app()->params['http_addr'].'site/contact'; ?>">
    <?php echo Yii::t('lang', 'contactLow'); ?>
</a>
<?php echo Yii::t('lang', 'the_site_admin_to_remove'); ?>.
<br/>
<font size="-1"><?php echo Yii::t('lang', 'For_entertainment_purposes_only'); ?>.
<a target="_blank" href="<?php echo Yii::app()->params['http_addr'].'site/disclaimer'; ?>"><?php echo Yii::t('lang', 'Disclaimer'); ?></a></font>
</td>
</tr>
</table>