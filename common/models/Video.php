<?php

namespace common\models;

use Imagine\Image\Box;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\FileHelper;
use yii\imagine\Image;
use yii\web\UploadedFile;

/**
 * This is the model class for table "{{%video}}".
 *
 * @property string $video_id
 * @property string $title
 * @property string|null $description
 * @property string|null $tags
 * @property int|null $status
 * @property int|null $has_thumbnail
 * @property string|null $video_name
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $created_by
 *
 * @property User $createdBy
 */
class Video extends \yii\db\ActiveRecord
{
    const STATUS_UNLISTED = 0;
    const STATUS_PUBLISHED = 1;

    /**
     * @var UploadedFile
     */
    public $video;

    /**
     * @var UploadedFile
     */
    public $thumbnail;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%video}}';
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class, // created_at and updated_at
            [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'created_by', // the default value is created_by
                'updatedByAttribute' => false // the default value is updated_by
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['video_id', 'title'], 'required'],
            [['description'], 'string'],
            [['status', 'has_thumbnail', 'created_at', 'updated_at', 'created_by'], 'integer'],
            [['video_id'], 'string', 'max' => 16],
            [['title', 'tags', 'video_name'], 'string', 'max' => 512],
            [['video_id'], 'unique'],
            ['has_thumbnail', 'default', 'value' => 0],
            ['status', 'default', 'value' => self::STATUS_UNLISTED],
            ['thumbnail', 'image', 'extensions' => ['jpg'], 'minWidth' => 1280],
            ['video', 'file', 'extensions' => ['mp4']],
//            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
        ];
    }

    /**
     * @return array
     */
    public function getStatusLabels()
    {
        return [
            self::STATUS_UNLISTED => 'Unlisted',
            self::STATUS_PUBLISHED => 'Published',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'video_id' => 'Video ID',
            'title' => 'Title',
            'description' => 'Description',
            'tags' => 'Tags',
            'status' => 'Status',
            'has_thumbnail' => 'Has Thumbnail',
            'video_name' => 'Video Name',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'thumbnail' => 'Thumbnail',
        ];
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery|\common\models\query\UserQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getViews()
    {
        return $this->hasMany(VideoView::class, ['video_id' => 'video_id']);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\VideoQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\VideoQuery(get_called_class());
    }

    /**
     * @param bool $runValidation
     * @param null $attributeNames
     * @return bool
     * @throws \yii\base\Exception
     */
    public function save($runValidation = true, $attributeNames = null)
    {
        $isInsert = $this->isNewRecord;

        if ($isInsert) {
            $this->video_id = Yii::$app->security->generateRandomString(8);
            $this->title = $this->video->name;
            $this->video_name = $this->video->name;
        }

        if ($this->thumbnail) {
            $this->has_thumbnail = 1;
        }

        $saved = parent::save($runValidation, $attributeNames);

        if (!$saved) {
            return false;
        }

        if ($isInsert) {
            $path = sprintf('@frontend/web/storage/videos/%s.mp4', $this->video_id);
            $videoPath = Yii::getAlias($path);

            if (!is_dir(dirname($videoPath))) {
                FileHelper::createDirectory(dirname($videoPath));
            }

            $this->video->saveAs($videoPath);
        }

        if ($this->thumbnail) {
            $path = sprintf('@frontend/web/storage/thumbs/%s.jpg', $this->video_id);
            $thumbnailPath = Yii::getAlias($path);

            if (!is_dir(dirname($thumbnailPath))) {
                FileHelper::createDirectory(dirname($thumbnailPath));
            }

            $this->thumbnail->saveAs($thumbnailPath);

            Image::getImagine()
                ->open($thumbnailPath)
                ->thumbnail(new Box(1280, 1280))
                ->save();
        }

        return true;
    }

    /**
     * Get the video url
     * @return string
     */
    public function getVideoLink(): string
    {
        $url = sprintf(
            '%s/storage/videos/%s.mp4',
            Yii::$app->params['frontendUrl'],
            $this->video_id
        );

        return $url;
    }

    /**
     * Get the thumbnail url
     * @return string
     */
    public function getThumbnailLink(): string
    {
        $url = sprintf(
            '%s/storage/thumbs/%s.jpg',
            Yii::$app->params['frontendUrl'],
            $this->video_id
        );

        return $this->has_thumbnail ? $url : "";
    }

    public function afterDelete(): void
    {
        $videoPath = sprintf('@frontend/web/storage/videos/%s.mp4', $this->video_id);
        $videoPath = Yii::getAlias($videoPath);
        unlink($videoPath);

        $thumbnailPath = sprintf('@frontend/web/storage/thumbs/%s.jpg', $this->video_id);
        $thumbnailPath = Yii::getAlias($thumbnailPath);
        if (file_exists($thumbnailPath)) {
            unlink($thumbnailPath);
        }
    }

    /**
     * @param $userId
     * @return array|VideoLike|null
     */
    public function isLikedBy($userId)
    {
        return VideoLike::find()
            ->byUserIdAndVideoId($userId, $this->video_id)
            ->liked()
            ->one();
    }

    /**
     * @return int|string
     */
    public function totalLikes()
    {
        return VideoLike::find()
            ->andWhere(['video_id' => $this->video_id])
            ->liked()
            ->count();
    }
}
