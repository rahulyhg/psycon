<?php

class BlogComments extends CActiveRecord
{
    const PER_PAGE = 5;
	/**
	 * The followings are the available columns in table 'blog_comments':
	 * @var integer $id
	 * @var integer $user_id
	 * @var string $date
	 * @var string $thread_id
	 * @var string $article_id
	 * @var string $text
         * @var integer $approved
	 */

	/**
	 * Returns the static model of the specified AR class.
	 * @return CActiveRecord the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

        public function getDbConnection(){
            return Yii::app()->blog_db;
        }

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'blog_comments';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, article_id, text', 'required'),
			array('user_id', 'numerical', 'integerOnly'=>true),
			array('thread_id, article_id', 'length', 'max'=>150),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array('blog_users' => array(self::BELONGS_TO, 'BlogUsers', 'user_id')
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'Id',
			'user_id' => 'User',
			'date' => 'Date',
			'thread_id' => 'Thread',
			'article_id' => 'Article',
			'text' => 'Text',
		);
	}

        public static function getUnapprovedComments(){
            $c = new CDbCriteria;
            $c->condition = 't.approved = 0';
            $c->with = 'blog_users';
            $dataProvider=new CActiveDataProvider('BlogComments', array(
                        'criteria'=>$c,
                        'pagination'=> array(
                                'pageSize'=> self::PER_PAGE,
                        )
            ));
            return $dataProvider;
        }

        public static function getArtComments($art_id){
            $c = new CDbCriteria;
            $c->condition = 't.approved = 1 AND article_id = :art_id';
            $c->params = array(':art_id' => $art_id);
            $c->with = 'blog_users';
            $dataProvider=new CActiveDataProvider('BlogComments', array(
                        'criteria'=>$c,
                        'pagination'=> array(
                                'pageSize'=> self::PER_PAGE,
                        )
            ));
            return $dataProvider;
        }

        public function addComment(){
            $this->date = date('Y-m-d');
            $this->approved = 0;
            $this->save();
        }

        public static function getComment($id){
            return self::model()->findByPk($id);
        }
}
?>
