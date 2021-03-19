<?php
/** @var $model \commom\models\Video */

use \yii\helpers\StringHelper;
use \yii\helpers\Url;
?>

<div class="media">
    <a href="<?= Url::to(['video/update', 'id' => $model->video_id]) ?>">
        <div class="embed-responsive embed-responsive-16by9 mr-2" style="width: 120px">
            <video  class="embed-responsive-item"
                    poster="<?= $model->getThumbnailLink() ?>"
                    src="<?= $model->getVideoLink() ?>"></video>
        </div>
    </a>
    <div class="media-body">
        <h5 class="mt-0"><?= $model->title ?></h5>
        <p><?= StringHelper::truncateWords($model->description, 10) ?></p>
    </div>
</div>
