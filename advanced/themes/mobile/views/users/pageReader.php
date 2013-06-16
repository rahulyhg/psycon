<?php if(Yii::app()->user->hasFlash('success')):?>
    <div class="info" style="position:absolute;width: 250px;height: 125px; background: url('../images/gradation.png') repeat-x;border: solid 1px;">
        <div style="font-size: 14px; color: black; margin: auto 0; padding-top: 50px; padding-left: 25px;"><?php echo Yii::app()->user->getFlash('success'); ?></div>
    </div>
<?php endif; ?>
<center><h3>Page Reader</h3></center>
<table cellspacing=0 cellpadding=0 border=0 width="100%">
<tr>
    <td colspan=2>
    <?php if($error_message): ?>
        <div class="error">
            <?php echo $error_message;?>
        </div>
    <?php endif; ?>
    <br>
    <?php echo Yii::t('lang', 'notifyreaders_msg_1'); ?>
    <br>
    <?php echo CHtml::beginForm('','post');?>
    <?php echo CHtml::hiddenField("action","send");?>
    <script type="text/javascript">
    <!--
    function setcustfld(prm)
    {
        prm.form.when[1].checked=true;
    }
    //-->
    </script>
    <br>
    <?php echo CHtml::beginForm('','post');?>
    <?php echo CHtml::radioButton("when", true,  array('value'=>'15mins'));?>  <?php echo Yii::t('lang', 'Within_the_next'); ?>
    <?php echo CHtml::radioButton("when", false, array('value'=>'custom'));?>  <?php echo Yii::t('lang', 'Make_an_appointment'); ?>
    <br>
    <br>
    <b><font color="#138A22"> <?php echo Yii::t('lang', 'The_current_time_is'); ?> <?php echo $current_hour?>:<?php echo $current_min?> <?php echo $current_a?></font></b>
    <br><br>
    <table border="0" width="200" cellspacing="0" cellpadding="0" name="chattime">
    <tr>
        <td>
            <?php echo CExHtml::selectStringMonthBox("month_main", $current_month, array('onClick'=>'setcustfld(this);') ,'',false);?>
        </td>
        <td>
            <?php echo CExHtml::selectDaysBox("day_main", $current_day, array('onClick'=>'setcustfld(this);') ,'', false);?>
        </td>
        <td>
            <?php echo CExHtml::selectCYearsBox("year_main",$current_year,$current_year+1,$current_year, array('onClick'=>'setcustfld(this);'), '', false);?>
        </td>
    </tr>
    </table>
    <?php echo CHtml::textField("time_main",$main_time, array ('onClick'=>'setcustfld(this);'));?>
    <br>
    <br>
        <b><?php echo Yii::t('lang', 'ALTERNATE_date_and_time'); ?>:</b>
    <br>
    <table border="0" width="200" cellspacing="0" cellpadding="0">
    <tr>
        <td>
            <?php echo CExHtml::selectStringMonthBox("month_alt", $current_month, array('onClick'=>'setcustfld(this);') ,'',false);?>
        </td>
        <td>
            <?php echo CExHtml::selectDaysBox("day_alt", $current_day, array('onClick'=>'setcustfld(this);') ,'', false);?>
        </td>
        <td>
            <?php echo CExHtml::selectCYearsBox("year_alt",$current_year,$current_year+1,$current_year, array('onClick'=>'setcustfld(this);'), '', false);?>
        </td>
    </tr>
    </table>
    <?php echo CHtml::textField("time_alt",$alt_time, array ('onClick'=>'setcustfld(this);'));?>
    <br>
    <br>
    <?php
    $this->widget('zii.widgets.grid.CGridView', array(
        'dataProvider'=>$readers_lst,
        'selectableRows'=>3,
        'columns'=>array(
            array(
                'htmlOptions' => array('style'=>'font-size: 10px'),
                'id'=>'readers',
                'name'=>'readers',
                'value'=>'$data->rr_record_id',
                'class'=>'CCheckBoxColumn',
            ),
            array(
                'htmlOptions' => array('style'=>'font-size: 10px'),
                'name'=>'login',
            ),
        ),
    ));
    ?>
    <br>
    <input type="submit" name="button" value="<?php echo Yii::t('lang', 'Send_Notification'); ?>" class="button">
    </form>
    </td>
</tr>
</table>

<br><br><br>