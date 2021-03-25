<?php
/** @var \common\models\Video */
use yii\helpers\Url;

?>
<a href="<?= Url::to(['/video/like', 'id' => $model->video_id]) ?>"
   data-method="post"
   data-pjax="1" >
    <button class="btn btn-sm <?= $model->isLikedBy(Yii::$app->user->id) ? 'btn-outline-primary' : 'btn-outline-secondary' ?>">
        <i class="fas fa-thumbs-up"></i> <?= $model->totalLikes() ?>
    </button>
</a>
<a href="<?= Url::to(['/video/dislike', 'id' => $model->video_id]) ?>">
    <button class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-thumbs-down"></i> 3
    </button>
</a>
