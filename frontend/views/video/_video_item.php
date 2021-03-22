<?php

/** $model common\models\Video */

use yii\helpers\Url;

?>
<div class="card m-3" style="width: 14rem;">
    <a href="<?= Url::to(['/video/view', 'id' => $model->video_id]) ?>">
        <div class="embed-responsive embed-responsive-16by9">
            <video
                    class="embed-responsive-item"
                    poster="<?= $model->getThumbnailLink() ?>"
                    src="<?= $model->getVideoLink() ?>"
            ></video>
        </div>
    </a>
    <div class="card-body p-2">
        <h6 class="card-title m-0"><?= $model->title ?></h6>
        <p class="text-muted card-text m-0">
            <?= $model->createdBy->username ?>
        </p>
        <p class="text-muted card-text m-0">
            140 views . <?= Yii::$app->formatter->asRelativeTime($model->created_at) ?>
        </p>
    </div>
</div>
