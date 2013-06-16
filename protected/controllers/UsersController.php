<?php

class UsersController extends PsyController {
    const PAGE_SIZE=10;

    /**
     * @return array action filters
     */
    public function filters($params = null) {
        $a = array(
                'accessControl', // perform access control for CRUD operations
                array('application.components.PsyNetRestrictor + mainmenu'), // Redirect if user from different site
                array('application.components.PsyBanController + mainmenu'), 
        );
        return parent::filters($a);
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules() {
        return array(
                array('allow',
                        'actions' => array('emailreadingsadmin', 'adminsearch', 'clientedit', 'clientadministration'),
                        'users' => array('Jayson') // @todo: add roles to filter
                    ),
                array('allow',  // allow all users to perform 'index' and 'view' actions
                        'actions'=>array('index','loadReaderStatus','banned','getBusyImage'),
                        'users'=>array('*'),
                ),
                array('allow', // allow authenticated user to perform 'create' and 'update' actions
                        'actions'=>array('mainmenu','pagereader','purchases',
                                         'notifyreaders', 'clientchathistory', 'readerchathistory', 'chathistorydel', 'chatstart',
                                         'emailreadingsReader', 'readersClients', 'OpenLog', 'ReaderBalance', 'PriceRate', 'Invoice',
                                         'UserBan', 'GetUser', 'EmailreadingsRates', 'PendingEmailreadings','SessionInfo','AddTime',
                                         'ManageTestimonials', 'EditTestimonial', 'DelTestimonial', 'NrrQuest', 'showEmailreading',),
                        'users'=>array('@'),
                ),
                array('deny',  // deny all users
                        'users'=>array('*'),
                ),
        );
    }

    /**
     * Show table with all email readings for current reader
     */
    public function actionEmailreadingsReader() {
        if((Yii::app()->user->isGuest) || (Yii::app()->user->type != 'reader')) { // ???? Administrator access
            $this->redirect('../site/index');
        } else {
            $model=new EmailQuestions();
            $reader_id = Yii::app()->user->id;
            $resp = $model->loadListForReader($reader_id);
            $this->render('emailreadings_reader', array('data' => $resp));
        }
    }
    /**
     * Show table with all email readings (for admin)
     */ 
    public function actionEmailreadingsAdmin() {
        if((Yii::app()->user->isGuest) || (Yii::app()->user->type != 'Administrator')) {
            $this->redirect('../site/index');
        } else {
            $model=new EmailQuestions();
            $resp = $model->loadListForAdmin();
            $this->render('emailreadings_reader', array('data' => $resp));
        }
    }
    /**
     * List of client's current reader read for
     */
    public function actionReadersClients(){
        $reader_id = Yii::app()->user->id;

        $clients = ChatHistory::getReadersClientList($reader_id);
        
        $this->render('clients_list', array('data' => $clients));
    }
    
    /**
     * Admin's user search block
     */
    public function actionAdminSearch() {
        if((Yii::app()->user->isGuest) || (Yii::app()->user->type != 'Administrator'))
            $this->redirect('../site/index');
        // users model
        $model = new UserSearch();
        // If page generated by CPagination
        if(isset($_GET['paging'])) {
            // If cc_hisory search using CreditCardHistory model
            if ($_GET['s_type'] == 'credit_card_history')
                $cc_model = new CreditCardHistory();
            // Search type checking var
            $search_type = $_GET['s_type'];
            $search_query = $_GET['query'];
        }
        // If search first page
        else {
            if ($_POST['search_by'] == 'credit_card_history')
                $cc_model = new CreditCardHistory();
            $search_type = $_POST['search_by'];
            $search_query = $_POST['psearch'];
        }
        if($search_type) {
            switch($search_type) {
                case 'default':
                    $resp = $model->loadUserList();
                    break;
                case 'login':
                    $resp = $model->searchUserByLogin($search_query);
                    break;
                case 'name':
                    $resp = $model->searchUserByName($search_query);
                    break;
                case 'phone':
                    $resp = $model->searchUserByPhone($search_query);
                    break;
                case 'email':
                    $resp = $model->searchUserByEmail($search_query);
                    break;
                case 'address':
                    $resp = $model->searchUserByAddress($search_query);
                    break;
                case 'signup_date':
                    $resp = $model->searchUserBySignupDate($search_query);
                    break;
                case 'credit_card':
                    $resp = $model->searchUserByCreditCard($search_query);
                    break;
                case 'dob_all':
                    $resp = $model->searchUserByDOBAll($search_query);
                    break;
                case 'credit_card_history':
                    $resp = $cc_model->searchCreditCard($search_query);
                    break;
                default:
                    $resp = $model->loadUserList();
                    break;
            }
        }
        if((isset($resp))&&(isset($cc_model)))
            $this->render('admin_search', array('search_type' => 'history', 'data' => $resp));
        elseif (isset($resp))
            $this->render('admin_search', array('search_type' => 'users', 'data' => $resp));
        else {
            $resp = $model->loadUserList();
            $this->render('admin_search', array('search_type' => 'users', 'data' => $resp));
        }
    }
    /**
     * Show, edit or remove current client profile
     */
    public function actionClientEdit(){
        $client_id = $_GET['id'];
        // basic info about user
        $client_info = UserSearch::loadOneUser($client_id);
        // Info about user's payment status, balance, limits
        $client_balance = ClientLimit::getUserInfo($client_id);
        // Info about users bans
        $client_ban = BanInfo::getUserBan($client_id);
        // Info about personal limits
        $pers_limit = PersonalLimits::getUserLimits($client_id);
        // Get information about user's deposits
        $user_deps = Deposits::getUsersDeposit($client_id);

        // If saving form
        if(isset($_POST['save'])){
            // Main user settings save
            $client_info->password = $_POST['password'];
            $client_info->balance = $_POST['balance_new'];
            $client_info->emailaddress = $_POST['emailaddress'];
            $client_info->gender = $_POST['gender'];
            $client_info->hear = $_POST['hear'];
            $client_info->month = $_POST['month'];
            $client_info->day = $_POST['day'];
            $client_info->year = $_POST['year'];
            $client_info->affiliate = $_POST['x_affiliate'];
            $client_info->chat_type = $_POST['chat_type'];
            $client_info->save();

            // Credit card settings save
            $cc_model = CreditCard::getCardInfo($client_id);
            $cc_model->firstname = $_POST['firstname'];
            $cc_model->lastname = $_POST['lastname'];
            $cc_model->save();

            // User balance settings save
            $client_balance->Client_status = $_POST['x_Client_status'];
            $client_balance->IP = $_POST['ip'];
            $client_balance->save();

            // User ban settings save
            if(isset($client_ban)){
                $client_ban->who_banned = $_POST['who_banned'];
                $client_ban->reason = $_POST['reason'];
                $client_ban->save();
            }

            // User limit settings save or delete
            if(!isset($pers_limit)) {
                if($_POST['personal_limits']){
                    $pers_limit = new PersonalLimits();
                    $pers_limit->ID_client = $client_id;
                    $pers_limit->Day_limit = $_POST['Day_limit'];
                    $pers_limit->Month_limit = $_POST['Month_limit'];
                    $pers_limit->save();
                }
            } else {
                if($_POST['personal_limits']){
                    $pers_limit->Day_limit = $_POST['Day_limit'];
                    $pers_limit->Month_limit = $_POST['Month_limit'];
                    $pers_limit->save();
                } else {
                    $pers_limit->delete();
                }
            }
            // Refresh client data
            $client_info = UserSearch::loadOneUser($client_id);
            $client_balance = ClientLimit::getUserInfo($client_id);
            $client_ban = BanInfo::getUserBan($client_id);
            $pers_limit = PersonalLimits::getUserLimits($client_id);
         }

         // If removing account - remove from T1_1, T1_4, band_list
         if(isset($_POST['remove_acc'])){
             // Main user settings remove
             $client_info->delete();
             // Credit card settings remove
             $cc_model = CreditCard::getCardInfo($client_id);
             $cc_model->delete();
             // User balance settings remove
             $client_balance->delete();
             // Rendering users list page
             $model = new UserSearch();
             $resp = $model->loadUserList();
             $this->render('admin_search', array('search_type' => 'users', 'data' => $resp));
         }
         $this->render('client_view', array('client' => $client_info,
                                           'balance' => $client_balance,
                                           'ban' => $client_ban,
                                           'per_lim' => $pers_limit,
                                           'user_deps' => $user_deps
                                     ));
    }
    /**
     * Action of clients administration page
     */
    public function actionClientAdministration(){
        if(isset($_POST['search'])) {
            $model = new UserSearch();            
            $client_info = UserSearch::loadOneUser($_POST['client_user']);
            if(!$client_info || empty($client_info))
                $this->redirect('adminSearch');
            $client_id = $client_info->rr_record_id;            
            $this->redirect('clientEdit?id='.$client_id);
        } else
            $this->render('client_administration');
    }
    /**
     * Action of client purchases page
     *
     */
    public function actionPurchases(){
        $client = Clients::getClient(Yii::app()->user->id);
        $data = Deposits::getUsersDeposit(Yii::app()->user->id);
        $this->render('purchases', array('data' => $data, 'client' => $client));
    }
    /**
     * Action off notify readers page
     */
    public function actionNotifyreaders(){
        $client = Clients::getClient(Yii::app()->user->id);
        $readers = Readers::getReadersList();
        if(isset($_POST['send'])){
            // Send emails to checked readers
            $subject = Yii::t('lang', 'CHAT_REQUEST');
            if($_POST['when'] == '15mins'){
                $body =     Yii::t('lang', 'DATEbigBtn').': '.date("M j, Y").'<br>'.Yii::t('lang', 'TIME_SENT').': '.
                            date("h:i a").'<br>'.Yii::t('lang', 'FROMbigBtn').': '.$client->login.'<br>'.
                            Yii::t('lang', 'Client').' '.$client->login.' '.Yii::t('lang', 'notifyreaders_txt_3');
            }
            else {
                $body =     Yii::t('lang', 'DATEbigBtn').': '.date("M j, Y").'<br>'.Yii::t('lang', 'TIME_SENT').': '.
                            date("h:i a").'<br>'.Yii::t('lang', 'FROMbigBtn').': '.$client->login.'<br>'. 
                            Yii::t('lang', 'Client').' '.$client->login.' '. Yii::t('lang', 'notifyreaders_txt_4_1').'<br>
                            '.$_POST['month_main'].'/'.$_POST['day_main'].'/'.$_POST['year_main'].' '.Yii::t('lang', 'at').' '.$_POST['time_main'].'<br>
                             '.Yii::t('lang', 'ALTERNATE_date_and_time').':<br>
                            '.$_POST['month_alt'].'/'.$_POST['day_alt'].'/'.$_POST['year_alt'].' '.Yii::t('lang', 'at').' '.$_POST['time_alt'].'<br>
                            '.Yii::t('lang', 'notifyreaders_txt_4_2');
            }            
            $readers_list = '(';
            foreach ($_POST['readers'] as $reader)
                $readers_list .= $reader.',';
            $readers_list = substr($readers_list, 0, strlen($readers_list)-1);
            $readers_list .= ')';            
            $sql = "SELECT * FROM `T1_1`
                    WHERE `rr_record_id` IN ".$readers_list;
            $connection=Yii::app()->db;
            $command=$connection->createCommand($sql);
            $res = $command->query();
            $readers_names = '';
            foreach($res as $r){
                $readers_names .= $r['name'].'<br>\n';
                PsyMailer::send($r['emailaddress'], $subject, $body);
            }
            // Send email to client
            if($_POST['when'] == '15mins'){
            $cl_body =         Yii::t('lang', 'notifyreaders_txt_5').'.<br>'.Yii::t('lang', 'TObigBtn').':<br>'.$readers_names.'<br>'.
                               Yii::t('lang', 'notifyreaders_txt_6').'<br>'.Yii::app()->params['http_addr'];
            } else {
                $cl_body =     Yii::t('lang', 'notifyreaders_txt_5').'.<br>'.Yii::t('lang', 'TObigBtn').':<br>'.$readers_names.'<br>'.Yii::t('lang', 'notifyreaders_txt_7').'<br>
                               '.$_POST['month_main'].'/'.$_POST['day_main'].'/'.$_POST['year_main'].' '.Yii::t('lang', 'at').' '.$_POST['time_main'].'<br>
                               '.Yii::t('lang', 'ALTERNATE_date_and_time') .':<br>
                               '.$_POST['month_alt'].'/'.$_POST['day_alt'].'/'.$_POST['year_alt'].' '.Yii::t('lang', 'at').' '.$_POST['time_alt'].'<br>
                               <br>'.Yii::t('lang', 'notifyreaders_txt_8').'<br>'.Yii::app()->params['http_addr'];
            }
            PsyMailer::send($client->emailaddress, $subject, $cl_body);
        }
        $this->render('notifyreaders', array('client' => $client, 'readers' => $readers));
    }
    /**
     * Chat history action
     */
    public function actionClientChatHistory(){
        $this->layout = $this->without_left_menu;
        $dur = ChatHistory::getTotalTimeDuration(Yii::app()->user->id);
        foreach($dur as $total)
            $total_duration = $total['Total'];

        // If user press search button //
        if(isset($_POST['filter'])){
            $opt = Util::createCriteriaForChatHistory($_POST['filter_year'], $_POST['month'], $_POST['period']);
            
            $dur = ChatHistory::getTotalTimeDuration(Yii::app()->user->id, $opt);
                foreach($dur as $total)
                    $total_duration = $total['Total'];
            $history = ChatHistory::getHistoryListForClient(Yii::app()->user->id, $opt);

        // If some page of data (for Paging) //
        } else if(isset($_GET['paging'])){
            $opt = Util::createCriteriaForChatHistory($_GET['year'], $_GET['month'], $_GET['day']);
            
            $dur = ChatHistory::getTotalTimeDuration(Yii::app()->user->id, $opt);
                foreach($dur as $total)
                    $total_duration = $total['Total'];
            $history = ChatHistory::getHistoryListForClient(Yii::app()->user->id, $opt);

        } else {
            $history = ChatHistory::getHistoryListForClient(Yii::app()->user->id);
        }
        $this->render('clientchathistory', array('data' => $history, 'total' => $total_duration));
    }

    public function actionReaderChatHistory(){
            $this->layout = $this->without_left_menu;
            $dur = ChatHistory::getReaderTotalTimeDuration(Yii::app()->user->id);
            foreach($dur as $total)
                $total_duration = $total['Total'];

            if(isset($_POST['filter'])){
                $opt = Util::createCriteriaForChatHistory($_POST['filter_year'], $_POST['month'], $_POST['period']);

                $dur = ChatHistory::getReaderTotalTimeDuration(Yii::app()->user->id, $opt);
                    foreach($dur as $total)
                        $total_duration = $total['Total'];
                $history = ChatHistory::getHistoryListForReader(Yii::app()->user->id, $opt);

            } else if(isset($_GET['paging'])){
                $opt = Util::createCriteriaForChatHistory($_GET['year'], $_GET['month'], $_GET['day']);

                $dur = ChatHistory::getTotalTimeDuration(Yii::app()->user->id, $opt);
                    foreach($dur as $total)
                        $total_duration = $total['Total'];
                $history = ChatHistory::getHistoryListForReader(Yii::app()->user->id, $opt);
            } else {
                $history = ChatHistory::getHistoryListForReader(Yii::app()->user->id);
            }
        $this->render('readerchathistory', array('data' => $history, 'total' => $total_duration));
    }

    public function actionOpenLog($session_id, $reader_id){
        
        $history = ChatHistory::getSession($session_id);
        $transcript = $history->getTranscript();

        if(empty($transcript))
            $transcript = ChatTranscripts::getTranscript($session_id, $reader_id);
        
        $this->renderPartial('transcripts', array('log' => $transcript));
    }
    
    public function actionSessionInfo()
    {
        $info = DebugExtra::getSessionInfo($_GET['session_id']);

        $client = Clients::getClient($info['client_id'], 'credit_cards');
        $reader = Readers::getReader($info['reader_id']);
        switch($info['chat_type'])
        {
            case 'NULL': 
                $chattype = 'default, by admin control';
                break;
            case 1 : 
                $chattype = 'new chat(debug)';
                break;
            case 2 : 
                $chattype = 'old chat(jchat)';
                break;
            case 3 : 
                $chattype = 'emergency_debug';
                break;
            case 4 : 
                $chattype = 'emergency';
                break;
            case 5 : 
                $chattype = 'debug 2';
                break;
            case 7 : 
                $chattype = 'new chat (advanced)';
                break;
        }
        $paid_time = floor($info['Paid_time']);
        $all_time = floatval($info['Paid_time']);
        
        $this->render('session_info', array(
            'info' => $info, 
            'client_name' => $client->credit_cards->firstname, 
            'reader_name' => $reader->name,
            'chat_type' => $chattype,
            'paid_time' => $paid_time,
            'all_time' => $all_time,
        ));
    }
    
    public function actionAddTime()
    {
        $info = DebugExtra::getSessionInfo($_POST['session_id']);
        
        if(isset($_POST['add_time']) && $_POST['time'] != 0)
        {
            //$client = Clients::getClient($info['client_id']);
            //$reader = Readers::getReader($info['reader_id']); 
            
            //Compute reader balance before reducing the time
            $RTS = new ReturnTimeStat();
            $RTS->reader_id = $info['reader_id'];
            $RTS->client_id = $info['client_id'];
            $RTS->session_id = $_POST['session_id'];
            $RTS->value = $_POST['time'];
            $RTS->saveRetTimeStat();
            unset($RTS);
            
            $RTS = ReturnTimeStat::getRTSBySession($_POST['session_id']);
            $RTS->saveBeforeReaderBalance();
            $RTS->saveAfterReaderBalance();
            
            $RT = new ReturnTime($info, $_POST['time']);
            
            //Check if the session in current period or out of it. Ticket (ADD TIME BACK FUNCTION)
            if($RT->ifSessionOld())
            {
                $period_start = (date("d") > 15)? strtotime(date('Y').'-'.date('m').'-15') : strtotime(date('Y').'-'.date('m').'-01');
                
                //If there is no session with such time return an error
                if(!$RT->reduceCurrentSession()) {
                    $this->render('session_info', array('error' => 1));
                    return;
                }
            }
            else
            {
                $RT->changeHistory();
                $RT->reduceReaderPayment();
                $RT->changeReaderBalance();
                $RT->changeClientBalance(); 
                $RT->notifyUsers();
            }
        }
        if(isset($_POST['add_free_time']) && !empty($_POST['free_time'])) {
            $RT = new ReturnTime($info, intval($_POST['free_time']));
           
            $RT->changeFreeTime();
            $RT->changeClientBalance();
            $RT->notifyUsers('free');
        }
        
        $this->redirect(Yii::app()->params['http_addr'].'users/sessionInfo?session_id='.$_POST['session_id']);
    }

    /*
     * AJAX action for delete chat logs
     */
    public function actionChathistoryDel(){
        $a = new AjaxResponseJSON();
        ChatHistory::deleteChatHistory($_GET['session_id']);
        $a->send('ok');
    }

    /**
     * Lists all models.
     */
    public function actionIndex() {
        $this->redirect('users/mainmenu');
    }
    /**
     * Render main menu page in dependence of user type
     * Sets a favorit reader
     */
    public function actionMainMenu() {
        $user_type = Yii::app()->user->type;
        if(isset($_GET['favorite'])){
            $pref = Preference::getFavouriteReader(Yii::app()->session['login_operator_id']);
            if(!empty($pref)){
                $pref->value = $_GET['favorite'];
                $pref->save();
            } else {
                $pref = new Preference();
                $pref->user_id = Yii::app()->session['login_operator_id'];
                $pref->name = 'reader';
                $pref->title = 'Favorite';
                $pref->value = $_GET['favorite'];
                $pref->save();
            }
            $reader = Readers::getReader($_GET['favorite']);
            $update_msg = Yii::t('lang', 'Your_favorite_reader').' '.$reader->name;
        }
        switch ($user_type) {
            case 'Administrator':
                ///$this->redirect(Yii::app()->params['site_domain'].'/chat/chatmain.php');
                $this->render('mainAdmin', array('favorite_msg' => $update_msg));
                break;
            case 'reader':
                ////$this->redirect(Yii::app()->params['site_domain'].'/chat/chatmain.php');
                $this->render('mainReader', array('favorite_msg' => $update_msg));
                break;
            case 'client':
                $this->render('mainClient', array('favorite_msg' => $update_msg));
                break;
            case 'gift_chat':
                $this->render('gift');
                break;
            case 'gift_chat_pending':
                $this->render('gift');
                break;
        }
    }
    /**
     * Loads a reeader status for AJAX page update
     */
    public function actionLoadReaderStatus(){
        $id = $_POST['reader_id'];
        $reader = Readers::getReader($id);
        $status = $reader->getStatus();
        $mins = $reader->minutesOnBreak();
        switch($status){
            case 'offline':                
                echo ($_POST['new']) ? '<img src="'.Yii::app()->params['http_addr'].'new_images/offline.gif">' : '<img src="'.Yii::app()->params['http_addr'].'images/status_offline.jpg">';
                break;
            case 'online':
                echo ($_POST['new']) ? '<img src="'.Yii::app()->params['http_addr'].'new_images/available.gif">' : '<img src="'.Yii::app()->params['http_addr'].'images/status_online.jpg">';
                break;
            case 'break'.$mins:
                echo ($_POST['new']) ? '<img src="'.Yii::app()->params['http_addr'].'new_images/backsoon.gif">' : '<img src="'.Yii::app()->params['http_addr'].'images/status_break'.$mins.'.jpg">';
                break;
            case 'busy':
                echo ($_POST['new']) ? '<img src="'.Yii::app()->params['http_addr'].'new_images/chatting.gif">' : '<img src="'.Yii::app()->params['http_addr'].'images/status_busy.jpg">';
                break;
        }
    }

    public function actionBanned(){
        $this->render('banned');
    }

    public function actionPageReader() {
        //Show flash message if it present
        Yii::app()->clientScript->registerScript(
                'myHideEffect',
                '$(".info").animate({opacity: 1.0}, 3000).fadeOut("slow");',
                CClientScript::POS_READY
        );

        Yii::app()->clientScript->registerScript(
                'alignCenter',
                "
            //align element in the middle of the screen
            $.fn.alignCenter = function() {
                //get margin left
                var marginLeft = Math.max(40, parseInt($(window).width()/2 - $(this).width()/2)) + 'px';
                //get margin top
                var marginTop = Math.max(40, parseInt($(window).height()/2 - $(this).height()/2)) + 'px';
                //return updated element
                return $(this).css({'left':marginLeft, 'top':marginTop});
            };
            $('.info').alignCenter();
            ",
                CClientScript::POS_READY
        );


        //get current date components and set them into view
        $current_month = date("n", time());
        $current_day   = date("j", time());
        $current_year  = date("Y", time());
        $current_hour = date("h", time());
        $current_min = date("i", time());
        $current_a = date("a", time());
        $main_time = $current_hour.':'.$current_min.' '.$current_a;
        $alt_time = ($current_hour+1).':'.$current_min.' '.$current_a;

        if(isset($_POST['action']) && $_POST['action'] == 'send') {
            $maxRequestPeriod = 2592000;//seconds, 2592000 seconds = 30 days
            $error = false;
            $readersArr = array();
            if(!empty($_POST['readers'])) {
                foreach($_POST['readers'] as $reader_id) {
                    $readersArr[] = $reader_id;
                }
            }
            else {
                $err_msg = '<font color="#FF0000">'. Yii::t('lang', 'select_the Readers') .'.</font><br>';
                $error = true;
            }
            if('custom' == $_POST['when']) {
                $currentTime = time();
                $mainDate = $_POST['year_main'].'-'.$_POST['month_main'].'-'.$_POST['day_main'];
                $mainTime = strtotime($mainDate);

                $altDate = $_POST['year_alt'].'-'.$_POST['month_alt'].'-'.$_POST['day_alt'];
                $altTime = strtotime($altDate);

                if(($maxRequestPeriod < (abs($mainTime - $currentTime))) or ($maxRequestPeriod < (abs($altTime - $currentTime)))) {
                    $err_msg = '<font color="#FF0000">'.Yii::t("lang", "notifyreaders_txt_1"). ' '.(round($maxRequestPeriod/60/60/24)).' '.Yii::t('lang', 'notifyreaders_txt_2'). '</font><br>';
                    $error = true;
                }
            }

            if(!$error) {
                $date_now = date("M j, Y");
                $time_now =date("h:i a");
                $subject = 'CHAT REQUEST';
                $model = new users;
                $logged_user = $model->findByPk(Yii::app()->user->id);

                if('15mins' == $_POST['when']) {
                    $message = 'DATE: '.$date_now.'<br>TIME SENT: '.$time_now.'<br>FROM: '.$logged_user->login.'<br>TO: {readers}<br>Client '.$logged_user->login.' would like to chat with you within the next 15 minutes <br>
                                Please log on and then send an email to the client (if listed on your roster) & let them know when you will be online<br>
                                PLEASE TRY TO LOG ON AS SOON AS YOU RECEIVE THIS EMAIL';
                }
                else {// custom time
                    $message = 'DATE: '.$date_now.'<br>TIME SENT: '.$time_now.'<br>FROM: '.$logged_user->login.'<br>TO: {readers}<br>Client '.$logged_user->login.' would like to chat with you on<br>
                               '.$_POST['month_main'].'/'.$_POST['day_main'].'/'.$_POST['year_main'].' at '.$_POST['time_main'].'<br>
                                ALTERNATE date and time:<br>
                               '.$_POST['month_alt'].'/'.$_POST['day_alt'].'/'.$_POST['year_alt'].' at '.$_POST['time_alt'].'<br>
                                Please log on and then send an email to the client (if listed on your roster) & let them know when you will be online<br>
                                PLEASE TRY TO LOG ON AS SOON AS YOU RECEIVE THIS EMAIL';
                }


                $headers = "From: javachat@psychic-contact.com\r\n";
                $headers.="Content-Type: text/html\r\n";

                //send emails to Readers
                foreach($readersArr as $reader_id) {
                    $reader = $model->findByPk($reader_id);
                    $text_2_send = str_replace('{readers}',$reader->login,$message);
                    // mail($reader->emailaddress, $subject, $text_2_send, $headers);
                    $readersNames[] = $reader->login;
                }
                $readersNames = join(',',$readersNames);

                if('15mins' == $_POST['when']) {
                    $message_2_user = 'Hello,<br>You have made an appointment for a chat session.<br>TO:<br>'.$readersNames.'<br>You would like to chat with the Reader within the next 15 minutes <br>
                                       <br>
                                       Best Regards,<br>
                                       '.Yii::app()->params['siteName'].'<br>'.Yii::app()->params['http_addr'];
                }
                else {
                    $message_2_user = 'Hello,<br>You have made an appointment for a chat session.<br>TO:<br>'.$readersNames.'<br>You would like to chat with the Reader on<br>
                                     '.$_POST['month_main'].'/'.$_POST['day_main'].'/'.$_POST['year_main'].' at '.$_POST['time_main'].'<br>
                                       ALTERNATE date and time:<br>
                                     '.$_POST['month_alt'].'/'.$_POST['day_alt'].'/'.$_POST['year_alt'].' at '.$_POST['time_alt'].'<br>
                                       <br>
                                       Best Regards,<br>
                                       '.Yii::app()->params['siteName'].'<br>'.Yii::app()->params['http_addr'];
                }

                //  mail($logged_user->emailaddress, $subject, $message_2_user, $headers);

                Yii::app()->user->setFlash('success',Yii::t('lang', 'Your_notification_was_sent'));
                $this->redirect('pagereader');
            }
        }
        else {

        }

        $readers_lst = new CActiveDataProvider('users',array(
                        'criteria'=>array(
                                'join' => "LEFT OUTER JOIN site_forbidden_readers fr on t.rr_record_id=fr.reader_id AND group_id = 1",
                                'condition'=>'type = "reader" AND fr.reader_id IS NULL',
                                'order'=>'login ASC',
                        ),
                        'pagination'=>false,
        ));

        $this->render('pageReader',array(
                'error_message'=>$err_msg,
                'readers_lst'=>$readers_lst,
                'current_month'=>$current_month,
                'current_day'=>$current_day,
                'current_year'=>$current_year,
                'current_hour'=>$current_hour,
                'current_min'=>$current_min,
                'current_a'=>$current_a,
                'main_time'=>$main_time,
                'alt_time'=>$alt_time,
        ));
    }

    /*
     * Action show all payments for current reader
     */
    public function actionReaderBalance(){
        $year = Yii::app()->request->getParam('year');
        $location = Yii::app()->request->getParam('location');
        $month = Yii::app()->request->getParam('month');
        $day = Yii::app()->request->getParam('day');
        
        $condition = array();
        if(empty($year)) 
            $condition['year'] = date('Y');
        else 
            $condition['year'] = $year;
        
        if(!empty($location) && $location != 'all')
            $condition['loc'] = $location;
        
        if(!empty($month) && $month != 'all')
            $condition['month'] = $month;
        
        if(!empty($day) && $day != 'all')
            $condition['day'] = $day;
        
        //echo $year.' '.$location.' '.$month.' '.$day;
        //die();
        
//        $condition = array();
//        $condition['year'] = $year;
//        $condition['month'] = $month;
//        $condition['loc'] = $location;
//        $condition['day'] = $day;
        
//        if(isset($_POST['filter']))
//        {            
//            $condition['year'] = $year;
//            
//            if($location != 'all')
//                $condition['loc'] = $location;
//            if($month != 'all')
//                $condition['month'] = $month;
//            if($day != 'all')
//                $condition['day'] = $day;
//        } 

        $data = ReaderPayment::getReaderPayments(Yii::app()->user->id, $condition);
        $total = ReaderPayment::getTotalSum(Yii::app()->user->id, $condition);
        $this->render('reader_payment', array('data' => $data, 'total' => $total, 'filter' => $condition));
    }
    /*
     * Static page with reader price rates
     */
    public function actionPriceRate(){
        $this->render('price_rate');
    }

    public function actionInvoice($invoice_id = null){
        $this->layout = $this->without_left_menu;        
        
        if(!is_null($invoice_id)){
            $criteria = new CDbCriteria;
            $count = ReaderInvoice::getInvoiceCountById($invoice_id);

            $pages=new CPagination($count);
            $pages->pageSize = 10;
            $pages->applyLimit($criteria);

            $invoices = ReaderInvoice::getInvoiceById($invoice_id, $pages->currentPage * $pages->pageSize, $pages->pageSize);

            $reader = Readers::getReader(Yii::app()->user->id);

            $this->render('invoice_single', array('invoices' => $invoices, 'pages' => $pages, 'reader' => $reader));
            return;
        }
        if(isset($_POST['Accept'])){
            foreach($_POST['id'] as $id){
                ReaderInvoice::updateInvoiceById($id, ReaderInvoice::ACCEPT_STATUS, $_POST['reader_supposed_amount'][$id]);

            }
        }
        if(isset($_POST['Decline'])){
            foreach($_POST['id'] as $id){
                ReaderInvoice::updateInvoiceById($id, ReaderInvoice::DECLINED_STATUS, $_POST['reader_supposed_amount'][$id]);
            }
        }

        $invoices = ReaderInvoice::getPendingInvoices(Yii::app()->user->id);
        $reader = Readers::getReader(Yii::app()->user->id);
        $this->render('invoice', array('invoices' => $invoices, 'reader' => $reader));
    }

    public function actionUserBan($unban = null){
        // Unban
        if(!is_null($unban)){
            UserBan::deleteBan($unban);
            $bans = UserBan::getReaderBanList(Yii::app()->user->id);
            $this->render('userban', array('banned' => $bans));
            return;
        }
        // New ban
        if(isset($_POST['new_ban'])){
            $new_ban = new UserBan;
            $new_ban->user_id = $_POST['ban_user_id'];
            $new_ban->reader_id = Yii::app()->user->id;
            $new_ban->reason = $_POST['reason'];
            $new_ban->save();

            $bans = UserBan::getReaderBanList(Yii::app()->user->id);
            $this->render('userban', array('banned' => $bans));
            return;
        }

        $bans = UserBan::getReaderBanList(Yii::app()->user->id);
        $this->render('userban', array('banned' => $bans));
    }
    /*
     * Ajax action. Find user by id, login or email address
     */
    public function actionGetUser(){
        $user = UserSearch::loadOneUser($_POST['query']);

        $ret = array();
        $ret['login'] = $user->login;
        $ret['signup'] = $user->rr_createdate;
        $ret['user'] = $user->rr_record_id;

        echo json_encode($ret);
    }   
    
    public function actionNrrQuest()
    {
        $del = Yii::app()->request->getParam('del');
        if($del)
        {
            $request = NrrRequests::getRequest($del);
            $request->delRequest();
        }
        
        $requests = NrrRequests::getByReaderId(Yii::app()->user->id);
        
        $stmt = array();
        foreach($requests as $req)
        {
            $client = users::getUser($req->client_id);
            $client_login = $client->getLogin();
            
            $stmt[] = array(
                'Id' => $req->id,
                'Date' => $req->date,
                'Username' => $client_login,
                'Notes' => $req->nrr_notes
            );
        }
        
        $data = new CArrayDataProvider($stmt, array(
                'id'=>'nrr',
                'pagination'=>array(
                    'pageSize' => 10,
                ),
            ));
        
        $this->render('nrr_quest', array('data' => $data));
    }
    
    public function actionGetBusyImage($reader_id)
    {
	// added by james 5-21-2012
	$reader_id = htmlentities(strip_tags($reader_id));

        $img = ReaderWithStatus::getBusyStatImg($reader_id);
        if(!$img)
            $img = Yii::app()->params['http_addr'].'images/busy.jpg';
        
        header( 'Content-Type: image/jpeg' );
        echo $img;
        die();        
    }
}