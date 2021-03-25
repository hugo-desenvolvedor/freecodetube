<?php

namespace frontend\controllers;

use common\models\Video;
use common\models\VideoLike;
use common\models\VideoView;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class VideoController extends Controller
{
    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['like', 'dislike'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@']
                    ]
                ],

            ],
            'verb' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'like' => ['post'],
                    'dislike' => ['post']
                ]
            ],
        ];
    }

    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider(
            [
                'query' => Video::find()->published()
            ]
        );

        return $this->render(
            'index',
            [
                'dataProvider' => $dataProvider
            ]
        );
    }

    public function actionLike($id)
    {
        $video = $this->findVideo($id);
        $userId = \Yii::$app->user->id;

        $videoLikeDislike = VideoLike::find()->andWhere(
            [
                'video_id' => $id,
                'user_id' => $userId
            ]
        )->one();

        if (!$videoLikeDislike) {
            $this->saveLikeDislike($id, $userId, VideoLike::TYPE_LIKE);
        } else if ($videoLikeDislike->type == VideoLike::TYPE_LIKE) {
            $videoLikeDislike->delete();
        } else {
            $videoLikeDislike->delete();
            $this->saveLikeDislike($id, $userId, VideoLike::TYPE_LIKE);
        }

        return $this->renderAjax(
            '_buttons',
            [
                'model' => $video
            ]
        );
    }

    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $this->layout = 'auth';

        $video = $this->findVideo($id);

        $videoView = new VideoView();
        $videoView->video_id = $id;
        $videoView->user_id = \Yii::$app->user->id;
        $videoView->created_at = time();

        return $this->render(
            'view',
            [
                'model' => $video
            ]
        );
    }

    /**
     * @param $id
     * @return Video|null
     * @throws NotFoundHttpException
     */
    protected function findVideo($id)
    {
        $video = Video::findOne($id); // based on primary key

        if (!$video) {
            throw new NotFoundHttpException("Video does not exist");
        }

        return $video;
    }

    /**
     * @param $videoId
     * @param $userId
     * @param $type
     */
    protected function saveLikeDislike($videoId, $userId, $type): void
    {
        $videoLikeDislike = new VideoLike();
        $videoLikeDislike->video_id = $videoId;
        $videoLikeDislike->user_id = $userId;
        $videoLikeDislike->type = $type;
        $videoLikeDislike->created_at = time();
        $videoLikeDislike->save();
    }
}
