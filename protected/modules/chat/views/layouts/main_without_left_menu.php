<HTML><HEAD>
<TITLE><?php echo Yii::t('lang', 'NS'); ?> - <?php echo Yii::t('lang', 'Overview'); ?></TITLE>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
<!--
<meta name="description" content="<?php echo Yii::app()->params['siteName']; ?> brings you closer to your customer.">
-->
<meta name="Description" content="<?php echo Yii::app()->params['siteName']; ?> Online psychics and Spiritual Psychic Advisors offering professional Psychic Online Readings,Tarot Readings, Live Online Psychic Chat and Email Readings." />
<meta name="Keywords" content="<?php echo Yii::app()->params['siteName']; ?> Chat, Online Psychic Readings, Free Psychic chat Readings, chat online, psychic readers, psychic, psychics, online, online psychic readings, psychic reading, online psychic chat, free psychic reading, online psychic reading, Email Readings, psychic tarot reading, online tarot Readings, free psychics online "/>
<meta name="Rating" content="General"/>
<meta name="author" content="PsychicContact"/>
<meta name="robots" content="index,follow"/>
<meta name="robots" content="all"/>
<meta name="copyright" content="Copyright 2002-2010 <?php echo Yii::app()->params['siteName']; ?> Inc">
<meta name="Classification" content="business">
<meta name="Language" content="en-us">


<link type="text/css" rel="stylesheet" href="<?php echo Yii::app()->params['http_addr'];?>/css/style.css">


</HEAD>

<BODY text=#000000 vLink=#333366 aLink=#333366 link=#333366 bgColor="#BF9DBF" background="<?php echo Yii::app()->params['site_domain']; ?>/chat/images/bg.gif" topmargin="0" leftmargin="0" rightmargin="0" bottommargin="0" marginwidth="0" marginheight="0">
<table width="838" border="0" cellspacing="0" cellpadding="0" align="center" style="height:100% ">
  <tr>
    <td valign="top" height="343px" align='center' bgcolor="#ffffff">
        <table cellspacing="0" cellpadding="0" border="0">
            <tr>
                <td colspan="6">
                    <img src="<?php echo Yii::app()->params['http_addr']; ?>images/source/head.jpg">
                </td>
            </tr>
            <tr>
                <td colspan="6">
                    <img src="<?php echo Yii::app()->params['http_addr']; ?>images/source/line.jpg">
                </td>
            </tr>
            <tr>
                <td>
                    <img src="<?php echo Yii::app()->params['http_addr']; ?>images/source/PsychicReadings.jpg">
                </td>
                <?php $this->widget('OldTopMenu'); ?>
            </tr>
        </table>
    </td>
  </tr>
  <tr>
    <td valign="top" align="left" bgcolor="#FFFFFF"><table cellspacing=0 cellpadding=0 border=0 align=center width=100% ><tr >

  <TD valign="top" align="center" width=100% >

  <?php echo $content; ?>

<br>
           </td>
    </tr>
    </table>
  </td>
</tr>
  <tr>
    <td height="5" width="838" valign="top" class="footer" align="center">
                <span>
                    <table>
                        <tr>
                            <?php $this->widget('BottomMenu'); ?>
                        </tr>
                    </table>
               <?php echo Yii::t('lang', 'Copyright_1998'); ?> <?php echo date('Y'); ?> <?php echo Yii::t('lang', 'NS_Jayson'); ?> </span>
    </td>
  </tr>
</table>
</body>
</html>
